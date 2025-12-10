<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Masuk</title>
    <link href="assets/img/favicon.png" rel="icon" />
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="assets/css/main.css" rel="stylesheet" />
  </head>
  <body class="d-flex align-items-center justify-content-center" style="min-height:100vh;background:#f8f9fb;">
    <div class="card shadow-sm" style="border:0;border-radius:12px;max-width:420px;width:100%;">
      <div class="card-body p-4 p-md-5">
        <h2 class="mb-4 text-center">Sign In</h2>
        <?= session()->has('message') ? '<div class="alert alert-info">' . session('message') . '</div>' : '' ?>
        <?php if (session()->has('error')): ?>
          <div class="alert alert-danger"><?= esc(session('error')) ?></div>
        <?php endif; ?>
        <?php if (session()->has('errors')): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ((array) session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form action="/login" method="post">
          <div class="mb-3">
            <label for="username" class="form-label">Nomer HP</label>
            <input type="text" id="username" name="username" class="form-control form-control-lg" value="<?= old('username') ?>" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-control form-control-lg" required>
          </div>
          <button type="submit" class="btn btn-primary btn-lg w-100">Masuk Boss</button>
        </form>

        <div class="text-center mt-4">
          <span class="text-muted">Belum jadi anggota?</span>
          <a href="/register" class="text-decoration-none">Daftar Disini</a>
        </div>
        <div class="text-center mt-2">
          <a href="/forgot" class="text-decoration-none">Lupa Password?</a>
        </div>
      </div>
    </div>

    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
  </body>
</html>
