/**
 * TablePlus v2.0
 * Dynamic theme (tailwind | bootstrap), responsive scroll,
 * sticky pagination outside scroll container, bug fixes & performance.
 */
class TablePlus {
  constructor(config) {
    this.url            = config.url;
    this.columns        = config.columns;
    this.perPage        = config.perPage        || 10;
    this.perPageOptions = config.perPageOptions || [10, 25, 50, 100, 1000000];
    this.page           = 1;
    this.total          = 0;
    this.lastPage       = 1;
    this.data           = [];
    this.allData        = [];
    this.container      = null;       // <table>
    this.wrapper        = null;       // root wrapper div (parent of controls + scroll + pagination)
    this.searchTerm     = '';
    this.sortKey        = null;
    this.sortOrder      = 'asc';
    this.visibleColumns = Object.keys(this.columns);
    this.debounceTimer  = null;
    this.isLoading      = false;
    this.abortController = null;

    this.selectedRows   = new Set();
    this.columnFilters  = {};
    this.savePreferences = config.savePreferences !== false;
    this.theme          = config.theme || 'tailwind';
    this.storageKey     = config.storageKey || `tableplus_${location.pathname}_${config.url}`;
    this.onRowSelect    = config.onRowSelect   || null;
    this.customActions  = config.customActions || [];
    this.rowIdentifier  = config.rowIdentifier || 'id';
    this.customFilters  = config.customFilters || {};

    // Track bound listeners so we can remove them on destroy()
    this._boundDocClick   = null;
    this._resizeObserver  = null;
    this._lastIsMobile    = null;
    this._openMenus       = [];   // { menu, closeHandler } pairs for cleanup

    if (this.savePreferences) this.loadPreferences();
  }

  // ─────────────────────────────────────────────────────────────────
  //  THEME HELPER
  // ─────────────────────────────────────────────────────────────────
  /** Returns a class string for the given semantic slot, per active theme. */
  cls(slot) {
    const tw = {
      // layout
      wrapperRoot   : 'tableplus-root',
      controlsRow   : 'flex flex-wrap justify-between items-center mb-3 gap-2',
      controlsLeft  : 'flex items-center gap-2 flex-wrap',
      controlsRight : 'flex items-center gap-2',
      bulkBar       : 'bg-blue-50 border border-blue-200 rounded-md p-3 mb-3 flex items-center justify-between',
      bulkInfo      : 'text-sm text-blue-800 font-medium',
      bulkActions   : 'flex gap-2',
      scrollWrap    : 'w-full overflow-x-auto rounded-md border',  // ← horizontal scroll here
      tableEl       : 'w-full border-collapse text-sm',
      th            : 'border-b px-3 py-2 bg-gray-100 text-sm font-semibold whitespace-nowrap text-left',
      thCheckbox    : 'border-b px-3 py-2 bg-gray-100 text-center w-10',
      td            : 'border-b px-3 py-2 text-sm whitespace-nowrap',
      tdCheckbox    : 'border-b px-3 py-2 text-center w-10',
      trHover       : 'hover:bg-gray-50 transition-colors',
      emptyCell     : 'px-3 py-10 text-center text-gray-400',
      paginationWrap: 'flex flex-wrap justify-between items-center text-sm gap-2 mt-3 pt-3 border-t',
      paginationInfo: 'text-gray-500',
      paginationBtns: 'flex items-center gap-1',
      // controls
      inputSearch   : 'border p-2 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 w-48 sm:w-64',
      selectPerPage : 'border p-2 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-300',
      labelPerPage  : 'text-sm text-gray-600',
      btnRefresh    : 'border p-2 rounded-md text-sm hover:bg-gray-100 transition-colors',
      btnExport     : 'bg-blue-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-blue-700 transition-colors',
      btnColumn     : 'bg-indigo-600 text-white px-3 py-1.5 rounded-md text-sm hover:bg-indigo-700 transition-colors',
      btnReset      : 'bg-yellow-400 text-white px-3 py-1.5 rounded-md text-sm hover:bg-yellow-500 transition-colors',
      btnExportSel  : 'bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700',
      btnClearSel   : 'bg-gray-200 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-300',
      dropdown      : 'absolute right-0 mt-1 bg-white shadow-lg border border-gray-100 rounded-md z-50 min-w-[140px]',
      dropdownItem  : 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm',
      colVisMenu    : 'absolute right-0 mt-1 bg-white shadow-lg border border-gray-100 rounded-md z-50 p-2 min-w-[180px] max-h-64 overflow-y-auto',
      colVisLabel   : 'flex items-center gap-2 text-sm px-2 py-1 hover:bg-gray-100 rounded cursor-pointer',
      pageBtn       : 'px-3 py-1 border rounded transition-colors bg-white hover:bg-gray-100 cursor-pointer',
      pageBtnActive : 'px-3 py-1 border rounded bg-blue-600 text-white border-blue-600',
      pageBtnDis    : 'px-3 py-1 border rounded bg-white opacity-40 cursor-not-allowed',
      errorBox      : 'bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-3',
      skeleton      : 'h-4 bg-gray-200 rounded animate-pulse',
      // mobile card
      cardWrap      : 'border rounded-xl p-4 bg-white shadow-sm mb-3',
      cardTopBar    : 'flex items-center justify-between mb-2',
      cardTitle     : 'text-base font-semibold text-gray-800',
      cardRow       : 'flex justify-between text-sm py-1 border-b last:border-0',
      cardLabel     : 'font-medium text-gray-500 mr-2',
      cardValue     : 'text-gray-900 text-right',
    };

    const bs = {
      wrapperRoot   : 'tableplus-root',
      controlsRow   : 'd-flex flex-wrap justify-content-between align-items-center mb-3 gap-2',
      controlsLeft  : 'd-flex align-items-center gap-2 flex-wrap',
      controlsRight : 'd-flex align-items-center gap-2',
      bulkBar       : 'alert alert-info d-flex justify-content-between align-items-center py-2 px-3 mb-3',
      bulkInfo      : 'fw-semibold mb-0',
      bulkActions   : 'd-flex gap-2',
      scrollWrap    : 'table-responsive border rounded',
      tableEl       : 'table table-bordered table-hover table-sm mb-0',
      th            : '',
      thCheckbox    : 'text-center',
      td            : '',
      tdCheckbox    : 'text-center',
      trHover       : '',
      emptyCell     : 'text-center py-5 text-muted',
      paginationWrap: 'd-flex flex-wrap justify-content-between align-items-center mt-3 pt-3 border-top gap-2',
      paginationInfo: 'text-muted small',
      paginationBtns: 'd-flex align-items-center gap-1',
      inputSearch   : 'form-control form-control-sm',
      selectPerPage : 'form-select form-select-sm',
      labelPerPage  : 'form-label mb-0 small',
      btnRefresh    : 'btn btn-outline-secondary btn-sm',
      btnExport     : 'btn btn-primary btn-sm',
      btnColumn     : 'btn btn-outline-secondary btn-sm',
      btnReset      : 'btn btn-warning btn-sm',
      btnExportSel  : 'btn btn-primary btn-sm',
      btnClearSel   : 'btn btn-secondary btn-sm',
      dropdown      : 'dropdown-menu show position-absolute',
      dropdownItem  : 'dropdown-item',
      colVisMenu    : 'dropdown-menu show position-absolute p-2',
      colVisLabel   : 'd-flex align-items-center gap-2 dropdown-item',
      pageBtn       : 'btn btn-outline-secondary btn-sm',
      pageBtnActive : 'btn btn-primary btn-sm',
      pageBtnDis    : 'btn btn-outline-secondary btn-sm disabled',
      errorBox      : 'alert alert-danger',
      skeleton      : 'placeholder col-12',
      cardWrap      : 'card mb-2 p-3',
      cardTopBar    : 'd-flex justify-content-between align-items-center mb-2',
      cardTitle     : 'fw-semibold',
      cardRow       : 'd-flex justify-content-between border-bottom py-1 small',
      cardLabel     : 'text-muted me-2',
      cardValue     : 'text-end',
    };

    return (this.theme === 'bootstrap' ? bs : tw)[slot] || '';
  }

