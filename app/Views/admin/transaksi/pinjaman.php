<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Data Pinjaman</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-warning card-outline mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Permohonan Pinjaman</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>No Angg.</th>
                  <th>Nama Angg.</th>
                  <th>Jumlah Pinj.</th>
                  <th>Bunga (%)</th>
                  <th>Tgl. Pinjam</th>
                  <th>Jangka Waktu (bulan)</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($permohonan)): ?>
                  <?php foreach ($permohonan as $p): ?>
                    <tr>
                      <td><?= esc($p['id_pinjaman']) ?></td>
                      <td><?= esc($p['no_anggota'] ?? '-') ?></td>
                      <td><?= esc($p['nama'] ?? '-') ?></td>
                      <td><?= number_format((float) $p['jumlah_pinjaman'], 2, ',', '.') ?></td>
                      <td><?= esc($p['bunga']) ?></td>
                      <td><?= esc($p['tanggal_pinjam'] ?? '-') ?></td>
                      <td><?= esc($p['jangka_waktu']) ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-success me-2" data-bs-toggle="modal" data-bs-target="#modalApprove" data-id="<?= esc($p['id_pinjaman']) ?>">Terima</button>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject" data-id="<?= esc($p['id_pinjaman']) ?>">Tolak</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center">Tidak ada permohonan pinjaman</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="card card-primary card-outline">
        <div class="card-header">
          <h5 class="card-title mb-0">Daftar Pinjaman</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>No Angg.</th>
                  <th>Nama Angg.</th>
                  <th>Jumlah Pinj.</th>
                  <th>Bunga (%)</th>
                  <th>Tgl. Pinjam</th>
                  <th>Jangka Waktu (bulan)</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($pinjaman)): ?>
                  <?php foreach ($pinjaman as $p): ?>
                    <tr>
                      <td><?= esc($p['id_pinjaman']) ?></td>
                      <td><?= esc($p['no_anggota'] ?? '-') ?></td>
                      <td><?= esc($p['nama'] ?? '-') ?></td>
                      <td><?= number_format((float) $p['jumlah_pinjaman'], 2, ',', '.') ?></td>
                      <td><?= esc($p['bunga']) ?></td>
                      <td><?= esc($p['tanggal_pinjam'] ?? '-') ?></td>
                      <td><?= esc($p['jangka_waktu']) ?></td>
                      <td><span class="badge text-bg-secondary"><?= esc($p['status']) ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="text-center">Tidak ada data pinjaman</td>
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
<div class="modal fade" id="modalApprove" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="/admin/pinjaman/approve" method="post" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title">Terima Permohonan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_pinjaman" id="approveId" />
        <div class="mb-3">
          <label for="approveKeterangan" class="form-label">Keterangan</label>
          <textarea class="form-control" name="keterangan" id="approveKeterangan" rows="3" placeholder="Isi keterangan persetujuan"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-success">Terima</button>
      </div>
    </form>
  </div>
 </div>

<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="/admin/pinjaman/reject" method="post" class="modal-content">
      <?= csrf_field() ?>
      <div class="modal-header">
        <h5 class="modal-title">Tolak Permohonan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_pinjaman" id="rejectId" />
        <div class="mb-3">
          <label for="rejectKeterangan" class="form-label">Alasan/Keterangan</label>
          <textarea class="form-control" name="keterangan" id="rejectKeterangan" rows="3" placeholder="Isi alasan penolakan"></textarea>
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
  const modalApprove = document.getElementById('modalApprove');
  modalApprove.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    document.getElementById('approveId').value = id;
  });

  const modalReject = document.getElementById('modalReject');
  modalReject.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    document.getElementById('rejectId').value = id;
  });
</script>
