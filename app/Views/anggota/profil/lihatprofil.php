<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Profil Anggota</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-outline card-secondary">
        <div class="card-body">
          <?php if (session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= esc(session()->getFlashdata('message')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <?php if (!empty($anggota['alasan_tolak_berhenti'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              Permohonan berhenti ditolak: <?= esc($anggota['alasan_tolak_berhenti']) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <?php if (session()->getFlashdata('warning')): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <?= esc(session()->getFlashdata('warning')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= esc(session()->getFlashdata('error')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <div class="row g-4">
            <div class="col-md-6 text-center">
              <?php $foto = trim((string) ($anggota['foto'] ?? '')); ?>
              <img src="<?= $foto !== '' ? esc($foto) : '/assets/img/user2-160x160.png' ?>" alt="Foto Anggota" class="img-thumbnail" style="max-width:300px; height:auto;" />
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div>
                  <h3 class="mb-4"><?= esc($anggota['nama'] ?? '') ?></h3>
                  <div class="mb-2"><strong>No. Anggota:</strong> <?= esc($anggota['no_anggota'] ?? '') ?></div>
                  <div class="mb-2"><strong>Jenis Kelamin:</strong> <?= esc($anggota['jenis_kelamin'] ?? '') ?></div>
                  <div class="mb-2"><strong>Tempat, Tanggal Lahir:</strong> <?= esc($anggota['tempat_lahir'] ?? '') ?>, <?= esc($anggota['tanggal_lahir'] ?? '') ?></div>
                  <div class="mb-2"><strong>Nama Ibu Kandung:</strong> <?= esc($anggota['nama_ibu'] ?? '') ?></div>
                  <div class="mb-2"><strong>Nama Ibu:</strong> <?= esc($anggota['nama_ibu'] ?? '') ?></div>
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
              <?php $bsraw = (string) ($anggota['basic_skill'] ?? '');
              $bs = '';
              if ($bsraw !== '') {
                if ($bsraw[0] === '[') {
                  $arr = json_decode($bsraw, true) ?: [];
                  $bs = implode(', ', $arr);
                } else {
                  $bs = $bsraw;
                }
              } ?>
              <div class="mb-2"><strong>Basic Skill:</strong> <?= esc($bs) ?></div>
              <div class="mb-2"><strong>Pengalaman Kerja:</strong> <?= esc($anggota['pengalaman_kerja'] ?? '') ?></div>
              <div class="mb-2"><strong>Pengalaman Organisasi:</strong> <?= esc($anggota['pengalaman_organisasi'] ?? '') ?></div>
            </div>
            <div class="col-md-6">
              <div class="mb-2"><strong>Tanggal Berhenti:</strong> <?= esc($anggota['tanggal_berhenti'] ?? '') ?></div>
              <div class="mb-2"><strong>Alasan Berhenti:</strong> <?= esc($anggota['alasan_berhenti'] ?? '') ?></div>
              <div class="mb-2"><strong>Alasan Tolak Berhenti:</strong> <?= esc($anggota['alasan_tolak_berhenti'] ?? '') ?></div>
              <div class="mb-2"><strong>Tanggal Tolak Berhenti:</strong> <?= esc($anggota['tanggal_tolak_berhenti'] ?? '') ?></div>
              <div class="mt-2">
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalBerhenti">Permohonan Berhenti</button>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <a href="/anggota" class="btn btn-secondary">Kembali</a>
            <a href="/anggota/profil/edit" class="btn btn-warning">Edit Profil</a>
          </div>
        </div>
        <div class="modal fade" id="modalBerhenti" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form action="/anggota/profil/berhenti" method="post" class="modal-content">
              <?= csrf_field() ?>
              <div class="modal-header">
                <h5 class="modal-title">Permohonan Berhenti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label">Alasan Berhenti</label>
                  <textarea name="alasan" class="form-control" rows="3" placeholder="Tuliskan alasan berhenti"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Kirim Permohonan</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
  (function(){
    var el = document.querySelector('.alert.alert-success');
    if (!el) return;
    setTimeout(function(){
      try { var inst = bootstrap.Alert.getOrCreateInstance(el); inst.close(); }
      catch(e){ el.remove(); }
    }, 4000);
  })();
</script>
<script>
  (function(){
    var el = document.querySelector('.alert.alert-success');
    if (!el) return;
    setTimeout(function(){
      try {
        var inst = bootstrap.Alert.getOrCreateInstance(el);
        inst.close();
      } catch (e) {
        el.remove();
      }
    }, 4000);
  })();
</script>

