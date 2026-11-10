<div class="card">
    <div class="actions" style="margin-bottom:14px;">
        <a href="<?= e(url('/admin/deals/create')) ?>" class="btn btn-primary">+ Create Deal</a>
    </div>

    <?php if (empty($deals)): ?>
        <p>No deals created yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Discount / Code</th>
                    <th>Schedule Window</th>
                    <th>Live Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($deals as $deal): ?>
                    <tr>
                        <td><strong><?= e($deal['title']) ?></strong></td>
                        <td><?= e($deal['type']) ?></td>
                        <td>
                            <?= e($deal['discount_label'] ?? '') ?>
                            <?php if (!empty($deal['coupon_code'])): ?>
                                <br><code><?= e($deal['coupon_code']) ?></code>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= e($deal['start_datetime'] ?: 'Immediate') ?> &rarr;
                            <?= e($deal['end_datetime'] ?: 'No expiry') ?>
                        </td>
                        <td><span class="pill"><?= e($deal['computed_status']) ?></span></td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/deals/' . $deal['id'] . '/edit')) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="<?= e(url('/admin/deals/' . $deal['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this deal?');">
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

