<?php $headerClass = isset($headerClass) ? $headerClass : 'fixed-top'; ?>
<header id="header" class="header d-flex align-items-center <?= esc($headerClass) ?>">
      <div
        class="container-fluid position-relative d-flex align-items-center justify-content-between">
        <a
          href="index.html"
          class="logo d-flex align-items-center me-auto me-xl-0">
          <img
            src="assets/img/Logo Supporter Entrepreneur Hub Blue Baby.png"
            alt="Logo Supporter Entrepreneur Hub" />
        </a>
        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="/">Beranda</a></li>
            <li><a href="/#services">Tentang</a></li>
            <li><a href="/#layanan">Layanan</a></li>
            <!--<li><a href="/#pricing">Produk</a></li>-->
            <li><a href="/#portfolio">Unit</a></li>
            <li><a href="/#membership-benefits">Keanggotaan</a></li>
            <li><a href="/#recent-posts">Kabar</a></li>
            <li><a href="/#contact">Kontak</a></li>
          </ul>
          <i
            class="mobile-nav-toggle d-xl-none bi bi-list"
            role="button"
            tabindex="0"
            aria-label="Toggle navigation"></i>
        </nav>
        <a class="btn-standard" href="/register">Daftar</a>
      </div>
    </header>
