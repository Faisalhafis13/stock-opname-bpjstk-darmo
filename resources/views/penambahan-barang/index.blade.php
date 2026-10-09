
@extends('layouts.app')

@section('title', 'Penambahan Barang')
@section('page-title', 'Penambahan Barang')

@section('content')

<div id="penambahanContent">

    {{-- ALERT --}}
    <div id="penambahanAlert">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- HEADER --}}
    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1 fw-semibold">Penambahan Barang</h5>
                <p class="text-muted mb-0">
                    Catat setiap penambahan atau penerimaan barang.
                </p>
            </div>

            <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Penambahan
            </button>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="content-card">

        <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
            <div class="text-muted small" id="penambahanInfo">
                @if($penambahanBarangs->total() > 0)
                    Menampilkan
                    <strong>{{ $penambahanBarangs->firstItem() }}</strong>
                    sampai
                    <strong>{{ $penambahanBarangs->lastItem() }}</strong>
                    dari
                    <strong>{{ $penambahanBarangs->total() }}</strong>
                    data
                @else
                    Tidak ada data
                @endif
            </div>

            @include('components.list-controls', [
                'action' => route('penambahan-barang.index'),
                'perPage' => $perPage,
            ])
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Total Harga</th>
                        <th>Keterangan</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($penambahanBarangs as $data)
                        <tr>
                            <td>
                                {{ $penambahanBarangs->firstItem() + $loop->index }}
                            </td>

                            <td>
                                {{ $data->tanggal->format('d/m/Y') }}
                            </td>

                            <td>
                                <div class="fw-semibold">
                                    {{ $data->barang->nama_barang }}
                                </div>
                                <small class="text-muted">
                                    {{ $data->barang->kode_barang }}
                                </small>
                            </td>

                            <td>
                                <span class="fw-semibold">
                                    {{ number_format($data->jumlah, 0, ',', '.') }}
                                </span>
                                {{ $data->barang->satuan }}
                            </td>

                            <td>
                                Rp {{ number_format($data->harga_satuan, 0, ',', '.') }}
                            </td>

                            <td>
                                <span class="fw-semibold">
                                    Rp {{ number_format($data->total_harga, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>{{ $data->keterangan ?: '-' }}</td>

                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <button type="button"
                                        class="btn btn-sm btn-warning"
                                        title="Edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit"
                                        data-id="{{ $data->id }}"
                                        data-barang="{{ $data->barang_id }}"
                                        data-tanggal="{{ $data->tanggal->format('Y-m-d') }}"
                                        data-jumlah="{{ $data->jumlah }}"
                                        data-harga="{{ $data->harga_satuan }}"
                                        data-keterangan="{{ $data->keterangan }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        title="Hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapus"
                                        data-id="{{ $data->id }}"
                                        data-barang="{{ $data->barang->nama_barang }}"
                                        data-jumlah="{{ number_format($data->jumlah, 0, ',', '.') }}"
                                        data-satuan="{{ $data->barang->satuan }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-box-arrow-in-down fs-2 d-block mb-2"></i>
                                Belum ada data penambahan barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($penambahanBarangs->hasPages())
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
                <div class="text-muted small">
                    Halaman <strong>{{ $penambahanBarangs->currentPage() }}</strong>
                    dari <strong>{{ $penambahanBarangs->lastPage() }}</strong>
                    <span class="ms-2">
                        Total {{ $penambahanBarangs->total() }} data
                    </span>
                </div>

                <div>
                    {{ $penambahanBarangs->appends(request()->query())->links('components.pagination.limited') }}
                </div>
            </div>
        @endif

    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('penambahan-barang.store') }}"
                    method="POST" data-penambahan-ajax>
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>
                            Tambah Penambahan Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-form-errors"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Barang <span class="text-danger">*</span>
                                </label>
                                <select name="barang_id" class="form-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}">
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Jumlah <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="jumlah" id="jumlahTambah"
                                    class="form-control" min="1" step="1"
                                    placeholder="Masukkan jumlah" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Harga Satuan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_satuan" id="hargaTambah"
                                        class="form-control" min="0" step="0.01"
                                        placeholder="0" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Total Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="totalTambah"
                                        class="form-control bg-light" value="0" readonly>
                                </div>
                                <small class="text-muted">
                                    Total harga dihitung otomatis dari jumlah × harga satuan.
                                </small>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"
                                    placeholder="Keterangan tambahan (opsional)"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formEditPenambahan" method="POST" data-penambahan-ajax>
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit Penambahan Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-form-errors"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Barang <span class="text-danger">*</span>
                                </label>
                                <select id="editBarang" name="barang_id"
                                    class="form-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}">
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" id="editTanggal" name="tanggal"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Jumlah <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="editJumlah" name="jumlah"
                                    class="form-control" min="1" step="1" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Harga Satuan <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" id="editHarga" name="harga_satuan"
                                        class="form-control" min="0" step="0.01" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Total Harga</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" id="totalEdit"
                                        class="form-control bg-light" value="0" readonly>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea id="editKeterangan" name="keterangan"
                                    class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    <div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="formHapusPenambahan" method="POST" data-penambahan-ajax>
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Hapus Data
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <div class="ajax-form-errors text-start"></div>
                        <i class="bi bi-trash3 text-danger fs-1"></i>
                        <p class="mt-3 mb-1">
                            Yakin ingin menghapus data penambahan?
                        </p>
                        <strong id="hapusBarang"></strong>
                        <p class="text-muted mt-2 mb-0">
                            Jumlah: <span id="hapusJumlah"></span>
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
    #penambahanContent .pagination {
        margin-bottom: 0;
    }

    #penambahanContent .pagination .page-item {
        margin-left: 3px;
    }

    #penambahanContent .pagination .page-link {
        min-width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 8px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        background: #fff;
        color: #495057;
        font-size: 12px;
        box-shadow: none;
    }

    #penambahanContent .pagination .page-link:hover {
        background: #f8f9fa;
        border-color: #ced4da;
        color: #0d6efd;
    }

    #penambahanContent .pagination .page-item.active .page-link {
        background: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
    }

    #penambahanContent .pagination .page-item.disabled .page-link {
        background: #f1f3f5;
        border-color: #dee2e6;
        color: #adb5bd;
    }

    @media (max-width: 767.98px) {
        #penambahanContent .content-card > .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
        }

        #penambahanContent .content-card > .d-flex form {
            width: 100%;
        }

        #penambahanContent .content-card > .d-flex form select {
            width: 100% !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    if (window.penambahanAjaxInitialized) return;
    window.penambahanAjaxInitialized = true;

    const selector = '#penambahanContent';

    function formatNumber(value) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 2
        }).format(value);
    }

    function calculateTotal(form, outputSelector) {
        const jumlah = parseFloat(form.querySelector('[name="jumlah"]')?.value) || 0;
        const harga = parseFloat(form.querySelector('[name="harga_satuan"]')?.value) || 0;
        const output = form.querySelector(outputSelector);

        if (output) output.value = formatNumber(jumlah * harga);
    }

    function showAlert(message, type = 'success') {
        const container = document.querySelector('#penambahanAlert');
        if (!container) return;

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');

        const icon = document.createElement('i');
        icon.className = type === 'success'
            ? 'bi bi-check-circle me-2'
            : 'bi bi-exclamation-circle me-2';

        const text = document.createElement('span');
        text.textContent = message;

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close';
        close.setAttribute('data-bs-dismiss', 'alert');

        alert.append(icon, text, close);
        container.replaceChildren(alert);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showErrors(form, errors) {
        const container = form.querySelector('.ajax-form-errors');
        if (!container) return;

        container.replaceChildren();

        if (!errors || !Object.keys(errors).length) return;

        const alert = document.createElement('div');
        alert.className = 'alert alert-danger';

        const title = document.createElement('strong');
        title.textContent = 'Periksa kembali data yang diisi:';

        const list = document.createElement('ul');
        list.className = 'mb-0 mt-2';

        Object.values(errors).flat().forEach(message => {
            const item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });

        alert.append(title, list);
        container.appendChild(alert);
    }

    async function refreshContent(url = window.location.href) {
        const response = await fetch(url, {
            headers: { 'Accept': 'text/html' }
        });

        if (!response.ok) {
            throw new Error('Gagal memuat data penambahan barang.');
        }

        const html = await response.text();
        const parsed = new DOMParser().parseFromString(html, 'text/html');
        const fresh = parsed.querySelector(selector);
        const current = document.querySelector(selector);

        if (!fresh || !current) {
            throw new Error('Konten penambahan barang tidak ditemukan.');
        }

        current.replaceWith(fresh);
    }

    // Mengisi modal edit dan hapus.
    document.addEventListener('show.bs.modal', event => {
        const modal = event.target;
        const button = event.relatedTarget;

        if (!button) return;

        if (modal.id === 'modalEdit') {
            const form = document.querySelector('#formEditPenambahan');

            form.action = @json(url('/penambahan-barang')) + '/' + button.dataset.id;

            document.querySelector('#editBarang').value = button.dataset.barang || '';
            document.querySelector('#editTanggal').value = button.dataset.tanggal || '';
            document.querySelector('#editJumlah').value = button.dataset.jumlah || '';
            document.querySelector('#editHarga').value = button.dataset.harga || '';
            document.querySelector('#editKeterangan').value = button.dataset.keterangan || '';

            calculateTotal(form, '#totalEdit');
            form.querySelector('.ajax-form-errors').replaceChildren();
        }

        if (modal.id === 'modalHapus') {
            const form = document.querySelector('#formHapusPenambahan');

            form.action = @json(url('/penambahan-barang')) + '/' + button.dataset.id;

            document.querySelector('#hapusBarang').textContent =
                button.dataset.barang || '';

            document.querySelector('#hapusJumlah').textContent =
                `${button.dataset.jumlah || ''} ${button.dataset.satuan || ''}`;

            form.querySelector('.ajax-form-errors').replaceChildren();
        }

        if (modal.matches('.modal')) {
            modal.querySelectorAll('.ajax-form-errors')
                .forEach(element => element.replaceChildren());
        }
    });

    // Hitung total harga pada modal tambah dan edit.
    document.addEventListener('input', event => {
        const form = event.target.closest('#modalTambah form, #modalEdit form');
        if (!form) return;

        if (
            event.target.name !== 'jumlah' &&
            event.target.name !== 'harga_satuan'
        ) return;

        calculateTotal(
            form,
            form.closest('#modalTambah') ? '#totalTambah' : '#totalEdit'
        );
    });

    // AJAX untuk tambah, edit, dan hapus.
    document.addEventListener('submit', async event => {
        const form = event.target.closest('form[data-penambahan-ajax]');
        if (!form) return;

        event.preventDefault();

        const button = form.querySelector('[type="submit"]');
        const originalText = button?.innerHTML;

        if (button) {
            button.disabled = true;
            button.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                showErrors(form, data.errors || {
                    request: [data.message || 'Permintaan gagal diproses.']
                });
                return;
            }

            const modal = form.closest('.modal');
            if (modal) {
                bootstrap.Modal.getInstance(modal)?.hide();
            }

            await refreshContent();
            showAlert(data.message || 'Data berhasil diproses.');
        } catch (error) {
            console.error(error);
            showErrors(form, {
                request: ['Terjadi gangguan saat menghubungi server. Silakan coba lagi.']
            });
        } finally {
            if (button) {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
    });

    // Pencarian melalui form GET.
    document.addEventListener('submit', async event => {
        const form = event.target;

        if (!form.closest(selector)) return;
        if (form.matches('[data-penambahan-ajax]')) return;
        if (form.method.toLowerCase() !== 'get') return;

        event.preventDefault();

        const url = new URL(form.action || window.location.href);

        new FormData(form).forEach((value, key) => {
            url.searchParams.set(key, value);
        });

        url.searchParams.delete('page');

        try {
            await refreshContent(url.toString());
            history.pushState({}, '', url.toString());
        } catch (error) {
            console.error(error);
            showAlert('Gagal memuat data.', 'danger');
        }
    });

    // Jumlah data per halaman.
    document.addEventListener('change', async event => {
        const select = event.target;

        if (!select.closest(selector) || select.name !== 'per_page') return;

        const form = select.closest('form');

        if (form && form.method.toLowerCase() === 'get') {
            form.requestSubmit();
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set('per_page', select.value);
        url.searchParams.delete('page');

        try {
            await refreshContent(url.toString());
            history.pushState({}, '', url.toString());
        } catch (error) {
            console.error(error);
            showAlert('Gagal mengubah jumlah data.', 'danger');
        }
    });

    // Pagination.
    document.addEventListener('click', async event => {
        const link = event.target.closest(`${selector} .pagination a`);
        if (!link) return;

        event.preventDefault();

        try {
            await refreshContent(link.href);
            history.pushState({}, '', link.href);
        } catch (error) {
            console.error(error);
            showAlert('Gagal membuka halaman data.', 'danger');
        }
    });

    // Navigasi tombol kembali dan maju browser.
    window.addEventListener('popstate', async () => {
        try {
            await refreshContent(window.location.href);
        } catch (error) {
            console.error(error);
        }
    });
})();
</script>
@endpush

@endsection