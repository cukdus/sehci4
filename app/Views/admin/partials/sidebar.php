<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">

          <a href="/admin" class="brand-link">

            <img
              src="<?php echo base_url('/assets/adminlte/img/iconadmin.webp'); ?>"
              alt="Panel SEH"
              class="brand-image opacity-75 shadow" style="max-height: 33px;"
            />

            <span class="brand-text fw-light">Panel SEH</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="navigation"
              aria-label="Main navigation"
              data-accordion="false"
              id="navigation"
            >
              
              <li class="nav-item">
                <a href="/admin" class="nav-link">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              
              <li class="nav-header">Anggota</li>
              <li class="nav-item">
                <a href="/admin/anggota" class="nav-link">
                  <i class="nav-icon bi bi-people"></i>
                  <p>Data Anggota</p>
                </a>
              </li>
              <li class="nav-header">Simpanan</li>
              <li class="nav-item">
                <a href="/admin/simpanan/data" class="nav-link">
                  <i class="nav-icon bi bi-download"></i>
                  <p>Data Simpanan</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-ui-checks-grid"></i>
                  <p>
                    Simpanan
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="/admin/simpanan/wajib" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Simpanan Wajib</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/admin/simpanan/sukarela" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Simpanan Sukarela</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="/admin/simpanan/pokok" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Simpanan Pokok</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-header">Transaksi</li>
              <li class="nav-item">
                <a href="/admin/pinjaman" class="nav-link">
                  <i class="nav-icon bi bi-download"></i>
                  <p>Pinjaman</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./docs/layout.html" class="nav-link">
                  <i class="nav-icon bi bi-grip-horizontal"></i>
                  <p>Angsuran</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="./docs/color-mode.html" class="nav-link">
                  <i class="nav-icon bi bi-star-half"></i>
                  <p>Color Mode</p>
                </a>
              </li>
              <li class="nav-header">Tabungan</li>
              <li class="nav-header">Settings</li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon bi bi-ui-checks-grid"></i>
                  <p>
                    Settings
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="/admin/setting/admin-data" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>Admin Data</p>
                    </a>
                  </li>  
                <li class="nav-item">
                    <a href="/admin/setting/waha" class="nav-link">
                      <i class="nav-icon bi bi-circle"></i>
                      <p>WAHA Template</p>
                    </a>
                  </li>
                </ul>
              </li>
            </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
