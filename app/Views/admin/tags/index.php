<div class="card">
    <h2 class="card-title">Add Dietary / Menu Tag</h2>
    <form method="POST" action="<?= e(url('/admin/menu/tags')) ?>" class="form-inline-grid">
        <?= csrf_field() ?>
        <div class="field">
            <label for="tag_name">Tag Name *</label>
            <input id="tag_name" type="text" name="name" value="<?= e(old('name')) ?>" placeholder="e.g. Vegan" required>
        </div>
        <div class="field">
            <label for="tag_color">Hex Color (optional)</label>
            <input id="tag_color" type="text" name="color" value="<?= e(old('color', '#b45309')) ?>" placeholder="#b45309">
        </div>
        <div class="form-inline-submit">
            <button type="submit" class="btn btn-primary">Add Tag</button>
        </div>
    </form>
</div>

<div class="card">
    <?php require base_path('app/Views/admin/partials/datatable.php'); ?>
</div>
