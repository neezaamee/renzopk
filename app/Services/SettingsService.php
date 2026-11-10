<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;

final class SettingsService
{
    private const DEFAULTS = [
        'restaurant_name' => 'Renzo',
        'restaurant_tagline' => 'Wood-Fired Kitchen & Contemporary Table',
        'currency_symbol' => 'Rs. ',
        'whatsapp_number' => '923111200925',
        'contact_phone' => '+92 311 1200925',
        'contact_email' => 'hello@renzo.example',
        'notification_email' => 'neezaamee@gmail.com',
        'contact_address' => '124 Harbor Lane, Culinary District',
        'opening_hours' => "Tue - Thu: 12:00 PM - 10:00 PM\nFri - Sun: 12:00 PM - 11:30 PM\nMon: Closed",
        'map_embed_url' => '',
        'careers_email' => 'neezaamee@gmail.com',
        'careers_intro' => 'We are always looking for passionate chefs, servers, and hospitality professionals to join the Renzo kitchen and dining room.',
        'careers_positions' => "Line Cook (Full-Time)\nPastry Sous Chef (Full-Time)\nFront of House Host / Server (Part-Time)",
        'social_instagram' => '',
        'social_facebook' => '',
        'social_tiktok' => '',
    ];

    private const KEY_GROUPS = [
        'restaurant_name' => 'general',
        'restaurant_tagline' => 'general',
        'currency_symbol' => 'general',
        'whatsapp_number' => 'contact',
        'contact_phone' => 'contact',
        'contact_email' => 'contact',
        'notification_email' => 'contact',
        'contact_address' => 'contact',
        'opening_hours' => 'hours',
        'map_embed_url' => 'contact',
        'careers_email' => 'careers',
        'careers_intro' => 'careers',
        'careers_positions' => 'careers',
        'social_instagram' => 'social',
        'social_facebook' => 'social',
        'social_tiktok' => 'social',
    ];

    private const PDF_TRIGGER_KEYS = [
        'restaurant_name',
        'restaurant_tagline',
        'currency_symbol',
        'contact_phone',
        'contact_email',
        'contact_address',
        'opening_hours',
    ];

    private ?Setting $settings;

    public function __construct(?Setting $settings = null)
    {
        $this->settings = $settings;
    }

    public function all(): array
    {
        try {
            $model = $this->settings ??= new Setting();
            $stored = $model->allAsKeyValue();
        } catch (\Throwable) {
            $stored = [];
        }

        return array_merge(self::DEFAULTS, $stored);
    }

    public function updateFromInput(array $input, int $adminId): void
    {
        $shouldRegeneratePdf = false;
        $model = $this->settings ??= new Setting();

        foreach (self::KEY_GROUPS as $key => $group) {
            if (!array_key_exists($key, $input)) {
                continue;
            }

            $value = trim((string) $input[$key]);
            $model->upsert($key, $value, $group, $adminId);

            if (in_array($key, self::PDF_TRIGGER_KEYS, true)) {
                $shouldRegeneratePdf = true;
            }
        }

        if ($shouldRegeneratePdf) {
            (new MenuPdfService())->regenerateSafely();
        }
    }
}
