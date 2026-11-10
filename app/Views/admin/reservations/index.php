<div class="card">
    <div class="actions" style="margin-bottom:14px;">
        <a href="<?= e(url('/admin/reservations')) ?>" class="btn btn-sm <?= $selectedStatus === '' ? 'btn-primary' : 'btn-secondary' ?>">All</a>
        <?php foreach ($statuses as $status): ?>
            <a href="<?= e(url('/admin/reservations?status=' . urlencode($status))) ?>" class="btn btn-sm <?= $selectedStatus === $status ? 'btn-primary' : 'btn-secondary' ?>">
                <?= e(ucfirst($status)) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($reservations)): ?>
        <p>No reservations matching this filter.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Guest</th>
                    <th>Party</th>
                    <th>Date &amp; Time</th>
                    <th>Status</th>
                    <th>Handled By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $res): ?>
                    <tr>
                        <td><?= e((string) $res['id']) ?></td>
                        <td>
                            <strong><?= e($res['customer_name']) ?></strong><br>
                            <small><?= e($res['customer_phone']) ?></small>
                        </td>
                        <td><?= e((string) $res['party_size']) ?></td>
                        <td><?= e($res['reservation_date'] . ' ' . substr((string) $res['reservation_time'], 0, 5)) ?></td>
                        <td><span class="pill"><?= e(ucfirst((string) $res['status'])) ?></span></td>
                        <td><?= e($res['handled_by_name'] ?? '—') ?></td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/reservations/' . $res['id'])) ?>" class="btn btn-secondary btn-sm">Manage</a>
                            <form method="POST" action="<?= e(url('/admin/reservations/' . $res['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this reservation?');">
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

