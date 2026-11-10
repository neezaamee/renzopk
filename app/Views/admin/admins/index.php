<div class="card" style="max-width:740px;">
    <h2 style="margin-top:0;font-size:1.1rem;">Add Owner / Manager Account</h2>
    <form method="POST" action="<?= e(url('/admin/admins')) ?>" class="form-grid" style="align-items:end;">
        <?= csrf_field() ?>
        <div class="field" style="margin-bottom:0;">
            <label for="adm_name">Name *</label>
            <input id="adm_name" type="text" name="name" value="<?= e(old('name')) ?>" required>
        </div>
        <div class="field" style="margin-bottom:0;">
            <label for="adm_email">Email *</label>
            <input id="adm_email" type="email" name="email" value="<?= e(old('email')) ?>" required>
        </div>
        <div class="field" style="margin-bottom:0;">
            <label for="adm_role">Role *</label>
            <select id="adm_role" name="role">
                <option value="manager">Manager</option>
                <option value="owner">Owner</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Create Admin</button>
        </div>
    </form>
</div>

<div class="card" style="max-width:740px;">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Last Login</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($admins as $adm): ?>
                <tr>
                    <td><?= e($adm['name']) ?></td>
                    <td><?= e($adm['email']) ?></td>
                    <td><span class="pill"><?= e(ucfirst((string) $adm['role'])) ?></span></td>
                    <td><span class="pill"><?= !empty($adm['is_active']) ? 'Active' : 'Disabled' ?></span></td>
                    <td><?= e($adm['last_login_at'] ?? 'Never') ?></td>
                    <td>
                        <form method="POST" action="<?= e(url('/admin/admins/' . $adm['id'] . '/toggle')) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm">
                                <?= !empty($adm['is_active']) ? 'Disable' : 'Enable' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

