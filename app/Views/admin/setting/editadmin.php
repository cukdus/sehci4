<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6"><h3 class="mb-0">Edit Admin/Petugas</h3></div>
        <div class="col-sm-6">
          <div class="float-sm-end mt-2 mt-sm-0">
            <a href="/admin/setting/admin-data" class="btn btn-sm btn-outline-secondary">Kembali</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-primary card-outline">
        <div class="card-header"><h5 class="card-title mb-0">Form Edit</h5></div>
        <div class="card-body">
          <?php $cur = session()->get('user');
          $curRole = (string) ($cur['role'] ?? '');
          $curId = (int) ($cur['id_user'] ?? 0); ?>
          <?php if (!isset($userRow) || empty($userRow)): ?>
            <div class="alert alert-warning mb-0">Data tidak ditemukan.</div>
          <?php else: ?>
            <?php $isSelf = ($curId === (int) ($userRow['id_user'] ?? 0)); ?>
            <?php if ($curRole === 'petugas' && !$isSelf): ?>
              <div class="alert alert-danger mb-0">Petugas hanya dapat mengubah data sendiri.</div>
            <?php else: ?>
              <form method="post" action="/admin/setting/admin/edit/<?= esc($userRow['id_user']) ?>" class="row g-3" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="col-md-6">
                  <label class="form-label" for="username">Username</label>
                  <input type="text" class="form-control" id="username" name="username" value="<?= esc($userRow['username'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="nama_petugas">Nama</label>
                  <input type="text" class="form-control" id="nama_petugas" name="nama_petugas" value="<?= esc($userRow['nama_petugas'] ?? '') ?>" placeholder="Nama lengkap">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="role">Role</label>
                  <select class="form-select" id="role" name="role" <?= $curRole === 'petugas' ? 'disabled' : '' ?> required>
                    <option value="admin" <?= (isset($userRow['role']) && $userRow['role'] === 'admin') ? 'selected' : '' ?>>admin</option>
                    <option value="petugas" <?= (isset($userRow['role']) && $userRow['role'] === 'petugas') ? 'selected' : '' ?>>petugas</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label" for="status">Status</label>
                  <select class="form-select" id="status" name="status" <?= $curRole === 'petugas' ? 'disabled' : '' ?> required>
                    <option value="aktif" <?= (isset($userRow['status']) && strtolower($userRow['status']) === 'aktif') ? 'selected' : '' ?>>aktif</option>
                    <option value="nonaktif" <?= (isset($userRow['status']) && strtolower($userRow['status']) !== 'aktif') ? 'selected' : '' ?>>nonaktif</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="new_password">Password Baru</label>
                  <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" placeholder="Kosongkan jika tidak mengubah">
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="new_password_confirm">Konfirmasi Password Baru</label>
                  <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" minlength="8" placeholder="Kosongkan jika tidak mengubah">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Foto</label>
                  <input type="file" name="foto" id="fotoInput" class="form-control" accept="image/*" />
                  <input type="hidden" name="foto_cropped" id="fotoCropped" />
                </div>
                <div class="col-md-4">
                  <label class="form-label">Preview</label>
                  <div>
                    <?php $foto = trim((string) ($userRow['foto'] ?? '')); ?>
                    <img id="fotoPreview" src="<?= $foto !== '' ? esc($foto) : 'https://via.placeholder.com/160x160?text=Foto' ?>" class="img-thumbnail" style="max-width:160px; height:auto;" />
                  </div>
                </div>
                <div class="col-12 d-flex gap-2">
                  <button type="submit" class="btn btn-primary">Simpan</button>
                  <a href="/admin/setting/admin-data" class="btn btn-outline-secondary">Batal</a>
                </div>
              </form>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<div class="modal fade" id="cropModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crop Foto</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="crop-area">
          <img id="cropImage" src="" alt="Crop" />
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-primary" id="btnApplyCrop">Simpan Crop</button>
      </div>
    </div>
  </div>
 </div>

<link rel="stylesheet" href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css">
<script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>
<style>
  #cropModal .modal-body { padding: 0; }
  #cropModal .crop-area { width: 100%; height: min(70vh, 600px); display: flex; align-items: center; justify-content: center; background: #fff; }
  #cropModal #cropImage { max-width: 100%; max-height: 100%; display: block; }
  #cropModal .cropper-container { width: 100% !important; height: 100% !important; }
</style>
<script>
  let cropper;
  const fotoInput = document.getElementById('fotoInput');
  const cropModalEl = document.getElementById('cropModal');
  const cropImage = document.getElementById('cropImage');
  const fotoPreview = document.getElementById('fotoPreview');
  const fotoCropped = document.getElementById('fotoCropped');
  let pendingCropUrl = '';
  cropModalEl.addEventListener('hidden.bs.modal', function() {
    if (cropper) { cropper.destroy(); cropper = null; }
    cropImage.src = '';
    pendingCropUrl = '';
  });
  cropModalEl.addEventListener('shown.bs.modal', function() {
    if (!pendingCropUrl) return;
    if (cropper) { cropper.destroy(); }
    cropper = new Cropper(cropImage, { aspectRatio: 1, viewMode: 2, autoCropArea: 1, responsive: true });
    pendingCropUrl = '';
  });
  fotoInput.addEventListener('change', function() {
    const f = this.files && this.files[0] ? this.files[0] : null;
    if (!f) return;
    if (f.size > 2 * 1024 * 1024) { alert('Ukuran foto maksimal 2MB'); this.value=''; return; }
    const url = URL.createObjectURL(f);
    pendingCropUrl = url;
    cropImage.src = url;
    const modal = new bootstrap.Modal(cropModalEl);
    modal.show();
  });
  document.getElementById('btnApplyCrop').addEventListener('click', function() {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({ width: 500, height: 500 });
    canvas.toBlob(function(blob) {
      const reader = new FileReader();
      reader.onloadend = function() {
        fotoCropped.value = reader.result;
        fotoPreview.src = reader.result;
        bootstrap.Modal.getInstance(cropModalEl).hide();
      };
      reader.readAsDataURL(blob);
    }, 'image/webp', 0.8);
  });
</script>
