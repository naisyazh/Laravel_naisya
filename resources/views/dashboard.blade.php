@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12 grid-margin">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Selamat Datang di Koleksi Buku</h4>
        <p>Anda login sebagai: {{ Auth::user()->role }}</p>
      </div>
    </div>
  </div>
</div>
@endsection