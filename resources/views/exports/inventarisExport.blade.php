<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventaris Barang</title>
      {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="header">
        <div class="title">LAPORAN INVENTARIS BARANG</div>
        @if($search)
        <div class="subtitle">Filter: "{{ $search }}"</div>
        @endif
        <div>Dicetak: {{ now()->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Produk</th>
                <th width="30%">Deskripsi</th>
                <th width="12%">Harga</th>
                <th width="8%">Stok</th>
                <th width="12%">Dibuat</th>
                <th width="13%">Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produks as $index => $produk)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $produk->nama_produk }}</td>
                <td>{{ $produk->deskripsi_produk }}</td>
                <td class="text-right">Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                <td class="text-center">{{ $produk->stok_barang }}</td>
                <td class="text-center">{{ $produk->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-center">{{ $produk->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>