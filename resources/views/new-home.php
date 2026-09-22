<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDCA Kanban Board - Task Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #eff6ff;
            --primary-dark: #1e40af;
            --success: #16a34a;
            --success-light: #f0fdf4;
            --warning: #d97706;
            --warning-light: #fffbeb;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.5;
            min-height: 100vh;
        }

        .header {
            background: white;
            border-bottom: 1px solid var(--gray-200);
            padding: 16px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }
        .header-left { display: flex; align-items: center; gap: 12px; }
        .logo {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: bold; font-size: 18px;
        }
        .header-title { font-size: 20px; font-weight: 600; color: var(--gray-800); }
        .header-subtitle { font-size: 12px; color: var(--gray-500); }
        .header-actions { display: flex; gap: 8px; align-items: center; }
        .user-info {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 12px; background: var(--gray-100);
            border-radius: var(--radius-sm); font-size: 13px; color: var(--gray-700);
        }
        .user-avatar {
            width: 28px; height: 28px; background: var(--primary); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 12px; font-weight: 600;
        }

        .btn {
            padding: 8px 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            cursor: pointer; font-weight: 500; font-size: 14px;
            transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 6px;
            background: white; color: var(--gray-700); white-space: nowrap;
        }
        .btn:hover { background: var(--gray-50); border-color: var(--gray-400); }
        .btn-primary { background: var(--primary); border-color: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-success { background: var(--success); border-color: var(--success); color: white; }
        .btn-success:hover { background: #15803d; border-color: #15803d; }
        .btn-danger { background: var(--danger); border-color: var(--danger); color: white; }
        .btn-danger:hover { background: #b91c1c; border-color: #b91c1c; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; }

        .container { max-width: 1400px; margin: 0 auto; padding: 24px; }

        .notice-bar {
            background: var(--primary-light);
            border: 1px solid #bfdbfe;
            border-radius: var(--radius);
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 12px;
            font-size: 13px; color: var(--primary-dark);
        }
        .notice-bar.warning { background: var(--warning-light); border-color: #fde68a; color: #92400e; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px; margin-bottom: 24px;
        }
        .stat-card {
            background: white; border: 1px solid var(--gray-200);
            border-radius: var(--radius); padding: 16px;
            box-shadow: var(--shadow-sm); transition: all 0.2s;
        }
        .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .stat-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray-500); }
        .stat-icon {
            width: 32px; height: 32px; border-radius: var(--radius-xs);
            display: flex; align-items: center; justify-content: center; font-size: 16px;
        }
        .stat-value { font-size: 24px; font-weight: 700; color: var(--gray-900); }
        .stat-plan { background: var(--primary-light); color: var(--primary); }
        .stat-do { background: var(--success-light); color: var(--success); }
        .stat-check { background: var(--warning-light); color: var(--warning); }
        .stat-act { background: var(--danger-light); color: var(--danger); }

        .filter-bar {
            background: white; border: 1px solid var(--gray-200);
            border-radius: var(--radius); padding: 16px; margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }
        .filter-row { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
        .search-box { flex: 1; min-width: 200px; position: relative; }
        .search-icon {
            position: absolute; left: 12px; top: 50%;
            transform: translateY(-50%); color: var(--gray-400); pointer-events: none;
        }
        .search-input {
            width: 100%; padding: 8px 12px 8px 36px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm); font-size: 14px; transition: all 0.2s;
        }
        .search-input:focus {
            outline: none; border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .filter-select, .date-input {
            padding: 8px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm); font-size: 14px;
            background: white; cursor: pointer; transition: all 0.2s;
        }
        .filter-select:focus, .date-input:focus { outline: none; border-color: var(--primary); }
        .quick-date-filters {
            display: flex; gap: 6px; flex-wrap: wrap; align-items: center;
        }
        .btn-quick-date { padding: 6px 12px; font-size: 12px; }
        .btn-quick-date.btn-primary { background: var(--primary); border-color: var(--primary); color: white; }

        /* Select2 Theme */
        .select2-container--default .select2-selection--single {
            height: 40px; border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm); background: white;
            transition: all 0.2s; display: flex; align-items: center;
        }
        .select2-container--default .select2-selection--single:hover { border-color: var(--gray-400); }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--gray-800); font-size: 14px; line-height: normal;
            padding-left: 12px; padding-right: 30px;
        }
        .select2-container--default .select2-selection--single .select2-selection__placeholder { color: var(--gray-400); }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 38px; right: 8px; width: 24px; }
        .select2-container--default .select2-selection--single .select2-selection__arrow b { border-color: var(--gray-500) transparent transparent transparent; }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background: var(--primary); color: white; }
        .select2-container--default .select2-results__option--selected { background: var(--primary-light); color: var(--primary-dark); }
        .select2-container--default .select2-results__option--selectable { font-size: 13px; padding: 8px 12px; }
        .select2-results__group {
            font-size: 11px; font-weight: 700; color: var(--gray-500);
            text-transform: uppercase; letter-spacing: 0.5px;
            padding: 8px 12px 4px; background: var(--gray-50);
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid var(--gray-300); border-radius: var(--radius-xs);
            padding: 8px 10px; font-size: 13px; outline: none;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field:focus { border-color: var(--primary); }
        .select2-container--default.select2-container--disabled .select2-selection--single { background: var(--gray-50); cursor: not-allowed; }
        .select2-dropdown { z-index: 10000 !important; }
        .select2-container { z-index: 10001 !important; }
        .select2-search--dropdown { padding: 8px !important; }
        .select2-search--dropdown .select2-search__field { font-family: inherit !important; }
        .select2-container--default .select2-selection--single .select2-selection__rendered.select2-loading {
            color: var(--gray-400); font-style: italic;
        }
        /* Batasi tinggi dropdown & enable scroll saat ribuan option */
        .select2-results__options {
            max-height: 320px !important;
            overflow-y: auto !important;
        }

        /* QR Code signature */
        .signature-box {
            text-align: center;
            width: 100%;
            padding: 4px;
            box-sizing: border-box;
            page-break-inside: avoid;
        }
        .signature-box .sig-label {
            color: #6b7280;
            margin-bottom: 6px;
            font-size: 12px;
            line-height: 1.3;
        }
        .signature-qrcode {
            display: block;
            width: 80px;
            height: 80px;
            margin: 0 auto 6px;
            line-height: 0;
            font-size: 0;
        }
        .signature-qrcode img,
        .signature-qrcode canvas {
            display: block;
            width: 80px !important;
            height: 80px !important;
        }
        .signature-qrcode[data-signature=""] {
            display: block;
            visibility: hidden;
            height: 80px;
        }
        .signature-box .sig-name {
            border-top: 1px solid #374151;
            padding-top: 4px;
            font-weight: 600;
            font-size: 12px;
            min-height: 20px;
            line-height: 1.4;
            word-break: break-word;
            color: #111827;
            background: #ffffff;
            position: relative;
            z-index: 1;
        }
        .signature-box .sig-role {
            color: #9ca3af;
            font-size: 10px;
            line-height: 1.3;
        }

        /* Task Report Styles */
        .tr-section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }
        .tr-section-title {
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 6px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .tr-info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        .tr-info-table td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .tr-info-table .tr-label {
            background: #f9fafb;
            color: #6b7280;
            font-weight: 600;
            width: 15%;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .tr-info-table .tr-value {
            color: #111827;
            width: 35%;
        }
        .tr-content-box {
            background: #f9fafb;
            border-left: 4px solid #d1d5db;
            border-radius: 4px;
            padding: 12px 14px;
            font-size: 13px;
            color: #374151;
            line-height: 1.5;
            min-height: 50px;
            word-break: break-word;
            white-space: pre-wrap;
        }
        .tr-box-problem { background: #fef2f2; border-left-color: #dc2626; color: #7f1d1d; }
        .tr-box-temp { background: #fffbeb; border-left-color: #d97706; color: #78350f; }
        .tr-box-perm { background: #f0fdf4; border-left-color: #16a34a; color: #14532d; }
        .tr-box-approval { background: #f0fdf4; border-left-color: #16a34a; color: #14532d; }
        .tr-empty { color: #9ca3af; font-style: italic; }
        .tr-approval-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 12px;
        }
        .tr-approval-grid .tr-label-inline { color: #6b7280; font-weight: 600; }

        /* Report Modal */
        .report-filter {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }
        .report-filter .filter-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .report-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }
        .report-stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            text-align: center;
        }
        .report-stat-card .report-stat-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.5px;
        }
        .report-stat-card .report-stat-value {
            font-size: 26px;
            font-weight: 700;
            margin-top: 4px;
        }
        .report-stat-card.plan  { background: #eff6ff; border-color: #bfdbfe; }
        .report-stat-card.plan  .report-stat-value { color: #2563eb; }
        .report-stat-card.do    { background: #f0fdf4; border-color: #bbf7d0; }
        .report-stat-card.do    .report-stat-value { color: #16a34a; }
        .report-stat-card.check { background: #fffbeb; border-color: #fde68a; }
        .report-stat-card.check .report-stat-value { color: #d97706; }
        .report-stat-card.act   { background: #fef2f2; border-color: #fecaca; }
        .report-stat-card.act   .report-stat-value { color: #dc2626; }

        .status-pill {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 8px;
            text-transform: uppercase;
        }
        .status-open        { background: #dbeafe; color: #1e40af; }
        .status-in_progress { background: #fef3c7; color: #92400e; }
        .status-done        { background: #dcfce7; color: #166534; }
        .status-cancelled   { background: #fee2e2; color: #991b1b; }

        .stage-pill {
            display: inline-block;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 8px;
            text-transform: uppercase;
            background: #f3f4f6;
            color: #374151;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .report-table th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid #e5e7eb;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .report-table td {
            padding: 8px 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .report-table tr:nth-child(even) td { background: #fafafa; }

        .section-breakdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            font-size: 12px;
        }
        .section-breakdown-bar {
            flex: 1;
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .section-breakdown-bar > div {
            height: 100%;
            background: #2563eb;
            border-radius: 4px;
            transition: width 0.4s;
        }

        /* Kanban Board */
        .board-container {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }
        .board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }
        .board-title { font-size: 18px; font-weight: 600; color: var(--gray-800); }
        .board-subtitle { font-size: 13px; color: var(--gray-500); }
        .board-info { display: flex; gap: 8px; align-items: center; }
        .info-badge {
            padding: 4px 10px;
            background: var(--gray-100);
            border-radius: 12px;
            font-size: 12px;
            color: var(--gray-600);
        }

        .kanban-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            min-height: 500px;
        }
        .kanban-column {
            background: var(--gray-50);
            border: 2px solid transparent;
            border-radius: var(--radius);
            padding: 12px;
            transition: all 0.2s;
            min-height: 200px;
        }
        .kanban-column.drag-over {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: scale(1.02);
        }
        .kanban-column.locked { opacity: 0.7; cursor: not-allowed; }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding: 0 4px;
        }
        .column-title { font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .column-icon {
            width: 28px; height: 28px;
            border-radius: var(--radius-xs);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
        }
        .column-plan .column-icon { background: var(--primary-light); color: var(--primary); }
        .column-do .column-icon { background: var(--success-light); color: var(--success); }
        .column-check .column-icon { background: var(--warning-light); color: var(--warning); }
        .column-act .column-icon { background: var(--danger-light); color: var(--danger); }
        .column-count {
            font-size: 11px; background: white; padding: 2px 8px;
            border-radius: 10px; color: var(--gray-500); font-weight: 600;
        }
        .kanban-items { display: flex; flex-direction: column; gap: 8px; min-height: 50px; }

        .kanban-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
            position: relative;
        }
        .kanban-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .kanban-card.draggable { cursor: grab; }
        .kanban-card.dragging {
            opacity: 0.5; cursor: grabbing;
            transform: scale(0.95);
            box-shadow: var(--shadow-lg);
        }
        .card-header {
            display: flex; justify-content: space-between; align-items: flex-start;
            margin-bottom: 8px;
        }
        .card-title {
            font-weight: 600; font-size: 13px; color: var(--gray-800);
            flex: 1; margin-right: 8px;
        }
        .card-delete {
            background: none; border: none; color: var(--gray-400);
            cursor: pointer; padding: 4px; border-radius: 4px;
            transition: all 0.2s; opacity: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .kanban-card:hover .card-delete { opacity: 1; }
        .card-delete:hover { color: var(--danger); background: var(--danger-light); }
        .card-delete .ti { font-size: 16px; }
        .card-desc { font-size: 12px; color: var(--gray-600); margin-bottom: 8px; word-break: break-word; }
        .card-meta { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 8px; }
        .card-tag {
            font-size: 10px; padding: 2px 6px; border-radius: 8px;
            font-weight: 500; background: var(--gray-100); color: var(--gray-600);
        }
        .card-footer {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 11px; color: var(--gray-500);
            border-top: 1px solid var(--gray-100); padding-top: 8px;
        }
        .card-operator { font-weight: 500; color: var(--gray-700); }
        .card-date { color: var(--gray-400); }
        .card-pending { background: var(--warning-light); border-color: #fde68a; }
        .pending-badge {
            font-size: 10px; padding: 2px 6px; border-radius: 8px;
            background: var(--warning-light); color: #92400e;
            font-weight: 500; display: inline-flex; align-items: center; gap: 4px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: white;
            border-radius: var(--radius);
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.3s;
            box-shadow: var(--shadow-lg);
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-200);
            position: sticky;
            top: 0;
            background: white;
            z-index: 1;
        }
        .modal-title { font-size: 18px; font-weight: 600; color: var(--gray-800); }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-400);
            width: 32px; height: 32px;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .modal-close:hover { background: var(--gray-100); color: var(--gray-600); }
        .modal-body { padding: 24px; }
        .form-section { margin-bottom: 24px; }
        .form-section-title {
            font-size: 14px; font-weight: 600; color: var(--gray-700);
            margin-bottom: 16px; padding-bottom: 8px;
            border-bottom: 2px solid var(--gray-100);
            display: flex; align-items: center; gap: 8px;
        }
        .lock-icon {
            font-size: 12px; color: var(--gray-400);
            display: inline-flex; align-items: center; gap: 4px;
        }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .form-group { margin-bottom: 16px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px; font-weight: 500;
            color: var(--gray-700);
        }
        .form-label.required::after { content: ' *'; color: var(--danger); }
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 14px;
            transition: all 0.2s;
            background: white; color: var(--gray-800);
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .form-input:disabled, .form-select:disabled, .form-textarea:disabled {
            background: var(--gray-50);
            color: var(--gray-400);
            cursor: not-allowed;
        }
        .form-textarea { resize: vertical; min-height: 80px; }
        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            position: sticky;
            bottom: 0;
            background: white;
            flex-wrap: wrap;
        }
        #taskModal .modal-content { max-width: 760px; }
        .modal-footer .btn { padding: 8px 14px; }
        @media (max-width: 640px) {
            .modal-footer { flex-direction: column-reverse; }
            .modal-footer .btn { width: 100%; justify-content: center; }
        }

        .toast {
            position: fixed; bottom: 24px; right: 24px;
            background: white; border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm); padding: 12px 16px;
            box-shadow: var(--shadow-lg); z-index: 2000;
            display: flex; align-items: center; gap: 8px;
            font-weight: 500; font-size: 14px;
            animation: slideInRight 0.3s;
        }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        .badge-status {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 10px; padding: 3px 8px; border-radius: 8px;
            font-weight: 600; margin-bottom: 6px; line-height: 1;
        }
        .badge-approved { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .badge-pending-approval { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-in-progress { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }

        .kanban-card.card-approved {
            background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
            border-color: #86efac;
        }
        .kanban-card.card-cancelled {
            opacity: 0.55;
            background: #f9fafb;
            border-color: #d1d5db;
        }
        .kanban-card.card-cancelled .card-title { text-decoration: line-through; color: #9ca3af; }

        .ti { vertical-align: -0.125em; stroke-width: 2; display: inline-flex; align-items: center; justify-content: center; }
        .btn .ti { font-size: 1.1em; margin-right: 2px; }
        .column-icon .ti { font-size: 16px; }
        .badge-status .ti { font-size: 12px; }
        .notice-bar > .ti { font-size: 1.3em; flex-shrink: 0; }
        .search-icon .ti { font-size: 16px; display: block; }
        .stat-icon .ti { font-size: 18px; }

        @media (max-width: 1024px) {
            .kanban-board { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .header-content { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; justify-content: space-between; }
            .container { padding: 12px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .form-grid { grid-template-columns: 1fr; }
            .filter-row { flex-direction: column; }
            .search-box { width: 100%; }
            .filter-select, .date-input { width: 100%; }
        }
        @media (max-width: 640px) {
            .kanban-board { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
        }

        @media print {
            body > * { display: none !important; }

            body.print-report #reportModal.active,
            body.print-task-report #taskReportModal.active {
                display: block !important;
                position: static !important;
                top: auto !important;
                left: auto !important;
                width: 100% !important;
                height: auto !important;
                max-height: none !important;
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                z-index: auto !important;
                overflow: visible !important;
                animation: none !important;
            }

            body.print-report #reportModal.active .modal-content,
            body.print-task-report #taskReportModal.active .modal-content {
                display: block !important;
                max-width: 100% !important;
                width: 100% !important;
                max-height: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                overflow: visible !important;
                animation: none !important;
                margin: 0 !important;
            }

            body.print-report #reportModal.active .modal-header,
            body.print-report #reportModal.active .modal-footer,
            body.print-report #reportModal.active .report-filter,
            body.print-task-report #taskReportModal.active .modal-header,
            body.print-task-report #taskReportModal.active .modal-footer {
                display: none !important;
            }

            body.print-report #reportModal.active .modal-body,
            body.print-task-report #taskReportModal.active .modal-body {
                display: block !important;
                padding: 0 !important;
                background: #ffffff !important;
                overflow: visible !important;
            }

            body.print-report #reportModal.active #reportContent,
            body.print-task-report #taskReportModal.active #taskReportContent {
                display: block !important;
                padding: 12px !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                margin: 0 !important;
            }

            .tr-section,
            .signature-box,
            .tr-info-table tr,
            .report-table tr {
                page-break-inside: avoid;
            }
            h1, h2, h3, h4, h5, h6, p {
                page-break-after: avoid;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        @media print {
            /* ── Halaman A4 dengan margin tipis ── */
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            /* ── Scale konten agar selalu fit 1 halaman ── */
            body.print-report #reportContent,
            body.print-task-report #taskReportContent {
                /* Trik: pakai zoom untuk Chrome/Edge/Safari */
                zoom: 1;

                /* Fallback untuk Firefox yang belum support zoom: */
                /* (kompensasi agar lebar tetap pas) */
                width: auto !important;
                padding: 0 !important;
            }

            /* ── Perkecil padding & font supaya hemat ruang ── */
            .tr-section {
                margin-bottom: 8px !important;
            }

            .tr-section-title {
                font-size: 11px !important;
                padding-bottom: 4px !important;
                margin-bottom: 6px !important;
            }

            .tr-content-box {
                padding: 8px 10px !important;
                font-size: 11px !important;
                min-height: auto !important;
            }

            .tr-info-table td {
                padding: 4px 6px !important;
                font-size: 11px !important;
            }

            .signature-qrcode,
            .signature-qrcode img,
            .signature-qrcode canvas {
                width: 60px !important;
                height: 60px !important;
            }

            /* ── Hilangkan margin-top besar di tabel signature ── */
            body.print-report #reportContent > table:last-of-type,
            body.print-task-report #taskReportContent > table:last-of-type {
                margin-top: 12px !important;
            }

            /* ── Table report — kecilkan supaya hemat ── */
            .report-table th,
            .report-table td {
                padding: 4px !important;
                font-size: 10px !important;
            }

            .report-stat-card {
                padding: 6px !important;
            }
            .report-stat-card .report-stat-value {
                font-size: 18px !important;
            }

            /* ── JANGAN pakai page-break-inside: avoid terlalu banyak ── */
            /* Elemen ini sering "mentok" dan dorong ke halaman 2 */
            .tr-section {
                page-break-inside: auto !important;
            }
            .signature-box {
                page-break-inside: avoid !important;  /* signature tetap jangan kepotong */
            }

            /* ── Hilangkan scrollbar dari overflow-x:auto ── */
            #reportTableWrapper {
                overflow: visible !important;
            }
        }
    </style>
    <style>
        .radio-card-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(110px, 1fr));
            gap: 8px;
        }
        .radio-card-group.pic-section-group {
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
        }
        .radio-card {
            cursor: pointer;
            position: relative;
            display: block;
        }
        .radio-card input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
        .radio-card-content {
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-sm);
            padding: 10px 6px;
            text-align: center;
            transition: all 0.2s;
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            min-height: 64px;
        }
        .radio-card-content .ti {
            font-size: 20px;
            color: var(--gray-500);
            transition: all 0.2s;
        }
        .radio-card-content .radio-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--gray-700);
            line-height: 1.2;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .radio-card:hover .radio-card-content {
            border-color: var(--gray-400);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
        .radio-card input:checked + .radio-card-content {
            border-color: var(--primary);
            background: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .radio-card input:checked + .radio-card-content .ti { color: var(--primary); }
        .radio-card input:checked + .radio-card-content .radio-label { color: var(--primary-dark); }
        .radio-card input:disabled + .radio-card-content {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--gray-50);
        }

        .cat-man      { background: #dbeafe; color: #1e40af; }
        .cat-machine  { background: #fef3c7; color: #92400e; }
        .cat-material { background: #dcfce7; color: #166534; }
        .cat-methode  { background: #f3e8ff; color: #6b21a8; }

        .picsec-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 600;
            background: #ecfeff;
            color: #155e75;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .doc-upload-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        @media (max-width: 640px) { .doc-upload-grid { grid-template-columns: 1fr; } }

        .doc-upload-card {
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            padding: 12px;
            background: white;
            transition: all 0.2s;
        }
        .doc-upload-card.has-file { border-color: #86efac; background: #f0fdf4; }

        .doc-upload-header {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
            font-size: 13px;
        }
        .doc-upload-header .doc-title { font-weight: 600; color: var(--gray-800); flex: 1; }
        .doc-upload-header .doc-status {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 8px;
            background: var(--gray-100);
            color: var(--gray-500);
            font-weight: 600;
            text-transform: uppercase;
        }
        .doc-upload-card.has-file .doc-status { background: #dcfce7; color: #166534; }

        .doc-dropzone {
            border: 2px dashed var(--gray-300);
            border-radius: var(--radius-xs);
            padding: 18px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--gray-50);
        }
        .doc-dropzone:hover, .doc-dropzone.drag-over {
            border-color: var(--primary);
            background: var(--primary-light);
        }
        .doc-dropzone .doc-icon { font-size: 26px; color: var(--gray-400); display: block; margin-bottom: 6px; }
        .doc-dropzone .doc-hint { font-size: 12px; color: var(--gray-600); font-weight: 500; }
        .doc-dropzone .doc-sub { font-size: 10px; color: var(--gray-400); margin-top: 2px; }

        .doc-preview {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            padding: 8px;
            background: white;
            border-radius: var(--radius-xs);
            border: 1px solid #bbf7d0;
        }
        .doc-info { display: flex; align-items: center; gap: 8px; min-width: 0; flex: 1; }
        .doc-info-icon { font-size: 20px; color: var(--success); flex-shrink: 0; }
        .doc-info-text { min-width: 0; }
        .doc-info-name {
            font-size: 12px;
            font-weight: 600;
            color: var(--gray-800);
            display: block;
            text-decoration: none;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
        }
        .doc-info-name:hover { color: var(--primary); text-decoration: underline; }
        .doc-info-meta { font-size: 10px; color: var(--gray-500); }
        .doc-actions { display: flex; gap: 4px; flex-shrink: 0; }
        .doc-actions .btn { padding: 4px 6px; }

        .doc-progress-bar {
            height: 4px;
            background: var(--gray-100);
            border-radius: 2px;
            margin-top: 10px;
            overflow: hidden;
        }
        .doc-progress-fill {
            height: 100%;
            background: var(--primary);
            width: 0;
            transition: width 0.2s;
        }
        .doc-upload-card.locked .doc-actions {
            display: none !important;
        }
        .doc-upload-card.locked .doc-dropzone {
            cursor: not-allowed;
            opacity: 0.55;
            pointer-events: none;
        }
        .doc-upload-card.locked .doc-status {
            background: #fee2e2 !important;
            color: #991b1b !important;
        }
        /* Spinner animation untuk tombol refresh */
        .ti-spin {
            animation: ti-spin 0.9s linear infinite;
        }
        @keyframes ti-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }
        /* Phase Badge */
        .phase-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #fff;
            margin-right: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.15);
            flex-shrink: 0;
        }
        .phase-1 { background: linear-gradient(135deg, #2563eb, #1e40af); }
        .phase-2 { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
        .phase-3 { background: linear-gradient(135deg, #0891b2, #0e7490); }
        .phase-4 { background: linear-gradient(135deg, #16a34a, #15803d); }

        /* Dokumen status radio (Sudah/Belum) */
        .doc-status-radio {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-top: 6px;
        }
        .doc-status-radio .radio-card-content {
            min-height: 48px;
            padding: 6px 4px;
            gap: 2px;
        }
        .doc-status-radio .radio-card-content .ti { font-size: 16px; }
        .doc-status-radio .radio-card-content .radio-label { font-size: 10px; }

        .doc-status-radio .radio-card input[value="sudah"]:checked + .radio-card-content {
            border-color: #16a34a;
            background: #f0fdf4;
        }
        .doc-status-radio .radio-card input[value="sudah"]:checked + .radio-card-content .ti,
        .doc-status-radio .radio-card input[value="sudah"]:checked + .radio-card-content .radio-label {
            color: #16a34a;
        }
        .doc-status-radio .radio-card input[value="belum"]:checked + .radio-card-content {
            border-color: #dc2626;
            background: #fef2f2;
        }
        .doc-status-radio .radio-card input[value="belum"]:checked + .radio-card-content .ti,
        .doc-status-radio .radio-card input[value="belum"]:checked + .radio-card-content .radio-label {
            color: #dc2626;
        }
        /* Kolom header dengan phase badge + subtitle */
        .kanban-column .column-title {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            flex: 1;
            min-width: 0;
        }
        .kanban-column .column-title .column-icon {
            margin-top: 2px;
            flex-shrink: 0;
        }
        .kanban-column .column-title > span:last-child {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
            line-height: 1.3;
        }
        .kanban-column .column-title .phase-badge {
            font-size: 9px;
            padding: 2px 8px;
            margin: 0;
            align-self: flex-start;
        }
        .kanban-column .column-title > span:last-child > span:not(.phase-badge) {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray-800);
            word-break: break-word;
        }
        /* Klasifikasi lebih fleksibel untuk label panjang */
        #categoryGroup {
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        }
        #categoryGroup .radio-card-content {
            min-height: 72px;
        }
        #categoryGroup .radio-card-content .radio-label {
            font-size: 10px;
            line-height: 1.25;
            white-space: normal;
            text-align: center;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        window.CSRF_TOKEN  = '<?= csrfHeader() ?>';
        window.CSRF_HEADER = 'X-CSRF-TOKEN';
        $.ajaxSetup({
            headers: {
                [window.CSRF_HEADER]: window.CSRF_TOKEN
            }
        });
    </script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo">P</div>
                <div>
                    <div class="header-title">PDCA Kanban Board</div>
                    <div class="header-subtitle">Continuous Improvement System</div>
                </div>
            </div>
            <div class="header-actions">
                <div class="user-info" id="userInfo">
                    <div class="user-avatar">O</div>
                    <span id="userName">Operator</span>
                </div>
                <button class="btn" onclick="openRoleInfo()" id="roleInfoBtn" title="Informasi Role & Akses">
                    <i class="ti ti-shield-lock"></i> Info Role
                </button>
                <button class="btn" onclick="openProfileModal()" id="profileBtn" style="display:none">
                    <i class="ti ti-user-cog"></i> Profile
                </button>
                <button class="btn btn-primary" onclick="openTaskModal()">
                    <i class="ti ti-plus"></i> Tambah Task
                </button>
                <button class="btn" onclick="openReport()" id="reportBtnHeader" style="display:none">
                    <i class="ti ti-file-analytics"></i> Report
                </button>
                <button class="btn btn-success" onclick="toggleRegister()" id="registerBtnHeader" style="display:none">
                    <i class="ti ti-user-plus"></i> User Baru
                </button>
                <button class="btn" onclick="toggleLogin()" id="loginBtn">
                    <i class="ti ti-lock"></i> Login Leader
                </button>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="notice-bar" id="noticeBar">
            <i class="ti ti-info-circle"></i>
            <span>Anda login sebagai <strong>Operator</strong>. Hanya bisa menambah task baru dengan data dasar.</span>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Phase 1</span>
                    <span class="stat-icon stat-plan"><i class="ti ti-clipboard-list"></i></span>
                </div>
                <div class="stat-value" id="planCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Phase 2</span>
                    <span class="stat-icon stat-do"><i class="ti ti-bolt"></i></span>
                </div>
                <div class="stat-value" id="doCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Phase 3</span>
                    <span class="stat-icon stat-check"><i class="ti ti-circle-check"></i></span>
                </div>
                <div class="stat-value" id="checkCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Phase 4</span>
                    <span class="stat-icon stat-act"><i class="ti ti-tool"></i></span>
                </div>
                <div class="stat-value" id="actCount">0</div>
            </div>
        </div>

        <div class="filter-bar">
            <div class="filter-row">
                <div class="search-box">
                    <span class="search-icon"><i class="ti ti-search"></i></span>
                    <input type="text" class="search-input" id="searchInput" placeholder="Cari task..." oninput="handleSearch(this.value)">
                </div>
                <select class="filter-select" id="sectionFilter" onchange="handleFilter()">
                    <option value="all">Semua Seksi</option>
                </select>
                <input type="date" class="date-input" id="dateFrom" onchange="handleFilter()" title="Dari Tanggal">
                <input type="date" class="date-input" id="dateTo" onchange="handleFilter()" title="Sampai Tanggal">
                <button class="btn btn-sm" onclick="clearFilters()">
                    <i class="ti ti-refresh"></i> Reset
                </button>
                <button class="btn btn-sm" onclick="refreshData()" id="refreshBtn" title="Muat ulang data dari server">
                    <i class="ti ti-reload"></i> Refresh
                </button>
            </div>
            <div class="filter-row" style="margin-top:10px;padding-top:10px;border-top:1px dashed var(--gray-200)">
                <span style="font-size:11px;color:var(--gray-500);font-weight:700;text-transform:uppercase;letter-spacing:0.5px">Quick Filter:</span>
                <div class="quick-date-filters">
                    <button class="btn btn-sm btn-quick-date btn-primary" data-range="today" onclick="setQuickDate('today')">
                        <i class="ti ti-calendar-event"></i> Hari Ini
                    </button>
                    <button class="btn btn-sm btn-quick-date" data-range="week" onclick="setQuickDate('week')">
                        <i class="ti ti-calendar-week"></i> 7 Hari
                    </button>
                    <button class="btn btn-sm btn-quick-date" data-range="month" onclick="setQuickDate('month')">
                        <i class="ti ti-calendar-month"></i> Bulan Ini
                    </button>
                    <button class="btn btn-sm btn-quick-date" data-range="all" onclick="setQuickDate('all')">
                        <i class="ti ti-calendar"></i> Semua
                    </button>
                    <button class="btn btn-sm" onclick="openCancelledModal()" id="cancelledBtn" title="Lihat semua task cancelled" style="margin-left:auto">
                        <i class="ti ti-circle-x"></i> Cancelled Task (<span id="cancelledCount">0</span>)
                    </button>
                </div>
            </div>
        </div>

        <div class="board-container">
            <div class="board-header">
                <div>
                    <div class="board-title">PDCA Board</div>
                    <div class="board-subtitle" id="boardSubtitle">Drag and drop hanya untuk leader</div>
                </div>
                <div class="board-info">
                    <span class="info-badge" id="totalTasks">0 Task</span>
                    <span class="info-badge" id="filteredTasks">0 Ditampilkan</span>
                </div>
            </div>
            <div class="kanban-board" id="kanbanBoard">
                <div class="kanban-column column-plan" data-stage="plan" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-clipboard-list"></i></span>
                            <span>
                                <span class="phase-badge phase-1">Phase 1</span>
                                <span>Informasi Masalah</span>
                                <div style="font-size:10px;color:#6b7280;font-weight:500;margin-top:2px">Operator</div>
                            </span>
                        </span>
                        <span class="column-count" id="planBadge">0</span>
                    </div>
                    <div class="kanban-items" id="planItems"></div>
                </div>
                <div class="kanban-column column-do" data-stage="do" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-bolt"></i></span>
                            <span>
                                <span class="phase-badge phase-2">Phase 2</span>
                                <span>Penanggung Jawab Masalah</span>
                                <div style="font-size:10px;color:#6b7280;font-weight:500;margin-top:2px">Team Leader</div>
                            </span>
                        </span>
                        <span class="column-count" id="doBadge">0</span>
                    </div>
                    <div class="kanban-items" id="doItems"></div>
                </div>
                <div class="kanban-column column-check" data-stage="check" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-circle-check"></i></span>
                            <span>
                                <span class="phase-badge phase-3">Phase 3</span>
                                <span>Isi Tindakan</span>
                                <div style="font-size:10px;color:#6b7280;font-weight:500;margin-top:2px">Seksi Terkait</div>
                            </span>
                        </span>
                        <span class="column-count" id="checkBadge">0</span>
                    </div>
                    <div class="kanban-items" id="checkItems"></div>
                </div>
                <div class="kanban-column column-act" data-stage="act" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-tool"></i></span>
                            <span>
                                <span class="phase-badge phase-4">Phase 4</span>
                                <span>Approval / Evaluasi Tindakan</span>
                                <div style="font-size:10px;color:#6b7280;font-weight:500;margin-top:2px">Group Leader UP</div>
                            </span>
                        </span>
                        <span class="column-count" id="actBadge">0</span>
                    </div>
                    <div class="kanban-items" id="actItems"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div class="modal" id="taskModal">
        <div class="modal-content" style="max-width: 760px;">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Tambah Task Baru</h3>
                <button class="modal-close" onclick="closeModal('taskModal')">×</button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <?= csrf()?>
                    <div class="form-section" id="phase1Section">
                        <div class="form-section-title">
                            <span class="phase-badge phase-1">Phase 1</span>
                            <i class="ti ti-notes"></i> 
                            Data Operator
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label required">Nama Operator</label>
                                <select class="form-select" id="operatorName" required>
                                    <option value="">-- Memuat data... --</option>
                                </select>
                                <small style="color:#6b7280;font-size:11px;display:block;margin-top:4px">
                                    <i class="ti ti-info-circle"></i> Ketik nama untuk mencari
                                </small>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Tanggal</label>
                                <input type="date" class="form-input" id="taskDate" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Seksi</label>
                                <select class="form-select" id="section" required>
                                    <option value="">Pilih Seksi</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label required">Masalah</label>
                                <textarea class="form-textarea" id="problem" required placeholder="Deskripsikan masalah yang terjadi..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-section" id="classificationSection">
                        <div class="form-section-title">
                            <span class="phase-badge phase-2">Phase 2</span>
                            <i class="ti ti-category"></i> Klasifikasi Masalah
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Category</label>
                            <div class="radio-card-group" id="categoryGroup">
                                <label class="radio-card">
                                    <input type="radio" name="category" value="man">
                                    <div class="radio-card-content">
                                        <i class="ti ti-user"></i>
                                        <span class="radio-label">Man</span>
                                    </div>
                                </label>
                               <label class="radio-card">
                                    <input type="radio" name="category" value="machine">
                                    <div class="radio-card-content">
                                        <i class="ti ti-engine"></i>
                                        <span class="radio-label">Machine / Jig / Mold</span>
                                    </div>
                                </label>
                                <label class="radio-card">
                                    <input type="radio" name="category" value="material">
                                    <div class="radio-card-content">
                                        <i class="ti ti-box"></i>
                                        <span class="radio-label">Material</span>
                                    </div>
                                </label>
                                <label class="radio-card">
                                    <input type="radio" name="category" value="methode">
                                    <div class="radio-card-content">
                                        <i class="ti ti-checklist"></i>
                                        <span class="radio-label">Methode</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group" style="margin-top:16px">
                            <label class="form-label required">PIC Section</label>
                            <div class="radio-card-group pic-section-group" id="picSectionGroup">
                                <label class="radio-card"><input type="radio" name="pic_section" value="mdf"><div class="radio-card-content"><span class="radio-label">MDF</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="pe"><div class="radio-card-content"><span class="radio-label">PE</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="pc"><div class="radio-card-content"><span class="radio-label">PC</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="inj"><div class="radio-card-content"><span class="radio-label">INJ</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="st"><div class="radio-card-content"><span class="radio-label">ST</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="qc"><div class="radio-card-content"><span class="radio-label">QC</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="whp"><div class="radio-card-content"><span class="radio-label">WHP</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="prc"><div class="radio-card-content"><span class="radio-label">PRC</span></div></label>
                                <label class="radio-card"><input type="radio" name="pic_section" value="log"><div class="radio-card-content"><span class="radio-label">LOG</span></div></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-section" id="documentsSection">
                        <div class="form-section-title">
                            <i class="ti ti-paperclip"></i> Dokumen Pendukung
                            <span class="lock-icon"><i class="ti ti-info-circle"></i> Semua wajib "Sudah" untuk approve</span>
                        </div>

                        <div class="doc-upload-grid">

                            <!-- 4M -->
                            <div class="doc-upload-card" data-type="4m">
                                <div class="doc-upload-header">
                                    <i class="ti ti-file-text"></i>
                                    <span class="doc-title">4M</span>
                                </div>
                                <div class="doc-status-radio">
                                    <label class="radio-card">
                                        <input type="radio" name="doc_status_4m" value="sudah">
                                        <div class="radio-card-content">
                                            <i class="ti ti-circle-check"></i>
                                            <span class="radio-label">Sudah</span>
                                        </div>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="doc_status_4m" value="belum">
                                        <div class="radio-card-content">
                                            <i class="ti ti-circle-x"></i>
                                            <span class="radio-label">Belum</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Logbook -->
                            <div class="doc-upload-card" data-type="logbook">
                                <div class="doc-upload-header">
                                    <i class="ti ti-notebook"></i>
                                    <span class="doc-title">Logbook</span>
                                </div>
                                <div class="doc-status-radio">
                                    <label class="radio-card">
                                        <input type="radio" name="doc_status_logbook" value="sudah">
                                        <div class="radio-card-content">
                                            <i class="ti ti-circle-check"></i>
                                            <span class="radio-label">Sudah</span>
                                        </div>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="doc_status_logbook" value="belum">
                                        <div class="radio-card-content">
                                            <i class="ti ti-circle-x"></i>
                                            <span class="radio-label">Belum</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Nursecall -->
                            <div class="doc-upload-card" data-type="nursecall">
                                <div class="doc-upload-header">
                                    <i class="ti ti-bell"></i>
                                    <span class="doc-title">Nursecall</span>
                                </div>
                                <div class="doc-status-radio">
                                    <label class="radio-card">
                                        <input type="radio" name="doc_status_nursecall" value="sudah">
                                        <div class="radio-card-content">
                                            <i class="ti ti-circle-check"></i>
                                            <span class="radio-label">Sudah</span>
                                        </div>
                                    </label>
                                    <label class="radio-card">
                                        <input type="radio" name="doc_status_nursecall" value="belum">
                                        <div class="radio-card-content">
                                            <i class="ti ti-circle-x"></i>
                                            <span class="radio-label">Belum</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="form-section" id="leaderSection">
                        <div class="form-section-title">
                            <span class="phase-badge phase-3">Phase 3</span>
                            <i class="ti ti-tool"></i> Tindakan 
                            <span class="lock-icon"><i class="ti ti-lock"></i></span>
                        </div>
                        <div class="form-grid">
                            <div class="form-group full-width">
                                <label class="form-label">Tindakan Temporary</label>
                                <textarea class="form-textarea" id="tempAction" disabled></textarea>
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label">Tindakan Permanent</label>
                                <textarea class="form-textarea" id="permAction" disabled></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Deadline</label>
                                <input type="date" class="form-input" id="deadline" disabled>
                            </div>
                            <div class="form-group">
                                <label class="form-label">PIC</label>
                                <select class="form-select" id="pic" disabled>
                                    <option value="">-- Pilih PIC --</option>
                                </select>
                                <small style="color:#6b7280;font-size:11px;display:block;margin-top:4px">
                                    <i class="ti ti-info-circle"></i> Ketik nama untuk mencari
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="form-section" id="approvalSection">
                        <div class="form-section-title">
                            <span class="phase-badge phase-4">Phase 4</span>
                            <i class="ti ti-circle-check"></i> Approval
                            <span class="lock-icon"><i class="ti ti-lock"></i> (Group Leader / Manager)</span>
                        </div>

                        <!-- Info status approval -->
                        <div id="approvalInfoBox" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px">
                            <!-- Empty state -->
                            <div id="approvalEmptyState" style="text-align:center;color:#9ca3af">
                                <i class="ti ti-clock-hour-4" style="font-size:32px;opacity:0.4;display:block;margin-bottom:8px"></i>
                                <div style="font-size:13px">Belum ada approval</div>
                                <div style="font-size:11px;margin-top:4px">Task dapat di-approve setelah semua Phase 1-3 terisi dan berada di stage ACT</div>
                            </div>

                            <!-- Filled state -->
                            <div id="approvalFilledState" style="display:none">
                                <div class="tr-approval-grid">
                                    <div>
                                        <div class="tr-label-inline">Disetujui oleh</div>
                                        <div style="font-weight:600;margin-top:2px;color:#111827" id="approvalByName">-</div>
                                    </div>
                                    <div>
                                        <div class="tr-label-inline">Waktu Approval</div>
                                        <div style="font-weight:600;margin-top:2px;color:#111827" id="approvalAt">-</div>
                                    </div>
                                    <div>
                                        <div class="tr-label-inline">Tanda Tangan</div>
                                        <div style="font-weight:600;margin-top:2px;color:#111827" id="approvalSignature">-</div>
                                    </div>
                                    <div>
                                        <div class="tr-label-inline">Status Akhir</div>
                                        <div style="margin-top:2px" id="approvalStatusBadge">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Checklist approval -->
                        <div id="approvalChecklist" style="margin-top:12px;padding:12px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:12px">
                            <div style="font-weight:600;color:#92400e;margin-bottom:8px">
                                <i class="ti ti-list-check"></i> Checklist Approval
                            </div>
                            <div id="approvalChecklistItems" style="color:#78350f;line-height:1.8">
                                <!-- diisi via JS -->
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="taskId">
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('taskModal')">Batal</button>
                <button class="btn" id="reportTaskBtn" onclick="openTaskReportFromEdit()" style="display:none">
                    <i class="ti ti-file-description"></i> Report
                </button>
                <button class="btn" id="historyTaskBtn" onclick="viewHistory()" style="display:none">
                    <i class="ti ti-history"></i> History
                </button>
                <button class="btn btn-danger" id="cancelTaskBtn" onclick="cancelTask()" style="display:none">
                    <i class="ti ti-circle-x"></i> Cancel Task
                </button>
                <button class="btn btn-success" id="approveTaskBtn" onclick="approveTask()" style="display:none">
                    <i class="ti ti-check"></i> Approve
                </button>
                <button class="btn btn-primary" onclick="saveTask()" id="saveBtn">
                    <i class="ti ti-device-floppy"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- Login Modal -->
    <div class="modal" id="loginModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Login Leader</h3>
                <button class="modal-close" onclick="closeModal('loginModal')">×</button>
            </div>
            <div class="modal-body">
                <form id="loginForm">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-input" id="loginUsername" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-input" id="loginPassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('loginModal')">Batal</button>
                <button class="btn btn-primary" onclick="loginLeader()">
                    <i class="ti ti-login"></i> Login
                </button>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal" id="registerModal">
        <div class="modal-content" style="max-width: 450px;">
            <div class="modal-header">
                <h3 class="modal-title">Daftar Akun Baru</h3>
                <button class="modal-close" onclick="closeModal('registerModal')">×</button>
            </div>
            <div class="modal-body">
                <form id="registerForm">
                    <div class="form-group">
                        <label class="form-label required">Nama Lengkap</label>
                        <input type="text" class="form-input" id="registerName" required placeholder="Nama lengkap Anda">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Username</label>
                        <input type="text" class="form-input" id="registerUsername" required minlength="3" placeholder="Minimal 3 karakter">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Password</label>
                        <input type="password" class="form-input" id="registerPassword" required minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Konfirmasi Password</label>
                        <input type="password" class="form-input" id="registerPasswordConfirm" required minlength="6" placeholder="Ulangi password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Daftar Sebagai</label>
                        <select class="form-select" id="registerRole">
                            <option value="operator">Operator</option>
                            <option value="team_leader">Team Leader</option>
                            <option value="group_leader">Group Leader</option>
                            <option value="manager">Manager</option>
                        </select>
                        <small style="color:#6b7280;font-size:12px;display:block;margin-top:4px">
                            <i class="ti ti-info-circle"></i> Role <strong>Leader</strong> memerlukan approval admin untuk aktivasi penuh.
                        </small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('registerModal')">Batal</button>
                <button class="btn btn-primary" onclick="registerUser()" id="registerBtn">
                    <i class="ti ti-user-plus"></i> Daftar
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="modal" id="profileModal">
        <div class="modal-content" style="max-width: 520px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="ti ti-user-cog"></i> Profile Saya
                </h3>
                <button class="modal-close" onclick="closeModal('profileModal')">×</button>
            </div>
            <div class="modal-body">
                <form id="profileForm">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="ti ti-user"></i> Informasi Akun
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Nama Lengkap</label>
                            <input type="text" class="form-input" id="profileName" required minlength="2" placeholder="Nama lengkap">
                        </div>
                        <div class="form-group">
                            <label class="form-label required">Username</label>
                            <input type="text" class="form-input" id="profileUsername" required minlength="3" placeholder="Minimal 3 karakter">
                            <small style="color:#6b7280;font-size:11px;display:block;margin-top:4px">
                                <i class="ti ti-info-circle"></i> Username harus unik, tidak boleh sama dengan user lain.
                            </small>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-input" id="profileRole" disabled>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="ti ti-lock"></i> Ganti Password
                            <span class="lock-icon" style="margin-left:auto">
                                <i class="ti ti-info-circle"></i> Kosongkan jika tidak diubah
                            </span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password Lama</label>
                            <div style="position:relative">
                                <input type="password" class="form-input" id="profileOldPassword" placeholder="Masukkan password saat ini" style="padding-right:40px">
                                <button type="button" class="btn-toggle-pw" onclick="togglePwVisibility('profileOldPassword', this)" style="position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px 8px">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password Baru</label>
                            <div style="position:relative">
                                <input type="password" class="form-input" id="profileNewPassword" placeholder="Minimal 6 karakter" style="padding-right:40px">
                                <button type="button" class="btn-toggle-pw" onclick="togglePwVisibility('profileNewPassword', this)" style="position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px 8px">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div style="position:relative">
                                <input type="password" class="form-input" id="profileConfirmPassword" placeholder="Ulangi password baru" style="padding-right:40px">
                                <button type="button" class="btn-toggle-pw" onclick="togglePwVisibility('profileConfirmPassword', this)" style="position:absolute;right:6px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9ca3af;padding:4px 8px">
                                    <i class="ti ti-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('profileModal')">Batal</button>
                <button class="btn btn-primary" onclick="saveProfile()" id="saveProfileBtn">
                    <i class="ti ti-device-floppy"></i> Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- Report Modal -->
    <div class="modal" id="reportModal">
        <div class="modal-content" style="max-width: 1100px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="ti ti-file-analytics"></i> Laporan PDCA
                </h3>
                <button class="modal-close" onclick="closeModal('reportModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="report-filter">
                    <div class="filter-row">
                        <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-input" id="reportDateFrom">
                        </div>
                        <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-input" id="reportDateTo">
                        </div>
                        <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px">
                            <label class="form-label">Seksi</label>
                            <select class="form-select" id="reportSection">
                                <option value="all">Semua Seksi</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px">
                            <label class="form-label">Stage</label>
                            <select class="form-select" id="reportStage">
                                <option value="all">Semua Stage</option>
                                <option value="plan">Phase 1</option>
                                <option value="do">Phase 2</option>
                                <option value="check">Phase 3</option>
                                <option value="act">Phase 4</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="reportStatus">
                                <option value="all">Semua Status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0">
                            <label class="form-label">&nbsp;</label>
                            <button class="btn btn-primary" onclick="loadReportData()">
                                <i class="ti ti-filter"></i> Terapkan
                            </button>
                        </div>
                    </div>
                </div>

                <div id="reportContent" style="background:white;padding:20px;border:1px solid #e5e7eb;border-radius:8px;margin-top:16px">
                    <div style="text-align:center;border-bottom:2px solid #1f2937;padding-bottom:16px;margin-bottom:20px">
                        <div style="display:flex;align-items:center;justify-content:center;gap:12px;margin-bottom:8px">
                            <div style="width:44px;height:44px;background:#2563eb;border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:20px">P</div>
                            <div style="text-align:left">
                                <div style="font-size:20px;font-weight:700;color:#1f2937">PDCA KANBAN BOARD</div>
                                <div style="font-size:12px;color:#6b7280">Continuous Improvement System</div>
                            </div>
                        </div>
                        <div style="font-size:15px;font-weight:600;color:#374151;margin-top:8px">LAPORAN TASK</div>
                        <div style="font-size:12px;color:#6b7280" id="reportPeriod">Periode: Semua Data</div>
                    </div>

                    <div id="reportSummary" class="report-summary-grid"></div>
                    <div id="reportSectionBreakdown" style="margin-top:20px"></div>

                    <div style="margin-top:20px">
                        <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;border-bottom:1px solid #e5e7eb;padding-bottom:6px">
                            Detail Task
                        </div>
                        <div id="reportTableWrapper" style="overflow-x:auto"></div>
                    </div>

                    <table style="width:100%;margin-top:32px;font-size:12px;border-collapse:collapse;page-break-inside:avoid">
                        <tr>
                            <td style="width:33.33%;vertical-align:top;text-align:center;padding:0 6px">
                                <div class="signature-box">
                                    <div class="sig-label">Dibuat oleh,</div>
                                    <div class="signature-qrcode" data-type="report-creator" data-signature=""></div>
                                    <div class="sig-name" id="reportCreatedBy">-</div>
                                    <div class="sig-role">Admin/Leader</div>
                                </div>
                            </td>
                            <td style="width:33.33%;vertical-align:top;text-align:center;padding:0 6px">
                                <div class="signature-box">
                                    <div class="sig-label">Diperiksa oleh,</div>
                                    <div class="signature-qrcode" data-type="report-checker" data-signature=""></div>
                                    <div class="sig-name">____________________</div>
                                    <div class="sig-role">Supervisor</div>
                                </div>
                            </td>
                            <td style="width:33.33%;vertical-align:top;text-align:center;padding:0 6px">
                                <div class="signature-box">
                                    <div class="sig-label">Disetujui oleh,</div>
                                    <div class="signature-qrcode" data-type="report-approver" data-signature=""></div>
                                    <div class="sig-name">____________________</div>
                                    <div class="sig-role">Manager</div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div style="text-align:center;font-size:10px;color:#9ca3af;margin-top:24px;border-top:1px solid #e5e7eb;padding-top:8px">
                        Dicetak: <span id="reportGeneratedAt"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('reportModal')">Tutup</button>
                <button class="btn" onclick="printReport()">
                    <i class="ti ti-printer"></i> Print
                </button>
                <button class="btn btn-danger" onclick="exportPDF()">
                    <i class="ti ti-file-type-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Task Report Modal -->
    <div class="modal" id="taskReportModal">
        <div class="modal-content" style="max-width: 850px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="ti ti-file-description"></i> Laporan Detail Task
                </h3>
                <button class="modal-close" onclick="closeModal('taskReportModal')">×</button>
            </div>
            <div class="modal-body" style="background:#f9fafb">
                <div id="taskReportContent" style="background:white;padding:24px;border-radius:8px">
                    <div style="border-bottom:2px solid #1f2937;padding-bottom:16px;margin-bottom:20px">
                        <div style="display:flex;align-items:center;gap:12px">
                            <div style="width:48px;height:48px;background:#2563eb;border-radius:8px;display:flex;align-items:center;justify-content:center;color:white;font-weight:bold;font-size:22px">P</div>
                            <div style="flex:1">
                                <div style="font-size:18px;font-weight:700;color:#1f2937">PDCA KANBAN BOARD</div>
                                <div style="font-size:12px;color:#6b7280">Continuous Improvement System</div>
                            </div>
                            <div style="text-align:right">
                                <div style="font-size:11px;color:#6b7280">Nomor</div>
                                <div style="font-size:14px;font-weight:700;color:#2563eb" id="trTaskCode">-</div>
                            </div>
                        </div>
                        <div style="text-align:center;font-size:15px;font-weight:600;color:#374151;margin-top:12px;letter-spacing:1px">
                            LAPORAN TASK PERBAIKAN
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;margin-bottom:16px;justify-content:center" id="trStatusBadges"></div>

                    <div class="tr-section">
                        <div class="tr-section-title"><i class="ti ti-info-circle"></i> Informasi Umum</div>
                        <table class="tr-info-table">
                            <tr>
                                <td class="tr-label">Operator</td>
                                <td class="tr-value" id="trOperatorName">-</td>
                                <td class="tr-label">Tanggal Task</td>
                                <td class="tr-value" id="trTaskDate">-</td>
                            </tr>
                            <tr>
                                <td class="tr-label">Seksi</td>
                                <td class="tr-value" id="trSection">-</td>
                                <td class="tr-label">PIC</td>
                                <td class="tr-value" id="trPic">-</td>
                            </tr>

                            <tr>
                                <td class="tr-label">Category</td>
                                <td class="tr-value" id="trCategory">-</td>
                                <td class="tr-label">PIC Section</td>
                                <td class="tr-value" id="trPicSection">-</td>
                            </tr>

                            <tr>
                                <td class="tr-label">Stage</td>
                                <td class="tr-value" id="trStage">-</td>
                                <td class="tr-label">Status</td>
                                <td class="tr-value" id="trStatus">-</td>
                            </tr>
                            <tr>
                                <td class="tr-label">Deadline</td>
                                <td class="tr-value" id="trDeadline">-</td>
                                <td class="tr-label">Dibuat</td>
                                <td class="tr-value" id="trCreatedAt">-</td>
                            </tr>
                        </table>
                    </div>

                    <div class="tr-section">
                        <div class="tr-section-title" style="color:#dc2626;border-bottom-color:#fecaca">
                            <i class="ti ti-alert-triangle"></i> Deskripsi Masalah
                        </div>
                        <div class="tr-content-box tr-box-problem" id="trProblem">-</div>
                    </div>

                    <div class="tr-section">
                        <div class="tr-section-title" style="color:#d97706;border-bottom-color:#fde68a">
                            <i class="ti ti-clock-hour-4"></i> Tindakan Temporary
                        </div>
                        <div class="tr-content-box tr-box-temp" id="trTempAction">-</div>
                    </div>

                    <div class="tr-section">
                        <div class="tr-section-title" style="color:#16a34a;border-bottom-color:#bbf7d0">
                            <i class="ti ti-shield-check"></i> Tindakan Permanent
                        </div>
                        <div class="tr-content-box tr-box-perm" id="trPermAction">-</div>
                    </div>

                    <div class="tr-section" id="trAttachmentSection" style="display:none">
                        <div class="tr-section-title" style="color:#2563eb;border-bottom-color:#bfdbfe">
                            <i class="ti ti-paperclip"></i> Dokumen Pendukung
                        </div>
                        <div id="trAttachmentList" style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px"></div>
                    </div>
                    <div class="tr-section" id="trApprovalSection" style="display:none">
                        <div class="tr-section-title" style="color:#16a34a;border-bottom-color:#86efac">
                            <i class="ti ti-circle-check"></i> Approval
                        </div>
                        <div class="tr-content-box tr-box-approval" id="trApprovalContent"></div>
                    </div>

                    <table style="width:100%;margin-top:32px;font-size:12px;border-collapse:collapse;page-break-inside:avoid">
                        <tr>
                            <td style="width:33.33%;vertical-align:top;text-align:center;padding:0 6px">
                                <div class="signature-box">
                                    <div class="sig-label">Dibuat oleh,</div>
                                    <div class="signature-qrcode" data-type="operator" data-signature=""></div>
                                    <div class="sig-name" id="trSignOperator">-</div>
                                    <div class="sig-role">Operator</div>
                                </div>
                            </td>
                            <td style="width:33.33%;vertical-align:top;text-align:center;padding:0 6px">
                                <div class="signature-box">
                                    <div class="sig-label">PIC,</div>
                                    <div class="signature-qrcode" data-type="pic" data-signature=""></div>
                                    <div class="sig-name" id="trSignPic">-</div>
                                    <div class="sig-role">Penanggung Jawab</div>
                                </div>
                            </td>
                            <td style="width:33.33%;vertical-align:top;text-align:center;padding:0 6px">
                                <div class="signature-box">
                                    <div class="sig-label">Disetujui oleh,</div>
                                    <div class="signature-qrcode" data-type="leader" data-signature=""></div>
                                    <div class="sig-name" id="trSignLeader">-</div>
                                    <div class="sig-role">Group Leader/Manager</div>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div style="text-align:center;font-size:10px;color:#9ca3af;margin-top:24px;border-top:1px solid #e5e7eb;padding-top:8px">
                        Dicetak: <span id="trGeneratedAt"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('taskReportModal')">Tutup</button>
                <button class="btn" onclick="printTaskReport()">
                    <i class="ti ti-printer"></i> Print
                </button>
                <button class="btn btn-danger" onclick="exportTaskPDF()">
                    <i class="ti ti-file-type-pdf"></i> Export PDF
                </button>
            </div>
        </div>
    </div>
    <!-- Role Info Modal -->
    <div class="modal" id="roleInfoModal">
        <div class="modal-content" style="max-width: 1100px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="ti ti-shield-lock"></i> Informasi Role & Hak Akses
                </h3>
                <button class="modal-close" onclick="closeModal('roleInfoModal')">×</button>
            </div>
            <div class="modal-body">

                <div class="notice-bar" style="margin-bottom:16px">
                    <i class="ti ti-info-circle"></i>
                    <span>Berikut daftar lengkap <strong>5 role</strong> di sistem PDCA Kanban beserta hak akses masing-masing.</span>
                </div>

                <!-- Legend Role Cards -->
                <div id="roleLegend" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:20px"></div>

                <!-- Matrix Table -->
                <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;border-bottom:1px solid #e5e7eb;padding-bottom:6px;display:flex;align-items:center;gap:6px">
                    <i class="ti ti-table"></i> Matrix Hak Akses
                </div>
                <div style="overflow-x:auto">
                    <table id="roleMatrixTable" class="report-table" style="font-size:12px"></table>
                </div>

                <!-- Detail per Role -->
                <div style="font-size:14px;font-weight:600;color:#1f2937;margin-top:24px;margin-bottom:12px;border-bottom:1px solid #e5e7eb;padding-bottom:6px;display:flex;align-items:center;gap:6px">
                    <i class="ti ti-list-details"></i> Detail Kemampuan per Role
                </div>
                <div id="roleDetailList" style="display:flex;flex-direction:column;gap:12px"></div>

                <!-- Catatan Penting -->
                <div style="margin-top:24px;padding:16px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:12px;color:#78350f">
                    <div style="font-weight:700;margin-bottom:8px;display:flex;align-items:center;gap:6px">
                        <i class="ti ti-alert-triangle"></i> Catatan Penting
                    </div>
                    <ul style="padding-left:20px;margin:0;line-height:1.7">
                        <li><strong>Approve task</strong> hanya bisa dilakukan oleh Group Leader, Manager & Admin — dan <strong>wajib upload 3 dokumen</strong> (4M, Logbook, Nursecall) terlebih dahulu.</li>
                        <li><strong>Task yang sudah di-approve (done) atau cancelled</strong> tidak bisa diubah lagi oleh role apapun.</li>
                        <li><strong>Operator</strong> hanya bisa tambah task dengan 4 field dasar; field tindakan hanya bisa diisi oleh Leader.</li>
                        <li>Transisi phase terbatas: <code>Phase 1 → 2 → 3 → 4</code>. Tidak bisa lompat phase.</li>
                        <li>Perpindahan <strong>Phase 2 → Phase 3</strong> memerlukan Tindakan Temporary / Permanent yang sudah diisi.</li>
                    </ul>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('roleInfoModal')">Tutup</button>
                <button class="btn btn-success" onclick="exportRoleInfoExcel()">
                    <i class="ti ti-file-spreadsheet"></i> Export Excel
                </button>
                <button class="btn" onclick="printRoleInfo()">
                    <i class="ti ti-printer"></i> Print
                </button>
            </div>
        </div>
    </div>
    <!-- Cancelled Tasks Modal -->
    <div class="modal" id="cancelledTasksModal">
        <div class="modal-content" style="max-width: 1000px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="ti ti-circle-x" style="color:#dc2626"></i> Task Cancelled
                </h3>
                <button class="modal-close" onclick="closeModal('cancelledTasksModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="notice-bar warning" style="margin-bottom:16px">
                    <i class="ti ti-info-circle"></i>
                    <span>Berikut daftar task yang sudah dibatalkan. Task cancelled tidak muncul di Kanban board.</span>
                </div>
                <div style="overflow-x:auto">
                    <table class="report-table" id="cancelledTable">
                        <thead>
                            <tr>
                                <th style="width:32px;text-align:center">No</th>
                                <th style="width:140px">Kode</th>
                                <th style="width:90px">Tanggal</th>
                                <th style="width:130px">Operator</th>
                                <th style="width:110px">Seksi</th>
                                <th>Masalah</th>
                                <th style="width:70px;text-align:center">Stage</th>
                                <th style="width:120px">PIC</th>
                                <th style="width:60px;text-align:center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="cancelledTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn" onclick="closeModal('cancelledTasksModal')">Tutup</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    // ============================================================
    // KONFIGURASI
    // ============================================================
    const API_BASE = '<?=url()?>';

    const SwalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // ============================================================
    // STATE
    // ============================================================
    let state = {
        tasks: [],
        currentUser: null,
        searchQuery: '',
        sectionFilter: 'all',
        dateFrom: null,
        dateTo: null
    };
    let employeeList    = [];
    let employeeLoaded  = false;
    let employeeLoading = false;
    let sectionsList    = [];
    let sectionsLoaded  = false;
    let select2Inited   = false;
    // let taskAttachments = {};
    let taskDocStatus = { '4m': 'belum', 'logbook': 'belum', 'nursecall': 'belum' };

    const USER_STORAGE_KEY = 'pdca_user_info';

    // ============================================================
    // API HELPER
    // ============================================================
    async function api(path, { method = 'GET', body = null, query = {}, headers = {} } = {}) {
        const url = new URL(API_BASE + path, window.location.origin);
        Object.entries(query).forEach(([k, v]) => {
            if (v !== '' && v !== null && v !== undefined) {
                url.searchParams.set(k, v);
            }
        });

        return new Promise((resolve, reject) => {
            $.ajax({
                url: url.toString(),
                type: method,
                dataType: 'text',
                contentType: body !== null ? 'application/json; charset=utf-8' : undefined,
                data: body !== null ? JSON.stringify(body) : undefined,
                xhrFields: { withCredentials: true },
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...headers
                },
                success: function (text) {
                    let data = null;
                    try { data = text ? JSON.parse(text) : null; }
                    catch { data = text; }
                    resolve(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    const status = jqXHR.status;
                    let data = null;
                    try { data = jqXHR.responseText ? JSON.parse(jqXHR.responseText) : null; }
                    catch { data = jqXHR.responseText; }

                    const msg =
                        (data && (data.message || data.error)) ||
                        (typeof data === 'string' && data.trim().slice(0, 300)) ||
                        errorThrown || textStatus || `HTTP ${status}`;

                    const err = new Error(msg);
                    err.status = status;
                    err.data = data;

                    if (status === 401) clearUser();
                    reject(err);
                }
            });
        });
    }

    // ============================================================
    // SESSION HELPERS
    // ============================================================
    function saveUser(user) {
        state.currentUser = user;
        try { localStorage.setItem(USER_STORAGE_KEY, JSON.stringify(user)); } catch {}
    }
    function clearUser() {
        state.currentUser = null;
        try { localStorage.removeItem(USER_STORAGE_KEY); } catch {}
        updateUserInterface();
        render();
    }
    function restoreUser() {
        try {
            const raw = localStorage.getItem(USER_STORAGE_KEY);
            if (raw) {
                const user = JSON.parse(raw);
                if (user && user.role) user.role = String(user.role).toLowerCase();
                state.currentUser = user;
            }
        } catch { state.currentUser = null; }
    }
    function normalizeRole(r) {
        r = String(r || '').toLowerCase();
        if (r === 'leader') return 'group_leader';
        if (r === 'admin')  return 'manager';
        return r;
    }

    function currentRole() {
        return state.currentUser ? normalizeRole(state.currentUser.role) : null;
    }

    function canEdit() {
        return ['team_leader', 'group_leader', 'manager'].includes(currentRole());
    }

    function canApprove() {
        return ['group_leader', 'manager'].includes(currentRole());
    }

    function canDragDrop() { return canEdit(); }

    // ============================================================
    // NORMALIZE TASK
    // ============================================================
    function normalizeTask(t) {
        return {
            id: t.id,
            taskCode: t.task_code || '',
            operatorName: t.operator_name || '',
            date: t.task_date || '',
            section: t.section || '',
            category: (t.category || '').toLowerCase(),
            picSection: (t.pic_section || '').toLowerCase(),
            problem: t.problem || '',
            tempAction: t.temporary_action || '',
            permAction: t.permanent_action || '',
            deadline: t.deadline || '',
            pic: t.pic || '',
            stage: (t.stage || 'plan').toLowerCase(),
            status: (t.status || 'open').toLowerCase(),
            leaderSignature: t.ttd_leader || t.leader_signature || '',
            approvedAt: t.approved_at || null,
            approvedBy: t.approved_by || null,
            createdByName: t.created_by_name || '',
            createdByRole: (t.created_by_role || 'operator').toLowerCase(),
            createdAt: t.created_at || null,
            updatedAt: t.updated_at || null,
            attachments: t.attachments || [],
            doc4mStatus:        (t.doc_4m_status        || 'belum').toLowerCase(),
            docLogbookStatus:   (t.doc_logbook_status   || 'belum').toLowerCase(),
            docNursecallStatus: (t.doc_nursecall_status || 'belum').toLowerCase(),
        };
    }

    // ============================================================
    // SELECT2 — untuk Operator & PIC
    // ============================================================
    function initSelect2() {
        if (typeof $.fn.select2 === 'undefined') {
            console.warn('[select2] library belum di-load.');
            return;
        }
        if (select2Inited) return;

        const commonOpts = {
            placeholder: '-- Pilih --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#taskModal'),
            minimumResultsForSearch: 0,   // paksa search box selalu muncul
            language: {
                noResults:     function () { return 'Data tidak ditemukan'; },
                searching:     function () { return 'Mencari...'; },
                inputTooShort: function () { return 'Ketik minimal 1 karakter'; },
            }
        };

        $('#operatorName').select2($.extend({}, commonOpts, {
            placeholder: '-- Pilih Operator --',
        }));

        $('#pic').select2($.extend({}, commonOpts, {
            placeholder: '-- Pilih PIC --',
        }));

        select2Inited = true;
    }

    // Helper set value ke Select2 (dipakai saat edit task)
    function setSelect2Value($sel, value, text) {
        if (!value) {
            $sel.val(null).trigger('change');
            return;
        }
        if (!$sel.find("option[value='" + String(value).replace(/'/g, "\\'") + "']").length) {
            const newOpt = new Option(text || value, value, true, true);
            $sel.append(newOpt);
        }
        $sel.val(value).trigger('change');
    }

    // ============================================================
    // LOAD EMPLOYEES — FIX PAGINATION (2000+ karyawan)
    // ============================================================
    async function loadEmployees() {
        if (employeeLoaded) return;

        const $opSel  = $('#operatorName');
        const $picSel = $('#pic');

        $opSel.html('<option value="">-- Memuat data... --</option>');
        $picSel.html('<option value="">-- Memuat data... --</option>');
        if ($opSel.data('select2'))  $opSel.trigger('change.select2');
        if ($picSel.data('select2')) $picSel.trigger('change.select2');

        try {
            const res  = await api('/endpoint/employee');
            const list = res?.data?.data || res?.data || (Array.isArray(res) ? res : []);
            employeeList = Array.isArray(list) ? list : [];

            // Sort by nama
            employeeList.sort((a, b) =>
                String(a.nama || '').localeCompare(String(b.nama || ''))
            );

            // Build flat list — tanpa optgroup
            let html = '<option value="">-- Pilih --</option>';
            employeeList.forEach(emp => {
                const nama    = String(emp.nama || '').trim();
                const nik     = String(emp.nik  || '').trim();
                const section = String(emp.kode_section || '').trim();
                if (!nama) return;

                const label = nik ? `${nama} — ${nik}` : nama;

                html += `<option value="${escapeHtml(nama)}"
                                data-nik="${escapeHtml(nik)}"
                                data-section="${escapeHtml(section)}">
                            ${escapeHtml(label)}
                        </option>`;
            });

            $opSel.html(html);
            $picSel.html(html);

            employeeLoaded = true;

            if ($opSel.data('select2'))  $opSel.trigger('change.select2');
            if ($picSel.data('select2')) $picSel.trigger('change.select2');

        } catch (err) {
            console.error('[employee] gagal load:', err);
            $opSel.html('<option value="">-- Gagal memuat, coba refresh --</option>');
            $picSel.html('<option value="">-- Gagal memuat, coba refresh --</option>');
            if ($opSel.data('select2'))  $opSel.trigger('change.select2');
            if ($picSel.data('select2')) $picSel.trigger('change.select2');
            showToast('Gagal memuat data employee: ' + err.message, 'warning');
        }
    }

    // ============================================================
    // INIT
    // ============================================================
    $(document).ready(async function () {
        initSelect2();
        setupEventListeners();
        restoreUser();
        updateUserInterface();
        setQuickDate('today');
        await loadSections();
        await loadTasks();
    });

    function setupEventListeners() {
        $('#taskForm').on('submit', function (e) {
            e.preventDefault();
            saveTask();
        });
        $('#loginForm').on('submit', function (e) {
            e.preventDefault();
            loginLeader();
        });
        $('#registerForm').on('submit', function (e) {
            e.preventDefault();
            registerUser();
        });
        $('.modal').on('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });

        // Auto-fill section saat operator dipilih
        $('#operatorName').on('select2:select', function (e) {
            // Ambil dari option yang terpilih, BUKAN dari e.params.data
            const $selected = $(this).find(':selected');
            const section = String($selected.data('section') || '').trim();

            if (!section) return;

            const $secSelect = $('#section');

            // Coba match dulu dengan option yang sudah ada
            let matched = false;
            $secSelect.find('option').each(function () {
                if (String($(this).val()).toLowerCase() === section.toLowerCase()) {
                    $secSelect.val($(this).val());
                    matched = true;
                    return false;
                }
            });

            // Kalau tidak ada, append option baru
            if (!matched) {
                $secSelect.append(`<option value="${escapeHtml(section)}">${escapeHtml(section)}</option>`);
                $secSelect.val(section);
            }

            if ($secSelect.data('select2')) $secSelect.trigger('change.select2');

            // Efek visual hijau sebentar
            $secSelect.css('background', '#f0fdf4');
            setTimeout(() => $secSelect.css('background', ''), 800);
        });
        $(document).on('change', 'input[name^="doc_status_"]', function () {
            updateApproveButtonState();
        });
    }

    // ============================================================
    // SECTIONS LOADER
    // ============================================================
    async function loadSections() {
        if (sectionsLoaded) return;

        try {
            const res = await api('/endpoint/dept');
            const list = res?.data || [];
            sectionsList = Array.isArray(list) ? list : [];

            const grouped = {};
            sectionsList.forEach(sec => {
                const dept = String(sec.dept || 'Lainnya').trim() || 'Lainnya';
                const kode = String(sec.kode_section || '').trim();
                if (!kode) return;

                if (!grouped[dept]) grouped[dept] = [];
                grouped[dept].push({
                    kode,
                    desc: sec.section_desc || kode,
                    singkatan: sec.singkatan,
                });
            });

            const sortedDepts = Object.keys(grouped).sort();
            sortedDepts.forEach(d => {
                grouped[d].sort((a, b) => a.kode.localeCompare(b.kode));
            });

            populateSectionDropdowns(grouped);
            sectionsLoaded = true;

        } catch (err) {
            console.error('[sections] gagal load:', err);
            showToast('Gagal memuat daftar seksi: ' + err.message, 'warning');
        }
    }

    function populateSectionDropdowns(grouped) {
        const configs = [
            { selector: '#sectionFilter', placeholder: 'Semua Seksi', placeholderValue: 'all' },
            { selector: '#reportSection', placeholder: 'Semua Seksi', placeholderValue: 'all' },
            { selector: '#section',       placeholder: 'Pilih Seksi', placeholderValue: '' },
        ];

        configs.forEach(cfg => {
            const $sel = $(cfg.selector);
            if (!$sel.length) return;

            const currentVal = $sel.val();
            $sel.empty().append(`<option value="${cfg.placeholderValue}">${cfg.placeholder}</option>`);

            Object.entries(grouped).forEach(([dept, items]) => {
                $sel.append(`<optgroup label="${escapeHtml(dept)}">`);
                items.forEach(item => {
                    $sel.append(`<option value="${escapeHtml(item.kode)}">${escapeHtml(item.kode)}</option>`);
                });
                $sel.append('</optgroup>');
            });

            if (currentVal !== undefined && currentVal !== null && currentVal !== '') {
                $sel.val(currentVal);
            }
        });
    }

    // ══════════════════════════════════════════════════════════════
    // REFRESH DATA
    // ══════════════════════════════════════════════════════════════
    async function refreshData() {
        const $btn  = $('#refreshBtn');
        const $icon = $btn.find('i');

        // Disable + spin icon
        $btn.prop('disabled', true);
        $icon.addClass('ti-spin');

        // Force reload sections (task selalu fresh dari loadTasks)
        sectionsLoaded = false;

        try {
            await Promise.all([
                loadSections(),
                loadTasks(),
            ]);
            showToast('Data berhasil di-refresh', 'success');
        } catch (err) {
            showToast('Gagal refresh: ' + err.message, 'error');
        } finally {
            $btn.prop('disabled', false);
            $icon.removeClass('ti-spin');
        }
    }

    // ============================================================
    // LOAD TASKS
    // ============================================================
    async function loadTasks() {
        try {
            const res = await api('/tasks', { query: { per_page: 500 } });
            const list = Array.isArray(res) ? res : (res?.data || []);
            state.tasks = list.map(normalizeTask);
            render();
        } catch (err) {
            if (err.status === 401) {
                state.tasks = [];
                render();
            } else {
                showToast('Gagal memuat task: ' + err.message, 'error');
            }
        }
    }

    // ============================================================
    // REGISTER
    // ============================================================
    function toggleRegister() {
        if (String(state.currentUser?.role || '').toLowerCase() !== 'admin') {
            showAlert('warning', 'Akses Ditolak', 'Hanya Admin yang bisa menambah user baru.');
            return;
        }
        openModal('registerModal');
    }

    async function registerUser() {
        const name = $('#registerName').val().trim();
        const username = $('#registerUsername').val().trim();
        const password = $('#registerPassword').val();
        const passwordConfirm = $('#registerPasswordConfirm').val();
        const role = $('#registerRole').val();

        if (String(state.currentUser?.role || '').toLowerCase() !== 'admin') {
            showAlert('warning', 'Akses Ditolak', 'Hanya Admin yang bisa mendaftarkan user baru.');
            return;
        }

        if (!name || !username || !password || !passwordConfirm) {
            showAlert('warning', 'Form Tidak Lengkap', 'Semua field wajib diisi!');
            return;
        }
        if (username.length < 3) {
            showAlert('warning', 'Username Terlalu Pendek', 'Username minimal 3 karakter.');
            return;
        }
        if (password.length < 6) {
            showAlert('warning', 'Password Terlalu Pendek', 'Password minimal 6 karakter.');
            return;
        }
        if (password !== passwordConfirm) {
            showAlert('warning', 'Password Tidak Sama', 'Konfirmasi password tidak cocok!');
            return;
        }

        Swal.fire({
            title: 'Mendaftarkan...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            await api('/auth/register', { method: 'POST', body: { name, username, password, role } });
            Swal.close();
            closeModal('registerModal');
            $('#registerForm')[0].reset();

            const result = await Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                html: `Akun <strong>${escapeHtml(username)}</strong> berhasil dibuat.<br>Silakan login untuk melanjutkan.`,
                confirmButtonText: 'Login Sekarang',
                confirmButtonColor: '#2563eb',
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                cancelButtonColor: '#6b7280',
                reverseButtons: true
            });

            if (result.isConfirmed) {
                $('#loginUsername').val(username);
                setTimeout(() => {
                    openModal('loginModal');
                    $('#loginPassword').focus();
                }, 250);
            }
        } catch (err) {
            Swal.close();
            showAlert('error', 'Registrasi Gagal', err.message);
        }
    }

    // ============================================================
    // PROFILE
    // ============================================================
    function openProfileModal() {
        if (!state.currentUser) {
            showAlert('warning', 'Belum Login', 'Silakan login terlebih dahulu.');
            return;
        }

        $('#profileName').val(state.currentUser.name || '');
        $('#profileUsername').val(state.currentUser.username || '');
        $('#profileRole').val((state.currentUser.role || '').toUpperCase());
        $('#profileOldPassword').val('');
        $('#profileNewPassword').val('');
        $('#profileConfirmPassword').val('');

        openModal('profileModal');
    }

    function togglePwVisibility(inputId, btn) {
        const $input = $('#' + inputId);
        const isPw = $input.attr('type') === 'password';
        $input.attr('type', isPw ? 'text' : 'password');
        $(btn).find('i').attr('class', isPw ? 'ti ti-eye-off' : 'ti ti-eye');
    }

    async function saveProfile() {
        const name     = $('#profileName').val().trim();
        const username = $('#profileUsername').val().trim();
        const oldPw    = $('#profileOldPassword').val();
        const newPw    = $('#profileNewPassword').val();
        const confirmPw= $('#profileConfirmPassword').val();

        if (!name || name.length < 2) {
            showAlert('warning', 'Nama Tidak Valid', 'Nama minimal 2 karakter.');
            return;
        }
        if (!username || username.length < 3) {
            showAlert('warning', 'Username Tidak Valid', 'Username minimal 3 karakter.');
            return;
        }

        const wantsChangePw = !!(oldPw || newPw || confirmPw);

        if (wantsChangePw) {
            if (!oldPw) {
                showAlert('warning', 'Password Lama Kosong', 'Masukkan password lama untuk mengganti password.');
                return;
            }
            if (!newPw || newPw.length < 6) {
                showAlert('warning', 'Password Baru Tidak Valid', 'Password baru minimal 6 karakter.');
                return;
            }
            if (newPw !== confirmPw) {
                showAlert('warning', 'Password Tidak Sama', 'Konfirmasi password baru tidak cocok.');
                return;
            }
            if (newPw === oldPw) {
                showAlert('warning', 'Password Sama', 'Password baru harus berbeda dengan password lama.');
                return;
            }
        }

        const payload = { name, username };
        if (wantsChangePw) {
            payload.old_password = oldPw;
            payload.new_password = newPw;
        }

        Swal.fire({
            title: 'Menyimpan...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await api('/auth/profile', { method: 'PUT', body: payload });

            const updated = res?.user || res?.data || { ...state.currentUser, name, username };
            saveUser({
                id:       updated.id       || state.currentUser.id,
                name:     updated.name     || name,
                username: updated.username || username,
                role:     updated.role     || state.currentUser.role,
            });

            Swal.close();
            closeModal('profileModal');
            updateUserInterface();
            showToast('Profile berhasil diupdate', 'success');

            if (wantsChangePw) {
                setTimeout(async () => {
                    const again = await showConfirm(
                        'Password Diubah',
                        'Password berhasil diubah. Disarankan login ulang untuk keamanan. Login sekarang?',
                        'Ya, Login Ulang', 'Nanti', 'question'
                    );
                    if (again) {
                        clearUser();
                        window.location.reload();
                    }
                }, 600);
            }

        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Menyimpan', err.message);
        }
    }

    // ============================================================
    // AUTH
    // ============================================================
    function toggleLogin() {
        if (state.currentUser) logout();
        else openModal('loginModal');
    }

    async function loginLeader() {
        const username = $('#loginUsername').val().trim();
        const password = $('#loginPassword').val();

        if (!username || !password) {
            showAlert('warning', 'Form Tidak Lengkap', 'Username dan password wajib diisi!');
            return;
        }

        try {
            const res = await api('/auth/login', { method: 'POST', body: { username, password } });
            const user = res?.user || res?.data || res;
            if (!user || !user.role) throw new Error('Response login tidak valid');

            const role = normalizeRole(user.role);
            const allowed = ['operator', 'team_leader', 'group_leader', 'manager'];
            if (!allowed.includes(role)) {
                throw new Error('Role akun tidak dikenal: ' + role);
            }

            saveUser({
                id:       user.id,
                name:     user.name,
                username: user.username,
                role:     user.role,
            });
            closeModal('loginModal');
            $('#loginForm')[0].reset();
            updateUserInterface();
            showToast('Login berhasil sebagai ' + role.replace('_', ' '), 'success');
            setTimeout(() => window.location.reload(), 800);
        } catch (err) {
            showAlert('error', 'Login Gagal', err.message);
        }
    }

    async function logout() {
        const confirmed = await showConfirm('Logout?', 'Anda yakin ingin keluar dari akun leader?', 'Ya, Logout', 'Batal');
        if (!confirmed) return;

        try { await api('/auth/logout', { method: 'POST' }); } catch (_) {}

        clearUser();
        showToast('Logout berhasil', 'info');
        setTimeout(() => window.location.reload(), 600);
    }

    function updateUserInterface() {
        const $userInfo  = $('#userInfo');
        const $userName  = $('#userName');
        const $loginBtn  = $('#loginBtn');
        const $noticeBar = $('#noticeBar');
        const $registerBtnHeader = $('#registerBtnHeader');
        const $reportBtnHeader = $('#reportBtnHeader');

        if (state.currentUser) {
            const rawRole = String(state.currentUser.role || '').toLowerCase();
            const role    = normalizeRole(rawRole);

            $userName.text(state.currentUser.name);
            $userInfo.find('.user-avatar').text((state.currentUser.name || 'U').charAt(0).toUpperCase());
            $userInfo.css('background', '#dcfce7');
            $loginBtn.html('<i class="ti ti-logout"></i> Logout');
            $('#profileBtn').show();

            let label = '', color = '';

            if (rawRole === 'admin') {
                label = 'Admin';        color = '#7c3aed';
                $registerBtnHeader.show();
            } else if (role === 'manager') {
                label = 'Manager';      color = '#7c3aed';
                $registerBtnHeader.hide();
            } else if (role === 'group_leader') {
                label = 'Group Leader'; color = '#16a34a';
                $registerBtnHeader.hide();
            } else if (role === 'team_leader') {
                label = 'Team Leader';  color = '#0891b2';
                $registerBtnHeader.hide();
            } else {
                label = 'Operator';     color = '#2563eb';
                $registerBtnHeader.hide();
            }

            $reportBtnHeader.show();

            let hint = '';
            if (canApprove()) {
                hint = 'Bisa mengisi tindakan, deadline, PIC, drag & drop, dan approve task.';
            } else if (canEdit()) {
                hint = 'Bisa mengisi tindakan, deadline, PIC, dan drag & drop. Approve hanya Group Leader / Manager.';
            } else {
                hint = 'Hanya bisa menambah task baru dengan data dasar.';
            }

            $noticeBar.html(
                '<i class="ti ti-circle-check"></i>' +
                '<span>Login sebagai <strong style="color:' + color + '">' + label + '</strong>. ' + hint + '</span>'
            );
            $noticeBar.attr('class', 'notice-bar');
        } else {
            $userName.text('Operator');
            $userInfo.find('.user-avatar').text('O');
            $userInfo.css('background', '');
            $loginBtn.html('<i class="ti ti-lock"></i> Login Leader');
            $noticeBar.html(
                '<i class="ti ti-info-circle"></i>' +
                '<span>Anda login sebagai <strong>Operator</strong>. Hanya bisa menambah task baru dengan data dasar.</span>'
            );
            $noticeBar.attr('class', 'notice-bar warning');
            $registerBtnHeader.hide();
            $reportBtnHeader.hide();
            $('#profileBtn').hide();
        }
    }

    // ============================================================
    // SEARCH & FILTER
    // ============================================================
    function handleSearch(q) {
        state.searchQuery = (q || '').toLowerCase();
        render();
    }
    function handleFilter() {
        state.sectionFilter = $('#sectionFilter').val();
        state.dateFrom = $('#dateFrom').val() || null;
        state.dateTo = $('#dateTo').val() || null;
        detectActiveQuickDate();
        render();
    }
    function clearFilters() {
        state.searchQuery = '';
        state.sectionFilter = 'all';
        state.dateFrom = null;
        state.dateTo = null;
        $('#searchInput').val('');
        $('#sectionFilter').val('all');
        $('#dateFrom').val('');
        $('#dateTo').val('');
        setQuickDate('today');
        render();
    }

    function getFilteredTasks() {
        return state.tasks.filter(task => {
            if (task.status === 'cancelled') return false;

            // ── Search (cek semua field) ──
            if (state.searchQuery) {
                const s = [
                    task.operatorName,
                    task.section,
                    task.problem,
                    task.tempAction,
                    task.permAction,
                    task.pic,
                    task.taskCode,
                    CATEGORY_LABEL[task.category] || '',
                    PIC_SECTION_LABEL[task.picSection] || '',
                    task.stage,
                    task.status,
                ].join(' ').toLowerCase();
                if (!s.includes(state.searchQuery)) return false;
            }

            // ── Filter seksi ──
            if (state.sectionFilter !== 'all' && task.section !== state.sectionFilter) return false;

            // ── Filter tanggal (selalu aktif) ──
            if (state.dateFrom) {
                if (new Date(task.date) < new Date(state.dateFrom)) return false;
            }
            if (state.dateTo) {
                const to = new Date(state.dateTo);
                to.setHours(23, 59, 59);
                if (new Date(task.date) > to) return false;
            }

            return true;
        });
    }

    // ============================================================
    // DRAG & DROP
    // ============================================================
    function handleDragStart(e, taskId) {
        if (!canDragDrop()) {
            e.preventDefault();
            showToast('Hanya leader yang bisa drag & drop task!', 'warning');
            return false;
        }
        e.target.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/plain', String(taskId));
    }
    function handleDragOver(e) {
        if (!canDragDrop()) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        e.target.closest('.kanban-column')?.classList.add('drag-over');
    }
    function handleDragLeave(e) {
        e.target.closest('.kanban-column')?.classList.remove('drag-over');
    }

    async function handleDrop(e) {
        if (!canDragDrop()) {
            showToast('Hanya leader yang bisa drag & drop task!', 'warning');
            return;
        }
        e.preventDefault();
        const column = e.target.closest('.kanban-column');
        if (!column) return;

        const taskId = parseInt(e.dataTransfer.getData('text/plain'), 10);
        const newStage = column.dataset.stage;
        const task = state.tasks.find(t => t.id === taskId);

        cleanupDragUI();
        if (!task || task.stage === newStage) return;

        const oldStage = task.stage;

        // ══════════════════════════════════════════════════════
        // VALIDASI KHUSUS: CHECK → ACT
        // Semua field wajib sudah terisi
        // ══════════════════════════════════════════════════════
        if (oldStage === 'check' && newStage === 'act') {
            const required = [
                { key: 'category',     label: 'Category' },
                { key: 'picSection',   label: 'PIC Section' },
                { key: 'tempAction',   label: 'Tindakan Temporary' },
                { key: 'permAction',   label: 'Tindakan Permanent' },
                { key: 'deadline',     label: 'Deadline' },
                { key: 'pic',          label: 'PIC' },
            ];

            const missing = required
                .filter(r => !task[r.key] || String(task[r.key]).trim() === '')
                .map(r => r.label);

            if (task.doc4mStatus !== 'sudah')        missing.push('Dokumen 4M');
            if (task.docLogbookStatus !== 'sudah')   missing.push('Dokumen Logbook');
            if (task.docNursecallStatus !== 'sudah') missing.push('Dokumen Nursecall');

            if (missing.length > 0) {
                showAlert(
                    'warning',
                    'Form Belum Lengkap',
                    'Untuk memindahkan task ke <strong>ACT</strong>, semua field berikut harus diisi terlebih dahulu:<br><br>' +
                    '<ul style="text-align:left;padding-left:20px;margin:8px 0">' +
                    missing.map(m => `<li><strong>${m}</strong></li>`).join('') +
                    '</ul>' +
                    '<small style="color:#6b7280">Buka task → klik Edit → isi field yang kurang → Simpan → coba pindahkan lagi.</small>'
                );
                return;
            }
        }

        // ══════════════════════════════════════════════════════
        // LANJUT PROSES PINDAH STAGE
        // ══════════════════════════════════════════════════════
        task.stage = newStage;
        render();

        try {
            await api(`/tasks/${taskId}/stage`, { method: 'PATCH', body: { stage: newStage } });
            const label = STAGE_LABEL[newStage];
            showToast(`Task dipindahkan ke ${label.short}: ${label.title}`, 'success');
            await loadTasks();
        } catch (err) {
            task.stage = oldStage;
            render();
            showToast('Gagal memindahkan task: ' + err.message, 'error');
        }
    }

    function handleDragEnd() { cleanupDragUI(); }
    function cleanupDragUI() {
        document.querySelectorAll('.kanban-column').forEach(c => c.classList.remove('drag-over'));
        document.querySelectorAll('.kanban-card').forEach(c => c.classList.remove('dragging'));
    }

    // ══════════════════════════════════════════════════════════════
    // CANCELLED TASKS VIEWER
    // ══════════════════════════════════════════════════════════════
    function openCancelledModal() {
        let cancelled = state.tasks
            .filter(t => t.status === 'cancelled')
            .sort((a, b) => new Date(b.date) - new Date(a.date));

        if (state.dateFrom) {
            const from = new Date(state.dateFrom);
            from.setHours(0, 0, 0, 0);
            cancelled = cancelled.filter(t => {
                const d = new Date(t.date);
                d.setHours(0, 0, 0, 0);
                return d >= from;
            });
        }
        if (state.dateTo) {
            const to = new Date(state.dateTo);
            to.setHours(23, 59, 59, 999);
            cancelled = cancelled.filter(t => {
                const d = new Date(t.date);
                return d <= to;
            });
        }

        const $body = $('#cancelledTableBody');

        if (cancelled.length === 0) {
            $body.html(`
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:#9ca3af">
                        <i class="ti ti-inbox" style="font-size:42px;display:block;margin-bottom:10px;opacity:0.4"></i>
                        <div style="font-size:14px">Tidak ada task cancelled pada periode ini</div>
                        <div style="font-size:12px;margin-top:6px;color:#d1d5db">
                            Coba ubah filter tanggal di halaman utama
                        </div>
                    </td>
                </tr>
            `);
        } else {
            $body.html(cancelled.map((t, i) => `
                <tr>
                    <td style="text-align:center">${i + 1}</td>
                    <td><strong>${escapeHtml(t.taskCode || '-')}</strong></td>
                    <td>${t.date ? formatDate(t.date) : '-'}</td>
                    <td>${escapeHtml(t.operatorName || '-')}</td>
                    <td>${escapeHtml(t.section || '-')}</td>
                    <td style="max-width:320px;word-break:break-word">${escapeHtml(t.problem || '-')}</td>
                    <td style="text-align:center">
                        <span class="stage-pill">${(t.stage || '-').toUpperCase()}</span>
                    </td>
                    <td>${escapeHtml(t.pic || '-')}</td>
                    <td style="text-align:center">
                        <button class="btn btn-sm" onclick="closeModal('cancelledTasksModal'); openTaskReport(${t.id})" title="Lihat Report">
                            <i class="ti ti-file-description"></i>
                        </button>
                    </td>
                </tr>
            `).join(''));
        }

        openModal('cancelledTasksModal');
    }

    // Update counter cancelled di tombol
    function updateCancelledCount() {
        let n = state.tasks.filter(t => t.status === 'cancelled').length;

        if (state.dateFrom) {
            const from = new Date(state.dateFrom);
            from.setHours(0, 0, 0, 0);
            n = state.tasks.filter(t => {
                if (t.status !== 'cancelled') return false;
                const d = new Date(t.date);
                d.setHours(0, 0, 0, 0);
                return d >= from;
            }).length;
        }

        $('#cancelledCount').text(n);
    }

    // ══════════════════════════════════════════════════════════════
    // PHASE VISIBILITY — Tampilkan section sesuai stage task
    // ══════════════════════════════════════════════════════════════
    function applyPhaseVisibility(stage, options = {}) {
        const { isLeader = false } = options;

        const stageOrder = ['plan', 'do', 'check', 'act'];
        const idx = Math.max(0, stageOrder.indexOf(String(stage || 'plan').toLowerCase()));

        // Phase 1: Data Operator — selalu tampil (context)
        $('#phase1Section').show();

        // Phase 2: Klasifikasi + Dokumen — tampil saat stage >= do (Phase 2)
        const showPhase2 = isLeader && idx >= 1;
        $('#classificationSection').toggle(showPhase2);
        $('#documentsSection').toggle(showPhase2);

        // Phase 3: Tindakan — tampil saat stage >= check (Phase 3)
        const showPhase3 = isLeader && idx >= 2;
        $('#leaderSection').toggle(showPhase3);

        // Phase 4: Approval — tampil saat stage >= act (Phase 4)
        const showPhase4 = isLeader && idx >= 3;
        $('#approvalSection').toggle(showPhase4);
    }

    // ============================================================
    // TASK CRUD
    // ============================================================
    function openTaskModal() {
        const isLeader = canEdit();

        $('#modalTitle').text(isLeader ? 'Tambah Task Baru (Leader)' : 'Tambah Task Baru (Operator)');
        $('#taskForm')[0].reset();
        $('input[name="category"]').prop('checked', false);
        $('input[name="pic_section"]').prop('checked', false);

        // Reset dokumen ke "Belum"
        $('input[name="doc_status_4m"][value="belum"]').prop('checked', true);
        $('input[name="doc_status_logbook"][value="belum"]').prop('checked', true);
        $('input[name="doc_status_nursecall"][value="belum"]').prop('checked', true);

        taskDocStatus = { '4m': 'belum', 'logbook': 'belum', 'nursecall': 'belum' };

        $('#taskId').val('');
        $('#taskDate').val(new Date().toISOString().split('T')[0]);

        $('#tempAction').prop('disabled', !isLeader);
        $('#permAction').prop('disabled', !isLeader);
        $('#deadline').prop('disabled', !isLeader);
        $('#pic').prop('disabled', !isLeader);
        $('input[name="category"], input[name="pic_section"]').prop('disabled', !isLeader);
        $('input[name^="doc_status_"]').prop('disabled', !isLeader);

        // Panggil helper visibility SETELAH semua set value/disabled
        applyPhaseVisibility('plan', { isLeader });

        renderApprovalSection(null);

        $('#historyTaskBtn').hide();
        $('#reportTaskBtn').hide();
        $('#approveTaskBtn').hide();
        $('#cancelTaskBtn').hide();

        setSelect2Value($('#operatorName'), '', '');
        setSelect2Value($('#pic'), '', '');

        loadEmployees();
        loadSections();
        openModal('taskModal');
        setTimeout(() => updateApproveButtonState(), 50);
    }

    async function saveTask() {
        const id = $('#taskId').val();
        const isLeader = canEdit();

        const category   = $('input[name="category"]:checked').val() || '';
        const picSection = $('input[name="pic_section"]:checked').val() || '';

        const basePayload = {
            operator_name: $('#operatorName').val() ? $('#operatorName').val().trim() : '',
            task_date:     $('#taskDate').val(),
            section:       $('#section').val(),
            problem:       $('#problem').val() ? $('#problem').val().trim() : '',
        };

        if (isLeader) {
            basePayload.category    = category;
            basePayload.pic_section = picSection;
        }

        if (!basePayload.operator_name || !basePayload.task_date || !basePayload.section || !basePayload.problem) {
            showAlert('warning', 'Form Tidak Lengkap', 'Mohon isi semua field yang wajib!');
            return;
        }

        if (isLeader && (!category || !picSection)) {
            showAlert('warning', 'Klasifikasi Belum Lengkap', 'Pilih Category dan PIC Section terlebih dahulu.');
            return;
        }

        const leaderPayload = {
            temporary_action:      $('#tempAction').val().trim(),
            permanent_action:      $('#permAction').val().trim(),
            deadline:              $('#deadline').val() || null,
            pic:                   $('#pic').val() ? $('#pic').val().trim() : '',
            doc_4m_status:         $('input[name="doc_status_4m"]:checked').val()        || 'belum',
            doc_logbook_status:    $('input[name="doc_status_logbook"]:checked').val()   || 'belum',
            doc_nursecall_status:  $('input[name="doc_status_nursecall"]:checked').val() || 'belum',
        };

        Swal.fire({
            title: 'Menyimpan...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            if (id) {
                const updateBody = {
                    operator_name: basePayload.operator_name,
                    task_date:     basePayload.task_date,
                    section:       basePayload.section,
                    problem:       basePayload.problem,
                    ...(isLeader ? {
                        category:         category,
                        pic_section:      picSection,
                        temporary_action: leaderPayload.temporary_action,
                        permanent_action: leaderPayload.permanent_action,
                        deadline:         leaderPayload.deadline,
                        pic:              leaderPayload.pic,
                        doc_4m_status:        leaderPayload.doc_4m_status,
                        doc_logbook_status:   leaderPayload.doc_logbook_status,
                        doc_nursecall_status: leaderPayload.doc_nursecall_status,
                    } : {})
                };
                await api(`/tasks/${id}`, { method: 'PUT', body: updateBody });
                showToast('Task berhasil diupdate', 'success');
            } else {
                const created = await api('/tasks', { method: 'POST', body: basePayload });
                const newId = created?.id ?? created?.data?.id;

                if (isLeader && newId && (
                    leaderPayload.temporary_action || leaderPayload.permanent_action ||
                    leaderPayload.deadline || leaderPayload.pic ||
                    leaderPayload.doc_4m_status === 'sudah' ||
                    leaderPayload.doc_logbook_status === 'sudah' ||
                    leaderPayload.doc_nursecall_status === 'sudah'
                )) {
                    await api(`/tasks/${newId}`, { method: 'PUT', body: leaderPayload });
                }
                showToast('Task berhasil ditambahkan', 'success');
            }

            Swal.close();
            closeModal('taskModal');
            await loadTasks();
        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Menyimpan', err.message);
        }
    }

    async function editTask(id) {
        if (!canEdit()) {
            showConfirm('Akses Ditolak', 'Silakan login sebagai Leader untuk mengedit task ini.', 'Login Sekarang', 'Batal')
                .then(confirmed => { if (confirmed) openModal('loginModal'); });
            return;
        }

        const task = state.tasks.find(t => t.id === id);
        if (!task) return;

        $('#modalTitle').text('Edit Task');
        $('#taskId').val(task.id);

        // Set status dokumen dari task
        taskDocStatus = {
            '4m':        task.doc4mStatus        || 'belum',
            'logbook':   task.docLogbookStatus   || 'belum',
            'nursecall': task.docNursecallStatus || 'belum',
        };
        ['4m', 'logbook', 'nursecall'].forEach(t => {
            const status = taskDocStatus[t] || 'belum';
            $(`input[name="doc_status_${t}"][value="${status}"]`).prop('checked', true);
        });

        $('#approveTaskBtn').prop('disabled', true);

        await Promise.all([loadEmployees(), loadSections()]);

        setSelect2Value($('#operatorName'), task.operatorName, task.operatorName);
        setSelect2Value($('#pic'), task.pic, task.pic);

        const $sec = $('#section');
        const sec = task.section || '';
        const foundSec = $sec.find('option').filter(function () { return this.value === sec; }).length;
        if (!foundSec && sec) {
            $sec.append(`<option value="${escapeHtml(sec)}">${escapeHtml(sec)}</option>`);
        }
        $sec.val(sec);
        if ($sec.data('select2')) $sec.trigger('change.select2');

        $('#taskDate').val(task.date);
        $('#problem').val(task.problem);

        $('input[name="category"]').prop('checked', false);
        if (task.category) {
            $(`input[name="category"][value="${task.category}"]`).prop('checked', true);
        }
        $('input[name="pic_section"]').prop('checked', false);
        if (task.picSection) {
            $(`input[name="pic_section"][value="${task.picSection}"]`).prop('checked', true);
        }
        const isLeaderEdit = canEdit();
        $('input[name="category"], input[name="pic_section"]').prop('disabled', !isLeaderEdit);
        $('input[name^="doc_status_"]').prop('disabled', !isLeaderEdit);

        // Terapkan visibility sesuai stage task
        
        $('#tempAction').val(task.tempAction);
        $('#permAction').val(task.permAction);
        $('#deadline').val(task.deadline);
        
        $('#tempAction, #permAction, #deadline, #pic').prop('disabled', false);
        // $('#leaderSection').css('display', 'block');
        
        renderApprovalSection(task);
        applyPhaseVisibility(task.stage, { isLeader: isLeaderEdit });

        const isDone = task.status === 'done';
        const isCancelled = task.status === 'cancelled';
        const isAct = task.stage === 'act';

        if (isAct && !isDone && !isCancelled && canApprove()) {
            $('#approveTaskBtn').show().prop('disabled', true);
        } else {
            $('#approveTaskBtn').hide();
        }

        if (!isDone && !isCancelled) $('#cancelTaskBtn').show();
        else $('#cancelTaskBtn').hide();

        $('#historyTaskBtn').show();
        $('#reportTaskBtn').show();

        if (isDone || isCancelled) {
            $('#operatorName, #taskDate, #section, #problem, #tempAction, #permAction, #deadline, #pic').prop('disabled', true);
            $('input[name^="doc_status_"]').prop('disabled', true);
            $('#saveBtn').prop('disabled', true);
        } else {
            $('#saveBtn').prop('disabled', false);
        }

        openModal('taskModal');
        setTimeout(() => updateApproveButtonState(), 50);
    }

    async function deleteTask(id) {
        if (!canDragDrop()) {
            const confirmed = await showConfirm('Akses Ditolak', 'Silakan login sebagai leader untuk menghapus task ini.', 'Login Sekarang', 'Batal');
            if (confirmed) openModal('loginModal');
            return;
        }

        const task = state.tasks.find(t => t.id === id);
        const title = task ? escapeHtml(task.problem) : 'task ini';

        const confirmed = await showConfirm(
            'Hapus Task?',
            `Anda yakin ingin menghapus task "<strong>${title}</strong>"?<br><small style="color:#6b7280">Tindakan ini tidak bisa dibatalkan.</small>`,
            'Ya, Hapus', 'Batal', 'warning'
        );
        if (!confirmed) return;

        try {
            await api(`/tasks/${id}`, { method: 'DELETE' });
            state.tasks = state.tasks.filter(t => t.id !== id);
            render();
            showToast('Task berhasil dihapus', 'success');
        } catch (err) {
            showAlert('error', 'Gagal Menghapus', err.message);
        }
    }

    async function approveTask() {
        if (!canApprove()) {
            showAlert('warning', 'Akses Ditolak', 'Hanya Group Leader & Manager yang bisa approve task.');
            return;
        }
        const id = $('#taskId').val();
        if (!id) return;

        // Cek status dari radio
        const missing = [];
        ['4m', 'logbook', 'nursecall'].forEach(t => {
            const status = $(`input[name="doc_status_${t}"]:checked`).val() || 'belum';
            if (status !== 'sudah') missing.push(DOC_LABEL[t] || t.toUpperCase());
        });

        if (missing.length > 0) {
            showAlert(
                'warning',
                'Dokumen Belum Lengkap',
                'Tandai "Sudah" untuk dokumen berikut sebelum approve:<br><br>' +
                '<ul style="text-align:left;padding-left:20px;margin:8px 0">' +
                missing.map(m => `<li><strong>${m}</strong></li>`).join('') +
                '</ul>'
            );
            return;
        }

        const task = state.tasks.find(t => t.id === id);
        const title = task ? escapeHtml(task.problem) : 'task ini';

        const confirmed = await showConfirm(
            'Approve Task?',
            `Task "<strong>${title}</strong>" akan ditandai <strong>DONE</strong> dan tidak bisa diubah lagi.<br><br>
            <small style="color:#6b7280">Tanda tangan: <strong>${escapeHtml(state.currentUser?.name || 'Leader')}</strong></small>`,
            'Ya, Approve', 'Batal', 'question'
        );
        if (!confirmed) return;

        Swal.fire({
            title: 'Memproses...',
            html: 'Menyimpan approval',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            await api(`/tasks/${id}/approve`, { method: 'PATCH' });
            Swal.close();
            closeModal('taskModal');
            showToast('Task berhasil di-approve', 'success');
            await loadTasks();
        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Approve', err.message);
        }
    }

    async function cancelTask() {
        const id = $('#taskId').val();
        if (!id) return;

        const task = state.tasks.find(t => t.id === id);
        const title = task ? escapeHtml(task.problem) : 'task ini';

        const confirmed = await showConfirm(
            'Batalkan Task?',
            `Task "<strong>${title}</strong>" akan ditandai <strong>CANCELLED</strong>.<br><br>
            <small style="color:#6b7280">Task tidak akan dihitung sebagai task aktif.</small>`,
            'Ya, Batalkan', 'Batal', 'warning'
        );
        if (!confirmed) return;

        Swal.fire({
            title: 'Memproses...',
            html: 'Membatalkan task',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            await api(`/tasks/${id}/cancel`, { method: 'PATCH' });
            Swal.close();
            closeModal('taskModal');
            showToast('Task berhasil dibatalkan', 'info');
            await loadTasks();
        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Cancel', err.message);
        }
    }

    async function viewHistory() {
        const id = $('#taskId').val();
        if (!id) return;

        const task = state.tasks.find(t => t.id === id);
        const title = task ? escapeHtml(task.problem) : 'Task';

        Swal.fire({
            title: 'Memuat history...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        let histories = [];
        try {
            const res = await api(`/tasks/${id}/history`);
            histories = Array.isArray(res) ? res : (res?.data || []);
        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Memuat History', err.message);
            return;
        }

        Swal.close();

        const iconMap = {
            'create':    { icon: 'ti-plus',         color: '#2563eb', bg: '#eff6ff', label: 'Dibuat' },
            'update':    { icon: 'ti-pencil',       color: '#7c3aed', bg: '#f5f3ff', label: 'Diupdate' },
            'move_stage':{ icon: 'ti-arrow-right',  color: '#0891b2', bg: '#ecfeff', label: 'Pindah Stage' },
            'approve':   { icon: 'ti-circle-check', color: '#16a34a', bg: '#f0fdf4', label: 'Approved' },
            'cancel':    { icon: 'ti-circle-x',     color: '#dc2626', bg: '#fef2f2', label: 'Cancelled' },
            'delete':    { icon: 'ti-trash',        color: '#dc2626', bg: '#fef2f2', label: 'Dihapus' },
            'restore':   { icon: 'ti-restore',      color: '#16a34a', bg: '#f0fdf4', label: 'Direstore' },
        };

        if (histories.length === 0) {
            Swal.fire({
                title: 'History Task',
                html: `<div style="text-align:center;padding:20px;color:#9ca3af">
                    <i class="ti ti-history" style="font-size:48px;opacity:0.3"></i>
                    <div style="margin-top:12px">Belum ada history untuk task ini</div>
                </div>`,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#2563eb'
            });
            return;
        }

        window._historyData = histories; // simpan untuk expand

        const timelineHtml = histories.map((h, idx) => {
            const meta = iconMap[h.action] || { icon: 'ti-activity', color: '#6b7280', bg: '#f3f4f6', label: h.action };
            const isLast = idx === histories.length - 1;

            let changeHtml = '';
            if (h.oldStage && h.newStage && h.oldStage !== h.newStage) {
                changeHtml += `<div style="font-size:12px;margin-top:4px;color:#374151">
                    <span style="background:#f3f4f6;padding:2px 6px;border-radius:4px">${(h.oldStage || '').toUpperCase()}</span>
                    <i class="ti ti-arrow-right" style="margin:0 6px;color:#9ca3af"></i>
                    <span style="background:#dbeafe;padding:2px 6px;border-radius:4px;color:#1e40af;font-weight:600">${(h.newStage || '').toUpperCase()}</span>
                </div>`;
            }
            if (h.oldStatus && h.newStatus && h.oldStatus !== h.newStatus) {
                changeHtml += `<div style="font-size:12px;margin-top:4px;color:#374151">
                    <span style="background:#f3f4f6;padding:2px 6px;border-radius:4px">${(h.oldStatus || '').toUpperCase()}</span>
                    <i class="ti ti-arrow-right" style="margin:0 6px;color:#9ca3af"></i>
                    <span style="background:#dcfce7;padding:2px 6px;border-radius:4px;color:#166534;font-weight:600">${(h.newStatus || '').toUpperCase()}</span>
                </div>`;
            }

            // Cek apakah ada diff yang bisa ditampilkan
            const oldV = h.oldValues || h.old_values;
            const newV = h.newValues || h.new_values;
            const hasDiff = !!(oldV || newV);

            const toggleBtn = hasDiff
                ? `<button type="button" onclick="toggleHistoryDetail(${idx})"
                        id="histToggleBtn${idx}"
                        style="margin-top:8px;background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;padding:4px 10px;border-radius:6px;cursor:pointer;font-size:11px;font-weight:600;display:inline-flex;align-items:center;gap:4px">
                        <i class="ti ti-chevron-down" id="histToggleIcon${idx}"></i> Lihat Perubahan
                </button>
                <div id="histDetail${idx}" style="display:none;margin-top:8px"></div>`
                : '';

            return `<div style="display:flex;gap:12px;position:relative;padding-bottom:${isLast ? '0' : '20px'}">
                <div style="flex-shrink:0;position:relative">
                    <div style="width:36px;height:36px;border-radius:50%;background:${meta.bg};color:${meta.color};display:flex;align-items:center;justify-content:center;position:relative;z-index:2">
                        <i class="ti ${meta.icon}" style="font-size:18px"></i>
                    </div>
                    ${!isLast ? `<div style="position:absolute;left:50%;top:36px;bottom:-20px;width:2px;background:#e5e7eb;transform:translateX(-50%)"></div>` : ''}
                </div>
                <div style="flex:1;padding-top:2px">
                    <div style="display:flex;justify-content:space-between;align-items:start;gap:8px">
                        <div style="font-weight:600;color:${meta.color};font-size:13px">${meta.label}</div>
                        <div style="font-size:11px;color:#9ca3af;white-space:nowrap">${formatDateTime(h.createdAt)}</div>
                    </div>
                    <div style="font-size:12px;color:#6b7280;margin-top:2px">
                        oleh <strong style="color:#374151">${escapeHtml(h.changedByName || '-')}</strong>
                        ${h.changedByRole ? `<span style="background:#f3f4f6;padding:1px 6px;border-radius:6px;font-size:10px;margin-left:4px">${escapeHtml(h.changedByRole)}</span>` : ''}
                    </div>
                    ${changeHtml}
                    ${h.notes ? `<div style="font-size:12px;color:#6b7280;margin-top:6px;font-style:italic">${escapeHtml(h.notes)}</div>` : ''}
                    ${toggleBtn}
                </div>
            </div>`;
        }).join('');

        Swal.fire({
            title: 'History Task',
            html: `<div style="text-align:left;max-height:65vh;overflow-y:auto;padding:4px 8px">
                <div style="background:#f9fafb;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px">
                    <div style="color:#6b7280;margin-bottom:4px">Task:</div>
                    <div style="font-weight:600;color:#111827">${title}</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:4px">
                        <i class="ti ti-hash"></i> ${escapeHtml(task?.taskCode || '-')}
                    </div>
                </div>
                <div>${timelineHtml}</div>
            </div>`,
            width: 700,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#2563eb'
        });
    }

    // Toggle expand detail
    function toggleHistoryDetail(idx) {
        const $detail = document.getElementById('histDetail' + idx);
        const $icon = document.getElementById('histToggleIcon' + idx);
        const $btn = document.getElementById('histToggleBtn' + idx);
        if (!$detail) return;

        const isOpen = $detail.style.display !== 'none';

        if (isOpen) {
            $detail.style.display = 'none';
            if ($icon) $icon.className = 'ti ti-chevron-down';
            if ($btn) $btn.innerHTML = '<i class="ti ti-chevron-down" id="histToggleIcon' + idx + '"></i> Lihat Perubahan';
        } else {
            if ($detail.innerHTML.trim() === '') {
                const h = window._historyData[idx];
                $detail.innerHTML = buildHistoryDetailHtml(h);
            }
            $detail.style.display = 'block';
            if ($btn) $btn.innerHTML = '<i class="ti ti-chevron-up" id="histToggleIcon' + idx + '"></i> Sembunyikan';
        }
    }

    function buildHistoryDetailHtml(h) {
        const fieldLabels = {
            operator_name:     'Nama Operator',
            task_date:         'Tanggal',
            section:           'Seksi',
            problem:           'Masalah',
            category:          'Category',
            pic_section:       'PIC Section',
            temporary_action:  'Tindakan Temporary',
            permanent_action:  'Tindakan Permanent',
            deadline:          'Deadline',
            pic:               'PIC',
            stage:             'Stage',
            status:            'Status',
        };

        let oldVals = h.oldValues || h.old_values || null;
        let newVals = h.newValues || h.new_values || null;

        const tryParse = v => {
            if (typeof v !== 'string') return v;
            try { return JSON.parse(v); } catch { return v; }
        };
        oldVals = tryParse(oldVals);
        newVals = tryParse(newVals);

        if (!oldVals && !newVals) {
            return `<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:6px;padding:10px;font-size:12px;color:#92400e">
                <i class="ti ti-alert-triangle"></i> Detail perubahan tidak tersedia.
                Backend belum menyimpan <code>old_values</code> / <code>new_values</code>.
            </div>`;
        }

        const keys = new Set([
            ...Object.keys(oldVals || {}),
            ...Object.keys(newVals || {})
        ]);

        const rows = [];
        keys.forEach(k => {
            const ov = (oldVals || {})[k];
            const nv = (newVals || {})[k];
            if (JSON.stringify(ov) === JSON.stringify(nv)) return;
            rows.push({ key: k, ov, nv });
        });

        if (rows.length === 0) {
            return `<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:10px;font-size:12px;color:#166534">
                <i class="ti ti-check"></i> Tidak ada perubahan nilai (kemungkinan save tanpa ubah).
            </div>`;
        }

        const tableRows = rows.map(r => {
            const label = fieldLabels[r.key] || r.key;
            const ovStr = r.ov == null || r.ov === '' ? '-' : String(r.ov);
            const nvStr = r.nv == null || r.nv === '' ? '-' : String(r.nv);
            return `<tr>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;background:#f9fafb;font-weight:600;color:#374151;font-size:11px;width:28%;vertical-align:top">${escapeHtml(label)}</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;color:#991b1b;background:#fef2f2;font-size:11px;word-break:break-word;vertical-align:top">${escapeHtml(ovStr)}</td>
                <td style="padding:6px 8px;border:1px solid #e5e7eb;color:#166534;background:#f0fdf4;font-size:11px;word-break:break-word;vertical-align:top">${escapeHtml(nvStr)}</td>
            </tr>`;
        }).join('');

        return `<table style="width:100%;border-collapse:collapse">
            <thead>
                <tr>
                    <th style="padding:6px 8px;border:1px solid #e5e7eb;background:#f3f4f6;text-align:left;font-size:10px;text-transform:uppercase;color:#6b7280">Field</th>
                    <th style="padding:6px 8px;border:1px solid #e5e7eb;background:#fee2e2;text-align:left;font-size:10px;text-transform:uppercase;color:#991b1b">Sebelum</th>
                    <th style="padding:6px 8px;border:1px solid #e5e7eb;background:#dcfce7;text-align:left;font-size:10px;text-transform:uppercase;color:#166534">Sesudah</th>
                </tr>
            </thead>
            <tbody>${tableRows}</tbody>
        </table>`;
    }

    // ============================================================
    // REPORT
    // ============================================================
    let reportData = null;

    function openReport() {
        if (!canDragDrop()) {
            showAlert('warning', 'Akses Ditolak', 'Silakan login sebagai Group leader/manager untuk melihat report.');
            return;
        }

        loadSections();

        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        const fmt = d => d.toISOString().split('T')[0];
        if (!$('#reportDateFrom').val()) $('#reportDateFrom').val(fmt(firstDay));
        if (!$('#reportDateTo').val()) $('#reportDateTo').val(fmt(lastDay));

        openModal('reportModal');
        loadReportData();
    }

    async function loadReportData() {
        Swal.fire({
            title: 'Memuat laporan...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await api('/tasks/report', {
                query: {
                    date_from: $('#reportDateFrom').val() || '',
                    date_to: $('#reportDateTo').val() || '',
                    section: $('#reportSection').val(),
                    stage: $('#reportStage').val(),
                    status: $('#reportStatus').val(),
                }
            });

            reportData = res;
            renderReport(res);
            Swal.close();
        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Memuat Laporan', err.message);
        }
    }

    function renderReport(data) {
        const summary = data.summary || {};
        const tasks = data.tasks || [];
        const period = data.period || {};

        let periodText = 'Periode: Semua Data';
        if (period.from && period.to) periodText = `Periode: ${formatDate(period.from)} — ${formatDate(period.to)}`;
        else if (period.from) periodText = `Periode: Sejak ${formatDate(period.from)}`;
        else if (period.to) periodText = `Periode: Sampai ${formatDate(period.to)}`;
        $('#reportPeriod').text(periodText);

        const byStage = summary.by_stage || {};
        $('#reportSummary').html(`
            <div class="report-stat-card plan"><div class="report-stat-label">Phase 1</div><div class="report-stat-value">${byStage.plan || 0}</div></div>
            <div class="report-stat-card do"><div class="report-stat-label">Phase 2</div><div class="report-stat-value">${byStage.do || 0}</div></div>
            <div class="report-stat-card check"><div class="report-stat-label">Phase 3</div><div class="report-stat-value">${byStage.check || 0}</div></div>
            <div class="report-stat-card act"><div class="report-stat-label">Phase 4</div><div class="report-stat-value">${byStage.act || 0}</div></div>
        `);

        const bySection = summary.by_section || {};
        const sectionKeys = Object.keys(bySection).sort((a, b) => bySection[b] - bySection[a]);
        const maxSection = Math.max(1, ...Object.values(bySection));

        let sectionHtml = `<div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;border-bottom:1px solid #e5e7eb;padding-bottom:6px">Distribusi per Seksi</div>`;
        if (sectionKeys.length === 0) {
            sectionHtml += `<div style="text-align:center;color:#9ca3af;font-size:12px;padding:12px">Tidak ada data</div>`;
        } else {
            sectionKeys.forEach(key => {
                const count = bySection[key];
                const pct = Math.round((count / maxSection) * 100);
                sectionHtml += `<div class="section-breakdown-item">
                    <div style="width:140px;font-weight:500;color:#374151">${escapeHtml(key)}</div>
                    <div class="section-breakdown-bar"><div style="width:${pct}%"></div></div>
                    <div style="width:40px;text-align:right;font-weight:600;color:#2563eb">${count}</div>
                </div>`;
            });
        }
        $('#reportSectionBreakdown').html(sectionHtml);

        const byStatus = summary.by_status || {};
        let tableHtml = '';
        if (tasks.length === 0) {
            tableHtml = `<div style="text-align:center;color:#9ca3af;font-size:12px;padding:20px;border:1px dashed #e5e7eb;border-radius:8px">Tidak ada task pada periode ini</div>`;
        } else {
            tableHtml = `<table class="report-table">
                <thead><tr>
                    <th style="width:30px">No</th>
                    <th style="width:130px">Kode</th>
                    <th style="width:90px">Tanggal</th>
                    <th style="width:100px">Seksi</th>
                    <th style="width:80px">Category</th>
                    <th style="width:70px">PIC Sec</th>
                    <th>Masalah</th>
                    <th style="width:70px">Stage</th>
                    <th style="width:90px">Status</th>
                    <th style="width:90px">PIC</th>
                </tr></thead>
                <tbody>
                    ${tasks.map((t, i) => `<tr>
                        <td style="text-align:center">${i + 1}</td>
                        <td><strong>${escapeHtml(t.taskCode)}</strong></td>
                        <td>${formatDate(t.date)}</td>
                        <td>${escapeHtml(t.section)}</td>
                        <td style="text-align:center"><span class="card-tag cat-${t.category || ''}">${escapeHtml(CATEGORY_LABEL[t.category] || '-')}</span></td>
                        <td style="text-align:center"><span class="picsec-badge">${escapeHtml(PIC_SECTION_LABEL[t.picSection] || '-')}</span></td>
                        <td>${escapeHtml(t.problem)}</td>
                        <td style="text-align:center"><span class="stage-pill">${t.stage}</span></td>
                        <td style="text-align:center"><span class="status-pill status-${t.status}">${t.status}</span></td>
                        <td>${escapeHtml(t.pic || '-')}</td>
                    </tr>`).join('')}
                </tbody>
                <tfoot><tr>
                    <td colspan="4" style="text-align:right;font-weight:600">TOTAL</td>
                    <td colspan="4" style="font-weight:600">
                        ${tasks.length} task — 
                        <span class="status-pill status-done">${byStatus.done || 0} done</span>
                        <span class="status-pill status-in_progress">${byStatus.in_progress || 0} progress</span>
                        <span class="status-pill status-open">${byStatus.open || 0} open</span>
                        <span class="status-pill status-cancelled">${byStatus.cancelled || 0} cancelled</span>
                    </td>
                </tr></tfoot>
            </table>`;
        }
        $('#reportTableWrapper').html(tableHtml);

        const creatorName = state.currentUser?.name || '-';
        $('#reportCreatedBy').text(creatorName);
        $('#reportGeneratedAt').text(new Date().toLocaleString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        }));

        setTimeout(() => {
            renderSignatureQRCodes('#reportContent', {
                'report-creator': creatorName !== '-' ? creatorName : '',
                'report-checker': '',
                'report-approver': '',
            });
        }, 200);
    }

    // ============================================================
    // EXPORT PDF (Report Umum)
    // ============================================================
    async function exportPDF() {
        const original = document.getElementById('reportContent');
        if (!original) return;

        const creatorName = (state.currentUser?.name || '').trim() || '-';
        const elCreator = document.getElementById('reportCreatedBy');
        if (elCreator) elCreator.textContent = creatorName;

        renderSignatureQRCodes('#reportContent', {
            'report-creator': creatorName === '-' ? '' : creatorName,
            'report-checker': '',
            'report-approver': '',
        });

        await new Promise(r => setTimeout(r, 400));

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

        const wrapper = document.createElement('div');
        wrapper.style.cssText = [
            'position: absolute',
            'left: -99999px',
            'top: 0',
            'width: 794px',
            'background: #ffffff',
            'padding: 0',
            'margin: 0',
            'z-index: -1',
            'box-sizing: border-box',
            'font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", sans-serif'
        ].join(';');

        const clone = original.cloneNode(true);
        clone.removeAttribute('style');
        clone.style.cssText = 'background:#ffffff; padding:24px; border:none; border-radius:0; width:794px; box-sizing:border-box;';

        wrapper.appendChild(clone);
        document.body.appendChild(wrapper);

        await new Promise(r => setTimeout(r, 400));

        const periodFrom = $('#reportDateFrom').val() || 'all';
        const periodTo = $('#reportDateTo').val() || 'all';
        const filename = `laporan-pdca-${periodFrom}-${periodTo}.pdf`;

        Swal.fire({
            title: 'Menyiapkan PDF...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const fullHeight = clone.scrollHeight;
            wrapper.style.height = fullHeight + 'px';

            const canvas = await html2canvas(clone, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                width: clone.offsetWidth,
                height: fullHeight,
                windowWidth: clone.offsetWidth,
                windowHeight: fullHeight,
                scrollX: 0,
                scrollY: 0
            });

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

            const pdfWidth  = 210;
            const pdfHeight = 297;
            const margin    = 8;
            const contentWidth  = pdfWidth  - (margin * 2);
            const contentHeight = pdfHeight - (margin * 2);

            const scaleW = contentWidth  / canvas.width;
            const scaleH = contentHeight / canvas.height;
            const scale  = Math.min(scaleW, scaleH);

            const imgWidth  = canvas.width  * scale;
            const imgHeight = canvas.height * scale;

            const posX = margin + (contentWidth - imgWidth) / 2;
            const posY = margin;

            const imgData = canvas.toDataURL('image/jpeg', 0.95);
            pdf.addImage(imgData, 'JPEG', posX, posY, imgWidth, imgHeight);
            if (pdf.getNumberOfPages() > 1) {
                pdf.deletePage(pdf.getNumberOfPages());
            }
            pdf.save(filename);
            Swal.close();
            showToast('PDF berhasil diunduh', 'success');
        } catch (err) {
            Swal.close();
            console.error('[exportPDF] error:', err);
            showAlert('error', 'Gagal Export PDF', err.message || 'Error tidak dikenal');
        } finally {
            if (wrapper.parentNode) wrapper.parentNode.removeChild(wrapper);
        }
    }

    // ============================================================
    // PRINT REPORT
    // ============================================================
    function printReport() {
        const modal = document.getElementById('reportModal');
        if (!modal) return;

        document.body.classList.add('print-report');

        setTimeout(() => {
            window.print();
            setTimeout(() => {
                document.body.classList.remove('print-report');
            }, 1000);
        }, 150);
    }

    // ============================================================
    // TASK REPORT (Per Task)
    // ============================================================
    function openTaskReportFromEdit() {
        const id = $('#taskId').val();
        if (!id) return;
        openTaskReport(parseInt(id, 10));
    }

    function openTaskReport(id) {
        const task = state.tasks.find(t => t.id === id);
        if (!task) {
            showAlert('error', 'Error', 'Task tidak ditemukan');
            return;
        }

        $('#trTaskCode').text(task.taskCode || '-');
        $('#trOperatorName').text(task.operatorName || '-');
        $('#trTaskDate').text(task.date ? formatDateLong(task.date) : '-');
        $('#trSection').text(task.section || '-');
        $('#trPic').text(task.pic || '-');
        $('#trCategory').html(
            task.category
                ? `<span class="card-tag cat-${task.category}">${escapeHtml(CATEGORY_LABEL[task.category] || task.category)}</span>`
                : '-'
        );
        $('#trPicSection').html(
            task.picSection
                ? `<span class="picsec-badge">${escapeHtml(PIC_SECTION_LABEL[task.picSection] || task.picSection)}</span>`
                : '-'
        );
        $('#trStage').text((task.stage || '-').toUpperCase());
        $('#trStatus').text((task.status || '-').toUpperCase());
        $('#trDeadline').text(task.deadline ? formatDateLong(task.deadline) : '-');
        $('#trCreatedAt').text(task.createdAt ? formatDateTime(task.createdAt) : '-');

        let badges = [];
        const isDone = task.status === 'done';
        const isCancelled = task.status === 'cancelled';
        const isInProgress = task.status === 'in_progress';
        const docList = [
            { type: '4m',        label: '4M',        status: task.doc4mStatus },
            { type: 'logbook',   label: 'Logbook',   status: task.docLogbookStatus },
            { type: 'nursecall', label: 'Nursecall', status: task.docNursecallStatus },
        ];
        $('#trAttachmentSection').show();
        $('#trAttachmentList').html(docList.map(d => {
            const ok = d.status === 'sudah';
            return `<div style="border:1px solid ${ok ? '#86efac' : '#fecaca'};border-radius:6px;padding:8px;text-align:center;background:${ok ? '#f0fdf4' : '#fef2f2'}">
                <i class="ti ${ok ? 'ti-circle-check' : 'ti-circle-x'}"
                style="font-size:20px;display:block;margin-bottom:4px;color:${ok ? '#16a34a' : '#dc2626'}"></i>
                <div style="font-weight:600;color:${ok ? '#166534' : '#991b1b'};font-size:12px">${d.label}</div>
                <div style="font-size:10px;margin-top:2px;color:${ok ? '#15803d' : '#b91c1c'}">${ok ? 'Sudah' : 'Belum'}</div>
            </div>`;
        }).join(''));

        if (isDone) badges.push(`<span class="badge-status badge-approved"><i class="ti ti-circle-check"></i> APPROVED</span>`);
        else if (isCancelled) badges.push(`<span class="badge-status badge-cancelled"><i class="ti ti-circle-x"></i> CANCELLED</span>`);
        else if (task.stage === 'act' && isInProgress) badges.push(`<span class="badge-status badge-pending-approval"><i class="ti ti-clock-hour-4"></i> MENUNGGU APPROVAL</span>`);
        else if (isInProgress) badges.push(`<span class="badge-status badge-in-progress"><i class="ti ti-bolt"></i> IN PROGRESS</span>`);
        else badges.push(`<span class="badge-status" style="background:#dbeafe;color:#1e40af;border:1px solid #93c5fd"><i class="ti ti-flag"></i> OPEN</span>`);
        badges.push(`<span class="stage-pill">STAGE: ${(task.stage || '').toUpperCase()}</span>`);
        $('#trStatusBadges').html(badges.join(''));

        $('#trProblem').html(task.problem ? escapeHtml(task.problem) : '<span class="tr-empty">Tidak ada deskripsi masalah</span>');
        $('#trTempAction').html(task.tempAction ? escapeHtml(task.tempAction) : '<span class="tr-empty">Belum ada tindakan temporary</span>');
        $('#trPermAction').html(task.permAction ? escapeHtml(task.permAction) : '<span class="tr-empty">Belum ada tindakan permanent</span>');

        if (task.approvedAt || task.leaderSignature) {
            $('#trApprovalSection').show();
            $('#trApprovalContent').html(`
                <div class="tr-approval-grid">
                    <div><span class="tr-label-inline">Tanda Tangan:</span> <strong>${escapeHtml(task.leaderSignature || '-')}</strong></div>
                    <div><span class="tr-label-inline">Waktu Approval:</span> <strong>${task.approvedAt ? formatDateTime(task.approvedAt) : '-'}</strong></div>
                </div>
            `);
        } else {
            $('#trApprovalSection').hide();
        }

        const isApproved = (task.status === 'done') || !!task.approvedAt || !!task.leaderSignature;

        const sigOperator = task.operatorName || '';
        const sigPic = task.pic || '';
        const sigLeader = isApproved ? (task.leaderSignature || task.approvedByName || 'Approved') : '';

        $('#trSignOperator').text(sigOperator || '-');
        $('#trSignPic').text(sigPic || '-');
        $('#trSignLeader').text(sigLeader || '-');

        $('#trGeneratedAt').text(new Date().toLocaleString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        }));

        openModal('taskReportModal');

        setTimeout(() => {
            renderSignatureQRCodes('#taskReportContent', {
                operator: sigOperator,
                pic: sigPic,
                leader: sigLeader,
            });
        }, 200);
    }

    // ============================================================
    // EXPORT TASK PDF
    // ============================================================
    async function exportTaskPDF() {
        const original = document.getElementById('taskReportContent');
        if (!original) return;

        const id = parseInt($('#taskId').val() || '0', 10);
        const task = state.tasks.find(t => t.id === id);

        let sigOperator = '-';
        let sigPic = '-';
        let sigLeader = '-';

        if (task) {
            const isApproved = (task.status === 'done') || !!task.approvedAt || !!task.leaderSignature;
            sigOperator = (task.operatorName || '').trim() || '-';
            sigPic = (task.pic || '').trim() || '-';
            sigLeader = isApproved ? ((task.leaderSignature || task.approvedByName || 'Approved').trim()) : '-';
        }

        const elOp = document.getElementById('trSignOperator');
        const elPic = document.getElementById('trSignPic');
        const elLead = document.getElementById('trSignLeader');
        if (elOp) elOp.textContent = sigOperator;
        if (elPic) elPic.textContent = sigPic;
        if (elLead) elLead.textContent = sigLeader;

        renderSignatureQRCodes('#taskReportContent', {
            operator: sigOperator === '-' ? '' : sigOperator,
            pic: sigPic === '-' ? '' : sigPic,
            leader: sigLeader === '-' ? '' : sigLeader,
        });

        await new Promise(r => setTimeout(r, 400));

        const qrCanvases = original.querySelectorAll('.signature-qrcode canvas');
        qrCanvases.forEach(canvas => {
            try {
                const dataURL = canvas.toDataURL('image/png');
                const img = document.createElement('img');
                img.src = dataURL;
                img.style.cssText = 'display:block;width:80px;height:80px;margin:0 auto 6px;';
                canvas.parentNode.replaceChild(img, canvas);
            } catch (e) {
                console.warn('[export] canvas toDataURL gagal:', e);
            }
        });

        const wrapper = document.createElement('div');
        wrapper.style.cssText = [
            'position: absolute',
            'left: -99999px',
            'top: 0',
            'width: 794px',
            'background: #ffffff',
            'padding: 0',
            'margin: 0',
            'z-index: -1',
            'box-sizing: border-box',
            'font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Inter", sans-serif'
        ].join(';');

        const clone = original.cloneNode(true);
        clone.removeAttribute('style');
        clone.style.cssText = 'background:#ffffff; padding:24px; border:none; border-radius:0; width:794px; box-sizing:border-box;';

        wrapper.appendChild(clone);
        document.body.appendChild(wrapper);

        await new Promise(r => setTimeout(r, 400));

        const taskCode = $('#trTaskCode').text() || 'task';
        const filename = `laporan-${taskCode}.pdf`;

        Swal.fire({
            title: 'Menyiapkan PDF...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const fullHeight = clone.scrollHeight;
            wrapper.style.height = fullHeight + 'px';

            const canvas = await html2canvas(clone, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                logging: false,
                width: clone.offsetWidth,
                height: fullHeight,
                windowWidth: clone.offsetWidth,
                windowHeight: fullHeight,
                scrollX: 0,
                scrollY: 0
            });

            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4'
            });

            const pdfWidth  = 210;
            const pdfHeight = 297;
            const margin    = 8;
            const contentWidth  = pdfWidth  - (margin * 2);
            const contentHeight = pdfHeight - (margin * 2);

            const scaleW = contentWidth  / canvas.width;
            const scaleH = contentHeight / canvas.height;
            const scale  = Math.min(scaleW, scaleH);

            const imgWidth  = canvas.width  * scale;
            const imgHeight = canvas.height * scale;

            const posX = margin + (contentWidth - imgWidth) / 2;
            const posY = margin;

            const imgData = canvas.toDataURL('image/jpeg', 0.95);
            pdf.addImage(imgData, 'JPEG', posX, posY, imgWidth, imgHeight);
            if (pdf.getNumberOfPages() > 1) {
                pdf.deletePage(pdf.getNumberOfPages());
            }
            pdf.save(filename);

            Swal.close();
            showToast('PDF berhasil diunduh', 'success');
        } catch (err) {
            Swal.close();
            console.error('[exportTaskPDF] error:', err);
            showAlert('error', 'Gagal Export PDF', err.message || 'Error tidak dikenal');
        } finally {
            if (wrapper.parentNode) wrapper.parentNode.removeChild(wrapper);
        }
    }

    // ============================================================
    // PRINT TASK REPORT
    // ============================================================
    function printTaskReport() {
        const modal = document.getElementById('taskReportModal');
        if (!modal) return;

        document.body.classList.add('print-task-report');

        setTimeout(() => {
            window.print();
            setTimeout(() => {
                document.body.classList.remove('print-task-report');
            }, 1000);
        }, 150);
    }

    // ============================================================
    // HELPERS
    // ============================================================
    function formatDateLong(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function viewTask(id) {
        const task = state.tasks.find(t => t.id === id);
        if (!task) return;

        const approvalInfo = task.approvedAt ? `
            <hr style="margin:12px 0;border:none;border-top:1px solid #e5e7eb">
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px">
                <div style="color:#166534;font-weight:600;margin-bottom:6px"><i class="ti ti-circle-check"></i> APPROVED</div>
                <div style="font-size:12px;color:#374151">
                    <div><strong>Tanda Tangan:</strong> ${escapeHtml(task.leaderSignature || '-')}</div>
                    <div><strong>Waktu:</strong> ${task.approvedAt}</div>
                </div>
            </div>` : '';

        const cancelledInfo = task.status === 'cancelled' ? `
            <hr style="margin:12px 0;border:none;border-top:1px solid #e5e7eb">
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px;color:#991b1b">
                <strong><i class="ti ti-circle-x"></i> TASK DIBATALKAN</strong>
            </div>` : '';

        const html = `<div style="text-align:left;font-size:14px;line-height:1.7">
            <table style="width:100%;border-collapse:collapse">
                <tr><td style="padding:4px 0;color:#6b7280;width:40%">Kode</td><td><strong>${escapeHtml(task.taskCode || '-')}</strong></td></tr>
                <tr><td style="padding:4px 0;color:#6b7280">Operator</td><td>${escapeHtml(task.operatorName)}</td></tr>
                <tr><td style="padding:4px 0;color:#6b7280">Tanggal</td><td>${escapeHtml(task.date)}</td></tr>
                <tr><td style="padding:4px 0;color:#6b7280">Seksi</td><td><span style="background:#f3f4f6;padding:2px 8px;border-radius:6px">${escapeHtml(task.section)}</span></td></tr>
                <tr><td style="padding:4px 0;color:#6b7280">Stage</td><td><strong>${task.stage.toUpperCase()}</strong></td></tr>
                <tr><td style="padding:4px 0;color:#6b7280">Status</td><td><strong>${task.status.toUpperCase()}</strong></td></tr>
            </table>
            <hr style="margin:12px 0;border:none;border-top:1px solid #e5e7eb">
            <div style="color:#6b7280;font-size:12px;margin-bottom:4px">MASALAH</div>
            <div style="margin-bottom:12px">${escapeHtml(task.problem)}</div>
            <div style="color:#6b7280;font-size:12px;margin-bottom:4px">TINDAKAN TEMPORARY</div>
            <div style="margin-bottom:12px">${escapeHtml(task.tempAction) || '<em style="color:#9ca3af">Belum diisi</em>'}</div>
            <div style="color:#6b7280;font-size:12px;margin-bottom:4px">TINDAKAN PERMANENT</div>
            <div style="margin-bottom:12px">${escapeHtml(task.permAction) || '<em style="color:#9ca3af">Belum diisi</em>'}</div>
            <table style="width:100%;border-collapse:collapse">
                <tr><td style="padding:4px 0;color:#6b7280;width:40%">Deadline</td><td>${task.deadline ? formatDate(task.deadline) : '<em style="color:#9ca3af">-</em>'}</td></tr>
                <tr><td style="padding:4px 0;color:#6b7280">PIC</td><td>${escapeHtml(task.pic) || '<em style="color:#9ca3af">-</em>'}</td></tr>
            </table>
            ${approvalInfo}
            ${cancelledInfo}
            <hr style="margin:12px 0;border:none;border-top:1px solid #e5e7eb">
            <div style="color:#9ca3af;font-size:12px;text-align:center">
                <i class="ti ti-lock"></i> Login sebagai leader untuk mengedit task ini
            </div>
        </div>`;

        Swal.fire({
            title: 'Detail Task',
            html: html,
            width: 600,
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-file-description"></i> Report',
            cancelButtonText: 'Tutup',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#6b7280',
            reverseButtons: true
        }).then(result => {
            if (result.isConfirmed) openTaskReport(id);
        });
    }

    // ============================================================
    // QR CODE
    // ============================================================
    function renderQRCode(containerEl, text) {
        if (!containerEl) return;
        const value = String(text || '').trim();

        if (!value || typeof QRCode === 'undefined') {
            containerEl.innerHTML = '';
            containerEl.dataset.signature = '';
            return;
        }

        try {
            containerEl.innerHTML = '';
            containerEl.dataset.signature = value;

            new QRCode(containerEl, {
                text: value,
                width: 80,
                height: 80,
                colorDark: '#1f2937',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.M
            });
            setTimeout(() => {
                const canvas = containerEl.querySelector('canvas');
                if (canvas) {
                    canvas.style.setProperty('display', 'block', 'important');
                }
                containerEl.querySelectorAll('img').forEach(img => img.remove());
            }, 100);

        } catch (e) {
            console.warn('[QRCode] gagal render:', value, e);
            containerEl.innerHTML = '';
            containerEl.dataset.signature = '';
        }
    }

    function renderSignatureQRCodes(containerSelector, signatures) {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        container.querySelectorAll('.signature-qrcode').forEach(el => {
            const type = el.dataset.type || '';
            const text = signatures[type] || '';
            renderQRCode(el, text);
        });
    }

    function formatDateTime(d) {
        if (!d) return '-';
        const date = new Date(d.replace(' ', 'T'));
        return date.toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    // ============================================================
    // RENDER
    // ============================================================
    function render() {
        const filtered = getFilteredTasks();
        const isLeader = canDragDrop();

        renderStats(filtered);
        renderKanban(filtered, isLeader);
        updateBoardInfo(filtered, isLeader);
        updateCancelledCount();
    }

    function renderStats(list) {
        $('#planCount').text(list.filter(t => t.stage === 'plan').length);
        $('#doCount').text(list.filter(t => t.stage === 'do').length);
        $('#checkCount').text(list.filter(t => t.stage === 'check').length);
        $('#actCount').text(list.filter(t => t.stage === 'act').length);
    }

    // ══════════════════════════════════════════════════════════════
    // STAGE LABEL MAPPING (untuk tooltip/badge)
    // ══════════════════════════════════════════════════════════════
    const STAGE_LABEL = {
        plan:  { title: 'Informasi Masalah',        role: 'Operator',      short: 'Phase 1' },
        do:    { title: 'Penanggung Jawab Masalah', role: 'Team Leader',   short: 'Phase 2' },
        check: { title: 'Isi Tindakan',             role: 'Seksi Terkait', short: 'Phase 3' },
        act:   { title: 'Approval / Evaluasi',      role: 'Group Leader',  short: 'Phase 4' },
    };

    function renderKanban(filtered, isLeader) {
        ['plan', 'do', 'check', 'act'].forEach(stage => {
            const $container = $(`#${stage}Items`);
            const $badge = $(`#${stage}Badge`);
            const $column = $container.closest('.kanban-column');
            const items = filtered.filter(t => t.stage === stage);

            $badge.text(items.length);
            $column.toggleClass('locked', !isLeader);

            if (items.length === 0) {
                $container.html(`<div style="text-align:center;padding:20px;color:#9ca3af;font-size:13px;">
                    ${isLeader ? 'Drop task di sini' : 'Tidak ada task'}
                </div>`);
                return;
            }

            $container.html(items.map(task => {
                const isPending = !task.tempAction && !task.permAction && !task.deadline && !task.pic;
                const isDone = task.status === 'done';
                const isCancelled = task.status === 'cancelled';
                const isInProgress = task.status === 'in_progress';
                const isActStage = task.stage === 'act';

                let badgeHtml = '';
                if (isDone) {
                    badgeHtml = `<div class="badge-status badge-approved">
                        <i class="ti ti-circle-check"></i> Approved${task.leaderSignature ? ' · ' + escapeHtml(task.leaderSignature) : ''}
                    </div>`;
                } else if (isCancelled) {
                    badgeHtml = `<div class="badge-status badge-cancelled"><i class="ti ti-circle-x"></i> Cancelled</div>`;
                } else if (isActStage && isInProgress) {
                    badgeHtml = `<div class="badge-status badge-pending-approval"><i class="ti ti-clock-hour-4"></i> Menunggu Approval</div>`;
                } else if (isInProgress && !isPending) {
                    badgeHtml = `<div class="badge-status badge-in-progress"><i class="ti ti-bolt"></i> In Progress</div>`;
                }

                const cardClasses = [
                    'kanban-card',
                    isLeader ? 'draggable' : '',
                    isPending && !isDone && !isCancelled ? 'card-pending' : '',
                    isDone ? 'card-approved' : '',
                    isCancelled ? 'card-cancelled' : ''
                ].filter(Boolean).join(' ');

                return `<div class="${cardClasses}"
                    ${isLeader ? `draggable="true" ondragstart="handleDragStart(event, ${task.id})" ondragend="handleDragEnd(event)"` : ''}
                    onclick="${isLeader ? `editTask(${task.id})` : `viewTask(${task.id})`}">
                    <div class="card-header">
                        <div class="card-title">${escapeHtml(task.problem)}</div>
                        ${isLeader && !isDone && !isCancelled
                            ? `<button class="card-delete" onclick="event.stopPropagation(); deleteTask(${task.id})" title="Hapus">
                                <i class="ti ti-trash"></i>
                               </button>` : ''}
                    </div>
                    ${badgeHtml}
                    ${isPending && !isDone && !isCancelled
                        ? '<div class="pending-badge"><i class="ti ti-alert-triangle"></i> Menunggu tindakan leader</div>' : ''}
                    ${task.tempAction ? `<div class="card-desc">Temp: ${escapeHtml(task.tempAction)}</div>` : ''}
                    ${task.permAction ? `<div class="card-desc">Perm: ${escapeHtml(task.permAction)}</div>` : ''}
                    <div class="card-meta">
                        ${task.category ? `<span class="card-tag cat-${task.category}">${escapeHtml(CATEGORY_LABEL[task.category] || task.category)}</span>` : ''}
                        ${task.picSection ? `<span class="picsec-badge">${escapeHtml(PIC_SECTION_LABEL[task.picSection] || task.picSection)}</span>` : ''}
                        <span class="card-tag">${escapeHtml(task.section)}</span>
                        ${task.pic ? `<span class="card-tag">PIC: ${escapeHtml(task.pic)}</span>` : ''}
                    </div>
                    <div class="card-footer">
                        <span class="card-operator">${escapeHtml(task.operatorName)}</span>
                        <span class="card-date">${task.deadline ? `Deadline: ${formatDate(task.deadline)}` : formatDate(task.date)}</span>
                    </div>
                </div>`;
            }).join(''));
        });
    }

    function updateBoardInfo(filtered, isLeader) {
        $('#totalTasks').text(`${state.tasks.length} Task`);
        $('#filteredTasks').text(`${filtered.length} Ditampilkan`);
        $('#boardSubtitle').text(isLeader
            ? 'Drag and drop untuk memindahkan task'
            : 'Login sebagai leader untuk drag & drop');
    }

    // ============================================================
    // ATTACHMENT UPLOAD
    // ============================================================
    const CATEGORY_LABEL = {
        man:      'Man',
        machine:  'Machine / Jig / Mold',
        material: 'Material',
        methode:  'Methode',
    };
    const PIC_SECTION_LABEL = {
        mdf: 'MDF', pe: 'PE', pc: 'PC', inj: 'INJ',
        st:  'ST',  qc: 'QC', whp: 'WHP', prc: 'PRC', log: 'LOG',
    };
    const DOC_LABEL = { '4m': '4M', 'logbook': 'Logbook', 'nursecall': 'Nursecall' };
    // const DOC_MAX_SIZE = 4 * 1024 * 1024;
    // const DOC_MIMES = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];

    // function getDocEls(type) {
    //     const t = String(type || '').toLowerCase();
    //     const KEY_MAP = {
    //         '4m':       '4m',
    //         'logbook':  'Logbook',
    //         'nursecall':'Nursecall',
    //     };
    //     const key = KEY_MAP[t];
    //     if (!key) return null;

    //     return {
    //         card:    $(`.doc-upload-card[data-type="${t}"]`),
    //         status:  $(`#docStatus${key}`),
    //         drop:    $(`#docDrop${key}`),
    //         preview: $(`#docPreview${key}`),
    //         input:   $(`#docInput${key}`),
    //         link:    $(`#docLink${key}`),
    //         meta:    $(`#docMeta${key}`),
    //     };
    // }

    // function isTaskLocked() {
    //     const id = parseInt($('#taskId').val() || '0', 10);
    //     if (!id) return false;
    //     const task = state.tasks.find(t => t.id === id);
    //     if (!task) return false;
    //     return task.status === 'done' || task.status === 'cancelled';
    // }

    // function applyDocLockState() {
    //     $('.doc-upload-card').toggleClass('locked', isTaskLocked());
    // }

    // function pickFile(type) {
    //     if (isTaskLocked()) {
    //         showToast('Task sudah di-approve, dokumen tidak bisa diubah', 'warning');
    //         return;
    //     }
    //     const els = getDocEls(type);
    //     if (els) els.input.trigger('click');
    // }

    // function handleDocDragOver(e) {
    //     e.preventDefault();
    //     e.currentTarget.classList.add('drag-over');
    // }
    // function handleDocDragLeave(e) {
    //     e.currentTarget.classList.remove('drag-over');
    // }
    // function handleDocDrop(e, type) {
    //     e.preventDefault();
    //     e.currentTarget.classList.remove('drag-over');
    //     const file = e.dataTransfer.files?.[0];
    //     if (file) uploadDoc(type, file);
    // }
    // function handleFileSelect(e, type) {
    //     const file = e.target.files?.[0];
    //     if (file) uploadDoc(type, file);
    //     e.target.value = '';
    // }

    // async function uploadDoc(type, file) {
    //     type = String(type || '').toLowerCase();

    //     if (file.size > DOC_MAX_SIZE) {
    //         showAlert('warning', 'File Terlalu Besar', 'Maksimum 4 MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + ' MB');
    //         return;
    //     }
    //     if (!DOC_MIMES.includes(file.type)) {
    //         showAlert('warning', 'Format Tidak Didukung', 'Hanya PDF / JPG / PNG.');
    //         return;
    //     }

    //     const taskId = $('#taskId').val();
    //     if (!taskId) {
    //         showAlert('warning', 'Simpan Task Dulu', 'Simpan task terlebih dahulu sebelum upload dokumen.');
    //         return;
    //     }

    //     $('#docProgressBar').show();
    //     $('#docProgressFill').css('width', '0%');

    //     const fd = new FormData();
    //     fd.append('file_type', type);
    //     fd.append('file', file);

    //     try {
    //         const token = window.CSRF_TOKEN || $('meta[name="csrf-token"]').attr('content') || '';
    //         await $.ajax({
    //             url: API_BASE + `/tasks/${taskId}/attachments`,
    //             type: 'POST',
    //             data: fd,
    //             processData: false,
    //             contentType: false,
    //             dataType: 'json',
    //             xhr: function () {
    //                 const xhr = new window.XMLHttpRequest();
    //                 xhr.upload.addEventListener('progress', function (e) {
    //                     if (e.lengthComputable) {
    //                         const pct = (e.loaded / e.total) * 100;
    //                         $('#docProgressFill').css('width', pct + '%');
    //                     }
    //                 });
    //                 return xhr;
    //             },
    //             headers: { 'X-CSRF-TOKEN': token }
    //         });

    //         await loadAttachments(taskId);

    //         showToast(DOC_LABEL[type] + ' berhasil diupload', 'success');

    //     } catch (err) {
    //         const msg = err?.responseJSON?.message || err?.responseText || err?.statusText || err?.message || 'Upload gagal';
    //         showAlert('error', 'Upload Gagal', msg);
    //     } finally {
    //         setTimeout(() => {
    //             $('#docProgressBar').hide();
    //             $('#docProgressFill').css('width', '0%');
    //         }, 600);
    //     }
    // }

    // function renderDocPreview(type, att) {
    //     type = String(type || '').toLowerCase();
    //     const els = getDocEls(type);
    //     if (!els || !els.card.length) return;

    //     if (!att) {
    //         els.card.removeClass('has-file');
    //         els.status.text('Belum ada');
    //         els.drop.show();
    //         els.preview.hide();
    //         return;
    //     }

    //     els.card.addClass('has-file');
    //     els.status.text('✓ Tersedia');
    //     els.drop.hide();
    //     els.preview.show();

    //     const fileUrl = API_BASE.replace(/\/$/, '') + '/storage/' + (att.file_path || '');
    //     els.link.attr('href', fileUrl).text(att.original_name || att.stored_name || '-');
    //     els.meta.text(formatFileSize(att.file_size) + ' · ' + (att.uploaded_by_name || '-'));
    // }

    // async function removeDoc(type) {
    //     type = String(type || '').toLowerCase();

    //     const taskId = $('#taskId').val();
    //     if (!taskId) return;

    //     const confirmed = await showConfirm(
    //         'Hapus Dokumen?',
    //         `Hapus file <strong>${DOC_LABEL[type]}</strong>?`,
    //         'Ya, Hapus', 'Batal', 'warning'
    //     );
    //     if (!confirmed) return;

    //     try {
    //         await api(`/tasks/${taskId}/attachments/${type}`, { method: 'DELETE' });
    //         delete taskAttachments[type];
    //         renderDocPreview(type, null);
    //         showToast(DOC_LABEL[type] + ' dihapus', 'info');
    //         updateApproveButtonState();
    //     } catch (err) {
    //         showAlert('error', 'Gagal Hapus', err.message);
    //     }
    // }

    function formatFileSize(bytes) {
        if (!bytes) return '0 B';
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1024 / 1024).toFixed(2) + ' MB';
    }

    // async function loadAttachments(taskId) {
    //     taskAttachments = {};
    //     ['4m', 'logbook', 'nursecall'].forEach(t => renderDocPreview(t, null));

    //     if (!taskId) {
    //         updateApproveButtonState();
    //         return;
    //     }

    //     try {
    //         const res = await api(`/tasks/${taskId}/attachments`);
    //         const list = Array.isArray(res) ? res : (res?.data || []);

    //         list.forEach(att => {
    //             const type = String(att.file_type || '').toLowerCase();
    //             if (!DOC_LABEL[type]) return;

    //             att.file_type = type;
    //             taskAttachments[type] = att;
    //             renderDocPreview(type, att);
    //         });
    //     } catch (err) {
    //         console.warn('[attachments] gagal load:', err);
    //     } finally {
    //         updateApproveButtonState();
    //     }
    // }

    // ============================================================
    // UTIL
    // ============================================================
    function escapeHtml(s) {
        if (s == null) return '';
        return String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }
    function formatDate(d) {
        if (!d) return '-';
        return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    }
    function getStageIcon(s) {
        return ({ plan: 'ti-clipboard-list', do: 'ti-bolt', check: 'ti-circle-check', act: 'ti-tool' })[s] || 'ti-clipboard-list';
    }
    function openModal(id) { $('#' + id).addClass('active'); }
    function closeModal(id) { $('#' + id).removeClass('active'); }
    function updateApproveButtonState() {
        const $btn = $('#approveTaskBtn');
        if (!$btn.length) return;

        const missing = [];
        ['4m', 'logbook', 'nursecall'].forEach(t => {
            const status = $(`input[name="doc_status_${t}"]:checked`).val() || 'belum';
            if (status !== 'sudah') missing.push(DOC_LABEL[t] || t.toUpperCase());
        });

        if (missing.length === 0) {
            $btn.prop('disabled', false)
                .attr('title', 'Approve task ini')
                .html('<i class="ti ti-check"></i> Approve');
        } else {
            $btn.prop('disabled', true)
                .attr('title', 'Tandai "Sudah" dulu: ' + missing.join(', '))
                .html('<i class="ti ti-lock"></i> Approve');
        }
    }

    function renderApprovalSection(task) {
        const $empty = $('#approvalEmptyState');
        const $filled = $('#approvalFilledState');
        const $checklist = $('#approvalChecklistItems');

        $empty.hide();
        $filled.hide();

        const isApproved = task && (
            task.status === 'done' || !!task.approvedAt || !!task.leaderSignature
        );

        if (isApproved) {
            $filled.show();
            $('#approvalByName').text(task.leaderSignature || task.approvedByName || '-');
            $('#approvalAt').text(task.approvedAt ? formatDateTime(task.approvedAt) : '-');
            $('#approvalSignature').html(
                task.leaderSignature
                    ? `<span style="display:inline-flex;align-items:center;gap:4px;color:#16a34a">
                        <i class="ti ti-circle-check"></i> ${escapeHtml(task.leaderSignature)}
                    </span>`
                    : '<span style="color:#9ca3af">-</span>'
            );
            $('#approvalStatusBadge').html(`<span class="status-pill status-done">APPROVED</span>`);
            $checklist.html('<div style="color:#16a34a"><i class="ti ti-circle-check"></i> Task sudah di-approve</div>');
            return;
        }

        if (!task) {
            $empty.show();
            $checklist.html(`
                <div><i class="ti ti-minus"></i> Simpan task terlebih dahulu</div>
                <div><i class="ti ti-minus"></i> Isi Phase 1-3</div>
                <div><i class="ti ti-minus"></i> Pindahkan ke stage ACT</div>
                <div><i class="ti ti-minus"></i> Tandai semua dokumen "Sudah"</div>
            `);
            return;
        }

        $empty.show();

        const docOk = task.doc4mStatus === 'sudah'
                && task.docLogbookStatus === 'sudah'
                && task.docNursecallStatus === 'sudah';

        const checks = [
            { label: 'Phase 1: Data Operator + Klasifikasi lengkap',
            done: !!(task.operatorName && task.date && task.section && task.problem && task.category && task.picSection) },
            { label: 'Phase 2: Semua dokumen ditandai "Sudah"',  done: docOk },
            { label: 'Phase 3: Tindakan & PIC terisi',
            done: !!(task.tempAction || task.permAction) && !!task.pic },
            { label: 'Task berada di stage ACT',                 done: task.stage === 'act' },
            { label: 'Status task = in_progress',                done: task.status === 'in_progress' },
        ];

        $checklist.html(checks.map(c => `
            <div style="display:flex;align-items:center;gap:6px">
                <i class="ti ${c.done ? 'ti-circle-check' : 'ti-circle-x'}"
                style="color:${c.done ? '#16a34a' : '#dc2626'}"></i>
                <span>${escapeHtml(c.label)}</span>
            </div>
        `).join(''));
    }

    // ============================================================
    // SWEETALERT HELPERS
    // ============================================================
    function showToast(message, icon = 'success') {
        SwalToast.fire({ icon: icon, title: message });
    }

    function showAlert(icon, title, text) {
        Swal.fire({
            icon: icon,
            title: title,
            html: text,
            confirmButtonText: 'OK',
            confirmButtonColor: '#2563eb'
        });
    }

    function showConfirm(title, text, confirmText = 'Ya', cancelText = 'Batal', icon = 'warning') {
        return Swal.fire({
            icon: icon,
            title: title,
            html: text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            reverseButtons: true
        }).then(result => result.isConfirmed);
    }
    // ============================================================
    // DATE HELPERS
    // ============================================================
    function fmtDate(d) {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${dd}`;
    }
    function getTodayStr() { return fmtDate(new Date()); }

    function setActiveQuickDate(range) {
        $('.btn-quick-date').each(function () {
            const $b = $(this);
            if ($b.data('range') === range) {
                $b.addClass('btn-primary');
            } else {
                $b.removeClass('btn-primary');
            }
        });
    }

    function setQuickDate(range) {
        const today = new Date();
        let from = '', to = '';

        if (range === 'today') {
            from = to = fmtDate(today);
        } else if (range === 'week') {
            const start = new Date(today);
            start.setDate(start.getDate() - 6);
            from = fmtDate(start);
            to = fmtDate(today);
        } else if (range === 'month') {
            const start = new Date(today.getFullYear(), today.getMonth(), 1);
            const end   = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            from = fmtDate(start);
            to = fmtDate(end);
        } else if (range === 'all') {
            from = ''; to = '';
        }

        $('#dateFrom').val(from);
        $('#dateTo').val(to);
        state.dateFrom = from || null;
        state.dateTo   = to   || null;

        setActiveQuickDate(range);
        render();
    }

    function detectActiveQuickDate() {
        const from = $('#dateFrom').val() || '';
        const to   = $('#dateTo').val()   || '';
        const today = getTodayStr();

        if (from === today && to === today) { setActiveQuickDate('today'); return; }

        const weekStart = new Date();
        weekStart.setDate(weekStart.getDate() - 6);
        if (from === fmtDate(weekStart) && to === today) { setActiveQuickDate('week'); return; }

        const now = new Date();
        const mStart = new Date(now.getFullYear(), now.getMonth(), 1);
        const mEnd   = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        if (from === fmtDate(mStart) && to === fmtDate(mEnd)) { setActiveQuickDate('month'); return; }

        if (!from && !to) { setActiveQuickDate('all'); return; }

        setActiveQuickDate(null);
    }

    // ══════════════════════════════════════════════════════════════
    // ROLE INFORMATION
    // ══════════════════════════════════════════════════════════════

    const ROLE_INFO = {
        roles: [
            {
                key: 'operator',
                name: 'Operator',
                icon: 'ti-user',
                color: '#2563eb',
                bg: '#eff6ff',
                desc: 'Hanya bisa melaporkan masalah dengan data dasar.',
            },
            {
                key: 'team_leader',
                name: 'Team Leader',
                icon: 'ti-users',
                color: '#0891b2',
                bg: '#ecfeff',
                desc: 'Bisa isi tindakan & gerakkan task antar stage.',
            },
            {
                key: 'group_leader',
                name: 'Group Leader',
                icon: 'ti-user-star',
                color: '#16a34a',
                bg: '#f0fdf4',
                desc: 'Bisa approve task setelah dokumen lengkap.',
            },
            {
                key: 'manager',
                name: 'Manager',
                icon: 'ti-briefcase',
                color: '#7c3aed',
                bg: '#f5f3ff',
                desc: 'Akses penuh untuk monitoring & approval.',
            },
            {
                key: 'admin',
                name: 'Admin',
                icon: 'ti-shield-check',
                color: '#7c3aed',
                bg: '#ede9fe',
                desc: 'Akses penuh + bisa tambah user baru.',
            },
        ],

        matrix: [
            { feature: 'Buat task baru',                 operator: true,  team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Isi data dasar (operator, tanggal, seksi, masalah)',
                                                        operator: true,  team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Isi klasifikasi (Category, PIC Section)',
                                                        operator: false, team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Isi tindakan (Temp, Perm, Deadline, PIC)',
                                                        operator: false, team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Drag & drop stage',              operator: false, team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Edit task',                      operator: false, team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Hapus task',                     operator: false, team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Approve task',                   operator: false, team_leader: false, group_leader: true,  manager: true,  admin: true },
            { feature: 'Cancel task',                    operator: false, team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Lihat Report',                   operator: false, team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Lihat History',                  operator: true,  team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Tambah User Baru',               operator: false, team_leader: false, group_leader: false, manager: false, admin: true },
            { feature: 'Login / Logout',                 operator: true,  team_leader: true,  group_leader: true,  manager: true,  admin: true },
            { feature: 'Edit Profile sendiri',           operator: true,  team_leader: true,  group_leader: true,  manager: true,  admin: true },
        ],

        details: [
            {
                name: 'Operator',
                color: '#2563eb',
                bg: '#eff6ff',
                icon: 'ti-user',
                can: [
                    'Tambah task baru dengan 4 field dasar (Operator, Tanggal, Seksi, Masalah)',
                    'Lihat semua task di Kanban board',
                    'Klik task untuk lihat detail (read-only)',
                    'Lihat history task',
                    'Login & edit profile sendiri',
                ],
                cannot: [
                    'Isi Category / PIC Section',
                    'Isi Tindakan / Deadline / PIC',
                    'Drag & drop task',
                    'Edit / Hapus / Approve task',
                    'Buka Report',
                ],
            },
            {
                name: 'Team Leader',
                color: '#0891b2',
                bg: '#ecfeff',
                icon: 'ti-users',
                can: [
                    'Semua akses Operator, PLUS:',
                    'Isi klasifikasi: Category + PIC Section',
                    'Isi Tindakan Temporary & Permanent',
                    'Set Deadline & PIC',
                    'Drag & drop task antar stage',
                    'Edit & Hapus task',
                    'Lihat Report',
                ],
                cannot: [
                    'Approve task (butuh Group Leader ke atas)',
                    'Tambah user baru',
                ],
            },
            {
                name: 'Group Leader',
                color: '#16a34a',
                bg: '#f0fdf4',
                icon: 'ti-user-star',
                can: [
                    'Semua akses Team Leader, PLUS:',
                    'Approve task (setelah 3 dokumen diupload)',
                    'Cancel task',
                ],
                cannot: [
                    'Tambah user baru',
                ],
            },
            {
                name: 'Manager',
                color: '#7c3aed',
                bg: '#f5f3ff',
                icon: 'ti-briefcase',
                can: [
                    'Semua akses Group Leader',
                    'Approve & Cancel task',
                    'Lihat Report lengkap',
                ],
                cannot: [
                    'Tambah user baru (khusus Admin)',
                ],
            },
            {
                name: 'Admin',
                color: '#7c3aed',
                bg: '#ede9fe',
                icon: 'ti-shield-check',
                can: [
                    'Semua akses Manager, PLUS:',
                    'Tambah User Baru (tombol muncul di header)',
                    'Daftarkan user dengan role apapun',
                    'Akses tanpa batasan',
                ],
                cannot: [],
            },
        ],
    };

    function openRoleInfo() {
        renderRoleLegend();
        renderRoleMatrix();
        renderRoleDetails();
        openModal('roleInfoModal');
    }

    function renderRoleLegend() {
        const html = ROLE_INFO.roles.map(r => `
            <div style="background:${r.bg};border:1px solid ${r.color}30;border-radius:10px;padding:12px;display:flex;align-items:flex-start;gap:10px">
                <div style="width:36px;height:36px;background:${r.color};color:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <i class="ti ${r.icon}" style="font-size:18px"></i>
                </div>
                <div style="min-width:0">
                    <div style="font-weight:700;color:${r.color};font-size:13px">${r.name}</div>
                    <div style="font-size:11px;color:#6b7280;line-height:1.4;margin-top:2px">${r.desc}</div>
                </div>
            </div>
        `).join('');
        $('#roleLegend').html(html);
    }

    function renderRoleMatrix() {
        const cols = ROLE_INFO.roles;
        const rows = ROLE_INFO.matrix;

        let html = `
            <thead>
                <tr>
                    <th style="text-align:left;min-width:240px">Fitur / Aksi</th>
                    ${cols.map(r => `
                        <th style="text-align:center;min-width:90px;background:${r.bg};color:${r.color}">
                            <i class="ti ${r.icon}"></i> ${r.name}
                        </th>
                    `).join('')}
                </tr>
            </thead>
            <tbody>
        `;

        rows.forEach((row, i) => {
            const bg = i % 2 === 0 ? '#ffffff' : '#fafafa';
            html += `<tr>`;
            html += `<td style="background:${bg};font-weight:500;color:#374151">${escapeHtml(row.feature)}</td>`;
            cols.forEach(r => {
                const v = row[r.key];
                if (v) {
                    html += `<td style="text-align:center;background:${bg}">
                        <i class="ti ti-check" style="color:#16a34a;font-size:16px"></i>
                    </td>`;
                } else {
                    html += `<td style="text-align:center;background:${bg}">
                        <i class="ti ti-x" style="color:#dc2626;font-size:16px;opacity:0.5"></i>
                    </td>`;
                }
            });
            html += `</tr>`;
        });

        html += `</tbody>`;
        $('#roleMatrixTable').html(html);
    }

    function renderRoleDetails() {
        const html = ROLE_INFO.details.map(d => `
            <div style="border:1px solid ${d.color}30;border-radius:10px;overflow:hidden">
                <div style="background:${d.bg};padding:10px 14px;display:flex;align-items:center;gap:8px;border-bottom:1px solid ${d.color}30">
                    <i class="ti ${d.icon}" style="color:${d.color};font-size:18px"></i>
                    <span style="font-weight:700;color:${d.color};font-size:14px">${d.name}</span>
                </div>
                <div style="padding:12px 14px;display:grid;grid-template-columns:1fr 1fr;gap:14px;font-size:12px">
                    <div>
                        <div style="font-weight:700;color:#16a34a;margin-bottom:6px;display:flex;align-items:center;gap:4px">
                            <i class="ti ti-circle-check"></i> Bisa
                        </div>
                        <ul style="padding-left:18px;margin:0;line-height:1.7;color:#374151">
                            ${d.can.map(c => `<li>${escapeHtml(c)}</li>`).join('')}
                        </ul>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#dc2626;margin-bottom:6px;display:flex;align-items:center;gap:4px">
                            <i class="ti ti-circle-x"></i> Tidak Bisa
                        </div>
                        ${d.cannot.length > 0
                            ? `<ul style="padding-left:18px;margin:0;line-height:1.7;color:#374151">
                                ${d.cannot.map(c => `<li>${escapeHtml(c)}</li>`).join('')}
                            </ul>`
                            : `<div style="color:#9ca3af;font-style:italic">Tidak ada batasan</div>`
                        }
                    </div>
                </div>
            </div>
        `).join('');
        $('#roleDetailList').html(html);
    }

    // ══════════════════════════════════════════════════════════════
    // EXPORT EXCEL
    // ══════════════════════════════════════════════════════════════
    function exportRoleInfoExcel() {
        if (typeof XLSX === 'undefined') {
            showAlert('error', 'Gagal Export', 'Library SheetJS (XLSX) belum dimuat. Cek koneksi internet Anda.');
            return;
        }

        try {
            const wb = XLSX.utils.book_new();

            // ── Sheet 1: Matrix ──────────────────────────────────────
            const headers = ['Fitur / Aksi', ...ROLE_INFO.roles.map(r => r.name)];

            const matrixRows = ROLE_INFO.matrix.map(row => [
                row.feature,
                ...ROLE_INFO.roles.map(r => row[r.key] ? '✓' : '✗'),
            ]);

            const sheet1Data = [
                ['PDCA KANBAN BOARD - Matrix Hak Akses Role'],
                ['Dicetak:', new Date().toLocaleString('id-ID')],
                [],
                headers,
                ...matrixRows,
            ];

            const ws1 = XLSX.utils.aoa_to_sheet(sheet1Data);

            // Column widths
            ws1['!cols'] = [
                { wch: 50 },  // Fitur
                { wch: 14 },  // Operator
                { wch: 14 },  // Team Leader
                { wch: 14 },  // Group Leader
                { wch: 14 },  // Manager
                { wch: 14 },  // Admin
            ];

            // Merge title
            ws1['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 5 } },  // Title
                { s: { r: 1, c: 0 }, e: { r: 1, c: 5 } },  // Date
            ];

            XLSX.utils.book_append_sheet(wb, ws1, 'Matrix Akses');

            // ── Sheet 2: Detail per Role ─────────────────────────────
            const detailRows = [['Role', 'Kategori', 'Keterangan']];

            ROLE_INFO.details.forEach(d => {
                detailRows.push([d.name, '✓ BISA', '']);
                d.can.forEach(c => {
                    detailRows.push(['', '', c]);
                });
                if (d.cannot.length > 0) {
                    detailRows.push([d.name, '✗ TIDAK BISA', '']);
                    d.cannot.forEach(c => {
                        detailRows.push(['', '', c]);
                    });
                }
                detailRows.push(['', '', '']); // empty separator
            });

            const ws2 = XLSX.utils.aoa_to_sheet(detailRows);
            ws2['!cols'] = [
                { wch: 16 },
                { wch: 16 },
                { wch: 80 },
            ];
            XLSX.utils.book_append_sheet(wb, ws2, 'Detail per Role');

            // ── Sheet 3: Catatan Penting ─────────────────────────────
            const notes = [
                ['CATATAN PENTING'],
                [],
                ['1.', 'Approve task hanya bisa dilakukan oleh Group Leader, Manager & Admin.'],
                ['',   'Wajib upload 3 dokumen (4M, Logbook, Nursecall) sebelum approve.'],
                ['2.', 'Task yang sudah di-approve (done) atau cancelled tidak bisa diubah lagi.'],
                ['3.', 'Operator hanya bisa tambah task dengan 4 field dasar.'],
                ['4.', 'Transisi stage: PLAN → DO → CHECK → ACT (tidak bisa lompat).'],
                ['5.', 'Perpindahan DO → CHECK memerlukan Tindakan Temporary / Permanent.'],
            ];
            const ws3 = XLSX.utils.aoa_to_sheet(notes);
            ws3['!cols'] = [{ wch: 5 }, { wch: 100 }];
            XLSX.utils.book_append_sheet(wb, ws3, 'Catatan Penting');

            // ── Save ─────────────────────────────────────────────────
            const dateStr = new Date().toISOString().slice(0, 10);
            XLSX.writeFile(wb, `Info-Role-PDCA-${dateStr}.xlsx`);

            showToast('Excel berhasil diunduh', 'success');

        } catch (err) {
            console.error('[exportRoleInfoExcel] error:', err);
            showAlert('error', 'Gagal Export Excel', err.message || 'Error tidak dikenal');
        }
    }

    // ══════════════════════════════════════════════════════════════
    // PRINT ROLE INFO
    // ══════════════════════════════════════════════════════════════
    function printRoleInfo() {
        const content = document.getElementById('roleInfoModal');
        if (!content) return;

        const printWin = window.open('', '_blank', 'width=900,height=700');
        const modalBody = content.querySelector('.modal-body').innerHTML;

        printWin.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Info Role - PDCA Kanban</title>
                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
                <style>
                    body { font-family: -apple-system, sans-serif; padding: 20px; color: #111827; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { border: 1px solid #e5e7eb; padding: 6px 8px; text-align: left; }
                    th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; }
                    .ti { vertical-align: -0.125em; stroke-width: 2; }
                    h1, h2, h3 { page-break-after: avoid; }
                    .modal-footer, .modal-header, .notice-bar { display: none; }
                    * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                </style>
            </head>
            <body>
                <h2 style="margin-bottom:4px">PDCA Kanban Board - Informasi Role & Hak Akses</h2>
                <div style="color:#6b7280;font-size:11px;margin-bottom:16px">Dicetak: ${new Date().toLocaleString('id-ID')}</div>
                ${modalBody}
            </body>
            </html>
        `);

        printWin.document.close();

        setTimeout(() => {
            printWin.print();
            setTimeout(() => printWin.close(), 500);
        }, 400);
    }
    </script>
</body>
</html>