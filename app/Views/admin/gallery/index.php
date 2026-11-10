<div class="card">
    <div class="actions" style="margin-bottom:14px;">
        <a href="<?= e(url('/admin/gallery/create')) ?>" class="btn btn-primary">+ Add Gallery Item</a>
    </div>

    <?php if (empty($items)): ?>
        <p>No gallery items yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Type</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <?php $type = (string) ($item['type'] ?? 'photo'); ?>
                    <tr>
                        <td>
                            <?php if ($type === 'youtube' && !empty($item['youtube_id'])): ?>
                                <img src="https://img.youtube.com/vi/<?= e($item['youtube_id']) ?>/hqdefault.jpg" alt="" class="thumb">
                            <?php elseif (!empty($item['image_path'])): ?>
                                <img src="<?= e(media_url($item['image_path'])) ?>" alt="" class="thumb">
                            <?php endif; ?>
                        </td>
                        <td><span class="pill"><?= e(ucfirst($type)) ?></span></td>
                        <td><?= e($item['title'] ?: 'Untitled') ?></td>
                        <td><?= e($item['category'] ?? '—') ?></td>
                        <td><?= e((string) $item['sort_order']) ?></td>
                        <td><span class="pill"><?= !empty($item['is_active']) ? 'Visible' : 'Hidden' ?></span></td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/gallery/' . $item['id'] . '/edit')) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="<?= e(url('/admin/gallery/' . $item['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this gallery item?');">
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
