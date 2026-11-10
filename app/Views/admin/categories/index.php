<div class="card">
    <div class="actions" style="margin-bottom:14px;">
        <a href="<?= e(url('/admin/menu/categories/create')) ?>" class="btn btn-primary">+ New Category</a>
        <a href="<?= e(url('/admin/menu')) ?>" class="btn btn-secondary">&larr; Back to Menu Items</a>
    </div>

    <?php if (empty($categories)): ?>
        <p>No categories created yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= e((string) $cat['sort_order']) ?></td>
                        <td><strong><?= e($cat['name']) ?></strong></td>
                        <td><?= e((string) $cat['items_count']) ?></td>
                        <td><span class="pill"><?= !empty($cat['is_active']) ? 'Active' : 'Hidden' ?></span></td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/menu/categories/' . $cat['id'] . '/edit')) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="<?= e(url('/admin/menu/categories/' . $cat['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this category?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

