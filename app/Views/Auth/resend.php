<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Kirim Ulang Link Aktivasi</title>
    <link href="/assets/img/favicon.png" rel="icon" />
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="/assets/css/main.css" rel="stylesheet" />
  </head>
  <body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#f8f9fb;">
    <div class="card shadow-sm" style="border:0;border-radius:12px;max-width:480px;width:100%;">
      <div class="card-body p-4 p-md-5">
        <h2 class="mb-3 text-center">Kirim Ulang Link Aktivasi</h2>
        <p class="text-center text-muted mb-4">Masukkan No. HP yang didaftarkan sebelumnya.</p>
        <?= session()->has('message') ? '<div class="alert alert-success text-center">' . esc(session('message')) . '</div>' : '' ?>
        <?php if (session()->has('error')): ?>
          <div class="alert alert-danger text-center"><?= esc(session('error')) ?></div>
        <?php endif; ?>
        <form action="<?= base_url('resend') ?>" method="post" novalidate>
          <div class="mb-3">
            <label for="username" class="form-label">No. HP (username)</label>
            <input type="text" id="username" name="username" class="form-control form-control-lg" placeholder="Contoh: 08123456789" value="<?= old('username') ?>" required>
            <div class="invalid-feedback">No. HP wajib diisi</div>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">Kirim</button>
        </form>
        <div class="text-center mt-4">
          <a href="/login" class="text-decoration-none">Kembali ke Halaman Masuk</a>
        </div>
      </div>
    </div>
    <script>
      (function(){
        var form = document.querySelector('form[action="<?= base_url('resend') ?>"]');
        if (!form) return;
        form.addEventListener('submit', function(e){
          var input = document.getElementById('username');
          input.classList.remove('is-invalid');
          if (!input.value) { e.preventDefault(); input.classList.add('is-invalid'); }
        });
      })();
    </script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
  </body>
</html>
