<div class="card">
    <div class="card-toolbar">
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

    <?php require base_path('app/Views/admin/partials/datatable.php'); ?>
</div>
