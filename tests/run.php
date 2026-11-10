<?php

declare(strict_types=1);

error_reporting(E_ALL);
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if ((error_reporting() & $severity) === 0) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['HTTP_HOST'] = 'renzo.test';
$_SERVER['HTTPS'] = 'on';
$_SESSION = [];

$passed = 0;
$assert = static function (bool $condition, string $label) use (&$passed): void {
    if (!$condition) {
        throw new RuntimeException('ASSERTION FAILED: ' . $label);
    }
    $passed++;
};

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->sqliteCreateFunction('NOW', static fn (): string => '2026-11-10 20:30:00', 0);

$pdo->exec('
CREATE TABLE admins (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT UNIQUE, role TEXT DEFAULT "manager", is_active INTEGER DEFAULT 1, last_login_at TEXT NULL);
CREATE TABLE otp_codes (id INTEGER PRIMARY KEY AUTOINCREMENT, admin_id INTEGER, code_hash TEXT, expires_at TEXT, attempts INTEGER DEFAULT 0, used_at TEXT NULL, ip_address TEXT NULL, created_at TEXT DEFAULT "2026-11-10 20:30:00");
CREATE TABLE menu_categories (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, slug TEXT, description TEXT, sort_order INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1);
CREATE TABLE menu_items (id INTEGER PRIMARY KEY AUTOINCREMENT, category_id INTEGER, name TEXT, slug TEXT, description TEXT, price REAL, image_path TEXT, is_featured INTEGER DEFAULT 0, is_available INTEGER DEFAULT 1, is_active INTEGER DEFAULT 1, sort_order INTEGER DEFAULT 0);
CREATE TABLE menu_item_variants (id INTEGER PRIMARY KEY AUTOINCREMENT, menu_item_id INTEGER, name TEXT, price REAL, is_available INTEGER DEFAULT 1, sort_order INTEGER DEFAULT 0);
CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, slug TEXT, color TEXT);
CREATE TABLE menu_item_tags (menu_item_id INTEGER, tag_id INTEGER);
CREATE TABLE deals (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, slug TEXT, description TEXT, type TEXT, discount_label TEXT, coupon_code TEXT, image_path TEXT, start_datetime TEXT, end_datetime TEXT, is_active INTEGER DEFAULT 1, sort_order INTEGER DEFAULT 0);
CREATE TABLE reservations (id INTEGER PRIMARY KEY AUTOINCREMENT, customer_name TEXT, customer_email TEXT, customer_phone TEXT, party_size INTEGER, reservation_date TEXT, reservation_time TEXT, special_requests TEXT, status TEXT DEFAULT "new", admin_notes TEXT NULL, handled_by_admin_id INTEGER NULL);
CREATE TABLE contact_messages (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, email TEXT, phone TEXT, subject TEXT, inquiry_type TEXT, message TEXT, is_read INTEGER DEFAULT 0, read_at TEXT NULL, ip_address TEXT NULL);
CREATE TABLE gallery_items (id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT DEFAULT "photo", title TEXT, caption TEXT, category TEXT, image_path TEXT, youtube_id TEXT, alt_text TEXT, sort_order INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1);
CREATE TABLE slider_items (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT, subtitle TEXT, cta_text TEXT, cta_url TEXT, image_path TEXT, alt_text TEXT, sort_order INTEGER DEFAULT 0, is_active INTEGER DEFAULT 1);
CREATE TABLE settings (id INTEGER PRIMARY KEY AUTOINCREMENT, setting_key TEXT UNIQUE, setting_value TEXT, setting_group TEXT, updated_by_admin_id INTEGER);
');

// SQLite-compatible OtpCode harness preserving exact OtpService logic
$sqliteOtpModel = new class($pdo) extends App\Models\OtpCode {
    public function createForAdmin(int $adminId, string $codeHash, string $ipAddress, int $expiryMinutes = 10): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO otp_codes (admin_id, code_hash, expires_at, attempts, ip_address, created_at)
             VALUES (:admin_id, :code_hash, datetime(NOW(), '+' || :expiry || ' minutes'), 0, :ip_address, NOW())"
        );
        $stmt->execute([
            'admin_id' => $adminId,
            'code_hash' => $codeHash,
            'expiry' => $expiryMinutes,
            'ip_address' => $ipAddress,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function countRecentRequests(int $adminId, int $windowMinutes = 10): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM otp_codes
             WHERE admin_id = :admin_id AND created_at >= datetime(NOW(), '-' || :minutes || ' minutes')"
        );
        $stmt->execute(['admin_id' => $adminId, 'minutes' => $windowMinutes]);
        return (int) $stmt->fetchColumn();
    }

    public function sumRecentAttempts(int $adminId, int $windowMinutes = 10): int
    {
        $stmt = $this->db->prepare(
            "SELECT COALESCE(SUM(attempts), 0) FROM otp_codes
             WHERE admin_id = :admin_id AND created_at >= datetime(NOW(), '-' || :minutes || ' minutes')"
        );
        $stmt->execute(['admin_id' => $adminId, 'minutes' => $windowMinutes]);
        return (int) $stmt->fetchColumn();
    }
};

