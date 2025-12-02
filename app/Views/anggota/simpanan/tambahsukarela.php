<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Tambah Simpanan Sukarela</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-primary card-outline">
        <div class="card-body">
          <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success" role="alert"><?= session()->getFlashdata('success') ?></div>
          <?php endif; ?>
          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger" role="alert"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>
          <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-info" role="alert"><?= session()->getFlashdata('message') ?></div>
          <?php endif; ?>
          <?php if (session()->getFlashdata('warning')): ?>
            <div class="alert alert-warning" role="alert"><?= session()->getFlashdata('warning') ?></div>
          <?php endif; ?>
          <div class="text-muted mb-2">ID Anggota: <?= esc(session()->get('user')['id_anggota'] ?? 'tidak tersedia') ?></div>

          <form method="post" action="/anggota/simpanan/sukarela/tambah" novalidate>
            <?= csrf_field() ?>
            <input type="hidden" name="id_anggota" value="<?= esc(session()->get('user')['id_anggota'] ?? '') ?>">
            <div class="row g-3">
              <div class="col-md-4">
                <label for="tanggal_simpan_sukarela_display" class="form-label">Tanggal Simpan</label>
                <input type="hidden" id="tanggal_simpan_sukarela_value" name="tanggal_simpan" value="<?= date('Y-m-d') ?>">
                <input type="text" class="form-control" id="tanggal_simpan_sukarela_display" readonly>
              </div>
              <div class="col-md-4">
                <label for="jumlah_sukarela" class="form-label">Jumlah</label>
                <input type="number" step="0.01" min="0" class="form-control" id="jumlah_sukarela" name="jumlah" placeholder="0" required>
              </div>
              <div class="col-md-4">
                <label for="tipe_sukarela" class="form-label">Tipe Simpanan</label>
                <select class="form-select" id="tipe_sukarela" name="tipe_sukarela" required>
                  <option value="" selected disabled>Pilih tipe</option>
                  <option value="biasa">Biasa</option>
                  <option value="berjangka">Berjangka</option>
                </select>
              </div>
              <div class="col-md-4" id="jangka_waktu_group" style="display:none;">
                <label for="jangka_waktu_sukarela" class="form-label">Jangka Waktu (bulan)</label>
                <input type="number" min="1" class="form-control" id="jangka_waktu_sukarela" name="jangka_waktu" placeholder="0">
                <div class="invalid-feedback" id="errJangka">Jangka waktu wajib diisi</div>
              </div>
              <div class="col-md-4" id="jatuh_tempo_group" style="display:none;">
                <label for="tanggal_jatuh_tempo_sukarela_display" class="form-label">Tanggal Jatuh Tempo</label>
                <input type="text" class="form-control" id="tanggal_jatuh_tempo_sukarela_display" readonly>
              </div>
            </div>
            <div class="mt-4 d-flex gap-2">
              <a href="/anggota/simpanan/sukarela" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
  (function(){
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
    const form = document.querySelector('form[action="/anggota/simpanan/sukarela/tambah"]');
    if(form){
      form.addEventListener('submit', function(e){
        let ok = true;
        const jumlahEl = document.getElementById('jumlah_sukarela');
        const tipeEl = document.getElementById('tipe_sukarela');
        const jangkaEl = document.getElementById('jangka_waktu_sukarela');
        const vJumlah = parseFloat((jumlahEl?.value||'').replace(/[,\s]/g,''));
        if(!jumlahEl || isNaN(vJumlah) || vJumlah<=0){
          ok = false; if(jumlahEl){ jumlahEl.classList.add('is-invalid'); }
        } else { jumlahEl.classList.remove('is-invalid'); }
        if(!tipeEl || !tipeEl.value){
          ok = false; if(tipeEl){ tipeEl.classList.add('is-invalid'); }
        } else { tipeEl.classList.remove('is-invalid'); }
        if(tipeEl && tipeEl.value==='berjangka'){
          const vJ = parseInt(jangkaEl?.value||'0',10);
          if(!jangkaEl || !vJ || vJ<=0){ ok=false; if(jangkaEl){ jangkaEl.classList.add('is-invalid'); } }
          else { jangkaEl.classList.remove('is-invalid'); }
        } else { if(jangkaEl){ jangkaEl.classList.remove('is-invalid'); } }
        if(!ok){ e.preventDefault(); }
      });
    }
  })();
</script>
