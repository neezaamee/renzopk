<?php
/** @var array $tableConfig */
$pagination = $tableConfig['pagination'] ?? [];
$columns = $tableConfig['columns'] ?? [];
$rows = $tableConfig['rows'] ?? [];
$filters = $tableConfig['filters'] ?? [];
$endpoint = (string) ($tableConfig['endpoint'] ?? current_path());
$filterParam = (string) ($tableConfig['filter_param'] ?? '');
$filterValue = (string) ($tableConfig['filter_value'] ?? '');

$page = (int) ($pagination['page'] ?? 1);
$perPage = (int) ($pagination['per_page'] ?? 10);
$total = (int) ($pagination['total'] ?? count($rows));
$filtered = (int) ($pagination['filtered'] ?? count($rows));
$totalPages = (int) ($pagination['total_pages'] ?? 1);
$from = (int) ($pagination['from'] ?? 0);
$to = (int) ($pagination['to'] ?? 0);
$sort = (string) ($pagination['sort'] ?? '');
$dir = (string) ($pagination['dir'] ?? 'asc');
$search = (string) ($pagination['search'] ?? '');
$pages = $pagination['pages'] ?? [1];

$buildPageUrl = static function (int $targetPage) use ($endpoint, $perPage, $search, $sort, $dir, $filterParam, $filterValue): string {
    $params = [
        'page' => $targetPage > 1 ? $targetPage : null,
        'per_page' => $perPage !== 10 ? $perPage : null,
        'q' => $search !== '' ? $search : null,
        'sort' => $sort !== '' ? $sort : null,
        'dir' => $sort !== '' ? $dir : null,
    ];
    if ($filterParam !== '' && $filterValue !== '') {
        $params[$filterParam] = $filterValue;
    }

    $clean = array_filter($params, static fn (mixed $v): bool => $v !== null && $v !== '');
    return $clean === [] ? $endpoint : $endpoint . '?' . http_build_query($clean);
};
?>
<div
    class="dt-wrapper"
    data-datatable
    id="<?= e((string) ($tableConfig['id'] ?? 'admin-datatable')) ?>"
    data-dt-endpoint="<?= e($endpoint) ?>"
    data-dt-filter-param="<?= e($filterParam) ?>"
    data-dt-filter-value="<?= e($filterValue) ?>"
    data-dt-sort="<?= e($sort) ?>"
    data-dt-dir="<?= e($dir) ?>"
    data-dt-page="<?= e((string) $page) ?>"
    data-dt-per-page="<?= e((string) $perPage) ?>"
    data-dt-empty="<?= e((string) ($tableConfig['empty_message'] ?? 'No records found.')) ?>"
