<p style="color:#57534e;margin-top:0;">Enter your registered Owner or Manager email to receive a one-time 6-digit login code.</p>
<form method="POST" action="<?= e(url('/admin/login')) ?>">
    <?= csrf_field() ?>
    <div class="field">
        <label for="email">Admin Email</label>
        <input id="email" type="email" name="email" value="<?= e(old('email')) ?>" required autofocus>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;">Send Verification Code</button>
</form>
<p style="margin-top:16px;margin-bottom:0;font-size:0.85rem;text-align:center;">
    <a href="<?= e(url('/')) ?>">&larr; Back to Public Website</a>
</p>

