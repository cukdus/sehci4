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
            <form action="forms/register.php" method="post" novalidate="">
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
                    required=""
                  />
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
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
