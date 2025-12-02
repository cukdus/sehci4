<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Data Simpanan</h3></div>
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
                  <h6>Total Pokok</h6>
                  <h3 class="fw-bold mb-0" id="sumPokok">Rp 0,-</h3>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
              <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                  <h6>Total Wajib</h6>
                  <h3 class="fw-bold mb-0" id="sumWajib">Rp 0,-</h3>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
              <div class="card shadow-sm border-0 bg-warning text-dark">
                <div class="card-body">
                  <h6>Total Sukarela</h6>
                  <h3 class="fw-bold mb-0" id="sumSukarela">Rp 0,-</h3>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap">
                  <thead class="table-light">
                    <tr>
                      <th>No</th>
                      <th>Tanggal Simpan</th>
                      <th>Jenis</th>
                      <th>No Anggota</th>
                      <th>Nama</th>
                      <th>Jumlah</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="rows"></tbody>
                  <tfoot>
                    <tr>
                      <th colspan="5" class="text-end">Total</th>
                      <th id="total"></th>
                      <th></th>
                    </tr>
                  </tfoot>
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
    const sumPokokEl = document.getElementById('sumPokok');
    const sumWajibEl = document.getElementById('sumWajib');
    const sumSukarelaEl = document.getElementById('sumSukarela');
    const totalEl = document.getElementById('total');
    function fmt(n){
      const s = new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n||0);
      return s.endsWith(',00') ? s.slice(0,-3)+',-' : s;
    }
    function fmtDate(d){if(!d)return '-';var m=d.match(/^(\d{4})-(\d{2})-(\d{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function labelJenis(j){if(!j)return '-';var map={pokok:'Simpanan Pokok',wajib:'Simpanan Wajib',sukarela:'Simpanan Sukarela'};return map[j]||j;}
    function badgeClass(st){var v=(st||'').toLowerCase(); if(v==='aktif') return 'text-bg-primary'; if(v==='pending') return 'text-bg-warning'; return 'text-bg-secondary';}
    function load(){
      fetch('/admin/api/simpanan/data?page='+page)
        .then(r=>r.json())
        .then(j=>{
          const data = j.data||[]; const meta = j.meta||{};
          rowsEl.innerHTML = '';
          const start = (meta.page-1)*perPage;
          data.forEach((s,i)=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${start+i+1}</td>
              <td>${fmtDate(s.tanggal_simpan)}</td>
              <td>${labelJenis(s.jenis_simpanan)}</td>
              <td>${s.no_anggota||'-'}</td>
              <td>${s.nama||'-'}</td>
              <td>Rp ${fmt(parseFloat(s.jumlah||0))}</td>
              <td><span class="badge ${badgeClass(s.status)}">${s.status||'-'}</span></td>
            `;
            rowsEl.appendChild(tr);
          });
          totalEl.textContent = 'Rp ' + fmt(parseFloat(meta.sumAll||0));
          pageInfo.textContent = `Halaman ${meta.page||1} dari ${meta.totalPages||1}`;
          prevBtn.disabled = (meta.page||1) <= 1;
          nextBtn.disabled = (meta.page||1) >= (meta.totalPages||1);
        });
    }
    function loadSummary(){
      fetch('/admin/api/simpanan/summary')
        .then(r=>r.json())
        .then(s=>{
          if(sumPokokEl) sumPokokEl.textContent = 'Rp ' + fmt(parseFloat(s.sumPokok||0));
          if(sumWajibEl) sumWajibEl.textContent = 'Rp ' + fmt(parseFloat(s.sumWajib||0));
          if(sumSukarelaEl) sumSukarelaEl.textContent = 'Rp ' + fmt(parseFloat(s.sumSukarela||0));
        });
    }
    prevBtn.addEventListener('click',()=>{ if(page>1){ page--; load(); } });
    nextBtn.addEventListener('click',()=>{ page++; load(); });
    load();
    loadSummary();
  })();
</script>
