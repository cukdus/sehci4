<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Detail Anggota</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-outline card-secondary">
        <div class="card-body">
          <div class="row g-4">
            <div class="col-md-3 text-center">
              <?php $foto = trim((string) ($anggota['foto'] ?? '')); ?>
              <img src="<?= $foto !== '' ? esc($foto) : 'https://via.placeholder.com/160x160?text=Foto' ?>" alt="Foto Anggota" class="img-thumbnail" style="max-width:300px; height:auto;" />
            </div>
            <div class="col-md-9">
              <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                  <h3 class="mb-4"><?= esc($anggota['nama'] ?? '') ?></h3>
                  <div class="mb-2"><strong>No Anggota:</strong> <?= esc($anggota['no_anggota'] ?? '') ?></div>
                  <div class="mb-2"><strong>Jenis Kelamin:</strong> <?= esc($anggota['jenis_kelamin'] ?? '') ?></div>
                    <div class="mb-2"><strong>Tempat, Tanggal Lahir:</strong> <?= esc($anggota['tempat_lahir'] ?? '') ?>, <?= esc($anggota['tanggal_lahir'] ?? '') ?></div>
                    <div class="mb-2"><strong>Alamat:</strong> <?= esc($anggota['alamat'] ?? '') ?></div>
                    <div class="mb-2"><strong>No Telepon:</strong> <?= esc($anggota['no_telepon'] ?? '') ?></div>
                    <div class="mb-2"><strong>Email:</strong> <?= esc($anggota['email'] ?? '') ?></div>
                    <div class="mb-2"><strong>Tanggal Gabung:</strong> <?= esc($anggota['tanggal_gabung'] ?? '') ?></div>
                </div>
                <div class="text-end">
                  <span class="badge text-bg-secondary me-2"><?= esc($anggota['status'] ?? '') ?></span>
                  <span class="badge text-bg-info"><?= esc($anggota['jenis_anggota'] ?? '') ?></span>
                </div>
              </div>
            </div>
          </div>

          <hr />

          <div class="row g-3">
            <div class="col-md-6">
              <div class="mb-2"><strong>No KTP:</strong> <?= esc($anggota['no_ktp'] ?? '') ?></div>
              <div class="mb-2"><strong>No KK:</strong> <?= esc($anggota['no_kk'] ?? '') ?></div>
              <div class="mb-2"><strong>No NPWP:</strong> <?= esc($anggota['no_npwp'] ?? '') ?></div>
              <div class="mb-2"><strong>Basic Skill:</strong> <?= esc($anggota['basic_skill'] ?? '') ?></div>
              <div class="mb-2"><strong>Pengalaman Kerja:</strong> <?= esc($anggota['pengalaman_kerja'] ?? '') ?></div>
              <div class="mb-2"><strong>Pengalaman Organisasi:</strong> <?= esc($anggota['pengalaman_organisasi'] ?? '') ?></div>
            </div>
            <div class="col-md-6">
              <div class="mb-2"><strong>Tanggal Berhenti:</strong> <?= esc($anggota['tanggal_berhenti'] ?? '') ?></div>
              <div class="mb-2"><strong>Alasan Berhenti:</strong> <?= esc($anggota['alasan_berhenti'] ?? '') ?></div>
              <div class="mb-2"><strong>Alasan Tolak Berhenti:</strong> <?= esc($anggota['alasan_tolak_berhenti'] ?? '') ?></div>
              <div class="mb-2"><strong>Tanggal Tolak Berhenti:</strong> <?= esc($anggota['tanggal_tolak_berhenti'] ?? '') ?></div>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <a href="/admin/anggota" class="btn btn-secondary">Kembali</a>
            <a href="/admin/anggota/edit/<?= esc($anggota['id_anggota']) ?>" class="btn btn-warning">Edit</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
