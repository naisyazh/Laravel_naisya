<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    {{-- PROFIL USER DI SIDEBAR --}}
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

    {{-- MENU DASHBOARD --}}
    <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>
    
    {{-- MENU KATEGORI (HANYA ADMIN) --}}
    @if(Auth::check() && Auth::user()->role == 'admin')
    <li class="nav-item {{ Request::is('kategori*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('kategori.index') }}">
        <span class="menu-title">Master Kategori</span>
        <i class="mdi mdi-format-list-bulleted menu-icon"></i>
      </a>
    </li>
    @endif

    {{-- MENU BUKU (SEMUA USER) --}}
    <li class="nav-item {{ Request::is('buku*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('buku.index') }}">
        <span class="menu-title">Koleksi Buku</span>
        <i class="mdi mdi-book-open-variant menu-icon"></i>
      </a>
    </li>

    {{-- ========================================= --}}
    {{-- TOMBOL LOGOUT (PENAMBAHAN BARU) --}}
    {{-- ========================================= --}}
    <li class="nav-item">
      <a class="nav-link" href="{{ route('logout') }}" 
         onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
        <span class="menu-title text-danger">Logout</span>
        <i class="mdi mdi-power menu-icon text-danger"></i>
      </a>
    </li>

  </ul>
</nav>

{{-- FORM HIDDEN UNTUK PROSES LOGOUT --}}
{{-- Ditaruh di luar UL tapi masih di dalam file agar rapi --}}
<form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>