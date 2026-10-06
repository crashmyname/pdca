<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — PDCA</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f1f5f9;
            color: #111827;
            min-height: 100vh;
        }

        /* ── HEADER ── */
        .header {
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: white;
            padding: 12px 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px; margin: 0 auto;
            display: flex; justify-content: space-between; align-items: center;
            gap: 12px; flex-wrap: wrap;
        }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .logo {
            width: 38px; height: 38px; background: white; color: #1e40af;
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 18px; flex-shrink: 0;
        }
        .header-title { font-size: 16px; font-weight: 600; }
        .header-subtitle { font-size: 11px; opacity: 0.85; }
        .header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        .user-info {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px; background: rgba(255,255,255,0.15);
            border-radius: 8px; font-size: 13px; border: 1px solid rgba(255,255,255,0.25);
        }
        .user-avatar {
            width: 26px; height: 26px; background: white; color: #1e40af;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }
        .user-role-badge {
            font-size: 10px; font-weight: 700; padding: 2px 8px;
            border-radius: 10px; background: rgba(255,255,255,0.25);
            text-transform: uppercase; letter-spacing: 0.4px;
        }
        .header-btn {
            background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3);
            color: white; padding: 6px 12px; border-radius: 6px;
            font-size: 12px; font-weight: 500; cursor: pointer;
            text-decoration: none; display: inline-flex; align-items: center; gap: 5px;
            font-family: inherit; transition: all 0.15s;
        }
        .header-btn:hover { background: rgba(255,255,255,0.25); color: white; }

        /* ── CONTAINER ── */
        .container { max-width: 1400px; margin: 0 auto; padding: 20px 24px; }

        /* ── TABS ── */
        .support-tabs {
            display: flex; gap: 4px; background: white;
            padding: 6px; border-radius: 10px; margin-bottom: 20px;
            border: 1px solid #e5e7eb; box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .support-tab {
            flex: 1; padding: 10px 16px; border: none; background: transparent;
            border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;
            color: #6b7280; display: flex; align-items: center; justify-content: center;
            gap: 6px; font-family: inherit; transition: all 0.15s; text-decoration: none;
        }
        .support-tab:hover { background: #f3f4f6; }
        .support-tab.active { background: #2563eb; color: white; }

        /* ── FORM SECTION ── */
        .form-section {
            background: white; border: 1px solid #e5e7eb;
            border-radius: 10px; padding: 18px 20px; margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .form-label {
            display: block; margin-bottom: 5px;
            font-size: 12px; font-weight: 600; color: #4b5563;
        }
        .form-input, .form-select {
            width: 100%; padding: 7px 11px;
            border: 1px solid #d1d5db; border-radius: 6px;
            font-size: 13px; font-family: inherit;
            background: white; color: #111827; transition: all 0.15s;
        }
        .form-input:focus, .form-select:focus {
            outline: none; border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .btn {
            padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 6px;
            cursor: pointer; font-weight: 500; font-size: 13px;
            display: inline-flex; align-items: center; gap: 6px;
            background: white; color: #374151; font-family: inherit;
            transition: all 0.15s; text-decoration: none;
        }
        .btn:hover { background: #f9fafb; border-color: #9ca3af; }
        .btn-primary { background: #2563eb; border-color: #2563eb; color: white; }
        .btn-primary:hover { background: #1e40af; border-color: #1e40af; }
        .btn-danger { background: #dc2626; border-color: #dc2626; color: white; }
        .btn-danger:hover { background: #b91c1c; border-color: #b91c1c; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ── FILTER ── */
        .report-filter {
            background: white; border: 1px solid #e5e7eb;
            border-radius: 10px; padding: 16px 20px; margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .filter-row {
            display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
        }
        .filter-row .form-group { margin-bottom: 0; flex: 1; min-width: 150px; }

        /* ── REPORT CONTENT ── */
        #srReportContainer {
            background: white; padding: 20px;
            border: 1px solid #e5e7eb; border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }

        /* Embedded mode */
        body.embedded .header { display: none !important; }
        body.embedded .container { padding-top: 12px; }

        @media (max-width: 768px) {
            .header-content { flex-direction: column; align-items: flex-start; }
            .container { padding: 12px; }
            .filter-row { flex-direction: column; }
            .filter-row .form-group { width: 100%; }
        }
        /* ═══════════════════════════════════════════════════════ */
        /* REPORT PAGE — Print-friendly A4 landscape             */
        /* ═══════════════════════════════════════════════════════ */
        .sr-page {
            background: #ffffff;
            padding: 12mm 10mm;
            margin-bottom: 20px;
            font-family: Arial, 'Helvetica Neue', sans-serif;
            color: #000;
            page-break-after: always;
            box-sizing: border-box;
        }
        .sr-page:last-child {
            page-break-after: auto;
            margin-bottom: 0;
        }

        /* ── TOP HEADER ── */
        .sr-top {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 6px;
        }
        .sr-top-logo {
            width: 130px;
            flex-shrink: 0;
        }
        .sr-top-logo img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .sr-top-title {
            flex: 1;
            text-align: center;
            padding-top: 6px;
        }
        .sr-top-title .sr-main-title {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #000;
            margin-bottom: 3px;
        }
        .sr-top-title .sr-sub-title {
            font-size: 11px;
            font-weight: 600;
            color: #000;
        }
        .sr-top-sig {
            width: 240px;
            flex-shrink: 0;
        }
        .sr-top-sig .sr-sig-date {
            font-size: 9px;
            text-align: right;
            margin-bottom: 3px;
        }
        .sr-top-sig table {
            width: 100%;
            border-collapse: collapse;
        }
        .sr-top-sig td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: top;
        }
        .sr-top-sig .sr-sig-header {
            text-align: center;
            font-size: 8px;
            font-weight: 700;
            background: #f0f0f0;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 2px 4px;
        }
        .sr-top-sig .sr-sig-body {
            height: 55px;
            text-align: center;
            vertical-align: middle;
            font-size: 9px;
            color: #333;
            position: relative;
        }
        .sr-top-sig .sr-sig-body .signature-qrcode {
            width: 50px !important;
            height: 50px !important;
            margin: 0 auto 2px !important;
        }
        .sr-top-sig .sr-sig-body .signature-qrcode img,
        .sr-top-sig .sr-sig-body .signature-qrcode canvas {
            width: 50px !important;
            height: 50px !important;
        }
        .sr-top-sig .sr-sig-name {
            font-size: 9px;
            text-align: center;
            font-weight: 600;
            color: #000;
            margin-top: 2px;
        }

        /* ── INFO ROW ── */
        .sr-info-row {
            display: flex;
            gap: 20px;
            font-size: 10px;
            font-weight: 700;
            margin: 8px 0 6px;
            text-transform: uppercase;
        }
        .sr-info-row .sr-info-item {
            display: flex;
            gap: 4px;
            align-items: center;
        }
        .sr-info-row .sr-info-label {
            min-width: 60px;
            color: #000;
        }
        .sr-info-row .sr-info-sep {
            width: 6px;
            color: #000;
        }
        .sr-info-row .sr-info-value {
            color: #000;
            font-weight: 600;
        }

        /* ── MAIN TABLE NURSECALL ── */
        .sr-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5px;
            margin-bottom: 8px;
            table-layout: fixed;
        }
        .sr-table th,
        .sr-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .sr-table th {
            background: #ffffff;
            text-align: center;
            font-weight: 700;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            padding: 3px 2px;
            line-height: 1.15;
        }
        .sr-table td {
            text-align: left;
            font-size: 9.5px;
            height: 20px;
        }
        .sr-table td.center { text-align: center; }
        .sr-table td.check { text-align: center; font-size: 12px; font-weight: 700; }

        .sr-table .col-tgl       { width: 52px; }
        .sr-table .col-jam       { width: 42px; }
        .sr-table .col-masalah   { width: 130px; }
        .sr-table .col-sumber    { width: 32px; }
        .sr-table .col-tindakan  { width: 130px; }
        .sr-table .col-nurse     { width: 55px; }
        .sr-table .col-status    { width: 42px; }
        .sr-table .col-judge     { width: 50px; }
        .sr-table .col-gleader   { width: 55px; }
        .sr-table .sr-sumber-header {
            text-align: center;
            font-size: 8px;
            line-height: 1.1;
        }
        .sr-table .sr-status-x { color: #dc2626; font-weight: 700; }
        .sr-table .sr-status-o { color: #000; font-weight: 700; }

        /* ── BOTTOM (Nursecall) ── */
        .sr-bottom {
            display: flex;
            gap: 10px;
            margin-top: 6px;
            align-items: stretch;
        }
        .sr-bottom-left {
            flex: 1;
            font-size: 8.5px;
            line-height: 1.5;
            color: #000;
        }
        .sr-bottom-left .sr-keterangan-title {
            font-weight: 700;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 9px;
        }
        .sr-bottom-left ol {
            padding-left: 16px;
            margin: 0;
        }
        .sr-bottom-left ol li {
            margin-bottom: 1px;
        }

        .sr-bottom-right {
            width: 40%;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .sr-bottom-right .sr-komentar-box {
            border: 1px solid #000;
            padding: 3px 5px;
            min-height: 60px;
            font-size: 8.5px;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .sr-bottom-right .sr-komentar-header {
            font-weight: 700;
            font-size: 9px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .sr-bottom-right .sr-tanggal-row {
            display: flex;
            gap: 6px;
            font-size: 9px;
            align-items: center;
        }
        .sr-bottom-right .sr-tanggal-label {
            font-weight: 700;
            text-transform: uppercase;
            min-width: 55px;
        }
        .sr-bottom-right .sr-tanggal-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding: 1px 3px;
            font-size: 9px;
        }

        .sr-manager-sig {
            display: flex;
            gap: 6px;
            margin-top: 4px;
        }
        .sr-manager-sig .sr-manager-sig-box {
            flex: 1;
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: center;
            min-height: 50px;
        }
        .sr-manager-sig .sr-manager-sig-box .sr-sig-header {
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        .sr-manager-sig .sr-manager-sig-box .signature-qrcode {
            width: 40px !important;
            height: 40px !important;
            margin: 2px auto !important;
        }
        .sr-manager-sig .sr-manager-sig-box .signature-qrcode img,
        .sr-manager-sig .sr-manager-sig-box .signature-qrcode canvas {
            width: 40px !important;
            height: 40px !important;
        }
        .sr-manager-sig .sr-manager-sig-box .sr-sig-name {
            font-size: 8px;
            font-weight: 600;
            min-height: 10px;
            border-top: 1px solid #000;
            padding-top: 2px;
        }

        /* ── FORM CODE ── */
        .sr-form-code {
            text-align: right;
            font-size: 8.5px;
            color: #000;
            margin-top: 8px;
            padding-top: 3px;
            font-style: italic;
        }

        /* ── 4M TABLE (2 kolom) ── */
        .sr-4m-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 8px;
        }
        .sr-4m-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            table-layout: fixed;
        }
        .sr-4m-table th,
        .sr-4m-table td {
            border: 1px solid #000;
            padding: 2px 3px;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .sr-4m-table th {
            background: #ffffff;
            text-align: center;
            font-weight: 700;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.1px;
            line-height: 1.1;
            padding: 3px 2px;
        }
        .sr-4m-table td {
            text-align: center;
            font-size: 9px;
            height: 22px;
        }
        .sr-4m-table td.tgl-num {
            font-size: 11px;
            font-weight: 700;
            width: 22px;
            line-height: 1.1;
            text-align: center;
        }
        .sr-4m-table td.problem-cell {
            text-align: left;
            font-size: 8.5px;
            padding-left: 4px;
        }
        .sr-4m-table td.tl-cell,
        .sr-4m-table td.gl-cell {
            font-size: 8.5px;
        }
        .sr-4m-table td.check {
            font-size: 12px;
            font-weight: 700;
        }
        .sr-4m-table .c-tgl    { width: 26px; }
        .sr-4m-table .c-shift  { width: 28px; }
        .sr-4m-table .c-prob   { width: auto; }
        .sr-4m-table .c-cat    { width: 44px; }
        .sr-4m-table .c-tl     { width: 48px; }
        .sr-4m-table .c-gl     { width: 48px; }

        /* ── PRINT STYLES ── */
        @media print {
            @page {
                size: A4 landscape;
                margin: 8mm;
            }
            body > * { display: none !important; }
            body.print-support-report .header,
            body.print-support-report .support-tabs,
            body.print-support-report .report-filter,
            body.print-support-report .container > div:last-child {
                display: none !important;
            }
            body.print-support-report .container {
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            body.print-support-report #srReportContainer {
                border: none !important;
                padding: 0 !important;
                background: white !important;
                box-shadow: none !important;
            }
            .sr-page {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body class="<?= !empty($embedded) ? 'embedded' : '' ?>">

<!-- ═══════════════ HEADER ═══════════════ -->
<header class="header">
    <div class="header-content">
        <div class="header-left">
            <div class="logo">P</div>
            <div>
                <div class="header-title"><?= htmlspecialchars($title) ?></div>
                <div class="header-subtitle">PDCA Report — Continuous Improvement</div>
            </div>
        </div>
        <div class="header-actions">
            <?php if (!empty($isLoggedIn)): ?>
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                    <span><?= htmlspecialchars($userName) ?></span>
                    <span class="user-role-badge"><?= htmlspecialchars(str_replace('_', ' ', $userRole)) ?></span>
                </div>
            <?php endif; ?>
            <a href="<?= env('APP_URL', '/') ?>" class="header-btn">
                <i class="ti ti-home"></i> Dashboard
            </a>
        </div>
    </div>
</header>

<div class="container">

    <!-- ═══════════════ TABS ═══════════════ -->
    <div class="support-tabs">
        <a href="#"
           class="support-tab <?= $type === 'nursecall' ? 'active' : '' ?>">
            <i class="ti ti-bell"></i> Nursecall
        </a>
        <a href="#"
           class="support-tab <?= $type === '4m' ? 'active' : '' ?>">
            <i class="ti ti-notebook"></i> Record Perubahan 4M
        </a>
    </div>

    <!-- ═══════════════ FILTER ═══════════════ -->
    <div class="report-filter">
        <div class="filter-row">
            <div class="form-group">
                <label class="form-label">Bulan / Tahun</label>
                <input type="month" class="form-input" id="srMonthYear">
            </div>
            <div class="form-group">
                <label class="form-label">No Lane</label>
                <select class="form-select" id="srNoLane">
                    <option value="">Semua Lane</option>
                </select>
            </div>
            <div class="form-group" style="flex:0;min-width:auto">
                <button class="btn btn-primary" onclick="loadSupportReport()">
                    <i class="ti ti-filter"></i> Tampilkan
                </button>
            </div>
        </div>
    </div>

    <!-- ═══════════════ REPORT CONTENT ═══════════════ -->
    <div id="srReportContainer">
        <div style="text-align:center;color:#9ca3af;padding:40px">
            <i class="ti ti-loader-2 ti-spin" style="font-size:42px;display:block;margin-bottom:10px;opacity:0.5"></i>
            <div>Memuat report...</div>
        </div>
    </div>

    <!-- ═══════════════ ACTION BAR ═══════════════ -->
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding:12px 20px;background:white;border:1px solid #e5e7eb;border-radius:10px;box-shadow:0 1px 2px rgba(0,0,0,0.04)">
        <button class="btn" onclick="loadSupportReport()">
            <i class="ti ti-reload"></i> Refresh
        </button>
        <button class="btn" onclick="printSupportReport()">
            <i class="ti ti-printer"></i> Print
        </button>
        <button class="btn btn-danger" onclick="exportSupportPDF()">
            <i class="ti ti-file-type-pdf"></i> Export PDF
        </button>
    </div>

</div>

<!-- ═══════════════ SCRIPTS ═══════════════ -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>

<script>
    // ═══════════════════════════════════════════════════════
    // CONFIG
    // ═══════════════════════════════════════════════════════
    window.CSRF_TOKEN  = '<?= csrfHeader() ?>';
    window.CSRF_HEADER = 'X-CSRF-TOKEN';
    $.ajaxSetup({ headers: { [window.CSRF_HEADER]: window.CSRF_TOKEN } });

    var API_BASE    = '<?= url() ?>';
    var ACTIVE_TYPE = '<?= $type ?>';
    var srActiveTab = '<?= $type ?>';
    var srReportData = null;

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
        return fetch(url, {
            method: opts.method || 'GET',
            credentials: 'include',
            headers: Object.assign({
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': window.CSRF_TOKEN,
            }, opts.body ? {'Content-Type': 'application/json'} : {}),
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
    function formatDate(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    }
    function formatDateLong(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }
    function formatDateTime(d) {
        if (!d) return '-';
        var date = new Date(String(d).replace(' ', 'T'));
        return date.toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }
    function showAlert(icon, title, html) {
        Swal.fire({ icon: icon, title: title, html: html, confirmButtonColor: '#2563eb' });
    }

    function renderQRCode(containerEl, text) {
        if (!containerEl) return;
        var value = String(text || '').trim();
        if (!value || typeof QRCode === 'undefined') {
            containerEl.innerHTML = '';
            return;
        }
        try {
            containerEl.innerHTML = '';
            new QRCode(containerEl, {
                text: value, width: 60, height: 60,
                colorDark: '#1f2937', colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M,
            });
            setTimeout(function () {
                var canvas = containerEl.querySelector('canvas');
                if (canvas) canvas.style.setProperty('display', 'block', 'important');
                containerEl.querySelectorAll('img').forEach(function (img) { img.remove(); });
            }, 100);
        } catch (e) {
            console.warn('[QRCode]', e);
            containerEl.innerHTML = '';
        }
    }

    // ═══════════════════════════════════════════════════════
    // LOAD REPORT
    // ═══════════════════════════════════════════════════════
    function loadSupportReport() {
        var monthYear = $('#srMonthYear').val() || '';
        var noLane    = $('#srNoLane').val()    || '';

        var endpoint = ACTIVE_TYPE === 'nursecall'
            ? '/support/nursecall/report'
            : '/support/4m/report';

        $('#srReportContainer').html(
            '<div style="text-align:center;color:#9ca3af;padding:40px">' +
                '<i class="ti ti-loader-2 ti-spin" style="font-size:42px;display:block;margin-bottom:10px;opacity:0.5"></i>' +
                '<div>Memuat report...</div>' +
            '</div>'
        );

        api(endpoint, { query: { month_year: monthYear, no_lane: noLane } })
            .then(function (res) {
                var list = Array.isArray(res) ? res : (res && res.data || []);
                srReportData = list;

                if (list.length === 0) {
                    $('#srReportContainer').html(
                        '<div style="text-align:center;color:#9ca3af;padding:40px">' +
                            '<i class="ti ti-inbox" style="font-size:42px;display:block;margin-bottom:10px;opacity:0.4"></i>' +
                            '<div>Tidak ada data untuk filter yang dipilih</div>' +
                        '</div>'
                    );
                    return;
                }

                renderSupportReport(list);
                sendReportSummaryToParent(list);

            }).catch(function (err) {
                $('#srReportContainer').html(
                    '<div style="text-align:center;color:#dc2626;padding:40px">' +
                        '<i class="ti ti-alert-circle" style="font-size:42px;display:block;margin-bottom:10px"></i>' +
                        '<div>Gagal memuat: ' + escapeHtml(err.message) + '</div>' +
                    '</div>'
                );
            });
    }

    // ═══════════════════════════════════════════════════════
    // DISPATCHER
    // ═══════════════════════════════════════════════════════
    function renderSupportReport(list) {
        if (ACTIVE_TYPE === 'nursecall') {
            renderNursecallReport(list);
        } else {
            renderRecord4mReport(list);
        }
    }

    function renderNursecallReport(list) {
        const html = list.map((entry, idx) => {
            const rec   = entry.record;
            const items = entry.items || [];
            const sigKey = 'nc';

            // Bulan / Tahun label
            let monthLabel = rec.month_year || '-';
            if (rec.month_year && rec.month_year.length === 7) {
                const [y, m] = rec.month_year.split('-');
                const monthNames = ['Januari','Februari','Maret','April','Mei','Juni',
                                    'Juli','Agustus','September','Oktober','November','Desember'];
                monthLabel = monthNames[parseInt(m, 10) - 1] + '/' + y;
            }

            const headerDate = rec.date_created || rec.footer_date || '';
            const headerDateLabel = headerDate
                ? new Date(headerDate).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
                : '';

            const sigCreated  = rec.footer_created_name  || rec.created_by_name  || '';
            const sigApproved = rec.footer_approved_name || rec.approved_by_name || '';

            // ── Tabel content Nursecall (15 baris min) ──
            const sumberCols = ['part', 'machine', 'man_power', 'tool', 'material', 'dll'];
            const minRows = 15;
            let rowsHtml = '';

            for (let i = 0; i < minRows; i++) {
                const it = items[i];
                if (it) {
                    const sumber = String(it.source || '').toLowerCase();
                    const sumberCells = sumberCols.map(col => {
                        const checked = sumber === col;
                        return `<td class="check">${checked ? '✓' : '&ndash;'}</td>`;
                    }).join('');

                    const statusVal = String(it.status || '').toUpperCase();
                    const judgeVal  = String(it.judgement || '').toUpperCase();

                    rowsHtml += `
                        <tr>
                            <td style="text-align:center">${it.date ? formatDateSlash(it.date) : ''}</td>
                            <td style="text-align:center">${it.time ? String(it.time).slice(0, 5) : ''}</td>
                            <td>${escapeHtml(it.problem || '')}</td>
                            ${sumberCells}
                            <td>${escapeHtml(it.action || '')}</td>
                            <td style="text-align:center">${escapeHtml(it.nurse_leader || '')}</td>
                            <td style="text-align:center" class="sr-status-${statusVal === 'O' ? 'o' : (statusVal === 'X' ? 'x' : '')}">${escapeHtml(statusVal || '')}</td>
                            <td style="text-align:center" class="sr-status-${judgeVal === 'O' ? 'o' : (judgeVal === 'X' ? 'x' : '')}">${escapeHtml(judgeVal || '')}</td>
                            <td style="text-align:center">${escapeHtml(it.group_leader || '')}</td>
                        </tr>
                    `;
                } else {
                    rowsHtml += `
                        <tr>
                            <td>&nbsp;</td><td></td><td></td>
                            <td></td><td></td><td></td><td></td><td></td><td></td>
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>
                    `;
                }
            }

            const tableHtml = `
                <table class="sr-table">
                    <thead>
                        <tr>
                            <th rowspan="2" class="col-tgl">TGL</th>
                            <th rowspan="2" class="col-jam">JAM</th>
                            <th rowspan="2" class="col-masalah">MASALAH</th>
                            <th colspan="6">SUMBER MASALAH</th>
                            <th rowspan="2" class="col-tindakan">TINDAKAN</th>
                            <th rowspan="2" class="col-nurse">NURSE<br>LEADER</th>
                            <th rowspan="2" class="col-status">STATUS</th>
                            <th rowspan="2" class="col-judge">JUDGEMENT</th>
                            <th rowspan="2" class="col-gleader">GROUP<br>LEADER</th>
                        </tr>
                        <tr>
                            <th class="col-sumber sr-sumber-header">PART</th>
                            <th class="col-sumber sr-sumber-header">MACHINE</th>
                            <th class="col-sumber sr-sumber-header">MAN<br>POWER</th>
                            <th class="col-sumber sr-sumber-header">TOOL</th>
                            <th class="col-sumber sr-sumber-header">MATERIAL</th>
                            <th class="col-sumber sr-sumber-header">DLL</th>
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
            `;

            const keterangan = `
                <div class="sr-bottom-left">
                    <div class="sr-keterangan-title">KETERANGAN :</div>
                    <ol>
                        <li>PENDATAAN DIISI / DILAKUKAN OLEH OPERATOR</li>
                        <li>DI CHECK DAN DILAKUKAN TINDAKAN OLEH PETUGAS ( NURSE LEADER, SHIFT LEADER )</li>
                        <li>CLOSE = O</li>
                        <li>OPEN = X</li>
                        <li>UNTUK KOLOM TANDA TANGAN MANAGER DAN GENERAL MANAGER DIISI KETIKA AKHIR BULAN,<br>
                            APABILA DIPERTENGAHAN BULAN KOLOM SUDAH TERISI PENUH MAKA CUKUP DENGAN DI CORET SAJA</li>
                    </ol>
                </div>
            `;

            const komentarText = rec.footer_comment || '';
            const komentarDate = rec.footer_date || '';

            const komentarHtml = `
                <div class="sr-bottom-right">
                    <div class="sr-komentar-box">
                        <div class="sr-komentar-header">KOMENTAR :</div>
                        <div>${escapeHtml(komentarText)}</div>
                    </div>
                    <div class="sr-tanggal-row">
                        <span class="sr-tanggal-label">TANGGAL :</span>
                        <span class="sr-tanggal-value">${komentarDate ? formatDateSlash(komentarDate) : ''}</span>
                    </div>
                    <div class="sr-manager-sig">
                        <div class="sr-manager-sig-box">
                            <div class="sr-sig-header">MANAGER</div>
                            <div class="signature-qrcode" data-type="mgr-${sigKey}-${idx}" data-signature=""></div>
                            <div class="sr-sig-name">&nbsp;</div>
                        </div>
                        <div class="sr-manager-sig-box">
                            <div class="sr-sig-header">GENERAL MANAGER</div>
                            <div class="signature-qrcode" data-type="gm-${sigKey}-${idx}" data-signature=""></div>
                            <div class="sr-sig-name">&nbsp;</div>
                        </div>
                    </div>
                </div>
            `;

            return `
                <div class="sr-page">
                    <div class="sr-top">
                        <div class="sr-top-logo">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Stanley_Black_Decker_logo.svg/320px-Stanley_Black_Decker_logo.svg.png" 
                                alt="Logo" 
                                onerror="this.style.display='none'">
                        </div>
                        <div class="sr-top-title">
                            <div class="sr-main-title">CATATAN FREKUENSI NURSE CALL</div>
                            <div class="sr-sub-title">BULAN / TAHUN : ${escapeHtml(monthLabel)}</div>
                        </div>
                        <div class="sr-top-sig">
                            <div class="sr-sig-date">TANGGAL : ${headerDateLabel}</div>
                            <table>
                                <tr>
                                    <td class="sr-sig-header">DIBUAT</td>
                                    <td class="sr-sig-header">DISETUJUI</td>
                                </tr>
                                <tr>
                                    <td class="sr-sig-body">
                                        <div class="signature-qrcode" data-type="created-${sigKey}-${idx}" data-signature=""></div>
                                        <div class="sr-sig-name">${escapeHtml(sigCreated || '&nbsp;')}</div>
                                    </td>
                                    <td class="sr-sig-body">
                                        <div class="signature-qrcode" data-type="approved-${sigKey}-${idx}" data-signature=""></div>
                                        <div class="sr-sig-name">${escapeHtml(sigApproved || '&nbsp;')}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="sr-info-row">
                        <div class="sr-info-item">
                            <span class="sr-info-label">NO LANE</span>
                            <span class="sr-info-sep">:</span>
                            <span class="sr-info-value">${escapeHtml(rec.no_lane || '-')}</span>
                        </div>
                        <div class="sr-info-item">
                            <span class="sr-info-label">TYPE</span>
                            <span class="sr-info-sep">:</span>
                            <span class="sr-info-value">${escapeHtml(rec.type || '-')}</span>
                        </div>
                    </div>

                    ${tableHtml}

                    <div class="sr-bottom">
                        ${keterangan}
                        ${komentarHtml}
                    </div>

                    <div class="sr-form-code">Q-108-ISE-001-FORM-001-REV.0</div>
                </div>
            `;
        }).join('');

        $('#srReportContainer').html(html);

        // Render QR
        setTimeout(() => {
            list.forEach((entry, idx) => {
                const rec = entry.record;
                const sigCreated  = rec.footer_created_name  || rec.created_by_name  || '';
                const sigApproved = rec.footer_approved_name || rec.approved_by_name || '';

                if (sigCreated) {
                    const el = document.querySelector(`[data-type="created-nc-${idx}"]`);
                    if (el) renderQRCode(el, sigCreated);
                }
                if (sigApproved) {
                    const el = document.querySelector(`[data-type="approved-nc-${idx}"]`);
                    if (el) renderQRCode(el, sigApproved);
                }
            });
        }, 200);
    }

    // ============================================================
    // 4M REPORT — Format "RECORD PERUBAHAN 4 M" (2 kolom)
    // ============================================================
    function renderRecord4mReport(list) {
        const html = list.map((entry, idx) => {
            const rec   = entry.record;
            const items = entry.items || [];
            const sigKey = 'm4';

            // Bulan / Tahun
            let monthLabel = rec.month_year || '-';
            let yearNum = '', monthNum = '';
            if (rec.month_year && rec.month_year.length === 7) {
                const [y, m] = rec.month_year.split('-');
                yearNum = y;
                monthNum = m;
                const monthNames = ['Januari','Februari','Maret','April','Mei','Juni',
                                    'Juli','Agustus','September','Oktober','November','Desember'];
                monthLabel = monthNames[parseInt(m, 10) - 1] + '/' + y;
            }

            const headerDate = rec.date || '';
            const headerDateLabel = headerDate
                ? new Date(headerDate).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })
                : '';

            const sigCreated  = rec.created_by_name  || '';
            const sigApproved = rec.approved_by_name || '';

            // ── Group items by date + shift ──
            const byDate = {};
            items.forEach(it => {
                if (!it.date) return;
                if (!byDate[it.date]) byDate[it.date] = {};
                byDate[it.date][String(it.shift || '1')] = it;
            });

            // Total hari dalam bulan
            let totalDays = 31;
            if (yearNum && monthNum) {
                totalDays = new Date(parseInt(yearNum, 10), parseInt(monthNum, 10), 0).getDate();
            }

            // Split: kiri = 1..ceil(half), kanan = ceil(half)+1..totalDays
            const halfPoint = Math.ceil(totalDays / 2);
            const leftDays  = [];
            const rightDays = [];
            for (let d = 1; d <= totalDays; d++) {
                const dateStr = `${yearNum}-${monthNum}-${String(d).padStart(2, '0')}`;
                if (d <= halfPoint) leftDays.push({ day: d, dateStr });
                else                rightDays.push({ day: d, dateStr });
            }

            const build4mRows = (days) => {
                let rows = '';
                days.forEach(({ day, dateStr }) => {
                    const dayData = byDate[dateStr] || {};
                    const shifts = ['1', '2', '3'];
                    const shiftLabels = ['I', 'II', 'III'];

                    shifts.forEach((shift, sIdx) => {
                        const it = dayData[shift];
                        const tglCell = sIdx === 0
                            ? `<td class="tgl-num" rowspan="3">${day}</td>`
                            : '';

                        const problem  = it ? escapeHtml(it.problem || '') : '';
                        const cat      = it ? String(it.category || '').toLowerCase() : '';

                        const manChk      = cat === 'man'      ? '✓' : '&ndash;';
                        const machineChk  = cat === 'machine'  ? '✓' : '&ndash;';
                        const materialChk = cat === 'material' ? '✓' : '&ndash;';
                        const methodeChk  = cat === 'methode'  ? '✓' : '&ndash;';

                        const teamLeader  = it ? escapeHtml(it.team_leader  || '') : '';
                        const groupLeader = it ? escapeHtml(it.group_leader || '') : '';

                        rows += `
                            <tr>
                                ${tglCell}
                                <td>${shiftLabels[sIdx]}</td>
                                <td class="problem-cell">${problem}</td>
                                <td class="check">${manChk}</td>
                                <td class="check">${machineChk}</td>
                                <td class="check">${materialChk}</td>
                                <td class="check">${methodeChk}</td>
                                <td class="tl-cell">${teamLeader}</td>
                                <td class="gl-cell">${groupLeader}</td>
                            </tr>
                        `;
                    });
                });
                return rows;
            };

            const tableHeader = `
                <thead>
                    <tr>
                        <th class="c-tgl">TGL</th>
                        <th class="c-shift">SHIFT</th>
                        <th class="c-prob">MASALAH</th>
                        <th class="c-cat">MAN</th>
                        <th class="c-cat">MACHINE</th>
                        <th class="c-cat">MATERIAL</th>
                        <th class="c-cat">METHODE</th>
                        <th class="c-tl">TEAM<br>LEADER</th>
                        <th class="c-gl">GROUP<br>LEADER</th>
                    </tr>
                </thead>
            `;

            const leftTable = `
                <table class="sr-4m-table">
                    ${tableHeader}
                    <tbody>${build4mRows(leftDays)}</tbody>
                </table>
            `;
            const rightTable = `
                <table class="sr-4m-table">
                    ${tableHeader}
                    <tbody>${build4mRows(rightDays)}</tbody>
                </table>
            `;

            return `
                <div class="sr-page">
                    <!-- TOP HEADER -->
                    <div class="sr-top">
                        <div class="sr-top-logo">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1a/Stanley_Black_Decker_logo.svg/320px-Stanley_Black_Decker_logo.svg.png" 
                                alt="Logo" 
                                onerror="this.style.display='none'">
                        </div>
                        <div class="sr-top-title">
                            <div class="sr-main-title">RECORD PERUBAHAN 4 M</div>
                        </div>
                        <div class="sr-top-sig">
                            <div class="sr-sig-date">TANGGAL : ${headerDateLabel}</div>
                            <table>
                                <tr>
                                    <td class="sr-sig-header">DIBUAT</td>
                                    <td class="sr-sig-header">DISETUJUI</td>
                                </tr>
                                <tr>
                                    <td class="sr-sig-body">
                                        <div class="signature-qrcode" data-type="created-${sigKey}-${idx}" data-signature=""></div>
                                        <div class="sr-sig-name">${escapeHtml(sigCreated || '&nbsp;')}</div>
                                    </td>
                                    <td class="sr-sig-body">
                                        <div class="signature-qrcode" data-type="approved-${sigKey}-${idx}" data-signature=""></div>
                                        <div class="sr-sig-name">${escapeHtml(sigApproved || '&nbsp;')}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- INFO ROW -->
                    <div class="sr-info-row">
                        <div class="sr-info-item">
                            <span class="sr-info-label">NO LANE</span>
                            <span class="sr-info-sep">:</span>
                            <span class="sr-info-value">${escapeHtml(rec.no_lane || '-')}</span>
                        </div>
                        <div class="sr-info-item">
                            <span class="sr-info-label">TYPE</span>
                            <span class="sr-info-sep">:</span>
                            <span class="sr-info-value">${escapeHtml(rec.type || '-')}</span>
                        </div>
                        <div class="sr-info-item">
                            <span class="sr-info-label">BULAN / TAHUN</span>
                            <span class="sr-info-sep">:</span>
                            <span class="sr-info-value">${escapeHtml(monthLabel)}</span>
                        </div>
                    </div>

                    <!-- 2 COLUMN TABLE -->
                    <div class="sr-4m-grid">
                        ${leftTable}
                        ${rightTable}
                    </div>

                    <div class="sr-form-code">Q-108-ISE-001-FORM-002-REV.0</div>
                </div>
            `;
        }).join('');

        $('#srReportContainer').html(html);

        // Render QR
        setTimeout(() => {
            list.forEach((entry, idx) => {
                const rec = entry.record;
                const sigCreated  = rec.created_by_name  || '';
                const sigApproved = rec.approved_by_name || '';

                if (sigCreated) {
                    const el = document.querySelector(`[data-type="created-m4-${idx}"]`);
                    if (el) renderQRCode(el, sigCreated);
                }
                if (sigApproved) {
                    const el = document.querySelector(`[data-type="approved-m4-${idx}"]`);
                    if (el) renderQRCode(el, sigApproved);
                }
            });
        }, 200);
    }

    // Helper: format tanggal ke "2026/10/05"
    function formatDateSlash(d) {
        if (!d) return '';
        const date = new Date(d);
        if (isNaN(date.getTime())) return d;
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        return `${y}/${m}/${dd}`;
    }

    // ═══════════════════════════════════════════════════════
    // PRINT
    // ═══════════════════════════════════════════════════════
    function printSupportReport() {
        if (!srReportData || srReportData.length === 0) {
            Swal.fire('Tidak ada data', 'Tampilkan report dulu sebelum print', 'warning');
            return;
        }
        document.body.classList.add('print-support-report');
        setTimeout(function () {
            window.print();
            setTimeout(function () {
                document.body.classList.remove('print-support-report');
            }, 800);
        }, 150);
    }

    // ═══════════════════════════════════════════════════════
    // EXPORT PDF
    // ═══════════════════════════════════════════════════════
    async function exportSupportPDF() {
        if (!srReportData || srReportData.length === 0) {
            showToast('Tidak ada data untuk di-export', 'warning');
            return;
        }

        const original = document.getElementById('srReportContainer');
        if (!original) return;

        // Convert QR canvas to img (biar aman saat di-render html2canvas)
        const qrCanvases = original.querySelectorAll('.signature-qrcode canvas');
        qrCanvases.forEach(canvas => {
            try {
                const dataURL = canvas.toDataURL('image/png');
                const img = document.createElement('img');
                img.src = dataURL;
                img.style.cssText = 'display:block;width:80px;height:80px;margin:0 auto 6px;';
                canvas.parentNode.replaceChild(img, canvas);
            } catch (e) { console.warn(e); }
        });

        // Wrapper untuk render off-screen
        const wrapper = document.createElement('div');
        wrapper.style.cssText = [
            'position: absolute',
            'left: -99999px',
            'top: 0',
            'width: 1123px',
            'background: #ffffff',
            'padding: 0',
            'margin: 0',
            'z-index: -1',
            'box-sizing: border-box',
        ].join(';');

        const clone = original.cloneNode(true);
        clone.removeAttribute('style');
        clone.style.cssText = 'background:#ffffff; padding:16px; border:none; border-radius:0; width:1123px; box-sizing:border-box;';
        wrapper.appendChild(clone);
        document.body.appendChild(wrapper);

        await new Promise(r => setTimeout(r, 300));

        Swal.fire({
            title: 'Menyiapkan PDF...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        try {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

            const pages = clone.querySelectorAll('.sr-page');
            const pdfWidth = 297;
            const pdfHeight = 210;
            const margin = 1;
            const contentWidth = pdfWidth - margin * 2;
            const contentHeight = pdfHeight - margin * 2;

            let firstPage = true;

            for (const page of pages) {
                const canvas = await html2canvas(page, {
                    scale: 8,
                    useCORS: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    windowWidth: clone.offsetWidth,
                });

                const imgData = canvas.toDataURL('image/jpeg', 0.95);

                const scaleW = contentWidth  / canvas.width;
                const scaleH = contentHeight / canvas.height;
                const scale  = Math.min(scaleW, scaleH);

                const imgW = canvas.width  * scale;
                const imgH = canvas.height * scale;
                const posX = margin + (contentWidth  - imgW) / 2;
                const posY = margin;

                if (!firstPage) pdf.addPage();
                pdf.addImage(imgData, 'JPEG', posX, posY, imgW, imgH);
                firstPage = false;
            }

            const tab = srActiveTab === 'nursecall' ? 'nursecall' : '4m';
            const monthPart = $('#srMonthYear').val() || 'all';
            pdf.save(`laporan-${tab}-${monthPart}.pdf`);

            Swal.close();
            showToast('PDF berhasil diunduh', 'success');

        } catch (err) {
            Swal.close();
            console.error('[exportSupportPDF]', err);
            showAlert('error', 'Gagal Export PDF', err.message);
        } finally {
            if (wrapper.parentNode) wrapper.parentNode.removeChild(wrapper);
        }
    }

    // ═══════════════════════════════════════════════════════
    // LANE FILTER
    // ═══════════════════════════════════════════════════════
    function populateLaneFilter() {
        var url = ACTIVE_TYPE === 'nursecall' ? '/support/nursecall/lanes' : '/support/4m/lanes';
        api(url).then(function (lanes) {
            var $sel = $('#srNoLane');
            var current = $sel.val();
            $sel.empty().append('<option value="">Semua Lane</option>');
            (Array.isArray(lanes) ? lanes : []).forEach(function (l) {
                $sel.append('<option value="' + escapeHtml(l) + '">' + escapeHtml(l) + '</option>');
            });
            if (current) $sel.val(current);
        }).catch(function () {});
    }

    // ═══════════════════════════════════════════════════════
    // POST MESSAGE ke parent (kalau di-embed)
    // ═══════════════════════════════════════════════════════
    function sendReportSummaryToParent(list) {
        if (window.parent === window) return;
        var totalRecords = list.length;
        var totalItems = list.reduce(function (sum, e) {
            return sum + (e.items ? e.items.length : 0);
        }, 0);
        try {
            window.parent.postMessage({
                type: 'support-report-summary',
                tab: ACTIVE_TYPE,
                total_records: totalRecords,
                total_items: total_items,
                month_year: $('#srMonthYear').val() || '',
            }, '*');
        } catch (e) {}
    }

    // ═══════════════════════════════════════════════════════
    // INIT
    // ═══════════════════════════════════════════════════════
    $(document).ready(function () {
        // Default bulan sekarang
        var today = new Date();
        var ym = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0');
        $('#srMonthYear').val(ym);

        populateLaneFilter();

        // Auto load
        loadSupportReport();
    });
</script>

</body>
</html>