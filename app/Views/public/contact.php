<section class="section">
    <div class="container">
        <div class="section-header">
            <h1>Contact <?= e($settings['restaurant_name'] ?? 'Renzo') ?></h1>
        </div>

        <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr));align-items:start;">
            <div class="form-panel" id="contact-form">
                <h2 style="margin-top:0;color:var(--gold);">Send Us a Message</h2>
                <?php $flashPrefix = 'contact_'; require base_path('app/Views/partials/flash.php'); ?>
                <form method="POST" action="<?= e(url('/contact')) ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_redirect" value="/contact#contact-form">
                    <div class="hp-field" aria-hidden="true">
                        <label for="c_hp_website">Website</label>
                        <input id="c_hp_website" type="text" name="_hp_website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="c_name">Name *</label>
                            <input id="c_name" type="text" name="name" value="<?= e(old('name')) ?>" required>
                        </div>
                        <div class="field">
                            <label for="c_email">Email *</label>
                            <input id="c_email" type="email" name="email" value="<?= e(old('email')) ?>" required>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="field">
                            <label for="c_phone">Phone</label>
                            <input id="c_phone" type="text" name="phone" value="<?= e(old('phone')) ?>">
                        </div>
                        <div class="field">
                            <label for="c_type">Inquiry Type</label>
                            <select id="c_type" name="inquiry_type">
                                <option value="general" <?= old('inquiry_type') === 'general' ? 'selected' : '' ?>>General Inquiry</option>
                                <option value="catering" <?= old('inquiry_type') === 'catering' ? 'selected' : '' ?>>Private Events &amp; Catering</option>
                                <option value="feedback" <?= old('inquiry_type') === 'feedback' ? 'selected' : '' ?>>Dining Feedback</option>
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label for="c_subject">Subject</label>
                        <input id="c_subject" type="text" name="subject" value="<?= e(old('subject')) ?>">
                    </div>
                    <div class="field">
                        <label for="c_msg">Message *</label>
                        <textarea id="c_msg" name="message" rows="5" required><?= e(old('message')) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-gold">Send Message</button>
                </form>
            </div>

            <div>
                <div class="form-panel" style="margin-bottom:22px;">
                    <h2 style="margin-top:0;color:var(--gold);">Opening Hours</h2>
                    <?php
                    $openingRows = $hoursRows ?? \App\Services\SettingsService::parseOpeningHours((string) ($settings['opening_hours'] ?? ''));
                    $hasPairColumns = false;
                    foreach ($openingRows as $hr) {
                        if (($hr['hours'] ?? '') !== '') {
                            $hasPairColumns = true;
                            break;
                        }
                    }
                    ?>
                    <?php if (empty($openingRows)): ?>
                        <p class="muted" style="margin:0;">Opening hours will be announced shortly.</p>
                    <?php else: ?>
                        <table class="hours-table">
                            <?php if ($hasPairColumns): ?>
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Hours</th>
                                    </tr>
                                </thead>
                            <?php endif; ?>
                            <tbody>
                                <?php foreach ($openingRows as $row): ?>
                                    <tr>
                                        <?php if (($row['hours'] ?? '') !== ''): ?>
                                            <td><?= e($row['day']) ?></td>
                                            <td><?= e($row['hours']) ?></td>
                                        <?php else: ?>
                                            <td colspan="2"><?= e($row['day']) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    <p class="muted" style="margin-bottom:0;margin-top:14px;">
                        <strong>Address:</strong> <?= e($settings['contact_address'] ?? '124 Harbor Lane, Culinary District') ?><br>
                        <strong>Phone:</strong> <?= e($settings['contact_phone'] ?? '+1 (555) 010-2040') ?><br>
                        <strong>Email:</strong> <?= e($settings['contact_email'] ?? 'hello@renzo.example') ?>
                    </p>
                </div>

                <?php require base_path('app/Views/partials/map_embed.php'); ?>
            </div>
        </div>
    </div>
</section>