>
    <?php if (!empty($filters)): ?>
        <div class="dt-filters" role="tablist" aria-label="Filter records">
            <?php foreach ($filters as $flt): ?>
                <a
                    href="<?= e($flt['url']) ?>"
                    class="dt-filter-btn<?= !empty($flt['active']) ? ' is-active' : '' ?>"
                    data-dt-filter="<?= e($flt['value']) ?>"
                >
                    <?= e($flt['label']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="<?= e($endpoint) ?>" class="dt-toolbar" data-dt-form>
        <?php if ($filterParam !== '' && $filterValue !== ''): ?>
            <input type="hidden" name="<?= e($filterParam) ?>" value="<?= e($filterValue) ?>" data-dt-hidden-filter>
        <?php endif; ?>

        <div class="dt-toolbar-left">
            <label class="dt-length">
                <span>Show</span>
                <select name="per_page" data-dt-per-page aria-label="Rows per page">
                    <?php foreach (\App\Services\AdminTableService::ALLOWED_PER_PAGE as $sizeOpt): ?>
                        <option value="<?= e((string) $sizeOpt) ?>" <?= $perPage === $sizeOpt ? 'selected' : '' ?>>
                            <?= e((string) $sizeOpt) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span>entries</span>
            </label>
        </div>

        <div class="dt-toolbar-right">
            <div class="dt-search">
                <svg class="dt-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input
                    type="search"
                    name="q"
                    value="<?= e($search) ?>"
                    placeholder="<?= e((string) ($tableConfig['search_placeholder'] ?? 'Search records...')) ?>"
                    data-dt-search
                    aria-label="Search table"
                    autocomplete="off"
                >
                <button
                    type="button"
                    class="dt-search-clear"
                    data-dt-clear
                    aria-label="Clear search"
                    <?= $search === '' ? 'hidden' : '' ?>
                >&times;</button>
            </div>
            <noscript><button type="submit" class="btn btn-secondary btn-sm">Filter</button></noscript>
        </div>
    </form>

    <div class="dt-status-banner alert alert-error" data-dt-error hidden role="alert"></div>

    <div class="table-responsive dt-table-container">
        <div class="dt-loading-overlay" data-dt-loading hidden aria-hidden="true">
            <span class="dt-spinner"></span>
            <span>Loading...</span>
        </div>

        <table class="dt-table">
            <thead>
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <?php $sortKey = $col['sort'] ?? null; ?>
                        <th>
                            <?php if ($sortKey !== null): ?>
                                <?php
                                $isCurrent = $sort === $sortKey;
                                $nextDir = ($isCurrent && $dir === 'asc') ? 'desc' : 'asc';
                                $arrow = $isCurrent ? ($dir === 'asc' ? '▲' : '▼') : '⇅';
                                ?>
                                <button
                                    type="button"
                                    class="dt-sort-btn<?= $isCurrent ? ' is-sorted-' . e($dir) : '' ?>"
                                    data-dt-sort="<?= e($sortKey) ?>"
                                    aria-label="Sort by <?= e($col['label']) ?>"
                                >
                                    <span><?= e($col['label']) ?></span>
                                    <span class="dt-sort-icon" aria-hidden="true"><?= $arrow ?></span>
                                </button>
                            <?php else: ?>
                                <?= e($col['label']) ?>
                            <?php endif; ?>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody data-dt-body>
                <?php if ($rows === []): ?>
                    <tr class="dt-empty-row">
                        <td colspan="<?= e((string) count($columns)) ?>" class="dt-empty-cell">
                            <?= e((string) ($tableConfig['empty_message'] ?? 'No records found.')) ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach (($row['cells'] ?? []) as $colIdx => $cell): ?>
                                <?php
                                $label = (string) ($columns[$colIdx]['label'] ?? '');
                                $cellType = (string) ($cell['type'] ?? 'text');
                                ?>
                                <td data-label="<?= e($label) ?>" class="<?= $cellType === 'actions' ? 'dt-actions-cell' : '' ?>">
                                    <?php if ($cellType === 'thumb'): ?>
                                        <?php if (!empty($cell['src'])): ?>
                                            <img src="<?= e($cell['src']) ?>" alt="<?= e($cell['alt'] ?? '') ?>" class="thumb" loading="lazy">
                                        <?php elseif (!empty($cell['fallback_pill'])): ?>
                                            <span class="pill pill-<?= e($cell['fallback_pill']['tone'] ?? 'neutral') ?>"><?= e($cell['fallback_pill']['text']) ?></span>
                                        <?php else: ?>
                                            <span class="dt-muted"><?= e($cell['empty_text'] ?? '—') ?></span>
                                        <?php endif; ?>
                                    <?php elseif ($cellType === 'pills'): ?>
                                        <div class="dt-pill-group">
                                            <?php foreach (($cell['items'] ?? []) as $pill): ?>
                                                <span class="pill pill-<?= e($pill['tone'] ?? 'neutral') ?>"><?= e($pill['text']) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php elseif ($cellType === 'code'): ?>
                                        <?php if (!empty($cell['prefix'])): ?>
                                            <span><?= e($cell['prefix']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($cell['text'])): ?>
                                            <?php if (!empty($cell['prefix'])): ?><br><?php endif; ?>
                                            <code class="dt-code"><?= e($cell['text']) ?></code>
                                        <?php elseif (empty($cell['prefix'])): ?>
                                            <span class="dt-muted">—</span>
                                        <?php endif; ?>
                                    <?php elseif ($cellType === 'color'): ?>
                                        <span class="dt-color-cell">
                                            <?php if (!empty($cell['hex'])): ?>
                                                <span class="dt-color-swatch" style="background:<?= e($cell['hex']) ?>;"></span>
                                            <?php endif; ?>
                                            <span><?= e($cell['text'] ?? '—') ?></span>
                                        </span>
                                    <?php elseif ($cellType === 'actions'): ?>
                                        <div class="actions">
                                            <?php foreach (($cell['items'] ?? []) as $action): ?>
                                                <?php if (($action['kind'] ?? 'link') === 'post'): ?>
                                                    <form method="POST" action="<?= e($action['url']) ?>" class="dt-inline-form"<?= !empty($action['confirm']) ? ' onsubmit="return confirm(\'' . e($action['confirm']) . '\');"' : '' ?>>
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-<?= e($action['style'] ?? 'secondary') ?> btn-sm"><?= e($action['label']) ?></button>
                                                    </form>
                                                <?php else: ?>
                                                    <a href="<?= e($action['url']) ?>" class="btn btn-<?= e($action['style'] ?? 'secondary') ?> btn-sm"><?= e($action['label']) ?></a>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <?php if (!empty($cell['strong'])): ?>
                                            <strong><?= e($cell['text'] ?? '') ?></strong>
                                        <?php else: ?>
                                            <span><?= e($cell['text'] ?? '') ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($cell['subtext'])): ?>
                                            <div class="dt-subtext"><?= e($cell['subtext']) ?></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="dt-footer">
        <div class="dt-info" data-dt-info aria-live="polite">
            <?php if ($filtered === 0): ?>
                Showing 0 entries<?= $total > 0 ? ' (filtered from ' . e((string) $total) . ' total)' : '' ?>
            <?php else: ?>
                Showing <?= e((string) $from) ?> to <?= e((string) $to) ?> of <?= e((string) $filtered) ?> entries<?= $filtered < $total ? ' (filtered from ' . e((string) $total) . ' total)' : '' ?>
            <?php endif; ?>
        </div>

        <nav class="dt-pagination" data-dt-pagination aria-label="Table Pagination">
            <?php if ($page > 1): ?>
                <a href="<?= e($buildPageUrl($page - 1)) ?>" class="dt-page-btn" data-dt-page="<?= e((string) ($page - 1)) ?>" aria-label="Previous page">&lsaquo; Prev</a>
            <?php else: ?>
                <span class="dt-page-btn is-disabled" aria-disabled="true">&lsaquo; Prev</span>
            <?php endif; ?>

            <?php foreach ($pages as $pg): ?>
                <?php if ($pg === '...'): ?>
                    <span class="dt-ellipsis" aria-hidden="true">&hellip;</span>
                <?php elseif ((int) $pg === $page): ?>
                    <span class="dt-page-btn is-active" aria-current="page"><?= e((string) $pg) ?></span>
                <?php else: ?>
                    <a href="<?= e($buildPageUrl((int) $pg)) ?>" class="dt-page-btn" data-dt-page="<?= e((string) $pg) ?>"><?= e((string) $pg) ?></a>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?= e($buildPageUrl($page + 1)) ?>" class="dt-page-btn" data-dt-page="<?= e((string) ($page + 1)) ?>" aria-label="Next page">Next &rsaquo;</a>
            <?php else: ?>
                <span class="dt-page-btn is-disabled" aria-disabled="true">Next &rsaquo;</span>
            <?php endif; ?>
        </nav>
    </div>
</div>

