<style>
    @page { margin: 10mm; }
    table { border-collapse: collapse; }
    td {
        width: 38mm; height: 19mm; /* Ukuran label TnJ 108 */
        border: 0.1pt solid #ccc; /* Garis pandu potong */
        text-align: center; vertical-align: middle;
        font-family: sans-serif; font-size: 10px;
    }
</style>

<table>
    @php $counter = 0; @endphp
    @for ($i = 0; $i < 8; $i++) {{-- 8 Baris --}}
        <tr>
            @for ($j = 0; $j < 5; $j++) {{-- 5 Kolom --}}
                @php $counter++; @endphp
                <td>
                    @if ($counter > $skip && count($barangs) > 0)
                        @php $item = $barangs->shift(); @endphp
                        <strong>{{ $item->nama }}</strong><br>
                        <span style="color: #d33;">Rp {{ number_format($item->harga) }}</span><br>
                        <small>{{ $item->id_barang }}</small>
                    @endif
                </td>
            @endfor
        </tr>
    @endfor
</table>