  // ─────────────────────────────────────────────────────────────────
  //  CDN LOADER  (idempotent)
  // ─────────────────────────────────────────────────────────────────
  _loadCSS(href, id) {
    if (document.getElementById(id)) return;
    const l = Object.assign(document.createElement('link'), {
      id, rel: 'stylesheet', href
    });
    document.head.appendChild(l);
  }

  _loadScript(src, id) {
    return new Promise(resolve => {
      if (document.getElementById(id)) { resolve(); return; }
      const s = Object.assign(document.createElement('script'), {
        id, src, onload: resolve
      });
      document.body.appendChild(s);
    });
  }

  _ensureThemeCDN() {
    if (this.theme === 'tailwind') {
      this._loadCSS(
        'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css',
        'tableplus-tailwind'
      );
    }
    // Bootstrap users are expected to load BS themselves, or we can do it:
    if (this.theme === 'bootstrap') {
      this._loadCSS(
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        'tableplus-bootstrap'
      );
    }
  }

  // ─────────────────────────────────────────────────────────────────
  //  PREFERENCES
  // ─────────────────────────────────────────────────────────────────
  savePreferencesToStorage() {
    if (!this.savePreferences) return;
    try {
      localStorage.setItem(this.storageKey, JSON.stringify({
        visibleColumns : this.visibleColumns,
        perPage        : this.perPage,
        sortKey        : this.sortKey,
        sortOrder      : this.sortOrder,
      }));
    } catch (_) {}
  }

  loadPreferences() {
    try {
      const saved = localStorage.getItem(this.storageKey);
      if (!saved) return;
      const p = JSON.parse(saved);
      this.visibleColumns = p.visibleColumns || this.visibleColumns;
      this.perPage        = p.perPage        || this.perPage;
      this.sortKey        = p.sortKey        || null;
      this.sortOrder      = p.sortOrder      || 'asc';
    } catch (e) {
      console.warn('TablePlus: failed to load prefs', e);
    }
  }

  // ─────────────────────────────────────────────────────────────────
  //  ROW SELECTION
  // ─────────────────────────────────────────────────────────────────
  toggleRowSelection(rowId) {
    const sid = String(rowId);
    if (this.selectedRows.has(sid)) this.selectedRows.delete(sid);
    else                            this.selectedRows.add(sid);
    this.updateSelectionUI();
    this.onRowSelect?.(Array.from(this.selectedRows));
  }

  selectAllRows() {
    this.data.forEach(item => this.selectedRows.add(String(item[this.rowIdentifier])));
    this.updateSelectionUI();
  }

  deselectAllRows() {
    this.selectedRows.clear();
    this.updateSelectionUI();
  }