$capturedOtp = '';
$spyMail = new class($capturedOtp) extends App\Services\MailService {
    public array $sent = [];
    public function __construct(private string &$capturedOtp) {}
    public function sendOtp(string $toEmail, string $toName, string $otpCode, int $expiryMinutes = 10): void
    {
        $this->capturedOtp = $otpCode;
        $this->sent[] = ['to' => $toEmail, 'code' => $otpCode];
    }
    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): void
    {
        $this->sent[] = ['to' => $toEmail, 'subject' => $subject];
    }
};

// 1. OTP hashing, single-use replay protection, and 6th-attempt lockout
$adminModel = new App\Models\Admin($pdo);
$adminId = (int) $adminModel->create(['name' => 'Owner', 'email' => 'owner@renzo.test', 'role' => 'owner', 'is_active' => 1]);
$adminRow = $adminModel->find($adminId);

$otpSvc = new App\Services\OtpService($sqliteOtpModel, $spyMail);
$issued = $otpSvc->issue($adminRow, '127.0.0.1');
$assert($issued['ok'] === true && preg_match('/^\d{6}$/', $capturedOtp) === 1, 'OTP issued cleanly');

$latestOtp = $sqliteOtpModel->findLatestValidForAdmin($adminId);
$assert($latestOtp !== null && $latestOtp['code_hash'] !== $capturedOtp && password_verify($capturedOtp, $latestOtp['code_hash']), 'OTP stored hashed with password_hash');

for ($attempt = 1; $attempt <= 5; $attempt++) {
    $wrongCode = $capturedOtp === '000000' ? '111111' : '000000';
    $res = $otpSvc->verify($adminId, $wrongCode);
    $assert($res['ok'] === false, "Wrong OTP attempt #$attempt rejected");
}

$sixthEvenCorrect = $otpSvc->verify($adminId, $capturedOtp);
$assert($sixthEvenCorrect['ok'] === false && str_contains(strtolower($sixthEvenCorrect['message']), 'too many'), 'OTP 6th attempt blocked even with correct code');

// Fresh admin verifies valid OTP once and replay fails
$admin2Id = (int) $adminModel->create(['name' => 'Manager', 'email' => 'mgr@renzo.test', 'role' => 'manager', 'is_active' => 1]);
$otpSvc->issue($adminModel->find($admin2Id), '127.0.0.1');
$validOtp = $capturedOtp;
$okFirst = $otpSvc->verify($admin2Id, $validOtp);
$assert($okFirst['ok'] === true, 'Valid OTP accepted on first try');
$replay = $otpSvc->verify($admin2Id, $validOtp);
$assert($replay['ok'] === false, 'Used OTP cannot be replayed');

// 2. Honeypot & mail notification tests
$contactSvc = new App\Services\ContactService(new App\Models\ContactMessage($pdo), $spyMail);
$honeypotBlocked = false;
try {
    $contactSvc->submit(['name' => 'Bot', 'email' => 'bot@example.com', 'message' => 'Spam', '_hp_website' => 'http://spam.test'], '127.0.0.1');
} catch (InvalidArgumentException) {
    $honeypotBlocked = true;
}
$assert($honeypotBlocked, 'Contact honeypot blocks bot submission');

$resSvc = new App\Services\ReservationService(new App\Models\Reservation($pdo), $spyMail);
$resHoneypotBlocked = false;
try {
    $resSvc->submitPublic(['customer_name' => 'Bot', 'customer_phone' => '123', 'party_size' => '2', 'reservation_date' => date('Y-m-d', strtotime('+1 day')), 'reservation_time' => '19:00', '_hp_website' => 'spam']);
} catch (InvalidArgumentException) {
    $resHoneypotBlocked = true;
}
$assert($resHoneypotBlocked, 'Reservation honeypot blocks bot submission');

// 3. Gallery WebP conversion via GD
$uploadSvc = new App\Services\UploadService();
$srcPng = tempnam(sys_get_temp_dir(), 'renzogd') . '.png';
$dstWebp = tempnam(sys_get_temp_dir(), 'renzowebp') . '.webp';
$im = imagecreatetruecolor(32, 32);
imagepng($im, $srcPng);
imagedestroy($im);
$converted = $uploadSvc->convertFileToWebp($srcPng, 'image/png', $dstWebp);
$finfo = new finfo(FILEINFO_MIME_TYPE);
$assert($converted && is_file($dstWebp) && $finfo->file($dstWebp) === 'image/webp', 'GD converts gallery image to valid WebP');
@unlink($srcPng);
@unlink($dstWebp);

