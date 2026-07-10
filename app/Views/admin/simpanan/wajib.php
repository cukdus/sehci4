<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Simpanan Wajib</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-outline card-secondary">
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
              <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                  <h6>Total Simpanan</h6>
                  <h3 class="fw-bold mb-0" id="totalSimpanan">Rp 0,00</h3>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
              <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                  <h6>Terbayar</h6>
                  <h3 class="fw-bold mb-0" id="paidCount">0</h3>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
              <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                  <h6>Belum Terbayar</h6>
                  <h3 class="fw-bold mb-0" id="unpaidCount">0</h3>
                </div>
              </div>
            </div>
          </div>
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-body d-flex gap-3">
              <select class="form-select w-25" id="filterBulan">
                <option value="">Filter Bulan</option>
                <option>Januari</option>
                <option>Februari</option>
                <option>Maret</option>
              </select>

              <select class="form-select w-25" id="filterTahun">
                <option value="">Filter Tahun</option>
                <option>2025</option>
                <option>2024</option>
              </select>

              <button class="btn btn-secondary" id="btnApplyFilter">
                <i class="fas fa-filter"></i> Terapkan
              </button>
            </div>
          </div>
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="mb-3 d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2 align-items-center">
                  <label for="filterStatus" class="form-label mb-0">Status</label>
                  <select id="filterStatus" class="form-select form-select-sm" style="width:auto; min-width: 160px;">
                    <option value="">Semua</option>
                    <option value="aktif">Aktif</option>
                    <option value="pending">Pending</option>
                  </select>
                </div>
                <div>
                  <button class="btn btn-primary btn-sm me-2" id="btnBulkAddWajib">Tambah Wajib (Semua)</button>
                  <button class="btn btn-success btn-sm me-2" id="btnAddWajibByAnggota">Tambah Wajib (Per Anggota)</button>
                  <button class="btn btn-outline-primary btn-sm" id="btnExport">Export</button>
                </div>
              </div>

              <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap">
                  <thead class="table-light">
                    <tr>
                      <th>No Anggota</th>
                      <th>Nama</th>
                      <th>Tanggal Simpan</th>
                      <th>Jumlah</th>
                      <th>Status</th>
                      <th class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="rows"></tbody>
                </table>
              </div>
              <div class="d-flex justify-content-between align-items-center mt-2" id="pagination">
                <button class="btn btn-outline-secondary btn-sm" id="prev">Sebelumnya</button>
                <span id="pageInfo"></span>
                <button class="btn btn-outline-secondary btn-sm" id="next">Berikutnya</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</main>

