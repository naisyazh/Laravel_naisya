<style>
  @media (min-width: 992px) {
    #sidebar {
      position: fixed;
      top: 70px;
      left: 0;
      width: 260px;
      height: calc(100vh - 70px);
      overflow-y: auto;
      z-index: 1000;
      transition: width 0.3s ease;
    }

      .main-panel {
      margin-left: 260px;
      width: calc(100% - 260px);
    }
  }

  #sidebar::-webkit-scrollbar {
    width: 4px;
  }
  #sidebar::-webkit-scrollbar-thumb {
    background: #e0e0e0;
    border-radius: 10px;
  }
</style>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('assets/images/faces/face1.jpg') }}" alt="profile">
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ Auth::user()->name ?? 'Guest' }}</span>
          <span class="text-secondary text-small">{{ ucfirst(Auth::user()->role ?? '') }}</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('otp.dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon text-primary"></i>
      </a>
    </li>
    @if(Auth::check() && Auth::user()->role == 'admin')
      <li class="nav-item">
        <div class="sidebar-heading" style="padding: 15px 15px 5px 25px; font-size: 11px; font-weight: bold; color: #afafaf;">
          ADMIN PANEL
        </div>
      </li>
      <li class="nav-item {{ Request::is('documents*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('documents.index') }}">
          <span class="menu-title">Manajemen Dokumen</span>
          <i class="mdi mdi-folder-multiple menu-icon text-info"></i>
        </a>
      </li>
      <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('kategori.index') }}">
          <span class="menu-title">Master Kategori</span>
          <i class="mdi mdi-format-list-bulleted menu-icon text-success"></i>
        </a>
      </li>
    @endif
    <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('buku.index') }}">
        <span class="menu-title">Koleksi Buku</span>
        <i class="mdi mdi-book-open-variant menu-icon text-info"></i>
      </a>
    </li>
    <li class="nav-item {{ Request::is('barang*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('barang.index') }}">
        <span class="menu-title">Tag Harga UMKM</span>
        <i class="mdi mdi-tag-multiple menu-icon text-primary"></i>
      </a>
    </li>
    @if(Auth::check() && Auth::user()->role == 'user')
      <li class="nav-item">
        <div class="sidebar-heading" style="padding: 15px 15px 5px 25px; font-size: 11px; font-weight: bold; color: #afafaf;">
          DOKUMEN EKSKLUSIF
        </div>
      </li>
      <li class="nav-item {{ Request::is('sertifikat*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('otp.sertifikat') }}">
          <span class="menu-title">Sertifikat Saya</span>
          <i class="mdi mdi-certificate menu-icon text-danger"></i>
        </a>
      </li>
      <li class="nav-item {{ Request::is('undangan*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('otp.undangan') }}">
          <span class="menu-title">Undangan Saya</span>
          <i class="mdi mdi-email-seal menu-icon text-warning"></i>
        </a>
      </li>
    @endif

    <li class="nav-item mt-3">
      <a class="nav-link" href="#" 
         onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();"
         style="cursor: pointer; z-index: 1001; position: relative;">
        <span class="menu-title text-danger">Logout</span>
        <i class="mdi mdi-power menu-icon text-danger"></i>
      </a>
    </li>
  </ul>
</nav>

<form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>