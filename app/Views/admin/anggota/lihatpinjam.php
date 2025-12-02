<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Pinjaman Anggota</h3></div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-outline card-secondary mb-3">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div><strong>No Anggota:</strong> <?= esc($anggota['no_anggota'] ?? '-') ?></div>
              <div><strong>Nama:</strong> <?= esc($anggota['nama'] ?? '-') ?></div>
            </div>
            <a href="/admin/anggota" class="btn btn-outline-secondary btn-sm">Kembali</a>
          </div>
        </div>
      </div>
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="alert alert-info mb-0">Halaman ini menampilkan detail pinjaman anggota. Silakan lengkapi kebutuhan data lebih lanjut sesuai proses bisnis.</div>
        </div>
      </div>
    </div>
  </div>
</main>
