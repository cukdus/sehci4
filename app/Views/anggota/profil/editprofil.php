<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Edit Profil</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-warning card-outline">
        <div class="card-body">
          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= esc(session()->getFlashdata('error')) ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>
          <form action="/anggota/profil/update" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="id_anggota" value="<?= esc($anggota['id_anggota']) ?>" />
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">No. Anggota</label>
                <input type="text" name="no_anggota" class="form-control" value="<?= esc($anggota['no_anggota']) ?>" disabled />
              </div>
              <div class="col-md-8">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= esc($anggota['nama']) ?>" required />
              </div>
              <div class="col-md-4">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-select">
                  <option value="" <?= ($anggota['jenis_kelamin'] ?? '') === '' ? 'selected' : '' ?>>-</option>
                  <option value="Laki-laki" <?= ($anggota['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                  <option value="Perempuan" <?= ($anggota['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" class="form-control" value="<?= esc($anggota['tempat_lahir'] ?? '') ?>" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" class="form-control" value="<?= esc($anggota['tanggal_lahir'] ?? '') ?>" />
              </div>
              <div class="col-md-8">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2"><?= esc($anggota['alamat']) ?></textarea>
              </div>
              <div class="col-md-4">
                <label class="form-label">No Telepon</label>
                <input type="text" name="no_telepon" class="form-control" value="<?= esc($anggota['no_telepon']) ?>" />
              </div>
              <div class="col-md-4">
                <label class="form-label">No KTP</label>
                <input type="text" name="no_ktp" class="form-control" value="<?= esc($anggota['no_ktp'] ?? '') ?>" />
              </div>
              <div class="col-md-4">
                <label class="form-label">No KK</label>
                <input type="text" name="no_kk" class="form-control" value="<?= esc($anggota['no_kk'] ?? '') ?>" />
              </div>
              <div class="col-md-4">
                <label class="form-label">No NPWP</label>
                <input type="text" name="no_npwp" class="form-control" value="<?= esc($anggota['no_npwp'] ?? '') ?>" />
              </div>
              <div class="col-md-12">
                <label class="form-label">Basic Skill</label>
                <?php $rawSkills = (string) ($anggota['basic_skill'] ?? '');
                $existingSkills = [];
                if ($rawSkills !== '') {
                    if ($rawSkills[0] === '[') {
                        $existingSkills = json_decode($rawSkills, true) ?: [];
                    } else {
                        $existingSkills = array_map('trim', explode(',', $rawSkills));
                    }
                } ?>
                <div class="row g-2">
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="basic_skill[]" value="Management" id="skillManagement" <?= in_array('Management', $existingSkills, true) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="skillManagement">Management</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="basic_skill[]" value="Accounting" id="skillAccounting" <?= in_array('Accounting', $existingSkills, true) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="skillAccounting">Accounting</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="basic_skill[]" value="Digital Marketing" id="skillDigitalMarketing" <?= in_array('Digital Marketing', $existingSkills, true) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="skillDigitalMarketing">Digital Marketing</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="basic_skill[]" value="Leadership" id="skillLeadership" <?= in_array('Leadership', $existingSkills, true) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="skillLeadership">Leadership</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="basic_skill[]" value="Ms. Office Program" id="skillMsOffice" <?= in_array('Ms. Office Program', $existingSkills, true) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="skillMsOffice">Ms. Office Program</label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="basic_skill[]" value="Design Grapich Program" id="skillDesign" <?= in_array('Design Grapich Program', $existingSkills, true) ? 'checked' : '' ?>>
                      <label class="form-check-label" for="skillDesign">Design Grapich Program</label>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-check d-flex align-items-center gap-2">
                      <input class="form-check-input" type="checkbox" name="basic_skill[]" value="Other" id="skillOther" <?= in_array('Other', $existingSkills, true) ? 'checked' : '' ?> />
                      <label class="form-check-label flex-grow-1 editable-skill" for="skillOther" id="skillOtherLabel" contenteditable="false" data-placeholder="Tambahkan skill">Other</label>
                      <input type="hidden" name="basic_skill_other" id="skillOtherHidden" value="" />
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Pengalaman Kerja</label>
                <input type="text" name="pengalaman_kerja" class="form-control" value="<?= esc($anggota['pengalaman_kerja'] ?? '') ?>" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Pengalaman Organisasi</label>
                <input type="text" name="pengalaman_organisasi" class="form-control" value="<?= esc($anggota['pengalaman_organisasi'] ?? '') ?>" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= esc($anggota['email']) ?>" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Tanggal Gabung</label>
                <input type="date" name="tanggal_gabung" class="form-control" value="<?= esc($anggota['tanggal_gabung'] ?? '') ?>" disabled />
              </div>
              
              <div class="col-md-4">
                <label class="form-label">Foto</label>
                <input type="file" name="foto" id="fotoInput" class="form-control" accept="image/*" />
                <input type="hidden" name="foto_cropped" id="fotoCropped" />
              </div>
              <div class="col-md-4">
                <label class="form-label">Preview</label>
                <div>
                  <?php $foto = trim((string) ($anggota['foto'] ?? '')); ?>
                  <img id="fotoPreview" src="<?= $foto !== '' ? esc($foto) : 'https://via.placeholder.com/160x160?text=Foto' ?>" class="img-thumbnail" style="max-width:160px; height:auto;" />
                </div>
              </div>
            </div>
            <div class="mt-4 d-flex gap-2">
              <a href="/anggota/profil" class="btn btn-secondary">Batal</a>
              <button type="submit" class="btn btn-warning">Simpan</button>
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
  .editable-skill[contenteditable="true"] { border-bottom: 1px dashed #adb5bd; padding: 2px 4px; min-width: 140px; display: inline-block; }
  .editable-skill[contenteditable="true"]:empty:before { content: attr(data-placeholder); color: #6c757d; font-style: italic; }
  .editable-skill[contenteditable="true"]:focus { outline: none; background-color: #f8f9fa; }
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

  const skillOtherCheckbox = document.getElementById('skillOther');
  const skillOtherLabel = document.getElementById('skillOtherLabel');
  const skillOtherHidden = document.getElementById('skillOtherHidden');
  function updateOtherSkill() {
    if (!skillOtherCheckbox || !skillOtherLabel) return;
    const enabled = skillOtherCheckbox.checked;
    skillOtherLabel.contentEditable = enabled ? 'true' : 'false';
    if (!enabled) {
      skillOtherLabel.textContent = 'Other';
      skillOtherHidden.value = '';
    } else {
      if (skillOtherLabel.textContent.trim().toLowerCase() === 'other') {
        skillOtherLabel.textContent = '';
      }
      skillOtherLabel.focus();
    }
  }
  function syncOtherSkill() {
    if (!skillOtherLabel || !skillOtherHidden) return;
    const text = skillOtherLabel.textContent.trim();
    skillOtherHidden.value = text && text.toLowerCase() !== 'other' ? text : '';
  }
  if (skillOtherCheckbox) {
    skillOtherCheckbox.addEventListener('change', updateOtherSkill);
    skillOtherLabel.addEventListener('input', syncOtherSkill);
    updateOtherSkill();
    syncOtherSkill();
  }
</script>

