<div class="card">
    <h2 class="card-title">Add Owner / Manager Account</h2>
    <form method="POST" action="<?= e(url('/admin/admins')) ?>" class="form-inline-grid">
        <?= csrf_field() ?>
        <div class="field">
            <label for="adm_name">Name *</label>
            <input id="adm_name" type="text" name="name" value="<?= e(old('name')) ?>" required>
        </div>
        <div class="field">
            <label for="adm_email">Email *</label>
            <input id="adm_email" type="email" name="email" value="<?= e(old('email')) ?>" required>
        </div>
        <div class="field">
            <label for="adm_role">Role *</label>
            <select id="adm_role" name="role">
                <option value="manager">Manager</option>
                <option value="owner">Owner</option>
            </select>
        </div>
        <div class="form-inline-submit">
            <button type="submit" class="btn btn-primary">Create Admin</button>
        </div>
    </form>
</div>

<div class="card">
    <?php require base_path('app/Views/admin/partials/datatable.php'); ?>
</div>
