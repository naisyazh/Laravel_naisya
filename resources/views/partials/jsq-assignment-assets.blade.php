@section('style')
    <!-- Additional CSS for assignment widgets -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        /* ==========================
                   ASSIGNMENT STYLES
                   ========================== */
        .table-clickable tbody tr {
            cursor: pointer;
        }

        .inline-add {
            display: flex;
            align-items: stretch;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .inline-add .form-control {
            flex: 1 1 220px;
            min-width: 0;
            height: 48px;
        }

        .inline-add .btn-add {
            flex: 0 0 auto;
            height: 48px;
            padding: 0 16px;
            white-space: nowrap;
        }

        .select2-container--default .select2-selection--single {
            height: 48px;
            display: flex;
            align-items: center;
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 48px;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px;
            right: 10px;
        }

        .btn-loading .spinner-inline {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.6);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 0.35rem;
            vertical-align: middle;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('script')
    <!-- External plugins for assignment only -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // ==========================
        // FORM VALIDATION
        // ==========================
        $(document).ready(function() {
            const state = {
                items: [],
                nextId: 1,
                selectedId: null
            };

            const $form = $('#product-form');
            const $submitBtn = $('#product-submit');
            const $plainTbody = $('#product-table-plain tbody');
            const $dtTable = $('#product-table-dt');
            const $modal = $('#productModal');
            const $modalForm = $('#product-modal-form');
            const $citySelect = $('#city-select');
            const $citySelect2 = $('#city-select-s2');
            let dataTableInstance = null;

            function setButtonLoading($btn, isLoading, loadingText) {
                const defaultText = $btn.data('default-text') || $btn.text();
                if (!$btn.data('default-text')) {
                    $btn.data('default-text', defaultText);
                }
                if (isLoading) {
                    $btn.addClass('btn-loading').prop('disabled', true);
                    $btn.html('<span class="spinner-inline" aria-hidden="true"></span>' + (loadingText ||
                        'Memproses...'));
                } else {
                    $btn.removeClass('btn-loading').prop('disabled', false).html($btn.data('default-text'));
                }
            }

            function formatPrice(val) {
                if (val === undefined || val === null || val === '') return '-';
                return 'Rp ' + Number(val).toLocaleString('id-ID');
            }

            // ==========================
            // TABLE CRUD LOGIC
            // ==========================
            function renderPlainTable() {
                $plainTbody.empty();
                state.items.forEach(function(item) {
                    const row = '<tr data-id="' + item.id + '">' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.nama + '</td>' +
                        '<td>' + formatPrice(item.harga) + '</td>' +
                        '</tr>';
                    $plainTbody.append(row);
                });
            }

            // ==========================
            // DATATABLE INITIALIZATION
            // ==========================
            function ensureDataTable() {
                if (!$.fn.DataTable) return;
                if (!dataTableInstance) {
                    dataTableInstance = $dtTable.DataTable({
                        paging: true,
                        searching: true,
                        info: true,
                        ordering: true,
                        data: [],
                        columns: [{
                                title: 'ID Barang'
                            },
                            {
                                title: 'Nama'
                            },
                            {
                                title: 'Harga'
                            }
                        ]
                    });
                }
            }

            function renderDataTable() {
                ensureDataTable();
                if (!dataTableInstance) return;
                const rows = state.items.map(function(item) {
                    return [item.id, item.nama, formatPrice(item.harga)];
                });
                dataTableInstance.clear().rows.add(rows).draw();
            }

            function resetForm() {
                $form[0].reset();
                $form.find('input').removeClass('is-invalid');
            }

            $form.on('submit', function(e) {
                e.preventDefault();
                const formEl = this;
                if (!formEl.checkValidity()) {
                    formEl.reportValidity();
                    return;
                }

                setButtonLoading($submitBtn, true, 'Menyimpan...');

                const newItem = {
                    id: state.nextId++,
                    nama: $('#product-nama').val().trim(),
                    harga: $('#product-harga').val()
                };

                state.items.push(newItem);
                renderPlainTable();
                renderDataTable();
                resetForm();

                setTimeout(function() {
                    setButtonLoading($submitBtn, false);
                }, 300);
            });

            // ==========================
            // MODAL HANDLERS
            // ==========================
            function openModalById(id) {
                const item = state.items.find(function(x) {
                    return x.id === id;
                });
                if (!item) return;
                state.selectedId = id;
                $('#modal-id').val(item.id);
                $('#modal-nama').val(item.nama);
                $('#modal-harga').val(item.harga);
                $modal.modal('show');
            }

            $('#product-table-plain').on('click', 'tbody tr', function() {
                const id = parseInt($(this).data('id'), 10);
                openModalById(id);
            });

            $('#product-table-dt').on('click', 'tbody tr', function() {
                const idText = $(this).children().first().text();
                const id = parseInt(idText, 10);
                openModalById(id);
            });

            $('#product-update').on('click', function() {
                const formEl = $modalForm[0];
                if (!formEl.checkValidity()) {
                    formEl.reportValidity();
                    return;
                }

                const idx = state.items.findIndex(function(x) {
                    return x.id === state.selectedId;
                });
                if (idx === -1) return;
                state.items[idx].nama = $('#modal-nama').val().trim();
                state.items[idx].harga = $('#modal-harga').val();

                renderPlainTable();
                renderDataTable();
                $modal.modal('hide');
            });

            $('#product-delete').on('click', function() {
                state.items = state.items.filter(function(x) {
                    return x.id !== state.selectedId;
                });
                renderPlainTable();
                renderDataTable();
                $modal.modal('hide');
            });

            // ==========================
            // SELECT CITY LOGIC
            // ==========================
            function bindCityForm($formEl, $inputEl, $selectEl, $displayEl, useSelect2) {
                $formEl.on('submit', function(e) {
                    e.preventDefault();
                    const val = $inputEl.val().trim();
                    if (!val) {
                        this.reportValidity();
                        return;
                    }
                    const option = new Option(val, val, false, false);
                    $selectEl.append(option);
                    if (useSelect2 && $.fn.select2) {
                        $selectEl.trigger('change.select2');
                    }
                    $inputEl.val('');
                });

                $selectEl.on('change', function() {
                    const selected = $(this).val() || '-';
                    $displayEl.text(selected);
                });

                if (useSelect2 && $.fn.select2) {
                    $selectEl.select2({
                        width: 'resolve',
                        placeholder: '-- Pilih --'
                    });
                }
            }

            bindCityForm($('#city-form'), $('#city-input'), $citySelect, $('#city-selected'), false);
            bindCityForm($('#city-form-s2'), $('#city-input-s2'), $citySelect2, $('#city-selected-s2'), true);

            // Initial render (empty state)
            renderPlainTable();
            renderDataTable();
        });
    </script>
@endsection
