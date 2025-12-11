<main class="app-main">
  <div class="app-content-header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6 d-flex align-items-center gap-2">
          <h3 class="mb-0">Data Admin</h3>
          <?php if ((session()->get('user')['role'] ?? '') === 'admin'): ?>
            <a href="/admin/setting/admin/add" class="btn btn-sm btn-primary ms-2">Tambah Admin</a>
          <?php endif; ?>
        </div>
        <div class="col-sm-6">
          <div class="input-group input-group-sm mt-2 mt-sm-0 float-sm-end" style="max-width:320px;">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama/username" />
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="app-content">
    <div class="container-fluid">
      <div class="card card-primary card-outline">
        <div class="card-header d-flex align-items-center">
          <h5 class="card-title mb-0">Daftar Admin</h5>
          <span class="ms-auto small text-muted" id="countInfo"></span>
        </div>
        <div class="card-body">
          <div class="row g-3" id="cards"></div>
        </div>
      </div>
    </div>
  </div>
</main>
<script>
(function(){
  const cardsEl = document.getElementById('cards');
  const searchEl = document.getElementById('searchInput');
  const countInfo = document.getElementById('countInfo');
  const currentUserId = <?= json_encode((int) (session()->get('user')['id_user'] ?? 0)) ?>;
  const currentRole = <?= json_encode((string) (session()->get('user')['role'] ?? '')) ?>;
  let dataSrc = []; let filtered = [];
  function roleBadge(r){
    const v=(r||'').toLowerCase();
    const cls = v==='admin' ? 'text-bg-primary' : 'text-bg-secondary';
    return `<span class="badge ${cls}">${r||'-'}</span>`;
  }
  function statusBadge(s){
    const v=(s||'').toLowerCase();
    const cls = v==='aktif' ? 'text-bg-success' : 'text-bg-warning';
    return `<span class="badge ${cls}">${s||'-'}</span>`;
  }
  function card(a){
    const nama = a.nama_petugas || a.username || '-';
    const lvl = a.level ? `<span class="badge text-bg-info">${a.level}</span>` : '';
    const foto = (a.foto||'').trim()!=='' ? a.foto : '/assets/img/user2-160x160.png';
    const canEdit = (currentRole === 'admin') || (currentRole === 'petugas' && (parseInt(a.id_user||0,10) === currentUserId));
    const canDelete = (currentRole === 'admin') && (parseInt(a.id_user||0,10) !== currentUserId);
    return `
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body d-flex align-items-center">
            <img src="${foto}" alt="Foto" class="rounded-circle me-3" style="width:72px;height:72px;object-fit:cover;" />
            <div class="flex-grow-1">
              <div class="fw-bold">${nama}</div>
              <div class="small text-muted">${a.username||'-'}</div>
              <div class="mt-2 d-flex gap-2 align-items-center">
                ${roleBadge(a.role)} ${lvl} ${statusBadge(a.status)}
              </div>
              <div class=\"mt-3 d-flex gap-2\">
                ${canEdit ? `<a class=\"btn btn-sm btn-outline-primary\" href=\"/admin/setting/admin/edit/${a.id_user}\">Edit</a>` : ''}
                ${canDelete ? `<button class=\"btn btn-sm btn-outline-danger\" data-id=\"${a.id_user}\" onclick=\"onDeleteAdmin(${a.id_user})\">Delete</button>` : ''}
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }
  function render(){
    cardsEl.innerHTML = '';
    filtered.forEach(a=>{ cardsEl.insertAdjacentHTML('beforeend', card(a)); });
    countInfo.textContent = `${filtered.length} admin`;
  }
  function applySearch(q){
    const s=(q||'').trim().toLowerCase();
    if(!s){ filtered=[...dataSrc]; return; }
    filtered = dataSrc.filter(a=>{
      return (String(a.nama_petugas||'').toLowerCase().includes(s)) ||
             (String(a.username||'').toLowerCase().includes(s));
    });
  }
  fetch('/admin/api/setting/admin-data')
    .then(r=>r.json())
    .then(j=>{ dataSrc = j.data||[]; filtered=[...dataSrc]; render(); });
  searchEl.addEventListener('input', function(){ applySearch(this.value); render(); });

  window.onDeleteAdmin = function(id){
    if(!id) return;
    if(!confirm('Hapus admin/petugas ini?')) return;
    fetch(`/admin/setting/admin/delete/${id}`, { method: 'POST' })
      .then(r=>r.json())
      .then(j=>{
        if(j && j.ok){
          dataSrc = dataSrc.filter(x=>parseInt(x.id_user||0,10)!==parseInt(id,10));
          filtered = filtered.filter(x=>parseInt(x.id_user||0,10)!==parseInt(id,10));
          render();
        } else {
          alert((j && j.error) ? j.error : 'Gagal menghapus');
        }
      })
      .catch(()=>alert('Gagal menghapus'));
  }
})();
</script>
