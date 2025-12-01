<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Transaksi</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Transaksi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Filter Transaksi</h5>
                    </div>
                    <div class="card-body">
                        <form class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Jenis Transaksi</label>
                                <select class="form-select">
                                    <option value="">Semua</option>
                                    <option>Simpanan</option>
                                    <option>Penarikan</option>
                                    <option>Pinjaman</option>
                                    <option>Angsuran</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control">
                            </div>

                            <div class="col-12 text-end">
                                <button class="btn btn-primary px-4">Tampilkan</button> 
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title mb-0">Daftar Transaksi</h5>
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
                                    <th>Tanggal</th>
                                    <th>Nama Anggota</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>2025-01-15</td>
                                    <td>Budi Santoso</td>
                                    <td>Simpanan</td>
                                    <td>Rp 300.000</td>
                                    <td>Setoran rutin</td>
                                </tr>
                            <tr>
                                <td>2</td>
                                <td>2025-01-16</td>
                                <td>Siti Aminah</td>
                                <td>Pinjaman</td>
                                <td>Rp 2.000.000</td>
                                <td>Pinjaman usaha</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