  updateSelectionUI() {
    this.container?.querySelectorAll('.row-checkbox').forEach(cb => {
      cb.checked = this.selectedRows.has(String(cb.dataset.rowId));
    });

    const allCb = this.container?.querySelector('.select-all-checkbox');
    if (allCb) {
      allCb.checked = this.data.length > 0 &&
        this.data.every(item => this.selectedRows.has(String(item[this.rowIdentifier])));
    }

    this._renderBulkBar();
  }

  _renderBulkBar() {
    this.wrapper?.querySelector('.tp-bulk-bar')?.remove();
    if (this.selectedRows.size === 0) return;

    const bar = document.createElement('div');
    bar.className = `tp-bulk-bar ${this.cls('bulkBar')}`;

    const info = document.createElement('span');
    info.className = this.cls('bulkInfo');
    info.textContent = `${this.selectedRows.size} baris dipilih`;

    const acts = document.createElement('div');
    acts.className = this.cls('bulkActions');

    const exportBtn = document.createElement('button');
    exportBtn.innerHTML = '⬇ Export Selected';
    exportBtn.className = this.cls('btnExportSel');
    exportBtn.onclick = () => this.exportSelected();

    const clearBtn = document.createElement('button');
    clearBtn.textContent = '✕ Clear';
    clearBtn.className = this.cls('btnClearSel');
    clearBtn.onclick = () => this.deselectAllRows();

    acts.append(exportBtn, clearBtn);

    this.customActions.forEach(action => {
      const btn = document.createElement('button');
      btn.textContent = action.label;
      btn.className = action.className || this.cls('btnExportSel');
      btn.onclick = () => action.onClick(Array.from(this.selectedRows));
      acts.appendChild(btn);
    });

    bar.append(info, acts);
    this.wrapper.insertBefore(bar, this.wrapper.firstChild);
  }

  exportSelected() {
    const rows = this.data.filter(item => this.selectedRows.has(String(item[this.rowIdentifier])));
    this._downloadCSV(rows, 'selected-export');
  }

  // ─────────────────────────────────────────────────────────────────
  //  FETCH
  // ─────────────────────────────────────────────────────────────────
  async fetchData() {
    this.abortController?.abort();
    this.abortController = new AbortController();

    const params = new URLSearchParams({
      page    : this.page,
      per_page: this.perPage,
      search  : this.searchTerm,
    });

    if (Object.keys(this.columnFilters).length > 0)
      params.append('filters', JSON.stringify(this.columnFilters));

    if (this.sortKey) {
      params.append('sort_by',    this.sortKey);
      params.append('sort_order', this.sortOrder);
    }

    Object.entries(this.customFilters).forEach(([k, v]) => {
      if (v !== null && v !== '') params.append(k, v);
    });

    this.isLoading = true;
    this._showSkeleton();

    try {
      const res = await fetch(`${this.url}?${params}`, {
        signal : this.abortController.signal,
        cache  : 'no-store',
        headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
      });

      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const json = await res.json();

      if (json.status === 200 && json.data?.data) {
        this.data     = json.data.data;
        this.total    = json.data.pagination.total;
        this.lastPage = json.data.pagination.last_page;
        if (!this.allData.length) this.allData = [...this.data];
      } else {
        this.data = []; this.total = 0; this.lastPage = 1;
      }
    } catch (e) {
      if (e.name === 'AbortError') return;
      console.error('TablePlus fetch error:', e);
      this.data = []; this.total = 0; this.lastPage = 1;
      this._showError('Gagal memuat data. Silakan coba lagi.');
    } finally {
      this.isLoading = false;
      this.abortController = null;
    }
  }

  // ─────────────────────────────────────────────────────────────────
  //  UI STATES
  // ─────────────────────────────────────────────────────────────────
  _showSkeleton() {
    const tbody = this.container?.querySelector('tbody');
    if (!tbody) return;
    const colCount = this.visibleColumns.length + 1;
    tbody.innerHTML = Array(5).fill(0).map(() =>
      `<tr>${Array(colCount).fill(0).map(() =>
        `<td class="${this.cls('td')}"><div class="${this.cls('skeleton')}"></div></td>`
      ).join('')}</tr>`
    ).join('');
  }

  _showError(msg) {
    const div = document.createElement('div');
    div.className = this.cls('errorBox');
    div.innerHTML = `<strong>Error:</strong> ${msg}
      <button onclick="this.parentElement.remove()" style="float:right;font-weight:bold;cursor:pointer;">✕</button>`;
    this.wrapper?.insertBefore(div, this.container?.parentNode); // before scroll wrapper
    setTimeout(() => div.remove(), 5000);
  }

