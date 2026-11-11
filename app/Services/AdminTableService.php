<?php

declare(strict_types=1);

namespace App\Services;

final class AdminTableService
{
    public const ALLOWED_PER_PAGE = [10, 25, 50, 100];

    public function buildMenuItemsTable(array $paginated, string $currencySymbol = 'Rs. '): array
    {
        $rows = [];
        foreach ($paginated['items'] as $item) {
            $flags = [
                [
                    'text' => !empty($item['is_active']) ? 'Active' : 'Hidden',
                    'tone' => !empty($item['is_active']) ? 'success' : 'neutral',
                ],
            ];
            if (empty($item['is_available'])) {
                $flags[] = ['text' => 'Out of Stock', 'tone' => 'danger'];
            }
            if (!empty($item['is_featured'])) {
                $flags[] = ['text' => 'Featured', 'tone' => 'warning'];
            }

            $priceText = $item['price'] !== null && $item['price'] !== ''
                ? format_price($item['price'], $currencySymbol)
                : 'Variants';

            $rows[] = [
                'id' => (int) $item['id'],
                'cells' => [
                    [
                        'type' => 'thumb',
                        'src' => !empty($item['image_path']) ? media_url((string) $item['image_path']) : '',
                        'alt' => (string) ($item['name'] ?? ''),
                        'empty_text' => 'No photo',
                    ],
                    [
                        'type' => 'text',
                        'text' => (string) ($item['name'] ?? ''),
                        'strong' => true,
                    ],
                    [
                        'type' => 'text',
                        'text' => (string) ($item['category_name'] ?? '—'),
                    ],
                    [
                        'type' => 'text',
                        'text' => $priceText,
                        'strong' => true,
                    ],
                    [
                        'type' => 'pills',
                        'items' => $flags,
                    ],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'link', 'label' => 'Edit', 'url' => url('/admin/menu/' . (int) $item['id'] . '/edit'), 'style' => 'secondary'],
                            ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/menu/' . (int) $item['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this menu item?'],
                        ],
                    ],
                ],
            ];
        }

        return [
            'id' => 'dt-menu-items',
            'endpoint' => url('/admin/menu'),
            'search_placeholder' => 'Search dishes, categories, prices...',
            'empty_message' => 'No menu items match your criteria.',
            'columns' => [
                ['label' => 'Image', 'sort' => null],
                ['label' => 'Dish', 'sort' => 'name'],
                ['label' => 'Category', 'sort' => 'category'],
                ['label' => 'Base Price', 'sort' => 'price'],
                ['label' => 'Flags', 'sort' => 'status'],
                ['label' => 'Actions', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildCategoriesTable(array $paginated): array
    {
        $rows = [];
        foreach ($paginated['items'] as $cat) {
            $rows[] = [
                'id' => (int) $cat['id'],
                'cells' => [
                    ['type' => 'text', 'text' => (string) ($cat['sort_order'] ?? 0)],
                    ['type' => 'text', 'text' => (string) ($cat['name'] ?? ''), 'strong' => true],
                    ['type' => 'text', 'text' => (string) ($cat['items_count'] ?? 0)],
                    [
                        'type' => 'pills',
                        'items' => [[
                            'text' => !empty($cat['is_active']) ? 'Active' : 'Hidden',
                            'tone' => !empty($cat['is_active']) ? 'success' : 'neutral',
                        ]],
                    ],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'link', 'label' => 'Edit', 'url' => url('/admin/menu/categories/' . (int) $cat['id'] . '/edit'), 'style' => 'secondary'],
                            ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/menu/categories/' . (int) $cat['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this category?'],
                        ],
                    ],
                ],
            ];
        }

        return [
            'id' => 'dt-categories',
            'endpoint' => url('/admin/menu/categories'),
            'search_placeholder' => 'Search categories...',
            'empty_message' => 'No categories match your criteria.',
            'columns' => [
                ['label' => 'Order', 'sort' => 'sort_order'],
                ['label' => 'Name', 'sort' => 'name'],
                ['label' => 'Items', 'sort' => 'items_count'],
                ['label' => 'Status', 'sort' => 'is_active'],
                ['label' => 'Actions', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildTagsTable(array $paginated): array
    {
        $rows = [];
        foreach ($paginated['items'] as $tag) {
            $rawColor = (string) ($tag['color'] ?? '');
            $safeHex = preg_match('/^#[0-9a-f]{3,8}$/i', $rawColor) === 1 ? $rawColor : '';

            $rows[] = [
                'id' => (int) $tag['id'],
                'cells' => [
                    ['type' => 'text', 'text' => (string) ($tag['name'] ?? ''), 'strong' => true],
                    ['type' => 'code', 'text' => (string) ($tag['slug'] ?? '')],
                    ['type' => 'color', 'text' => $rawColor !== '' ? $rawColor : '—', 'hex' => $safeHex],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/menu/tags/' . (int) $tag['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this tag?'],
                        ],
                    ],
                ],
            ];
        }

        return [
            'id' => 'dt-tags',
            'endpoint' => url('/admin/menu/tags'),
            'search_placeholder' => 'Search tags or slugs...',
            'empty_message' => 'No tags match your criteria.',
            'columns' => [
                ['label' => 'Name', 'sort' => 'name'],
                ['label' => 'Slug', 'sort' => 'slug'],
                ['label' => 'Color', 'sort' => 'color'],
                ['label' => 'Action', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildDealsTable(array $paginated, string $selectedType = ''): array
    {
        $typeLabels = [
            'permanent' => 'Permanent',
            'time_limited' => 'Time Limited',
            'coupon' => 'Coupon',
        ];
        $statusTones = [
            'Active' => 'success',
            'Scheduled' => 'warning',
            'Expired' => 'danger',
            'Inactive' => 'neutral',
        ];

        $rows = [];
        foreach ($paginated['items'] as $deal) {
            $type = (string) ($deal['type'] ?? 'time_limited');
            $computedStatus = (string) ($deal['computed_status'] ?? 'Inactive');
            $start = !empty($deal['start_datetime']) ? (string) $deal['start_datetime'] : 'Immediate';
            $end = !empty($deal['end_datetime']) ? (string) $deal['end_datetime'] : 'No expiry';

            $rows[] = [
                'id' => (int) $deal['id'],
                'cells' => [
                    ['type' => 'text', 'text' => (string) ($deal['title'] ?? ''), 'strong' => true],
                    [
                        'type' => 'pills',
                        'items' => [['text' => $typeLabels[$type] ?? ucfirst($type), 'tone' => 'info']],
                    ],
                    [
                        'type' => 'code',
                        'prefix' => (string) ($deal['discount_label'] ?? ''),
                        'text' => (string) ($deal['coupon_code'] ?? ''),
                    ],
                    ['type' => 'text', 'text' => $start . ' → ' . $end],
                    [
                        'type' => 'pills',
                        'items' => [['text' => $computedStatus, 'tone' => $statusTones[$computedStatus] ?? 'neutral']],
                    ],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'link', 'label' => 'Edit', 'url' => url('/admin/deals/' . (int) $deal['id'] . '/edit'), 'style' => 'secondary'],
                            ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/deals/' . (int) $deal['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this deal?'],
                        ],
                    ],
                ],
            ];
        }

        $filters = [
            ['label' => 'All', 'param' => 'type', 'value' => '', 'active' => $selectedType === '', 'url' => url('/admin/deals')],
            ['label' => 'Time Limited', 'param' => 'type', 'value' => 'time_limited', 'active' => $selectedType === 'time_limited', 'url' => url('/admin/deals?type=time_limited')],
            ['label' => 'Coupon', 'param' => 'type', 'value' => 'coupon', 'active' => $selectedType === 'coupon', 'url' => url('/admin/deals?type=coupon')],
            ['label' => 'Permanent', 'param' => 'type', 'value' => 'permanent', 'active' => $selectedType === 'permanent', 'url' => url('/admin/deals?type=permanent')],
        ];

        return [
            'id' => 'dt-deals',
            'endpoint' => url('/admin/deals'),
            'filter_param' => 'type',
            'filter_value' => $selectedType,
            'filters' => $filters,
            'search_placeholder' => 'Search deals, coupon codes, discounts...',
            'empty_message' => 'No deals match your criteria.',
            'columns' => [
                ['label' => 'Title', 'sort' => 'title'],
                ['label' => 'Type', 'sort' => 'type'],
                ['label' => 'Discount / Code', 'sort' => null],
                ['label' => 'Schedule Window', 'sort' => 'end_datetime'],
                ['label' => 'Live Status', 'sort' => 'is_active'],
                ['label' => 'Actions', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildGalleryTable(array $paginated, string $selectedType = ''): array
    {
        $rows = [];
        foreach ($paginated['items'] as $item) {
            $type = (string) ($item['type'] ?? 'photo');
            $thumbUrl = '';
            if ($type === 'youtube' && !empty($item['youtube_id'])) {
                $thumbUrl = 'https://img.youtube.com/vi/' . rawurlencode((string) $item['youtube_id']) . '/hqdefault.jpg';
            } elseif (!empty($item['image_path'])) {
                $thumbUrl = media_url((string) $item['image_path']);
            }

            $rows[] = [
                'id' => (int) $item['id'],
                'cells' => [
                    [
                        'type' => 'thumb',
                        'src' => $thumbUrl,
                        'alt' => (string) ($item['alt_text'] ?: ($item['title'] ?: 'Gallery item')),
                        'empty_text' => 'No preview',
                    ],
                    [
                        'type' => 'pills',
                        'items' => [[
                            'text' => ucfirst($type),
                            'tone' => $type === 'youtube' ? 'warning' : 'info',
                        ]],
                    ],
                    ['type' => 'text', 'text' => (string) ($item['title'] ?: 'Untitled'), 'strong' => true],
                    ['type' => 'text', 'text' => (string) ($item['category'] ?: '—')],
                    ['type' => 'text', 'text' => (string) ($item['sort_order'] ?? 0)],
                    [
                        'type' => 'pills',
                        'items' => [[
                            'text' => !empty($item['is_active']) ? 'Visible' : 'Hidden',
                            'tone' => !empty($item['is_active']) ? 'success' : 'neutral',
                        ]],
                    ],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'link', 'label' => 'Edit', 'url' => url('/admin/gallery/' . (int) $item['id'] . '/edit'), 'style' => 'secondary'],
                            ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/gallery/' . (int) $item['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this gallery item?'],
                        ],
                    ],
                ],
            ];
        }

        $filters = [
            ['label' => 'All', 'param' => 'type', 'value' => '', 'active' => $selectedType === '', 'url' => url('/admin/gallery')],
            ['label' => 'Photos', 'param' => 'type', 'value' => 'photo', 'active' => $selectedType === 'photo', 'url' => url('/admin/gallery?type=photo')],
            ['label' => 'YouTube', 'param' => 'type', 'value' => 'youtube', 'active' => $selectedType === 'youtube', 'url' => url('/admin/gallery?type=youtube')],
        ];

        return [
            'id' => 'dt-gallery',
            'endpoint' => url('/admin/gallery'),
            'filter_param' => 'type',
            'filter_value' => $selectedType,
            'filters' => $filters,
            'search_placeholder' => 'Search titles, categories, captions...',
            'empty_message' => 'No gallery items match your criteria.',
            'columns' => [
                ['label' => 'Preview', 'sort' => null],
                ['label' => 'Type', 'sort' => 'type'],
                ['label' => 'Title', 'sort' => 'title'],
                ['label' => 'Category', 'sort' => 'category'],
                ['label' => 'Order', 'sort' => 'sort_order'],
                ['label' => 'Status', 'sort' => 'is_active'],
                ['label' => 'Actions', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildSlidersTable(array $paginated): array
    {
        $rows = [];
        foreach ($paginated['items'] as $slide) {
            $rows[] = [
                'id' => (int) $slide['id'],
                'cells' => [
                    [
                        'type' => 'thumb',
                        'src' => !empty($slide['image_path']) ? media_url((string) $slide['image_path']) : '',
                        'alt' => (string) ($slide['alt_text'] ?: ($slide['title'] ?: 'Slide banner')),
                        'fallback_pill' => ['text' => 'Gradient Placeholder', 'tone' => 'warning'],
                    ],
                    [
                        'type' => 'text',
                        'text' => (string) ($slide['title'] ?: 'Untitled Slide'),
                        'strong' => true,
                        'subtext' => (string) ($slide['subtitle'] ?? ''),
                    ],
                    ['type' => 'text', 'text' => (string) ($slide['cta_text'] ?: '—')],
                    ['type' => 'text', 'text' => (string) ($slide['sort_order'] ?? 0)],
                    [
                        'type' => 'pills',
                        'items' => [[
                            'text' => !empty($slide['is_active']) ? 'Active' : 'Hidden',
                            'tone' => !empty($slide['is_active']) ? 'success' : 'neutral',
                        ]],
                    ],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'link', 'label' => 'Edit', 'url' => url('/admin/sliders/' . (int) $slide['id'] . '/edit'), 'style' => 'secondary'],
                            ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/sliders/' . (int) $slide['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this slide?'],
                        ],
                    ],
                ],
            ];
        }

        return [
            'id' => 'dt-sliders',
            'endpoint' => url('/admin/sliders'),
            'search_placeholder' => 'Search headlines, subtitles, CTAs...',
            'empty_message' => 'No slider banners match your criteria.',
            'columns' => [
                ['label' => 'Banner', 'sort' => null],
                ['label' => 'Title', 'sort' => 'title'],
                ['label' => 'CTA', 'sort' => 'cta_text'],
                ['label' => 'Order', 'sort' => 'sort_order'],
                ['label' => 'Status', 'sort' => 'is_active'],
                ['label' => 'Actions', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildReservationsTable(array $paginated, string $selectedStatus = '', array $statuses = []): array
    {
        $toneMap = [
            'new' => 'info',
            'contacted' => 'warning',
            'confirmed' => 'success',
            'cancelled' => 'danger',
        ];

        $rows = [];
        foreach ($paginated['items'] as $res) {
            $status = (string) ($res['status'] ?? 'new');
            $rows[] = [
                'id' => (int) $res['id'],
                'cells' => [
                    ['type' => 'text', 'text' => (string) $res['id']],
                    [
                        'type' => 'text',
                        'text' => (string) ($res['customer_name'] ?? ''),
                        'strong' => true,
                        'subtext' => (string) ($res['customer_phone'] ?? ''),
                    ],
                    ['type' => 'text', 'text' => (string) ($res['party_size'] ?? '')],
                    ['type' => 'text', 'text' => (string) $res['reservation_date'] . ' ' . substr((string) $res['reservation_time'], 0, 5)],
                    [
                        'type' => 'pills',
                        'items' => [['text' => ucfirst($status), 'tone' => $toneMap[$status] ?? 'neutral']],
                    ],
                    ['type' => 'text', 'text' => (string) ($res['handled_by_name'] ?? '—')],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'link', 'label' => 'Manage', 'url' => url('/admin/reservations/' . (int) $res['id']), 'style' => 'secondary'],
                            ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/reservations/' . (int) $res['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this reservation?'],
                        ],
                    ],
                ],
            ];
        }

        $filters = [
            ['label' => 'All', 'param' => 'status', 'value' => '', 'active' => $selectedStatus === '', 'url' => url('/admin/reservations')],
        ];
        foreach ($statuses as $st) {
            $filters[] = [
                'label' => ucfirst($st),
                'param' => 'status',
                'value' => $st,
                'active' => $selectedStatus === $st,
                'url' => url('/admin/reservations?status=' . rawurlencode($st)),
            ];
        }

        return [
            'id' => 'dt-reservations',
            'endpoint' => url('/admin/reservations'),
            'filter_param' => 'status',
            'filter_value' => $selectedStatus,
            'filters' => $filters,
            'search_placeholder' => 'Search guest, phone, email, ID...',
            'empty_message' => 'No reservations match this filter.',
            'columns' => [
                ['label' => '#', 'sort' => 'id'],
                ['label' => 'Guest', 'sort' => 'customer_name'],
                ['label' => 'Party', 'sort' => 'party_size'],
                ['label' => 'Date & Time', 'sort' => 'reservation_date'],
                ['label' => 'Status', 'sort' => 'status'],
                ['label' => 'Handled By', 'sort' => null],
                ['label' => 'Actions', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildMessagesTable(array $paginated, string $selectedFilter = ''): array
    {
        $rows = [];
        foreach ($paginated['items'] as $msg) {
            $isUnread = empty($msg['is_read']);
            $actions = [
                ['kind' => 'link', 'label' => 'Open', 'url' => url('/admin/messages/' . (int) $msg['id']), 'style' => 'secondary'],
            ];
            if ($isUnread) {
                $actions[] = ['kind' => 'post', 'label' => 'Mark Read', 'url' => url('/admin/messages/' . (int) $msg['id'] . '/read'), 'style' => 'secondary'];
            }
            $actions[] = ['kind' => 'post', 'label' => 'Delete', 'url' => url('/admin/messages/' . (int) $msg['id'] . '/delete'), 'style' => 'danger', 'confirm' => 'Delete this message?'];

            $type = (string) ($msg['inquiry_type'] ?? 'general');
            $rows[] = [
                'id' => (int) $msg['id'],
                'cells' => [
                    [
                        'type' => 'pills',
                        'items' => [[
                            'text' => $isUnread ? 'Unread' : 'Read',
                            'tone' => $isUnread ? 'danger' : 'success',
                        ]],
                    ],
                    [
                        'type' => 'text',
                        'text' => (string) ($msg['name'] ?? ''),
                        'strong' => true,
                        'subtext' => (string) ($msg['email'] ?? ''),
                    ],
                    [
                        'type' => 'pills',
                        'items' => [['text' => ucfirst($type), 'tone' => $type === 'career' ? 'warning' : 'info']],
                    ],
                    ['type' => 'text', 'text' => (string) ($msg['subject'] ?: '(No subject)')],
                    ['type' => 'text', 'text' => (string) ($msg['created_at'] ?? '')],
                    [
                        'type' => 'actions',
                        'items' => $actions,
                    ],
                ],
            ];
        }

        $filters = [
            ['label' => 'All', 'param' => 'filter', 'value' => '', 'active' => $selectedFilter === '', 'url' => url('/admin/messages')],
            ['label' => 'Unread', 'param' => 'filter', 'value' => 'unread', 'active' => $selectedFilter === 'unread', 'url' => url('/admin/messages?filter=unread')],
            ['label' => 'Careers', 'param' => 'filter', 'value' => 'career', 'active' => $selectedFilter === 'career', 'url' => url('/admin/messages?filter=career')],
        ];

        return [
            'id' => 'dt-messages',
            'endpoint' => url('/admin/messages'),
            'filter_param' => 'filter',
            'filter_value' => $selectedFilter,
            'filters' => $filters,
            'search_placeholder' => 'Search sender, email, subject, message...',
            'empty_message' => 'No messages match your criteria.',
            'columns' => [
                ['label' => 'Status', 'sort' => 'is_read'],
                ['label' => 'From', 'sort' => 'name'],
                ['label' => 'Type', 'sort' => 'inquiry_type'],
                ['label' => 'Subject', 'sort' => null],
                ['label' => 'Received', 'sort' => 'created_at'],
                ['label' => 'Actions', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }

    public function buildAdminsTable(array $paginated): array
    {
        $rows = [];
        foreach ($paginated['items'] as $adm) {
            $role = (string) ($adm['role'] ?? 'manager');
            $isActive = !empty($adm['is_active']);

            $rows[] = [
                'id' => (int) $adm['id'],
                'cells' => [
                    ['type' => 'text', 'text' => (string) ($adm['name'] ?? ''), 'strong' => true],
                    ['type' => 'text', 'text' => (string) ($adm['email'] ?? '')],
                    [
                        'type' => 'pills',
                        'items' => [['text' => ucfirst($role), 'tone' => $role === 'owner' ? 'warning' : 'info']],
                    ],
                    [
                        'type' => 'pills',
                        'items' => [['text' => $isActive ? 'Active' : 'Disabled', 'tone' => $isActive ? 'success' : 'danger']],
                    ],
                    ['type' => 'text', 'text' => (string) ($adm['last_login_at'] ?? 'Never')],
                    [
                        'type' => 'actions',
                        'items' => [
                            ['kind' => 'post', 'label' => $isActive ? 'Disable' : 'Enable', 'url' => url('/admin/admins/' . (int) $adm['id'] . '/toggle'), 'style' => 'secondary'],
                        ],
                    ],
                ],
            ];
        }

        return [
            'id' => 'dt-admins',
            'endpoint' => url('/admin/admins'),
            'search_placeholder' => 'Search admin name, email, role...',
            'empty_message' => 'No admin accounts match your criteria.',
            'columns' => [
                ['label' => 'Name', 'sort' => 'name'],
                ['label' => 'Email', 'sort' => 'email'],
                ['label' => 'Role', 'sort' => 'role'],
                ['label' => 'Status', 'sort' => 'is_active'],
                ['label' => 'Last Login', 'sort' => 'last_login_at'],
                ['label' => 'Action', 'sort' => null],
            ],
            'rows' => $rows,
            'pagination' => $paginated['pagination'],
        ];
    }
}

