<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6">
          <h3 class="mb-0">Data Anggota</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-primary card-outline">
        <div class="card-header d-flex align-items-center">
          <h5 class="card-title mb-0">Daftar Anggota</h5>
          <div class="d-flex align-items-center gap-2 ms-auto">
            <div class="input-group input-group-sm" style="max-width:320px;">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" id="searchInput" class="form-control" placeholder="Cari nama atau no anggota" />
            </div>
            <a href="/admin/anggota/tambah" class="btn btn-primary btn-sm text-nowrap">Tambah Anggota</a>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover" id="anggotaTable">
              <thead>
                <tr>
                  <th class="sortable" data-key="id_anggota">ID <i class="bi bi-arrow-down-up ms-1 sort-icon"></i></th>
                  <th class="sortable" data-key="no_anggota">No Anggota <i class="bi bi-arrow-down-up ms-1 sort-icon"></i></th>
                  <th class="sortable" data-key="nama">Nama <i class="bi bi-arrow-down-up ms-1 sort-icon"></i></th>
                  <th>Simpan-Pinjam</th>
                  <th data-key="no_telepon">No Telepon</th>
                  <th data-key="email">Email</th>
                  <th data-key="status">Status</th>
                  <th data-key="jenis_anggota">Jenis Anggota</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody id="anggotaBody"></tbody>
            </table>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <div id="pageInfo" class="small text-muted"></div>
            <nav>
              <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>


<script>
  let dataSrc = [];
  let filtered = [];
  let currentPage = 1;
  const pageSize = 15;
  let sortKey = 'id_anggota';
  let sortAsc = false;

  function renderTable() {
    const start = (currentPage - 1) * pageSize;
    const end = start + pageSize;
    const pageData = filtered.slice(start, end);
    const tbody = document.getElementById('anggotaBody');
    tbody.innerHTML = '';
    pageData.forEach(a => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${a.id_anggota}</td>
        <td>${a.no_anggota || ''}</td>
        <td>${a.nama || ''}</td>
        <td>
          <div class="btn-group" role="group">
            <a href="/admin/anggota/lihatsimpan/${a.id_anggota}" class="btn btn-sm btn-success" title="Lihat Simpan"><i class="bi bi-piggy-bank"></i> Simpan</a>
            <a href="/admin/anggota/lihatpinjam/${a.id_anggota}" class="btn btn-sm btn-warning" title="Lihat Pinjam"><i class="bi bi-cash-coin"></i> Pinjam</a>
          </div>
        </td>
        <td>${a.no_telepon || '-'}</td>
        <td>${a.email || '-'}</td>
        <td>
          <div class="form-check form-switch d-inline-block">
            <input class="form-check-input" type="checkbox" role="switch" ${a.status==='aktif'?'checked':''} data-id="${a.id_anggota}" onchange="window._toggleAnggota(this)" />
          </div>
        </td>
        <td>${a.jenis_anggota || ''}</td>
        <td>
          <div class="btn-group" role="group">
            <a href="/admin/anggota/lihat/${a.id_anggota}" class="btn btn-sm btn-primary rounded-0 rounded-start" title="Lihat"><i class="bi bi-eye"></i></a>
            <a href="/admin/anggota/edit/${a.id_anggota}" class="btn btn-sm btn-warning rounded-0" title="Edit"><i class="bi bi-pencil-square"></i></a>
            <form action="/admin/anggota/delete" method="post" onsubmit="return confirm('Hapus anggota ini?');" style="display:inline;">
              <input type="hidden" name="id_anggota" value="${a.id_anggota}" />
              <button type="submit" class="btn btn-sm btn-danger rounded-0 rounded-end" title="Hapus"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });
    const info = document.getElementById('pageInfo');
    info.textContent = `Menampilkan ${start + 1}-${Math.min(end, filtered.length)} dari ${filtered.length} data`;
    renderPagination();
  }

  function renderPagination() {
    const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
    const ul = document.getElementById('pagination');
    ul.innerHTML = '';
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#">&laquo;</a>`;
    prevLi.onclick = (e) => { e.preventDefault(); if (currentPage > 1) { currentPage--; renderTable(); } };
    ul.appendChild(prevLi);
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage >= totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#">&raquo;</a>`;
    nextLi.onclick = (e) => { e.preventDefault(); if (currentPage < totalPages) { currentPage++; renderTable(); } };
    ul.appendChild(nextLi);
  }

  function applySort() {
    filtered.sort((a, b) => {
      const va = (a[sortKey] ?? '').toString().toLowerCase();
      const vb = (b[sortKey] ?? '').toString().toLowerCase();
      const na = Number(va);
      const nb = Number(vb);
      const isNum = !isNaN(na) && !isNaN(nb);
      if (isNum) return sortAsc ? na - nb : nb - na;
      return sortAsc ? va.localeCompare(vb) : vb.localeCompare(va);
    });
  }

  function applySearch(q) {
    const s = q.trim().toLowerCase();
    if (!s) { filtered = [...dataSrc]; return; }
    filtered = dataSrc.filter(x => (x.nama || '').toLowerCase().includes(s) || (x.no_anggota || '').toLowerCase().includes(s));
  }

  fetch('/admin/anggota/data')
    .then(r => r.json())
    .then(j => { dataSrc = j.data || []; filtered = [...dataSrc]; applySort(); renderTable(); });

  document.getElementById('searchInput').addEventListener('input', (e) => {
    currentPage = 1;
    applySearch(e.target.value);
    applySort();
    renderTable();
  });

  document.querySelectorAll('#anggotaTable thead th.sortable').forEach(th => {
    th.style.cursor = 'pointer';
    th.addEventListener('click', () => {
      const key = th.getAttribute('data-key');
      if (sortKey === key) { sortAsc = !sortAsc; } else { sortKey = key; sortAsc = true; }
      document.querySelectorAll('#anggotaTable thead th .sort-icon').forEach(i => i.className = 'bi bi-arrow-down-up ms-1 sort-icon');
      const icon = th.querySelector('.sort-icon');
      icon.className = sortAsc ? 'bi bi-arrow-up-short ms-1 sort-icon' : 'bi bi-arrow-down-short ms-1 sort-icon';
      applySort();
      renderTable();
    });
  });

  window._toggleAnggota = function(sw){
    const id = sw.getAttribute('data-id');
    const status = sw.checked ? 'aktif' : 'nonaktif';
    const ok = confirm('Ubah status anggota menjadi '+status+'?');
    if(!ok){ sw.checked = !sw.checked; return; }
    fetch('/admin/anggota/toggle',{
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:'id_anggota='+encodeURIComponent(id)+'&status='+encodeURIComponent(status)
    }).then(r=>r.json()).then(j=>{
      return fetch('/admin/anggota/data').then(r=>r.json()).then(j2=>{ dataSrc = j2.data||[]; filtered=[...dataSrc]; applySort(); renderTable(); });
    });
  }
</script>