  // ─────────────────────────────────────────────────────────────────
  //  CONTROLS
  // ─────────────────────────────────────────────────────────────────
  _buildControls() {
    this.wrapper?.querySelector('.tp-controls')?.remove();

    const wrap = document.createElement('div');
    wrap.className = `tp-controls ${this.cls('controlsRow')}`;

    // ── LEFT ──
    const left = document.createElement('div');
    left.className = this.cls('controlsLeft');

    const search = document.createElement('input');
    search.type        = 'text';
    search.placeholder = 'Search...';
    search.value       = this.searchTerm;
    search.className   = this.cls('inputSearch');
    search.oninput = e => {
      clearTimeout(this.debounceTimer);
      this.debounceTimer = setTimeout(() => {
        this.searchTerm = e.target.value;
        this.page = 1;
        this.update();
      }, 400);
    };

    const perLabel = document.createElement('label');
    perLabel.className   = this.cls('labelPerPage');
    perLabel.textContent = 'Tampilkan:';

    const perSel = document.createElement('select');
    perSel.className = this.cls('selectPerPage');
    this.perPageOptions.forEach(n => {
      const o = document.createElement('option');
      o.value = n; o.textContent = n === 1000000 ? 'Semua' : n;
      o.selected = this.perPage === n;
      perSel.appendChild(o);
    });
    perSel.onchange = e => {
      this.perPage = parseInt(e.target.value);
      this.page = 1;
      this.savePreferencesToStorage();
      this.update();
    };

    const refreshBtn = document.createElement('button');
    refreshBtn.innerHTML   = '↺';
    refreshBtn.title       = 'Refresh';
    refreshBtn.className   = this.cls('btnRefresh');
    refreshBtn.onclick     = () => this.update();

    left.append(search, perLabel, perSel, refreshBtn);

    // ── RIGHT ──
    const right = document.createElement('div');
    right.className = this.cls('controlsRight');

    right.append(
      this._buildExportDropdown(),
      this._buildColVisDropdown(),
      this._buildResetBtn()
    );

    wrap.append(left, right);
    this.wrapper.insertBefore(wrap, this._scrollWrap);
  }

  _buildExportDropdown() {
    const btn = document.createElement('button');
    btn.innerHTML  = '⬇ Export';
    btn.className  = this.cls('btnExport');

    const menu = document.createElement('div');
    menu.className = `${this.cls('dropdown')} hidden`;

    ['Copy','CSV','Excel','PDF'].forEach(type => {
      const item = document.createElement('div');
      item.textContent = type;
      item.className   = this.cls('dropdownItem');
      item.onclick = () => {
        menu.classList.add('hidden');
        if (type === 'Copy')  this.exportCopy();
        if (type === 'CSV')   this.exportCSV();
        if (type === 'Excel') this.exportXLSX();
        if (type === 'PDF')   this.exportPDF();
      };
      menu.appendChild(item);
    });

    btn.onclick = e => { e.stopPropagation(); menu.classList.toggle('hidden'); };
    const wrap = document.createElement('div');
    wrap.style.position = 'relative';
    wrap.append(btn, menu);

    this._registerDocClick(() => menu.classList.add('hidden'));
    return wrap;
  }

  _buildColVisDropdown() {
    const btn = document.createElement('button');
    btn.innerHTML = '⚙ Columns';
    btn.className = this.cls('btnColumn');

    const menu = document.createElement('div');
    menu.className = `${this.cls('colVisMenu')} hidden`;

    Object.keys(this.columns).forEach(key => {
      const lbl = document.createElement('label');
      lbl.className = this.cls('colVisLabel');
      const cb = document.createElement('input');
      cb.type    = 'checkbox';
      cb.checked = this.visibleColumns.includes(key);
      cb.onchange = () => {
        if (cb.checked) this.visibleColumns.push(key);
        else            this.visibleColumns = this.visibleColumns.filter(c => c !== key);
        this.savePreferencesToStorage();
        this.renderTable();
      };
      const span = document.createElement('span');
      span.textContent = this.getColumnLabel(key);
      lbl.append(cb, span);
      menu.appendChild(lbl);
    });

    btn.onclick = e => { e.stopPropagation(); menu.classList.toggle('hidden'); };
    const wrap = document.createElement('div');
    wrap.style.position = 'relative';
    wrap.append(btn, menu);

    this._registerDocClick(() => menu.classList.add('hidden'));
    return wrap;
  }

  _buildResetBtn() {
    const btn = document.createElement('button');
    btn.textContent = '↺ Reset';
    btn.title       = 'Reset preferensi';
    btn.className   = this.cls('btnReset');
    btn.onclick     = () => {
      if (confirm('Reset semua preferensi ke default?')) {
        localStorage.removeItem(this.storageKey);
        location.reload();
      }
    };
    return btn;
  }

  /** Register a doc-click handler with automatic cleanup tracking. */
  _registerDocClick(fn) {
    document.addEventListener('click', fn);
    this._openMenus.push(fn);
  }

  // ─────────────────────────────────────────────────────────────────
  //  COLUMN FILTER DROPDOWN
  // ─────────────────────────────────────────────────────────────────
  _buildColFilterBtn(columnKey) {
    const wrap = document.createElement('span');
    wrap.style.position = 'relative';
    wrap.style.display  = 'inline-block';

    const btn = document.createElement('button');
    btn.textContent = '⋮';
    btn.title       = 'Filter kolom';
    btn.style.cssText = 'background:none;border:none;cursor:pointer;font-size:14px;padding:0 2px;color:#888;';
    wrap.appendChild(btn);

    let menu = null;

    btn.onclick = async e => {
      e.stopPropagation();
      // If already open, close it
      if (menu && document.body.contains(menu)) {
        document.body.removeChild(menu);
        menu = null;
        return;
      }

      menu = this._createColFilterMenu(columnKey);
      document.body.appendChild(menu);

      const rect = btn.getBoundingClientRect();
      menu.style.top  = `${rect.bottom + window.scrollY + 4}px`;
      menu.style.left = `${Math.max(0, rect.right - 200 + window.scrollX)}px`;

      const closeHandler = ev => {
        if (!menu.contains(ev.target) && ev.target !== btn) {
          document.body.removeChild(menu);
          menu = null;
          document.removeEventListener('click', closeHandler);
        }
      };
      setTimeout(() => document.addEventListener('click', closeHandler), 50);
    };

    return wrap;
  }

