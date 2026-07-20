window.CrmTable = {
    create(defaults = { sort_by: 'id', sort_dir: 'desc' }) {
        const state = {
            sort_by: defaults.sort_by || 'id',
            sort_dir: defaults.sort_dir || 'desc',
        };

        const api = {
            state,
            params(extra = {}) {
                return {
                    ...extra,
                    sort_by: state.sort_by,
                    sort_dir: state.sort_dir,
                };
            },
            sr(meta, index) {
                if (!meta) return index + 1;
                return ((meta.current_page - 1) * meta.per_page) + index + 1;
            },
            paint(tableEl) {
                if (!tableEl) return;
                tableEl.querySelectorAll('th[data-sort]').forEach((th) => {
                    th.classList.add('crm-sortable');
                    th.classList.remove('is-sorted-asc', 'is-sorted-desc');
                    if (th.dataset.sort === state.sort_by) {
                        th.classList.add(state.sort_dir === 'asc' ? 'is-sorted-asc' : 'is-sorted-desc');
                    }
                });
            },
            bind(tableEl, onChange) {
                if (!tableEl) return;
                tableEl.querySelectorAll('th[data-sort]').forEach((th) => {
                    th.classList.add('crm-sortable');
                    th.addEventListener('click', () => {
                        const col = th.dataset.sort;
                        if (!col) return;
                        if (state.sort_by === col) {
                            state.sort_dir = state.sort_dir === 'asc' ? 'desc' : 'asc';
                        } else {
                            state.sort_by = col;
                            state.sort_dir = th.dataset.dir || 'asc';
                        }
                        api.paint(tableEl);
                        onChange?.(1);
                    });
                });
                api.paint(tableEl);
            },
        };

        return api;
    },
};
