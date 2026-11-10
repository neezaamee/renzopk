<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($status . ' — ' . $title) ?></title>
    <link rel="stylesheet" href="<?= e(asset('css/app.min.css')) ?>">
</head>
<body>
<div class="container" style="padding:80px 20px;text-align:center;max-width:600px;">
    <h1 style="font-size:3rem;margin:0;color:var(--gold);"><?= e((string) $status) ?></h1>
    <h2><?= e($title) ?></h2>
    <p class="muted"><?= e($message) ?></p>
    <p><a href="<?= e(url('/')) ?>" class="btn btn-gold">Return Home</a></p>
</div>
</body>
</html>
