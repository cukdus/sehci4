<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Simpanan Sukarela</h3>
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
                    <button class="btn btn-success btn-sm me-2" id="btnAddSukarelaByAnggota">Tambah Simpanan</button>
                    <button class="btn btn-outline-primary btn-sm me-2" id="btnExport">Export</button>
                  </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle text-nowrap">
                    <thead class="table-light">
                        <tr>
                        <th>No Anggota</th>
                        <th>Nama</th>
                        <th>Tanggal Simpan</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
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

<div class="modal fade" id="singleSukarelaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Simpanan Sukarela per Anggota</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="singleSukarelaForm">
        <div class="modal-body">
          <div id="singleSukarelaAlert"></div>
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
            <div class="col-md-6">
              <label class="form-label">Tipe Simpanan</label>
              <select class="form-select" name="tipe_sukarela" id="singleTipeSukarela" required>
                <option value="">Pilih tipe</option>
                <option value="biasa">Biasa</option>
                <option value="berjangka">Berjangka</option>
              </select>
            </div>
            <div class="col-md-6" id="singleJangkaGroup" style="display:none;">
              <label class="form-label">Jangka Waktu (bulan)</label>
              <input type="number" class="form-control" name="jangka_waktu" id="singleJangkaWaktu" min="1">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success" id="singleSukarelaSubmit">Tambah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="editSukarelaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Simpanan Sukarela</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="editSukarelaForm">
        <div class="modal-body">
          <div id="editSukarelaAlert"></div>
          <input type="hidden" name="id_simpanan" id="editIdSimpanan">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nama Anggota</label>
              <input type="text" class="form-control" id="editNamaAnggota" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">No Anggota</label>
              <input type="text" class="form-control" id="editNoAnggota" readonly>
            </div>
            <div class="col-md-4">
              <label class="form-label">Tanggal Simpan</label>
              <input type="date" class="form-control" name="tanggal_simpan" id="editTanggalSimpan" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Jumlah</label>
              <input type="number" class="form-control" name="jumlah" id="editJumlah" min="1" step="0.01" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" id="editStatus">
                <option value="pending">Pending</option>
                <option value="aktif">Aktif</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tipe Simpanan</label>
              <select class="form-select" name="tipe_sukarela" id="editTipeSukarela" required>
                <option value="">Pilih tipe</option>
                <option value="biasa">Biasa</option>
                <option value="berjangka">Berjangka</option>
              </select>
            </div>
            <div class="col-md-6" id="editJangkaGroup" style="display:none;">
              <label class="form-label">Jangka Waktu (bulan)</label>
              <input type="number" class="form-control" name="jangka_waktu" id="editJangkaWaktu" min="1">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning" id="editSukarelaSubmit">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    let page = 1; const perPage = 25;
    const rowsEl = document.getElementById('rows');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const filterStatusEl = document.getElementById('filterStatus');
    const btnAddSukarelaByAnggota = document.getElementById('btnAddSukarelaByAnggota');
    const singleModal = new bootstrap.Modal(document.getElementById('singleSukarelaModal'));
    const editModal = new bootstrap.Modal(document.getElementById('editSukarelaModal'));
    const singleForm = document.getElementById('singleSukarelaForm');
    const singleAlert = document.getElementById('singleSukarelaAlert');
    const singleSubmit = document.getElementById('singleSukarelaSubmit');
    const singleNama = document.getElementById('singleNama');
    const singleIdAnggota = document.getElementById('singleIdAnggota');
    const singleSuggest = document.getElementById('singleSuggest');
    const singleTanggalSimpan = document.getElementById('singleTanggalSimpan');
    const singleTipeSukarela = document.getElementById('singleTipeSukarela');
    const singleJangkaGroup = document.getElementById('singleJangkaGroup');
    const singleJangkaWaktu = document.getElementById('singleJangkaWaktu');
    const editForm = document.getElementById('editSukarelaForm');
    const editAlert = document.getElementById('editSukarelaAlert');
    const editSubmit = document.getElementById('editSukarelaSubmit');
    const editIdSimpanan = document.getElementById('editIdSimpanan');
    const editNamaAnggota = document.getElementById('editNamaAnggota');
    const editNoAnggota = document.getElementById('editNoAnggota');
    const editTanggalSimpan = document.getElementById('editTanggalSimpan');
    const editJumlah = document.getElementById('editJumlah');
    const editStatus = document.getElementById('editStatus');
    const editTipeSukarela = document.getElementById('editTipeSukarela');
    const editJangkaGroup = document.getElementById('editJangkaGroup');
    const editJangkaWaktu = document.getElementById('editJangkaWaktu');
    function escHtml(v){return String(v||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');}
    function escAttr(v){return String(v||'').replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#039;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
    function fmt(n){return new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n||0);}    
    function fmtDate(d){if(!d)return '-';var m=d.match(/^(\d{4})-(\d{2})-(\d{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function badgeClass(st){var v=(st||'').toLowerCase(); if(v==='aktif') return 'text-bg-primary'; if(v==='pending') return 'text-bg-warning'; return 'text-bg-secondary';}
  function load(){
      var qs = '';
      var st = (filterStatusEl?.value||'').trim();
      if(st){ qs += '&status='+encodeURIComponent(st); }
      fetch('/admin/api/simpanan/sukarela?page='+page+qs)
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
              <td>${s.tipe_sukarela||'-'}</td>
              <td>${fmt(parseFloat(s.jumlah||0))}</td>
              <td><span class="badge ${badgeClass(s.status)}">${s.status||'-'}</span></td>
              <td>${fmtDate(s.tanggal_jatuh_tempo)}</td>
              <td class="text-center">
                <div class="d-inline-flex align-items-center gap-2">
                  <button type="button" class="btn btn-sm btn-warning btn-edit-sukarela"
                    data-id="${escAttr(s.id_simpanan||'')}"
                    data-no-anggota="${escAttr(s.no_anggota||'')}"
                    data-nama="${escAttr(s.nama||'')}"
                    data-tanggal-simpan="${escAttr(s.tanggal_simpan||'')}"
                    data-tipe-sukarela="${escAttr(s.tipe_sukarela||'')}"
                    data-jumlah="${escAttr(s.jumlah||'')}"
                    data-status="${escAttr(s.status||'')}"
                    data-jangka-waktu="${escAttr(s.jangka_waktu||'')}">Edit</button>
                  <button type="button" class="btn btn-sm btn-danger btn-delete-sukarela"
                    data-id="${escAttr(s.id_simpanan||'')}"
                    data-nama="${escAttr(s.nama||'')}">Hapus</button>
                  <div class="form-check form-switch d-inline-block mb-0">
                  <input class="form-check-input" type="checkbox" role="switch" ${s.status==='aktif'?'checked':''} data-id="${s.id_simpanan}" onchange="window._toggleSukarela(this)" />
                  </div>
                </div>
              </td>
            `;
            rowsEl.appendChild(tr);
          });
          pageInfo.textContent = `Halaman ${meta.page||1} dari ${meta.totalPages||1}`;
          document.getElementById('totalSimpanan').textContent = 'Rp ' + fmt(parseFloat(meta.sumAll||0));
          document.getElementById('paidCount').textContent = (meta.paidCount||0);
          document.getElementById('unpaidCount').textContent = (meta.unpaidCount||0);
          prevBtn.disabled = (meta.page||1) <= 1;
          nextBtn.disabled = (meta.page||1) >= (meta.totalPages||1);
        });
    }
    prevBtn.addEventListener('click',()=>{ if(page>1){ page--; load(); } });
    nextBtn.addEventListener('click',()=>{ page++; load(); });
    if(filterStatusEl){ filterStatusEl.addEventListener('change', ()=>{ page = 1; load(); }); }
    window._toggleSukarela = function(sw){
      const id = sw.getAttribute('data-id');
      const status = sw.checked ? 'aktif' : 'pending';
      const ok = confirm('Ubah status simpanan sukarela menjadi '+status+'?');
      if(!ok){ sw.checked = !sw.checked; return; }
      fetch('/admin/simpanan/sukarela/toggle',{
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
    function updateBerjangkaUI(){
      if (singleTipeSukarela.value === 'berjangka') {
        singleJangkaGroup.style.display = '';
        singleJangkaWaktu.required = true;
      } else {
        singleJangkaGroup.style.display = 'none';
        singleJangkaWaktu.required = false;
        singleJangkaWaktu.value = '';
      }
    }
    function updateEditBerjangkaUI(){
      if (editTipeSukarela.value === 'berjangka') {
        editJangkaGroup.style.display = '';
        editJangkaWaktu.required = true;
      } else {
        editJangkaGroup.style.display = 'none';
        editJangkaWaktu.required = false;
        editJangkaWaktu.value = '';
      }
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
    singleTipeSukarela.addEventListener('change', updateBerjangkaUI);
    editTipeSukarela.addEventListener('change', updateEditBerjangkaUI);
    document.addEventListener('click', function(e){
      const editBtn = e.target && e.target.closest ? e.target.closest('.btn-edit-sukarela') : null;
      if (editBtn) {
        editAlert.innerHTML = '';
        editForm.reset();
        editIdSimpanan.value = editBtn.getAttribute('data-id') || '';
        editNamaAnggota.value = editBtn.getAttribute('data-nama') || '';
        editNoAnggota.value = editBtn.getAttribute('data-no-anggota') || '';
        editTanggalSimpan.value = editBtn.getAttribute('data-tanggal-simpan') || '';
        editJumlah.value = editBtn.getAttribute('data-jumlah') || '';
        editStatus.value = editBtn.getAttribute('data-status') || 'pending';
        editTipeSukarela.value = editBtn.getAttribute('data-tipe-sukarela') || '';
        editJangkaWaktu.value = editBtn.getAttribute('data-jangka-waktu') || '';
        updateEditBerjangkaUI();
        editModal.show();
        return;
      }
      const deleteBtn = e.target && e.target.closest ? e.target.closest('.btn-delete-sukarela') : null;
      if (deleteBtn) {
        const id = deleteBtn.getAttribute('data-id') || '';
        const nama = deleteBtn.getAttribute('data-nama') || '';
        const ok = confirm('Hapus simpanan sukarela milik ' + (nama || 'anggota') + '?');
        if (!ok) return;
        const fd = new FormData();
        fd.append('id_simpanan', id);
        fetch('/admin/api/simpanan/sukarela/delete', { method:'POST', body: fd })
          .then(async (x)=>{ const j = await x.json(); if(!x.ok) throw j; return j; })
          .then(()=>{ load(); })
          .catch((err)=>{
            const msg = err && (err.error || err.message) ? (err.error || err.message) : 'Gagal menghapus';
            alert(msg);
          });
        return;
      }
      const btn = e.target && e.target.closest ? e.target.closest('#singleSuggest button') : null;
      if (btn) {
        singleIdAnggota.value = btn.getAttribute('data-id') || '';
        singleNama.value = (btn.getAttribute('data-nama') || '') + ' (' + (btn.getAttribute('data-no') || '') + ')';
        showSuggest([]);
        return;
      }
      if (!e.target.closest || !e.target.closest('#singleSuggest') && e.target !== singleNama) {
        showSuggest([]);
      }
    });
    btnAddSukarelaByAnggota.addEventListener('click', function(){
      singleAlert.innerHTML = '';
      singleForm.reset();
      singleIdAnggota.value = '';
      singleTanggalSimpan.value = todayIso();
      updateBerjangkaUI();
      singleModal.show();
    });
    singleForm.addEventListener('submit', function(e){
      e.preventDefault();
      singleAlert.innerHTML = '';
      if (!singleIdAnggota.value) {
        singleAlert.innerHTML = '<div class="alert alert-danger mb-0">Silakan pilih anggota dari daftar hasil pencarian.</div>';
        return;
      }
      if (!singleTipeSukarela.value) {
        singleAlert.innerHTML = '<div class="alert alert-danger mb-0">Silakan pilih tipe simpanan.</div>';
        return;
      }
      if (singleTipeSukarela.value === 'berjangka' && !(parseInt(singleJangkaWaktu.value || '0', 10) > 0)) {
        singleAlert.innerHTML = '<div class="alert alert-danger mb-0">Jangka waktu wajib diisi untuk simpanan berjangka.</div>';
        return;
      }
      singleSubmit.disabled = true;
      singleSubmit.textContent = 'Memproses...';
      const fd = new FormData(singleForm);
      fetch('/admin/api/simpanan/sukarela/add-by-anggota', { method:'POST', body: fd })
        .then(async (x)=>{ const j = await x.json(); if(!x.ok) throw j; return j; })
        .then(()=>{
          singleAlert.innerHTML = '<div class="alert alert-success mb-0">Simpanan sukarela berhasil ditambahkan.</div>';
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
    editForm.addEventListener('submit', function(e){
      e.preventDefault();
      editAlert.innerHTML = '';
      if (!editTipeSukarela.value) {
        editAlert.innerHTML = '<div class="alert alert-danger mb-0">Silakan pilih tipe simpanan.</div>';
        return;
      }
      if (editTipeSukarela.value === 'berjangka' && !(parseInt(editJangkaWaktu.value || '0', 10) > 0)) {
        editAlert.innerHTML = '<div class="alert alert-danger mb-0">Jangka waktu wajib diisi untuk simpanan berjangka.</div>';
        return;
      }
      editSubmit.disabled = true;
      editSubmit.textContent = 'Menyimpan...';
      const fd = new FormData(editForm);
      fetch('/admin/api/simpanan/sukarela/update', { method:'POST', body: fd })
        .then(async (x)=>{ const j = await x.json(); if(!x.ok) throw j; return j; })
        .then(()=>{
          editAlert.innerHTML = '<div class="alert alert-success mb-0">Simpanan sukarela berhasil diperbarui.</div>';
          load();
        })
        .catch((err)=>{
          const msg = err && (err.error || err.message) ? (err.error || err.message) : 'Gagal memperbarui';
          editAlert.innerHTML = '<div class="alert alert-danger mb-0">' + escHtml(msg) + '</div>';
        })
        .finally(()=>{
          editSubmit.disabled = false;
          editSubmit.textContent = 'Simpan Perubahan';
        });
    });
    singleTanggalSimpan.value = todayIso();
    updateBerjangkaUI();
    updateEditBerjangkaUI();
    load();
  });
</script>
