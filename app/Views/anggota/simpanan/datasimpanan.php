<main class="app-main" id="main" tabindex="-1">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12"><h3 class="mb-0">Data Simpanan</h3></div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-4 col-12">
          <div class="small-box text-bg-primary">
            <div class="inner">
              <h3 id="sumPokok">Rp 0,-</h3>
              <p>Simpanan Pokok</p>
            </div>
            <i class="bi bi-piggy-bank small-box-icon" aria-hidden="true"></i>
            <a
              href="#"
              class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
            >
              More info <i class="bi bi-link-45deg"></i>
            </a>
          </div>
        </div>
        <div class="col-lg-4 col-12">
          <div class="small-box text-bg-success">
            <div class="inner">
              <h3 id="sumWajib">Rp 0,-</h3>
              <p>Simpanan Wajib</p>
            </div>
            <i class="bi bi-cash-coin small-box-icon" aria-hidden="true"></i>
            <a
              href="#"
              class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover"
            >
              More info <i class="bi bi-link-45deg"></i>
            </a>
          </div>
        </div>
        <div class="col-lg-4 col-12">
          <div class="small-box text-bg-warning">
            <div class="inner">
              <h3 id="sumSukarela">Rp 0,-</h3>
              <p>Simpanan Sukarela</p>
            </div>
            <i class="bi bi-coin small-box-icon" aria-hidden="true"></i>
            <a
              href="#"
              class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover"
            >
              More info <i class="bi bi-link-45deg"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="card card-primary card-outline">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Nomor</th>
                <th>Tgl Tsx</th>
                <th>Jenis</th>
                <th>Jumlah</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="rows"></tbody>
            <tfoot>
              <tr>
                <th colspan="2" class="text-end">Total</th>
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
    <div class="mt-3">
      <a href="/anggota" class="btn btn-secondary">Kembali</a>
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
    const totalEl = document.getElementById('total');
    const sumPokokEl = document.getElementById('sumPokok');
    const sumWajibEl = document.getElementById('sumWajib');
    const sumSukarelaEl = document.getElementById('sumSukarela');
    function fmt(n){
      const s = new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n||0);
      return s.endsWith(',00') ? s.slice(0,-3)+',-' : s;
    }
    function fmtDate(d){if(!d)return '-';var m=d.match(/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function labelJenis(j){
      if(!j) return '-';
      const map = {pokok:'Simpanan Pokok', wajib:'Simpanan Wajib', sukarela:'Simpanan Sukarela'};
      return map[j]||j;
    }
    function load(){
      fetch('/anggota/api/simpanan/data?page='+page)
        .then(r=>r.json())
        .then(j=>{
          const data = j.data||[]; const meta = j.meta||{};
          rowsEl.innerHTML = '';
          data.forEach((s)=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${s.id_simpanan||'-'}</td>
              <td>${fmtDate(s.tanggal_simpan)}</td>
              <td>${labelJenis(s.jenis_simpanan)}</td>
              <td>Rp ${fmt(parseFloat(s.jumlah||0))}</td>
              <td><span class="badge text-bg-secondary">${s.status||'-'}</span></td>
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
      fetch('/anggota/api/simpanan/summary')
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