// 4. Launch slider service & CTA sanitization
$sliderSvc = new App\Services\SliderService(new App\Models\SliderItem($pdo), $uploadSvc);
$xssBlocked = false;
try {
    $sliderSvc->save(null, ['title' => 'Test', 'cta_url' => 'javascript:alert(1)', 'is_active' => '1'], null);
} catch (InvalidArgumentException) {
    $xssBlocked = true;
}
$assert($xssBlocked, 'SliderService blocks javascript: CTA URL');
$sliderSvc->save(null, ['title' => 'Opening soon', 'subtitle' => 'Wood-fired kitchen', 'cta_text' => 'Menu', 'cta_url' => '/menu', 'sort_order' => '1', 'is_active' => '1'], null);
$assert(count($sliderSvc->getActiveSlides()) === 1, 'SliderService stores & loads active slide');

// 5. Expired deal never renders
$dealSvc = new App\Services\DealService(new App\Models\Deal($pdo));
$dealSvc->save(null, ['title' => 'Past Deal', 'type' => 'time_limited', 'end_datetime' => '2026-01-01 00:00:00', 'is_active' => '1'], null);
$dealSvc->save(null, ['title' => 'Live Deal', 'type' => 'permanent', 'is_active' => '1'], null);
$activeDeals = $dealSvc->active(10);
$assert(count($activeDeals) === 1 && $activeDeals[0]['title'] === 'Live Deal', 'Expired deals never returned by DealService::active()');

// 6. Menu PDF generation & cleanup
$menuSvc = new App\Services\MenuService(
    new App\Models\MenuCategory($pdo),
    new App\Models\MenuItem($pdo),
    new App\Models\MenuItemVariant($pdo),
    new App\Models\Tag($pdo),
    new App\Models\MenuItemTag($pdo),
);
$catId = $menuSvc->saveCategory(null, ['name' => 'Starters', 'is_active' => '1']);
$menuSvc->saveItem(null, ['category_id' => (string) $catId, 'name' => 'Truffle Bruschetta', 'price' => '1100', 'is_active' => '1', 'is_available' => '1'], null);
$pdfPath = storage_path('pdfs' . DIRECTORY_SEPARATOR . 'menu.pdf');
$assert(is_file($pdfPath) && str_starts_with((string) file_get_contents($pdfPath), '%PDF-'), 'Menu save regenerates /storage/pdfs/menu.pdf');
@unlink($pdfPath);

// 7. SEO routes & layout checks (minified assets, JSON-LD, unique titles/descriptions, sitemap, robots)
$routes = require BASE_PATH . '/routes/web.php';
$router = new App\Core\Router($routes);
$rfStatus = new ReflectionProperty(App\Core\Response::class, 'status');
$rfContent = new ReflectionProperty(App\Core\Response::class, 'content');
$rfHeaders = new ReflectionProperty(App\Core\Response::class, 'headers');

$sitemapResp = $router->dispatch(new App\Core\Request('GET', '/sitemap.xml', [], [], [], ['HTTP_HOST' => 'renzo.test', 'HTTPS' => 'on']));
$assert($rfStatus->getValue($sitemapResp) === 200 && str_contains($rfContent->getValue($sitemapResp), '<urlset') && str_contains($rfContent->getValue($sitemapResp), '/menu</loc>'), '/sitemap.xml valid');

$robotsResp = $router->dispatch(new App\Core\Request('GET', '/robots.txt', [], [], [], ['HTTP_HOST' => 'renzo.test', 'HTTPS' => 'on']));
$assert($rfStatus->getValue($robotsResp) === 200 && str_contains($rfContent->getValue($robotsResp), 'Disallow: /admin/') && str_contains($rfContent->getValue($robotsResp), 'Sitemap:'), '/robots.txt valid');

$homeResp = $router->dispatch(new App\Core\Request('GET', '/', [], [], [], ['HTTP_HOST' => 'renzo.test', 'HTTPS' => 'on']));
$homeBody = $rfContent->getValue($homeResp);
$assert(str_contains($homeBody, 'app.min.css') && str_contains($homeBody, 'app.min.js'), 'Layout loads minified CSS/JS');
$assert(str_contains($homeBody, '"@type":"Restaurant"') && str_contains($homeBody, 'application/ld+json'), 'Layout includes Restaurant JSON-LD');
$assert(str_contains($homeBody, 'name="_hp_website"'), 'Home contact form includes honeypot field');

$menuResp = $router->dispatch(new App\Core\Request('GET', '/menu', [], [], [], ['HTTP_HOST' => 'renzo.test', 'HTTPS' => 'on']));
$assert($rfContent->getValue($menuResp) !== '' && !str_contains($rfContent->getValue($menuResp), '<title>Home') && str_contains($rfContent->getValue($menuResp), '<title>Menu'), 'Unique title per page');

// 8. Open redirect protection on POST /reservations (with valid CSRF)
$csrfToken = App\Services\CsrfService::token();
$openRedirectResp = $router->dispatch(new App\Core\Request('POST', '/reservations', [], ['_token' => $csrfToken, '_redirect' => '//evil.com'], [], ['HTTP_HOST' => 'renzo.test', 'HTTPS' => 'on']));
$location = $rfHeaders->getValue($openRedirectResp)['Location'] ?? '';
$assert(!str_contains($location, 'evil.com'), 'Protocol-relative //evil.com redirect blocked');

echo "TESTS PASSED: $passed assertions\n";

