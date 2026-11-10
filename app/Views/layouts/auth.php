<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($pageTitle ?? 'Admin Sign In') . ' — ' . ($settings['restaurant_name'] ?? 'Renzo')) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        <h2 style="margin-top:0;font-family:Georgia,serif;"><?= e($settings['restaurant_name'] ?? 'Renzo') ?> Admin</h2>
        <?php require base_path('app/Views/partials/flash.php'); ?>
        <?= $content ?>
    </div>
</div>
</body>
</html>

