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
          <div class="d-flex justify-content-end gap-2 mb-3 flex-wrap">
            <button type="button" class="btn btn-primary" id="sendWajibReminderBtn">
              Kirim Pengingat Simpanan Wajib
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#broadcastModal">
              Kirim Informasi ke Anggota Aktif
            </button>
          </div>
          <div class="alert alert-info">
            Pengingat simpanan wajib bisa dikirim manual dari tombol di atas atau otomatis melalui command <code>php spark waha:send-wajib-reminders</code>. Sistem tetap mengikuti tanggal, jam mulai, dan jeda 1 menit untuk setiap pesan, kecuali admin memilih paksa kirim.
          </div>
          <form id="formWaha" class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Tanggal Pengingat Simpanan Wajib</label>
              <input type="number" min="1" max="31" class="form-control" id="wajibReminderDay" name="wajib_reminder_day" placeholder="Contoh: 10">
              <small class="text-muted">Admin bisa mengatur tanggal kirim otomatis tiap bulan. Jika tanggal melebihi jumlah hari dalam bulan berjalan, sistem akan memakai tanggal terakhir bulan tersebut.</small>
            </div>
            <div class="col-md-4">
              <label class="form-label">Jam Mulai Pengingat Simpanan Wajib</label>
              <input type="time" class="form-control" id="wajibReminderStartTime" name="wajib_reminder_start_time">
              <small class="text-muted">Pesan mulai dikirim pada jam ini, lalu sistem memberi jeda 1 menit untuk setiap pesan berikutnya.</small>
            </div>
            <div class="col-12">
              <label class="form-label">Template Pengingat Pembayaran Simpanan Wajib</label>
              <textarea class="form-control" id="tplWajibReminder" name="wajib_reminder" rows="4" placeholder="Contoh: Halo {{nama}} ({{no_anggota}}), ini pengingat pembayaran simpanan wajib bulan {{bulan}} sebesar Rp {{jumlah_wajib}}. Mohon lakukan pembayaran sebelum {{tanggal_pengingat}}."></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{no_anggota}}, {{bulan}}, {{jumlah_wajib}}, {{tanggal_pengingat}}, {{status}}</small>
            </div>
            <div class="col-12">
              <label class="form-label">Template Pesan Registrasi</label>
              <textarea class="form-control" id="tplRegister" name="register" rows="4" placeholder="Contoh: Halo {{nama}}, silakan aktivasi akun: {{link}}"></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{no_anggota}}, {{link}}</small>
            </div>
            <div class="col-12">
              <label class="form-label">Template Pesan Daftar</label>
              <textarea class="form-control" id="tplDaftar" name="daftar" rows="4" placeholder="Contoh: Halo {{nama}} ({{no_anggota}}), biaya awal: Pokok Rp {{biaya_pokok}}, Wajib Rp {{biaya_wajib}}. Total Rp {{total}}."></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{no_anggota}}, {{biaya_pokok}}, {{biaya_wajib}}, {{total}}</small>
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
            <div class="col-12">
              <label class="form-label">Template Pesan Status Anggota</label>
              <textarea class="form-control" id="tplStatusAnggota" name="status_anggota" rows="4" placeholder="Contoh: Halo {{nama}} ({{no_anggota}}), status keanggotaan anda: {{status}}."></textarea>
              <small class="text-muted">Variabel: {{nama}}, {{no_anggota}}, {{status}}</small>
            </div>
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
        </div>
      </div>
      <div class="card card-outline card-secondary mt-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Riwayat Job Pengingat Simpanan Wajib</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 140px;">Waktu</th>
                  <th style="width: 140px;">Pengirim</th>
                  <th style="width: 90px;">Mode</th>
                  <th style="width: 70px;">Force</th>
                  <th style="width: 110px;">Status</th>
                  <th style="width: 80px;">Target</th>
                  <th style="width: 80px;">Kirim</th>
                  <th style="width: 80px;">Gagal</th>
                  <th style="width: 80px;">Lewat</th>
                  <th>Pesan</th>
                  <th style="width: 120px;">Aksi</th>
                </tr>
              </thead>
              <tbody id="reminderJobHistoryBody">
                <tr>
                  <td colspan="11" class="text-center text-muted">Memuat riwayat job...</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="text-muted mt-2" style="font-size: 13px;">
            Agar job diproses otomatis, jalankan worker <code>php spark waha:process-reminder-jobs</code> via scheduler (mis. tiap 1 menit).
          </div>
        </div>
      </div>

      <div class="card card-outline card-secondary mt-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Riwayat Broadcast WhatsApp</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle mb-0">
              <thead>
                <tr>
                  <th style="width: 140px;">Waktu</th>
                  <th style="width: 160px;">Pengirim</th>
                  <th style="width: 180px;">Judul</th>
                  <th>Pesan</th>
                  <th style="width: 80px;">Target</th>
                  <th style="width: 80px;">Kirim</th>
                  <th style="width: 80px;">Gagal</th>
                  <th style="width: 80px;">Lewat</th>
                  <th style="width: 100px;">Status</th>
                  <th style="width: 90px;">Aksi</th>
                </tr>
              </thead>
              <tbody id="broadcastHistoryBody">
                <tr>
                  <td colspan="10" class="text-center text-muted">Memuat riwayat broadcast...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<div class="modal fade" id="sendWajibReminderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Buat Job Pengingat Simpanan Wajib</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="sendWajibReminderForm">
        <div class="modal-body">
          <div id="sendWajibReminderAlert"></div>
          <div class="alert alert-warning">
            Pengiriman memakai template pengingat simpanan wajib untuk anggota aktif yang memiliki nomor anggota, dan sistem memberi jeda 1 menit pada setiap pesan.
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="forceWajibReminder" name="force">
            <label class="form-check-label" for="forceWajibReminder">
              Paksa kirim sekarang walau belum sesuai tanggal atau jam pengingat
            </label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary" id="sendWajibReminderSubmit">Buat Job</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="modal fade" id="broadcastModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Kirim Informasi WhatsApp</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="broadcastForm">
        <div class="modal-body">
          <div id="broadcastAlert"></div>
          <div class="mb-3">
            <label class="form-label">Judul Informasi</label>
            <input type="text" class="form-control" id="broadcastTitle" name="title" placeholder="Contoh: Informasi Kegiatan Koperasi">
          </div>
          <div class="mb-3">
            <label class="form-label">Isi Pesan</label>
            <textarea class="form-control" id="broadcastMessage" name="message" rows="6" placeholder="Tulis berita atau informasi yang ingin dikirim ke semua anggota aktif."></textarea>
            <small class="text-muted">Variabel yang bisa dipakai: {{nama}}, {{no_anggota}}, {{status}}</small>
          </div>
          <div class="alert alert-warning mb-0">
            Pesan ini akan dikirim manual ke semua anggota dengan status aktif yang memiliki nomor telepon. Sistem memberi jeda 1 menit untuk setiap pesan yang benar-benar dikirim.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success" id="broadcastSubmit">Kirim Pesan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    const f = document.getElementById('formWaha');
    const bf = document.getElementById('broadcastForm');
    const swrBtn = document.getElementById('sendWajibReminderBtn');
    const swrForm = document.getElementById('sendWajibReminderForm');
    const swrAlert = document.getElementById('sendWajibReminderAlert');
    const swrSubmit = document.getElementById('sendWajibReminderSubmit');
    const ba = document.getElementById('broadcastAlert');
    const bs = document.getElementById('broadcastSubmit');
    const bh = document.getElementById('broadcastHistoryBody');
    const rjh = document.getElementById('reminderJobHistoryBody');
    const rd = document.getElementById('wajibReminderDay');
    const rt = document.getElementById('wajibReminderStartTime');
    const wr = document.getElementById('tplWajibReminder');
    const r = document.getElementById('tplRegister');
    const d = document.getElementById('tplDaftar');
    const w = document.getElementById('tplWajib');
    const s = document.getElementById('tplSukarela');
    const fg = document.getElementById('tplForgot');
    const sta = document.getElementById('tplStatusAnggota');
    const swrModal = new bootstrap.Modal(document.getElementById('sendWajibReminderModal'));
    function escHtml(value) {
      return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function loadBroadcastHistory() {
      bh.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Memuat riwayat broadcast...</td></tr>';
      fetch('/admin/api/setting/waha/broadcast-history')
        .then(async (x) => {
          const j = await x.json();
          if (!x.ok) throw j;
          return j;
        })
        .then((j) => {
          const items = Array.isArray(j.items) ? j.items : [];
          if (!items.length) {
            bh.innerHTML = '<tr><td colspan="10" class="text-center text-muted">Belum ada riwayat broadcast.</td></tr>';
            return;
          }
          bh.innerHTML = items.map((item) => {
            const message = escHtml(item.message || '');
            const shortMessage = message.length > 160 ? message.slice(0, 160) + '...' : message;
            return '<tr>'
              + '<td>' + escHtml(item.created_at || '-') + '</td>'
              + '<td>' + escHtml(item.created_by || '-') + '</td>'
              + '<td>' + escHtml(item.title || '-') + '</td>'
              + '<td title="' + message + '">' + shortMessage + '</td>'
              + '<td>' + escHtml(item.total_target || 0) + '</td>'
              + '<td>' + escHtml(item.sent_count || 0) + '</td>'
              + '<td>' + escHtml(item.failed_count || 0) + '</td>'
              + '<td>' + escHtml(item.skipped_count || 0) + '</td>'
              + '<td>' + escHtml(item.status || '-') + '</td>'
              + '<td><a href="/admin/setting/waha/broadcast/' + escHtml(item.id || '') + '" class="btn btn-sm btn-primary">Detail</a></td>'
              + '</tr>';
          }).join('');
        })
        .catch(() => {
          bh.innerHTML = '<tr><td colspan="10" class="text-center text-danger">Gagal memuat riwayat broadcast.</td></tr>';
        });
    }

    function loadReminderJobHistory() {
      rjh.innerHTML = '<tr><td colspan="11" class="text-center text-muted">Memuat riwayat job...</td></tr>';
      fetch('/admin/api/setting/waha/reminder-jobs')
        .then(async (x) => {
          const j = await x.json();
          if (!x.ok) throw j;
          return j;
        })
        .then((j) => {
          const items = Array.isArray(j.items) ? j.items : [];
          if (!items.length) {
            rjh.innerHTML = '<tr><td colspan="11" class="text-center text-muted">Belum ada job.</td></tr>';
            return;
          }
          rjh.innerHTML = items.map((item) => {
            const msg = escHtml(item.message || '');
            const shortMsg = msg.length > 160 ? msg.slice(0, 160) + '...' : msg;
            const canRetry = (item.mode || '') !== 'failed';
            const retryBtn = canRetry
              ? '<button type="button" class="btn btn-sm btn-warning reminder-retry-btn" data-period="' + escHtml(item.period || '') + '">Retry Failed</button>'
              : '<span class="text-muted">-</span>';

            return '<tr>'
              + '<td>' + escHtml(item.created_at || '-') + '</td>'
              + '<td>' + escHtml(item.created_by || '-') + '</td>'
              + '<td>' + escHtml(item.mode || '-') + '</td>'
              + '<td>' + (String(item.is_force || '0') === '1' ? 'Ya' : 'Tidak') + '</td>'
              + '<td>' + escHtml(item.status || '-') + '</td>'
              + '<td>' + escHtml(item.total_target || 0) + '</td>'
              + '<td>' + escHtml(item.sent_count || 0) + '</td>'
              + '<td>' + escHtml(item.failed_count || 0) + '</td>'
              + '<td>' + escHtml(item.skipped_count || 0) + '</td>'
              + '<td title="' + msg + '">' + shortMsg + '</td>'
              + '<td>' + retryBtn + '</td>'
              + '</tr>';
          }).join('');
        })
        .catch(() => {
          rjh.innerHTML = '<tr><td colspan="11" class="text-center text-danger">Gagal memuat riwayat job.</td></tr>';
        });
    }

    fetch('/admin/api/setting/waha').then(x=>x.json()).then(j=>{
      rd.value = j.wajib_reminder_day || '10';
      rt.value = j.wajib_reminder_start_time || '08:00';
      wr.value = j.wajib_reminder || '';
      r.value = j.register||''; d.value = j.daftar||''; w.value = j.wajib||''; s.value = j.sukarela||''; fg.value = j.forgot||''; sta.value = j.status_anggota||'';
    });
    loadReminderJobHistory();
    loadBroadcastHistory();
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

    swrBtn.addEventListener('click', function(){
      swrForm.reset();
      swrAlert.innerHTML = '';
      swrModal.show();
    });

    swrForm.addEventListener('submit', function(e){
      e.preventDefault();
      swrAlert.innerHTML = '';
      swrSubmit.disabled = true;
      swrSubmit.textContent = 'Memproses...';
      const fd = new FormData(swrForm);
      fetch('/admin/api/setting/waha/send-wajib-reminder', { method:'POST', body: fd })
        .then(async (x) => {
          const j = await x.json();
          if (!x.ok) throw j;
          return j;
        })
        .then((j) => {
          const lines = [];
          if (j.message) lines.push(j.message);
          lines.push('ID Job: ' + (j.job_id || '-'));
          lines.push('Status: ' + (j.status || '-'));
          lines.push('Mode: ' + (j.mode || '-'));
          lines.push('Periode: ' + (j.period || '-'));
          lines.push('');
          lines.push('Catatan: job akan diproses oleh worker background dan tidak akan mengulang nomor yang sudah sukses terkirim.');
          swrAlert.innerHTML = '<div class="alert ' + (j.ok ? 'alert-success' : 'alert-warning') + ' mb-0" style="white-space: pre-line;">' + escHtml(lines.join('\n')) + '</div>';
          loadReminderJobHistory();
        })
        .catch((err) => {
          const msg = err && (err.error || err.message) ? (err.error || err.message) : 'Gagal menjalankan pengingat simpanan wajib';
          swrAlert.innerHTML = '<div class="alert alert-danger mb-0">' + escHtml(msg) + '</div>';
        })
        .finally(() => {
          swrSubmit.disabled = false;
          swrSubmit.textContent = 'Buat Job';
        });
    });

    document.addEventListener('click', function(e){
      const btn = e.target && e.target.classList ? (e.target.classList.contains('reminder-retry-btn') ? e.target : null) : null;
      if (!btn) return;
      const period = btn.getAttribute('data-period') || '';
      btn.disabled = true;
      const fd = new FormData();
      fd.append('period', period);
      fetch('/admin/api/setting/waha/reminder-jobs/retry-failed', { method:'POST', body: fd })
        .then(async (x) => {
          const j = await x.json();
          if (!x.ok) throw j;
          return j;
        })
        .then(() => {
          loadReminderJobHistory();
        })
        .catch(() => {
          loadReminderJobHistory();
        })
        .finally(() => {
          btn.disabled = false;
        });
    });

    setInterval(loadReminderJobHistory, 10000);

    bf.addEventListener('submit', function(e){
      e.preventDefault();
      ba.innerHTML = '';
      bs.disabled = true;
      bs.textContent = 'Mengirim...';
      const fd = new FormData(bf);
      fetch('/admin/api/setting/waha/broadcast', { method:'POST', body: fd })
        .then(async (x) => {
          const j = await x.json();
          if (!x.ok) throw j;
          return j;
        })
        .then((j) => {
          const lines = [
            'Pesan berhasil diproses.',
            'ID Broadcast: ' + (j.broadcast_id || '-'),
            'Terkirim: ' + (j.sent || 0),
            'Gagal: ' + (j.failed || 0),
            'Dilewati: ' + (j.skipped || 0)
          ];
          if (Array.isArray(j.errors) && j.errors.length) {
            lines.push('Contoh error: ' + j.errors.map(x => (x.nama || '-') + ' (' + (x.phone || '-') + ')').join(', '));
          }
          ba.innerHTML = '<div class="alert alert-success mb-0">' + lines.join('<br>') + '</div>';
          loadBroadcastHistory();
        })
        .catch((err) => {
          const msg = err && err.error ? err.error : 'Gagal mengirim pesan broadcast';
          ba.innerHTML = '<div class="alert alert-danger mb-0">' + msg + '</div>';
        })
        .finally(() => {
          bs.disabled = false;
          bs.textContent = 'Kirim Pesan';
        });
    });
  });
</script>
