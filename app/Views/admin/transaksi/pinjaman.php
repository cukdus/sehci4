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
                  <th class="sortable">ID</th>
                  <th class="sortable">No Anggota</th>
                  <th class="sortable">Nama Anggota</th>
                  <th class="sortable">Jumlah Pinjaman</th>
                  <th class="sortable">Bunga (%)</th>
                  <th class="sortable">Tanggal Pinjam</th>
                  <th class="sortable">Jangka Waktu (bulan)</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($permohonan)): ?>
                  <?php foreach ($permohonan as $p): ?>
                    <tr>
                      <td data-value="<?= esc($p['id_pinjaman']) ?>"><?= esc($p['id_pinjaman']) ?></td>
                      <td><?= esc($p['no_anggota'] ?? '-') ?></td>
                      <td><?= esc($p['nama'] ?? '-') ?></td>
                      <td data-value="<?= esc($p['jumlah_pinjaman']) ?>"><?= number_format((float) $p['jumlah_pinjaman'], 2, ',', '.') ?></td>
                      <td data-value="<?= esc($p['bunga']) ?>"><?= esc($p['bunga']) ?></td>
                      <td data-value="<?= $p['tanggal_pinjam'] ? strtotime($p['tanggal_pinjam']) : 0 ?>"><?= esc($p['tanggal_pinjam'] ?? '-') ?></td>
                      <td data-value="<?= esc($p['jangka_waktu']) ?>"><?= esc($p['jangka_waktu']) ?></td>
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
                  <th class="sortable">ID</th>
                  <th class="sortable">No Anggota</th>
                  <th class="sortable">Nama Anggota</th>
                  <th class="sortable">Jumlah Pinjaman</th>
                  <th class="sortable">Bunga (%)</th>
                  <th class="sortable">Tanggal Pinjam</th>
                  <th class="sortable">Jangka Waktu (bulan)</th>
                  <th class="sortable">Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($pinjaman)): ?>
                  <?php foreach ($pinjaman as $p): ?>
                    <tr>
                      <td data-value="<?= esc($p['id_pinjaman']) ?>"><?= esc($p['id_pinjaman']) ?></td>
                      <td><?= esc($p['no_anggota'] ?? '-') ?></td>
                      <td><?= esc($p['nama'] ?? '-') ?></td>
                      <td data-value="<?= esc($p['jumlah_pinjaman']) ?>"><?= number_format((float) $p['jumlah_pinjaman'], 2, ',', '.') ?></td>
                      <td data-value="<?= esc($p['bunga']) ?>"><?= esc($p['bunga']) ?></td>
                      <td data-value="<?= $p['tanggal_pinjam'] ? strtotime($p['tanggal_pinjam']) : 0 ?>"><?= esc($p['tanggal_pinjam'] ?? '-') ?></td>
                      <td data-value="<?= esc($p['jangka_waktu']) ?>"><?= esc($p['jangka_waktu']) ?></td>
                      <td data-value="<?= esc($p['status']) ?>"><span class="badge text-bg-secondary"><?= esc($p['status']) ?></span></td>
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
<script>
  function sortTable(table, columnIndex, asc) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const getVal = (td) => td.dataset.value !== undefined ? td.dataset.value : td.textContent.trim().toLowerCase();
    const isNumeric = rows.every(r => {
      const v = getVal(r.children[columnIndex]);
      return v !== '' && !isNaN(Number(v));
    });
    rows.sort((a, b) => {
      const va = getVal(a.children[columnIndex]);
      const vb = getVal(b.children[columnIndex]);
      if (isNumeric) {
        const na = Number(va);
        const nb = Number(vb);
        return asc ? na - nb : nb - na;
      }
      return asc ? va.localeCompare(vb) : vb.localeCompare(va);
    });
    rows.forEach(r => tbody.appendChild(r));
  }
  document.querySelectorAll('table').forEach(table => {
    const headers = table.querySelectorAll('th.sortable');
    headers.forEach((th, idx) => {
      th.style.cursor = 'pointer';
      th.addEventListener('click', () => {
        const current = th.dataset.direction === 'asc' ? 'desc' : 'asc';
        headers.forEach(h => delete h.dataset.direction);
        th.dataset.direction = current;
        sortTable(table, idx, current === 'asc');
      });
    });
  });
</script>
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
