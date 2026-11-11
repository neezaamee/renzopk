<?php
$reservations = $recentReservations ?? [];
$messages = $recentMessages ?? [];
?>
<div class="stats-grid">
    <a href="<?= e(url('/admin/reservations?status=new')) ?>" class="stat-card stat-info">
        <div class="stat-label">New Reservations</div>
        <div class="stat-value"><?= e((string) ($stats['new_reservations'] ?? 0)) ?></div>
    </a>
    <a href="<?= e(url('/admin/messages?filter=unread')) ?>" class="stat-card stat-danger">
        <div class="stat-label">Unread Messages</div>
        <div class="stat-value"><?= e((string) ($stats['unread_messages'] ?? 0)) ?></div>
    </a>
    <a href="<?= e(url('/admin/deals')) ?>" class="stat-card stat-warning">
        <div class="stat-label">Active Deals</div>
        <div class="stat-value"><?= e((string) ($stats['visible_deals'] ?? 0)) ?></div>
    </a>
    <a href="<?= e(url('/admin/menu')) ?>" class="stat-card stat-success">
        <div class="stat-label">Menu Items</div>
        <div class="stat-value"><?= e((string) ($stats['menu_items'] ?? 0)) ?></div>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h2>Recent Reservations</h2>
        <a href="<?= e(url('/admin/reservations')) ?>" class="btn btn-secondary btn-sm">View All &rarr;</a>
    </div>
    <?php if (empty($reservations)): ?>
        <p class="dt-muted" style="margin:0;">No new reservation requests.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th>Guest</th>
                        <th>Party</th>
                        <th>Date &amp; Time</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td data-label="Guest"><strong><?= e($res['customer_name']) ?></strong></td>
                            <td data-label="Party"><?= e((string) $res['party_size']) ?></td>
                            <td data-label="Date &amp; Time"><?= e($res['reservation_date'] . ' ' . substr((string) $res['reservation_time'], 0, 5)) ?></td>
                            <td data-label="Phone"><?= e($res['customer_phone']) ?></td>
                            <td data-label="Status">
                                <form method="POST" action="<?= e(url('/admin/reservations/' . $res['id'] . '/status')) ?>" class="actions">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_redirect" value="/admin/dashboard">
                                    <input type="hidden" name="admin_notes" value="<?= e($res['admin_notes'] ?? '') ?>">
                                    <select name="status" class="compact-select" aria-label="Status for <?= e($res['customer_name']) ?>" onchange="this.form.submit()">
                                        <?php foreach (\App\Services\ReservationService::STATUSES as $st): ?>
                                            <option value="<?= e($st) ?>" <?= ($res['status'] ?? 'new') === $st ? 'selected' : '' ?>>
                                                <?= e(ucfirst($st)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                                </form>
                            </td>
                            <td data-label="Details" class="dt-actions-cell">
                                <a href="<?= e(url('/admin/reservations/' . $res['id'])) ?>" class="btn btn-secondary btn-sm">Open</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h2>Unread Messages</h2>
        <a href="<?= e(url('/admin/messages')) ?>" class="btn btn-secondary btn-sm">View All &rarr;</a>
    </div>
    <?php if (empty($messages)): ?>
        <p class="dt-muted" style="margin:0;">Inbox is clear.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="dt-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Type</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td data-label="From">
                                <strong><?= e($msg['name']) ?></strong>
                                <div class="dt-subtext"><?= e($msg['email']) ?></div>
                            </td>
                            <td data-label="Type">
                                <span class="pill pill-<?= ($msg['inquiry_type'] ?? '') === 'career' ? 'warning' : 'info' ?>"><?= e(ucfirst((string) $msg['inquiry_type'])) ?></span>
                            </td>
                            <td data-label="Subject"><?= e($msg['subject'] ?: '(No subject)') ?></td>
                            <td data-label="Received"><?= e($msg['created_at']) ?></td>
                            <td data-label="Action" class="dt-actions-cell">
                                <a href="<?= e(url('/admin/messages/' . $msg['id'])) ?>" class="btn btn-secondary btn-sm">Read</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
