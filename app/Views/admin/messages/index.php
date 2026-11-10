<div class="card">
    <div class="actions" style="margin-bottom:14px;">
        <a href="<?= e(url('/admin/messages')) ?>" class="btn btn-sm <?= $selectedFilter === '' ? 'btn-primary' : 'btn-secondary' ?>">All</a>
        <a href="<?= e(url('/admin/messages?filter=unread')) ?>" class="btn btn-sm <?= $selectedFilter === 'unread' ? 'btn-primary' : 'btn-secondary' ?>">Unread</a>
        <a href="<?= e(url('/admin/messages?filter=career')) ?>" class="btn btn-sm <?= $selectedFilter === 'career' ? 'btn-primary' : 'btn-secondary' ?>">Careers</a>
    </div>

    <?php if (empty($messages)): ?>
        <p>No messages found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Status</th>
                    <th>From</th>
                    <th>Type</th>
                    <th>Subject</th>
                    <th>Received</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $msg): ?>
                    <tr>
                        <td><span class="pill"><?= empty($msg['is_read']) ? 'Unread' : 'Read' ?></span></td>
                        <td>
                            <strong><?= e($msg['name']) ?></strong><br>
                            <small><?= e($msg['email']) ?></small>
                        </td>
                        <td><?= e(ucfirst((string) $msg['inquiry_type'])) ?></td>
                        <td><?= e($msg['subject'] ?: '(No subject)') ?></td>
                        <td><?= e($msg['created_at']) ?></td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/messages/' . $msg['id'])) ?>" class="btn btn-secondary btn-sm">Open</a>
                            <?php if (empty($msg['is_read'])): ?>
                                <form method="POST" action="<?= e(url('/admin/messages/' . $msg['id'] . '/read')) ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-secondary btn-sm">Mark Read</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" action="<?= e(url('/admin/messages/' . $msg['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this message?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

