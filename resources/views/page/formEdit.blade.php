<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inventaris Produk</title>

  {{-- Bootstrap CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  {{-- Bootstrap Icons --}}
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<main class="py-4">
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card border-primary">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Edit Produk</h5>
            <a href="{{ route('data.inventaris') }}" class="btn btn-sm btn-secondary">
              <i class="bi bi-arrow-left"></i> Kembali
            </a>
          </div>

          <form method="POST" action="{{ route('inventaris.update', $produk->id) }}" id="editInventarisForm">
            @csrf
            @method('PUT')

            <div class="card-body">
              <div class="mb-3">
                <label for="nama_produk" class="form-label">Nama Produk</label>
                <input type="text" class="form-control @error('nama_produk') is-invalid @enderror"
                       id="nama_produk" name="nama_produk"
                       value="{{ old('nama_produk', $produk->nama_produk) }}" 
                       required minlength="3" maxlength="100">
                @error('nama_produk')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="nama-error" style="display: none;">
                  Nama produk minimal 3 karakter
                </div>
              </div>

              <div class="mb-3">
                <label for="deskripsi_produk" class="form-label">Deskripsi Produk</label>
                <textarea class="form-control @error('deskripsi_produk') is-invalid @enderror"
                          id="deskripsi_produk" name="deskripsi_produk" rows="3"
                          maxlength="500">{{ old('deskripsi_produk', $produk->deskripsi_produk) }}</textarea>
                @error('deskripsi_produk')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" class="form-control @error('harga') is-invalid @enderror"
                         id="harga" name="harga" min="0" step="100"
                         value="{{ old('harga', intval($produk->harga)) }}" required>
                </div>
                @error('harga')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="harga-error" style="display: none;">
                  Harga minimal Rp 100
                </div>
              </div>

              <div class="mb-3">
                <label for="stok_barang" class="form-label">Stok Barang</label>
                <input type="number" class="form-control @error('stok_barang') is-invalid @enderror"
                       id="stok_barang" name="stok_barang" min="0"
                       value="{{ old('stok_barang', $produk->stok_barang) }}" required>
                @error('stok_barang')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="invalid-feedback" id="stok-error" style="display: none;">
                  Stok tidak boleh negatif
                </div>
              </div>
            </div>

            <div class="card-footer text-end">
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Perbarui Data
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editInventarisForm');
    const namaInput = document.getElementById('nama_produk');
    const hargaInput = document.getElementById('harga');
    const stokInput = document.getElementById('stok_barang');
    
    const namaError = document.getElementById('nama-error');
    const hargaError = document.getElementById('harga-error');
    const stokError = document.getElementById('stok-error');

    namaInput.addEventListener('input', validateNama);
    hargaInput.addEventListener('input', validateHarga);
    stokInput.addEventListener('input', validateStok);

    form.addEventListener('submit', function(e) {
        const isNamaValid = validateNama();
        const isHargaValid = validateHarga();
        const isStokValid = validateStok();
        
        if (!isNamaValid || !isHargaValid || !isStokValid) {
            e.preventDefault();
            
            if (!isNamaValid) {
                namaInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                namaInput.focus();
            } else if (!isHargaValid) {
                hargaInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                hargaInput.focus();
            } else if (!isStokValid) {
                stokInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                stokInput.focus();
            }
        }
    });

    // Fungsi validasi
    function validateNama() {
        const value = namaInput.value.trim();
        
        if (!value) {
            showError(namaInput, namaError, 'Nama produk wajib diisi');
            return false;
        } else {
            clearError(namaInput, namaError);
            return true;
        }
    }

    function validateHarga() {
        const value = parseFloat(hargaInput.value);
        
        if (isNaN(value)) {
            showError(hargaInput, hargaError, 'Harga wajib diisi');
            return false;
        } else if (value < 0) {
            showError(hargaInput, hargaError, 'Harga minimal Rp 0');
            return false;
        } else {
            clearError(hargaInput, hargaError);
            return true;
        }
    }

    function validateStok() {
        const value = parseInt(stokInput.value);
        
        if (isNaN(value)) {
            showError(stokInput, stokError, 'Stok wajib diisi');
            return false;
        } else if (value < 0) {
            showError(stokInput, stokError, 'Stok tidak boleh negatif');
            return false;
        } else {
            clearError(stokInput, stokError);
            return true;
        }
    }

    function showError(input, errorElement, message) {
        input.classList.add('is-invalid');
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }

    function clearError(input, errorElement) {
        input.classList.remove('is-invalid');
        errorElement.style.display = 'none';
    }

    validateNama();
    validateHarga();
    validateStok();
});
</script>

</body>
</html>