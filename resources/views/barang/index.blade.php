@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Manajemen Tag Harga Buku</h4>
                
                <button type="button" class="btn btn-gradient-primary btn-fw mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="mdi mdi-plus"></i> Tambah Buku
                </button>

                <form action="{{ route('barang.cetak') }}" method="POST">
                    @csrf
                    <div class="row mb-3 bg-light p-3 rounded">
                        <div class="col-md-3">
                            <label>Mulai Kolom (X)</label>
                            <input type="number" name="x" class="form-control" min="1" max="5" value="1" required>
                        </div>
                        <div class="col-md-3">
                            <label>Mulai Baris (Y)</label>
                            <input type="number" name="y" class="form-control" min="1" max="8" value="1" required>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-gradient-danger">Cetak Label Terpilih</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped" id="barangTable">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th>ID Buku</th>
                                    <th>Judul Buku</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($barangs as $b)
                                <tr>
                                    <td><input type="checkbox" name="selected_ids[]" value="{{ $b->id_barang }}"></td>
                                    <td>{{ $b->id_barang }}</td>
                                    <td>{{ $b->nama }}</td>
                                    <td>Rp {{ number_format($b->harga, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-inverse-warning btn-edit" data-id="{{ $b->id_barang }}">
                                                Edit
                                            </button>

                                            <form action="{{ route('barang.destroy', $b->id_barang) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-inverse-danger" onclick="return confirm('Hapus buku ini?')">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('barang.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Koleksi Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="nama" class="form-control" required placeholder="Contoh: Novel Bumi">
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control" required placeholder="95000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formEdit" method="POST" class="modal-content">
            @csrf @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Koleksi Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Judul Buku</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" name="harga" id="edit_harga" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() 
    $('#selectAll').on('click', function() {
        var checkboxes = $('input[name="selected_ids[]"]');
        checkboxes.prop('checked', this.checked);
    });
    $('.btn-edit').on('click', function() {
        var id = $(this).data('id');
        
        $.ajax({
            url: '/barang/' + id + '/edit',
            type: 'GET',
            success: function(data) {
                $('#edit_nama').val(data.nama);
                $('#edit_harga').val(data.harga);
                
                $('#formEdit').attr('action', '/barang/' + id);

                $('#modalEdit').modal('show');
            },
            error: function() {
                alert('Gagal mengambil data buku.');
            }
        });
    });
});
</script>
@endsection