<main class="main">
  <div class="page-title aos-init aos-animate" data-aos="fade">
    <div class="heading">
      <div class="container">
        <div class="row d-flex justify-content-center text-center">
          <div class="col-lg-8">
            <h1>Daftar Anggota</h1>
            <p class="mb-0">
              Isi formulir berikut untuk mendaftar menjadi anggota Supporter
              Entrepreneur Hub.
            </p>
          </div>
        </div>
      </div>
    </div>
    <nav class="breadcrumbs">
      <div class="container">
        <ol>
          <li><a href="index.html">Home</a></li>
          <li class="current">Daftar Anggota</li>
        </ol>
      </div>
    </nav>
  </div>

  <section id="register-section" class="register-section section">
    <div class="container aos-init aos-animate" data-aos="fade-up">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="card p-4 shadow-sm">
            <h3 class="mb-3">Formulir Pendaftaran Anggota</h3>
            <?= session()->has('message') ? '<div class="alert alert-success">' . esc(session('message')) . '</div>' : '' ?>
            <?php if (session()->has('error')): ?>
              <div class="alert alert-danger"><?= esc(session('error')) ?></div>
            <?php endif; ?>
            <?php if (session()->has('errors')): ?>
              <div class="alert alert-warning">
                <ul class="mb-0">
                  <?php foreach ((array) session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>
            <form action="/register" method="post" novalidate="">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="fullName" class="form-label">Nama Lengkap</label>
                  <input
                    type="text"
                    id="fullName"
                    name="fullName"
                    class="form-control"
                    required=""
                  />
                  <div class="invalid-feedback">Nama wajib diisi</div>
                </div>

                <div class="col-md-6">
                  <label for="birthDate" class="form-label"
                    >Tanggal Lahir</label
                  >
                  <input
                    type="date"
                    id="birthDate"
                    name="birthDate"
                    class="form-control"
                    max="<?= date('Y-m-d', strtotime('-18 years')) ?>"
                    required=""
                  />
                  <div class="invalid-feedback">Tanggal lahir wajib diisi</div>
                </div>

                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    required=""
                  />
                  <div class="invalid-feedback">Email wajib diisi</div>
                </div>

                <div class="col-md-6">
                  <label for="phone" class="form-label">No. Telepon</label>
                  <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-control"
                    pattern="[0-9+\-() ]+"
                    required=""
                  />
                  <div class="invalid-feedback">No. telepon wajib diisi</div>
                </div>

                <div class="col-12">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      value="agree"
                      id="agree"
                      name="agree"
                      required=""
                    />
                    <label class="form-check-label" for="agree"
                      >Saya setuju dengan <a href="#">ketentuan</a> dan
                      kebijakan keanggotaan.</label
                    >
                  </div>
                </div>

              <div class="col-12 text-end">
                <button type="submit" class="btn-standard">Daftar</button>
              </div>
              </div>
            </form>
            <script>
              (function(){
                const form = document.querySelector('#register-section form');
                if (!form) return;
                form.addEventListener('submit', function(e){
                  let missing = [];
                  const fullName = document.getElementById('fullName');
                  const birthDate = document.getElementById('birthDate');
                  const email = document.getElementById('email');
                  const phone = document.getElementById('phone');
                  const agree = document.getElementById('agree');
                  [fullName, birthDate, email, phone].forEach(function(el){
                    el.classList.remove('is-invalid');
                    if (!el.value || (el.type === 'email' && !el.checkValidity())) {
                      el.classList.add('is-invalid');
                      const label = el.previousElementSibling ? el.previousElementSibling.textContent.trim() : el.name;
                      missing.push(label);
                    }
                  });
                  if (!agree.checked) {
                    missing.push('Persetujuan');
                  }
                  const existing = document.getElementById('registerAlert');
                  if (existing) { existing.remove(); }
                  if (missing.length > 0) {
                    e.preventDefault();
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-danger';
                    alertDiv.id = 'registerAlert';
                    alertDiv.innerHTML = 'Data wajib diisi: ' + missing.join(', ');
                    form.parentNode.insertBefore(alertDiv, form);
                    alertDiv.scrollIntoView({behavior:'smooth'});
                  }
                });
              })();
            </script>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
