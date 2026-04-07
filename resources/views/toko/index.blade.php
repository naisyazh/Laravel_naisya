@extends('layouts.app')

@section('title', 'Checkout Toko Buku')

@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .book-pos-shell .card {
            border-radius: 1rem;
        }

        .book-pos-shell .hero-card {
            overflow: hidden;
            position: relative;
        }

        .book-pos-shell .hero-card::after {
            content: '';
            position: absolute;
            right: -40px;
            top: -40px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }

        .book-pos-shell .featured-book {
            border: 1px solid #ecebf3;
            border-radius: 1rem;
            padding: 1rem;
            background: #fff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .book-pos-shell .featured-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(80, 51, 150, 0.08);
        }

        .book-pos-shell .featured-book-code {
            display: inline-flex;
            padding: 0.35rem 0.7rem;
            border-radius: 999px;
            background: rgba(139, 92, 246, 0.12);
            color: #7c3aed;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .book-pos-shell .book-cart {
            background: linear-gradient(135deg, #6d28d9, #8b5cf6);
            color: #fff;
            border: none;
        }

        .book-pos-shell .book-cart .table {
            color: #fff;
        }

        .book-pos-shell .book-cart .table th,
        .book-pos-shell .book-cart .table td {
            border-color: rgba(255, 255, 255, 0.14);
            vertical-align: middle;
        }

        .book-pos-shell .checkout-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            margin-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 1.1rem;
            font-weight: 700;
        }

        .book-pos-shell .helper-box {
            border-radius: 1rem;
            background: #f5f3ff;
            color: #5b21b6;
            padding: 0.9rem 1rem;
            font-weight: 600;
        }

        .book-pos-shell .empty-cart {
            text-align: center;
            color: rgba(255, 255, 255, 0.78);
            padding: 2rem 0;
        }
    </style>
@endsection

