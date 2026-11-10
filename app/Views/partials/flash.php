<?php
$prefix = $flashPrefix ?? '';
if ($msg = flash($prefix . 'success')): ?>
    <div class="alert alert-success" role="status"><?= e($msg) ?></div>
<?php endif; ?>
<?php if ($err = flash($prefix . 'error')): ?>
    <div class="alert alert-error" role="alert"><?= e($err) ?></div>
<?php endif;
unset($flashPrefix);
