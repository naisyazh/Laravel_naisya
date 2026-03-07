@extends('layouts.app')

@section('content')
    <div class="content-wrapper">
        <div class="page-header mb-3">
            <h3 class="page-title d-flex align-items-center gap-2">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-code-tags"></i>
                </span>
                Tugas Javascript &amp; jQuery
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('otp.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tugas JS</li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <p class="text-muted small mb-3">Semua data tersimpan di memori browser (frontend only).</p>
                        @include('partials.jsq-assignment')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('partials.jsq-assignment-assets')
