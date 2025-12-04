<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Setting Template WAHA</h3></div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-outline card-secondary">
        <div class="card-body">
          <form id="formWaha" class="row g-3">
            <div class="col-12">
              <label class="form-label">Template Pesan Registrasi</label>
              <textarea class="form-control" id="tplRegister" name="register" rows="4" placeholder="Contoh: Halo {{nama}}, silakan aktivasi akun: {{link}}"></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{no_anggota}}, {{link}}</small>
            </div>
            <div class="col-12">
              <label class="form-label">Template Pesan Simpanan Wajib</label>
              <textarea class="form-control" id="tplWajib" name="wajib" rows="4" placeholder="Contoh: {{nama}} telah menyimpan wajib tanggal {{tanggal}} sebesar Rp {{jumlah}}"></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{tanggal}}, {{jumlah}}, {{status}}</small>
            </div>
            <div class="col-12">
              <label class="form-label">Template Pesan Simpanan Sukarela</label>
              <textarea class="form-control" id="tplSukarela" name="sukarela" rows="4" placeholder="Contoh: {{nama}} menyimpan sukarela tipe {{tipe}} tanggal {{tanggal}} Rp {{jumlah}}"></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{tipe}}, {{tanggal}}, {{jumlah}}, {{status}}</small>
            </div>
            <div class="col-12">
              <label class="form-label">Template Pesan Lupa Password</label>
              <textarea class="form-control" id="tplForgot" name="forgot" rows="4" placeholder="Contoh: {{nama}}, reset password anda: {{link}}"></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{link}}</small>
            </div>
            <div class="col-12 text-end">
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
    const f = document.getElementById('formWaha');
    const r = document.getElementById('tplRegister');
    const w = document.getElementById('tplWajib');
    const s = document.getElementById('tplSukarela');
    const fg = document.getElementById('tplForgot');
    fetch('/admin/api/setting/waha').then(x=>x.json()).then(j=>{
      r.value = j.register||''; w.value = j.wajib||''; s.value = j.sukarela||''; fg.value = j.forgot||'';
    });
    f.addEventListener('submit', function(e){
      e.preventDefault();
      const fd = new FormData(f);
      fetch('/admin/api/setting/waha', { method:'POST', body: fd }).then(x=>x.json()).then(j=>{
        const ok = j && (j.ok===true);
        const alert = document.createElement('div');
        alert.className = 'alert ' + (ok?'alert-success':'alert-danger');
        alert.textContent = ok ? 'Template berhasil disimpan' : 'Gagal menyimpan template';
        f.parentElement.insertBefore(alert, f);
        setTimeout(()=>{ alert.remove(); }, 3000);
      });
    });
  })();
</script>
