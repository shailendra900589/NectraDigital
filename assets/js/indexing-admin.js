(function() {
    var root = document.getElementById('indexingLiveRoot');
    if (!root) return;

    var apiUrl = root.getAttribute('data-api-url') || 'indexing-api.php';
    var pollMs = parseInt(root.getAttribute('data-poll-ms') || '8000', 10);
    var timer = null;
    var busy = false;

    function fmt(n) {
        n = parseInt(n, 10) || 0;
        try { return n.toLocaleString(); } catch (e) { return String(n); }
    }

    function setText(id, value) {
        var el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function statusBadge(status) {
        var s = (status || '').toLowerCase();
        if (s === 'completed') return '<span class="ge-badge ge-badge-indexed">completed</span>';
        if (s === 'failed') return '<span class="ge-badge ge-badge-failed">failed</span>';
        if (s === 'pending') return '<span class="ge-badge ge-badge-pending">pending</span>';
        return '<span class="ge-badge">' + (status || '—') + '</span>';
    }

    function pathFromUrl(url) {
        if (!url) return '—';
        try {
            var u = new URL(url, window.location.origin);
            return u.pathname || url;
        } catch (e) {
            return url;
        }
    }

    function renderActivity(rows) {
        var tbody = document.getElementById('idxActivityBody');
        if (!tbody) return;
        if (!rows || !rows.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-muted text-center small">No queue activity yet.</td></tr>';
            return;
        }
        tbody.innerHTML = rows.map(function(row) {
            var when = row.processed_at || row.created_at || '—';
            return '<tr>' +
                '<td class="small"><code>' + pathFromUrl(row.url) + '</code></td>' +
                '<td>' + statusBadge(row.status) + '</td>' +
                '<td class="small text-muted">' + (row.created_at || '—') + '</td>' +
                '<td class="small text-muted">' + (row.processed_at || '—') + '</td>' +
                '</tr>';
        }).join('');
    }

    function applyStats(data) {
        if (!data || !data.stats) return;
        var s = data.stats;
        var pages = s.pages || {};
        var queue = s.queue || {};

        setText('idx-stat-total', fmt(pages.total));
        setText('idx-stat-indexed', fmt(pages.indexed));
        setText('idx-stat-pending', fmt(pages.pending));
        setText('idx-stat-submitted', fmt(pages.submitted));
        setText('idx-stat-failed', fmt(pages.failed));
        setText('idx-stat-stale', fmt(pages.stale_submitted));

        setText('idx-queue-pending', fmt(queue.pending));
        setText('idx-queue-completed', fmt(queue.completed));
        setText('idx-queue-failed', fmt(queue.failed));

        var pct = parseInt(s.queue_progress_pct, 10) || 0;
        var bar = document.getElementById('idxQueueProgress');
        if (bar) {
            bar.style.width = pct + '%';
            bar.setAttribute('aria-valuenow', String(pct));
        }
        setText('idx-queue-pct', pct + '%');

        var live = document.getElementById('idxLiveStatus');
        if (live) {
            var updated = s.updated_at ? new Date(s.updated_at).toLocaleTimeString() : '—';
            live.textContent = 'Live · updated ' + updated;
        }

        var lastCron = document.getElementById('idxLastCron');
        if (lastCron) {
            lastCron.textContent = s.last_cron_at
                ? ('Last cron: ' + s.last_cron_at + (s.last_cron_meta && s.last_cron_meta.processed != null ? ' · processed ' + s.last_cron_meta.processed : ''))
                : 'Last cron: never (set up Hostinger cron below)';
        }

        var lastQ = document.getElementById('idxLastQueue');
        if (lastQ) {
            lastQ.textContent = s.last_queue_processed_at
                ? ('Last queue batch: ' + s.last_queue_processed_at)
                : 'Last queue batch: none yet';
        }

        var alertEl = document.getElementById('idxQueueAlert');
        if (alertEl) {
            if ((queue.pending || 0) > 0) {
                alertEl.classList.remove('d-none');
                setText('idx-queue-alert-count', fmt(queue.pending));
            } else {
                alertEl.classList.add('d-none');
            }
        }

        if (data.activity) renderActivity(data.activity);
    }

    function refresh() {
        if (busy) return;
        busy = true;
        fetch(apiUrl + '?t=' + Date.now(), { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.ok) applyStats(data);
            })
            .catch(function() {})
            .finally(function() { busy = false; });
    }

    function processBatchAjax(btn) {
        if (busy) return;
        busy = true;
        var original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing…';
        var fd = new FormData();
        fd.append('action', 'process_batch');
        fetch(apiUrl, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data && data.ok) {
                    applyStats(data);
                    var toast = document.getElementById('idxToast');
                    if (toast) {
                        toast.textContent = data.message || 'Batch processed.';
                        toast.classList.remove('d-none');
                        setTimeout(function() { toast.classList.add('d-none'); }, 4000);
                    }
                }
            })
            .catch(function() {})
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = original;
                busy = false;
                refresh();
            });
    }

    var ajaxBtn = document.getElementById('idxProcessAjax');
    if (ajaxBtn) {
        ajaxBtn.addEventListener('click', function() { processBatchAjax(ajaxBtn); });
    }

    refresh();
    timer = setInterval(refresh, pollMs);
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) refresh();
    });
})();
