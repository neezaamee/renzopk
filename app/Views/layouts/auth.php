<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= e(($pageTitle ?? 'Admin Sign In') . ' — ' . ($settings['restaurant_name'] ?? 'Renzo')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="auth-body">
<div class="auth-wrap">
    <div class="auth-card">
        <div class="auth-brand">
            <span class="brand-mark"><?= e(substr((string) ($settings['restaurant_name'] ?? 'Renzo'), 0, 1) ?: 'R') ?></span>
            <h2><?= e($settings['restaurant_name'] ?? 'Renzo') ?> Admin</h2>
        </div>
        <?php require base_path('app/Views/partials/flash.php'); ?>
        <?= $content ?>
    </div>
</div>
</body>
</html>

