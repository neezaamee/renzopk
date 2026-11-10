<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContactMessage;
use InvalidArgumentException;
use Throwable;

final class ContactService
{
    private const INQUIRY_TYPES = ['general', 'catering', 'feedback', 'career'];
    private const DEFAULT_NOTIFY_EMAIL = 'neezaamee@gmail.com';

    private ContactMessage $messages;
    private MailService $mail;

    public function __construct(?ContactMessage $messages = null, ?MailService $mail = null)
    {
        $this->messages = $messages ?? new ContactMessage();
        $this->mail = $mail ?? new MailService();
    }

    public function submit(array $input, string $ipAddress, string $defaultType = 'general'): int
    {
        if (trim((string) ($input['_hp_website'] ?? '')) !== '') {
            throw new InvalidArgumentException('Submission could not be processed.');
        }

        $name = trim((string) ($input['name'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $message = trim((string) ($input['message'] ?? ''));
        $type = trim((string) ($input['inquiry_type'] ?? $defaultType));

        if ($name === '' || $message === '') {
            throw new InvalidArgumentException('Name and message are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        if (!in_array($type, self::INQUIRY_TYPES, true)) {
            $type = $defaultType;
        }

        $payload = [
            'name' => $name,
            'email' => $email,
            'phone' => trim((string) ($input['phone'] ?? '')) ?: null,
            'subject' => trim((string) ($input['subject'] ?? '')) ?: null,
            'inquiry_type' => $type,
            'message' => $message,
            'is_read' => 0,
            'ip_address' => $ipAddress,
        ];

        $messageId = (int) $this->messages->create($payload);

        try {
            $settings = (new SettingsService())->all();
            $notifyTo = trim((string) ($settings['notification_email'] ?? self::DEFAULT_NOTIFY_EMAIL));
            $this->mail->sendContactNotification($payload, $notifyTo !== '' ? $notifyTo : self::DEFAULT_NOTIFY_EMAIL);
        } catch (Throwable $e) {
            error_log('[Renzo] Contact notification email failed: ' . $e->getMessage());
        }

        return $messageId;
    }

    public function listAdmin(?string $filter = null): array
    {
        return $this->messages->allOrdered($filter);
    }

    public function viewAndMarkRead(int $id): ?array
    {
        $message = $this->messages->find($id);
        if ($message === null) {
            return null;
        }

        if (empty($message['is_read'])) {
            $this->messages->markRead($id);
            $message = $this->messages->find($id);
        }

        return $message;
    }

    public function markRead(int $id): void
    {
        $this->messages->markRead($id);
    }

    public function delete(int $id): void
    {
        $this->messages->delete($id);
    }
}
