<div class="card">
    <div class="actions" style="justify-content:space-between;margin-bottom:14px;">
        <div class="actions">
            <a href="<?= e(url('/admin/menu/create')) ?>" class="btn btn-primary">+ Add Menu Item</a>
            <a href="<?= e(url('/admin/menu/categories')) ?>" class="btn btn-secondary">Categories</a>
            <a href="<?= e(url('/admin/menu/tags')) ?>" class="btn btn-secondary">Tags</a>
        </div>
        <div class="actions">
            <form method="POST" action="<?= e(url('/admin/menu/regenerate-pdf')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary">Regenerate PDF</button>
            </form>
            <a href="<?= e(url('/menu/pdf')) ?>" class="btn btn-secondary" target="_blank" rel="noopener">Preview Menu PDF</a>
        </div>
    </div>

    <?php if (empty($items)): ?>
        <p>No menu items yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Dish</th>
                    <th>Category</th>
                    <th>Base Price</th>
                    <th>Flags</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['image_path'])): ?>
                                <img src="<?= e(media_url($item['image_path'])) ?>" alt="" class="thumb">
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($item['name']) ?></strong></td>
                        <td><?= e($item['category_name']) ?></td>
                        <td><?= $item['price'] !== null ? e(format_price($item['price'], (string) ($settings['currency_symbol'] ?? '$'))) : 'Variants' ?></td>
                        <td>
                            <span class="pill"><?= !empty($item['is_active']) ? 'Active' : 'Hidden' ?></span>
                            <?php if (empty($item['is_available'])): ?>
                                <span class="pill">Out of Stock</span>
                            <?php endif; ?>
                            <?php if (!empty($item['is_featured'])): ?>
                                <span class="pill">Featured</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/menu/' . $item['id'] . '/edit')) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="<?= e(url('/admin/menu/' . $item['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this menu item?');">
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

