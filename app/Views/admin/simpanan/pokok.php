<main class="app-main">
  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Daftar Simpanan Pokok</h5>
        <a href="https://eqiyu.id/admin/setting/users/create" class="btn btn-sm btn-primary">
          <i class="bi bi-plus-lg"></i> Tambah Simpanan
        </a>
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
</main>
<script>
  (function(){
    let page = 1; const perPage = 25;
    const rowsEl = document.getElementById('rows');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    const filterStatusEl = document.getElementById('filterStatus');
    function fmt(n){return new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n||0);}    
    function fmtDate(d){if(!d)return '-';var m=d.match(/^(\d{4})-(\d{2})-(\d{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function badgeClass(st){var v=(st||'').toLowerCase(); if(v==='aktif') return 'text-bg-primary'; if(v==='pending') return 'text-bg-warning'; return 'text-bg-secondary';}
    function load(){
      var qs = '';
      var st = (filterStatusEl?.value||'').trim();
      if(st){ qs += '&status='+encodeURIComponent(st); }
      fetch('/admin/api/simpanan/pokok?page='+page+qs)
        .then(r=>r.json())
        .then(j=>{
          const data = j.data||[]; const meta = j.meta||{};
          rowsEl.innerHTML = '';
          const start = (meta.page-1)*perPage;
          data.forEach((s,i)=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${s.no_anggota||'-'}</td>
              <td>${s.nama||'-'}</td>
              <td>${fmtDate(s.tanggal_simpan)}</td>
              <td>${fmt(parseFloat(s.jumlah||0))}</td>
              <td><span class="badge ${badgeClass(s.status)}">${s.status||'-'}</span></td>
              <td class="text-center">
                <div class="form-check form-switch d-inline-block">
                  <input class="form-check-input" type="checkbox" role="switch" ${s.status==='aktif'?'checked':''} data-id="${s.id_simpanan}" onchange="window._togglePokok(this)" />
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
    window._togglePokok = function(sw){
      const id = sw.getAttribute('data-id');
      const status = sw.checked ? 'aktif' : 'pending';
      const ok = confirm('Ubah status simpanan pokok menjadi '+status+'?');
      if(!ok){ sw.checked = !sw.checked; return; }
      fetch('/admin/simpanan/pokok/toggle',{
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'id_simpanan='+encodeURIComponent(id)+'&status='+encodeURIComponent(status)
      }).then(r=>r.json()).then(j=>{ load(); });
    }
    load();
  })();
</script>
