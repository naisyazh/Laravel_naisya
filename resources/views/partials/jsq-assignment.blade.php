<!-- JS & jQuery Assignment Section: Isolated UI so existing logic remains intact -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">Tugas Javascript &amp; jQuery (Frontend)</h4>
                <p class="text-muted small mb-4">Semua data tersimpan di memori browser (tanpa backend). Klik baris untuk
                    edit/hapus melalui modal.</p>

                <div class="row g-3">
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">Tambah Barang</h5>
                                <form id="product-form" novalidate>
                                    <div class="mb-3">
                                        <label for="product-nama" class="form-label">Nama Barang</label>
                                        <input type="text" id="product-nama" name="nama" class="form-control"
                                            required placeholder="Contoh: Buku Nota" />
                                    </div>
                                    <div class="mb-3">
                                        <label for="product-harga" class="form-label">Harga Barang</label>
                                        <input type="number" min="0" step="100" id="product-harga"
                                            name="harga" class="form-control" required placeholder="Contoh: 25000" />
                                    </div>
                                    <button type="submit" id="product-submit"
                                        class="btn btn-gradient-primary w-100">Simpan Barang</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0">Tabel Barang (HTML)</h5>
                                    <span class="text-muted small">Klik baris untuk ubah/hapus</span>
                                </div>
                                <div class="table-responsive">
                                    <table id="product-table-plain"
                                        class="table table-striped table-hover table-clickable mb-0">
                                        <thead>
                                            <tr>
                                                <th>ID Barang</th>
                                                <th>Nama</th>
                                                <th>Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5 class="card-title mb-0">Tabel Barang (DataTables)</h5>
                                    <span class="text-muted small">Pagination, pencarian, sorting</span>
                                </div>
                                <div class="table-responsive">
                                    <table id="product-table-dt"
                                        class="table table-striped table-hover table-clickable mb-0" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID Barang</th>
                                                <th>Nama</th>
                                                <th>Harga</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Select Kota</h5>
                <form id="city-form" class="inline-add" novalidate>
                    <input type="text" id="city-input" class="form-control" placeholder="Tambah kota" required />
                    <button type="submit" class="btn btn-outline-primary btn-add" id="city-add">Tambahkan</button>
                </form>
                <div class="mt-3">
                    <label for="city-select" class="form-label">Pilih Kota</label>
                    <select id="city-select" class="form-select" aria-label="Pilih kota">
                        <option value="">-- Pilih --</option>
                    </select>
                    <p class="mt-3 mb-0">Kota Terpilih: <span id="city-selected" class="fw-bold">-</span></p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Select2 Kota</h5>
                <form id="city-form-s2" class="inline-add" novalidate>
                    <input type="text" id="city-input-s2" class="form-control" placeholder="Tambah kota" required />
                    <button type="submit" class="btn btn-outline-primary btn-add" id="city-add-s2">Tambahkan</button>
                </form>
                <div class="mt-3">
                    <label for="city-select-s2" class="form-label">Pilih Kota</label>
                    <select id="city-select-s2" class="form-select" aria-label="Pilih kota" style="width:100%">
                        <option value="">-- Pilih --</option>
                    </select>
                    <p class="mt-3 mb-0">Kota Terpilih: <span id="city-selected-s2" class="fw-bold">-</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal CRUD Barang (reusable untuk HTML & DataTables) -->
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-modal-form" novalidate>
                    <div class="mb-3">
                        <label class="form-label">ID Barang</label>
                        <input type="text" id="modal-id" class="form-control" readonly />
                    </div>
                    <div class="mb-3">
                        <label for="modal-nama" class="form-label">Nama Barang</label>
                        <input type="text" id="modal-nama" class="form-control" required />
                    </div>
                    <div class="mb-3">
                        <label for="modal-harga" class="form-label">Harga Barang</label>
                        <input type="number" min="0" step="100" id="modal-harga" class="form-control"
                            required />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" id="product-delete">Hapus</button>
                <button type="button" class="btn btn-gradient-primary" id="product-update">Ubah</button>
            </div>
        </div>
    </div>
</div>
