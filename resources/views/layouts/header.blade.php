<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aplikasi Koleksi Buku')</title>

    {{-- Memanggil file style global di atas --}}
    @include('layouts.style-global')
    
    {{-- Memanggil style khusus per halaman (jika ada) --}}
    @include('layouts.style-page')
</head>