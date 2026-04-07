@extends('layouts.app')

@section('title', 'Detail Checkout Toko Buku')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-receipt"></i>
            </span>
            Detail Checkout Toko Buku
        </h3>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('otp.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('toko-buku.index') }}">Checkout Toko Buku</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $penjualan->nomor_transaksi }}</li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3">
                        <div>
                            <h4 class="font-weight-normal mb-2">Status Pembayaran Buku</h4>
                            <h2 class="mb-2">{{ $penjualan->nomor_transaksi }}</h2>
                            <p class="mb-0">
                                Gunakan halaman ini untuk memeriksa apakah transaksi
                                {{ $isManualDemoOrder ? 'transfer demo' : 'Midtrans' }} sudah berubah menjadi lunas.
                            </p>
                        </div>
                        <div class="text-lg-end">
                            <span class="badge {{ $penjualan->paymentStatusBadgeClass() }} px-3 py-2" id="payment_badge">
                                {{ $penjualan->paymentStatusLabel() }}
                            </span>
                            <div class="mt-3 d-flex gap-2 justify-content-lg-end flex-wrap">
                                <a href="{{ route('toko-buku.index') }}" class="btn btn-light text-primary">Kembali ke
                                    POS</a>
                                @if ($isManualDemoOrder)
                                    @if ($penjualan->payment_status === 'pending')
                                        <button type="button" class="btn btn-warning" id="confirm_demo_payment"
                                            data-url="{{ route('toko-buku.orders.confirm-demo-payment', $penjualan->nomor_transaksi) }}">
                                            Saya Sudah Transfer
                                        </button>
                                    @endif
                                @else
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Ringkasan Buku yang Di-checkout</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Buku</th>
                                    <th>Judul Buku</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($penjualan->items as $item)
                                    <tr>
                                        <td>{{ $item->barang_id }}</td>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td>{{ $item->jumlah }}</td>
                                        <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 grid-margin stretch-card">
            <div class="card w-100">
                <div class="card-body">
                    <h4 class="card-title">Ringkasan Pembayaran</h4>
                    <div class="mb-3">
                        <small class="text-muted d-block">User</small>
                        <strong>{{ $penjualan->customer_name ?? ($penjualan->user?->name ?? '-') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $penjualan->customer_email ?? '-' }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Metode</small>
                        <strong>{{ strtoupper($penjualan->payment_type ?? 'BELUM DIPILIH') }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Total</small>
                        <strong>Rp {{ number_format($penjualan->total, 0, ',', '.') }}</strong>
                    </div>
                    <div class="mb-4">
                        <small class="text-muted d-block">Status Message</small>
                        <span id="status_message">{{ $penjualan->status_message ?? 'Belum ada pembaruan status.' }}</span>
                    </div>

                    <h5 class="mb-3">Instruksi Pembayaran</h5>
                    @if ($paymentInstructions)
                        @foreach ($paymentInstructions as $instruction)
                            <div class="border rounded p-3 mb-2">
                                <small class="text-muted d-block">{{ $instruction['label'] }}</small>
                                @if (!empty($instruction['is_url']))
                                    <a href="{{ $instruction['value'] }}" target="_blank" rel="noopener">
                                        {{ $instruction['value'] }}
                                    </a>
                                @else
                                    <strong>{{ $instruction['value'] }}</strong>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">
                            @if ($isManualDemoOrder)
                                Transfer ke rekening demo di atas, lalu klik <strong>Saya Sudah Transfer</strong> agar
                                status berubah menjadi sedang diproses.
                            @else
                                Pembayaran diproses otomatis oleh sistem.
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (() => {
            const csrfToken = @json(csrf_token());
            const refreshButton = document.getElementById('refresh_status');
            const confirmDemoPaymentButton = document.getElementById('confirm_demo_payment');
            const paymentBadge = document.getElementById('payment_badge');

            const refreshStatus = async (showSuccessToast = true) => {
                if (!refreshButton) {
                    return;
                }

                refreshButton.disabled = true;
                refreshButton.textContent = 'Mengecek...';

                try {
                    const response = await fetch(refreshButton.dataset.url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'Gagal memeriksa status pembayaran.');
                    }

                    if (showSuccessToast) {
                        await Swal.fire({
                            icon: 'success',
                            title: payload.data.payment_status_label,
                            text: 'Status pembayaran toko buku berhasil diperbarui.',
                        });
                    }

                    window.location.reload();
                } catch (error) {
                    if (showSuccessToast) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Sinkronisasi gagal',
                            text: error.message,
                        });
                    }
                } finally {
                    refreshButton.disabled = false;
                    refreshButton.textContent = 'Periksa Status';
                }
            };

            const confirmDemoPayment = async () => {
                if (!confirmDemoPaymentButton) {
                    return;
                }

                confirmDemoPaymentButton.disabled = true;
                confirmDemoPaymentButton.textContent = 'Memproses...';

                try {
                    const response = await fetch(confirmDemoPaymentButton.dataset.url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        throw new Error(payload.message || 'Gagal mengirim konfirmasi pembayaran demo.');
                    }

                    await Swal.fire({
                        icon: 'success',
                        title: payload.data.payment_status_label,
                        text: payload.data.status_message,
                    });

                    window.location.reload();
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Konfirmasi gagal',
                        text: error.message,
                    });
                } finally {
                    confirmDemoPaymentButton.disabled = false;
                    confirmDemoPaymentButton.textContent = 'Saya Sudah Transfer';
                }
            };

            if (refreshButton) {
                refreshButton.addEventListener('click', () => refreshStatus(true));
            }

            if (confirmDemoPaymentButton) {
                confirmDemoPaymentButton.addEventListener('click', confirmDemoPayment);
            }

            if (refreshButton && paymentBadge.textContent.trim() === 'Menunggu Pembayaran') {
                setTimeout(() => refreshStatus(false), 12000);
            }
        })();
    </script>
@endsection
