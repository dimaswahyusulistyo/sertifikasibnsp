<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventaris Produk</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
<body>
<main class="app-main py-4">
  <div class="container-fluid">
    <div class="row mb-3">
      <div class="col-sm-6">
        <h3 class="mb-0">Inventaris Produk Elektronik</h3>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div id="searchContainer"></div>
              </div>
              <div class="col-md-6">
                <div class="btn-group float-end" role="group">
                    <a href="{{ route('inventaris.create') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle"></i> Tambah Produk
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-file-earmark-arrow-down"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportData('excel')">Excel</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportData('pdf')">PDF</a></li>
                        </ul>
                    </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card-body">
            <div class="table-responsive">
              <table id="inventarisTable" class="table table-bordered table-striped align-middle">
                  <thead class="table-light text-center">
                  <tr>
                      <th style="width: 60px;" class="text-center" data-orderable="false">#</th>
                      <th>Nama Produk</th>
                      <th class="d-none d-md-table-cell" style="width: 30%;">Deskripsi</th>
                      <th style="width: 120px;">Harga</th>
                      <th style="width: 80px;" class="text-center">Stok</th>
                      <th class="d-none d-lg-table-cell" style="width: 150px;">Dibuat</th>
                      <th class="d-none d-lg-table-cell" style="width: 150px;">Diperbarui</th>
                      <th style="width: 110px;" data-orderable="false" class="text-center">Aksi</th>
                  </tr>
                  </thead>
                  <tbody>
                  @forelse($produks as $produk)
                  <tr>
                      <td class="text-center"></td>
                      <td>{{ $produk->nama_produk }}</td>
                      <td class="d-none d-md-table-cell">{{ Str::limit($produk->deskripsi_produk) }}</td>
                      <td data-order="{{ $produk->harga }}">Rp {{ number_format($produk->harga, 0, ',', '.') }}</td>
                      <td class="text-center">{{ $produk->stok_barang }}</td>
                      <td class="d-none d-lg-table-cell text-center" data-order="{{ $produk->created_at->timestamp }}">{{ $produk->created_at->format('d-m-Y H:i') }}</td>
                      <td class="d-none d-lg-table-cell text-center" data-order="{{ $produk->updated_at->timestamp }}">{{ $produk->updated_at->format('d-m-Y H:i') }}</td>
                      <td class="text-center">
                      <div class="btn-group" role="group">
                          <a href="{{ route('inventaris.edit', $produk->id) }}" class="btn btn-sm btn-warning me-2">
                          <i class="bi bi-pencil text-white"></i>
                          </a>
                          <form action="{{ route('inventaris.destroy', $produk->id) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus produk ini?')" style="display:inline;">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger">
                              <i class="bi bi-trash"></i>
                          </button>
                          </form>
                      </div>
                      </td>
                  </tr>
                  @empty
                  {{-- Baris kosong dengan jumlah kolom yang tepat untuk mencegah error DataTables --}}
                  {{-- Pesan "Data tidak ditemukan" akan ditampilkan oleh DataTables language.emptyTable --}}
                  @endforelse
                  </tbody>
              </table>
            </div>
          </div>
          
          <!-- Card Footer -->
          <div class="card-footer">
            <div class="row align-items-center">
              <div class="col-md-4">
                <div id="entriesContainer"></div>
              </div>
              <div class="col-md-4 text-center">
                <div class="pagination-info" id="paginationInfo"></div>
              </div>
              <div class="col-md-4">
                <nav aria-label="Page navigation" class="float-end" id="paginationContainer">
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Cek apakah tabel memiliki data
    var hasData = $('#inventarisTable tbody tr').length > 0;
    
    // Inisialisasi DataTables
    var table = $('#inventarisTable').DataTable({
        responsive: true,
        processing: true,
        autoWidth: false,
        
        // Pagination untuk DataTables
        info: true,
        paging: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, 100], [5, 10, 25, 50, 100]],
        
        // Pengaturan bahasa Indonesia
        language: {
            "decimal": "",
            "emptyTable": "Tidak ada data yang tersedia di tabel",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ total entri)",
            "infoPostFix": "",
            "thousands": ".",
            "lengthMenu": "Tampilkan _MENU_ entri per halaman",
            "loadingRecords": "Memuat...",
            "processing": "Sedang memproses...",
            "search": "Cari:",
            "searchPlaceholder": "Cari inventaris...",
            "zeroRecords": "Tidak ditemukan data yang sesuai",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir", 
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            },
            "aria": {
                "sortAscending": ": aktifkan untuk mengurutkan kolom ascending",
                "sortDescending": ": aktifkan untuk mengurutkan kolom descending"
            }
        },
        
        // Konfigurasi kolom
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    // Jika tidak ada data, jangan tampilkan nomor
                    if (!hasData) {
                        return '';
                    }
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                targets: -1,
                orderable: false,
                searchable: false
            },
            {
                targets: 3,
                type: 'num',
                render: function(data, type, row, meta) {
                    if (type === 'sort' || type === 'type') {
                        var priceText = data.toString();
                        var numericValue = priceText.replace(/[^\d]/g, '');
                        return numericValue ? parseInt(numericValue) : 0;
                    }
                    return data;
                }
            },
            {
                targets: 4, // Kolom stok
                type: 'num'
            },
            {
                targets: [5, 6], // Kolom tanggal
                type: 'num'
            }
        ],
        
        order: hasData ? [[1, 'asc']] : [],
        
        // Custom DOM layout - hilangkan pagination default
        dom: '<"row"<"col-12"tr>>',
        
        drawCallback: function(settings) {
            // Update nomor urut setelah sorting/paging hanya jika ada data
            if (hasData) {
                var api = this.api();
                var start = api.page.info().start;
                api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                    cell.innerHTML = start + i + 1;
                });
            }
        },
        
        initComplete: function() {
            var api = this.api();
            
            // Setup search box
            var searchHtml = '<div class="d-flex align-items-center">' +
                            '<label class="me-2 mb-0 fw-medium">Cari:</label>' +
                            '<input type="search" class="form-control form-control-sm" placeholder="Cari inventaris..." id="customSearch" style="width: 250px;">' +
                            '</div>';
            $('#searchContainer').html(searchHtml);
            
            $('#customSearch').on('keyup', function() {
                api.search(this.value).draw();
            });
            
            // Setup entries dropdown
            var entriesHtml = '<div class="dataTables_length">' +
                             '<label class="d-flex align-items-center">' +
                             '<span>Tampilkan</span>' +
                             '<select class="form-select form-select-sm mx-2" style="width: auto;">' +
                             '<option value="5">5</option>' +
                             '<option value="10" selected>10</option>' +
                             '<option value="25">25</option>' +
                             '<option value="50">50</option>' +
                             '<option value="100">100</option>' +
                             '</select>' +
                             '<span>data per halaman</span>' +
                             '</label>' +
                             '</div>';
            $('#entriesContainer').html(entriesHtml);
            
            $('#entriesContainer select').on('change', function() {
                api.page.len(parseInt($(this).val())).draw();
            });
            
            // Setup custom pagination info
            function updateInfo() {
                var info = api.page.info();
                var infoText = '';
                
                if (info.recordsTotal === 0) {
                    infoText = 'Tidak ada data untuk ditampilkan';
                } else {
                    infoText = 'Menampilkan ' + (info.start + 1) + ' sampai ' + info.end + ' dari ' + info.recordsTotal + ' data';
                    if (info.recordsFiltered !== info.recordsTotal) {
                        infoText += ' (disaring dari ' + info.recordsDisplay + ' total data)';
                    }
                }
                $('#paginationInfo').html(infoText);
            }
            
            // Setup custom pagination
            function updatePagination() {
                var info = api.page.info();
                
                // Jika tidak ada data, sembunyikan pagination
                if (info.recordsTotal === 0) {
                    $('#paginationContainer').html('');
                    return;
                }
                
                var paginationHtml = '<ul class="pagination pagination-sm mb-0">';
                
                // Previous button
                paginationHtml += '<li class="page-item ' + (info.page === 0 ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" data-page="prev">« Sebelumnya</a>';
                paginationHtml += '</li>';
                
                // Page numbers
                var startPage = Math.max(0, info.page - 2);
                var endPage = Math.min(info.pages - 1, info.page + 2);
                
                if (startPage > 0) {
                    paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="0">1</a></li>';
                    if (startPage > 1) {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                }
                
                for (var i = startPage; i <= endPage; i++) {
                    paginationHtml += '<li class="page-item ' + (i === info.page ? 'active' : '') + '">';
                    paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a>';
                    paginationHtml += '</li>';
                }
                
                if (endPage < info.pages - 1) {
                    if (endPage < info.pages - 2) {
                        paginationHtml += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (info.pages - 1) + '">' + info.pages + '</a></li>';
                }
                
                // Next button
                paginationHtml += '<li class="page-item ' + (info.page === info.pages - 1 ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" data-page="next">Selanjutnya »</a>';
                paginationHtml += '</li>';
                
                paginationHtml += '</ul>';
                $('#paginationContainer').html(paginationHtml);
                
                // Bind pagination events
                $('#paginationContainer .page-link').on('click', function(e) {
                    e.preventDefault();
                    var page = $(this).data('page');
                    if (page === 'prev') {
                        api.page('previous').draw('page');
                    } else if (page === 'next') {
                        api.page('next').draw('page');
                    } else if (typeof page === 'number') {
                        api.page(page).draw('page');
                    }
                });
            }
            
            // Initial update
            updateInfo();
            updatePagination();
            
            // Update on draw
            api.on('draw', function() {
                updateInfo();
                updatePagination();
            });
        }
    });
});

// Fungsi export data
function exportData(type) {
    var table = $('#inventarisTable').DataTable();
    var searchValue = table.search();
    
    const params = new URLSearchParams();
    params.set('type', type);
    params.set('search', searchValue);
    
    var order = table.order();
    if (order.length > 0) {
        var columnIndex = order[0][0];
        var direction = order[0][1];
        
        var sortMap = {
            1: 'name', // Kolom Nama Produk
            3: 'price', // Kolom Harga
            4: 'stock', // Kolom Stok
            5: 'created', // Kolom Dibuat
            6: 'updated' // Kolom Diperbarui
        };
        
        if (sortMap[columnIndex]) {
            params.set('sort', sortMap[columnIndex] + '-' + (direction === 'asc' ? 'asc' : 'desc'));
        }
    }
    
    window.location.href = "{{ route('inventaris.export') }}?" + params.toString();
}
</script>

</body>
</html>