<div class="card card-form">
    <form method="POST" action="<?= e(url('/admin/settings')) ?>">
        <?= csrf_field() ?>
        <div class="form-grid">
            <div class="field">
                <label for="st_name">Restaurant Name</label>
                <input id="st_name" type="text" name="restaurant_name" value="<?= e($allSettings['restaurant_name'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="st_currency">Currency Symbol / Prefix</label>
                <input id="st_currency" type="text" name="currency_symbol" value="<?= e($allSettings['currency_symbol'] ?? 'Rs. ') ?>">
            </div>
        </div>
        <div class="field">
            <label for="st_tag">Tagline</label>
            <input id="st_tag" type="text" name="restaurant_tagline" value="<?= e($allSettings['restaurant_tagline'] ?? '') ?>">
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="st_whatsapp">WhatsApp Number (digits with country code)</label>
                <input id="st_whatsapp" type="text" name="whatsapp_number" value="<?= e($allSettings['whatsapp_number'] ?? '923111200925') ?>">
            </div>
            <div class="field">
                <label for="st_phone">Display Phone</label>
                <input id="st_phone" type="text" name="contact_phone" value="<?= e($allSettings['contact_phone'] ?? '') ?>">
            </div>
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="st_email">Public Contact Email</label>
                <input id="st_email" type="email" name="contact_email" value="<?= e($allSettings['contact_email'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="st_notify">Submissions Notification Email</label>
                <input id="st_notify" type="email" name="notification_email" value="<?= e($allSettings['notification_email'] ?? 'neezaamee@gmail.com') ?>">
            </div>
        </div>
        <div class="field">
            <label for="st_addr">Address</label>
            <input id="st_addr" type="text" name="contact_address" value="<?= e($allSettings['contact_address'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="st_map">Map Embed URL (Google Maps https:// embed src)</label>
            <input id="st_map" type="url" name="map_embed_url" value="<?= e($allSettings['map_embed_url'] ?? '') ?>" placeholder="https://www.google.com/maps/embed?pb=...">
        </div>
        <div class="field">
            <label for="st_hours">Opening Hours</label>
            <textarea id="st_hours" name="opening_hours" rows="4"><?= e($allSettings['opening_hours'] ?? '') ?></textarea>
            <small style="display:block;color:#57534e;margin-top:4px;">Enter one line per schedule row, e.g. <code>Tue - Thu: 12:00 PM - 10:00 PM</code>. Automatically reflected on Contact, Home, and Footer.</small>
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="st_c_email">Careers Email</label>
                <input id="st_c_email" type="email" name="careers_email" value="<?= e($allSettings['careers_email'] ?? 'neezaamee@gmail.com') ?>">
            </div>
        </div>
        <div class="field">
            <label for="st_c_intro">Careers Intro Text</label>
            <textarea id="st_c_intro" name="careers_intro" rows="2"><?= e($allSettings['careers_intro'] ?? '') ?></textarea>
        </div>
        <div class="form-grid">
            <div class="field">
                <label for="st_ig">Instagram URL</label>
                <input id="st_ig" type="url" name="social_instagram" value="<?= e($allSettings['social_instagram'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="st_fb">Facebook URL</label>
                <input id="st_fb" type="url" name="social_facebook" value="<?= e($allSettings['social_facebook'] ?? '') ?>">
            </div>
            <div class="field">
                <label for="st_tt">TikTok URL</label>
                <input id="st_tt" type="url" name="social_tiktok" value="<?= e($allSettings['social_tiktok'] ?? '') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div>
