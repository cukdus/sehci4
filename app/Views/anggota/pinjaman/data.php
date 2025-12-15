<main class="app-main" id="main" tabindex="-1">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 d-flex align-items-center">
          <h3 class="mb-0">Data Pinjaman</h3>
          <a href="/anggota/pinjaman/ajukan" class="btn btn-primary btn-sm ms-auto">Ajukan Pinjaman</a>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <?php if (session()->has('success')): ?>
        <div class="alert alert-success"><?= esc(session('success')) ?></div>
      <?php endif; ?>
      <?php if (session()->has('error')): ?>
        <div class="alert alert-danger"><?= esc(session('error')) ?></div>
      <?php endif; ?>
      <div class="row">
        <div class="col-lg-4 col-12">
          <div class="small-box text-bg-danger">
            <div class="inner">
              <h3 id="sumPinjaman">Rp 0,-</h3>
              <p>Total Pinjaman</p>
            </div>
            <i class="bi bi-credit-card-2-front small-box-icon" aria-hidden="true"></i>
            <a href="#" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
              Ringkasan <i class="bi bi-link-45deg"></i>
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
                  <th>Tgl Pinjam</th>
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
    
    const rowsEl = document.getElementById('rows');
    const totalEl = document.getElementById('total');
    const sumPinjamanEl = document.getElementById('sumPinjaman');
    function fmt(n){
      const s = new Intl.NumberFormat('id-ID',{minimumFractionDigits:2,maximumFractionDigits:2}).format(n||0);
      return s.endsWith(',00') ? s.slice(0,-3)+',-' : s;
    }
    function fmtDate(d){if(!d)return '-';var m=d.match(/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function badgeClass(st){var v=(st||'').toLowerCase(); if(v==='proses') return 'text-bg-warning'; if(v==='aktif') return 'text-bg-primary'; if(v==='lunas') return 'text-bg-success'; if(v==='menunggak') return 'text-bg-danger'; return 'text-bg-secondary';}
    function load(){
      fetch('/anggota/api/pinjaman?all=1')
        .then(r=>r.json())
        .then(j=>{
          const data = j.data||[]; const meta = j.meta||{};
          rowsEl.innerHTML = '';
          data.forEach((p,i)=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${i+1}</td>
              <td>${fmtDate(p.tanggal_pinjam)}</td>
              <td>Rp ${fmt(parseFloat(p.jumlah_pinjaman||0))}</td>
              <td><span class="badge ${badgeClass(p.status)}">${p.status||'-'}</span></td>
            `;
            rowsEl.appendChild(tr);
          });
          totalEl.textContent = 'Rp ' + fmt(parseFloat(meta.sumAll||0));
          const btn = document.querySelector('.app-content-header a.btn');
          if (btn) btn.style.display = ((meta.totalItems||0) === 0) ? '' : '';
        });
    }
    function loadSummary(){
      fetch('/anggota/api/simpanan/summary')
        .then(r=>r.json())
        .then(s=>{
          if(sumPinjamanEl) sumPinjamanEl.textContent = 'Rp ' + fmt(parseFloat(s.sumPinjaman||0));
        });
    }
    load();
    loadSummary();
  })();
</script>
