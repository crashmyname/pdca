<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log — PDCA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f1f5f9;
            padding: 20px;
            color: #111827;
            min-height: 100vh;
        }
        .container {
            max-width: 1500px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        h1 { font-size: 22px; color: #1e293b; margin-bottom: 6px; display: flex; align-items: center; gap: 10px; }
        p.subtitle { color: #64748b; font-size: 13px; margin-bottom: 20px; }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 16px;
            padding: 12px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
        }
        .filter-bar input,
        .filter-bar select {
            padding: 7px 11px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            background: white;
            color: #111827;
        }
        .filter-bar input:focus,
        .filter-bar select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .filter-bar .filter-label {
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            margin-right: -4px;
        }

        /* ── BUTTONS ── */
        .btn {
            padding: 7px 14px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            background: white;
            color: #374151;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .btn:hover { background: #f9fafb; }
        .btn-primary { background: #2563eb; color: white; border-color: #2563eb; }
        .btn-primary:hover { background: #1e40af; border-color: #1e40af; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid #e5e7eb; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th {
            background: #f3f4f6;
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 700;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        tr:hover td { background: #f8fafc; }
        tr:last-child td { border-bottom: none; }

        /* ── BADGE ── */
        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .badge-create      { background: #dcfce7; color: #166534; }
        .badge-update      { background: #fef3c7; color: #92400e; }
        .badge-delete      { background: #fee2e2; color: #991b1b; }
        .badge-restore     { background: #dbeafe; color: #1e40af; }
        .badge-force_delete{ background: #fee2e2; color: #7f1d1d; }

        /* ── ACTION BUTTONS IN TABLE ── */
        .action-cell {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            font-family: inherit;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s;
            white-space: nowrap;
        }
        .btn-action:hover { transform: translateY(-1px); box-shadow: 0 2px 6px rgba(0,0,0,0.15); }
        .btn-view     { background: #2563eb; color: white; }
        .btn-view:hover { background: #1e40af; }
        .btn-restore  { background: #16a34a; color: white; }
        .btn-restore:hover { background: #15803d; }
        .btn-rollback { background: #dc2626; color: white; }
        .btn-rollback:hover { background: #b91c1c; }

        /* ── EMPTY STATE ── */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
        }
        .empty-state .ti {
            font-size: 42px;
            display: block;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        /* ── LOADING ── */
        .loading-state {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        .loading-state .ti {
            font-size: 32px;
            display: block;
            margin-bottom: 8px;
            color: #2563eb;
        }
        .ti-spin { animation: spin 0.9s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── PREVIEW DIFF ── */
        .diff-block {
            background: #f8fafc;
            border-radius: 6px;
            padding: 10px;
            font-size: 12px;
            font-family: 'Courier New', monospace;
            max-height: 250px;
            overflow: auto;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .diff-old { background: #fef2f2; border-left: 3px solid #dc2626; }
        .diff-new { background: #f0fdf4; border-left: 3px solid #16a34a; }

        @media (max-width: 768px) {
            .container { padding: 14px; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-bar input, .filter-bar select, .filter-bar button { width: 100%; }
            .action-cell { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="container">

    <h1><i class="ti ti-history"></i> Audit Log</h1>
    <p class="subtitle">
        Riwayat perubahan data. Klik <strong>View</strong> untuk detail,
        <strong>Restore</strong> untuk kembalikan data terhapus,
        <strong>Rollback</strong> untuk undo perubahan.
    </p>

    <!-- ═══════════════ FILTER ═══════════════ -->
    <div class="filter-bar">
        <select id="filterTable" title="Filter Tabel">
            <option value="">Semua Tabel</option>
            <option value="tasks">Tasks</option>
            <option value="task_documents">Task Documents</option>
            <option value="nursecall_records">Nursecall Records</option>
            <option value="nursecall_items">Nursecall Items</option>
            <option value="record_4m">Record 4M</option>
            <option value="record_4m_items">Record 4M Items</option>
        </select>

        <select id="filterAction" title="Filter Aksi">
            <option value="all">Semua Aksi</option>
            <option value="create">Create</option>
            <option value="update">Update</option>
            <option value="delete">Delete</option>
            <option value="restore">Restore</option>
        </select>

        <input type="date" id="filterFrom" title="Dari Tanggal">
        <input type="date" id="filterTo" title="Sampai Tanggal">

        <button class="btn btn-primary" onclick="loadLogs()">
            <i class="ti ti-filter"></i> Terapkan
        </button>

        <button class="btn" onclick="resetFilter()" title="Reset semua filter">
            <i class="ti ti-refresh"></i> Reset
        </button>

        <div style="margin-left:auto">
            <span id="logCount" style="font-size:12px;color:#6b7280;font-weight:600"></span>
        </div>
    </div>

    <!-- ═══════════════ TABLE ═══════════════ -->
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">ID</th>
                    <th style="width:150px">Waktu</th>
                    <th style="width:100px">Aksi</th>
                    <th style="width:150px">Tabel</th>
                    <th style="width:80px">Record ID</th>
                    <th style="width:180px">User</th>
                    <th>Catatan</th>
                    <th style="width:220px">Aksi</th>
                </tr>
            </thead>
            <tbody id="logTableBody">
                <tr>
                    <td colspan="8" class="loading-state">
                        <i class="ti ti-loader-2 ti-spin"></i>
                        Memuat data...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // ═══════════════════════════════════════════════════════
    // CONFIG
    // ═══════════════════════════════════════════════════════
    var API_BASE = '<?= url() ?>';

    // ⭐ CSRF Token — dipakai untuk POST/PUT/DELETE
    window.CSRF_TOKEN  = '<?= csrfHeader() ?>';
    window.CSRF_HEADER = 'X-CSRF-TOKEN';

    // ═══════════════════════════════════════════════════════
    // API HELPER
    // ═══════════════════════════════════════════════════════
    function api(path, opts) {
        opts = opts || {};
        var url = API_BASE + path;

        if (opts.query) {
            var qs = new URLSearchParams();
            Object.keys(opts.query).forEach(function (k) {
                var v = opts.query[k];
                if (v !== '' && v !== null && v !== undefined) qs.append(k, v);
            });
            var s = qs.toString();
            if (s) url += (url.indexOf('?') >= 0 ? '&' : '?') + s;
        }

        // ⭐ Bangun headers
        var headers = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };

        // ⭐ Inject CSRF token (selalu, GET juga aman)
        if (window.CSRF_TOKEN) {
            headers[window.CSRF_HEADER] = window.CSRF_TOKEN;
        }

        if (opts.body) {
            headers['Content-Type'] = 'application/json';
        }

        return fetch(url, {
            method: opts.method || 'GET',
            credentials: 'include',
            headers: headers,
            body: opts.body ? JSON.stringify(opts.body) : undefined,
        }).then(function (res) {
            return res.text().then(function (text) {
                var data = null;
                try { data = text ? JSON.parse(text) : null; } catch (e) { data = text; }
                if (!res.ok) {
                    var err = new Error((data && data.message) || 'HTTP ' + res.status);
                    err.status = res.status;
                    throw err;
                }
                return data;
            });
        });
    }

    // ═══════════════════════════════════════════════════════
    // HELPERS
    // ═══════════════════════════════════════════════════════
    function escapeHtml(s) {
        if (s == null) return '';
        return String(s).replace(/[&<>"']/g, function (c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function formatDateTime(d) {
        if (!d) return '-';
        try {
            var date = new Date(String(d).replace(' ', 'T'));
            return date.toLocaleString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit'
            });
        } catch (e) {
            return d;
        }
    }

    // ═══════════════════════════════════════════════════════
    // LOAD LOGS
    // ═══════════════════════════════════════════════════════
    function loadLogs() {
        var query = {
            table_name: $('#filterTable').val() || '',
            action:     $('#filterAction').val() || 'all',
            date_from:  $('#filterFrom').val() || '',
            date_to:    $('#filterTo').val() || '',
            per_page:   100,
        };

        $('#logTableBody').html(
            '<tr><td colspan="8" class="loading-state">' +
                '<i class="ti ti-loader-2 ti-spin"></i>' +
                'Memuat data...' +
            '</td></tr>'
        );

        api('/audit', { query: query })
            .then(function (res) {
                var data = res && res.data ? res.data : (Array.isArray(res) ? res : []);

                $('#logCount').text(data.length + ' log ditemukan');

                if (data.length === 0) {
                    $('#logTableBody').html(
                        '<tr><td colspan="8" class="empty-state">' +
                            '<i class="ti ti-inbox"></i>' +
                            'Tidak ada log untuk filter yang dipilih' +
                        '</td></tr>'
                    );
                    return;
                }

                renderLogs(data);
            })
            .catch(function (err) {
                console.error('[loadLogs]', err);
                $('#logTableBody').html(
                    '<tr><td colspan="8" class="empty-state" style="color:#dc2626">' +
                        '<i class="ti ti-alert-circle"></i>' +
                        'Gagal memuat: ' + escapeHtml(err.message) +
                    '</td></tr>'
                );
            });
    }

    function renderLogs(data) {
        var html = data.map(function (log) {
            var action = log.action || 'unknown';
            var badgeCls = 'badge-' + action;

            var actions = '';

            actions +=
                '<button class="btn-action btn-view" onclick="viewLog(' + log.id + ')" title="Lihat detail">' +
                    '<i class="ti ti-eye"></i> View' +
                '</button>';

            if (action === 'delete') {
                actions +=
                    '<button class="btn-action btn-restore" onclick="restoreLog(' + log.id + ')" title="Restore data">' +
                        '<i class="ti ti-restore"></i> Restore' +
                    '</button>';
            }

            if (action === 'create' || action === 'update') {
                actions +=
                    '<button class="btn-action btn-rollback" onclick="rollbackLog(' + log.id + ')" title="Undo aksi">' +
                        '<i class="ti ti-arrow-back-up"></i> Rollback' +
                    '</button>';
            }

            return '<tr>' +
                '<td><strong>#' + log.id + '</strong></td>' +
                '<td style="white-space:nowrap">' + formatDateTime(log.created_at) + '</td>' +
                '<td><span class="badge ' + badgeCls + '">' + escapeHtml(action) + '</span></td>' +
                '<td><code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;font-size:11px">' + escapeHtml(log.table_name || '-') + '</code></td>' +
                '<td><strong>' + (log.record_id || '-') + '</strong></td>' +
                '<td>' +
                    escapeHtml(log.changed_by_name || '-') +
                    (log.changed_by_role ? '<br><small style="color:#9ca3af">' + escapeHtml(log.changed_by_role) + '</small>' : '') +
                '</td>' +
                '<td style="max-width:300px;word-break:break-word;font-size:12px;color:#6b7280">' +
                    escapeHtml(log.notes || '-') +
                '</td>' +
                '<td><div class="action-cell">' + actions + '</div></td>' +
            '</tr>';
        }).join('');

        $('#logTableBody').html(html);
    }

    // ═══════════════════════════════════════════════════════
    // RESET FILTER
    // ═══════════════════════════════════════════════════════
    function resetFilter() {
        $('#filterTable').val('');
        $('#filterAction').val('all');
        $('#filterFrom').val('');
        $('#filterTo').val('');
        loadLogs();
    }

    // ═══════════════════════════════════════════════════════
    // VIEW DETAIL
    // ═══════════════════════════════════════════════════════
    function viewLog(id) {
        Swal.fire({
            title: 'Memuat...',
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); },
        });

        api('/audit/' + id)
            .then(function (log) {
                Swal.close();
                showLogDetail(log);
            })
            .catch(function (err) {
                Swal.close();
                Swal.fire('Gagal', err.message, 'error');
            });
    }

    function showLogDetail(log) {
        var html = '<div style="text-align:left;font-size:13px;line-height:1.7">';

        html += '<div style="background:#f8fafc;padding:12px;border-radius:6px;margin-bottom:12px">';
        html += '<div><strong>Tabel:</strong> <code style="background:#e5e7eb;padding:2px 6px;border-radius:4px">' + escapeHtml(log.table_name) + '</code></div>';
        html += '<div><strong>Record ID:</strong> #' + log.record_id + '</div>';
        html += '<div><strong>Aksi:</strong> <span class="badge badge-' + log.action + '">' + escapeHtml(log.action) + '</span></div>';
        html += '<div><strong>Oleh:</strong> ' + escapeHtml(log.changed_by_name || '-') + (log.changed_by_role ? ' (' + escapeHtml(log.changed_by_role) + ')' : '') + '</div>';
        html += '<div><strong>Waktu:</strong> ' + formatDateTime(log.created_at) + '</div>';
        if (log.ip_address) {
            html += '<div><strong>IP:</strong> <code>' + escapeHtml(log.ip_address) + '</code></div>';
        }
        html += '</div>';

        if (log.changed_fields && Array.isArray(log.changed_fields) && log.changed_fields.length > 0) {
            html += '<div style="margin-bottom:12px">';
            html += '<strong style="color:#6b7280;font-size:12px">FIELD YANG BERUBAH</strong><br>';
            log.changed_fields.forEach(function (f) {
                html += '<span style="display:inline-block;background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:4px;font-size:11px;margin:3px 3px 0 0">' + escapeHtml(f) + '</span>';
            });
            html += '</div>';
        }

        if (log.old_values) {
            html += '<div style="margin-bottom:12px">';
            html += '<strong style="color:#dc2626;font-size:12px">DATA SEBELUM</strong>';
            html += '<pre class="diff-block diff-old">' + escapeHtml(JSON.stringify(log.old_values, null, 2)) + '</pre>';
            html += '</div>';
        }

        if (log.new_values) {
            html += '<div style="margin-bottom:12px">';
            html += '<strong style="color:#16a34a;font-size:12px">DATA SESUDAH</strong>';
            html += '<pre class="diff-block diff-new">' + escapeHtml(JSON.stringify(log.new_values, null, 2)) + '</pre>';
            html += '</div>';
        }

        html += '</div>';

        Swal.fire({
            title: 'Audit Log #' + log.id,
            html: html,
            width: 780,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#2563eb',
        });
    }

    // ═══════════════════════════════════════════════════════
    // RESTORE (untuk delete)
    // ═══════════════════════════════════════════════════════
    function restoreLog(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Restore Data?',
            html: 'Data akan di-restore ke kondisi <b>sebelum dihapus</b>.<br>' +
                  '<small style="color:#6b7280">Tindakan ini akan dicatat ke audit log.</small>',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-restore"></i> Ya, Restore',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#16a34a',
            reverseButtons: true,
        }).then(function (r) {
            if (!r.isConfirmed) return;
            doRestore(id);
        });
    }

    function doRestore(id) {
        Swal.fire({
            title: 'Memproses...',
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); },
        });

        api('/audit/' + id + '/restore', { method: 'POST' })
            .then(function (res) {
                Swal.close();
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Data berhasil di-restore',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    loadLogs();
                } else {
                    Swal.fire('Gagal', res.message || 'Restore gagal', 'error');
                }
            })
            .catch(function (err) {
                Swal.close();
                Swal.fire('Error', err.message || 'Gagal restore', 'error');
            });
    }

    // ═══════════════════════════════════════════════════════
    // ROLLBACK (untuk create / update)
    // ═══════════════════════════════════════════════════════
    function rollbackLog(id) {
        Swal.fire({
            icon: 'warning',
            title: 'Rollback Aksi Ini?',
            html: 'Data akan dikembalikan ke kondisi <b>sebelum</b> aksi ini.<br><br>' +
                  '<small style="color:#dc2626">Aksi ini tidak bisa dibatalkan.</small>',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-arrow-back-up"></i> Ya, Rollback',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            reverseButtons: true,
        }).then(function (r) {
            if (!r.isConfirmed) return;
            doRollback(id);
        });
    }

    function doRollback(id) {
        Swal.fire({
            title: 'Memproses rollback...',
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); },
        });

        api('/audit/' + id + '/rollback', { method: 'POST' })
            .then(function (res) {
                Swal.close();
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message || 'Rollback berhasil',
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    loadLogs();
                } else {
                    Swal.fire('Gagal', res.message || 'Rollback gagal', 'error');
                }
            })
            .catch(function (err) {
                Swal.close();
                Swal.fire('Error', err.message || 'Gagal rollback', 'error');
            });
    }

    // ═══════════════════════════════════════════════════════
    // INIT
    // ═══════════════════════════════════════════════════════
    $(document).ready(function () {
        // Debug: cek CSRF token ke-console
        console.log('[Audit] CSRF Token length:', (window.CSRF_TOKEN || '').length);

        loadLogs();
    });
</script>
</body>
</html>