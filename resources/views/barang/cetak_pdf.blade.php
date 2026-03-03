<!DOCTYPE html>
<html>
<head>
    <title>Cetak Label Harga</title>
    <style>
        @page { margin: 0; }
        body { margin: 10mm; font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        td {
            width: 20%;
            height: 35mm;
            border: 0.1pt solid #eee;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
            padding: 5px;
        }
        .harga { color: #d33; font-weight: bold; font-size: 13px; }
        .id { font-size: 9px; color: #777; }
    </style>
</head>
<body>
<table>
@php
$counter = 0;
@endphp

@for ($i = 0; $i < 8; $i++)
<tr>
    @for ($j = 0; $j < 5; $j++)
        @php $counter++; @endphp
        <td>
            @if ($counter > $skip && $barangs->count())
                @php $item = $barangs->shift(); @endphp
                <strong>{{ $item->nama }}</strong><br>
                <span class="harga">Rp {{ number_format($item->harga, 0, ',', '.') }}</span><br>
                <span class="id">{{ $item->id_barang }}</span>
            @endif
        </td>
    @endfor
</tr>
@endfor

</table>
</body>
</html>