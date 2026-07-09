<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-8">
          <h3 class="mb-0">Detail Broadcast WhatsApp</h3>
        </div>
        <div class="col-sm-4 text-sm-end mt-2 mt-sm-0">
          <a href="/admin/setting/waha" class="btn btn-secondary">Kembali</a>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-outline card-secondary">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-3">
              <div><strong>ID Broadcast:</strong></div>
              <div><?= esc($broadcast['id'] ?? '-') ?></div>
            </div>
            <div class="col-md-3">
              <div><strong>Pengirim:</strong></div>
              <div><?= esc($broadcast['created_by'] ?? '-') ?></div>
            </div>
            <div class="col-md-3">
              <div><strong>Waktu:</strong></div>
              <div><?= esc($broadcast['created_at'] ?? '-') ?></div>
            </div>
            <div class="col-md-3">
              <div><strong>Status:</strong></div>
              <div><?= esc($broadcast['status'] ?? '-') ?></div>
            </div>
            <div class="col-md-12">
              <div><strong>Judul:</strong></div>
              <div><?= esc($broadcast['title'] ?? '-') ?></div>
            </div>
            <div class="col-md-12">
              <div><strong>Isi Pesan:</strong></div>
              <div class="border rounded p-3 bg-body-tertiary" style="white-space: pre-wrap;"><?= esc($broadcast['message'] ?? '-') ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="small-box text-bg-primary">
            <div class="inner">
              <h3><?= esc($broadcast['total_target'] ?? 0) ?></h3>
              <p>Total Target</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box text-bg-success">
            <div class="inner">
              <h3><?= esc($broadcast['sent_count'] ?? 0) ?></h3>
              <p>Berhasil</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box text-bg-danger">
            <div class="inner">
              <h3><?= esc($broadcast['failed_count'] ?? 0) ?></h3>
              <p>Gagal</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="small-box text-bg-warning">
            <div class="inner">
              <h3><?= esc($broadcast['skipped_count'] ?? 0) ?></h3>
              <p>Dilewati</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card card-outline card-secondary">
        <div class="card-header">
          <h5 class="card-title mb-0">Daftar Penerima</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
              <thead>
                <tr>
                  <th style="width: 60px;">No</th>
                  <th>Nama</th>
                  <th style="width: 140px;">No Anggota</th>
                  <th style="width: 150px;">No Telepon</th>
                  <th style="width: 110px;">Status</th>
                  <th style="width: 170px;">Waktu Kirim</th>
                  <th>Respons</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($logs)): ?>
                  <?php foreach ($logs as $index => $log): ?>
                    <tr>
                      <td><?= esc($index + 1) ?></td>
                      <td><?= esc($log['nama'] ?? '-') ?></td>
                      <td><?= esc($log['no_anggota'] ?? '-') ?></td>
                      <td><?= esc($log['phone'] ?? '-') ?></td>
                      <td>
                        <?php
                          $status = (string) ($log['status'] ?? '-');
                          $badgeClass = match ($status) {
                              'sent' => 'text-bg-success',
                              'failed' => 'text-bg-danger',
                              'skipped' => 'text-bg-warning',
                              default => 'text-bg-secondary',
                          };
                        ?>
                        <span class="badge <?= esc($badgeClass) ?>"><?= esc($status) ?></span>
                      </td>
                      <td><?= esc($log['sent_at'] ?? '-') ?></td>
                      <td style="white-space: pre-wrap;"><?= esc($log['response_text'] ?? '-') ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada detail penerima untuk broadcast ini.</td>
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
