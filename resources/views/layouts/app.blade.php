<!DOCTYPE html>
<html lang="en">

{{-- 1. Include Header --}}
@include('layouts.header')

<body>
  <div class="container-scroller">
    
    {{-- 2. Include Navbar --}}
    @include('layouts.navbar')

    <div class="container-fluid page-body-wrapper">
      
      {{-- 3. Include Sidebar --}}
      @include('layouts.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">
            
            {{-- 4. Content Utama (Berubah-ubah) --}}
            @yield('content')
            
        </div>

        @include('layouts.footer')
      </div>
    </div>
  </div>

  @include('layouts.js-global')
  
  @yield('script')
</body>
</html>