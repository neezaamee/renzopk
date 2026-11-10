<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reservation;
use InvalidArgumentException;
use Throwable;

final class ReservationService
{
    public const STATUSES = ['new', 'contacted', 'confirmed', 'cancelled'];
    private const DEFAULT_NOTIFY_EMAIL = 'neezaamee@gmail.com';

    private Reservation $reservations;
    private MailService $mail;

    public function __construct(?Reservation $reservations = null, ?MailService $mail = null)
    {
        $this->reservations = $reservations ?? new Reservation();
        $this->mail = $mail ?? new MailService();
    }

    public function submitPublic(array $input): int
    {
        if (trim((string) ($input['_hp_website'] ?? '')) !== '') {
            throw new InvalidArgumentException('Submission could not be processed.');
        }

        $name = trim((string) ($input['customer_name'] ?? ''));
        $email = trim((string) ($input['customer_email'] ?? ''));
        $phone = trim((string) ($input['customer_phone'] ?? ''));
        $partySize = (int) ($input['party_size'] ?? 0);
        $date = trim((string) ($input['reservation_date'] ?? ''));
        $time = trim((string) ($input['reservation_time'] ?? ''));

        if ($name === '' || $phone === '') {
            throw new InvalidArgumentException('Name and phone number are required.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        if ($partySize < 1 || $partySize > 50) {
            throw new InvalidArgumentException('Party size must be between 1 and 50 guests.');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || strtotime($date) === false) {
            throw new InvalidArgumentException('Please select a valid reservation date.');
        }

        if ($date < date('Y-m-d')) {
            throw new InvalidArgumentException('Reservation date cannot be in the past.');
        }

        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
            throw new InvalidArgumentException('Please select a valid reservation time.');
        }

        $payload = [
            'customer_name' => $name,
            'customer_email' => $email !== '' ? $email : null,
            'customer_phone' => $phone,
            'party_size' => $partySize,
            'reservation_date' => $date,
            'reservation_time' => strlen($time) === 5 ? $time . ':00' : $time,
            'special_requests' => trim((string) ($input['special_requests'] ?? '')) ?: null,
            'status' => 'new',
        ];

        $reservationId = (int) $this->reservations->create($payload);

        try {
            $settings = (new SettingsService())->all();
            $notifyTo = trim((string) ($settings['notification_email'] ?? self::DEFAULT_NOTIFY_EMAIL));
            $this->mail->sendReservationNotification($payload, $notifyTo !== '' ? $notifyTo : self::DEFAULT_NOTIFY_EMAIL);
        } catch (Throwable $e) {
            error_log('[Renzo] Reservation notification email failed: ' . $e->getMessage());
        }

        return $reservationId;
    }

    public function listAdmin(?string $status = null): array
    {
        if ($status !== null && $status !== '' && !in_array($status, self::STATUSES, true)) {
            $status = null;
        }

        return $this->reservations->allWithAdmin($status);
    }

    public function findAdmin(int $id): ?array
    {
        return $this->reservations->findWithAdmin($id);
    }

    public function updateStatus(int $id, string $status, string $adminNotes, int $adminId): void
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Invalid reservation status.');
        }

        if ($this->reservations->find($id) === null) {
            throw new InvalidArgumentException('Reservation not found.');
        }

        $this->reservations->update($id, [
            'status' => $status,
            'admin_notes' => trim($adminNotes) ?: null,
            'handled_by_admin_id' => $adminId,
        ]);
    }

    public function delete(int $id): void
    {
        $this->reservations->delete($id);
    }
}
