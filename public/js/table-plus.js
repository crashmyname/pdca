class TablePlus {
  constructor(config) {
    this.url = config.url;
    this.columns = config.columns;
    this.perPage = config.perPage || 10;
    this.perPageOptions = config.perPageOptions || [10, 25, 50, 100, 1000000];
    this.page = 1;
    this.total = 0;
    this.lastPage = 1;
    this.data = [];
    this.allData = [];
    this.container = null;
    this.searchTerm = '';
    this.sortKey = null;
    this.sortOrder = 'asc';
    this.visibleColumns = Object.keys(this.columns);
    this.debounceTimer = null;
    this.isLoading = false;
    this.abortController = null;
    
    this.selectedRows = new Set();
    this.columnFilters = {};
    this.savePreferences = config.savePreferences !== false;
    this.theme = config.theme || 'tailwind'; 
    this.storageKey = config.storageKey || `tableplus_${location.pathname}_${config.url}`;
    this.onRowSelect = config.onRowSelect || null;
    this.customActions = config.customActions || [];
    this.rowIdentifier = config.rowIdentifier || 'id';
    this.customFilters = config.customFilters || {};

    this.virtual = {
        enabled: false,
        rowHeight: 40,
        buffer: 5,
        scrollContainer: null,
        spacer: null
    };
    
    if (this.savePreferences) {
      this.loadPreferences();
    }
  }

  getClass(type) {
    const themes = {
      tailwind: {
        button: 'px-3 py-1.5 rounded-md text-sm',
        buttonPrimary: 'bg-blue-600 text-white hover:bg-blue-700',
        buttonSecondary: 'bg-gray-200 text-gray-700 hover:bg-gray-300',
        input: 'border p-2 rounded-md text-sm focus:ring focus:ring-blue-200',
        select: 'border p-2 rounded-md text-sm',
        table: 'w-full border-collapse',
        th: 'border px-3 py-2 bg-gray-100 text-sm',
        td: 'border px-3 py-2 text-sm',
        dropdown: 'bg-white border rounded shadow-md',
      },

      bootstrap: {
        button: 'btn btn-sm',
        buttonPrimary: 'btn btn-primary',
        buttonSecondary: 'btn btn-secondary',
        input: 'form-control form-control-sm',
        select: 'form-select form-select-sm',
        table: 'table table-bordered table-striped',
        th: '',
        td: '',
        dropdown: 'dropdown-menu show',
      }
    };

    return themes[this.theme][type] || '';
  }

  // ========== PREFERENCES ==========
  savePreferencesToStorage() {
    if (!this.savePreferences) return;
    const prefs = {
      visibleColumns: this.visibleColumns,
      perPage: this.perPage,
      sortKey: this.sortKey,
      sortOrder: this.sortOrder
    };
    localStorage.setItem(this.storageKey, JSON.stringify(prefs));
  }

  loadPreferences() {
    try {
      const saved = localStorage.getItem(this.storageKey);
      if (saved) {
        const prefs = JSON.parse(saved);
        this.visibleColumns = prefs.visibleColumns || this.visibleColumns;
        this.perPage = prefs.perPage || this.perPage;
        this.sortKey = prefs.sortKey || null;
        this.sortOrder = prefs.sortOrder || 'asc';
      }
    } catch (e) {
      console.warn('Failed to load preferences:', e);
    }
  }

  // ========== ROW SELECTION ==========
  toggleRowSelection(rowId) {
    if (this.selectedRows.has(rowId)) {
      this.selectedRows.delete(rowId);
    } else {
      this.selectedRows.add(rowId);
    }
    this.updateSelectionUI();
    if (this.onRowSelect) {
      this.onRowSelect(Array.from(this.selectedRows));
    }
  }

  selectAllRows() {
    this.data.forEach(item => {
      this.selectedRows.add(item[this.rowIdentifier]);
    });
    this.updateSelectionUI();
  }

  deselectAllRows() {
    this.selectedRows.clear();
    this.updateSelectionUI();
  }

  updateSelectionUI() {
    const checkboxes = this.container.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => {
      const rowId = cb.dataset.rowId;
      // bandingkan dengan normalisasi tipe data
      const isChecked = Array.from(this.selectedRows).some(id => String(id) === String(rowId));
      cb.checked = isChecked;
    });

    const selectAllCb = this.container.querySelector('.select-all-checkbox');
    if (selectAllCb) {
      const allSelected = this.data.length > 0 &&
        this.data.every(item =>
          Array.from(this.selectedRows).some(id => String(id) === String(item[this.rowIdentifier]))
        );
      selectAllCb.checked = allSelected;
    }

    this.renderBulkActionsBar();
  }

  renderBulkActionsBar() {
    const existing = this.container.parentNode.querySelector('.bulk-actions-bar');
    if (existing) existing.remove();

    if (this.selectedRows.size === 0) return;

    const bar = document.createElement('div');
    bar.className = 'bulk-actions-bar bg-blue-50 border border-blue-200 rounded-md p-3 mb-3 flex items-center justify-between';
    
    const info = document.createElement('span');
    info.className = 'text-sm text-blue-800 font-medium';
    info.textContent = `${this.selectedRows.size} baris dipilih`;
    
    const actions = document.createElement('div');
    actions.className = 'flex gap-2';
    
    const exportBtn = document.createElement('button');
    exportBtn.innerHTML = '<i class="fas fa-download"></i> Export Selected';
    exportBtn.className = 'bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700';
    exportBtn.onclick = () => this.exportSelected();
    
    const clearBtn = document.createElement('button');
    clearBtn.textContent = '✕ Clear Selection';
    clearBtn.className = 'bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-300';
    clearBtn.onclick = () => this.deselectAllRows();
    
    actions.append(exportBtn, clearBtn);
    
    this.customActions.forEach(action => {
      const btn = document.createElement('button');
      btn.textContent = action.label;
      btn.className = action.className || 'bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700';
      btn.onclick = () => action.onClick(Array.from(this.selectedRows));
      actions.appendChild(btn);
    });
    
    bar.append(info, actions);
    this.container.parentNode.insertBefore(bar, this.container);
  }

  exportSelected() {
    const selectedData = this.data.filter(item => 
      this.selectedRows.has(item[this.rowIdentifier])
    );

    // Header
    let csv = this.visibleColumns.map(k => this.getColumnLabel(k)).join(',') + '\n';

    // Body
    selectedData.forEach(item => {
      const row = this.visibleColumns.map(k => {
        const val = String(this.getColumnText(k, item) ?? '').replace(/"/g, '""');
        return `"${val}"`;
      }).join(',');
      csv += row + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `selected-export-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  // ========== FETCH DATA ==========
  async fetchData() {
    if (this.abortController) {
      this.abortController.abort();
    }
    this.abortController = new AbortController();

    const params = new URLSearchParams({
      page: this.page,
      per_page: this.perPage,
      search: this.searchTerm,
    });

    // Kirim filter dalam bentuk JSON agar backend bisa parse langsung
    if (Object.keys(this.columnFilters).length > 0) {
      params.append('filters', JSON.stringify(this.columnFilters));
    }

    if (this.sortKey) {
      params.append('sort_by', this.sortKey);
      params.append('sort_order', this.sortOrder);
    }

    Object.keys(this.customFilters).forEach(key => {
      if (this.customFilters[key] !== null && this.customFilters[key] !== '') {
        params.append(key, this.customFilters[key]);
      }
    });

    this.isLoading = true;
    this.showLoadingState();

    try {
      const response = await fetch(`${this.url}?${params.toString()}`, {
          signal: this.abortController.signal,
          cache: 'no-store',
          headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
          }
      });

      if (!response.ok) throw new Error(`HTTP ${response.status}`);

      const json = await response.json();

      if (json.status === 200 && json.data?.data) {
        this.data = json.data.data;
        this.total = json.data.pagination.total;
        this.lastPage = json.data.pagination.last_page;

        // simpan semua data saat pertama kali fetch (untuk dropdown filter)
        if (!Array.isArray(this.allData) || this.allData.length === 0) {
          this.allData = [...this.data];
        }
      } else {
        this.data = [];
        this.total = 0;
        this.lastPage = 1;
      }
    } catch (e) {
      if (e.name === 'AbortError') {
        console.log('Request cancelled');
        return;
      }
      console.error('Fetch error:', e);
      this.data = [];
      this.total = 0;
      this.lastPage = 1;
      this.showError('Gagal memuat data. Silakan coba lagi.');
    } finally {
      this.isLoading = false;
      this.hideLoadingState();
      this.abortController = null;
    }
  }

  sortData(data) {
    if (!this.sortKey) return data;
    return data.sort((a, b) => {
      const valA = a[this.sortKey] ?? '';
      const valB = b[this.sortKey] ?? '';
      if (valA < valB) return this.sortOrder === 'asc' ? -1 : 1;
      if (valA > valB) return this.sortOrder === 'asc' ? 1 : -1;
      return 0;
    });
  }

  // ========== UI STATES ==========
  showLoadingState() {
    const tbody = this.container?.querySelector('tbody');
    if (tbody) {
      const skeletonRows = Array(5).fill(0).map(() => {
        const tr = document.createElement('tr');
        this.visibleColumns.forEach(() => {
          const td = document.createElement('td');
          td.className = 'border px-3 py-3';
          td.innerHTML = '<div class="h-4 bg-gray-200 rounded animate-pulse"></div>';
          tr.appendChild(td);
        });
        return tr.outerHTML;
      }).join('');
      
      tbody.innerHTML = skeletonRows;
    }
  }

  hideLoadingState() {
    // Handled by renderTable()
  }

  showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-3';
    errorDiv.innerHTML = `
      <strong>Error:</strong> ${message}
      <button onclick="this.parentElement.remove()" class="float-right text-red-900 font-bold">✕</button>
    `;
    this.container.parentNode.insertBefore(errorDiv, this.container);
    setTimeout(() => errorDiv.remove(), 5000);
  }

  // ========== RENDER CONTROLS ==========
  renderControls() {
    const wrapper = document.createElement('div');
    wrapper.className = 'table-controls flex flex-wrap justify-between items-center mb-3 gap-2';

    const leftDiv = document.createElement('div');
    leftDiv.className = 'flex items-center gap-2 flex-wrap';

    // Search input
    const searchInput = document.createElement('input');
    searchInput.placeholder = 'Search...';
    searchInput.value = this.searchTerm;
    searchInput.className = 'border p-2 rounded-md text-sm focus:ring focus:ring-blue-200 w-64';
    searchInput.oninput = e => {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => {
        this.searchTerm = e.target.value;
        this.page = 1;
        this.update();
      }, 500);
    };

    // Per Page Selector
    const perPageLabel = document.createElement('span');
    perPageLabel.textContent = 'Tampilkan:';
    perPageLabel.className = 'text-sm text-gray-600';
    
    const perPageSelect = document.createElement('select');
    perPageSelect.className = 'border p-2 rounded-md text-sm focus:ring focus:ring-blue-200';
    (this.perPageOptions || [10, 25, 50, 100, 1000000]).forEach(num => {
      const opt = document.createElement('option');
      opt.value = num;
      opt.textContent = num;
      opt.selected = this.perPage === num;
      perPageSelect.appendChild(opt);
    });
    perPageSelect.onchange = e => {
      this.perPage = parseInt(e.target.value);
      this.page = 1;
      this.savePreferencesToStorage();
      this.update();
    };

    // NEW: Refresh button
    const refreshBtn = document.createElement('button');
    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
    refreshBtn.title = 'Refresh data';
    refreshBtn.className = 'border p-2 rounded-md text-sm hover:bg-gray-100';
    refreshBtn.onclick = () => this.update();

    leftDiv.append(searchInput, perPageLabel, perPageSelect, refreshBtn);

    // Right side buttons
    const rightDiv = document.createElement('div');
    rightDiv.className = 'flex items-center gap-2';

    // Export Dropdown
    const exportWrapper = this.createExportDropdown();
    
    // Column visibility
    const filterWrapper = this.createColumnVisibilityDropdown();

    // NEW: Reset preferences
    const resetBtn = document.createElement('button');
    resetBtn.textContent = '↺ Reset';
    resetBtn.title = 'Reset ke default';
    resetBtn.className = 'bg-yellow-400 text-white px-3 py-1.5 rounded-md text-sm hover:bg-yellow-500';
    resetBtn.onclick = () => {
      if (confirm('Reset semua preferensi ke default?')) {
        localStorage.removeItem(this.storageKey);
        location.reload();
      }
    };

    rightDiv.append(exportWrapper, filterWrapper, resetBtn);
    wrapper.append(leftDiv, rightDiv);

    this.container.parentNode.querySelector('.table-controls')?.remove();
    this.container.parentNode.insertBefore(wrapper, this.container);
  }

  createExportDropdown() {
    const exportBtn = document.createElement('button');
    exportBtn.innerHTML = '<i class="fas fa-download"></i> Export';
    exportBtn.className = 'bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-700 relative transition-colors';
    
    const exportMenu = document.createElement('div');
    exportMenu.className = 'absolute right-0 mt-1 bg-white shadow-lg rounded-md hidden z-50 min-w-[120px]';
    
    ['Copy', 'CSV', 'Excel', 'PDF'].forEach(type => {
      const opt = document.createElement('div');
      opt.textContent = type;
      opt.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm';
      opt.onclick = () => {
        if (type === 'Copy') this.exportCopy();
        if (type === 'CSV') this.exportCSV();
        if (type === 'Excel') this.exportXLSX();
        if (type === 'PDF') this.exportPDF();
        exportMenu.classList.add('hidden');
      };
      exportMenu.appendChild(opt);
    });
    
    exportBtn.onclick = (e) => {
      e.stopPropagation();
      exportMenu.classList.toggle('hidden');
    };
    
    document.addEventListener('click', () => {
      exportMenu.classList.add('hidden');
    });
    
    const wrapper = document.createElement('div');
    wrapper.className = 'relative';
    wrapper.append(exportBtn, exportMenu);
    return wrapper;
  }

  createColumnVisibilityDropdown() {
    const filterBtn = document.createElement('button');
    filterBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Column';
    filterBtn.className = 'bg-indigo-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-indigo-700 relative transition-colors';
    
    const filterMenu = document.createElement('div');
    filterMenu.className = 'absolute right-0 mt-1 bg-white shadow-lg rounded-md hidden z-50 p-2 min-w-[180px] max-h-64 overflow-y-auto';
    
    Object.keys(this.columns).forEach(key => {
      const wrapper = document.createElement('label');
      wrapper.className = 'flex items-center gap-2 text-sm px-2 py-1 hover:bg-gray-100 rounded cursor-pointer';
      
      const cb = document.createElement('input');
      cb.type = 'checkbox';
      cb.checked = this.visibleColumns.includes(key);
      cb.onchange = () => {
        if (cb.checked) {
          this.visibleColumns.push(key);
        } else {
          this.visibleColumns = this.visibleColumns.filter(c => c !== key);
        }
        this.savePreferencesToStorage();
        this.renderTable();
      };
      
      const span = document.createElement('span');
      span.textContent = this.getColumnLabel(key); // pakai helper

      wrapper.append(cb, span);
      filterMenu.append(wrapper);
    });
    
    filterBtn.onclick = (e) => {
      e.stopPropagation();
      filterMenu.classList.toggle('hidden');
    };
    
    document.addEventListener('click', () => {
      filterMenu.classList.add('hidden');
    });
    
    const wrapper = document.createElement('div');
    wrapper.className = 'relative';
    wrapper.append(filterBtn, filterMenu);
    return wrapper;
  }

  createColumnFilterDropdown(columnKey) {
    const wrapper = document.createElement('div');
    wrapper.className = 'relative inline-block text-left';

    const btn = document.createElement('button');
    btn.textContent = '⋮';
    btn.className = 'text-gray-500 hover:text-gray-700 ml-1 text-xs';
    wrapper.appendChild(btn);

    const menu = document.createElement('div');
    menu.className =
      'absolute right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-lg z-50 w-48 hidden p-2';
    menu.style.minWidth = '150px';
    menu.addEventListener('click', (e) => e.stopPropagation());

    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Cari...';
    searchInput.className =
      'w-full mb-2 border px-2 py-1 rounded text-sm focus:ring focus:ring-blue-200';
    menu.appendChild(searchInput);

    const list = document.createElement('div');
    list.className = 'max-h-40 overflow-y-auto';
    menu.appendChild(list);

    // ================= STATE =================
    let page = 1;
    let hasMore = true;
    let loading = false;
    let searchTerm = '';
    let uniqueVals = [];

    // ================= APPEND (FIX ERROR UTAMA) =================
    const appendOptions = (values) => {
      values.forEach((val) => {
        // hindari duplikat
        if (uniqueVals.includes(val)) return;
        uniqueVals.push(val);

        const lbl = document.createElement('label');
        lbl.className =
          'flex items-center gap-2 px-2 py-1 text-sm hover:bg-gray-100 rounded cursor-pointer';

        const cb = document.createElement('input');
        cb.type = 'checkbox';
        cb.checked = (this.columnFilters[columnKey] || []).includes(val);

        cb.onchange = () => {
          if (!this.columnFilters[columnKey]) this.columnFilters[columnKey] = [];

          if (cb.checked) {
            this.columnFilters[columnKey].push(val);
          } else {
            this.columnFilters[columnKey] =
              this.columnFilters[columnKey].filter((v) => v !== val);
          }

          this.page = 1;
          this.update();
        };

        const span = document.createElement('span');
        span.textContent = val || '(kosong)';

        lbl.append(cb, span);
        list.appendChild(lbl);
      });
    };

    // ================= FETCH =================
    const loadDistinctValues = async (reset = false) => {
      if (loading || !hasMore) return;

      if (reset) {
        page = 1;
        hasMore = true;
        uniqueVals = [];
        list.innerHTML = '';
      }

      loading = true;

      try {
        const response = await fetch(
          `${this.url}?distinct=${columnKey}&page=${page}&limit=25&search=${encodeURIComponent(searchTerm)}`,
          {
            cache: 'no-store',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          }
        );

        const json = await response.json();

        if (json.status === 200 && Array.isArray(json.data)) {
          const values = json.data;

          if (values.length < 25) {
            hasMore = false;
          }

          appendOptions(values);
          page++;
        } else {
          hasMore = false;
        }
      } catch (err) {
        console.error('Gagal memuat distinct:', err);
      }

      loading = false;
    };

    // ================= INFINITE SCROLL =================
    list.addEventListener('scroll', () => {
      if (list.scrollTop + list.clientHeight >= list.scrollHeight - 5) {
        loadDistinctValues();
      }
    });

    // ================= OPEN / CLOSE =================
    btn.onclick = async (e) => {
      e.stopPropagation();

      const isOpen = !menu.classList.contains('hidden');
      if (isOpen) {
        menu.classList.add('hidden');
        if (document.body.contains(menu)) document.body.removeChild(menu);
        return;
      }

      await loadDistinctValues(true);
      searchInput.value = '';

      const rect = btn.getBoundingClientRect();
      menu.style.position = 'absolute';
      menu.style.top = `${rect.bottom + window.scrollY}px`;
      menu.style.left = `${rect.right - 200 + window.scrollX}px`;
      menu.style.zIndex = 9999;

      menu.classList.remove('hidden');
      document.body.appendChild(menu);

      const closeHandler = (ev) => {
        if (!menu.contains(ev.target) && ev.target !== btn) {
          menu.classList.add('hidden');
          if (document.body.contains(menu)) document.body.removeChild(menu);
          document.removeEventListener('click', closeHandler);
        }
      };

      setTimeout(() => document.addEventListener('click', closeHandler), 50);
    };

    // ================= SEARCH =================
    searchInput.oninput = (e) => {
      searchTerm = e.target.value;
      loadDistinctValues(true);
    };

    return wrapper;
  }

  // ========== RENDER TABLE ==========
  renderTable() {
    const tbody = this.container.querySelector('tbody');
    const thead = this.container.querySelector('thead');
    tbody.classList.remove('block');
    thead.classList.remove('hidden');
    tbody.innerHTML = '';
    thead.innerHTML = '';

    const isMobile = window.innerWidth < 768;
    const data = this.sortData(this.data);

    if (data.length === 0 && !this.isLoading) {
      const colCount = this.visibleColumns.length + 1;
      tbody.innerHTML = `
        <tr>
          <td colspan="${colCount}" class="text-center p-8 text-gray-500">
            <div class="flex flex-col items-center gap-2">
              <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
              </svg>
              <span>Tidak ada data</span>
            </div>
          </td>
        </tr>
      `;
      return;
    }

    const fragment = document.createDocumentFragment();

    if (!isMobile) {
      // --- TAMPILAN DESKTOP / TABLET (NORMAL TABLE) ---
      const trHead = document.createElement('tr');

      // Select all checkbox
      const selectAllTh = document.createElement('th');
      selectAllTh.className = 'border px-3 py-2 text-sm bg-gray-100 text-center';
      const selectAllCb = document.createElement('input');
      selectAllCb.type = 'checkbox';
      selectAllCb.className = 'select-all-checkbox cursor-pointer';
      selectAllCb.onchange = (e) => {
        e.target.checked ? this.selectAllRows() : this.deselectAllRows();
      };
      selectAllTh.appendChild(selectAllCb);
      trHead.appendChild(selectAllTh);

      // Column headers
      this.visibleColumns.forEach(k => {
        const th = document.createElement('th');
        th.className = 'border px-2 sm:px-3 py-2 text-xs sm:text-sm bg-gray-100 whitespace-nowrap';

        const content = document.createElement('div');
        content.className = 'flex items-center justify-between';

        const leftPart = document.createElement('div');
        leftPart.className = 'flex items-center gap-1';
        const label = document.createElement('span');
        label.textContent = this.getColumnLabel(k);
        leftPart.appendChild(label);

        leftPart.appendChild(this.createColumnFilterDropdown(k));

        const icon = document.createElement('span');
        icon.className = 'text-xs ml-2 cursor-pointer';
        icon.textContent =
          this.sortKey === k ? (this.sortOrder === 'asc' ? '↑' : '↓') : '↕';
        icon.onclick = (e) => {
          e.stopPropagation();
          if (this.sortKey === k) {
            this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
          } else {
            this.sortKey = k;
            this.sortOrder = 'asc';
          }
          this.savePreferencesToStorage();
          this.renderTable();
        };

        content.append(leftPart, icon);
        th.appendChild(content);
        trHead.appendChild(th);
      });

      thead.appendChild(trHead);

      // Body
      data.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gray-50 transition-colors';

        // Checkbox
        const checkboxTd = document.createElement('td');
        checkboxTd.className = 'border px-3 py-2 text-center';
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'row-checkbox cursor-pointer';
        checkbox.dataset.rowId = item[this.rowIdentifier];
        checkbox.checked = this.selectedRows.has(item[this.rowIdentifier]);
        checkbox.onchange = () => this.toggleRowSelection(item[this.rowIdentifier]);
        checkboxTd.appendChild(checkbox);
        tr.appendChild(checkboxTd);

        // Data columns
        this.visibleColumns.forEach(k => {
          const td = document.createElement('td');
          td.className = 'border px-2 sm:px-3 py-1.5 sm:py-2 text-xs sm:text-sm whitespace-nowrap';

          const content = this.getColumnContent(k, item);
          if (content instanceof HTMLElement) td.appendChild(content);
          else td.innerHTML = content;

          tr.appendChild(td);
        });

        fragment.appendChild(tr);
      });
    } else {
      // --- TAMPILAN MOBILE (CARD / STACKED) ---
        tbody.classList.add('block');
        thead.classList.add('hidden');

        data.forEach(item => {
          const cardWrapper = document.createElement('div');
          cardWrapper.className = 'card-mobile border rounded-xl p-4 bg-white shadow-sm';

          const topBar = document.createElement('div');
          topBar.className = 'flex items-center justify-between mb-2';
          const cb = document.createElement('input');
          cb.type = 'checkbox';
          cb.className = 'row-checkbox cursor-pointer';
          cb.dataset.rowId = item[this.rowIdentifier];
          cb.checked = this.selectedRows.has(item[this.rowIdentifier]);
          cb.onchange = () => this.toggleRowSelection(item[this.rowIdentifier]);

          const idText = document.createElement('span');
          idText.className = 'text-base font-semibold text-gray-800';

          let firstDataKey = this.visibleColumns.find(k => {
            const col = this.columns[k];
            return col && typeof col === 'object' && col.isTitle === true;
          });
          if (!firstDataKey) {
            idText.textContent = '';
          } else {
            const label = this.getColumnLabel(firstDataKey);
            const content = this.getColumnContent(firstDataKey, item);
            idText.textContent = content ? `${content}` : label || '';
          }

          topBar.append(cb, idText);
          cardWrapper.appendChild(topBar);

          this.visibleColumns.slice(1).forEach(k => {
              const row = document.createElement('div');
              row.className = 'flex justify-between text-sm py-1 border-b last:border-0';

              const label = document.createElement('span');
              label.className = 'font-medium text-gray-500';
              label.textContent = this.getColumnLabel(k);

              const value = document.createElement('span');
              value.className = 'text-gray-900 text-right';

              const content = this.getColumnContent(k, item);
              if (content instanceof HTMLElement) value.appendChild(content);
              else value.innerHTML = content;

              row.append(label, value);
              cardWrapper.appendChild(row);
          });

          const tr = document.createElement('tr');
          const td = document.createElement('td');
          td.colSpan = this.visibleColumns.length + 1;
          td.appendChild(cardWrapper);
          tr.appendChild(td);
          fragment.appendChild(tr);
        });
    }

    tbody.appendChild(fragment);
  }

  // ========== PAGINATION ==========
  renderPagination() {
    const paginationDiv = document.createElement('div');
    paginationDiv.className = 'pagination mt-4 flex flex-wrap justify-between items-center text-sm gap-2';

    const info = document.createElement('div');
    info.className = 'text-gray-600';
    const start = (this.page - 1) * this.perPage + 1;
    const end = Math.min(this.page * this.perPage, this.total);
    info.textContent = `Menampilkan ${start}-${end} dari ${this.total} data`;

    const buttonsDiv = document.createElement('div');
    buttonsDiv.className = 'flex items-center gap-1';

    const totalPages = this.lastPage || 1;
    const makeBtn = (text, page, active = false, disabled = false) => {
      const btn = document.createElement('button');
      btn.textContent = text;
      btn.disabled = disabled;
      btn.className = `px-3 py-1 border rounded transition-colors ${
        active ? 'bg-blue-600 text-white border-blue-600' : 'hover:bg-gray-200 bg-white'
      } ${disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'}`;
      btn.onclick = () => { 
        if (!disabled) {
          this.page = page; 
          this.update();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      };
      return btn;
    };

    buttonsDiv.appendChild(makeBtn('←', this.page - 1, false, this.page === 1));

    const range = 2;
    for (let i = 1; i <= totalPages; i++) {
      if (i === 1 || i === totalPages || (i >= this.page - range && i <= this.page + range)) {
        buttonsDiv.appendChild(makeBtn(i, i, this.page === i));
      } else if (
        (i === this.page - range - 1 && i > 1) ||
        (i === this.page + range + 1 && i < totalPages)
      ) {
        const span = document.createElement('span');
        span.textContent = '...';
        span.className = 'px-2 text-gray-400';
        buttonsDiv.appendChild(span);
      }
    }

    buttonsDiv.appendChild(makeBtn('→', this.page + 1, false, this.page === totalPages));

    paginationDiv.append(info, buttonsDiv);

    this.container.parentNode.querySelector('.pagination')?.remove();
    this.container.parentNode.appendChild(paginationDiv);
  }

  // Ambil label kolom untuk header / export
  getColumnLabel(key) {
    const col = this.columns[key];
    if (!col) return key;
    if (typeof col === 'string') return col;
    if (typeof col === 'object' && col.label) return col.label;
    return key;
  }

  // Ambil konten kolom untuk cell (boleh HTML)
  getColumnContent(key, row) {
    const col = this.columns[key];
    if (!col) return row[key] ?? '';
    if (typeof col === 'function') return col(row);
    if (typeof col === 'object' && col.render) return col.render(row);
    return row[key] ?? '';
  }

  // Ambil konten kolom untuk PDF (hanya text)
  getColumnText(key, row) {
    const col = this.columns[key];
    if (!col) return row[key] ?? '';

    if (typeof col === 'object' && col.exportText) {
      return col.exportText(row); // pakai exportText dulu
    }

    if (typeof col === 'function') return col(row); // fallback
    if (typeof col === 'object' && col.render) {
      const content = col.render(row);
      if (content instanceof HTMLElement) return content.textContent || '';
      return String(content ?? '');
    }

    return row[key] ?? '';
  }

  // ========== EXPORT METHODS ==========
  exportCSV() {
    let csv = this.visibleColumns.map(k => this.getColumnLabel(k)).join(',') + '\n';
    this.data.forEach(item => {
      const row = this.visibleColumns.map(k => `"${String(this.getColumnText(k, item) ?? '')}"`).join(',');
      csv += row + '\n';
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `table-export-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    URL.revokeObjectURL(a.href);
  }

  exportXLSX() {
    if (!window.XLSX) {
      const script = document.createElement('script');
      script.src = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';
      script.onload = () => this.exportXLSX();
      document.body.appendChild(script);
      return;
    }

    const wsData = [
      this.visibleColumns.map(k => this.getColumnLabel(k)),
      ...this.data.map(item => this.visibleColumns.map(k => this.getColumnText(k, item)))
    ];

    const ws = window.XLSX.utils.aoa_to_sheet(wsData);
    const wb = window.XLSX.utils.book_new();
    window.XLSX.utils.book_append_sheet(wb, ws, 'Data');
    window.XLSX.writeFile(wb, `table-export-${new Date().toISOString().split('T')[0]}.xlsx`);
  }

  exportPDF() {
    const loadJsPDF = async () => {
      if (!window.jspdf) {
        await new Promise(resolve => {
          const script = document.createElement('script');
          script.src = 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js';
          script.onload = resolve;
          document.body.appendChild(script);
        });
      }

      if (!window.jspdf?.autoTable) {
        await new Promise(resolve => {
          const script = document.createElement('script');
          script.src = 'https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.31/dist/jspdf.plugin.autotable.min.js';
          script.onload = resolve;
          document.body.appendChild(script);
        });
      }

      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('l', 'pt', 'a4');
      doc.text('Laporan Data', 40, 30);
      doc.setFontSize(10);
      doc.text(`Generated: ${new Date().toLocaleString('id-ID')}`, 40, 50);

      doc.autoTable({
        head: [this.visibleColumns.map(k => this.getColumnLabel(k))],
        body: this.data.map(item => this.visibleColumns.map(k => this.getColumnText(k, item))),
        styles: { fontSize: 9, cellPadding: 3 },
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        theme: 'grid',
        margin: { top: 60 },
      });

      doc.save(`table-export-${new Date().toISOString().split('T')[0]}.pdf`);
    };

    loadJsPDF();
  }

  exportCopy() {
    const text = this.data.map(item =>
      this.visibleColumns.map(k => this.getColumnText(k, item)).join('\t')
    ).join('\n');

    navigator.clipboard.writeText(text).then(() => {
      const msg = document.createElement('div');
      msg.innerHTML = '<i class="fas fa-check"></i> Data disalin ke clipboard!';
      msg.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-md shadow-lg z-50';
      document.body.appendChild(msg);
      setTimeout(() => msg.remove(), 2000);
    }).catch(err => {
      console.error('Copy failed:', err);
      alert('Gagal menyalin data');
    });
  }

  // ========== MAIN METHODS ==========
  async update() {
    await this.fetchData();
    this.renderTable();
    this.renderPagination();
    this.updateSelectionUI();
  }

  async render(selector) {
    this.container = document.querySelector(selector);
    if (!this.container) return console.error(`Element ${selector} not found`);
    
    if (!document.querySelector('link[href*="tailwind"]')) {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css';
      document.head.appendChild(link);
    }
    
    this.renderControls();
    await this.update();
    this.lastIsMobile = window.innerWidth < 768;

    if (!this.resizeListenerAttached) {
      this.resizeListenerAttached = true;
      window.addEventListener('resize', () => {
        const isMobile = window.innerWidth < 768;
        if (isMobile !== this.lastIsMobile) {
          this.lastIsMobile = isMobile;
          this.renderTable();
        }
      });
    }
  }

  destroy() {
    if (this.abortController) {
      this.abortController.abort();
    }
    clearTimeout(this.debounceTimer);
    this.container.parentNode.querySelector('.table-controls')?.remove();
    this.container.parentNode.querySelector('.pagination')?.remove();
    this.container.parentNode.querySelector('.bulk-actions-bar')?.remove();
    this.container.innerHTML = '';
    this.selectedRows.clear();
  }

  // ========== PUBLIC API ==========
  getSelectedRows() {
    return Array.from(this.selectedRows);
  }

  getSelectedData() {
    return this.data.filter(item => 
      this.selectedRows.has(item[this.rowIdentifier])
    );
  }

  refresh() {
    return this.update();
  }

  setPage(page) {
    this.page = page;
    return this.update();
  }

  setPerPage(perPage) {
    this.perPage = perPage;
    this.page = 1;
    this.savePreferencesToStorage();
    return this.update();
  }

  setSearch(term) {
    this.searchTerm = term;
    this.page = 1;
    return this.update();
  }

  setFilter(key, value) {
    this.customFilters[key] = value;
    this.page = 1;
    return this.update();
  }

  setFilters(filters) {
    this.columnFilters = { ...this.columnFilters, ...filters };
    this.page = 1;
    return this.update();
  }

  resetFilters() {
    this.customFilters = {};
    this.page = 1;
    return this.update();
  }

}