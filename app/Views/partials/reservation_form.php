<div class="form-panel">
    <?php $flashPrefix = 'reservation_'; require base_path('app/Views/partials/flash.php'); ?>
    <form method="POST" action="<?= e(url('/reservations')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="_redirect" value="<?= e($redirectPath ?? '/#reserve') ?>">
        <div class="hp-field" aria-hidden="true">
            <label for="res_hp_website">Website</label>
            <input id="res_hp_website" type="text" name="_hp_website" tabindex="-1" autocomplete="off">
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="res_name">Full Name *</label>
                <input id="res_name" type="text" name="customer_name" value="<?= e(old('customer_name')) ?>" required>
            </div>
            <div class="field">
                <label for="res_phone">Phone *</label>
                <input id="res_phone" type="text" name="customer_phone" value="<?= e(old('customer_phone')) ?>" required>
            </div>
            <div class="field">
                <label for="res_email">Email</label>
                <input id="res_email" type="email" name="customer_email" value="<?= e(old('customer_email')) ?>">
            </div>
            <div class="field">
                <label for="res_party">Guests *</label>
                <input id="res_party" type="number" name="party_size" min="1" max="50" value="<?= e(old('party_size', '2')) ?>" required>
            </div>
            <div class="field">
                <label for="res_date">Date *</label>
                <input id="res_date" type="date" name="reservation_date" min="<?= e(date('Y-m-d')) ?>" value="<?= e(old('reservation_date', date('Y-m-d'))) ?>" required>
            </div>
            <div class="field">
                <label for="res_time">Time *</label>
                <input id="res_time" type="time" name="reservation_time" value="<?= e(old('reservation_time', '19:00')) ?>" required>
            </div>
        </div>
        <div class="field">
            <label for="res_notes">Special Requests</label>
            <textarea id="res_notes" name="special_requests" rows="2" placeholder="Dietary notes, occasion, seating preference"><?= e(old('special_requests')) ?></textarea>
        </div>
        <button type="submit" class="btn btn-gold">Request Table Reservation</button>
    </form>
</div>
