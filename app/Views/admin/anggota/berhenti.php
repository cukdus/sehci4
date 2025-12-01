<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Permohonan Berhenti Anggota</h3>
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
          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= esc(session()->getFlashdata('error')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>No Anggota</th>
                  <th>Nama</th>
                  <th>Tanggal Berhenti</th>
                  <th>Alasan</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($pending)):
                  foreach ($pending as $row): ?>
                  <tr>
                    <td><?= esc($row['no_anggota'] ?? '') ?></td>
                    <td><?= esc($row['nama'] ?? '') ?></td>
                    <td><?= esc($row['tanggal_berhenti'] ?? '') ?></td>
                    <td><?= esc($row['alasan_berhenti'] ?? '') ?></td>
                    <td class="d-flex gap-2">
                      <form action="/admin/anggota/berhenti/approve" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_anggota" value="<?= esc($row['id_anggota']) ?>" />
                        <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                      </form>
                      <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalTolak" data-id="<?= esc($row['id_anggota']) ?>">Tolak</button>
                    </td>
                  </tr>
                <?php endforeach;
                else: ?>
                  <tr>
                    <td colspan="5" class="text-center">Tidak ada permohonan berhenti</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <hr />
          <h5 class="mt-3">Permohonan Ditolak</h5>
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>No Anggota</th>
                  <th>Nama</th>
                  <th>Tanggal Berhenti (permohonan)</th>
                  <th>Tanggal Penolakan</th>
                  <th>Alasan Berhenti</th>
                  <th>Alasan Penolakan</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($rejected ?? [])):
                  foreach ($rejected as $row): ?>
                  <tr>
                    <td><?= esc($row['no_anggota'] ?? '') ?></td>
                    <td><?= esc($row['nama'] ?? '') ?></td>
                    <td><?= esc($row['tanggal_berhenti'] ?? '') ?></td>
                    <td><?= esc($row['tanggal_tolak_berhenti'] ?? '') ?></td>
                    <td><?= esc($row['alasan_berhenti'] ?? '') ?></td>
                    <td><?= esc($row['alasan_tolak_berhenti'] ?? '') ?></td>
                  </tr>
                <?php endforeach;
                else: ?>
                  <tr>
                    <td colspan="5" class="text-center">Tidak ada penolakan</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<div class="modal fade" id="modalTolak" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="/admin/anggota/berhenti/reject" method="post" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title">Tolak Permohonan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_anggota" id="tolakIdAnggota" />
        <div class="mb-3">
          <label class="form-label">Alasan Penolakan</label>
          <textarea class="form-control" name="alasan_tolak_berhenti" rows="3" placeholder="Tuliskan alasan penolakan"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Tolak</button>
      </div>
    </form>
  </div>
</div>

<script>
  const modalTolak = document.getElementById('modalTolak');
  if (modalTolak) {
    modalTolak.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      document.getElementById('tolakIdAnggota').value = id;
    });
  }
</script>
