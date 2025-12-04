<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Aktivasi Akun</title>
    <link href="/assets/img/favicon.png" rel="icon" />
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="/assets/css/main.css" rel="stylesheet" />
  </head>
  <body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#f8f9fb;">
    <div class="card shadow-sm" style="border:0;border-radius:12px;max-width:480px;width:100%;">
      <div class="card-body p-4 p-md-5">
        <h2 class="mb-3 text-center">Aktivasi Akun</h2>
        <p class="text-center text-muted mb-4">Buat password untuk menyelesaikan aktivasi akun.</p>
        <?= session()->has('error') ? '<div class="alert alert-danger">' . esc(session('error')) . '</div>' : '' ?>
        <form action="/activate/<?= esc($token) ?>" method="post" novalidate>
          <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" id="password" name="password" class="form-control form-control-lg" required minlength="6">
          </div>
          <div class="mb-4">
            <label for="confirm" class="form-label">Konfirmasi Password</label>
            <input type="password" id="confirm" name="confirm" class="form-control form-control-lg" required minlength="6">
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">Simpan Password</button>
        </form>
        <div class="text-center mt-4">
          <a href="/login" class="text-decoration-none">Kembali ke Halaman Masuk</a>
        </div>
      </div>
    </div>

    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/main.js"></script>
  </body>
  </html>
