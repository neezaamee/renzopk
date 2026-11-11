<div class="card">
    <div class="card-toolbar">
        <div class="actions">
            <a href="<?= e(url('/admin/deals/create')) ?>" class="btn btn-primary">+ Create Deal</a>
        </div>
    </div>

    <?php require base_path('app/Views/admin/partials/datatable.php'); ?>
</div>
