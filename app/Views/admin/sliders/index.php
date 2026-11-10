<div class="card">
    <div class="actions" style="margin-bottom:14px;">
        <a href="<?= e(url('/admin/sliders/create')) ?>" class="btn btn-primary">+ Add Home Slide</a>
    </div>

    <?php if (empty($slides)): ?>
        <p>No slider banners configured yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Banner</th>
                    <th>Title</th>
                    <th>CTA</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($slides as $slide): ?>
                    <tr>
                        <td>
                            <?php if (!empty($slide['image_path'])): ?>
                                <img src="<?= e(media_url($slide['image_path'])) ?>" alt="" class="thumb">
                            <?php else: ?>
                                <span class="pill">Gradient Placeholder</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= e($slide['title'] ?: 'Untitled Slide') ?></strong></td>
                        <td><?= e($slide['cta_text'] ?? '—') ?></td>
                        <td><?= e((string) $slide['sort_order']) ?></td>
                        <td><span class="pill"><?= !empty($slide['is_active']) ? 'Active' : 'Hidden' ?></span></td>
                        <td class="actions">
                            <a href="<?= e(url('/admin/sliders/' . $slide['id'] . '/edit')) ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <form method="POST" action="<?= e(url('/admin/sliders/' . $slide['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this slide?');">
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

