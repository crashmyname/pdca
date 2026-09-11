<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDCA Kanban Board - Task Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', sans-serif;
            background: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.5;
            min-height: 100vh;
        }

        /* Header */
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

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .header-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .header-subtitle {
            font-size: 12px;
            color: var(--gray-500);
        }

        .header-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: var(--gray-100);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--gray-700);
        }

        .user-avatar {
            width: 28px;
            height: 28px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
            font-weight: 600;
        }

        .btn {
            padding: 8px 16px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: white;
            color: var(--gray-700);
            white-space: nowrap;
        }

        .btn:hover {
            background: var(--gray-50);
            border-color: var(--gray-400);
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .btn-success {
            background: var(--success);
            border-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
            border-color: #15803d;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Notice Bar */
        .notice-bar {
            background: var(--primary-light);
            border: 1px solid #bfdbfe;
            border-radius: var(--radius);
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: var(--primary-dark);
        }

        .notice-bar.warning {
            background: var(--warning-light);
            border-color: #fde68a;
            color: #92400e;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--gray-500);
        }

        .stat-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-xs);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
        }

        .stat-plan { background: var(--primary-light); color: var(--primary); }
        .stat-do { background: var(--success-light); color: var(--success); }
        .stat-check { background: var(--warning-light); color: var(--warning); }
        .stat-act { background: var(--danger-light); color: var(--danger); }

        /* Filter Bar */
        .filter-bar {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius);
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
        }

        .filter-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-box {
            flex: 1;
            min-width: 200px;
            position: relative;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            pointer-events: none;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 36px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 14px;
            transition: all 0.2s;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .filter-select,
        .date-input {
            padding: 8px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 14px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-select:focus,
        .date-input:focus {
            outline: none;
            border-color: var(--primary);
        }

        /* ==================== Task Report Styles ==================== */
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
            line-height: 1.6;
            min-height: 50px;
            word-break: break-word;
            white-space: pre-wrap;
        }
        .tr-box-problem {
            background: #fef2f2;
            border-left-color: #dc2626;
            color: #7f1d1d;
        }
        .tr-box-temp {
            background: #fffbeb;
            border-left-color: #d97706;
            color: #78350f;
        }
        .tr-box-perm {
            background: #f0fdf4;
            border-left-color: #16a34a;
            color: #14532d;
        }
        .tr-box-approval {
            background: #f0fdf4;
            border-left-color: #16a34a;
            color: #14532d;
        }
        .tr-empty {
            color: #9ca3af;
            font-style: italic;
        }
        .tr-approval-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 12px;
        }
        .tr-approval-grid .tr-label-inline {
            color: #6b7280;
            font-weight: 600;
        }

        /* Print specific — hanya task report */
        @media print {
            /* Sembunyikan semua */
            body * { visibility: hidden; }

            /* Tampilkan hanya konten report yang sedang aktif */
            body.print-report #reportContent,
            body.print-report #reportContent * { visibility: visible; }

            body.print-task-report #taskReportContent,
            body.print-task-report #taskReportContent * { visibility: visible; }

            body.print-report #reportContent,
            body.print-task-report #taskReportContent {
                position: absolute;
                left: 0; top: 0;
                width: 100%;
                border: none;
                padding: 0;
            }
        }

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

        /* Summary Cards Grid */
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

        /* Status pills */
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

        /* Report table */
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
        .report-table tr:nth-child(even) td {
            background: #fafafa;
        }

        /* Section breakdown */
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

        /* Print mode */
        @media print {
            body * { visibility: hidden; }
            #reportContent, #reportContent * { visibility: visible; }
            #reportContent {
                position: absolute;
                left: 0; top: 0;
                width: 100%;
                border: none;
            }
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

        .board-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .board-subtitle {
            font-size: 13px;
            color: var(--gray-500);
        }

        .board-info {
            display: flex;
            gap: 8px;
            align-items: center;
        }

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

        .kanban-column.locked {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding: 0 4px;
        }

        .column-title {
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .column-icon {
            width: 28px;
            height: 28px;
            border-radius: var(--radius-xs);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .column-plan .column-icon { background: var(--primary-light); color: var(--primary); }
        .column-do .column-icon { background: var(--success-light); color: var(--success); }
        .column-check .column-icon { background: var(--warning-light); color: var(--warning); }
        .column-act .column-icon { background: var(--danger-light); color: var(--danger); }

        .column-count {
            font-size: 11px;
            background: white;
            padding: 2px 8px;
            border-radius: 10px;
            color: var(--gray-500);
            font-weight: 600;
        }

        .kanban-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 50px;
        }

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

        .kanban-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .kanban-card.draggable {
            cursor: grab;
        }

        .kanban-card.dragging {
            opacity: 0.5;
            cursor: grabbing;
            transform: scale(0.95);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .card-title {
            font-weight: 600;
            font-size: 13px;
            color: var(--gray-800);
            flex: 1;
            margin-right: 8px;
        }

        .card-delete {
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .kanban-card:hover .card-delete {
            opacity: 1;
        }

        .card-delete:hover {
            color: var(--danger);
            background: var(--danger-light);
        }

        .card-delete .ti {
            font-size: 16px;
        }

        .card-desc {
            font-size: 12px;
            color: var(--gray-600);
            margin-bottom: 8px;
            word-break: break-word;
        }

        .card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 8px;
        }

        .card-tag {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 500;
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 11px;
            color: var(--gray-500);
            border-top: 1px solid var(--gray-100);
            padding-top: 8px;
        }

        .card-operator {
            font-weight: 500;
            color: var(--gray-700);
        }

        .card-date {
            color: var(--gray-400);
        }

        .card-pending {
            background: var(--warning-light);
            border-color: #fde68a;
        }

        .pending-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 8px;
            background: var(--warning-light);
            color: #92400e;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.2s;
        }

        .modal.active {
            display: flex;
        }

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

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

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

        .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--gray-400);
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: var(--gray-100);
            color: var(--gray-600);
        }

        .modal-body {
            padding: 24px;
        }

        .form-section {
            margin-bottom: 24px;
        }

        .form-section-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--gray-100);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .lock-icon {
            font-size: 12px;
            color: var(--gray-400);
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--gray-700);
        }

        .form-label.required::after {
            content: ' *';
            color: var(--danger);
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius-sm);
            font-size: 14px;
            transition: all 0.2s;
            background: white;
            color: var(--gray-800);
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-input:disabled,
        .form-select:disabled,
        .form-textarea:disabled {
            background: var(--gray-50);
            color: var(--gray-400);
            cursor: not-allowed;
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            position: sticky;
            bottom: 0;
            background: white;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            box-shadow: var(--shadow-lg);
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 14px;
            animation: slideInRight 0.3s;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Status Badges */
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 10px;
            padding: 3px 8px;
            border-radius: 8px;
            font-weight: 600;
            margin-bottom: 6px;
            line-height: 1;
        }
        .badge-approved {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }
        .badge-cancelled {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .badge-pending-approval {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }
        .badge-in-progress {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        /* Card states */
        .kanban-card.card-approved {
            background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%);
            border-color: #86efac;
        }
        .kanban-card.card-cancelled {
            opacity: 0.55;
            background: #f9fafb;
            border-color: #d1d5db;
        }
        .kanban-card.card-cancelled .card-title {
            text-decoration: line-through;
            color: #9ca3af;
        }

        /* Tombol danger (cancel) */
        .btn-danger {
            background: var(--danger);
            border-color: var(--danger);
            color: white;
        }
        .btn-danger:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        /* Tabler icon sizing */
        .ti {
            vertical-align: -0.125em;
            stroke-width: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* Ukuran icon di dalam tombol */
        .btn .ti {
            font-size: 1.1em;
            margin-right: 2px;
        }

        /* Icon di column header */
        .column-icon .ti {
            font-size: 16px;
        }

        /* Icon di badge status */
        .badge-status .ti {
            font-size: 12px;
        }

        /* Icon di notice bar */
        .notice-bar > .ti {
            font-size: 1.3em;
            flex-shrink: 0;
        }

        /* Icon di search box */
        .search-icon .ti {
            font-size: 16px;
            display: block;
        }

        /* Stats icon color */
        .stat-icon .ti {
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .kanban-board {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-actions {
                width: 100%;
                justify-content: space-between;
            }

            .container {
                padding: 12px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .filter-row {
                flex-direction: column;
            }

            .search-box {
                width: 100%;
            }

            .filter-select,
            .date-input {
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .kanban-board {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
    <!-- Header -->
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

    <!-- Main Content -->
    <div class="container">
        <!-- Notice Bar -->
        <div class="notice-bar" id="noticeBar">
            <i class="ti ti-info-circle"></i>
            <span>Anda login sebagai <strong>Operator</strong>. Hanya bisa menambah task baru dengan data dasar.</span>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Plan</span>
                    <span class="stat-icon stat-plan"><i class="ti ti-clipboard-list"></i></span>
                </div>
                <div class="stat-value" id="planCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Do</span>
                    <span class="stat-icon stat-do"><i class="ti ti-bolt"></i></span>
                </div>
                <div class="stat-value" id="doCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Check</span>
                    <span class="stat-icon stat-check"><i class="ti ti-circle-check"></i></span>
                </div>
                <div class="stat-value" id="checkCount">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Act</span>
                    <span class="stat-icon stat-act"><i class="ti ti-tool"></i></span>
                </div>
                <div class="stat-value" id="actCount">0</div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-row">
                <div class="search-box">
                    <span class="search-icon"><i class="ti ti-search"></i></span>
                    <input type="text" class="search-input" id="searchInput" placeholder="Cari task..." oninput="handleSearch(this.value)">
                </div>
                <select class="filter-select" id="sectionFilter" onchange="handleFilter()">
                    <option value="all">Semua Seksi</option>
                    <option value="Produksi">Produksi</option>
                    <option value="Quality Control">Quality Control</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Logistik">Logistik</option>
                    <option value="Engineering">Engineering</option>
                </select>
                <input type="date" class="date-input" id="dateFrom" onchange="handleFilter()" title="Dari Tanggal">
                <input type="date" class="date-input" id="dateTo" onchange="handleFilter()" title="Sampai Tanggal">
                <button class="btn btn-sm" onclick="clearFilters()">
                    <i class="ti ti-refresh"></i> Reset
                </button>
            </div>
        </div>

        <!-- Kanban Board -->
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
                <!-- Plan Column -->
                <div class="kanban-column column-plan" data-stage="plan" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-clipboard-list"></i></span>
                            Plan
                        </span>
                        <span class="column-count" id="planBadge">0</span>
                    </div>
                    <div class="kanban-items" id="planItems"></div>
                </div>

                <!-- Do Column -->
                <div class="kanban-column column-do" data-stage="do" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-bolt"></i></span>
                            Do
                        </span>
                        <span class="column-count" id="doBadge">0</span>
                    </div>
                    <div class="kanban-items" id="doItems"></div>
                </div>

                <!-- Check Column -->
                <div class="kanban-column column-check" data-stage="check" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-circle-check"></i></span>
                            Check
                        </span>
                        <span class="column-count" id="checkBadge">0</span>
                    </div>
                    <div class="kanban-items" id="checkItems"></div>
                </div>

                <!-- Act Column -->
                <div class="kanban-column column-act" data-stage="act" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon"><i class="ti ti-tool"></i></span>
                            Act
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
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Tambah Task Baru</h3>
                <button class="modal-close" onclick="closeModal('taskModal')">×</button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <!-- Operator Section (Selalu bisa diisi) -->
                     <?= csrf()?>
                    <div class="form-section">
                        <div class="form-section-title"><i class="ti ti-notes"></i> Data Operator</div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label required">Nama Operator</label>
                                <input type="text" class="form-input" id="operatorName" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Tanggal</label>
                                <input type="date" class="form-input" id="taskDate" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label required">Seksi</label>
                                <select class="form-select" id="section" required>
                                    <option value="">Pilih Seksi</option>
                                    <option value="Produksi">Produksi</option>
                                    <option value="Quality Control">Quality Control</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Logistik">Logistik</option>
                                    <option value="Engineering">Engineering</option>
                                </select>
                            </div>
                            <div class="form-group full-width">
                                <label class="form-label required">Masalah</label>
                                <textarea class="form-textarea" id="problem" required placeholder="Deskripsikan masalah yang terjadi..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Leader Section (Hanya bisa diisi leader) -->
                    <div class="form-section" id="leaderSection">
                        <div class="form-section-title">
                            <i class="ti ti-tool"></i> Tindakan & Approval 
                            <span class="lock-icon"><i class="ti ti-lock"></i> (Khusus Leader)</span>
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
                                <input type="text" class="form-input" id="pic" disabled placeholder="Nama PIC">
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
                        <input type="text" class="form-input" id="registerName" required
                            placeholder="Nama lengkap Anda">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Username</label>
                        <input type="text" class="form-input" id="registerUsername" required
                            minlength="3" placeholder="Minimal 3 karakter">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Password</label>
                        <input type="password" class="form-input" id="registerPassword" required
                            minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="form-group">
                        <label class="form-label required">Konfirmasi Password</label>
                        <input type="password" class="form-input" id="registerPasswordConfirm" required
                            minlength="6" placeholder="Ulangi password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Daftar Sebagai</label>
                        <select class="form-select" id="registerRole">
                            <option value="operator">Operator</option>
                            <option value="leader">Leader</option>
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

                <!-- Filter Bar -->
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
                                <option value="Produksi">Produksi</option>
                                <option value="Quality Control">Quality Control</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Logistik">Logistik</option>
                                <option value="Engineering">Engineering</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom:0;flex:1;min-width:150px">
                            <label class="form-label">Stage</label>
                            <select class="form-select" id="reportStage">
                                <option value="all">Semua Stage</option>
                                <option value="plan">Plan</option>
                                <option value="do">Do</option>
                                <option value="check">Check</option>
                                <option value="act">Act</option>
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

                <!-- Report Content (yang akan jadi PDF) -->
                <div id="reportContent" style="background:white;padding:20px;border:1px solid #e5e7eb;border-radius:8px;margin-top:16px">

                    <!-- Header Laporan -->
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

                    <!-- Summary Cards -->
                    <div id="reportSummary" class="report-summary-grid"></div>

                    <!-- Section Breakdown -->
                    <div id="reportSectionBreakdown" style="margin-top:20px"></div>

                    <!-- Task Table -->
                    <div style="margin-top:20px">
                        <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;border-bottom:1px solid #e5e7eb;padding-bottom:6px">
                            Detail Task
                        </div>
                        <div id="reportTableWrapper" style="overflow-x:auto"></div>
                    </div>

                    <!-- Signature Area -->
                    <div style="margin-top:32px;display:flex;justify-content:space-between;font-size:12px">
                        <div style="text-align:center;width:30%">
                            <div style="color:#6b7280;margin-bottom:60px">Dibuat oleh,</div>
                            <div style="border-top:1px solid #374151;padding-top:4px;font-weight:600" id="reportCreatedBy">-</div>
                            <div style="color:#9ca3af;font-size:10px">Admin/Leader</div>
                        </div>
                        <div style="text-align:center;width:30%">
                            <div style="color:#6b7280;margin-bottom:60px">Diperiksa oleh,</div>
                            <div style="border-top:1px solid #374151;padding-top:4px;font-weight:600">____________________</div>
                            <div style="color:#9ca3af;font-size:10px">Supervisor</div>
                        </div>
                        <div style="text-align:center;width:30%">
                            <div style="color:#6b7280;margin-bottom:60px">Disetujui oleh,</div>
                            <div style="border-top:1px solid #374151;padding-top:4px;font-weight:600">____________________</div>
                            <div style="color:#9ca3af;font-size:10px">Manager</div>
                        </div>
                    </div>

                    <!-- Footer -->
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
    <!-- Report Per Task Modal -->
    <div class="modal" id="taskReportModal">
        <div class="modal-content" style="max-width: 850px;">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="ti ti-file-description"></i> Laporan Detail Task
                </h3>
                <button class="modal-close" onclick="closeModal('taskReportModal')">×</button>
            </div>
            <div class="modal-body" style="background:#f9fafb">

                <!-- Report Content (yang jadi PDF) -->
                <div id="taskReportContent" style="background:white;padding:24px;border-radius:8px">

                    <!-- Header Laporan -->
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

                    <!-- Status Badge -->
                    <div style="display:flex;gap:8px;margin-bottom:16px;justify-content:center" id="trStatusBadges"></div>

                    <!-- Info Umum -->
                    <div class="tr-section">
                        <div class="tr-section-title">
                            <i class="ti ti-info-circle"></i> Informasi Umum
                        </div>
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

                    <!-- Masalah -->
                    <div class="tr-section">
                        <div class="tr-section-title" style="color:#dc2626;border-bottom-color:#fecaca">
                            <i class="ti ti-alert-triangle"></i> Deskripsi Masalah
                        </div>
                        <div class="tr-content-box tr-box-problem" id="trProblem">-</div>
                    </div>

                    <!-- Tindakan Temporary -->
                    <div class="tr-section">
                        <div class="tr-section-title" style="color:#d97706;border-bottom-color:#fde68a">
                            <i class="ti ti-clock-hour-4"></i> Tindakan Temporary
                        </div>
                        <div class="tr-content-box tr-box-temp" id="trTempAction">-</div>
                    </div>

                    <!-- Tindakan Permanent -->
                    <div class="tr-section">
                        <div class="tr-section-title" style="color:#16a34a;border-bottom-color:#bbf7d0">
                            <i class="ti ti-shield-check"></i> Tindakan Permanent
                        </div>
                        <div class="tr-content-box tr-box-perm" id="trPermAction">-</div>
                    </div>

                    <!-- Approval Info (kalau sudah approved) -->
                    <div class="tr-section" id="trApprovalSection" style="display:none">
                        <div class="tr-section-title" style="color:#16a34a;border-bottom-color:#86efac">
                            <i class="ti ti-circle-check"></i> Approval
                        </div>
                        <div class="tr-content-box tr-box-approval" id="trApprovalContent"></div>
                    </div>

                    <!-- Tanda Tangan -->
                    <div style="margin-top:32px;display:flex;justify-content:space-between;font-size:12px;page-break-inside:avoid">
                        <div style="text-align:center;width:30%">
                            <div style="color:#6b7280;margin-bottom:60px">Dibuat oleh,</div>
                            <div style="border-top:1px solid #374151;padding-top:4px;font-weight:600" id="trSignOperator">-</div>
                            <div style="color:#9ca3af;font-size:10px">Operator</div>
                        </div>
                        <div style="text-align:center;width:30%">
                            <div style="color:#6b7280;margin-bottom:60px">PIC,</div>
                            <div style="border-top:1px solid #374151;padding-top:4px;font-weight:600" id="trSignPic">-</div>
                            <div style="color:#9ca3af;font-size:10px">Penanggung Jawab</div>
                        </div>
                        <div style="text-align:center;width:30%">
                            <div style="color:#6b7280;margin-bottom:60px">Disetujui oleh,</div>
                            <div style="border-top:1px solid #374151;padding-top:4px;font-weight:600" id="trSignLeader">-</div>
                            <div style="color:#9ca3af;font-size:10px">Leader/Admin</div>
                        </div>
                    </div>

                    <!-- Footer -->
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // ============================================================
    // KONFIGURASI
    // ============================================================
    const API_BASE = '<?=url()?>';

    // ============================================================
    // SWEETALERT2 CONFIG
    // ============================================================
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

    const USER_STORAGE_KEY = 'pdca_user_info';

    // ============================================================
    // API HELPER — jQuery AJAX
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
                url:         url.toString(),
                type:        method,
                dataType:    'text',
                contentType: body !== null ? 'application/json; charset=utf-8' : undefined,
                data:        body !== null ? JSON.stringify(body) : undefined,
                xhrFields:   { withCredentials: true },
                headers: {
                    'Accept':           'application/json',
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
                        errorThrown ||
                        textStatus ||
                        `HTTP ${status}`;

                    const err = new Error(msg);
                    err.status = status;
                    err.data   = data;

                    if (status === 401) clearUser();
                    reject(err);
                }
            });
        });
    }

    // ============================================================
    // USER SESSION HELPERS
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
                if (user && user.role) {
                    user.role = String(user.role).toLowerCase();
                }
                state.currentUser = user;
            }
        } catch {
            state.currentUser = null;
        }
    }

    // ============================================================
    // MAPPING backend (snake_case) → frontend (camelCase)
    // ============================================================
    function normalizeTask(t) {
        return {
            id:               t.id,
            taskCode:         t.task_code        || '',
            operatorName:     t.operator_name    || '',
            date:             t.task_date        || '',
            section:          t.section          || '',
            problem:          t.problem          || '',
            tempAction:       t.temporary_action || '',
            permAction:       t.permanent_action || '',
            deadline:         t.deadline         || '',
            pic:              t.pic              || '',
            stage:            (t.stage  || 'plan').toLowerCase(),
            status:           (t.status || 'open').toLowerCase(),
            leaderSignature:  t.ttd_leader || t.leader_signature || '',
            approvedAt:       t.approved_at      || null,
            approvedBy:       t.approved_by      || null,
            createdByName:    t.created_by_name  || '',
            createdByRole:    (t.created_by_role || 'operator').toLowerCase(),
            createdAt:        t.created_at       || null,
            updatedAt:        t.updated_at       || null
        };
    }

    // ============================================================
    // INIT
    // ============================================================
    $(document).ready(async function () {
        setupEventListeners();
        restoreUser();
        updateUserInterface();
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
        openModal('registerModal');
    }

    async function registerUser() {
        const name             = $('#registerName').val().trim();
        const username         = $('#registerUsername').val().trim();
        const password         = $('#registerPassword').val();
        const passwordConfirm  = $('#registerPasswordConfirm').val();
        const role             = $('#registerRole').val();

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
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            const res = await api('/auth/register', {
                method: 'POST',
                body: { name, username, password, role }
            });

            Swal.close();
            closeModal('registerModal');
            $('#registerForm')[0].reset();

            const result = await Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                html: `Akun <strong>${escapeHtml(username)}</strong> berhasil dibuat.<br>
                    Silakan login untuk melanjutkan.`,
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
    // AUTH
    // ============================================================
    function toggleLogin() {
        if (state.currentUser) {
            logout();
        } else {
            openModal('loginModal');
        }
    }

    async function loginLeader() {
        const username = $('#loginUsername').val().trim();
        const password = $('#loginPassword').val();

        if (!username || !password) {
            showAlert('warning', 'Form Tidak Lengkap', 'Username dan password wajib diisi!');
            return;
        }

        try {
            const res = await api('/auth/login', {
                method: 'POST',
                body: { username, password }
            });

            const user = res?.user || res?.data || res;
            if (!user || !user.role) {
                throw new Error('Response login tidak valid (role tidak ditemukan)');
            }

            const role = String(user.role).toLowerCase();

            if (!['admin', 'leader'].includes(role)) {
                throw new Error('Akun ini tidak memiliki akses sebagai Leader/Admin');
            }

            saveUser({
                id:       user.id,
                name:     user.name,
                username: user.username,
                role:     role
            });

            closeModal('loginModal');
            $('#loginForm')[0].reset();
            updateUserInterface();
            showToast('Login berhasil sebagai ' + role, 'success');
            setTimeout(() => window.location.reload(), 800);
        } catch (err) {
            showAlert('error', 'Login Gagal', err.message);
        }
    }

    async function logout() {
        const confirmed = await showConfirm(
            'Logout?',
            'Anda yakin ingin keluar dari akun leader?',
            'Ya, Logout',
            'Batal'
        );
        if (!confirmed) return;

        try {
            await api('/auth/logout', { method: 'POST' });
        } catch (_) {}

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
        const $reportBtnHeader   = $('#reportBtnHeader');

        if (state.currentUser) {
            $userName.text(state.currentUser.name);
            $userInfo.find('.user-avatar').text((state.currentUser.name || 'U').charAt(0).toUpperCase());
            $userInfo.css('background', '#dcfce7');
            $loginBtn.html('<i class="ti ti-logout"></i> Logout');

            const role = state.currentUser.role;
            let label = '';
            let color = '';
            $reportBtnHeader.show();
            if (role === 'admin') {
                label = 'Admin';
                color = '#7c3aed';
                $registerBtnHeader.show();
            } else if (role === 'leader') {
                label = 'Leader';
                color = '#16a34a';
                $registerBtnHeader.show();
            } else {
                label = role.charAt(0).toUpperCase() + role.slice(1);
                color = '#2563eb';
                $registerBtnHeader.hide();
            }

            $noticeBar.html(
                '<i class="ti ti-circle-check"></i>' +
                '<span>Anda login sebagai <strong style="color:' + color + '">' + label + '</strong>. ' +
                'Bisa mengisi tindakan, deadline, PIC, dan melakukan drag & drop.</span>'
            );
            $noticeBar.attr('class', 'notice-bar');

            if (role === 'admin') {
                $registerBtnHeader.show();
            } else {
                $registerBtnHeader.hide();
                $reportBtnHeader.hide();
            }

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
        }
    }

    // ============================================================
    // SEARCH & FILTER (client-side)
    // ============================================================
    function handleSearch(q) {
        state.searchQuery = (q || '').toLowerCase();
        render();
    }
    function handleFilter() {
        state.sectionFilter = $('#sectionFilter').val();
        state.dateFrom = $('#dateFrom').val() || null;
        state.dateTo   = $('#dateTo').val()   || null;
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
        render();
    }

    function getFilteredTasks() {
        return state.tasks.filter(task => {
            if (task.status === 'cancelled') return false;

            if (state.searchQuery) {
                const s = [
                    task.operatorName, task.section, task.problem,
                    task.tempAction, task.permAction, task.pic, task.taskCode
                ].join(' ').toLowerCase();
                if (!s.includes(state.searchQuery)) return false;
            }
            if (state.sectionFilter !== 'all' && task.section !== state.sectionFilter) return false;

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
    function canDragDrop() {
        return state.currentUser && ['leader', 'admin'].includes(state.currentUser.role);
    }

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

        const taskId   = parseInt(e.dataTransfer.getData('text/plain'), 10);
        const newStage = column.dataset.stage;
        const task     = state.tasks.find(t => t.id === taskId);

        cleanupDragUI();
        if (!task || task.stage === newStage) return;

        const oldStage = task.stage;
        task.stage = newStage;
        render();

        try {
            await api(`/tasks/${taskId}/stage`, {
                method: 'PATCH',
                body: { stage: newStage }
            });
            showToast(`Task dipindahkan ke ${newStage.toUpperCase()}`, 'success');
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

    // ============================================================
    // TASK MODAL & CRUD
    // ============================================================
    function openTaskModal() {
        const isLeader = canDragDrop();

        $('#modalTitle').text(isLeader ? 'Tambah Task Baru (Leader)' : 'Tambah Task Baru (Operator)');
        $('#taskForm')[0].reset();
        $('#taskId').val('');
        $('#taskDate').val(new Date().toISOString().split('T')[0]);

        $('#tempAction').prop('disabled', !isLeader);
        $('#permAction').prop('disabled', !isLeader);
        $('#deadline').prop('disabled', !isLeader);
        $('#pic').prop('disabled', !isLeader);
        $('#leaderSection').css('display', isLeader ? 'block' : 'none');

        $('#historyTaskBtn').hide();
        $('#reportTaskBtn').hide();
        $('#approveTaskBtn').hide();
        $('#cancelTaskBtn').hide();

        openModal('taskModal');
    }

    async function saveTask() {
        const id = $('#taskId').val();
        const isLeader = canDragDrop();

        const basePayload = {
            operator_name: $('#operatorName').val().trim(),
            task_date:     $('#taskDate').val(),
            section:       $('#section').val(),
            problem:       $('#problem').val().trim()
        };

        if (!basePayload.operator_name || !basePayload.task_date ||
            !basePayload.section || !basePayload.problem) {
            showAlert('warning', 'Form Tidak Lengkap', 'Mohon isi semua field yang wajib!');
            return;
        }

        const leaderPayload = {
            temporary_action: $('#tempAction').val().trim(),
            permanent_action: $('#permAction').val().trim(),
            deadline:         $('#deadline').val() || null,
            pic:              $('#pic').val().trim()
        };

        Swal.fire({
            title: 'Menyimpan...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            if (id) {
                await api(`/tasks/${id}`, {
                    method: 'PUT',
                    body: {
                        operator_name: basePayload.operator_name,
                        task_date:     basePayload.task_date,
                        section:       basePayload.section,
                        problem:       basePayload.problem,
                        ...(isLeader ? leaderPayload : {})
                    }
                });
                showToast('Task berhasil diupdate', 'success');
            } else {
                const created = await api('/tasks', { method: 'POST', body: basePayload });
                const newId = created?.id ?? created?.data?.id;

                if (isLeader && newId && (
                    leaderPayload.temporary_action ||
                    leaderPayload.permanent_action ||
                    leaderPayload.deadline ||
                    leaderPayload.pic
                )) {
                    await api(`/tasks/${newId}`, {
                        method: 'PUT',
                        body: leaderPayload
                    });
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

    function editTask(id) {
        if (!canDragDrop()) {
            showConfirm(
                'Akses Ditolak',
                'Silakan login sebagai leader untuk mengedit task ini.',
                'Login Sekarang',
                'Batal'
            ).then(confirmed => {
                if (confirmed) openModal('loginModal');
            });
            return;
        }
        const task = state.tasks.find(t => t.id === id);
        if (!task) return;

        $('#modalTitle').text('Edit Task (Leader)');
        $('#taskId').val(task.id);
        $('#operatorName').val(task.operatorName);
        $('#taskDate').val(task.date);
        $('#section').val(task.section);
        $('#problem').val(task.problem);
        $('#tempAction').val(task.tempAction);
        $('#permAction').val(task.permAction);
        $('#deadline').val(task.deadline);
        $('#pic').val(task.pic);

        $('#tempAction, #permAction, #deadline, #pic').prop('disabled', false);
        $('#leaderSection').css('display', 'block');

        const isDone      = task.status === 'done';
        const isCancelled = task.status === 'cancelled';
        const isAct       = task.stage === 'act';

        if (isAct && !isDone && !isCancelled) {
            $('#approveTaskBtn').show();
        } else {
            $('#approveTaskBtn').hide();
        }

        if (!isDone && !isCancelled) {
            $('#cancelTaskBtn').show();
        } else {
            $('#cancelTaskBtn').hide();
        }

        $('#historyTaskBtn').show();
        $('#reportTaskBtn').show();

        if (isDone || isCancelled) {
            $('#operatorName, #taskDate, #section, #problem, #tempAction, #permAction, #deadline, #pic')
                .prop('disabled', true);
            $('#saveBtn').prop('disabled', true);
        } else {
            $('#saveBtn').prop('disabled', false);
        }

        openModal('taskModal');
    }

    async function deleteTask(id) {
        if (!canDragDrop()) {
            const confirmed = await showConfirm(
                'Akses Ditolak',
                'Silakan login sebagai leader untuk menghapus task ini.',
                'Login Sekarang',
                'Batal'
            );
            if (confirmed) openModal('loginModal');
            return;
        }

        const task = state.tasks.find(t => t.id === id);
        const title = task ? escapeHtml(task.problem) : 'task ini';

        const confirmed = await showConfirm(
            'Hapus Task?',
            `Anda yakin ingin menghapus task "<strong>${title}</strong>"?<br><small style="color:#6b7280">Tindakan ini tidak bisa dibatalkan.</small>`,
            'Ya, Hapus',
            'Batal',
            'warning'
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
        const id = $('#taskId').val();
        if (!id) return;

        const task = state.tasks.find(t => t.id === id);
        const title = task ? escapeHtml(task.problem) : 'task ini';

        const confirmed = await showConfirm(
            'Approve Task?',
            `Task "<strong>${title}</strong>" akan ditandai <strong>DONE</strong> dan tidak bisa diubah lagi.<br><br>
            <small style="color:#6b7280">Tanda tangan: <strong>${escapeHtml(state.currentUser?.name || 'Leader')}</strong></small>`,
            'Ya, Approve',
            'Batal',
            'question'
        );
        if (!confirmed) return;

        Swal.fire({
            title: 'Memproses...',
            html: 'Menyimpan approval',
            allowOutsideClick: false,
            allowEscapeKey: false,
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
            'Ya, Batalkan',
            'Batal',
            'warning'
        );
        if (!confirmed) return;

        Swal.fire({
            title: 'Memproses...',
            html: 'Membatalkan task',
            allowOutsideClick: false,
            allowEscapeKey: false,
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
            'create':       { icon: 'ti-plus',            color: '#2563eb', bg: '#eff6ff', label: 'Dibuat' },
            'update':       { icon: 'ti-pencil',          color: '#7c3aed', bg: '#f5f3ff', label: 'Diupdate' },
            'move_stage':   { icon: 'ti-arrow-right',     color: '#0891b2', bg: '#ecfeff', label: 'Pindah Stage' },
            'approve':      { icon: 'ti-circle-check',    color: '#16a34a', bg: '#f0fdf4', label: 'Approved' },
            'cancel':       { icon: 'ti-circle-x',        color: '#dc2626', bg: '#fef2f2', label: 'Cancelled' },
            'delete':       { icon: 'ti-trash',           color: '#dc2626', bg: '#fef2f2', label: 'Dihapus' },
            'restore':      { icon: 'ti-restore',         color: '#16a34a', bg: '#f0fdf4', label: 'Direstore' },
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

        const timelineHtml = histories.map((h, idx) => {
            const meta = iconMap[h.action] || { icon: 'ti-activity', color: '#6b7280', bg: '#f3f4f6', label: h.action };
            const isLast = idx === histories.length - 1;

            let changeHtml = '';
            if (h.oldStage && h.newStage && h.oldStage !== h.newStage) {
                changeHtml += `
                    <div style="font-size:12px;margin-top:4px;color:#374151">
                        <span style="background:#f3f4f6;padding:2px 6px;border-radius:4px">${(h.oldStage||'').toUpperCase()}</span>
                        <i class="ti ti-arrow-right" style="margin:0 6px;color:#9ca3af"></i>
                        <span style="background:#dbeafe;padding:2px 6px;border-radius:4px;color:#1e40af;font-weight:600">${(h.newStage||'').toUpperCase()}</span>
                    </div>`;
            }
            if (h.oldStatus && h.newStatus && h.oldStatus !== h.newStatus) {
                changeHtml += `
                    <div style="font-size:12px;margin-top:4px;color:#374151">
                        <span style="background:#f3f4f6;padding:2px 6px;border-radius:4px">${(h.oldStatus||'').toUpperCase()}</span>
                        <i class="ti ti-arrow-right" style="margin:0 6px;color:#9ca3af"></i>
                        <span style="background:#dcfce7;padding:2px 6px;border-radius:4px;color:#166534;font-weight:600">${(h.newStatus||'').toUpperCase()}</span>
                    </div>`;
            }

            return `
                <div style="display:flex;gap:12px;position:relative;padding-bottom:${isLast ? '0' : '20px'}">
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
                    </div>
                </div>
            `;
        }).join('');

        Swal.fire({
            title: 'History Task',
            html: `
                <div style="text-align:left;max-height:60vh;overflow-y:auto;padding:4px 8px">
                    <div style="background:#f9fafb;border-radius:8px;padding:12px;margin-bottom:16px;font-size:13px">
                        <div style="color:#6b7280;margin-bottom:4px">Task:</div>
                        <div style="font-weight:600;color:#111827">${title}</div>
                        <div style="font-size:12px;color:#6b7280;margin-top:4px">
                            <i class="ti ti-hash"></i> ${escapeHtml(task?.taskCode || '-')}
                        </div>
                    </div>
                    <div>${timelineHtml}</div>
                </div>
            `,
            width: 650,
            confirmButtonText: 'Tutup',
            confirmButtonColor: '#2563eb'
        });
    }

    // ============================================================
    // REPORT
    // ============================================================
    let reportData = null;

    function openReport() {
        if (!canDragDrop()) {
            showAlert('warning', 'Akses Ditolak', 'Silakan login sebagai leader/admin untuk melihat report.');
            return;
        }

        // Set default date range: bulan ini
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay  = new Date(now.getFullYear(), now.getMonth() + 1, 0);

        const fmt = d => d.toISOString().split('T')[0];
        if (!$('#reportDateFrom').val()) $('#reportDateFrom').val(fmt(firstDay));
        if (!$('#reportDateTo').val())   $('#reportDateTo').val(fmt(lastDay));

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
                    date_to:   $('#reportDateTo').val()   || '',
                    section:   $('#reportSection').val(),
                    stage:     $('#reportStage').val(),
                    status:    $('#reportStatus').val(),
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
        const tasks   = data.tasks   || [];
        const period  = data.period  || {};

        // Period text
        let periodText = 'Periode: Semua Data';
        if (period.from && period.to) {
            periodText = `Periode: ${formatDate(period.from)} — ${formatDate(period.to)}`;
        } else if (period.from) {
            periodText = `Periode: Sejak ${formatDate(period.from)}`;
        } else if (period.to) {
            periodText = `Periode: Sampai ${formatDate(period.to)}`;
        }
        $('#reportPeriod').text(periodText);

        // Summary cards
        const byStage = summary.by_stage || {};
        $('#reportSummary').html(`
            <div class="report-stat-card plan">
                <div class="report-stat-label">Plan</div>
                <div class="report-stat-value">${byStage.plan || 0}</div>
            </div>
            <div class="report-stat-card do">
                <div class="report-stat-label">Do</div>
                <div class="report-stat-value">${byStage.do || 0}</div>
            </div>
            <div class="report-stat-card check">
                <div class="report-stat-label">Check</div>
                <div class="report-stat-value">${byStage.check || 0}</div>
            </div>
            <div class="report-stat-card act">
                <div class="report-stat-label">Act</div>
                <div class="report-stat-value">${byStage.act || 0}</div>
            </div>
        `);

        // Section breakdown
        const bySection = summary.by_section || {};
        const sectionKeys = Object.keys(bySection).sort((a, b) => bySection[b] - bySection[a]);
        const maxSection = Math.max(1, ...Object.values(bySection));

        let sectionHtml = `
            <div style="font-size:14px;font-weight:600;color:#1f2937;margin-bottom:8px;border-bottom:1px solid #e5e7eb;padding-bottom:6px">
                Distribusi per Seksi
            </div>`;

        if (sectionKeys.length === 0) {
            sectionHtml += `<div style="text-align:center;color:#9ca3af;font-size:12px;padding:12px">Tidak ada data</div>`;
        } else {
            sectionKeys.forEach(key => {
                const count = bySection[key];
                const pct   = Math.round((count / maxSection) * 100);
                sectionHtml += `
                    <div class="section-breakdown-item">
                        <div style="width:140px;font-weight:500;color:#374151">${escapeHtml(key)}</div>
                        <div class="section-breakdown-bar"><div style="width:${pct}%"></div></div>
                        <div style="width:40px;text-align:right;font-weight:600;color:#2563eb">${count}</div>
                    </div>`;
            });
        }
        $('#reportSectionBreakdown').html(sectionHtml);

        // Status summary
        const byStatus = summary.by_status || {};

        // Table
        let tableHtml = '';
        if (tasks.length === 0) {
            tableHtml = `<div style="text-align:center;color:#9ca3af;font-size:12px;padding:20px;border:1px dashed #e5e7eb;border-radius:8px">Tidak ada task pada periode ini</div>`;
        } else {
            tableHtml = `
                <table class="report-table">
                    <thead>
                        <tr>
                            <th style="width:30px">No</th>
                            <th style="width:130px">Kode</th>
                            <th style="width:90px">Tanggal</th>
                            <th style="width:100px">Seksi</th>
                            <th>Masalah</th>
                            <th style="width:70px">Stage</th>
                            <th style="width:90px">Status</th>
                            <th style="width:90px">PIC</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tasks.map((t, i) => `
                            <tr>
                                <td style="text-align:center">${i + 1}</td>
                                <td><strong>${escapeHtml(t.taskCode)}</strong></td>
                                <td>${formatDate(t.date)}</td>
                                <td>${escapeHtml(t.section)}</td>
                                <td>${escapeHtml(t.problem)}</td>
                                <td style="text-align:center">
                                    <span class="stage-pill">${t.stage}</span>
                                </td>
                                <td style="text-align:center">
                                    <span class="status-pill status-${t.status}">${t.status}</span>
                                </td>
                                <td>${escapeHtml(t.pic || '-')}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right;font-weight:600">TOTAL</td>
                            <td colspan="4" style="font-weight:600">
                                ${tasks.length} task — 
                                <span class="status-pill status-done">${byStatus.done || 0} done</span>
                                <span class="status-pill status-in_progress">${byStatus.in_progress || 0} progress</span>
                                <span class="status-pill status-open">${byStatus.open || 0} open</span>
                                <span class="status-pill status-cancelled">${byStatus.cancelled || 0} cancelled</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            `;
        }
        $('#reportTableWrapper').html(tableHtml);

        // Footer meta
        $('#reportCreatedBy').text(state.currentUser?.name || '-');
        $('#reportGeneratedAt').text(new Date().toLocaleString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        }));
    }

    // ============================================================
    // EXPORT PDF
    // ============================================================
    async function exportPDF() {
        const element = document.getElementById('reportContent');
        if (!element) return;

        const periodFrom = $('#reportDateFrom').val() || 'all';
        const periodTo   = $('#reportDateTo').val()   || 'all';
        const filename   = `laporan-pdca-${periodFrom}-${periodTo}.pdf`;

        Swal.fire({
            title: 'Menyiapkan PDF...',
            html: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => Swal.showLoading()
        });

        try {
            await html2pdf().set({
                margin:      [10, 10, 10, 10],
                filename:    filename,
                image:       { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
                jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak:   { mode: ['avoid-all', 'css', 'legacy'] }
            }).from(element).save();

            Swal.close();
            showToast('PDF berhasil diunduh', 'success');
        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Export PDF', err.message);
        }
    }

    function printReport() {
        document.body.classList.add('print-report');
        setTimeout(() => {
            window.print();
            setTimeout(() => document.body.classList.remove('print-report'), 1000);
        }, 100);
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

        // ============================================================
        // Header Info
        // ============================================================
        $('#trTaskCode').text(task.taskCode || '-');
        $('#trOperatorName').text(task.operatorName || '-');
        $('#trTaskDate').text(task.date ? formatDateLong(task.date) : '-');
        $('#trSection').text(task.section || '-');
        $('#trPic').text(task.pic || '-');
        $('#trStage').text((task.stage || '-').toUpperCase());
        $('#trStatus').text((task.status || '-').toUpperCase());
        $('#trDeadline').text(task.deadline ? formatDateLong(task.deadline) : '-');
        $('#trCreatedAt').text(task.createdAt ? formatDateTime(task.createdAt) : '-');

        // ============================================================
        // Status Badges
        // ============================================================
        let badges = [];
        const isDone      = task.status === 'done';
        const isCancelled = task.status === 'cancelled';
        const isInProgress = task.status === 'in_progress';

        if (isDone) {
            badges.push(`<span class="badge-status badge-approved"><i class="ti ti-circle-check"></i> APPROVED</span>`);
        } else if (isCancelled) {
            badges.push(`<span class="badge-status badge-cancelled"><i class="ti ti-circle-x"></i> CANCELLED</span>`);
        } else if (task.stage === 'act' && isInProgress) {
            badges.push(`<span class="badge-status badge-pending-approval"><i class="ti ti-clock-hour-4"></i> MENUNGGU APPROVAL</span>`);
        } else if (isInProgress) {
            badges.push(`<span class="badge-status badge-in-progress"><i class="ti ti-bolt"></i> IN PROGRESS</span>`);
        } else {
            badges.push(`<span class="badge-status" style="background:#dbeafe;color:#1e40af;border:1px solid #93c5fd"><i class="ti ti-flag"></i> OPEN</span>`);
        }
        badges.push(`<span class="stage-pill">STAGE: ${(task.stage || '').toUpperCase()}</span>`);
        $('#trStatusBadges').html(badges.join(''));

        // ============================================================
        // Content Boxes
        // ============================================================
        $('#trProblem').html(
            task.problem
                ? escapeHtml(task.problem)
                : '<span class="tr-empty">Tidak ada deskripsi masalah</span>'
        );
        $('#trTempAction').html(
            task.tempAction
                ? escapeHtml(task.tempAction)
                : '<span class="tr-empty">Belum ada tindakan temporary</span>'
        );
        $('#trPermAction').html(
            task.permAction
                ? escapeHtml(task.permAction)
                : '<span class="tr-empty">Belum ada tindakan permanent</span>'
        );

        // ============================================================
        // Approval Section
        // ============================================================
        if (task.approvedAt || task.leaderSignature) {
            $('#trApprovalSection').show();
            $('#trApprovalContent').html(`
                <div class="tr-approval-grid">
                    <div>
                        <span class="tr-label-inline">Tanda Tangan:</span>
                        <strong>${escapeHtml(task.leaderSignature || '-')}</strong>
                    </div>
                    <div>
                        <span class="tr-label-inline">Waktu Approval:</span>
                        <strong>${task.approvedAt ? formatDateTime(task.approvedAt) : '-'}</strong>
                    </div>
                </div>
            `);
        } else {
            $('#trApprovalSection').hide();
        }

        // ============================================================
        // Signature Area
        // ============================================================
        $('#trSignOperator').text(task.operatorName || '-');
        $('#trSignPic').text(task.pic || '-');
        $('#trSignLeader').text(task.leaderSignature || '-');

        // ============================================================
        // Footer
        // ============================================================
        $('#trGeneratedAt').text(new Date().toLocaleString('id-ID', {
            day: '2-digit', month: 'long', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        }));

        // Show modal
        openModal('taskReportModal');
    }

    // ============================================================
    // EXPORT PDF — Per Task
    // ============================================================
    async function exportTaskPDF() {
        const element = document.getElementById('taskReportContent');
        if (!element) return;

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
            await html2pdf().set({
                margin:      [10, 10, 10, 10],
                filename:    filename,
                image:       { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, backgroundColor: '#ffffff' },
                jsPDF:       { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak:   { mode: ['avoid-all', 'css', 'legacy'] }
            }).from(element).save();

            Swal.close();
            showToast('PDF berhasil diunduh', 'success');
        } catch (err) {
            Swal.close();
            showAlert('error', 'Gagal Export PDF', err.message);
        }
    }

    // ============================================================
    // PRINT — Per Task
    // ============================================================
    function printTaskReport() {
        document.body.classList.add('print-task-report');
        setTimeout(() => {
            window.print();
            // Hapus class setelah print dialog ditutup
            setTimeout(() => document.body.classList.remove('print-task-report'), 1000);
        }, 100);
    }

    // ============================================================
    // Helper: Format Tanggal Panjang (Indonesian)
    // ============================================================
    function formatDateLong(d) {
        if (!d) return '-';
        const date = new Date(d);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }

    function viewTask(id) {
        const task = state.tasks.find(t => t.id === id);
        if (!task) return;

        const approvalInfo = task.approvedAt ? `
            <hr style="margin:12px 0;border:none;border-top:1px solid #e5e7eb">
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:12px">
                <div style="color:#166534;font-weight:600;margin-bottom:6px">
                    <i class="ti ti-circle-check"></i> APPROVED
                </div>
                <div style="font-size:12px;color:#374151">
                    <div><strong>Tanda Tangan:</strong> ${escapeHtml(task.leaderSignature || '-')}</div>
                    <div><strong>Waktu:</strong> ${task.approvedAt}</div>
                </div>
            </div>
        ` : '';

        const cancelledInfo = task.status === 'cancelled' ? `
            <hr style="margin:12px 0;border:none;border-top:1px solid #e5e7eb">
            <div style="background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px;color:#991b1b">
                <strong><i class="ti ti-circle-x"></i> TASK DIBATALKAN</strong>
            </div>
        ` : '';

        const html = `
            <div style="text-align:left;font-size:14px;line-height:1.7">
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
            </div>
        `;

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
            if (result.isConfirmed) {
                openTaskReport(id);
            }
        });
    }

    function formatDateTime(d) {
        if (!d) return '-';
        const date = new Date(d.replace(' ', 'T'));
        return date.toLocaleString('id-ID', {
            day:    '2-digit',
            month:  'short',
            year:   'numeric',
            hour:   '2-digit',
            minute: '2-digit'
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
    }

    function renderStats(list) {
        $('#planCount').text(list.filter(t => t.stage === 'plan').length);
        $('#doCount').text(list.filter(t => t.stage === 'do').length);
        $('#checkCount').text(list.filter(t => t.stage === 'check').length);
        $('#actCount').text(list.filter(t => t.stage === 'act').length);
    }

    function renderKanban(filtered, isLeader) {
        ['plan', 'do', 'check', 'act'].forEach(stage => {
            const $container = $(`#${stage}Items`);
            const $badge     = $(`#${stage}Badge`);
            const $column    = $container.closest('.kanban-column');
            const items      = filtered.filter(t => t.stage === stage);

            $badge.text(items.length);
            $column.toggleClass('locked', !isLeader);

            if (items.length === 0) {
                $container.html(`<div style="text-align:center;padding:20px;color:#9ca3af;font-size:13px;">
                    ${isLeader ? 'Drop task di sini' : 'Tidak ada task'}
                </div>`);
                return;
            }

            $container.html(items.map(task => {
            const isPending  = !task.tempAction && !task.permAction && !task.deadline && !task.pic;
            const isDone     = task.status === 'done';
            const isCancelled = task.status === 'cancelled';
            const isInProgress = task.status === 'in_progress';
            const isActStage = task.stage === 'act';

            let badgeHtml = '';
            if (isDone) {
                badgeHtml = `<div class="badge-status badge-approved">
                    <i class="ti ti-circle-check"></i> Approved${task.leaderSignature ? ' · ' + escapeHtml(task.leaderSignature) : ''}
                </div>`;
            } else if (isCancelled) {
                badgeHtml = `<div class="badge-status badge-cancelled">
                    <i class="ti ti-circle-x"></i> Cancelled
                </div>`;
            } else if (isActStage && isInProgress) {
                badgeHtml = `<div class="badge-status badge-pending-approval">
                    <i class="ti ti-clock-hour-4"></i> Menunggu Approval
                </div>`;
            } else if (isInProgress && !isPending) {
                badgeHtml = `<div class="badge-status badge-in-progress">
                    <i class="ti ti-bolt"></i> In Progress
                </div>`;
            }

            const cardClasses = [
                'kanban-card',
                isLeader ? 'draggable' : '',
                isPending && !isDone && !isCancelled ? 'card-pending' : '',
                isDone ? 'card-approved' : '',
                isCancelled ? 'card-cancelled' : ''
            ].filter(Boolean).join(' ');

            return `
                <div class="${cardClasses}"
                    ${isLeader ? `draggable="true" ondragstart="handleDragStart(event, ${task.id})" ondragend="handleDragEnd(event)"` : ''}
                    onclick="${isLeader ? `editTask(${task.id})` : `viewTask(${task.id})`}">
                    <div class="card-header">
                        <div class="card-title">${escapeHtml(task.problem)}</div>
                        ${isLeader && !isDone && !isCancelled
                            ? `<button class="card-delete" onclick="event.stopPropagation(); deleteTask(${task.id})" title="Hapus">
                                <i class="ti ti-trash"></i>
                               </button>`
                            : ''}
                    </div>
                    ${badgeHtml}
                    ${isPending && !isDone && !isCancelled
                        ? '<div class="pending-badge"><i class="ti ti-alert-triangle"></i> Menunggu tindakan leader</div>'
                        : ''}
                    ${task.tempAction ? `<div class="card-desc">Temp: ${escapeHtml(task.tempAction)}</div>` : ''}
                    ${task.permAction ? `<div class="card-desc">Perm: ${escapeHtml(task.permAction)}</div>` : ''}
                    <div class="card-meta">
                        <span class="card-tag">${escapeHtml(task.section)}</span>
                        ${task.pic ? `<span class="card-tag">PIC: ${escapeHtml(task.pic)}</span>` : ''}
                    </div>
                    <div class="card-footer">
                        <span class="card-operator">${escapeHtml(task.operatorName)}</span>
                        <span class="card-date">${task.deadline ? `Deadline: ${formatDate(task.deadline)}` : formatDate(task.date)}</span>
                    </div>
                </div>
            `;
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
    function openModal(id)  { $('#' + id).addClass('active'); }
    function closeModal(id) { $('#' + id).removeClass('active'); }

    // ============================================================
    // SWEETALERT2 HELPERS
    // ============================================================
    function showToast(message, icon = 'success') {
        SwalToast.fire({
            icon: icon,
            title: message
        });
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
    </script>
</body>
</html>