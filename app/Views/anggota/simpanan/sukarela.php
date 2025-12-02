<main class="app-main">
  <div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center">
      <h3 class="mb-0">Daftar Simpanan Sukarela</h3>
        <a href="/anggota/simpanan/sukarela/tambah" class="btn btn-sm btn-primary">
          <i class="bi bi-plus-lg"></i> Tambah Simpanan
        </a>
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

                <th>
                  <select class="form-select form-select-sm" id="filterTipeSukarela">
                    <option value="">Semua</option>
                    <option value="biasa">Biasa</option>
                    <option value="berjangka">Berjangka</option>
                  </select>
                </th>

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
      const tipe = (document.getElementById('filterTipeSukarela')?.value||'');
      const apiBase = '/anggota/api/simpanan/sukarela';
      const q = apiBase + '?page='+page + (tipe?('&tipe='+encodeURIComponent(tipe)):'');
      fetch(q)
        .then(r=>r.json())
        .then(j=>{
          const data = j.data||[]; const meta = j.meta||{};
          rowsEl.innerHTML = '';
          const hdr = document.createElement('tr');
          hdr.innerHTML = `
            <th>Nomor</th>
            <th>Tanggal Simpan</th>
            <th>Tipe Simpanan</th>
            <th>Jangka Waktu</th>
            <th>Jatuh Tempo</th>
            <th>Jumlah</th>
            <th>Status</th>
          `;
          rowsEl.appendChild(hdr);
          const start = (meta.page-1)*perPage;
          data.forEach((s,i)=>{
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${start+i+1}</td>
              <td>${fmtDate(s.tanggal_simpan)}</td>
              <td>${s.tipe_sukarela||'-'}</td>
              <td>${s.jangka_waktu? (s.jangka_waktu+' bln') : '-'}</td>
              <td>${fmtDate(s.tanggal_jatuh_tempo)}</td>
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
    const dDisplay = document.getElementById('tanggal_simpan_sukarela_display');
    const dValue = document.getElementById('tanggal_simpan_sukarela_value');
    const tipeFormSel = document.getElementById('tipe_sukarela');
    const jangkaGroup = document.getElementById('jangka_waktu_group');
    const jatuhGroup = document.getElementById('jatuh_tempo_group');
    const jangkaInput = document.getElementById('jangka_waktu_sukarela');
    const jatuhDisplay = document.getElementById('tanggal_jatuh_tempo_sukarela_display');
    if(dDisplay){
      const d = new Date();
      dDisplay.value = d.toLocaleDateString('id-ID',{day:'numeric', month:'long', year:'numeric'});
    }
    function updateBerjangkaUI(){
      if(tipeFormSel && tipeFormSel.value==='berjangka'){
        jangkaGroup.style.display='';
        jatuhGroup.style.display='';
        if(jangkaInput){ jangkaInput.required=true; }
        computeJatuhTempo();
      } else {
        jangkaGroup.style.display='none';
        jatuhGroup.style.display='none';
        if(jangkaInput){ jangkaInput.required=false; }
        if(jatuhDisplay){ jatuhDisplay.value=''; }
      }
    }
    function computeJatuhTempo(){
      const base = dValue ? dValue.value : '';
      const months = parseInt(jangkaInput ? (jangkaInput.value||'0') : '0',10);
      if(!base || !months || months<=0){ if(jatuhDisplay){ jatuhDisplay.value=''; } return; }
      var m = base.match(/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/);
      var dt;
      if(m){ dt = new Date(parseInt(m[1],10), parseInt(m[2],10)-1, parseInt(m[3],10)); }
      else { dt = new Date(base); }
      if(isNaN(dt.getTime())){ if(jatuhDisplay){ jatuhDisplay.value=''; } return; }
      dt.setMonth(dt.getMonth()+months);
      if(jatuhDisplay){ jatuhDisplay.value = dt.toLocaleDateString('id-ID',{day:'numeric', month:'long', year:'numeric'}); }
    }
    if(tipeFormSel){ tipeFormSel.addEventListener('change', updateBerjangkaUI); updateBerjangkaUI(); }
    if(jangkaInput){ jangkaInput.addEventListener('input', computeJatuhTempo); }
    prevBtn.addEventListener('click',()=>{ if(page>1){ page--; load(); } });
    nextBtn.addEventListener('click',()=>{ page++; load(); });
    const tipeSel = document.getElementById('filterTipeSukarela');
    if(tipeSel){ tipeSel.addEventListener('change',()=>{ page=1; load(); }); }
    load();
  })();
</script>