  _createColFilterMenu(columnKey) {
    const menu = document.createElement('div');
    menu.style.cssText =
      'position:absolute;background:#fff;border:1px solid #ddd;border-radius:6px;' +
      'box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:9999;width:200px;padding:8px;';

    const searchInput = document.createElement('input');
    searchInput.type        = 'text';
    searchInput.placeholder = 'Cari...';
    searchInput.style.cssText = 'width:100%;margin-bottom:8px;border:1px solid #ccc;border-radius:4px;padding:4px 8px;font-size:13px;box-sizing:border-box;';
    menu.appendChild(searchInput);

    const list = document.createElement('div');
    list.style.cssText = 'max-height:160px;overflow-y:auto;';
    menu.appendChild(list);

    let page = 1, hasMore = true, loading = false, searchTerm = '', uniqueVals = [];

    const appendOptions = values => {
      values.forEach(val => {
        if (uniqueVals.includes(val)) return;
        uniqueVals.push(val);

        const lbl = document.createElement('label');
        lbl.style.cssText = 'display:flex;align-items:center;gap:6px;padding:3px 4px;font-size:13px;cursor:pointer;border-radius:3px;';
        lbl.onmouseenter = () => lbl.style.background = '#f3f4f6';
        lbl.onmouseleave = () => lbl.style.background = '';

        const cb = document.createElement('input');
        cb.type    = 'checkbox';
        cb.checked = (this.columnFilters[columnKey] || []).includes(val);
        cb.onchange = () => {
          this.columnFilters[columnKey] = this.columnFilters[columnKey] || [];
          if (cb.checked) this.columnFilters[columnKey].push(val);
          else            this.columnFilters[columnKey] = this.columnFilters[columnKey].filter(v => v !== val);
          this.page = 1;
          this.update();
        };

        const span = document.createElement('span');
        span.textContent = val || '(kosong)';
        lbl.append(cb, span);
        list.appendChild(lbl);
      });
    };

    const loadValues = async (reset = false) => {
      if (loading || !hasMore) return;
      if (reset) { page = 1; hasMore = true; uniqueVals = []; list.innerHTML = ''; }
      loading = true;
      try {
        const res = await fetch(
          `${this.url}?distinct=${columnKey}&page=${page}&limit=25&search=${encodeURIComponent(searchTerm)}`,
          { cache: 'no-store', headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }
        );
        const json = await res.json();
        if (json.status === 200 && Array.isArray(json.data)) {
          appendOptions(json.data);
          if (json.data.length < 25) hasMore = false;
          else page++;
        } else { hasMore = false; }
      } catch (err) {
        console.error('TablePlus distinct error:', err);
      }
      loading = false;
    };

    list.addEventListener('scroll', () => {
      if (list.scrollTop + list.clientHeight >= list.scrollHeight - 5) loadValues();
    });

    searchInput.oninput = e => { searchTerm = e.target.value; loadValues(true); };
    searchInput.addEventListener('click', e => e.stopPropagation());

    loadValues(true);
    return menu;
  }

