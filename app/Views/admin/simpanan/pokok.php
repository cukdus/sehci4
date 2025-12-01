<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Simpanan Pokok</h3>
        </div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-outline card-secondary">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-primary text-white">
                    <div class="card-body">
                    <h6>Total Transaksi</h6>
                    <h3 class="fw-bold mb-0" id="totalCount">0</h3>
                    </div>
                </div>
                </div>
                <div class="col-md-4">
                <div class="card shadow-sm border-0 bg-success text-white">
                    <div class="card-body">
                    <h6>Terbayar</h6>
                    <h3 class="fw-bold mb-0" id="paidCount">0</h3>
                    </div>
                </div>
                </div>
                <div class="col-md-4">
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
                  <div></div>
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
    function fmt(n){return new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n||0);}    
    function fmtDate(d){if(!d)return '-';var m=d.match(/^(\d{4})-(\d{2})-(\d{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function load(){
      fetch('/admin/api/simpanan/pokok?page='+page)
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
              <td><span class="badge text-bg-secondary">${s.status||'-'}</span></td>
              <td class="text-center">
                <button class="btn btn-sm btn-info me-1" title="Detail"><i class="bi bi-eye"></i></button>
              </td>
            `;
            rowsEl.appendChild(tr);
          });
          document.getElementById('totalCount').textContent = (meta.totalItems||0);
          document.getElementById('paidCount').textContent = (meta.paidCount||0);
          document.getElementById('unpaidCount').textContent = (meta.unpaidCount||0);
          pageInfo.textContent = `Halaman ${meta.page||1} dari ${meta.totalPages||1}`;
          prevBtn.disabled = (meta.page||1) <= 1;
          nextBtn.disabled = (meta.page||1) >= (meta.totalPages||1);
        });
    }
    prevBtn.addEventListener('click',()=>{ if(page>1){ page--; load(); } });
    nextBtn.addEventListener('click',()=>{ page++; load(); });
    load();
  })();
</script>
