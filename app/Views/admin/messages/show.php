<div class="card" style="max-width:700px;">
    <p><strong>From:</strong> <?= e($message['name']) ?> &lt;<?= e($message['email']) ?>&gt;</p>
    <p><strong>Phone:</strong> <?= e($message['phone'] ?? '—') ?></p>
    <p><strong>Type:</strong> <span class="pill"><?= e(ucfirst((string) $message['inquiry_type'])) ?></span></p>
    <p><strong>Subject:</strong> <?= e($message['subject'] ?? '—') ?></p>
    <p><strong>Received:</strong> <?= e($message['created_at']) ?></p>

    <div style="background:#fafaf9;border:1px solid #e7e5e4;padding:14px;border-radius:6px;white-space:pre-line;margin:16px 0;">
        <?= e($message['message']) ?>
    </div>

    <div class="actions">
        <a href="<?= e(url('/admin/messages')) ?>" class="btn btn-secondary">Back to Inbox</a>
        <form method="POST" action="<?= e(url('/admin/messages/' . $message['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this message?');">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-danger">Delete Message</button>
        </form>
    </div>
</div>

