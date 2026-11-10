<?php $currency = (string) ($settings['currency_symbol'] ?? 'Rs. '); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= e(($settings['restaurant_name'] ?? 'Renzo') . ' Menu') ?></title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            color: #1b120d;
            font-size: 11px;
            line-height: 1.45;
            margin: 24px 28px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #b08b22;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #1b120d;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .header p { margin: 4px 0 0; color: #57534e; font-size: 10px; }
        .category { margin-bottom: 18px; page-break-inside: avoid; }
        .category-title {
            font-size: 14px;
            color: #9a3412;
            border-bottom: 1px solid #d6cec2;
            padding-bottom: 4px;
            margin: 0 0 8px;
        }
        .category-desc { margin: 0 0 8px; color: #57534e; font-size: 10px; }
        .item { margin-bottom: 9px; page-break-inside: avoid; }
        .item-top { width: 100%; border-collapse: collapse; }
        .item-name { font-weight: bold; font-size: 11.5px; }
        .item-price { text-align: right; font-weight: bold; color: #9a3412; white-space: nowrap; }
        .item-desc { color: #57534e; font-size: 10px; margin-top: 2px; }
        .variants { margin-top: 3px; font-size: 10px; color: #44403c; }
        .tags { margin-top: 2px; font-size: 9px; color: #78716c; }
        .empty-note { text-align: center; color: #57534e; padding: 30px 0; }
        .footer {
            margin-top: 24px;
            border-top: 1px solid #d6cec2;
            padding-top: 8px;
            text-align: center;
            font-size: 9px;
            color: #78716c;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= e($settings['restaurant_name'] ?? 'Renzo') ?></h1>
        <p><?= e($settings['restaurant_tagline'] ?? '') ?></p>
        <p><?= e($settings['contact_address'] ?? '') ?> &middot; <?= e($settings['contact_phone'] ?? '') ?></p>
    </div>

    <?php $hasPrintedItem = false; ?>
    <?php foreach ($categories as $category): ?>
        <?php if (empty($category['items'])) { continue; } ?>
        <?php $hasPrintedItem = true; ?>
        <div class="category">
            <h2 class="category-title"><?= e($category['name']) ?></h2>
            <?php if (!empty($category['description'])): ?>
                <p class="category-desc"><?= e($category['description']) ?></p>
            <?php endif; ?>

            <?php foreach ($category['items'] as $item): ?>
                <div class="item">
                    <table class="item-top">
                        <tr>
                            <td class="item-name">
                                <?= e($item['name']) ?>
                                <?php if (empty($item['is_available'])): ?>
                                    <span style="font-weight:normal;color:#991b1b;">(Currently Unavailable)</span>
                                <?php endif; ?>
                            </td>
                            <td class="item-price">
                                <?php if ($item['price'] !== null && $item['price'] !== ''): ?>
                                    <?= e(format_price($item['price'], $currency)) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    <?php if (!empty($item['description'])): ?>
                        <div class="item-desc"><?= e($item['description']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($item['variants'])): ?>
                        <div class="variants">
                            <?php
                            $variantLabels = array_map(
                                fn (array $v): string => (string) $v['name'] . ': ' . format_price($v['price'], $currency),
                                $item['variants']
                            );
                            ?>
                            Options: <?= e(implode(' | ', $variantLabels)) ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($item['tags'])): ?>
                        <div class="tags">
                            Tags: <?= e(implode(', ', array_column($item['tags'], 'name'))) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <?php if (!$hasPrintedItem): ?>
        <p class="empty-note">Our menu is currently being updated. Please check back shortly.</p>
    <?php endif; ?>

    <div class="footer">
        Generated on <?= e($generatedAt ?? date('F j, Y')) ?> &middot; <?= e($settings['contact_email'] ?? '') ?>
    </div>
</body>
</html>
