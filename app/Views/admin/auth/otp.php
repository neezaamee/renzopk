<p style="color:#57534e;margin-top:0;">Enter the 6-digit code sent to <strong><?= e($pendingEmail) ?></strong>. Codes expire in 10 minutes (max 5 attempts).</p>
<form method="POST" action="<?= e(url('/admin/verify')) ?>">
    <?= csrf_field() ?>
    <input type="hidden" name="email" value="<?= e($pendingEmail) ?>">
    <div class="field">
        <label for="otp_code">6-Digit Code</label>
        <input id="otp_code" type="text" name="otp_code" inputmode="numeric" pattern="\d{6}" maxlength="6" placeholder="000000" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;">Verify &amp; Sign In</button>
</form>
<p style="margin-top:16px;margin-bottom:0;font-size:0.85rem;text-align:center;">
    <a href="<?= e(url('/admin/login')) ?>">&larr; Use a different email / Request new code</a>
</p>

