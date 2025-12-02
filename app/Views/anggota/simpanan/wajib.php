<main class="app-main">
  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center">
      <h3 class="mb-0">Daftar Simpanan Wajib</h3>
    </div>
  </div>
  <div class="container-fluid">
    <div class="card card-primary card-outline">
      <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success" role="alert"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger" role="alert"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>Nomor</th>
                <th>Tanggal Simpan</th>
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
</main>
<script>
  (function(){
    let page = 1; const perPage = 25;
    const rowsEl = document.getElementById('rows');
    const totalEl = document.getElementById('total');
    const pageInfo = document.getElementById('pageInfo');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');
    function fmt(n){var v = Number(n||0); var t = new Intl.NumberFormat('id-ID',{maximumFractionDigits:0}).format(v); return 'Rp ' + t + ',-';}
    function fmtDate(d){if(!d)return '-';var m=d.match(/^(\d{4})-(\d{2})-(\d{2})$/);if(m){return m[3]+'-'+m[2]+'-'+m[1];}try{var dt=new Date(d);var dd=('0'+dt.getDate()).slice(-2);var mm=('0'+(dt.getMonth()+1)).slice(-2);var yyyy=dt.getFullYear();return dd+'-'+mm+'-'+yyyy;}catch(e){return d;}}
    function badgeClass(st){var v=(st||'').toLowerCase(); if(v==='aktif') return 'text-bg-primary'; if(v==='pending') return 'text-bg-warning'; return 'text-bg-secondary';}
    function load(){
      fetch('/anggota/api/simpanan/wajib?page='+page)
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
              <td>${fmt(parseFloat(s.jumlah||0))}</td>
              <td><span class="badge ${badgeClass(s.status)}">${s.status||'-'}</span></td>
            `;
            rowsEl.appendChild(tr);
          });
          totalEl.textContent = fmt(parseFloat((meta.sumAll)||0));
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