  // ─────────────────────────────────────────────────────────────────
  //  RENDER TABLE
  // ─────────────────────────────────────────────────────────────────
  renderTable() {
    const tbody  = this.container.querySelector('tbody');
    const thead  = this.container.querySelector('thead');
    tbody.innerHTML = '';
    thead.innerHTML = '';

    const isMobile = window.innerWidth < 640;
    const data     = this._sortData(this.data);

    if (data.length === 0 && !this.isLoading) {
      const colCount = this.visibleColumns.length + 1;
      tbody.innerHTML = `
        <tr><td colspan="${colCount}" class="${this.cls('emptyCell')}">
          <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#d1d5db">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <span>Tidak ada data</span>
          </div>
        </td></tr>`;
      return;
    }

    const frag = document.createDocumentFragment();

    if (!isMobile) {
      // ── DESKTOP HEADER ──
      const trH = document.createElement('tr');

      const thCb = document.createElement('th');
      thCb.className = this.cls('thCheckbox');
      const selectAll = document.createElement('input');
      selectAll.type      = 'checkbox';
      selectAll.className = 'select-all-checkbox';
      selectAll.style.cursor = 'pointer';
      selectAll.onchange = e => e.target.checked ? this.selectAllRows() : this.deselectAllRows();
      thCb.appendChild(selectAll);
      trH.appendChild(thCb);

      this.visibleColumns.forEach(k => {
        const th = document.createElement('th');
        th.className = this.cls('th');

        const inner = document.createElement('div');
        inner.style.cssText = 'display:flex;align-items:center;justify-content:space-between;gap:4px;';

        const leftPart = document.createElement('div');
        leftPart.style.cssText = 'display:flex;align-items:center;gap:4px;';

        const lbl = document.createElement('span');
        lbl.textContent = this.getColumnLabel(k);
        leftPart.append(lbl, this._buildColFilterBtn(k));

        const sortIcon = document.createElement('span');
        sortIcon.style.cssText = 'cursor:pointer;font-size:12px;user-select:none;';
        sortIcon.textContent   = this.sortKey === k ? (this.sortOrder === 'asc' ? '↑' : '↓') : '↕';
        sortIcon.onclick = e => {
          e.stopPropagation();
          if (this.sortKey === k) this.sortOrder = this.sortOrder === 'asc' ? 'desc' : 'asc';
          else { this.sortKey = k; this.sortOrder = 'asc'; }
          this.savePreferencesToStorage();
          this.renderTable();
        };

        inner.append(leftPart, sortIcon);
        th.appendChild(inner);
        trH.appendChild(th);
      });

      thead.appendChild(trH);

      // ── DESKTOP BODY ──
      data.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = this.cls('trHover');

        const tdCb = document.createElement('td');
        tdCb.className = this.cls('tdCheckbox');
        const cb = document.createElement('input');
        cb.type        = 'checkbox';
        cb.className   = 'row-checkbox';
        cb.style.cursor = 'pointer';
        cb.dataset.rowId = item[this.rowIdentifier];
        cb.checked     = this.selectedRows.has(String(item[this.rowIdentifier]));
        cb.onchange    = () => this.toggleRowSelection(item[this.rowIdentifier]);
        tdCb.appendChild(cb);
        tr.appendChild(tdCb);

        this.visibleColumns.forEach(k => {
          const td = document.createElement('td');
          td.className = this.cls('td');
          const content = this.getColumnContent(k, item);
          if (content instanceof HTMLElement) td.appendChild(content);
          else td.innerHTML = content ?? '';
          tr.appendChild(td);
        });

        frag.appendChild(tr);
      });
    } else {
      // ── MOBILE CARDS ──
      thead.innerHTML = '';

      data.forEach(item => {
        const card = document.createElement('div');
        card.className = this.cls('cardWrap');

        const topBar = document.createElement('div');
        topBar.className = this.cls('cardTopBar');

        const cb = document.createElement('input');
        cb.type        = 'checkbox';
        cb.className   = 'row-checkbox';
        cb.style.cursor = 'pointer';
        cb.dataset.rowId = item[this.rowIdentifier];
        cb.checked     = this.selectedRows.has(String(item[this.rowIdentifier]));
        cb.onchange    = () => this.toggleRowSelection(item[this.rowIdentifier]);

        const titleEl = document.createElement('span');
        titleEl.className = this.cls('cardTitle');
        const titleKey = this.visibleColumns.find(k => {
          const c = this.columns[k];
          return typeof c === 'object' && c?.isTitle;
        });
        if (titleKey) {
          const c = this.getColumnContent(titleKey, item);
          if (c instanceof HTMLElement) titleEl.appendChild(c);
          else titleEl.textContent = c ?? '';
        }

        topBar.append(cb, titleEl);
        card.appendChild(topBar);

        this.visibleColumns.forEach((k, i) => {
          if (i === 0 && titleKey === k) return; // skip title row
          const row = document.createElement('div');
          row.className = this.cls('cardRow');

          const lbl = document.createElement('span');
          lbl.className   = this.cls('cardLabel');
          lbl.textContent = this.getColumnLabel(k);

          const val = document.createElement('span');
          val.className = this.cls('cardValue');
          const c = this.getColumnContent(k, item);
          if (c instanceof HTMLElement) val.appendChild(c);
          else val.innerHTML = c ?? '';

          row.append(lbl, val);
          card.appendChild(row);
        });

        const tr = document.createElement('tr');
        const td = document.createElement('td');
        td.colSpan = this.visibleColumns.length + 1;
        td.style.padding = '0';
        td.appendChild(card);
        tr.appendChild(td);
        frag.appendChild(tr);
      });
    }

    tbody.appendChild(frag);
  }

  _sortData(data) {
    if (!this.sortKey) return data;
    return [...data].sort((a, b) => {
      const va = a[this.sortKey] ?? '', vb = b[this.sortKey] ?? '';
      if (va < vb) return this.sortOrder === 'asc' ? -1 : 1;
      if (va > vb) return this.sortOrder === 'asc' ?  1 : -1;
      return 0;
    });
  }

  // ─────────────────────────────────────────────────────────────────
  //  PAGINATION  ← outside the scroll container
  // ─────────────────────────────────────────────────────────────────
  renderPagination() {
    // The pagination div lives AFTER _scrollWrap in wrapper, not inside
    this.wrapper?.querySelector('.tp-pagination')?.remove();

    const pag = document.createElement('div');
    pag.className = `tp-pagination ${this.cls('paginationWrap')}`;

    const info = document.createElement('div');
    info.className = this.cls('paginationInfo');
    const start = (this.page - 1) * this.perPage + 1;
    const end   = Math.min(this.page * this.perPage, this.total);
    info.textContent = this.total > 0
      ? `Menampilkan ${start}–${end} dari ${this.total} data`
      : 'Tidak ada data';

    const btns = document.createElement('div');
    btns.className = this.cls('paginationBtns');

    const makeBtn = (label, targetPage, active = false, disabled = false) => {
      const b = document.createElement('button');
      b.textContent = label;
      b.disabled    = disabled;
      b.className   = disabled
        ? this.cls('pageBtnDis')
        : active
          ? this.cls('pageBtnActive')
          : this.cls('pageBtn');
      if (!disabled) {
        b.onclick = () => { this.page = targetPage; this.update(); };
      }
      return b;
    };

    btns.appendChild(makeBtn('‹', this.page - 1, false, this.page === 1));

    const total = this.lastPage || 1, range = 2;
    for (let i = 1; i <= total; i++) {
      if (i === 1 || i === total || (i >= this.page - range && i <= this.page + range)) {
        btns.appendChild(makeBtn(i, i, this.page === i));
      } else if (i === this.page - range - 1 || i === this.page + range + 1) {
        const dots = document.createElement('span');
        dots.textContent = '…';
        dots.style.padding = '0 4px';
        btns.appendChild(dots);
      }
    }

    btns.appendChild(makeBtn('›', this.page + 1, false, this.page === this.lastPage));

    pag.append(info, btns);
    this.wrapper.appendChild(pag);
  }

  // ─────────────────────────────────────────────────────────────────
  //  COLUMN HELPERS
  // ─────────────────────────────────────────────────────────────────
  getColumnLabel(key) {
    const c = this.columns[key];
    if (!c) return key;
    if (typeof c === 'string') return c;
    if (typeof c === 'object' && c.label) return c.label;
    return key;
  }

  getColumnContent(key, row) {
    const c = this.columns[key];
    if (!c) return row[key] ?? '';
    if (typeof c === 'function') return c(row);
    if (typeof c === 'object' && c.render) return c.render(row);
    return row[key] ?? '';
  }

  getColumnText(key, row) {
    const c = this.columns[key];
    if (!c) return row[key] ?? '';
    if (typeof c === 'object' && c.exportText) return c.exportText(row);
    if (typeof c === 'function') return c(row);
    if (typeof c === 'object' && c.render) {
      const content = c.render(row);
      if (content instanceof HTMLElement) return content.textContent || '';
      return String(content ?? '');
    }
    return row[key] ?? '';
  }

  // ─────────────────────────────────────────────────────────────────
  //  EXPORT
  // ─────────────────────────────────────────────────────────────────
  _downloadCSV(rows, filename = 'table-export') {
    const header = this.visibleColumns.map(k => `"${this.getColumnLabel(k)}"`).join(',');
    const body   = rows.map(item =>
      this.visibleColumns.map(k => `"${String(this.getColumnText(k, item) ?? '').replace(/"/g, '""')}"`).join(',')
    ).join('\n');
    const blob = new Blob([`${header}\n${body}`], { type: 'text/csv;charset=utf-8;' });
    const a    = Object.assign(document.createElement('a'), {
      href    : URL.createObjectURL(blob),
      download: `${filename}-${new Date().toISOString().split('T')[0]}.csv`,
    });
    a.click();
    URL.revokeObjectURL(a.href);
  }

  exportCSV()  { this._downloadCSV(this.data); }

  exportCopy() {
    const text = this.data.map(item =>
      this.visibleColumns.map(k => this.getColumnText(k, item)).join('\t')
    ).join('\n');
    navigator.clipboard.writeText(text).then(() => {
      const msg = Object.assign(document.createElement('div'), {
        innerHTML : '✓ Data disalin ke clipboard!',
        className : 'tableplus-toast',
      });
      Object.assign(msg.style, {
        position:'fixed', top:'16px', right:'16px', background:'#16a34a',
        color:'#fff', padding:'8px 16px', borderRadius:'6px', zIndex:9999,
        boxShadow:'0 4px 12px rgba(0,0,0,.15)', fontSize:'14px'
      });
      document.body.appendChild(msg);
      setTimeout(() => msg.remove(), 2200);
    }).catch(err => {
      console.error('Clipboard error:', err);
      alert('Gagal menyalin data');
    });
  }

  async exportXLSX() {
    if (!window.XLSX) await this._loadScript('https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js', 'tp-xlsx');
    const wsData = [
      this.visibleColumns.map(k => this.getColumnLabel(k)),
      ...this.data.map(item => this.visibleColumns.map(k => this.getColumnText(k, item)))
    ];
    const ws = window.XLSX.utils.aoa_to_sheet(wsData);
    const wb = window.XLSX.utils.book_new();
    window.XLSX.utils.book_append_sheet(wb, ws, 'Data');
    window.XLSX.writeFile(wb, `table-export-${new Date().toISOString().split('T')[0]}.xlsx`);
  }

  async exportPDF() {
    if (!window.jspdf) await this._loadScript('https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js', 'tp-jspdf');
    if (!window.jspdf?.autoTable) await this._loadScript('https://cdn.jsdelivr.net/npm/jspdf-autotable@3.5.31/dist/jspdf.plugin.autotable.min.js', 'tp-autotable');
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4');
    doc.setFontSize(14); doc.text('Laporan Data', 40, 30);
    doc.setFontSize(10); doc.text(`Generated: ${new Date().toLocaleString('id-ID')}`, 40, 50);
    doc.autoTable({
      head      : [this.visibleColumns.map(k => this.getColumnLabel(k))],
      body      : this.data.map(item => this.visibleColumns.map(k => this.getColumnText(k, item))),
      styles    : { fontSize: 9, cellPadding: 3 },
      headStyles: { fillColor: [41, 128, 185], textColor: 255 },
      theme     : 'grid',
      margin    : { top: 60 },
    });
    doc.save(`table-export-${new Date().toISOString().split('T')[0]}.pdf`);
  }

  // ─────────────────────────────────────────────────────────────────
  //  MAIN LIFECYCLE
  // ─────────────────────────────────────────────────────────────────
  async update() {
    await this.fetchData();
    this.renderTable();
    this.renderPagination();
    this.updateSelectionUI();
  }

  /**
   * Mount the table into the given CSS selector.
   * The selector should point to a <div> (wrapper), NOT a <table>.
   * TablePlus will build its own internal structure.
   */
  async render(selector) {
    this._ensureThemeCDN();

    const mountPoint = document.querySelector(selector);
    if (!mountPoint) return console.error(`TablePlus: element "${selector}" not found`);

    // Build wrapper
    this.wrapper = document.createElement('div');
    this.wrapper.className = this.cls('wrapperRoot');

    // Scroll container (horizontal scroll lives HERE)
    this._scrollWrap = document.createElement('div');
    this._scrollWrap.className = this.cls('scrollWrap');

    // The actual <table>
    this.container = document.createElement('table');
    this.container.className = this.cls('tableEl');
    this.container.appendChild(document.createElement('thead'));
    this.container.appendChild(document.createElement('tbody'));

    this._scrollWrap.appendChild(this.container);
    this.wrapper.appendChild(this._scrollWrap);
    mountPoint.appendChild(this.wrapper);

    // Controls inject themselves before _scrollWrap
    this._buildControls();

    await this.update();

    // Responsive re-render on resize (debounced via ResizeObserver)
    this._lastIsMobile = window.innerWidth < 640;
    this._resizeObserver = new ResizeObserver(() => {
      const isMobile = window.innerWidth < 640;
      if (isMobile !== this._lastIsMobile) {
        this._lastIsMobile = isMobile;
        this.renderTable();
      }
    });
    this._resizeObserver.observe(mountPoint);
  }

  destroy() {
    this.abortController?.abort();
    clearTimeout(this.debounceTimer);

    // Remove tracked document click listeners
    this._openMenus.forEach(fn => document.removeEventListener('click', fn));
    this._openMenus = [];

    this._resizeObserver?.disconnect();

    // Remove all DOM nodes created by TablePlus
    this.wrapper?.remove();
    this.wrapper = null;
    this.container = null;
    this.selectedRows.clear();
  }

  // ─────────────────────────────────────────────────────────────────
  //  PUBLIC API
  // ─────────────────────────────────────────────────────────────────
  getSelectedRows()  { return Array.from(this.selectedRows); }
  getSelectedData()  { return this.data.filter(item => this.selectedRows.has(String(item[this.rowIdentifier]))); }
  refresh()          { return this.update(); }
  setPage(p)         { this.page = p;           return this.update(); }
  setPerPage(n)      { this.perPage = n; this.page = 1; this.savePreferencesToStorage(); return this.update(); }
  setSearch(term)    { this.searchTerm = term;   this.page = 1; return this.update(); }
  setFilter(k, v)    { this.customFilters[k] = v; this.page = 1; return this.update(); }
  setFilters(obj)    { this.columnFilters = { ...this.columnFilters, ...obj }; this.page = 1; return this.update(); }
  resetFilters()     { this.customFilters = {};  this.page = 1; return this.update(); }

  // ─────────────────────────────────────────────────────────────────
  //  BACKWARD-COMPAT ALIASES  (method dari v1 yang diganti nama)
  //  Tetap ada agar kode lama tidak perlu diubah sama sekali.
  // ─────────────────────────────────────────────────────────────────

  /**
   * @deprecated Gunakan cls(slot) — getClass() tetap ada untuk kompatibilitas.
   * Map tipe lama ('button', 'input', dst.) ke slot cls() terdekat.
   */
  getClass(type) {
    const map = {
      button         : 'pageBtn',
      buttonPrimary  : 'btnExport',
      buttonSecondary: 'btnReset',
      input          : 'inputSearch',
      select         : 'selectPerPage',
      table          : 'tableEl',
      th             : 'th',
      td             : 'td',
      dropdown       : 'dropdown',
    };
    return this.cls(map[type] || type);
  }

  /**
   * @deprecated Gunakan _sortData() secara internal.
   * Tetap publik agar subclass / kode luar bisa memanggil table.sortData(data).
   */
  sortData(data) {
    return this._sortData(data);
  }

  /**
   * @deprecated Gunakan _renderBulkBar() secara internal.
   * Tetap publik untuk kompatibilitas dengan kode yang override method ini.
   */
  renderBulkActionsBar() {
    return this._renderBulkBar();
  }

  /**
   * @deprecated Gunakan _buildControls() secara internal.
   * Tetap publik untuk kompatibilitas.
   */
  renderControls() {
    return this._buildControls();
  }

  /**
   * Alias publik untuk _buildExportDropdown().
   * Kode lama yang memanggil createExportDropdown() tetap berjalan.
   */
  createExportDropdown() {
    return this._buildExportDropdown();
  }

  /**
   * Alias publik untuk _buildColVisDropdown().
   * Kode lama yang memanggil createColumnVisibilityDropdown() tetap berjalan.
   */
  createColumnVisibilityDropdown() {
    return this._buildColVisDropdown();
  }

  /**
   * Alias publik untuk _buildColFilterBtn().
   * Kode lama yang memanggil createColumnFilterDropdown(key) tetap berjalan.
   */
  createColumnFilterDropdown(columnKey) {
    return this._buildColFilterBtn(columnKey);
  }

  /**
   * @deprecated Gunakan _showError() secara internal.
   * Tetap publik untuk kompatibilitas.
   */
  showError(message) {
    return this._showError(message);
  }

  /**
   * @deprecated Gunakan _showSkeleton() secara internal.
   * Tetap publik untuk kompatibilitas.
   */
  showLoadingState() {
    return this._showSkeleton();
  }

  /** @deprecated No-op — loading kini ditangani oleh renderTable() otomatis. */
  hideLoadingState() {}
}