<div class="card" style="max-width:680px;">
    <p><strong>Guest:</strong> <?= e($reservation['customer_name']) ?></p>
    <p><strong>Phone:</strong> <?= e($reservation['customer_phone']) ?></p>
    <p><strong>Email:</strong> <?= e($reservation['customer_email'] ?? '—') ?></p>
    <p><strong>Party Size:</strong> <?= e((string) $reservation['party_size']) ?></p>
    <p><strong>Date &amp; Time:</strong> <?= e($reservation['reservation_date'] . ' ' . substr((string) $reservation['reservation_time'], 0, 5)) ?></p>
    <p><strong>Special Requests:</strong> <?= e($reservation['special_requests'] ?? 'None') ?></p>
    <p><strong>Last Handled By:</strong> <?= e($reservation['handled_by_name'] ?? 'Unassigned') ?></p>

    <hr style="border:0;border-top:1px solid #e7e5e4;margin:18px 0;">

    <form method="POST" action="<?= e(url('/admin/reservations/' . $reservation['id'] . '/status')) ?>">
        <?= csrf_field() ?>
        <div class="field">
            <label for="r_status">Status</label>
            <select id="r_status" name="status">
                <?php foreach ($statuses as $st): ?>
                    <option value="<?= e($st) ?>" <?= $reservation['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="r_notes">Internal Staff Notes</label>
            <textarea id="r_notes" name="admin_notes" rows="3"><?= e($reservation['admin_notes'] ?? '') ?></textarea>
        </div>
        <div class="actions">
            <button type="submit" class="btn btn-primary">Update Reservation</button>
            <a href="<?= e(url('/admin/reservations')) ?>" class="btn btn-secondary">Back to List</a>
        </div>
    </form>
</div>

