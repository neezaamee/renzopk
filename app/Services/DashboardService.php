<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ContactMessage;
use App\Models\Deal;
use App\Models\GalleryItem;
use App\Models\MenuItem;
use App\Models\Reservation;

final class DashboardService
{
    public function getSummary(): array
    {
        $menuItems = new MenuItem();
        $deals = new Deal();
        $reservations = new Reservation();
        $messages = new ContactMessage();
        $gallery = new GalleryItem();

        $visibleDeals = $deals->activeVisible();
        $newReservations = $reservations->allWithAdmin('new');
        $unreadMessages = $messages->allOrdered('unread');

        return [
            'stats' => [
                'menu_items' => $menuItems->count(),
                'visible_deals' => count($visibleDeals),
                'new_reservations' => count($newReservations),
                'unread_messages' => count($unreadMessages),
                'gallery_items' => $gallery->count(['is_active' => 1]),
            ],
            'recent_reservations' => array_slice($newReservations, 0, 5),
            'recent_messages' => array_slice($unreadMessages, 0, 5),
        ];
    }
}

