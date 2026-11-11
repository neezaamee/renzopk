<div class="card">
    <div class="card-toolbar">
        <div class="actions">
            <a href="<?= e(url('/admin/menu/categories/create')) ?>" class="btn btn-primary">+ New Category</a>
            <a href="<?= e(url('/admin/menu')) ?>" class="btn btn-secondary">&larr; Back to Menu Items</a>
        </div>
    </div>

    <?php require base_path('app/Views/admin/partials/datatable.php'); ?>
</div>
