<main class="app-main" id="main" tabindex="-1">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 d-flex align-items-center">
          <h3 class="mb-0">Ajukan Pinjaman</h3>
          <a href="/anggota/pinjaman" class="btn btn-outline-secondary btn-sm ms-auto">Kembali</a>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-primary card-outline">
        <div class="card-body">
          <?php if (session()->has('error')): ?>
            <div class="alert alert-danger"><?= esc(session('error')) ?></div>
          <?php endif; ?>
          <?php if (session()->has('success')): ?>
            <div class="alert alert-success"><?= esc(session('success')) ?></div>
          <?php endif; ?>
          <form method="post" action="/anggota/pinjaman/ajukan" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-4">
              <label class="form-label">Jumlah Pinjaman</label>
              <input type="text" name="jumlah_pinjaman" class="form-control" placeholder="contoh: 5000000" value="<?= esc(old('jumlah_pinjaman') ?? '') ?>" required />
              <small class="text-muted">Masukkan angka tanpa titik/koma</small>
            </div>
            <div class="col-md-4">
              <label class="form-label">Jangka Waktu (bulan)</label>
              <input type="number" name="jangka_waktu" class="form-control" min="1" value="<?= esc(old('jangka_waktu') ?? '') ?>" required />
            </div>
            <div class="col-md-4">
              <label class="form-label">Bunga (%)</label>
              <input type="text" name="bunga" class="form-control" placeholder="contoh: 2.5" value="<?= esc(old('bunga') ?? '') ?>" disabled />
            </div>
            <div class="col-md-12">
              <label class="form-label">Jaminan</label>
              <input type="text" name="jaminan" class="form-control" value="<?= esc(old('jaminan') ?? '') ?>" />
            </div>
            <div class="col-12">
              <label class="form-label">Keterangan</label>
              <textarea name="keterangan" class="form-control" rows="3"><?= esc(old('keterangan') ?? '') ?></textarea>
            </div>
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-primary">Ajukan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>
