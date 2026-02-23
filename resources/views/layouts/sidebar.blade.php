<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    {{-- Profil User --}}
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

    {{-- Menu Dashboard --}}
    <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('otp.dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon text-primary"></i>
      </a>
    </li>

    {{-- Menu Master Data (Buku & Kategori) --}}
    @if(Auth::check() && Auth::user()->role == 'admin')
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

    {{-- PEMBATAS (Heading Menu Baru) --}}
    <li class="nav-item">
      <div class="sidebar-heading" style="padding: 15px 15px 5px 25px; font-size: 11px; font-weight: bold; color: #afafaf;">
        DOKUMEN EKSKLUSIF
      </div>
    </li>

    {{-- Menu Sertifikat --}}
    <li class="nav-item {{ Request::is('sertifikat*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('otp.sertifikat') }}">
        <span class="menu-title">Sertifikat</span>
        <i class="mdi mdi-certificate menu-icon text-danger"></i>
      </a>
    </li>

    {{-- Menu Undangan --}}
    <li class="nav-item {{ Request::is('undangan*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('otp.undangan') }}">
        <span class="menu-title">Undangan</span>
        <i class="mdi mdi-email-seal menu-icon text-warning"></i>
      </a>
    </li>

    {{-- Menu Logout --}}
    <li class="nav-item mt-3">
      <a class="nav-link" href="{{ route('logout') }}" 
         onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
        <span class="menu-title text-danger">Logout</span>
        <i class="mdi mdi-power menu-icon text-danger"></i>
      </a>
    </li>
  </ul>
</nav>

<form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>