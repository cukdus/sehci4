<!doctype html>
<html lang="en">
<head>
<?php echo view('anggota/partials/head', ['title' => $title ?? 'anggota']); ?>
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
<div class="app-wrapper">
  <?php echo view('anggota/partials/navbar'); ?>
  <?php echo view('anggota/partials/sidebar'); ?>
  <?php echo $content; ?>
  <?php echo view('anggota/partials/footer'); ?>
</div>
<?php if (!empty($showProfileIncompleteModal)): ?>
<div class="modal fade" id="profileIncompleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lengkapi Profil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Data profil anda belum lengkap. Mohon lengkapi untuk pengalaman yang lebih baik.</p>
        <?php if (!empty($profileMissingFields)): ?>
          <div class="small text-muted">Kurang: <?= esc(implode(', ', $profileMissingFields)) ?></div>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <a href="/anggota/profil/edit" class="btn btn-primary">Lengkapi Profil</a>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Lain kali</button>
      </div>
    </div>
  </div>
  </div>
<script>
  document.addEventListener('DOMContentLoaded', function(){
    var el = document.getElementById('profileIncompleteModal');
    if (el && typeof bootstrap !== 'undefined') { new bootstrap.Modal(el).show(); }
  });
 </script>
<?php endif; ?>
<script
  src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-rc3/dist/js/adminlte.min.js"
  crossorigin="anonymous"
></script>
<script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->
    <!-- OPTIONAL SCRIPTS -->
    <!-- sortablejs -->
    <script
      src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"
      crossorigin="anonymous"
    ></script>
    <!-- sortablejs -->
    <script>
      (function(){
        const el = document.querySelector('.connectedSortable');
        if (el && typeof Sortable !== 'undefined') {
          try {
            new Sortable(el, { group: 'shared', handle: '.card-header' });
            const cardHeaders = document.querySelectorAll('.connectedSortable .card-header');
            cardHeaders.forEach((cardHeader) => { cardHeader.style.cursor = 'move'; });
          } catch (e) {
            console.error('Sortable init failed:', e);
          }
        }
      })();
    </script>
    <!-- apexcharts -->
    <script
      src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
      integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
      crossorigin="anonymous"
    ></script>
    <!-- ChartJS -->
    
    <!-- jsvectormap -->
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"
      integrity="sha256-/t1nN2956BT869E6H4V1dnt0X5pAQHPytli+1nTZm2Y="
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"
      integrity="sha256-XPpPaZlU8S/HWf7FZLAncLg2SAkP8ScUTII89x9D3lY="
      crossorigin="anonymous"
    ></script>
    <!-- jsvectormap -->
</body>
</html>
