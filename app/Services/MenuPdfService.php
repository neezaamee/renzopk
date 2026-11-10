<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\View;
use Dompdf\Dompdf;
use Dompdf\Options;
use RuntimeException;

final class MenuPdfService
{
    private const CACHE_FILENAME = 'menu.pdf';

    private ?MenuService $menu;
    private ?SettingsService $settings;

    public function __construct(?MenuService $menu = null, ?SettingsService $settings = null)
    {
        $this->menu = $menu;
        $this->settings = $settings;
    }

    public function cachedPath(): string
    {
        return storage_path('pdfs' . DIRECTORY_SEPARATOR . self::CACHE_FILENAME);
    }

    public function ensureExists(): string
    {
        $path = $this->cachedPath();
        if (is_file($path)) {
            return $path;
        }

        return $this->regenerate();
    }

    public function regenerate(): string
    {
        if (!class_exists(Dompdf::class)) {
            throw new RuntimeException('Dompdf is not installed. Run "composer install" to generate the Menu PDF.');
        }

        $menuData = ($this->menu ??= new MenuService())->getPublicMenu();
        $settings = ($this->settings ??= new SettingsService())->all();

        $html = View::render('pdf/menu', [
            'categories' => $menuData,
            'settings' => $settings,
            'generatedAt' => date('F j, Y'),
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('chroot', base_path());

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $outputDir = storage_path('pdfs');
        if (!is_dir($outputDir) && !mkdir($outputDir, 0755, true) && !is_dir($outputDir)) {
            throw new RuntimeException('Unable to create PDF cache directory.');
        }

        $targetPath = $this->cachedPath();
        $tempPath = $targetPath . '.tmp.' . bin2hex(random_bytes(4));

        if (file_put_contents($tempPath, $dompdf->output()) === false) {
            @unlink($tempPath);
            throw new RuntimeException('Failed to write generated Menu PDF to cache.');
        }

        if (!rename($tempPath, $targetPath)) {
            @unlink($tempPath);
            throw new RuntimeException('Failed to replace cached Menu PDF atomically.');
        }

        return $targetPath;
    }

    public function regenerateSafely(): void
    {
        try {
            $this->regenerate();
        } catch (\Throwable $e) {
            error_log('[Renzo] Menu PDF regeneration failed: ' . $e->getMessage());
            $cached = $this->cachedPath();
            if (is_file($cached)) {
                @unlink($cached);
            }
        }
    }
}
