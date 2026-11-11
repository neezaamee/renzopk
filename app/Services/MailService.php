<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Env;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\PHPMailer;
use RuntimeException;

class MailService
{
    public function sendOtp(string $toEmail, string $toName, string $otpCode, int $expiryMinutes = 10): void
    {
        $appName = (string) Env::get('APP_NAME', 'Renzo');
        $subject = $appName . ' Admin Login Verification Code';
        $safeName = htmlspecialchars($toName, ENT_QUOTES, 'UTF-8');
        $safeCode = htmlspecialchars($otpCode, ENT_QUOTES, 'UTF-8');

        $htmlBody = sprintf(
            '<p>Hello %s,</p>
             <p>Your one-time verification code for the <strong>%s</strong> admin dashboard is:</p>
             <p style="font-size:24px;font-weight:bold;letter-spacing:4px;">%s</p>
             <p>This code expires in %d minutes. If you did not request this login, please ignore this email.</p>',
            $safeName,
            htmlspecialchars($appName, ENT_QUOTES, 'UTF-8'),
            $safeCode,
            $expiryMinutes
        );

        $textBody = sprintf(
            "Hello %s,\n\nYour %s admin login code is: %s\nThis code expires in %d minutes.\n",
            $toName,
            $appName,
            $otpCode,
            $expiryMinutes
        );

        $this->send($toEmail, $toName, $subject, $htmlBody, $textBody);
    }

    public function sendReservationNotification(array $reservation, string $toEmail): void
    {
        $subject = sprintf(
            'New Table Reservation: %s (%d guests on %s)',
            (string) ($reservation['customer_name'] ?? 'Guest'),
            (int) ($reservation['party_size'] ?? 1),
            (string) ($reservation['reservation_date'] ?? '')
        );

        $esc = static fn (mixed $v): string => htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
        $htmlBody = sprintf(
            '<h3>New Table Reservation Request</h3>
             <p><strong>Guest:</strong> %s</p>
             <p><strong>Phone:</strong> %s</p>
             <p><strong>Email:</strong> %s</p>
             <p><strong>Party Size:</strong> %d</p>
             <p><strong>Date &amp; Time:</strong> %s %s</p>
             <p><strong>Special Requests:</strong> %s</p>',
            $esc($reservation['customer_name'] ?? ''),
            $esc($reservation['customer_phone'] ?? ''),
            $esc($reservation['customer_email'] ?? 'Not provided'),
            (int) ($reservation['party_size'] ?? 1),
            $esc($reservation['reservation_date'] ?? ''),
            $esc($reservation['reservation_time'] ?? ''),
            $esc($reservation['special_requests'] ?? 'None')
        );

        $this->send($toEmail, 'Renzo Host Team', $subject, $htmlBody);
    }

    public function sendContactNotification(array $contact, string $toEmail): void
    {
        $subject = sprintf(
            'New %s Inquiry from %s',
            ucfirst((string) ($contact['inquiry_type'] ?? 'general')),
            (string) ($contact['name'] ?? 'Visitor')
        );

        $esc = static fn (mixed $v): string => htmlspecialchars((string) ($v ?? ''), ENT_QUOTES, 'UTF-8');
        $htmlBody = sprintf(
            '<h3>New Contact Submission</h3>
             <p><strong>From:</strong> %s (%s)</p>
             <p><strong>Phone:</strong> %s</p>
             <p><strong>Type:</strong> %s</p>
             <p><strong>Subject:</strong> %s</p>
             <p><strong>Message:</strong><br>%s</p>',
            $esc($contact['name'] ?? ''),
            $esc($contact['email'] ?? ''),
            $esc($contact['phone'] ?? 'Not provided'),
            $esc($contact['inquiry_type'] ?? 'general'),
            $esc($contact['subject'] ?? '(None)'),
            nl2br($esc($contact['message'] ?? ''))
        );

        $this->send($toEmail, 'Renzo Management', $subject, $htmlBody);
    }

    public function send(string $toEmail, string $toName, string $subject, string $htmlBody, string $textBody = ''): void
    {
        if (!class_exists(PHPMailer::class)) {
            throw new RuntimeException('PHPMailer is not installed. Run "composer install" to enable email delivery.');
        }

        $mailer = new PHPMailer(true);

        try {
            $this->configureMailer($mailer);
            $mailer->addAddress($toEmail, $toName);
            $mailer->isHTML(true);
            $mailer->Subject = $subject;
            $mailer->Body = $htmlBody;
            $mailer->AltBody = $textBody !== '' ? $textBody : strip_tags($htmlBody);

            $mailer->send();
        } catch (PHPMailerException $e) {
            throw new RuntimeException('Unable to send email: ' . $e->getMessage(), 0, $e);
        }
    }

    public function configureMailer(PHPMailer $mailer): void
    {
        $driver = strtolower(trim((string) Env::get('MAIL_MAILER', 'smtp')));
        if ($driver === 'sendmail') {
            $mailer->isSendmail();
        } elseif ($driver === 'mail') {
            $mailer->isMail();
        } else {
            $mailer->isSMTP();
            $mailer->Host = trim((string) Env::get('MAIL_HOST', '127.0.0.1'));
            $mailer->Port = (int) Env::get('MAIL_PORT', 587);

            $username = trim((string) (Env::get('MAIL_USERNAME', '') ?? ''));
            $password = (string) (Env::get('MAIL_PASSWORD', '') ?? '');
            $mailer->SMTPAuth = $username !== '';
            if ($mailer->SMTPAuth) {
                $mailer->Username = $username;
                $mailer->Password = $password;
            }

            $encryption = strtolower(trim((string) (Env::get('MAIL_ENCRYPTION', 'tls') ?? '')));
            if ($encryption === 'ssl' || $encryption === 'smtps') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($encryption === 'tls' || $encryption === 'starttls') {
                $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $mailer->SMTPSecure = '';
                $mailer->SMTPAutoTLS = false;
            }
        }

        $configuredFrom = trim((string) (Env::get('MAIL_FROM_ADDRESS', '') ?? ''));
        $smtpUser = trim((string) (Env::get('MAIL_USERNAME', '') ?? ''));
        if (($configuredFrom === '' || $configuredFrom === 'noreply@renzo.example') && filter_var($smtpUser, FILTER_VALIDATE_EMAIL)) {
            $fromAddress = $smtpUser;
        } else {
            $fromAddress = $configuredFrom !== '' ? $configuredFrom : 'noreply@renzo.example';
        }
        $fromName = trim((string) Env::get('MAIL_FROM_NAME', 'Renzo Restaurant')) ?: 'Renzo Restaurant';

        $mailer->CharSet = 'UTF-8';
        $mailer->setFrom($fromAddress, $fromName);
    }
}
