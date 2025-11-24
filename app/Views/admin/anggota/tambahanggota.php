<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Tambah Anggota</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-primary card-outline">
        <div class="card-body">
          <form action="/admin/anggota/create" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">No Anggota</label>
                <input type="text" name="no_anggota" class="form-control" required />
              </div>
              <div class="col-md-8">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" required />
              </div>
              <div class="col-md-4">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select">
                  <option value="">-</option>
                  <option value="Laki-laki">Laki-laki</option>
                  <option value="Perempuan">Perempuan</option>
                </select>
              </div>
              <div class="col-md-8">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"></textarea>
              </div>
              <div class="col-md-4">
                <label class="form-label">No Telepon</label>
                <input type="text" name="no_telepon" class="form-control" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                  <option value="aktif">Aktif</option>
                  <option value="nonaktif">Nonaktif</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Jenis Anggota</label>
                <select name="jenis_anggota" class="form-select">
                  <option value="aktif">Aktif</option>
                  <option value="pasif">Pasif</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" id="fotoInput" class="form-control" accept="image/*" />
                <input type="hidden" name="foto_cropped" id="fotoCropped" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Preview</label>
                <div>
                  <img id="fotoPreview" src="https://via.placeholder.com/160x160?text=Foto" class="img-thumbnail" style="max-width:160px; height:auto;" />
                </div>
              </div>
            </div>
            <div class="mt-4 d-flex gap-2">
              <a href="/admin/anggota" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
          </form>
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
