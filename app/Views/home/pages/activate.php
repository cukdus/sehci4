<main class="main">
  <div class="page-title" data-aos="fade">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1>Aktivasi Akun</h1>
            <p class="mb-0">Buat password untuk menyelesaikan aktivasi akun anda.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section id="activate-section" class="section">
    <div class="container" data-aos="fade-up">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card p-4 shadow-sm">
            <h3 class="mb-3">Buat Password</h3>
            <?= session()->has('error') ? '<div class="alert alert-danger">' . esc(session('error')) . '</div>' : '' ?>
            <form action="/activate/<?= esc($token) ?>" method="post" novalidate>
              <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control" required minlength="6">
              </div>
              <div class="mb-3">
                <label for="confirm" class="form-label">Konfirmasi Password</label>
                <input type="password" id="confirm" name="confirm" class="form-control" required minlength="6">
              </div>
              <div class="text-end">
                <button type="submit" class="btn-standard">Simpan Password</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