<div class="modal fade" id="bulkWajibModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Simpanan Wajib untuk Semua Anggota Aktif</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="bulkWajibForm">
        <div class="modal-body">
          <div id="bulkWajibAlert"></div>
          <div class="alert alert-info">
            Sistem akan membuat data simpanan wajib untuk semua anggota dengan status aktif dan memiliki nomor anggota. Jika untuk tanggal yang sama sudah ada, data akan dilewati atau ditimpa sesuai pilihan.
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Tanggal Simpan</label>
              <input type="date" class="form-control" name="tanggal_simpan" id="bulkTanggalSimpan" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Jumlah</label>
              <input type="number" class="form-control" name="jumlah" id="bulkJumlah" min="1" step="0.01" required>
              <small class="text-muted">Default diambil dari setting fee wajib.</small>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" id="bulkStatus">
                <option value="pending">Pending</option>
                <option value="aktif">Aktif</option>
              </select>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="bulkOverwrite" name="overwrite">
                <label class="form-check-label" for="bulkOverwrite">
                  Timpa data jika sudah ada (update jumlah & status)
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="bulkWajibSubmit">Proses</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="singleWajibModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Simpanan Wajib per Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="singleWajibForm">
        <div class="modal-body">
          <div id="singleWajibAlert"></div>
          <div class="row g-3">
            <div class="col-12 position-relative">
              <label class="form-label">Nama Anggota</label>
              <input type="text" class="form-control" id="singleNama" placeholder="Ketik minimal 2 huruf nama atau nomor anggota" autocomplete="off" required>
              <input type="hidden" name="id_anggota" id="singleIdAnggota">
              <div class="list-group position-absolute w-100 shadow-sm" id="singleSuggest" style="z-index: 1080; display:none;"></div>
              <small class="text-muted">Hanya menampilkan anggota aktif yang memiliki nomor anggota.</small>
            </div>
            <div class="col-md-4">
              <label class="form-label">Tanggal Simpan</label>
              <input type="date" class="form-control" name="tanggal_simpan" id="singleTanggalSimpan" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Jumlah</label>
              <input type="number" class="form-control" name="jumlah" id="singleJumlah" min="1" step="0.01" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" id="singleStatus">
                <option value="pending">Pending</option>
                <option value="aktif">Aktif</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success" id="singleWajibSubmit">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  (function(){
    let page = 1; const perPage = 25;
    const rowsEl = document.getElementById('rows');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const filterStatusEl = document.getElementById('filterStatus');
    const btnBulkAddWajib = document.getElementById('btnBulkAddWajib');
    const btnAddWajibByAnggota = document.getElementById('btnAddWajibByAnggota');
    const bulkModal = new bootstrap.Modal(document.getElementById('bulkWajibModal'));
    const singleModal = new bootstrap.Modal(document.getElementById('singleWajibModal'));
    const bulkForm = document.getElementById('bulkWajibForm');
    const bulkAlert = document.getElementById('bulkWajibAlert');
    const bulkSubmit = document.getElementById('bulkWajibSubmit');
    const bulkTanggalSimpan = document.getElementById('bulkTanggalSimpan');
    const bulkJumlah = document.getElementById('bulkJumlah');
    const singleForm = document.getElementById('singleWajibForm');
    const singleAlert = document.getElementById('singleWajibAlert');
    const singleSubmit = document.getElementById('singleWajibSubmit');
    const singleNama = document.getElementById('singleNama');
    const singleIdAnggota = document.getElementById('singleIdAnggota');
    const singleSuggest = document.getElementById('singleSuggest');
    const singleTanggalSimpan = document.getElementById('singleTanggalSimpan');
    const singleJumlah = document.getElementById('singleJumlah');
    function escHtml(v){return String(v||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}
    function fmt(n){return new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n||0);}    
    function fmtDate(d){if(!d)return '-';var m=d.match(/^(\d{4})-(\d{2})-(\d{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function badgeClass(st){var v=(st||'').toLowerCase(); if(v==='aktif') return 'text-bg-primary'; if(v==='pending') return 'text-bg-warning'; return 'text-bg-secondary';}
    function load(){
      var qs = '';
      var st = (filterStatusEl?.value||'').trim();
      if(st){ qs += '&status='+encodeURIComponent(st); }
      fetch('/admin/api/simpanan/wajib?page='+page+qs)
        .then(r=>r.json())
        .then(j=>{
          const data = j.data||[]; const meta = j.meta||{};
          rowsEl.innerHTML = '';
          data.forEach((s)=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${s.no_anggota||'-'}</td>
              <td>${s.nama||'-'}</td>
              <td>${fmtDate(s.tanggal_simpan)}</td>
              <td>${fmt(parseFloat(s.jumlah||0))}</td>
              <td><span class="badge ${badgeClass(s.status)}">${s.status||'-'}</span></td>
              <td class="text-center">
                <div class="form-check form-switch d-inline-block">
                  <input class="form-check-input" type="checkbox" role="switch" ${s.status==='aktif'?'checked':''} data-id="${s.id_simpanan}" onchange="window._toggleWajib(this)" />
                </div>
              </td>
            `;
            rowsEl.appendChild(tr);
          });
          document.getElementById('totalSimpanan').textContent = 'Rp ' + fmt(parseFloat(meta.sumAll||0));
          document.getElementById('paidCount').textContent = (meta.paidCount||0);
          document.getElementById('unpaidCount').textContent = (meta.unpaidCount||0);
          pageInfo.textContent = `Halaman ${meta.page||1} dari ${meta.totalPages||1}`;
          prevBtn.disabled = (meta.page||1) <= 1;
          nextBtn.disabled = (meta.page||1) >= (meta.totalPages||1);
        });
    }
    prevBtn.addEventListener('click',()=>{ if(page>1){ page--; load(); } });
    nextBtn.addEventListener('click',()=>{ page++; load(); });
    if(filterStatusEl){ filterStatusEl.addEventListener('change', ()=>{ page = 1; load(); }); }
    window._toggleWajib = function(sw){
      const id = sw.getAttribute('data-id');
      const status = sw.checked ? 'aktif' : 'pending';
      const ok = confirm('Ubah status simpanan wajib menjadi '+status+'?');
      if(!ok){ sw.checked = !sw.checked; return; }
      fetch('/admin/simpanan/wajib/toggle',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_simpanan='+encodeURIComponent(id)+'&status='+encodeURIComponent(status)
      }).then(r=>r.json()).then(j=>{ load(); });
    }
    function todayIso(){
      const d = new Date();
      const mm = ('0'+(d.getMonth()+1)).slice(-2);
      const dd = ('0'+d.getDate()).slice(-2);
      return d.getFullYear() + '-' + mm + '-' + dd;
    }
    function loadFeeWajib(){
      fetch('/admin/api/simpanan/config')
        .then(x=>x.json())
        .then((j)=>{
          const fee = parseFloat(j.fee_wajib || 0) || 0;
          if (fee > 0) {
            bulkJumlah.value = String(fee);
            singleJumlah.value = String(fee);
          }
        })
        .catch(()=>{});
    }
    function showSuggest(items){
      if (!Array.isArray(items) || !items.length) {
        singleSuggest.style.display = 'none';
        singleSuggest.innerHTML = '';
        return;
      }
      singleSuggest.innerHTML = items.map((it)=>{
        const label = escHtml((it.nama || '-') + ' (' + (it.no_anggota || '-') + ')');
        return '<button type="button" class="list-group-item list-group-item-action" data-id="' + escHtml(it.id_anggota || '') + '" data-nama="' + escHtml(it.nama || '') + '" data-no="' + escHtml(it.no_anggota || '') + '">' + label + '</button>';
      }).join('');
      singleSuggest.style.display = 'block';
    }
    let searchTimer = null;
    function doSearch(){
      const term = (singleNama.value || '').trim();
      singleIdAnggota.value = '';
      if (term.length < 2) {
        showSuggest([]);
        return;
      }
      fetch('/admin/api/anggota/search?term=' + encodeURIComponent(term))
        .then(x=>x.json())
        .then((j)=>{ showSuggest(j.items || []); })
        .catch(()=>{ showSuggest([]); });
    }
    singleNama.addEventListener('input', function(){
      clearTimeout(searchTimer);
      searchTimer = setTimeout(doSearch, 250);
    });
    singleNama.addEventListener('focus', function(){ doSearch(); });
    document.addEventListener('click', function(e){
      const btn = e.target && e.target.closest ? e.target.closest('#singleSuggest button') : null;
      if (btn) {
        const id = btn.getAttribute('data-id') || '';
        const nm = btn.getAttribute('data-nama') || '';
        const no = btn.getAttribute('data-no') || '';
        singleIdAnggota.value = id;
        singleNama.value = nm + ' (' + no + ')';
        showSuggest([]);
        return;
      }
      if (!e.target.closest || !e.target.closest('#singleSuggest') && e.target !== singleNama) {
        showSuggest([]);
      }
    });

    btnBulkAddWajib.addEventListener('click', function(){
      bulkAlert.innerHTML = '';
      bulkTanggalSimpan.value = todayIso();
      singleSuggest.style.display = 'none';
      bulkModal.show();
    });
    btnAddWajibByAnggota.addEventListener('click', function(){
      singleAlert.innerHTML = '';
      singleForm.reset();
      singleIdAnggota.value = '';
      singleTanggalSimpan.value = todayIso();
      loadFeeWajib();
      singleModal.show();
    });
    bulkForm.addEventListener('submit', function(e){
      e.preventDefault();
      bulkAlert.innerHTML = '';
      bulkSubmit.disabled = true;
      bulkSubmit.textContent = 'Memproses...';
      const fd = new FormData(bulkForm);
      fetch('/admin/api/simpanan/wajib/bulk-add', { method:'POST', body: fd })
        .then(async (x)=>{ const j = await x.json(); if(!x.ok) throw j; return j; })
        .then((j)=>{
          const lines = [];
          lines.push('Selesai membuat data simpanan wajib.');
          lines.push('Target: ' + (j.total_target || 0));
          lines.push('Berhasil: ' + (j.inserted || 0));
          lines.push('Ditimpa: ' + (j.updated || 0));
          lines.push('Dilewati: ' + (j.skipped || 0));
          lines.push('Gagal: ' + (j.failed || 0));
          bulkAlert.innerHTML = '<div class="alert alert-success mb-0" style="white-space: pre-line;">' + escHtml(lines.join('\n')) + '</div>';
          page = 1;
          load();
        })
        .catch((err)=>{
          const msg = err && (err.error || err.message) ? (err.error || err.message) : 'Gagal memproses';
          bulkAlert.innerHTML = '<div class="alert alert-danger mb-0">' + escHtml(msg) + '</div>';
        })
        .finally(()=>{
          bulkSubmit.disabled = false;
          bulkSubmit.textContent = 'Proses';
        });
    });
    singleForm.addEventListener('submit', function(e){
      e.preventDefault();
      singleAlert.innerHTML = '';
      if (!singleIdAnggota.value) {
        singleAlert.innerHTML = '<div class="alert alert-danger mb-0">Silakan pilih anggota dari daftar hasil pencarian.</div>';
        return;
      }
      singleSubmit.disabled = true;
      singleSubmit.textContent = 'Memproses...';
      const fd = new FormData(singleForm);
      fetch('/admin/api/simpanan/wajib/add-by-anggota', { method:'POST', body: fd })
        .then(async (x)=>{ const j = await x.json(); if(!x.ok) throw j; return j; })
        .then(()=>{
          singleAlert.innerHTML = '<div class="alert alert-success mb-0">Simpanan wajib berhasil ditambahkan.</div>';
          page = 1;
          load();
        })
        .catch((err)=>{
          const msg = err && (err.error || err.message) ? (err.error || err.message) : 'Gagal menambahkan';
          singleAlert.innerHTML = '<div class="alert alert-danger mb-0">' + escHtml(msg) + '</div>';
        })
        .finally(()=>{
          singleSubmit.disabled = false;
          singleSubmit.textContent = 'Tambah';
        });
    });
    bulkTanggalSimpan.value = todayIso();
    singleTanggalSimpan.value = todayIso();
    loadFeeWajib();
    load();
  })();
</script>
