<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flow - Advanced PDCA Board</title>
    <style>
        :root {
            --bg: #f8fafc;
            --surface: #ffffff;
            --border: #e2e8f0;
            --text: #0f172a;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --primary: #4f46e5;
            --primary-light: #eef2ff;
            --success: #059669;
            --success-light: #ecfdf5;
            --warning: #d97706;
            --warning-light: #fffbeb;
            --danger: #dc2626;
            --danger-light: #fef2f2;
            --info: #0284c7;
            --info-light: #f0f9ff;
            --radius: 12px;
            --radius-sm: 8px;
            --radius-xs: 6px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
            --transition: all 0.2s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            line-height: 1.5;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 16px 24px;
            margin-bottom: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 16px;
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
            font-size: 20px;
            font-weight: bold;
        }

        .title {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        /* Search and Filter Bar */
        .filter-bar {
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

        .search-input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            transition: var(--transition);
            background: var(--bg);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            background: var(--surface);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .filter-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            background: var(--bg);
            cursor: pointer;
            transition: var(--transition);
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .date-input {
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 14px;
            background: var(--bg);
            transition: var(--transition);
        }

        .date-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Buttons */
        .btn {
            padding: 10px 16px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--surface);
            color: var(--text);
            white-space: nowrap;
        }

        .btn:hover {
            background: var(--bg);
            border-color: #cbd5e1;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
            border-color: #4338ca;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .stat-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
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
            color: var(--text-secondary);
        }

        .stat-badge {
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
            letter-spacing: -0.5px;
        }

        .stat-footer {
            margin-top: 4px;
            font-size: 11px;
            color: var(--text-muted);
        }

        .stat-plan .stat-badge { background: var(--info-light); }
        .stat-do .stat-badge { background: var(--success-light); }
        .stat-check .stat-badge { background: var(--warning-light); }
        .stat-act .stat-badge { background: var(--danger-light); }

        /* Kanban Board */
        .board-container {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 16px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
        }

        .board-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .board-title-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .board-title {
            font-size: 18px;
            font-weight: 600;
        }

        .board-subtitle {
            font-size: 13px;
            color: var(--text-secondary);
        }

        .board-info {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .info-badge {
            padding: 4px 8px;
            background: var(--bg);
            border-radius: 12px;
            font-size: 12px;
            color: var(--text-secondary);
        }

        .kanban-board {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            min-height: 400px;
            max-height: 600px;
            overflow-y: auto;
        }

        .kanban-column {
            background: var(--bg);
            border: 2px solid transparent;
            border-radius: var(--radius);
            padding: 12px;
            transition: var(--transition);
            min-height: 200px;
        }

        .kanban-column.drag-over {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .column-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            padding: 0 4px;
            position: sticky;
            top: 0;
            background: var(--bg);
            z-index: 1;
        }

        .column-title {
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .column-icon {
            width: 24px;
            height: 24px;
            border-radius: var(--radius-xs);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .column-count {
            font-size: 11px;
            background: var(--surface);
            padding: 2px 6px;
            border-radius: 10px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .kanban-items {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-height: 50px;
        }

        .kanban-item {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px;
            cursor: grab;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
        }

        .kanban-item:hover {
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }

        .kanban-item.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .kanban-item-title {
            font-weight: 500;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .kanban-item-desc {
            font-size: 11px;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .kanban-item-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .kanban-item-date {
            font-size: 10px;
            color: var(--text-muted);
        }

        .kanban-item-tag {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 8px;
            font-weight: 500;
        }

        .tag-plan { background: var(--info-light); color: var(--info); }
        .tag-do { background: var(--success-light); color: var(--success); }
        .tag-check { background: var(--warning-light); color: var(--warning); }
        .tag-act { background: var(--danger-light); color: var(--danger); }

        .kanban-item-delete {
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
            transition: var(--transition);
            opacity: 0;
            font-size: 12px;
        }

        .kanban-item:hover .kanban-item-delete {
            opacity: 1;
        }

        .kanban-item-delete:hover {
            color: var(--danger);
            background: var(--danger-light);
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            flex-wrap: wrap;
        }

        .page-btn {
            padding: 6px 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            background: var(--surface);
            cursor: pointer;
            transition: var(--transition);
            font-size: 13px;
            min-width: 32px;
        }

        .page-btn:hover {
            background: var(--bg);
        }

        .page-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 48px;
            margin-bottom: 12px;
        }

        .empty-text {
            font-size: 14px;
            margin-bottom: 4px;
        }

        .empty-subtext {
            font-size: 12px;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--surface);
            border-radius: var(--radius);
            padding: 24px;
            width: 90%;
            max-width: 500px;
            animation: slideUp 0.3s ease;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            border-radius: var(--radius-xs);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .modal-close:hover {
            background: var(--bg);
            color: var(--text);
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-xs);
            font-size: 14px;
            transition: var(--transition);
            background: var(--surface);
            color: var(--text);
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            box-shadow: var(--shadow-lg);
            animation: slideInRight 0.3s ease;
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 14px;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .kanban-board {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .container {
                padding: 10px;
            }
            
            .header {
                padding: 12px;
            }
            
            .header-top {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-bar {
                flex-direction: column;
            }
            
            .kanban-board {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header-actions {
                width: 100%;
            }
            
            .header-actions .btn {
                flex: 1;
                justify-content: center;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-select,
            .date-input {
                flex: 1;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-top">
                <div class="header-left">
                    <div class="logo">F</div>
                    <span class="title">Flow Board</span>
                </div>
                <div class="header-actions">
                    <button class="btn btn-primary" onclick="openModal('todo')">
                        <span>+</span> New Task
                    </button>
                    <button class="btn btn-success" onclick="openModal('pdca')">
                        <span>+</span> New PDCA
                    </button>
                    <button class="btn" onclick="exportData()">
                        <span>📊</span> Export
                    </button>
                </div>
            </div>
            
            <!-- Search and Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" 
                           class="search-input" 
                           id="searchInput" 
                           placeholder="Search tasks and PDCA items..."
                           oninput="handleSearch(this.value)">
                </div>
                <div class="filter-group">
                    <select class="filter-select" id="stageFilter" onchange="handleFilter()">
                        <option value="all">All Stages</option>
                        <option value="plan">Plan</option>
                        <option value="do">Do</option>
                        <option value="check">Check</option>
                        <option value="act">Act</option>
                    </select>
                    <input type="date" class="date-input" id="dateFrom" onchange="handleFilter()">
                    <input type="date" class="date-input" id="dateTo" onchange="handleFilter()">
                    <button class="btn btn-sm" onclick="clearFilters()">Clear</button>
                </div>
            </div>
        </header>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card stat-plan">
                <div class="stat-header">
                    <span class="stat-label">Plan</span>
                    <span class="stat-badge">🎯</span>
                </div>
                <div class="stat-value" id="planCount">0</div>
                <div class="stat-footer" id="planProgress">0% of total</div>
            </div>
            <div class="stat-card stat-do">
                <div class="stat-header">
                    <span class="stat-label">Do</span>
                    <span class="stat-badge">⚡</span>
                </div>
                <div class="stat-value" id="doCount">0</div>
                <div class="stat-footer" id="doProgress">0% of total</div>
            </div>
            <div class="stat-card stat-check">
                <div class="stat-header">
                    <span class="stat-label">Check</span>
                    <span class="stat-badge">✅</span>
                </div>
                <div class="stat-value" id="checkCount">0</div>
                <div class="stat-footer" id="checkProgress">0% of total</div>
            </div>
            <div class="stat-card stat-act">
                <div class="stat-header">
                    <span class="stat-label">Act</span>
                    <span class="stat-badge">🔧</span>
                </div>
                <div class="stat-value" id="actCount">0</div>
                <div class="stat-footer" id="actProgress">0% of total</div>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="board-container">
            <div class="board-header">
                <div class="board-title-section">
                    <div>
                        <div class="board-title">PDCA Board</div>
                        <div class="board-subtitle" id="resultInfo">Showing all items</div>
                    </div>
                </div>
                <div class="board-info">
                    <span class="info-badge" id="totalItems">0 items</span>
                    <span class="info-badge" id="filteredItems">0 filtered</span>
                </div>
            </div>
            <div class="kanban-board" id="kanbanBoard">
                <div class="kanban-column column-plan" data-stage="plan" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon">🎯</span>
                            Plan
                        </span>
                        <span class="column-count" id="planBadge">0</span>
                    </div>
                    <div class="kanban-items" id="planItems"></div>
                </div>
                <div class="kanban-column column-do" data-stage="do" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon">⚡</span>
                            Do
                        </span>
                        <span class="column-count" id="doBadge">0</span>
                    </div>
                    <div class="kanban-items" id="doItems"></div>
                </div>
                <div class="kanban-column column-check" data-stage="check" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon">✅</span>
                            Check
                        </span>
                        <span class="column-count" id="checkBadge">0</span>
                    </div>
                    <div class="kanban-items" id="checkItems"></div>
                </div>
                <div class="kanban-column column-act" data-stage="act" 
                     ondragover="handleDragOver(event)" 
                     ondragleave="handleDragLeave(event)" 
                     ondrop="handleDrop(event)">
                    <div class="column-header">
                        <span class="column-title">
                            <span class="column-icon">🔧</span>
                            Act
                        </span>
                        <span class="column-count" id="actBadge">0</span>
                    </div>
                    <div class="kanban-items" id="actItems"></div>
                </div>
            </div>
        </div>

        <!-- Todo Section -->
        <div class="board-container">
            <div class="board-header">
                <div>
                    <div class="board-title">Quick Tasks</div>
                    <div class="board-subtitle" id="todoStats">0 tasks</div>
                </div>
                <button class="btn btn-sm" onclick="clearCompletedTodos()">Clear Completed</button>
            </div>
            <ul class="todo-list" id="todoList" style="list-style: none; display: flex; flex-direction: column; gap: 8px;"></ul>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal" id="todoModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">New Task</h3>
                <button class="modal-close" onclick="closeModal('todo')">×</button>
            </div>
            <form id="todoForm">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-input" id="todoTitle" required placeholder="Task title">
                </div>
                <div class="form-group">
                    <label class="form-label">Priority</label>
                    <select class="form-select" id="todoPriority">
                        <option value="high">High</option>
                        <option value="medium" selected>Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal('todo')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="pdcaModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">New PDCA Item</h3>
                <button class="modal-close" onclick="closeModal('pdca')">×</button>
            </div>
            <form id="pdcaForm">
                <div class="form-group">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-input" id="pdcaTitle" required placeholder="Item title">
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-textarea" id="pdcaDescription" rows="3" placeholder="Add description..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Stage</label>
                    <select class="form-select" id="pdcaStage">
                        <option value="plan">Plan</option>
                        <option value="do">Do</option>
                        <option value="check">Check</option>
                        <option value="act">Act</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal('pdca')">Cancel</button>
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // State Management
        let state = {
            todos: [],
            pdcaItems: [],
            searchQuery: '',
            stageFilter: 'all',
            dateFrom: null,
            dateTo: null
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadState();
            setupEventListeners();
            render();
        });

        function setupEventListeners() {
            document.getElementById('todoForm').addEventListener('submit', (e) => {
                e.preventDefault();
                addTodo();
            });

            document.getElementById('pdcaForm').addEventListener('submit', (e) => {
                e.preventDefault();
                addPdcaItem();
            });

            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) closeModal(modal.id.replace('Modal', ''));
                });
            });
        }

        function loadState() {
            const saved = localStorage.getItem('flow_state');
            if (saved) {
                state = JSON.parse(saved);
            } else {
                // Generate sample data
                state.todos = generateSampleTodos();
                state.pdcaItems = generateSamplePdcaItems();
                saveState();
            }
        }

        function generateSampleTodos() {
            const priorities = ['high', 'medium', 'low'];
            const titles = [
                'Review project specs', 'Update documentation', 'Schedule meeting',
                'Prepare presentation', 'Send report', 'Call client',
                'Update dashboard', 'Fix bugs', 'Write tests',
                'Deploy feature', 'Review PR', 'Plan sprint'
            ];
            
            return titles.map((title, index) => ({
                id: index + 1,
                title,
                priority: priorities[index % 3],
                completed: index % 4 === 0,
                createdAt: new Date(Date.now() - index * 86400000).toISOString()
            }));
        }

        function generateSamplePdcaItems() {
            const stages = ['plan', 'do', 'check', 'act'];
            const titles = [
                'Define Q3 goals', 'Implement new feature', 'Review metrics',
                'Optimize process', 'Create roadmap', 'Develop API',
                'Test application', 'Deploy to production', 'Monitor performance',
                'Gather feedback', 'Analyze results', 'Adjust strategy',
                'Plan marketing', 'Execute campaign', 'Review analytics',
                'Improve UX', 'Set KPIs', 'Build prototype',
                'Run tests', 'Document process', 'Train team',
                'Evaluate outcomes', 'Scale solution', 'Optimize costs'
            ];
            
            return titles.map((title, index) => ({
                id: index + 1,
                title,
                description: `Description for ${title}`,
                stage: stages[index % 4],
                createdAt: new Date(Date.now() - index * 43200000).toISOString()
            }));
        }

        function saveState() {
            localStorage.setItem('flow_state', JSON.stringify(state));
        }

        // Search and Filter
        function handleSearch(query) {
            state.searchQuery = query.toLowerCase();
            render();
        }

        function handleFilter() {
            state.stageFilter = document.getElementById('stageFilter').value;
            state.dateFrom = document.getElementById('dateFrom').value;
            state.dateTo = document.getElementById('dateTo').value;
            render();
        }

        function clearFilters() {
            state.searchQuery = '';
            state.stageFilter = 'all';
            state.dateFrom = null;
            state.dateTo = null;
            document.getElementById('searchInput').value = '';
            document.getElementById('stageFilter').value = 'all';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            render();
        }

        function getFilteredItems() {
            return state.pdcaItems.filter(item => {
                // Search filter
                if (state.searchQuery && !item.title.toLowerCase().includes(state.searchQuery) && 
                    !item.description.toLowerCase().includes(state.searchQuery)) {
                    return false;
                }
                
                // Stage filter
                if (state.stageFilter !== 'all' && item.stage !== state.stageFilter) {
                    return false;
                }
                
                // Date filter
                const itemDate = new Date(item.createdAt);
                if (state.dateFrom) {
                    const fromDate = new Date(state.dateFrom);
                    if (itemDate < fromDate) return false;
                }
                if (state.dateTo) {
                    const toDate = new Date(state.dateTo);
                    toDate.setHours(23, 59, 59);
                    if (itemDate > toDate) return false;
                }
                
                return true;
            });
        }

        // Drag and Drop
        function handleDragStart(e, itemId) {
            e.target.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', itemId);
        }

        function handleDragOver(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            const column = e.target.closest('.kanban-column');
            if (column) {
                column.classList.add('drag-over');
            }
        }

        function handleDragLeave(e) {
            const column = e.target.closest('.kanban-column');
            if (column) {
                column.classList.remove('drag-over');
            }
        }

        function handleDrop(e) {
            e.preventDefault();
            const column = e.target.closest('.kanban-column');
            if (!column) return;

            const itemId = parseInt(e.dataTransfer.getData('text/plain'));
            const newStage = column.dataset.stage;
            
            const item = state.pdcaItems.find(i => i.id === itemId);
            if (item && item.stage !== newStage) {
                item.stage = newStage;
                item.updatedAt = new Date().toISOString();
                saveState();
                render();
                showToast(`Moved to ${newStage}`, getStageIcon(newStage));
            }

            document.querySelectorAll('.kanban-column').forEach(col => {
                col.classList.remove('drag-over');
            });
            document.querySelectorAll('.kanban-item').forEach(item => {
                item.classList.remove('dragging');
            });
        }

        function handleDragEnd(e) {
            e.target.classList.remove('dragging');
            document.querySelectorAll('.kanban-column').forEach(col => {
                col.classList.remove('drag-over');
            });
        }

        // Actions
        function addTodo() {
            const title = document.getElementById('todoTitle').value.trim();
            const priority = document.getElementById('todoPriority').value;

            if (!title) return;

            state.todos.unshift({
                id: Date.now(),
                title,
                priority,
                completed: false,
                createdAt: new Date().toISOString()
            });

            saveState();
            render();
            closeModal('todo');
            document.getElementById('todoForm').reset();
            showToast('Task added', '✓');
        }

        function toggleTodo(id) {
            const todo = state.todos.find(t => t.id === id);
            if (todo) {
                todo.completed = !todo.completed;
                saveState();
                render();
            }
        }

        function deleteTodo(id) {
            state.todos = state.todos.filter(t => t.id !== id);
            saveState();
            render();
        }

        function clearCompletedTodos() {
            state.todos = state.todos.filter(t => !t.completed);
            saveState();
            render();
            showToast('Cleared completed tasks', '🧹');
        }

        function addPdcaItem() {
            const title = document.getElementById('pdcaTitle').value.trim();
            const stage = document.getElementById('pdcaStage').value;
            const description = document.getElementById('pdcaDescription').value.trim();

            if (!title) return;

            state.pdcaItems.unshift({
                id: Date.now(),
                title,
                stage,
                description,
                createdAt: new Date().toISOString(),
                updatedAt: new Date().toISOString()
            });

            saveState();
            render();
            closeModal('pdca');
            document.getElementById('pdcaForm').reset();
            showToast('PDCA item added', '🔄');
        }

        function deletePdcaItem(id) {
            state.pdcaItems = state.pdcaItems.filter(i => i.id !== id);
            saveState();
            render();
        }

        // Render
        function render() {
            const filteredItems = getFilteredItems();
            renderStats(filteredItems);
            renderKanban(filteredItems);
            renderTodos();
            updateInfoBar(filteredItems);
        }

        function renderStats(filteredItems) {
            const stages = ['plan', 'do', 'check', 'act'];
            const total = filteredItems.length || 1;

            stages.forEach(stage => {
                const count = filteredItems.filter(i => i.stage === stage).length;
                const percentage = Math.round((count / total) * 100);
                
                document.getElementById(`${stage}Count`).textContent = count;
                document.getElementById(`${stage}Badge`).textContent = count;
                document.getElementById(`${stage}Progress`).textContent = `${percentage}% of total`;
            });
        }

        function renderKanban(filteredItems) {
            const stages = ['plan', 'do', 'check', 'act'];
            
            stages.forEach(stage => {
                const container = document.getElementById(`${stage}Items`);
                const items = filteredItems.filter(i => i.stage === stage);

                if (items.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <div class="empty-text">No items</div>
                            <div class="empty-subtext">Drop here or create new</div>
                        </div>
                    `;
                    return;
                }

                container.innerHTML = items.map(item => `
                    <div class="kanban-item" 
                         draggable="true"
                         ondragstart="handleDragStart(event, ${item.id})"
                         ondragend="handleDragEnd(event)">
                        <div class="kanban-item-title">${item.title}</div>
                        ${item.description ? `<div class="kanban-item-desc">${item.description}</div>` : ''}
                        <div class="kanban-item-meta">
                            <span class="kanban-item-date">${formatDate(item.createdAt)}</span>
                            <div style="display: flex; gap: 4px; align-items: center;">
                                <span class="kanban-item-tag tag-${stage}">${stage}</span>
                                <button class="kanban-item-delete" onclick="deletePdcaItem(${item.id})">×</button>
                            </div>
                        </div>
                    </div>
                `).join('');
            });
        }

        function renderTodos() {
            const list = document.getElementById('todoList');
            const activeTodos = state.todos.filter(t => !t.completed);
            const completedTodos = state.todos.filter(t => t.completed);
            
            document.getElementById('todoStats').textContent = `${activeTodos.length} active, ${completedTodos.length} completed`;

            if (state.todos.length === 0) {
                list.innerHTML = '<div class="empty-state"><div class="empty-text">No tasks</div></div>';
                return;
            }

            list.innerHTML = state.todos.map(todo => `
                <li style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg); border-radius: var(--radius-sm);">
                    <input type="checkbox" 
                           style="width: 18px; height: 18px; cursor: pointer;"
                           ${todo.completed ? 'checked' : ''}
                           onchange="toggleTodo(${todo.id})">
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 500; ${todo.completed ? 'text-decoration: line-through; color: var(--text-muted);' : ''}">
                            ${todo.title}
                        </div>
                        <div style="font-size: 11px; color: var(--text-muted);">${formatDate(todo.createdAt)}</div>
                    </div>
                    <span style="font-size: 11px; padding: 2px 8px; border-radius: 8px; background: ${getPriorityColor(todo.priority)};">
                        ${todo.priority}
                    </span>
                    <button onclick="deleteTodo(${todo.id})" 
                            style="background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px;">
                        ×
                    </button>
                </li>
            `).join('');
        }

        function updateInfoBar(filteredItems) {
            const total = state.pdcaItems.length;
            const filtered = filteredItems.length;
            document.getElementById('totalItems').textContent = `${total} total items`;
            document.getElementById('filteredItems').textContent = `${filtered} showing`;
            document.getElementById('resultInfo').textContent = 
                filtered === total ? 'Showing all items' : `Showing ${filtered} of ${total} items`;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = now - date;
            const minutes = Math.floor(diff / 60000);
            const hours = Math.floor(diff / 3600000);
            const days = Math.floor(diff / 86400000);

            if (minutes < 1) return 'just now';
            if (minutes < 60) return `${minutes}m ago`;
            if (hours < 24) return `${hours}h ago`;
            if (days < 7) return `${days}d ago`;
            return date.toLocaleDateString();
        }

        function getPriorityColor(priority) {
            const colors = {
                high: 'var(--danger-light)',
                medium: 'var(--warning-light)',
                low: 'var(--success-light)'
            };
            return colors[priority] || 'var(--bg)';
        }

        function getStageIcon(stage) {
            const icons = {
                plan: '🎯',
                do: '⚡',
                check: '✅',
                act: '🔧'
            };
            return icons[stage] || '📋';
        }

        // UI Helpers
        function openModal(type) {
            document.getElementById(`${type}Modal`).classList.add('active');
        }

        function closeModal(type) {
            document.getElementById(`${type}Modal`).classList.remove('active');
        }

        function showToast(message, icon = '✓') {
            const toast = document.createElement('div');
            toast.className = 'toast';
            toast.innerHTML = `${icon} ${message}`;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 2000);
        }

        function exportData() {
            const data = {
                ...state,
                exportDate: new Date().toISOString()
            };
            
            const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `flow-data-${new Date().toISOString().split('T')[0]}.json`;
            a.click();
            URL.revokeObjectURL(url);
            
            showToast('Data exported', '📊');
        }
    </script>
</body>
</html>