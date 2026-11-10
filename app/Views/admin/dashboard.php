<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= e((string) ($stats['new_reservations'] ?? 0)) ?></div>
        <div>New Reservations</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= e((string) ($stats['unread_messages'] ?? 0)) ?></div>
        <div>Unread Messages</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= e((string) ($stats['visible_deals'] ?? 0)) ?></div>
        <div>Active Deals</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= e((string) ($stats['menu_items'] ?? 0)) ?></div>
        <div>Menu Items</div>
    </div>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h2 style="margin:0;font-size:1.15rem;">Recent Reservations</h2>
        <a href="<?= e(url('/admin/reservations')) ?>">View All &rarr;</a>
    </div>
    <?php if (empty($recentReservations)): ?>
        <p style="color:#57534e;margin:0;">No new reservation requests.</p>
    <?php else: ?>
        <table>
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
                <?php foreach ($recentReservations as $res): ?>
                    <tr>
                        <td><?= e($res['customer_name']) ?></td>
                        <td><?= e((string) $res['party_size']) ?></td>
                        <td><?= e($res['reservation_date'] . ' ' . substr((string) $res['reservation_time'], 0, 5)) ?></td>
                        <td><?= e($res['customer_phone']) ?></td>
                        <td>
                            <form method="POST" action="<?= e(url('/admin/reservations/' . $res['id'] . '/status')) ?>" class="actions">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_redirect" value="/admin/dashboard">
                                <input type="hidden" name="admin_notes" value="<?= e($res['admin_notes'] ?? '') ?>">
                                <select name="status" aria-label="Status for <?= e($res['customer_name']) ?>" onchange="this.form.submit()">
                                    <?php foreach (\App\Services\ReservationService::STATUSES as $st): ?>
                                        <option value="<?= e($st) ?>" <?= ($res['status'] ?? 'new') === $st ? 'selected' : '' ?>>
                                            <?= e(ucfirst($st)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-secondary btn-sm">Update</button>
                            </form>
                        </td>
                        <td><a href="<?= e(url('/admin/reservations/' . $res['id'])) ?>" class="btn btn-secondary btn-sm">Open</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <h2 style="margin:0;font-size:1.15rem;">Unread Messages</h2>
        <a href="<?= e(url('/admin/messages')) ?>">View All &rarr;</a>
    </div>
    <?php if (empty($recentMessages)): ?>
        <p style="color:#57534e;margin:0;">Inbox is clear.</p>
    <?php else: ?>
        <table>
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
                <?php foreach ($recentMessages as $msg): ?>
                    <tr>
                        <td><?= e($msg['name']) ?> (<?= e($msg['email']) ?>)</td>
                        <td><span class="pill"><?= e(ucfirst((string) $msg['inquiry_type'])) ?></span></td>
                        <td><?= e($msg['subject'] ?: '(No subject)') ?></td>
                        <td><?= e($msg['created_at']) ?></td>
                        <td><a href="<?= e(url('/admin/messages/' . $msg['id'])) ?>" class="btn btn-secondary btn-sm">Read</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
