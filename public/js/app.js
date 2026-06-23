(function () {
    'use strict';

    const csrf = window.PRITECH ? window.PRITECH.csrf : '';

    const jsonHeaders = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf,
    };

    async function api(url, options = {}) {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: { ...jsonHeaders, ...(options.headers || {}) },
            ...options,
        });

        let data = null;
        try { data = await response.json(); } catch (_) {}

        if (!response.ok) {
            const error = new Error(data && data.message ? data.message : 'Request failed');
            error.status = response.status;
            error.data = data;
            throw error;
        }

        return data;
    }

    function debounce(fn, wait) {
        let t;
        return function (...args) {
            clearTimeout(t);
            t = setTimeout(() => fn.apply(this, args), wait);
        };
    }

    function initIssueTags() {
        const root = document.querySelector('[data-issue-tags]');
        if (!root) return;

        const issueId = root.dataset.issueId;
        const list    = root.querySelector('[data-tag-list]');
        const select  = root.querySelector('[data-tag-select]');
        const addBtn  = root.querySelector('[data-tag-add]');
        const feedback = root.querySelector('[data-tag-feedback]');

        function renderChip(tag) {
            const chip = document.createElement('span');
            chip.className = 'tag-chip me-1 mb-1';
            chip.style.background = tag.color ? hexToSoft(tag.color) : '#ecf0f1';
            chip.style.borderColor = tag.color || 'transparent';
            chip.dataset.tagId = tag.id;
            chip.innerHTML = `
                <span class="color-swatch" style="background:${tag.color || '#cccccc'}"></span>
                ${escapeHtml(tag.name)}
                <button type="button" class="btn-close" data-detach aria-label="Remove tag"></button>
            `;
            return chip;
        }

        function rerender(tags) {
            list.innerHTML = '';
            if (!tags.length) {
                list.innerHTML = '<span class="text-muted small">No tags yet.</span>';
                return;
            }
            tags.forEach(t => list.appendChild(renderChip(t)));
        }

        addBtn.addEventListener('click', async () => {
            const tagId = parseInt(select.value, 10);
            if (!tagId) {
                showFeedback(feedback, 'Pick a tag first.', 'warning');
                return;
            }
            try {
                const data = await api(`/issues/${issueId}/tags`, {
                    method: 'POST',
                    body: JSON.stringify({ tag_id: tagId }),
                });
                rerender(data.tags);
                select.value = '';
                showFeedback(feedback, 'Tag attached.', 'success');
            } catch (e) {
                showFeedback(feedback, e.message || 'Could not attach tag.', 'danger');
            }
        });

        list.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-detach]');
            if (!btn) return;
            const chip = btn.closest('.tag-chip');
            const tagId = chip.dataset.tagId;
            try {
                const data = await api(`/issues/${issueId}/tags/${tagId}`, { method: 'DELETE' });
                rerender(data.tags);
                showFeedback(feedback, 'Tag detached.', 'success');
            } catch (e) {
                showFeedback(feedback, 'Could not detach tag.', 'danger');
            }
        });
    }

    function initIssueAssignees() {
        const root = document.querySelector('[data-issue-assignees]');
        if (!root) return;

        const issueId  = root.dataset.issueId;
        const list     = root.querySelector('[data-assignee-list]');
        const select   = root.querySelector('[data-assignee-select]');
        const addBtn   = root.querySelector('[data-assignee-add]');
        const feedback = root.querySelector('[data-assignee-feedback]');

        function renderRow(u) {
            const row = document.createElement('span');
            row.className = 'tag-chip me-1 mb-1';
            row.dataset.userId = u.id;
            row.innerHTML = `
                <i class="bi bi-person-circle"></i>
                ${escapeHtml(u.name)}
                <button type="button" class="btn-close" data-detach aria-label="Remove"></button>
            `;
            return row;
        }

        function rerender(users) {
            list.innerHTML = '';
            if (!users.length) {
                list.innerHTML = '<span class="text-muted small">Nobody assigned yet.</span>';
                return;
            }
            users.forEach(u => list.appendChild(renderRow(u)));
        }

        addBtn.addEventListener('click', async () => {
            const userId = parseInt(select.value, 10);
            if (!userId) {
                showFeedback(feedback, 'Pick a member first.', 'warning');
                return;
            }
            try {
                const data = await api(`/issues/${issueId}/assignees`, {
                    method: 'POST',
                    body: JSON.stringify({ user_id: userId }),
                });
                rerender(data.assignees);
                select.value = '';
                showFeedback(feedback, 'Member assigned.', 'success');
            } catch (e) {
                showFeedback(feedback, 'Could not assign.', 'danger');
            }
        });

        list.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-detach]');
            if (!btn) return;
            const row = btn.closest('.tag-chip');
            const userId = row.dataset.userId;
            try {
                const data = await api(`/issues/${issueId}/assignees/${userId}`, { method: 'DELETE' });
                rerender(data.assignees);
                showFeedback(feedback, 'Member removed.', 'success');
            } catch (e) {
                showFeedback(feedback, 'Could not remove.', 'danger');
            }
        });
    }

    function initComments() {
        const root = document.querySelector('[data-comments]');
        if (!root) return;

        const issueId  = root.dataset.issueId;
        const listEl   = root.querySelector('[data-comment-list]');
        const moreBtn  = root.querySelector('[data-comment-more]');
        const form     = root.querySelector('[data-comment-form]');
        const errBox   = root.querySelector('[data-comment-errors]');
        const counter  = root.querySelector('[data-comment-counter]');

        let nextPage = 1;
        let loading  = false;

        async function loadPage() {
            if (loading) return;
            loading = true;
            moreBtn.disabled = true;
            try {
                const data = await api(`/issues/${issueId}/comments?page=${nextPage}`);
                data.comments.forEach(c => {
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = c.html;
                    listEl.appendChild(wrapper.firstElementChild);
                });
                counter.textContent = data.meta.total;
                if (data.meta.has_more) {
                    nextPage = data.meta.next_page;
                    moreBtn.classList.remove('d-none');
                    moreBtn.disabled = false;
                } else {
                    moreBtn.classList.add('d-none');
                }
            } catch (_) {
                moreBtn.disabled = false;
            } finally {
                loading = false;
            }
        }

        moreBtn.addEventListener('click', loadPage);
        loadPage();

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errBox.innerHTML = '';

            const payload = {
                author_name: form.elements.author_name.value.trim(),
                body:        form.elements.body.value.trim(),
            };

            try {
                const data = await api(`/issues/${issueId}/comments`, {
                    method: 'POST',
                    body: JSON.stringify(payload),
                });
                const wrapper = document.createElement('div');
                wrapper.innerHTML = data.comment.html;
                listEl.insertBefore(wrapper.firstElementChild, listEl.firstChild);
                counter.textContent = (parseInt(counter.textContent, 10) || 0) + 1;
                listEl.scrollTop = 0;
                form.reset();
            } catch (err) {
                if (err.status === 422 && err.data && err.data.errors) {
                    const lis = Object.values(err.data.errors).flat()
                        .map(m => `<li>${escapeHtml(m)}</li>`).join('');
                    errBox.innerHTML = `<div class="alert alert-danger mb-2"><ul class="mb-0">${lis}</ul></div>`;
                } else {
                    errBox.innerHTML = `<div class="alert alert-danger mb-2">Could not post comment.</div>`;
                }
            }
        });
    }

    function initIssueQuickEdit() {
        const root = document.querySelector('[data-issue-quick]');
        if (!root) return;

        const issueId = root.dataset.issueId;

        const statusClasses = {
            open:        'bg-primary',
            in_progress: 'bg-info text-dark',
            closed:      'bg-success',
        };
        const statusLabels = {
            open: 'Open', in_progress: 'In Progress', closed: 'Closed',
        };
        const priorityClasses = {
            low: 'bg-secondary', medium: 'bg-warning text-dark', high: 'bg-danger',
        };
        const priorityLabels = {
            low: 'Low', medium: 'Medium', high: 'High',
        };

        const feedback = root.querySelector('[data-quick-feedback]');

        root.addEventListener('click', async (e) => {
            const item = e.target.closest('[data-quick-set]');
            if (!item) return;

            const field = item.dataset.quickSet;
            const value = item.dataset.value;
            const trigger = root.querySelector(`[data-quick-trigger="${field}"]`);

            if (trigger.dataset.current === value) return;

            const original = {
                className: trigger.className,
                text: trigger.textContent.trim(),
                value: trigger.dataset.current,
            };

            const classes = field === 'status' ? statusClasses : priorityClasses;
            const labels  = field === 'status' ? statusLabels  : priorityLabels;

            trigger.className = `badge ${classes[value]} dropdown-toggle border-0`;
            trigger.textContent = labels[value];
            trigger.dataset.current = value;

            try {
                await api(`/issues/${issueId}/status`, {
                    method: 'PATCH',
                    body: JSON.stringify({ [field]: value }),
                });

                root.querySelectorAll(`[data-quick-set="${field}"]`).forEach(btn => {
                    const check = btn.querySelector('.bi-check2');
                    if (check) check.remove();
                });
                const newCheck = document.createElement('i');
                newCheck.className = 'bi bi-check2 ms-auto text-success';
                item.appendChild(newCheck);

                showFeedback(feedback, `${field === 'status' ? 'Status' : 'Priority'} updated.`, 'success');
            } catch (_) {
                trigger.className = original.className;
                trigger.textContent = original.text;
                trigger.dataset.current = original.value;
                showFeedback(feedback, 'Could not update — please retry.', 'danger');
            }
        });
    }

    function initIssuesFilter() {
        const form = document.querySelector('[data-issues-filter]');
        if (!form) return;

        const target = document.querySelector('[data-issues-target]');
        const pager  = document.querySelector('[data-issues-pager]');

        async function refresh() {
            const params = new URLSearchParams(new FormData(form)).toString();
            const url = `${window.PRITECH.urls.issuesIndex}?${params}`;
            try {
                const data = await api(url);
                target.innerHTML = data.html;
                pager.innerHTML  = data.pagination;
                window.history.replaceState({}, '', `?${params}`);
            } catch (_) {}
        }

        const debouncedRefresh = debounce(refresh, 300);

        form.querySelectorAll('select').forEach(el => el.addEventListener('change', refresh));
        const search = form.querySelector('input[name="q"]');
        if (search) search.addEventListener('input', debouncedRefresh);

        document.addEventListener('click', (e) => {
            const link = e.target.closest('[data-issues-pager] a');
            if (!link) return;
            e.preventDefault();
            const url = new URL(link.href);
            const page = url.searchParams.get('page') || 1;
            const formData = new FormData(form);
            formData.set('page', page);
            const params = new URLSearchParams(formData).toString();
            api(`${window.PRITECH.urls.issuesIndex}?${params}`).then(data => {
                target.innerHTML = data.html;
                pager.innerHTML  = data.pagination;
                window.history.replaceState({}, '', `?${params}`);
            });
        });
    }

    function showFeedback(el, message, type) {
        if (!el) return;
        el.innerHTML = `<div class="alert alert-${type} py-1 px-2 mb-0 small">${escapeHtml(message)}</div>`;
        clearTimeout(el._t);
        el._t = setTimeout(() => { el.innerHTML = ''; }, 2500);
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, s => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
        }[s]));
    }

    function hexToSoft(hex) {
        const m = /^#([0-9a-f]{6})$/i.exec(hex);
        if (!m) return '#ecf0f1';
        const n = parseInt(m[1], 16);
        const r = (n >> 16) & 255, g = (n >> 8) & 255, b = n & 255;
        return `rgba(${r},${g},${b},0.18)`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        initIssueTags();
        initIssueAssignees();
        initComments();
        initIssuesFilter();
        initIssueQuickEdit();
    });
})();
