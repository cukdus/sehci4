<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Pinjaman</h3>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-end">
            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Pinjaman</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!--end::App Content Header-->

  <!--begin::App Content-->
  <div class="content-wrapper">
  <div class="content">
    <div class="container-fluid">

      <!-- FORM PENGAJUAN PINJAMAN -->
      <div class="card shadow-sm mb-4">
        <div class="card-header">
          <h5 class="card-title mb-0">Form Pengajuan Pinjaman</h5>
        </div>

        <div class="card-body">
          <form class="row g-3">

            <div class="col-md-6">
              <label class="form-label">Nama Anggota</label>
              <input type="text" class="form-control" placeholder="Masukkan nama anggota">
            </div>

            <div class="col-md-6">
              <label class="form-label">Jumlah Pinjaman</label>
              <input type="number" class="form-control" placeholder="Contoh: 2000000">
            </div>

            <div class="col-md-4">
              <label class="form-label">Tenor (Bulan)</label>
              <select class="form-select">
                <option value="">Pilih Tenor</option>
                <option>3</option>
                <option>6</option>
                <option>12</option>
                <option>18</option>
                <option>24</option>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Bunga (%)</label>
              <input type="number" class="form-control" placeholder="Misal 2%">
            </div>

            <div class="col-md-4">
              <label class="form-label">Tanggal Pengajuan</label>
              <input type="date" class="form-control">
            </div>

            <div class="col-12">
              <label class="form-label">Keperluan Pinjaman</label>
              <textarea class="form-control" rows="2" placeholder="Contoh: Modal usaha, renovasi rumah, dll"></textarea>
            </div>

            <div class="col-12 text-end">
              <button class="btn btn-primary px-4">Ajukan Pinjaman</button>
            </div>

          </form>
        </div>
      </div>

      <!-- DAFTAR PINJAMAN -->
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between">
          <h5 class="card-title mb-0">Daftar Pinjaman</h5>
          <div>
            <button class="btn btn-danger btn-sm">PDF</button>
            <button class="btn btn-success btn-sm">Excel</button>
          </div>
        </div>

        <div class="card-body table-responsive">
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Tenor</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>

              <tr>
                <td>1</td>
                <td>Budi Santoso</td>
                <td>2025-01-15</td>
                <td>Rp 3.000.000</td>
                <td>12 Bulan</td>
                <td><span class="badge bg-warning">Proses</span></td>
              </tr>

              <tr>
                <td>2</td>
                <td>Siti Aminah</td>
                <td>2025-01-10</td>
                <td>Rp 2.000.000</td>
                <td>6 Bulan</td>
                <td><span class="badge bg-success">Disetujui</span></td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
</main>

