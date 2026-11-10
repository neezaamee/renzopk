<div class="card" style="max-width:680px;">
    <h2 style="margin-top:0;font-size:1.1rem;">Add Dietary / Menu Tag</h2>
    <form method="POST" action="<?= e(url('/admin/menu/tags')) ?>" class="form-grid" style="align-items:end;">
        <?= csrf_field() ?>
        <div class="field" style="margin-bottom:0;">
            <label for="tag_name">Tag Name *</label>
            <input id="tag_name" type="text" name="name" value="<?= e(old('name')) ?>" placeholder="e.g. Vegan" required>
        </div>
        <div class="field" style="margin-bottom:0;">
            <label for="tag_color">Hex Color (optional)</label>
            <input id="tag_color" type="text" name="color" value="<?= e(old('color', '#b45309')) ?>" placeholder="#b45309">
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Add Tag</button>
        </div>
    </form>
</div>

<div class="card" style="max-width:680px;">
    <?php if (empty($tags)): ?>
        <p>No tags defined.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Color</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tags as $tag): ?>
                    <tr>
                        <td><strong><?= e($tag['name']) ?></strong></td>
                        <td><?= e($tag['slug']) ?></td>
                        <td><?= e($tag['color'] ?? '—') ?></td>
                        <td>
                            <form method="POST" action="<?= e(url('/admin/menu/tags/' . $tag['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this tag?');">
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

