document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile & Tablet Off-Canvas Sidebar Drawer
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.querySelector('[data-admin-backdrop]');
    const openBtn = document.querySelector('[data-admin-nav-open]');
    const closeBtn = document.querySelector('[data-admin-nav-close]');

    const setDrawerOpen = (open) => {
        if (!sidebar || !backdrop) {
            return;
        }
        sidebar.classList.toggle('is-open', open);
        backdrop.hidden = !open;
        document.body.classList.toggle('nav-drawer-open', open);
        if (openBtn) {
            openBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
    };

    if (openBtn) {
        openBtn.addEventListener('click', () => setDrawerOpen(true));
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', () => setDrawerOpen(false));
    }
    if (backdrop) {
        backdrop.addEventListener('click', () => setDrawerOpen(false));
    }
    if (sidebar) {
        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 960) {
                    setDrawerOpen(false);
                }
            });
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && sidebar && sidebar.classList.contains('is-open')) {
            setDrawerOpen(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 960 && sidebar && sidebar.classList.contains('is-open')) {
            setDrawerOpen(false);
        }
    });

    // 2. Menu Item Dynamic Variant Row Adder & Remover
    const addVariantBtn = document.querySelector('[data-add-variant]');
    const variantsBox = document.querySelector('[data-variants-container]');
    if (addVariantBtn && variantsBox) {
        let nextVariantIndex = variantsBox.querySelectorAll('[data-variant-row]').length;

        variantsBox.addEventListener('click', (event) => {
            const removeBtn = event.target.closest('[data-remove-variant]');
            if (removeBtn) {
                const row = removeBtn.closest('[data-variant-row]');
                if (row) {
                    row.remove();
                }
            }
        });

        addVariantBtn.addEventListener('click', () => {
            const index = nextVariantIndex++;
            const row = document.createElement('div');
            row.className = 'form-grid';
            row.setAttribute('data-variant-row', '1');
            row.innerHTML = `
                <div class="field">
                    <label>Variant Name</label>
                    <input type="text" name="variant_name[${index}]" placeholder="e.g. Large">
                </div>
                <div class="field">
                    <label>Price</label>
                    <input type="number" step="0.01" min="0" name="variant_price[${index}]" placeholder="0.00">
                </div>
                <div class="field" style="display:flex;align-items:center;gap:10px;">
                    <label style="margin:0;"><input type="checkbox" name="variant_available[${index}]" value="1" checked> Available</label>
                    <button type="button" class="btn btn-danger btn-sm" data-remove-variant>Remove</button>
                </div>
            `;
            variantsBox.appendChild(row);
        });
    }

    // 3. Server-Side AJAX DataTables
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') || '' : '';

    const el = (tag, className, text) => {
        const node = document.createElement(tag);
        if (className) {
            node.className = className;
        }
        if (text !== undefined && text !== null) {
            node.textContent = String(text);
        }
        return node;
    };

    const renderCellContent = (td, cell) => {
        const type = cell.type || 'text';

        if (type === 'thumb') {
            if (cell.src) {
                const img = document.createElement('img');
                img.src = cell.src;
                img.alt = cell.alt || '';
                img.className = 'thumb';
                img.loading = 'lazy';
                td.appendChild(img);
            } else if (cell.fallback_pill) {
                const tone = cell.fallback_pill.tone || 'neutral';
                td.appendChild(el('span', `pill pill-${tone}`, cell.fallback_pill.text || ''));
            } else {
                td.appendChild(el('span', 'dt-muted', cell.empty_text || '—'));
            }
            return;
        }

        if (type === 'pills') {
            const group = el('div', 'dt-pill-group');
            (cell.items || []).forEach((pill) => {
                group.appendChild(el('span', `pill pill-${pill.tone || 'neutral'}`, pill.text || ''));
            });
            td.appendChild(group);
            return;
        }

        if (type === 'code') {
            if (cell.prefix) {
                td.appendChild(el('span', '', cell.prefix));
            }
            if (cell.text) {
                if (cell.prefix) {
                    td.appendChild(document.createElement('br'));
                }
                td.appendChild(el('code', 'dt-code', cell.text));
            } else if (!cell.prefix) {
                td.appendChild(el('span', 'dt-muted', '—'));
            }
            return;
        }

        if (type === 'color') {
            const wrap = el('span', 'dt-color-cell');
            if (cell.hex) {
                const swatch = el('span', 'dt-color-swatch');
                swatch.style.background = cell.hex;
                wrap.appendChild(swatch);
            }
            wrap.appendChild(el('span', '', cell.text || '—'));
            td.appendChild(wrap);
            return;
        }

        if (type === 'actions') {
            td.classList.add('dt-actions-cell');
            const bar = el('div', 'actions');
            (cell.items || []).forEach((action) => {
                const btnStyle = action.style || 'secondary';
                if (action.kind === 'post') {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = action.url || '';
                    form.className = 'dt-inline-form';
                    if (action.confirm) {
                        form.addEventListener('submit', (e) => {
                            if (!window.confirm(action.confirm)) {
                                e.preventDefault();
                            }
                        });
                    }
                    const tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = '_token';
                    tokenInput.value = csrfToken;
                    form.appendChild(tokenInput);

                    const btn = el('button', `btn btn-${btnStyle} btn-sm`, action.label || 'Submit');
                    btn.type = 'submit';
                    form.appendChild(btn);
                    bar.appendChild(form);
                } else {
                    const link = el('a', `btn btn-${btnStyle} btn-sm`, action.label || 'Open');
                    link.href = action.url || '#';
                    bar.appendChild(link);
                }
            });
            td.appendChild(bar);
            return;
        }

        if (cell.strong) {
            td.appendChild(el('strong', '', cell.text || ''));
        } else {
            td.appendChild(el('span', '', cell.text || ''));
        }
        if (cell.subtext) {
            td.appendChild(el('div', 'dt-subtext', cell.subtext));
        }
    };

    const initDataTable = (root) => {
        const endpoint = root.dataset.dtEndpoint || window.location.pathname;
        const tbody = root.querySelector('[data-dt-body]');
        const infoEl = root.querySelector('[data-dt-info]');
        const paginationEl = root.querySelector('[data-dt-pagination]');
        const loadingEl = root.querySelector('[data-dt-loading]');
        const errorEl = root.querySelector('[data-dt-error]');
        const searchInput = root.querySelector('[data-dt-search]');
        const clearBtn = root.querySelector('[data-dt-clear]');
        const perPageSelect = root.querySelector('[data-dt-per-page]');
        const filterLinks = Array.from(root.querySelectorAll('[data-dt-filter]'));
        const sortButtons = Array.from(root.querySelectorAll('[data-dt-sort]'));
        const filterForm = root.querySelector('[data-dt-form]');
        const hiddenFilter = root.querySelector('[data-dt-hidden-filter]');

        if (!tbody || !infoEl || !paginationEl) {
            return;
        }

        const state = {
            page: parseInt(root.dataset.dtPage || '1', 10) || 1,
            perPage: parseInt(root.dataset.dtPerPage || '10', 10) || 10,
            q: searchInput ? searchInput.value.trim() : '',
            sort: root.dataset.dtSort || '',
            dir: root.dataset.dtDir || 'asc',
            filterParam: root.dataset.dtFilterParam || '',
            filterValue: root.dataset.dtFilterValue || '',
        };

        let activeController = null;
        let searchTimer = null;

        const buildParams = (includeFormatJson) => {
            const params = new URLSearchParams();
            if (includeFormatJson) {
                params.set('format', 'json');
            }
            if (state.page > 1) {
                params.set('page', String(state.page));
            }
            if (state.perPage !== 10 || includeFormatJson) {
                params.set('per_page', String(state.perPage));
            }
            if (state.q !== '') {
                params.set('q', state.q);
            }
            if (state.sort !== '') {
                params.set('sort', state.sort);
                params.set('dir', state.dir);
            }
            if (state.filterParam !== '' && state.filterValue !== '') {
                params.set(state.filterParam, state.filterValue);
            }
            return params;
        };

        const buildBrowserUrl = (pageOverride) => {
            const prevPage = state.page;
            if (typeof pageOverride === 'number') {
                state.page = pageOverride;
            }
            const params = buildParams(false);
            if (!params.has('per_page') && state.perPage !== 10) {
                params.set('per_page', String(state.perPage));
            }
            state.page = prevPage;
            const qs = params.toString();
            return qs ? `${endpoint}?${qs}` : endpoint;
        };

        const syncSortHeaders = () => {
            sortButtons.forEach((btn) => {
                const key = btn.dataset.dtSort || '';
                const icon = btn.querySelector('.dt-sort-icon');
                btn.classList.remove('is-sorted-asc', 'is-sorted-desc');
                if (state.sort === key) {
                    btn.classList.add(state.dir === 'desc' ? 'is-sorted-desc' : 'is-sorted-asc');
                    if (icon) {
                        icon.textContent = state.dir === 'desc' ? '▼' : '▲';
                    }
                } else if (icon) {
                    icon.textContent = '⇅';
                }
            });
        };

        const syncFilterLinks = () => {
            filterLinks.forEach((link) => {
                const val = link.dataset.dtFilter || '';
                link.classList.toggle('is-active', val === state.filterValue);
            });
            if (hiddenFilter) {
                hiddenFilter.value = state.filterValue;
            }
        };

        const renderTable = (table) => {
            const p = table.pagination || {};
            const columns = table.columns || [];
            const rows = table.rows || [];
            const colLabels = columns.map((c) => c.label || '');

            state.page = p.page || 1;
            state.perPage = p.per_page || state.perPage;
            state.sort = p.sort || '';
            state.dir = p.dir || 'asc';

            const fragment = document.createDocumentFragment();
            if (rows.length === 0) {
                const emptyTr = el('tr', 'dt-empty-row');
                const emptyTd = el('td', 'dt-empty-cell', table.empty_message || root.dataset.dtEmpty || 'No records found.');
                emptyTd.colSpan = Math.max(1, columns.length);
                emptyTr.appendChild(emptyTd);
                fragment.appendChild(emptyTr);
            } else {
                rows.forEach((row) => {
                    const tr = document.createElement('tr');
                    (row.cells || []).forEach((cell, idx) => {
                        const td = document.createElement('td');
                        td.setAttribute('data-label', colLabels[idx] || '');
                        renderCellContent(td, cell);
                        tr.appendChild(td);
                    });
                    fragment.appendChild(tr);
                });
            }
            tbody.replaceChildren(fragment);

            const total = p.total || 0;
            const filtered = p.filtered || 0;
            if (filtered === 0) {
                infoEl.textContent = total > 0
                    ? `Showing 0 entries (filtered from ${total} total)`
                    : 'Showing 0 entries';
            } else {
                const baseInfo = `Showing ${p.from || 1} to ${p.to || rows.length} of ${filtered} entries`;
                infoEl.textContent = filtered < total ? `${baseInfo} (filtered from ${total} total)` : baseInfo;
            }

            const pagFrag = document.createDocumentFragment();
            const currentPage = p.page || 1;
            const totalPages = p.total_pages || 1;

            const addNavItem = (label, targetPage, disabled, active) => {
                if (disabled) {
                    const span = el('span', 'dt-page-btn is-disabled', label);
                    span.setAttribute('aria-disabled', 'true');
                    pagFrag.appendChild(span);
                    return;
                }
                if (active) {
                    const span = el('span', 'dt-page-btn is-active', label);
                    span.setAttribute('aria-current', 'page');
                    pagFrag.appendChild(span);
                    return;
                }
                const a = el('a', 'dt-page-btn', label);
                a.href = buildBrowserUrl(targetPage);
                a.dataset.dtPage = String(targetPage);
                pagFrag.appendChild(a);
            };

            addNavItem('‹ Prev', currentPage - 1, currentPage <= 1, false);
            (p.pages || [1]).forEach((pg) => {
                if (pg === '...') {
                    const dots = el('span', 'dt-ellipsis', '…');
                    dots.setAttribute('aria-hidden', 'true');
                    pagFrag.appendChild(dots);
                } else {
                    const num = parseInt(pg, 10);
                    addNavItem(String(num), num, false, num === currentPage);
                }
            });
            addNavItem('Next ›', currentPage + 1, currentPage >= totalPages, false);
            paginationEl.replaceChildren(pagFrag);

            syncSortHeaders();
            syncFilterLinks();

            if (window.history && typeof window.history.replaceState === 'function') {
                window.history.replaceState(null, '', buildBrowserUrl());
            }
        };

        const loadData = async () => {
            if (activeController) {
                activeController.abort();
            }
            activeController = new AbortController();

            if (loadingEl) {
                loadingEl.hidden = false;
            }
            if (errorEl) {
                errorEl.hidden = true;
                errorEl.textContent = '';
            }

            const params = buildParams(true);
            const requestUrl = `${endpoint}?${params.toString()}`;

            try {
                const resp = await fetch(requestUrl, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                    },
                    signal: activeController.signal,
                });

                const payload = await resp.json().catch(() => null);
                if (resp.status === 401 && payload && payload.redirect) {
                    window.location.href = payload.redirect;
                    return;
                }
                if (!resp.ok || !payload || !payload.table) {
                    throw new Error((payload && payload.error) || `Unable to load data (${resp.status}).`);
                }

                renderTable(payload.table);
            } catch (err) {
                if (err && err.name === 'AbortError') {
                    return;
                }
                if (errorEl) {
                    errorEl.textContent = (err && err.message) || 'Failed to load table data. Please retry.';
                    errorEl.hidden = false;
                }
            } finally {
                if (loadingEl) {
                    loadingEl.hidden = true;
                }
            }
        };

        if (filterForm) {
            filterForm.addEventListener('submit', (event) => {
                event.preventDefault();
                clearTimeout(searchTimer);
                state.q = searchInput ? searchInput.value.trim() : '';
                state.page = 1;
                loadData();
            });
        }

        if (perPageSelect) {
            perPageSelect.addEventListener('change', () => {
                state.perPage = parseInt(perPageSelect.value, 10) || 10;
                state.page = 1;
                loadData();
            });
        }

        if (searchInput) {
            const toggleClear = () => {
                if (clearBtn) {
                    clearBtn.hidden = searchInput.value.trim() === '';
                }
            };
            toggleClear();

            searchInput.addEventListener('input', () => {
                toggleClear();
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    const nextQuery = searchInput.value.trim();
                    if (nextQuery !== state.q) {
                        state.q = nextQuery;
                        state.page = 1;
                        loadData();
                    }
                }, 280);
            });
        }

        if (clearBtn && searchInput) {
            clearBtn.addEventListener('click', () => {
                searchInput.value = '';
                clearBtn.hidden = true;
                state.q = '';
                state.page = 1;
                searchInput.focus();
                loadData();
            });
        }

        sortButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.dtSort || '';
                if (!key) {
                    return;
                }
                if (state.sort === key) {
                    state.dir = state.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    state.sort = key;
                    state.dir = 'asc';
                }
                state.page = 1;
                loadData();
            });
        });

        filterLinks.forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();
                state.filterValue = link.dataset.dtFilter || '';
                state.page = 1;
                loadData();
            });
        });

        paginationEl.addEventListener('click', (event) => {
            const link = event.target.closest('a[data-dt-page]');
            if (!link) {
                return;
            }
            event.preventDefault();
            const target = parseInt(link.dataset.dtPage || '1', 10) || 1;
            if (target !== state.page) {
                state.page = target;
                loadData();
            }
        });
    };

    document.querySelectorAll('[data-datatable]').forEach(initDataTable);
});