@section('content')
    <div class="book-pos-shell">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-book-open-page-variant"></i>
                </span>
                Checkout Toko Buku
            </h3>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('otp.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">POS Midtrans</li>
                </ul>
            </nav>
        </div>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card bg-gradient-primary hero-card text-white">
                    <div class="card-body">
                        <h4 class="font-weight-normal mb-2">
                            {{ $manualDemoPaymentEnabled ? 'Demo Transfer Manual untuk User' : 'Demo Payment Gateway Midtrans untuk User' }}
                        </h4>
                        <h2 class="mb-3">Checkout buku langsung dari halaman POS dengan template Purple Admin.</h2>
                        <p class="mb-0">
                            User login dapat mencari buku berdasarkan kode, membuat keranjang, lalu membayar
                            {{ $manualDemoPaymentEnabled ? 'via transfer demo ke rekening yang Anda tentukan.' : 'via QRIS Midtrans.' }}
                            Data buku diambil dari master buku toko yang dikelola admin.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        @if ($manualDemoPaymentEnabled)
            <div class="alert alert-info">
                Mode <strong>transfer demo manual</strong> aktif. Tombol bayar akan membuat order lalu mengarahkan user ke
                detail transfer demo.
                Rekening demo saat ini: <strong>{{ $manualDemoBankDetails['bank_name'] }}</strong> -
                <strong>{{ $manualDemoBankDetails['account_number'] }}</strong> a.n.
                <strong>{{ $manualDemoBankDetails['account_name'] }}</strong>.
            </div>
            @if ($manualDemoPaymentNotice)
                <div class="alert alert-warning">
                    {{ $manualDemoPaymentNotice }}
                </div>
            @endif
        @elseif (!$midtransConfigured)
            <div class="alert alert-warning">
                Konfigurasi Midtrans belum lengkap. Isi `MIDTRANS_SERVER_KEY` dan `MIDTRANS_CLIENT_KEY` pada `.env`.
            </div>
        @elseif ($midtransConfigurationNotice)
            <div class="alert alert-warning">
                {{ $midtransConfigurationNotice }}
            </div>
        @else
            <div class="alert alert-info">
                Demo checkout ini dibatasi ke <strong>QRIS saja</strong>. Jika popup Snap menampilkan
                <strong>No payment channels available</strong>, biasanya QRIS belum aktif di akun Midtrans atau belum
                diaktifkan pada <strong>Settings &gt; Snap Preferences &gt; Payment Channels</strong>.
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-1">Point of Sales Buku</h4>
                        <p class="card-description mb-4">
                            Ketik kode buku lalu tekan <code>Enter</code>, atau gunakan data buku cepat di bawah yang
                            langsung terhubung ke master admin.
                        </p>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Kode Buku</label>
                                <input type="text" class="form-control" id="book_code" placeholder="Contoh: BRG00001">
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-gradient-info w-100" id="search_book">
                                    Cari Buku
                                </button>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" id="book_name" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Harga</label>
                                <input type="text" class="form-control" id="book_price" readonly>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Jumlah</label>
                                <input type="number" class="form-control" id="book_qty" min="1" value="1">
                            </div>
                        </div>

                        <div
                            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3 gap-3">
                            <p class="mb-0 text-muted" id="lookup_status">
                                User login: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})
                            </p>
                            <button type="button" class="btn btn-gradient-primary" id="add_to_cart" disabled>
                                Tambah ke Keranjang
                            </button>
                        </div>

                        <div class="helper-box mt-4" id="book_status_box">
                            Masukkan kode buku untuk mulai checkout
                            {{ $manualDemoPaymentEnabled ? 'transfer demo.' : 'Midtrans.' }}
                        </div>

                        <div class="mt-4">
                            <h5 class="mb-3">Data Buku Cepat dari Master Admin</h5>
                            <p class="text-muted small">
                                Semua data di bawah diambil langsung dari `Master Buku Toko` yang aktif, jadi bisa dipakai
                                untuk demo dan checkout.
                            </p>
                            <div class="row">
                                @forelse ($quickBooks as $book)
                                    <div class="col-md-6 col-xl-4 grid-margin">
                                        <div class="featured-book h-100">
                                            <span class="featured-book-code">{{ $book->id_barang }}</span>
                                            <h6 class="mt-3 mb-2">{{ $book->nama }}</h6>
                                            <p class="text-muted mb-3">Rp {{ number_format($book->harga, 0, ',', '.') }}
                                            </p>
                                            <button type="button" class="btn btn-sm btn-gradient-primary use-book-fast"
                                                data-kode="{{ $book->id_barang }}" data-nama="{{ $book->nama }}"
                                                data-harga="{{ (int) $book->harga }}">
                                                Forward
                                            </button>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            Belum ada buku aktif di master toko. Minta admin menambahkan buku di menu
                                            `Master Buku Toko`.
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card book-cart w-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title text-white mb-1">Keranjang Buku</h4>
                                <p class="mb-0 text-white-50">
                                    {{ $manualDemoPaymentEnabled ? 'Checkout user dengan transfer demo' : 'Checkout user dengan QRIS Midtrans' }}
                                </p>
                            </div>
                            <span class="badge badge-light text-dark" id="cart_count">0 item</span>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Buku</th>
                                        <th>Qty</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="cart_body">
                                    <tr>
                                        <td colspan="4" class="empty-cart">Belum ada buku di keranjang.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="checkout-total">
                            <span>Total</span>
                            <span id="cart_total">Rp 0</span>
                        </div>

                        <button type="button" class="btn btn-light text-primary font-weight-bold w-100 mt-3"
                            id="pay_button" {{ $paymentGatewayReady ? '' : 'disabled' }}>
                            {{ $paymentButtonLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (!$manualDemoPaymentEnabled && $midtransConfigured)
        <script src="{{ $midtransSnapScriptUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
    @endif
    <script>
        (() => {
            const bookCatalog = @json($quickBookCatalog);
            const routes = {
                lookup: @json(route('toko-buku.lookup')),
                checkout: @json(route('toko-buku.checkout')),
            };
            const csrfToken = @json(csrf_token());
            const manualDemoPaymentEnabled = @json($manualDemoPaymentEnabled);
            const midtransConfigured = @json($midtransConfigured);
            const paymentGatewayReady = @json($paymentGatewayReady);
            const paymentButtonLabel = @json($paymentButtonLabel);

            const elements = {
                code: document.getElementById('book_code'),
                searchButton: document.getElementById('search_book'),
                name: document.getElementById('book_name'),
                price: document.getElementById('book_price'),
                qty: document.getElementById('book_qty'),
                addButton: document.getElementById('add_to_cart'),
                statusBox: document.getElementById('book_status_box'),
                cartBody: document.getElementById('cart_body'),
                cartCount: document.getElementById('cart_count'),
                cartTotal: document.getElementById('cart_total'),
                payButton: document.getElementById('pay_button'),
            };

            const state = {
                selectedBook: null,
                cart: [],
            };

            const bookIndex = bookCatalog.reduce((carry, book) => {
                carry[String(book.kode).toUpperCase()] = {
                    kode: String(book.kode),
                    nama: String(book.nama),
                    harga: Number(book.harga),
                };

                return carry;
            }, {});

            const formatRupiah = (value) => 'Rp ' + Number(value || 0).toLocaleString('id-ID');

            const showStatus = (message) => {
                elements.statusBox.textContent = message;
            };

            const parseErrorMessage = (error) => {
                if (error && error.responseJSON && error.responseJSON.message) {
                    return error.responseJSON.message;
                }

                if (error && error.responseJSON && error.responseJSON.errors) {
                    const firstKey = Object.keys(error.responseJSON.errors)[0];
                    if (firstKey && error.responseJSON.errors[firstKey][0]) {
                        return error.responseJSON.errors[firstKey][0];
                    }
                }

                if (error instanceof Error) {
                    return error.message;
                }

                return 'Terjadi kesalahan saat memproses permintaan.';
            };

            const transport = {
                async get(url, params = {}) {
                    const requestUrl = new URL(url, window.location.origin);

                    Object.entries(params).forEach(([key, value]) => {
                        requestUrl.searchParams.set(key, value);
                    });

                    const response = await fetch(requestUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    const payload = await response.json();

                    if (!response.ok) {
                        throw {
                            responseJSON: payload
                        };
                    }

                    return payload;
                },
                async post(url, payload = {}) {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify(payload),
                    });

                    const responsePayload = await response.json();

                    if (!response.ok) {
                        throw {
                            responseJSON: responsePayload
                        };
                    }

                    return responsePayload;
                },
            };

            const clearSelection = () => {
                state.selectedBook = null;
                elements.name.value = '';
                elements.price.value = '';
                elements.qty.value = 1;
                elements.addButton.disabled = true;
            };

            const updatePricePreview = () => {
                if (!state.selectedBook) {
                    elements.price.value = '';
                    return;
                }

                const rawQty = parseInt(elements.qty.value || '1', 10);
                const qty = Number.isNaN(rawQty) || rawQty < 1 ? 1 : rawQty;
                elements.price.value = formatRupiah(state.selectedBook.harga * qty);
            };

            const renderCart = () => {
                if (!state.cart.length) {
                    elements.cartBody.innerHTML =
                        '<tr><td colspan="4" class="empty-cart">Belum ada buku di keranjang.</td></tr>';
                } else {
                    elements.cartBody.innerHTML = state.cart.map((item) => `
                        <tr>
                            <td>
                                <strong>${item.nama}</strong>
                                <div class="text-white-50 small">${item.kode}</div>
                            </td>
                            <td>${item.jumlah}</td>
                            <td>${formatRupiah(item.subtotal)}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-light remove-item" data-kode="${item.kode}">X</button>
                            </td>
                        </tr>
                    `).join('');
                }

                const total = state.cart.reduce((sum, item) => sum + item.subtotal, 0);
                elements.cartTotal.textContent = formatRupiah(total);
                elements.cartCount.textContent = `${state.cart.length} item`;
                elements.payButton.disabled = !paymentGatewayReady || state.cart.length === 0;
            };

            const setSelectedBook = (book, sourceMessage = null) => {
                state.selectedBook = {
                    kode: String(book.kode || ''),
                    nama: String(book.nama || ''),
                    harga: Number(book.harga || 0),
                };

                elements.code.value = state.selectedBook.kode;
                elements.name.value = state.selectedBook.nama;
                elements.qty.value = 1;
                updatePricePreview();
                elements.addButton.disabled = false;

                if (sourceMessage) {
                    showStatus(sourceMessage);
                }
            };

            const addBookToCart = (book, qty = 1) => {
                const existing = state.cart.find((item) => item.kode === book.kode);

                if (existing) {
                    existing.jumlah += qty;
                    existing.subtotal = existing.jumlah * existing.harga;
                } else {
                    state.cart.push({
                        kode: book.kode,
                        nama: book.nama,
                        harga: Number(book.harga),
                        jumlah: qty,
                        subtotal: Number(book.harga) * qty,
                    });
                }

                renderCart();
            };

            const lookupBook = async (code) => {
                clearSelection();
                showStatus('Mencari data buku...');

                try {
                    const normalizedCode = String(code).trim().toUpperCase();
                    const localBook = bookIndex[normalizedCode];

                    if (localBook) {
                        setSelectedBook(localBook,
                            `Buku ${localBook.nama} berhasil ditemukan dari master admin.`);
                        return;
                    }

                    const payload = await transport.get(routes.lookup, {
                        kode: normalizedCode
                    });
                    setSelectedBook(payload.data, `Buku ${payload.data.nama} siap ditambahkan ke keranjang.`);
                } catch (error) {
                    showStatus(parseErrorMessage(error));
                }
            };

            const addToCart = () => {
                if (!state.selectedBook) {
                    return;
                }

                const qty = Math.max(1, parseInt(elements.qty.value || '1', 10));
                addBookToCart(state.selectedBook, qty);
                showStatus(`Buku ${state.selectedBook.nama} berhasil masuk ke keranjang.`);
                elements.code.value = '';
                clearSelection();
                elements.code.focus();
            };

            const handleLookupFromInput = () => {
                const code = elements.code.value.trim().toUpperCase();

                if (!code) {
                    showStatus('Kode buku wajib diisi.');
                    return;
                }

                lookupBook(code);
            };

            elements.code.addEventListener('keydown', (event) => {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();
                handleLookupFromInput();
            });

            elements.searchButton.addEventListener('click', handleLookupFromInput);

            elements.qty.addEventListener('input', () => {
                updatePricePreview();
            });

            document.querySelectorAll('.use-book-fast').forEach((button) => {
                button.addEventListener('click', () => {
                    const quickBook = {
                        kode: String(button.dataset.kode),
                        nama: String(button.dataset.nama),
                        harga: Number(button.dataset.harga),
                    };

                    setSelectedBook(quickBook);
                    addBookToCart(quickBook, 1);
                    showStatus(
                        `Buku ${quickBook.nama} berhasil mengisi POS dan otomatis masuk ke keranjang.`
                    );
                });
            });

            elements.addButton.addEventListener('click', addToCart);

            elements.cartBody.addEventListener('click', (event) => {
                const button = event.target.closest('.remove-item');
                if (!button) {
                    return;
                }

                state.cart = state.cart.filter((item) => item.kode !== button.dataset.kode);
                renderCart();
            });

            elements.payButton.addEventListener('click', async () => {
                if (!state.cart.length) {
                    return;
                }

                elements.payButton.disabled = true;
                elements.payButton.textContent = 'Membuat transaksi...';

                try {
                    const payload = await transport.post(routes.checkout, {
                        items: state.cart.map((item) => ({
                            kode: item.kode,
                            jumlah: item.jumlah,
                        })),
                    });

                    if (manualDemoPaymentEnabled || payload.data.payment_mode === 'manual_demo' || !payload
                        .data.snap_token) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Instruksi transfer dibuat',
                            text: 'Anda akan diarahkan ke halaman detail pembayaran demo.',
                        });

                        window.location.href = payload.data.order_url;
                        return;
                    }

                    if (!window.snap) {
                        throw new Error('Snap.js Midtrans belum termuat.');
                    }

                    window.snap.pay(payload.data.snap_token, {
                        onSuccess: () => window.location.href = payload.data.order_url,
                        onPending: () => window.location.href = payload.data.order_url,
                        onError: (result) => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran gagal',
                                text: result.status_message ||
                                    'Terjadi kendala dari Midtrans.',
                            }).then(() => {
                                window.location.href = payload.data.order_url;
                            });
                        },
                        onClose: () => {
                            Swal.fire({
                                icon: 'info',
                                title: 'Pembayaran belum diselesaikan',
                                text: 'Anda akan diarahkan ke halaman detail order untuk mengecek status pembayaran.',
                            }).then(() => {
                                window.location.href = payload.data.order_url;
                            });
                        }
                    });
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Checkout gagal',
                        text: parseErrorMessage(error),
                    });
                } finally {
                    elements.payButton.textContent = paymentButtonLabel;
                    elements.payButton.disabled = !paymentGatewayReady || state.cart.length === 0;
                }
            });

            renderCart();
        })();
    </script>
@endsection
