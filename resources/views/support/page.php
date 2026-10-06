<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> — PDCA</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* ══════ BASE ══════ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: #f9fafb;
            color: #111827;
            line-height: 1.5;
            min-height: 100vh;
        }

        /* ══════ HEADER ══════ */
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
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .logo {
            width: 38px; height: 38px;
            background: white;
            color: #1e40af;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 18px;
            flex-shrink: 0;
        }
        .header-title { font-size: 16px; font-weight: 600; color: white; }
        .header-subtitle { font-size: 11px; opacity: 0.85; }
        .header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

        .user-info {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            font-size: 13px;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .user-avatar {
            width: 26px; height: 26px;
            background: white;
            color: #1e40af;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 700;
        }
        .user-role-badge {
            font-size: 10px; font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            background: rgba(255,255,255,0.25);
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .header-btn {
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: inherit;
            transition: all 0.15s;
        }
        .header-btn:hover { background: rgba(255,255,255,0.25); color: white; }

        /* ══════ CONTAINER ══════ */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 24px;
        }

        /* ══════ TABS ══════ */
        .support-tabs {
            display: flex;
            gap: 4px;
            background: white;
            padding: 6px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .support-tab {
            flex: 1;
            padding: 10px 16px;
            border: none;
            background: transparent;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            color: #6b7280;
            display: flex; align-items: center; justify-content: center;
            gap: 6px;
            font-family: inherit;
            transition: all 0.15s;
            text-decoration: none;
        }
        .support-tab:hover { background: #f3f4f6; }
        .support-tab.active { background: #2563eb; color: white; }

        /* ══════ FORM SECTION ══════ */
        .support-form { display: none; }
        .support-form.active { display: block; }

        .form-section {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .form-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 14px;
            display: flex; align-items: center; gap: 6px;
            justify-content: space-between;
        }
        .form-section-title > span:first-child { display: flex; align-items: center; gap: 6px; }

        /* ══════ FORM GRID ══════ */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px;
        }
        .form-group { margin-bottom: 0; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-label {
            display: block; margin-bottom: 5px;
            font-size: 12px; font-weight: 600; color: #4b5563;
        }
        .form-label.required::after { content: ' *'; color: #dc2626; }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 7px 11px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 13px;
            font-family: inherit;
            background: white;
            color: #111827;
            transition: all 0.15s;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-textarea { resize: vertical; min-height: 60px; }

        /* ══════ REFERENCE SECTION ══════ */
        .reference-section {
            background: #eff6ff;
            border-color: #bfdbfe;
        }
        .reference-section .form-section-title { border-bottom-color: #bfdbfe; }

        /* ══════ DYNAMIC TABLE ══════ */
        .dynamic-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .dynamic-table th {
            background: #f3f4f6;
            color: #374151;
            padding: 7px 5px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }
        .dynamic-table td {
            padding: 3px;
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }
        .dynamic-table input, .dynamic-table select, .dynamic-table textarea {
            width: 100%;
            padding: 5px 7px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            font-size: 12px;
            font-family: inherit;
            background: white;
        }
        .dynamic-table input:focus, .dynamic-table select:focus, .dynamic-table textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.1);
        }
        .dynamic-table textarea { resize: vertical; min-height: 34px; line-height: 1.3; }
        .dynamic-table .row-num {
            text-align: center;
            font-weight: 600;
            color: #6b7280;
            background: #f9fafb;
            font-size: 12px;
        }
        .btn-remove-row {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            width: 26px; height: 26px;
            border-radius: 4px;
            cursor: pointer;
            padding: 0;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto;
        }
        .btn-remove-row:hover { background: #dc2626; color: white; }

        /* ══════ BUTTONS ══════ */
        .btn {
            padding: 8px 16px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: white;
            color: #374151;
            font-family: inherit;
            transition: all 0.15s;
            text-decoration: none;
        }
        .btn:hover { background: #f9fafb; border-color: #9ca3af; }
        .btn-primary { background: #2563eb; border-color: #2563eb; color: white; }
        .btn-primary:hover { background: #1e40af; border-color: #1e40af; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ══════ MODE BADGE ══════ */
        .record-mode-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }
        .mode-empty { background: #f3f4f6; color: #6b7280; }
        .mode-new { background: #dbeafe; color: #1e40af; }
        .mode-new-month { background: #fef3c7; color: #92400e; }
        .mode-edit { background: #fef3c7; color: #92400e; }

        /* ══════ EMPTY STATE ══════ */
        .empty-row td {
            text-align: center;
            padding: 30px !important;
            color: #9ca3af;
            background: #f9fafb;
        }
        .empty-row .ti { font-size: 32px; opacity: 0.5; display: block; margin-bottom: 8px; }

        /* ══════ SELECT2 THEME ══════ */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            background: white;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 11px;
            font-size: 13px;
            color: #111827;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-dropdown { z-index: 10000 !important; }
        .select2-results__options { max-height: 320px !important; overflow-y: auto !important; }

        /* ══════ EMBEDDED MODE ══════ */
        body.embedded .header { display: none !important; }
        body.embedded .container { padding-top: 12px; }

        /* ══════ ACTION BAR ══════ */
        .action-bar {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            position: sticky;
            bottom: 0;
            padding: 12px 20px;
            background: white;
            border-top: 1px solid #e5e7eb;
            margin: 0 -24px -20px;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.04);
        }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .header-content { flex-direction: column; align-items: flex-start; }
            .container { padding: 12px; }
            .action-bar { margin: 0 -12px -12px; }
        }

        /* ══════ LOGIN SCREEN ══════ */
        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            padding: 32px 28px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .login-logo {
            width: 56px;
            height: 56px;
            background: #2563eb;
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 26px;
            margin: 0 auto 16px;
        }
        .login-title {
            font-size: 22px;
            font-weight: 700;
            color: #111827;
            text-align: center;
            margin-bottom: 4px;
        }
        .login-subtitle {
            font-size: 13px;
            color: #6b7280;
            text-align: center;
            margin-bottom: 24px;
            line-height: 1.5;
        }
        .login-note {
            font-size: 11px;
            color: #9ca3af;
            text-align: center;
            margin-top: 16px;
            padding-top: 14px;
            border-top: 1px dashed #e5e7eb;
        }
    </style>
</head>
<body class="<?= !empty($embedded) ? 'embedded' : '' ?>">

<?php if (empty($isLoggedIn)): ?>
    <!-- ═══════════════ LOGIN SCREEN ═══════════════ -->
    <div class="login-wrap" id="loginScreen">
        <div class="login-card">
            <div class="login-logo">P</div>
            <h2 class="login-title">PDCA Support</h2>
            <p class="login-subtitle">
                Login untuk mengakses<br>
                <?= htmlspecialchars($title) ?>
            </p>

            <form id="loginForm" onsubmit="event.preventDefault(); doLogin();">
                <div class="form-group" style="margin-bottom:14px">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-input" id="loginUsername" required autofocus autocomplete="username">
                </div>
                <div class="form-group" style="margin-bottom:18px">
                    <label class="form-label">Password</label>
                    <div style="position:relative">
                        <input type="password" class="form-input" id="loginPassword" required autocomplete="current-password" style="padding-right:40px">
                        <button type="button" onclick="togglePw(this)" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px">
                            <i class="ti ti-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px">
                    <i class="ti ti-login"></i> Login
                </button>
            </form>

            <div class="login-note">
                <i class="ti ti-info-circle"></i> Hanya user terdaftar di PDCA yang bisa login
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ═══════════════ SUPPORT FORM ═══════════════ -->

    <header class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo">P</div>
                <div>
                    <div class="header-title"><?= htmlspecialchars($title) ?></div>
                    <div class="header-subtitle">PDCA Support — Continuous Improvement</div>
                </div>
            </div>
            <div class="header-actions">
                <div class="user-info">
                    <div class="user-avatar"><?= strtoupper(substr($userName, 0, 1)) ?></div>
                    <span><?= htmlspecialchars($userName) ?></span>
                    <span class="user-role-badge"><?= htmlspecialchars(str_replace('_', ' ', $userRole)) ?></span>
                </div>
                <a href="<?= env('APP_URL', '/') ?>" class="header-btn">
                    <i class="ti ti-home"></i> Dashboard
                </a>
                <button type="button" class="header-btn" onclick="doLogout()">
                    <i class="ti ti-logout"></i> Logout
                </button>
            </div>
        </div>
    </header>

    <div class="container">

        <!-- TABS -->
        <div class="support-tabs">
            <a href="/support/page/nursecall" class="support-tab <?= $type === 'nursecall' ? 'active' : '' ?>">
                <i class="ti ti-bell"></i> Nursecall
            </a>
            <a href="/support/page/4m" class="support-tab <?= $type === '4m' ? 'active' : '' ?>">
                <i class="ti ti-notebook"></i> Record Perubahan 4M
            </a>
        </div>

        <!-- ═══════════════ FORM NURSECALL ═══════════════ -->
        <form id="nursecallForm" class="support-form <?= $type === 'nursecall' ? 'active' : '' ?>" style="<?= $type !== 'nursecall' ? 'display:none' : '' ?>">

            <!-- Header -->
            <div class="form-section">
                <div class="form-section-title">
                    <span><i class="ti ti-file-info"></i> Header</span>
                    <button type="button" class="btn btn-sm" onclick="resetHeader('nc')">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                </div>
                <div id="ncModeIndicator" style="margin-bottom:10px"></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">No Lane</label>
                        <select class="form-select" id="ncNoLane" required>
                            <option value="">-- Ketik atau pilih lane --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Type</label>
                        <input type="text" class="form-input" id="ncType" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Bulan / Tahun</label>
                        <input type="month" class="form-input" id="ncMonthYear" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Tanggal Dibuat</label>
                        <input type="date" class="form-input" id="ncDateCreated" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">TTD Dibuat — Nama</label>
                        <input type="text" class="form-input" id="ncCreatedName">
                    </div>
                    <div class="form-group">
                        <label class="form-label">TTD Disetujui — Nama</label>
                        <input type="text" class="form-input" id="ncApprovedName">
                    </div>
                </div>
            </div>

            <!-- Reference -->
            <div class="form-section reference-section">
                <div class="form-section-title">
                    <span><i class="ti ti-database-import"></i> Reference Data PDCA</span>
                </div>
                <select class="form-select" id="ncReferenceTask" onchange="applyNursecallReference(this.value)">
                    <option value="">-- Pilih No Lane dulu --</option>
                </select>
                <small style="color:#6b7280;font-size:11px;display:block;margin-top:6px">
                    <i class="ti ti-info-circle"></i> Pilih POST IT → 1 baris di Content. Pilih ulang untuk update.
                </small>
            </div>

            <!-- Content -->
            <div class="form-section">
                <div class="form-section-title">
                    <span><i class="ti ti-list"></i> Content</span>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addNursecallRow()">
                        <i class="ti ti-plus"></i> Tambah Baris
                    </button>
                </div>
                <div style="overflow-x:auto">
                    <table class="dynamic-table" id="ncContentTable">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th style="width:120px">Tanggal</th>
                                <th style="width:85px">Jam</th>
                                <th style="min-width:170px">Masalah</th>
                                <th style="width:120px">Sumber</th>
                                <th style="min-width:170px">Tindakan</th>
                                <th style="width:120px">Nurse Leader</th>
                                <th style="width:65px">Status</th>
                                <th style="width:80px">Judgement</th>
                                <th style="width:120px">Group Leader</th>
                                <th style="width:36px"></th>
                            </tr>
                        </thead>
                        <tbody id="ncContentBody"></tbody>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="form-section">
                <div class="form-section-title">
                    <span><i class="ti ti-signature"></i> Footer</span>
                </div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Komentar</label>
                        <textarea class="form-textarea" id="ncComment" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tanggal</label>
                        <input type="date" class="form-input" id="ncFooterDate">
                    </div>
                    <div class="form-group">
                        <label class="form-label">TTD Dibuat — Nama</label>
                        <input type="text" class="form-input" id="ncFooterCreatedName">
                    </div>
                    <div class="form-group">
                        <label class="form-label">TTD Disetujui — Nama</label>
                        <input type="text" class="form-input" id="ncFooterApprovedName">
                    </div>
                </div>
            </div>

            <div class="action-bar">
                <button type="button" class="btn" onclick="resetHeader('nc')">
                    <i class="ti ti-x"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy"></i> Simpan Nursecall
                </button>
            </div>
        </form>

        <!-- ═══════════════ FORM 4M ═══════════════ -->
        <form id="record4mForm" class="support-form <?= $type === '4m' ? 'active' : '' ?>" style="<?= $type !== '4m' ? 'display:none' : '' ?>">

            <!-- Header -->
            <div class="form-section">
                <div class="form-section-title">
                    <span><i class="ti ti-file-info"></i> Header</span>
                    <button type="button" class="btn btn-sm" onclick="resetHeader('m4')">
                        <i class="ti ti-refresh"></i> Reset
                    </button>
                </div>
                <div id="m4ModeIndicator" style="margin-bottom:10px"></div>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label required">No Lane</label>
                        <select class="form-select" id="m4NoLane" required>
                            <option value="">-- Ketik atau pilih lane --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Type</label>
                        <input type="text" class="form-input" id="m4Type" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Bulan / Tahun</label>
                        <input type="month" class="form-input" id="m4MonthYear" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Tanggal</label>
                        <input type="date" class="form-input" id="m4Date" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">TTD Dibuat — Nama</label>
                        <input type="text" class="form-input" id="m4CreatedName">
                    </div>
                    <div class="form-group">
                        <label class="form-label">TTD Disetujui — Nama</label>
                        <input type="text" class="form-input" id="m4ApprovedName">
                    </div>
                </div>
            </div>

            <!-- Reference -->
            <div class="form-section reference-section">
                <div class="form-section-title">
                    <span><i class="ti ti-database-import"></i> Reference Data PDCA</span>
                </div>
                <select class="form-select" id="m4ReferenceTask" onchange="applyRecord4mReference(this.value)">
                    <option value="">-- Pilih No Lane dulu --</option>
                </select>
                <small style="color:#6b7280;font-size:11px;display:block;margin-top:6px">
                    <i class="ti ti-info-circle"></i> Pilih POST IT → 1 baris di Content. Pilih ulang untuk update.
                </small>
            </div>

            <!-- Content -->
            <div class="form-section">
                <div class="form-section-title">
                    <span><i class="ti ti-list"></i> Content</span>
                    <button type="button" class="btn btn-sm btn-primary" onclick="addRecord4mRow()">
                        <i class="ti ti-plus"></i> Tambah Baris
                    </button>
                </div>
                <div style="overflow-x:auto">
                    <table class="dynamic-table" id="m4ContentTable">
                        <thead>
                            <tr>
                                <th style="width:36px">#</th>
                                <th style="width:130px">Tanggal</th>
                                <th style="width:80px">Shift</th>
                                <th style="min-width:220px">Masalah</th>
                                <th style="width:130px">Category</th>
                                <th style="width:160px">Team Leader</th>
                                <th style="width:160px">Group Leader</th>
                                <th style="width:36px"></th>
                            </tr>
                        </thead>
                        <tbody id="m4ContentBody"></tbody>
                    </table>
                </div>
            </div>

            <div class="action-bar">
                <button type="button" class="btn" onclick="resetHeader('m4')">
                    <i class="ti ti-x"></i> Reset
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy"></i> Simpan Record 4M
                </button>
            </div>
        </form>

    </div>
<?php endif; ?>

<!-- ═══════════════ SCRIPTS ═══════════════ -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // ═══════════════════════════════════════════════════════
    // CONFIG
    // ═══════════════════════════════════════════════════════
    window.CSRF_TOKEN  = '<?= csrfHeader() ?>';
    window.CSRF_HEADER = 'X-CSRF-TOKEN';
    $.ajaxSetup({ headers: { [window.CSRF_HEADER]: window.CSRF_TOKEN } });

    var API_BASE     = '<?= url() ?>';
    var ACTIVE_TYPE  = '<?= $type ?>';
    var IS_LOGGED_IN = <?= !empty($isLoggedIn) ? 'true' : 'false' ?>;

    var state = {
        currentUser: <?= json_encode(['id' => $user->id ?? 0, 'name' => $userName, 'role' => $userRole]) ?>,
    };

    var ncExistingRecordId = null;
    var m4ExistingRecordId = null;
    var laneSelect2Inited = false;
    var laneCache = null;

    // ═══════════════════════════════════════════════════════
    // API HELPER — GLOBAL (dipakai login & form)
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
    // LOGIN / LOGOUT
    // ═══════════════════════════════════════════════════════
    function togglePw(btn) {
        var $input = $(btn).prev('input');
        var isPw = $input.attr('type') === 'password';
        $input.attr('type', isPw ? 'text' : 'password');
        $(btn).find('i').attr('class', isPw ? 'ti ti-eye-off' : 'ti ti-eye');
    }

    function doLogin() {
        var username = $('#loginUsername').val().trim();
        var password = $('#loginPassword').val();

        if (!username || !password) {
            Swal.fire('Form Tidak Lengkap', 'Username dan password wajib diisi', 'warning');
            return;
        }

        Swal.fire({
            title: 'Login...',
            allowOutsideClick: false,
            didOpen: function () { Swal.showLoading(); },
        });

        api('/auth/login', {
            method: 'POST',
            body: { username: username, password: password },
        }).then(function () {
            Swal.close();
            location.reload();
        }).catch(function (err) {
            Swal.close();
            Swal.fire('Login Gagal', err.message, 'error');
        });
    }

    function doLogout() {
        Swal.fire({
            icon: 'warning',
            title: 'Logout?',
            text: 'Anda yakin ingin keluar?',
            showCancelButton: true,
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            reverseButtons: true,
        }).then(function (r) {
            if (!r.isConfirmed) return;
            api('/auth/logout', { method: 'POST' })
                .catch(function () {})
                .then(function () { location.reload(); });
        });
    }

    // ═══════════════════════════════════════════════════════
    // FORM HELPERS — hanya saat login
    // ═══════════════════════════════════════════════════════
    if (IS_LOGGED_IN) {

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

        function toast(msg, icon) {
            Swal.fire({ toast: true, position: 'top-end', icon: icon || 'success', title: msg, showConfirmButton: false, timer: 3000 });
        }

        function showAlert(icon, title, html) {
            Swal.fire({ icon: icon, title: title, html: html, confirmButtonColor: '#2563eb' });
        }

        // ── Select2 ──
        function setSelect2Value($sel, value, text) {
            if (!value) { $sel.val(null).trigger('change'); return; }
            if (!$sel.find("option[value='" + String(value).replace(/'/g, "\\'") + "']").length) {
                var newOpt = new Option(text || value, value, true, true);
                $sel.append(newOpt);
            }
            $sel.val(value).trigger('change');
        }

        function initLaneSelect2() {
            if (laneSelect2Inited || typeof $.fn.select2 === 'undefined') return;

            var opts = {
                placeholder: '-- Ketik atau pilih lane --',
                allowClear: true,
                width: '100%',
                tags: true,
                tokenSeparators: [],
                language: {
                    noResults: function () { return 'Tidak ada lane. Ketik lalu Enter untuk buat baru.'; },
                },
                createTag: function (params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return { id: term, text: term + ' (baru)', newTag: true };
                },
            };

            $('#ncNoLane, #m4NoLane').select2(opts);

            $('#ncNoLane').on('select2:select', function () {
                var v = $(this).val();
                if (!v) return;
                clearFormExceptLane('nc');
                setTimeout(function () { checkByLane('nc', v); }, 100);
            });
            $('#m4NoLane').on('select2:select', function () {
                var v = $(this).val();
                if (!v) return;
                clearFormExceptLane('m4');
                setTimeout(function () { checkByLane('m4', v); }, 100);
            });

            $('#ncNoLane').on('select2:clear', function () {
                clearFormExceptLane('nc');
                updateModeBadge('nc', 'empty');
            });
            $('#m4NoLane').on('select2:clear', function () {
                clearFormExceptLane('m4');
                updateModeBadge('m4', 'empty');
            });

            laneSelect2Inited = true;
        }

        function populateLaneOptions() {
            var url = ACTIVE_TYPE === 'nursecall' ? '/support/nursecall/lanes' : '/support/4m/lanes';
            api(url).then(function (lanes) {
                laneCache = (Array.isArray(lanes) ? lanes : []).sort();
                ['#ncNoLane', '#m4NoLane'].forEach(function (sel) {
                    var $sel = $(sel);
                    var current = $sel.val();
                    $sel.empty().append('<option value="">-- Ketik atau pilih lane --</option>');
                    laneCache.forEach(function (l) {
                        $sel.append('<option value="' + escapeHtml(l) + '">' + escapeHtml(l) + '</option>');
                    });
                    if (current) $sel.val(current).trigger('change.select2');
                });
            }).catch(function () {});
        }

        // ── Mode Badge ──
        function updateModeBadge(which, mode, extra) {
            var id = which === 'nc' ? '#ncModeIndicator' : '#m4ModeIndicator';
            var html = '';
            if (mode === 'empty') {
                html = '<span class="record-mode-badge mode-empty"><i class="ti ti-info-circle"></i> Pilih / ketik No Lane dulu</span>';
            } else if (mode === 'new-lane') {
                html = '<span class="record-mode-badge mode-new"><i class="ti ti-plus"></i> Lane baru — silakan isi header</span>';
            } else if (mode === 'new-month') {
                html = '<span class="record-mode-badge mode-new-month"><i class="ti ti-calendar-plus"></i> Lane existing (' + escapeHtml(extra || '') + ') — record baru</span>';
            } else if (mode === 'edit') {
                html = '<span class="record-mode-badge mode-edit"><i class="ti ti-edit"></i> Melanjutkan record (' + (extra || 0) + ' baris)</span>';
            }
            $(id).html(html);
        }

        // ── Clear Form ──
        function clearFormExceptLane(which) {
            var today = new Date().toISOString().split('T')[0];
            var currentMonth = today.slice(0, 7);
            var userName = state.currentUser && state.currentUser.name || '';

            if (which === 'nc') {
                var currentLane = $('#ncNoLane').val();
                $('#ncType').val('');
                $('#ncMonthYear').val(currentMonth);
                $('#ncDateCreated').val(today);
                $('#ncCreatedName').val(userName);
                $('#ncApprovedName').val('');
                $('#ncComment').val('');
                $('#ncFooterDate').val(today);
                $('#ncFooterCreatedName').val(userName);
                $('#ncFooterApprovedName').val('');
                ncExistingRecordId = null;
                document.getElementById('ncContentBody').innerHTML = '';
                renderEmptyState('nc');
                if (currentLane) setSelect2Value($('#ncNoLane'), currentLane, currentLane);
            } else {
                var currentLaneM4 = $('#m4NoLane').val();
                $('#m4Type').val('');
                $('#m4MonthYear').val(currentMonth);
                $('#m4Date').val(today);
                $('#m4CreatedName').val(userName);
                $('#m4ApprovedName').val('');
                m4ExistingRecordId = null;
                document.getElementById('m4ContentBody').innerHTML = '';
                renderEmptyState('m4');
                if (currentLaneM4) setSelect2Value($('#m4NoLane'), currentLaneM4, currentLaneM4);
            }
        }

        // ── Empty State ──
        function renderEmptyState(which) {
            var tbodyId = which === 'nc' ? 'ncContentBody' : 'm4ContentBody';
            var colspan = which === 'nc' ? 11 : 8;
            document.getElementById(tbodyId).innerHTML =
                '<tr class="empty-row"><td colspan="' + colspan + '">' +
                    '<i class="ti ti-inbox"></i>' +
                    'Pilih Reference Data di atas atau klik "+ Tambah Baris"' +
                '</td></tr>';
        }
        function removeEmptyState(which) {
            var tbodyId = which === 'nc' ? 'ncContentBody' : 'm4ContentBody';
            $('#' + tbodyId + ' .empty-row').remove();
        }

        // ── Dynamic Rows ──
        function addNursecallRow() {
            removeEmptyState('nc');
            var today = new Date().toISOString().split('T')[0];
            var now = new Date();
            var t = String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="row-num"></td>' +
                '<td><input type="date" class="nc-date" value="' + today + '"></td>' +
                '<td><input type="time" class="nc-time" value="' + t + '"></td>' +
                '<td><textarea class="nc-problem" rows="1"></textarea></td>' +
                '<td><select class="nc-source">' +
                    '<option value="">-</option>' +
                    '<option value="part">Part</option>' +
                    '<option value="machine">Machine</option>' +
                    '<option value="man_power">Man Power</option>' +
                    '<option value="tool">Tool</option>' +
                    '<option value="material">Material</option>' +
                    '<option value="dll">DLL</option>' +
                '</select></td>' +
                '<td><textarea class="nc-action" rows="1"></textarea></td>' +
                '<td><input type="text" class="nc-nurse-leader"></td>' +
                '<td><select class="nc-status"><option value="">-</option><option value="O">O</option><option value="X">X</option></select></td>' +
                '<td><select class="nc-judgement"><option value="">-</option><option value="O">O</option><option value="X">X</option></select></td>' +
                '<td><input type="text" class="nc-group-leader"></td>' +
                '<td><button type="button" class="btn-remove-row" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>';
            document.getElementById('ncContentBody').appendChild(tr);
            renumberRows('nc');
        }

        function addRecord4mRow() {
            removeEmptyState('m4');
            var today = new Date().toISOString().split('T')[0];
            var tr = document.createElement('tr');
            tr.innerHTML =
                '<td class="row-num"></td>' +
                '<td><input type="date" class="m4-date" value="' + today + '"></td>' +
                '<td><select class="m4-shift"><option value="1">1</option><option value="2">2</option><option value="3">3</option></select></td>' +
                '<td><textarea class="m4-problem" rows="1"></textarea></td>' +
                '<td><select class="m4-category">' +
                    '<option value="">-</option>' +
                    '<option value="man">Man</option>' +
                    '<option value="machine">Machine</option>' +
                    '<option value="material">Material</option>' +
                    '<option value="methode">Methode</option>' +
                '</select></td>' +
                '<td><input type="text" class="m4-team-leader"></td>' +
                '<td><input type="text" class="m4-group-leader"></td>' +
                '<td><button type="button" class="btn-remove-row" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>';
            document.getElementById('m4ContentBody').appendChild(tr);
            renumberRows('m4');
        }

        function removeRow(btn) {
            var tr = btn.closest('tr');
            var tbody = tr.parentNode;
            if (tbody.querySelectorAll('tr:not(.empty-row)').length <= 1) {
                toast('Minimal 1 baris', 'warning');
                return;
            }
            tr.remove();
            renumberRows(tbody.id === 'ncContentBody' ? 'nc' : 'm4');
        }

        function renumberRows(which) {
            var tbody = document.getElementById(which === 'nc' ? 'ncContentBody' : 'm4ContentBody');
            Array.from(tbody.children).forEach(function (tr, i) {
                var el = tr.querySelector('.row-num');
                if (el) el.textContent = i + 1;
            });
        }

        // ── Reference Tasks ──
        function populateReferenceSelects(which) {
            if (!which) {
                populateReferenceSelects('nc');
                populateReferenceSelects('m4');
                return;
            }

            var isNc = which === 'nc';
            var noLane    = isNc ? $('#ncNoLane').val()    : $('#m4NoLane').val();
            var monthYear = isNc ? $('#ncMonthYear').val() : $('#m4MonthYear').val();
            var docType   = isNc ? 'nursecall' : '4m';
            var $select   = isNc ? $('#ncReferenceTask') : $('#m4ReferenceTask');

            if (!$select.length) return;

            if (!noLane) {
                $select.html('<option value="">-- Pilih No Lane dulu --</option>');
                return;
            }

            $select.html('<option value="">-- Memuat task PDCA... --</option>');

            api('/support/reference-tasks', {
                query: {
                    doc_type:   docType,
                    no_lane:    noLane    || '',
                    month_year: monthYear || '',
                }
            }).then(function (list) {
                list = Array.isArray(list) ? list : (list && list.data || []);

                if (isNc) window._refTasksNc = list;
                else      window._refTasksM4 = list;

                if (list.length === 0) {
                    $select.html('<option value="">-- Tidak ada task tersedia --</option>');
                    return;
                }

                var opts = ['<option value="">-- Pilih POST IT --</option>'];
                list.forEach(function (t) {
                    var short = (t.problem || '').slice(0, 55);
                    var flag  = t.inRecord ? ' [ADA]' : '';
                    opts.push('<option value="' + t.id + '">' +
                        escapeHtml((t.taskCode || '-') + ' · ' + formatDate(t.date) + ' · ' + (t.section || '-') + ' · ' + short + flag) +
                        '</option>');
                });
                $select.html(opts.join(''));
            }).catch(function (err) {
                console.error('[populateReferenceSelects]', which, err);
                $select.html('<option value="">-- Gagal memuat --</option>');
            });
        }

        // ── Apply Reference ──
        function applyNursecallReference(taskId) {
            if (!taskId) return;
            var task = (window._refTasksNc || []).filter(function (t) { return t.id === parseInt(taskId, 10); })[0];
            if (!task) { toast('Task tidak ditemukan', 'warning'); return; }

            var $row = null;
            $('#ncContentBody tr').each(function () {
                if (String($(this).data('task-id')) === String(task.id)) { $row = $(this); return false; }
            });
            var isUpdate = !!$row;
            if (!$row) {
                addNursecallRow();
                $row = $('#ncContentBody tr').last();
                $row.attr('data-task-id', task.id);
            }

            $row.find('.nc-date').val(task.date || '');
            if (task.createdAt) {
                var d = new Date(String(task.createdAt).replace(' ', 'T'));
                if (!isNaN(d.getTime())) {
                    $row.find('.nc-time').val(String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0'));
                }
            }
            $row.find('.nc-problem').val(task.problem || '');
            $row.find('.nc-action').val(task.permAction || task.tempAction || '');
            $row.find('.nc-nurse-leader').val(task.pic || '');
            $row.find('.nc-group-leader').val(task.leaderSignature || '');

            var map = { machine: 'machine', material: 'material', man: 'man_power', methode: 'dll' };
            if (map[task.category]) $row.find('.nc-source').val(map[task.category]);

            $('#ncReferenceTask').val('').trigger('change');
            $row.css('background', isUpdate ? '#fef3c7' : '#f0fdf4');
            setTimeout(function () { $row.css('background', ''); }, 1200);
            toast(isUpdate ? 'Baris di-update' : 'Baris ditambahkan', isUpdate ? 'info' : 'success');
        }

        function applyRecord4mReference(taskId) {
            if (!taskId) return;
            var task = (window._refTasksM4 || []).filter(function (t) { return t.id === parseInt(taskId, 10); })[0];
            if (!task) return;

            var $row = null;
            $('#m4ContentBody tr').each(function () {
                if (String($(this).data('task-id')) === String(task.id)) { $row = $(this); return false; }
            });
            var isUpdate = !!$row;
            if (!$row) {
                addRecord4mRow();
                $row = $('#m4ContentBody tr').last();
                $row.attr('data-task-id', task.id);
            }

            $row.find('.m4-date').val(task.date || '');
            $row.find('.m4-problem').val(task.problem || '');
            if (['man', 'machine', 'material', 'methode'].indexOf(task.category) >= 0) {
                $row.find('.m4-category').val(task.category);
            }
            if (task.createdByName) $row.find('.m4-team-leader').val(task.createdByName);
            if (task.leaderSignature) $row.find('.m4-group-leader').val(task.leaderSignature);

            $('#m4ReferenceTask').val('').trigger('change');
            $row.css('background', isUpdate ? '#fef3c7' : '#f0fdf4');
            setTimeout(function () { $row.css('background', ''); }, 1200);
            toast(isUpdate ? 'Baris di-update' : 'Baris ditambahkan', isUpdate ? 'info' : 'success');
        }

        // ── Check Existing by Lane ──
        function checkByLane(which, noLane) {
            if (!noLane) return;
            var isNc = which === 'nc';
            var monthYear = isNc ? $('#ncMonthYear').val() : $('#m4MonthYear').val();
            var endpoint = isNc ? '/support/nursecall/by-lane' : '/support/4m/by-lane';

            return api(endpoint, { query: { no_lane: noLane, month_year: monthYear || '' } }).then(function (res) {
                if (!res || !res.record) {
                    clearFormExceptLane(which);
                    updateModeBadge(which, 'new-lane');
                    populateReferenceSelects(which);
                    return;
                }
                var rec = res.record;
                var items = res.items || [];
                var sameMonth = !!res.is_same_month;

                if (isNc) {
                    if (rec.type) $('#ncType').val(rec.type);
                    if (rec.created_by_name) $('#ncCreatedName').val(rec.created_by_name);
                    if (rec.approved_by_name) $('#ncApprovedName').val(rec.approved_by_name);
                    if (rec.footer_created_name) $('#ncFooterCreatedName').val(rec.footer_created_name);
                    if (rec.footer_approved_name) $('#ncFooterApprovedName').val(rec.footer_approved_name);
                } else {
                    if (rec.type) $('#m4Type').val(rec.type);
                    if (rec.created_by_name) $('#m4CreatedName').val(rec.created_by_name);
                    if (rec.approved_by_name) $('#m4ApprovedName').val(rec.approved_by_name);
                }

                if (sameMonth) {
                    if (isNc) {
                        if (rec.date_created) $('#ncDateCreated').val(rec.date_created);
                        if (rec.footer_comment) $('#ncComment').val(rec.footer_comment);
                        if (rec.footer_date) $('#ncFooterDate').val(rec.footer_date);
                        renderNursecallItems(items);
                        ncExistingRecordId = rec.id;
                        updateModeBadge('nc', 'edit', items.length);
                    } else {
                        if (rec.date) $('#m4Date').val(rec.date);
                        renderRecord4mItems(items);
                        m4ExistingRecordId = rec.id;
                        updateModeBadge('m4', 'edit', items.length);
                    }
                    toast('Data dimuat — ' + items.length + ' baris', 'info');
                } else {
                    clearFormExceptLane(which);
                    if (isNc && rec.type) $('#ncType').val(rec.type);
                    if (!isNc && rec.type) $('#m4Type').val(rec.type);
                    updateModeBadge(which, 'new-month', rec.month_year);
                    toast('Lane sudah ada di bulan ' + rec.month_year + '. Siap buat record baru.', 'info');
                }

                populateReferenceSelects(which);

            }).catch(function (err) {
                console.warn('[checkByLane]', err);
            });
        }

        function renderNursecallItems(items) {
            document.getElementById('ncContentBody').innerHTML = '';
            if (!items || items.length === 0) { renderEmptyState('nc'); return; }
            items.forEach(function (it) {
                addNursecallRow();
                var $row = $('#ncContentBody tr').last();
                if (it.task_id) $row.attr('data-task-id', it.task_id);
                if (it.date) $row.find('.nc-date').val(it.date);
                if (it.time) $row.find('.nc-time').val(String(it.time).slice(0, 5));
                if (it.problem) $row.find('.nc-problem').val(it.problem);
                if (it.source) $row.find('.nc-source').val(it.source);
                if (it.action) $row.find('.nc-action').val(it.action);
                if (it.nurse_leader) $row.find('.nc-nurse-leader').val(it.nurse_leader);
                if (it.status) $row.find('.nc-status').val(it.status);
                if (it.judgement) $row.find('.nc-judgement').val(it.judgement);
                if (it.group_leader) $row.find('.nc-group-leader').val(it.group_leader);
            });
        }

        function renderRecord4mItems(items) {
            document.getElementById('m4ContentBody').innerHTML = '';
            if (!items || items.length === 0) { renderEmptyState('m4'); return; }
            items.forEach(function (it) {
                addRecord4mRow();
                var $row = $('#m4ContentBody tr').last();
                if (it.task_id) $row.attr('data-task-id', it.task_id);
                if (it.date) $row.find('.m4-date').val(it.date);
                if (it.shift) $row.find('.m4-shift').val(it.shift);
                if (it.problem) $row.find('.m4-problem').val(it.problem);
                if (it.category) $row.find('.m4-category').val(it.category);
                if (it.team_leader) $row.find('.m4-team-leader').val(it.team_leader);
                if (it.group_leader) $row.find('.m4-group-leader').val(it.group_leader);
            });
        }

        // ── Reset ──
        function resetHeader(which) {
            clearFormExceptLane(which);
            setSelect2Value($('#ncNoLane'), '', '');
            setSelect2Value($('#m4NoLane'), '', '');
            updateModeBadge(which, 'empty');
            populateReferenceSelects(which);
            toast('Form direset', 'info');
        }

        // ── Save Nursecall ──
        function saveNursecall(e) {
            if (e) e.preventDefault();

            var header = {
                no_lane: $('#ncNoLane').val() ? $('#ncNoLane').val().trim() : '',
                type: $('#ncType').val().trim(),
                month_year: $('#ncMonthYear').val(),
                date_created: $('#ncDateCreated').val(),
                created_by_name: $('#ncCreatedName').val().trim(),
                approved_by_name: $('#ncApprovedName').val().trim(),
            };
            if (!header.no_lane || !header.type || !header.month_year || !header.date_created) {
                showAlert('warning', 'Header Belum Lengkap', 'Isi No Lane, Type, Bulan/Tahun, dan Tanggal Dibuat.');
                return;
            }

            var items = [];
            $('#ncContentBody tr').each(function () {
                var $t = $(this);
                if ($t.hasClass('empty-row')) return;
                var item = {
                    task_id: $t.data('task-id') || null,
                    date: $t.find('.nc-date').val() || '',
                    time: $t.find('.nc-time').val() || '',
                    problem: $t.find('.nc-problem').val().trim(),
                    source: $t.find('.nc-source').val(),
                    action: $t.find('.nc-action').val().trim(),
                    nurse_leader: $t.find('.nc-nurse-leader').val().trim(),
                    status: $t.find('.nc-status').val(),
                    judgement: $t.find('.nc-judgement').val(),
                    group_leader: $t.find('.nc-group-leader').val().trim(),
                };
                if (item.problem || item.action || item.nurse_leader) items.push(item);
            });
            if (items.length === 0) { showAlert('warning', 'Content Kosong', 'Isi minimal 1 baris.'); return; }

            var footer = {
                comment: $('#ncComment').val().trim(),
                date: $('#ncFooterDate').val(),
                created_by_name: $('#ncFooterCreatedName').val().trim(),
                approved_by_name: $('#ncFooterApprovedName').val().trim(),
            };

            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

            api('/support/nursecall', { method: 'POST', body: Object.assign(header, { items: items, footer: footer }) })
                .then(function (res) {
                    Swal.close();
                    toast(res && res.message || 'Disimpan', 'success');
                    ncExistingRecordId = res && res.record_id || null;
                    populateLaneOptions();
                    populateReferenceSelects('nc');
                })
                .catch(function (err) {
                    Swal.close();
                    showAlert('error', 'Gagal Menyimpan', err.message);
                });
        }

        // ── Save Record 4M ──
        function saveRecord4m(e) {
            if (e) e.preventDefault();

            var header = {
                no_lane: $('#m4NoLane').val() ? $('#m4NoLane').val().trim() : '',
                type: $('#m4Type').val().trim(),
                month_year: $('#m4MonthYear').val(),
                date: $('#m4Date').val(),
                created_by_name: $('#m4CreatedName').val().trim(),
                approved_by_name: $('#m4ApprovedName').val().trim(),
            };
            if (!header.no_lane || !header.type || !header.month_year || !header.date) {
                showAlert('warning', 'Header Belum Lengkap', 'Isi No Lane, Type, Bulan/Tahun, dan Tanggal.');
                return;
            }

            var items = [];
            $('#m4ContentBody tr').each(function () {
                var $t = $(this);
                if ($t.hasClass('empty-row')) return;
                var item = {
                    task_id: $t.data('task-id') || null,
                    date: $t.find('.m4-date').val() || '',
                    shift: $t.find('.m4-shift').val() || '',
                    problem: $t.find('.m4-problem').val().trim(),
                    category: $t.find('.m4-category').val(),
                    team_leader: $t.find('.m4-team-leader').val().trim(),
                    group_leader: $t.find('.m4-group-leader').val().trim(),
                };
                if (item.problem || item.category) items.push(item);
            });
            if (items.length === 0) { showAlert('warning', 'Content Kosong', 'Isi minimal 1 baris.'); return; }

            Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: function () { Swal.showLoading(); } });

            api('/support/4m', { method: 'POST', body: Object.assign(header, { items: items }) })
                .then(function (res) {
                    Swal.close();
                    toast(res && res.message || 'Disimpan', 'success');
                    m4ExistingRecordId = res && res.record_id || null;
                    populateLaneOptions();
                    populateReferenceSelects('m4');
                })
                .catch(function (err) {
                    Swal.close();
                    showAlert('error', 'Gagal Menyimpan', err.message);
                });
        }

        // ── Init Form ──
        $(document).ready(function () {
            initLaneSelect2();
            populateLaneOptions();

            var today = new Date().toISOString().split('T')[0];
            var currentMonth = today.slice(0, 7);
            $('#ncDateCreated').val(today);
            $('#ncFooterDate').val(today);
            $('#ncMonthYear').val(currentMonth);
            $('#m4Date').val(today);
            $('#m4MonthYear').val(currentMonth);

            if (state.currentUser && state.currentUser.name) {
                $('#ncCreatedName').val(state.currentUser.name);
                $('#ncFooterCreatedName').val(state.currentUser.name);
                $('#m4CreatedName').val(state.currentUser.name);
            }

            renderEmptyState('nc');
            renderEmptyState('m4');
            updateModeBadge('nc', 'empty');
            updateModeBadge('m4', 'empty');

            $('#nursecallForm').on('submit', saveNursecall);
            $('#record4mForm').on('submit', saveRecord4m);

            $('#ncMonthYear').on('change', function () {
                if ($('#ncNoLane').val()) populateReferenceSelects('nc');
            });
            $('#m4MonthYear').on('change', function () {
                if ($('#m4NoLane').val()) populateReferenceSelects('m4');
            });
        });
    }

    // ═══════════════════════════════════════════════════════
    // EXPOSE GLOBAL — wajib supaya onclick HTML bisa akses
    // ═══════════════════════════════════════════════════════
    window.doLogin     = doLogin;
    window.doLogout    = doLogout;
    window.togglePw    = togglePw;

    if (IS_LOGGED_IN) {
        window.setSelect2Value           = setSelect2Value;
        window.addNursecallRow           = addNursecallRow;
        window.addRecord4mRow            = addRecord4mRow;
        window.removeRow                 = removeRow;
        window.resetHeader               = resetHeader;
        window.applyNursecallReference   = applyNursecallReference;
        window.applyRecord4mReference    = applyRecord4mReference;
    }
</script>

</body>
</html>