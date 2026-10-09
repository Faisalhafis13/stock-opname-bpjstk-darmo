
@extends('layouts.app')

@section('title', 'Data Barang')
@section('page-title', 'Data Barang')

@section('content')

<div id="barangContent">

    <div id="barangAlert"></div>

    {{-- HEADER --}}
    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1 fw-semibold">Data Barang</h5>
                <p class="text-muted mb-0">
                    Kelola data master barang yang digunakan dalam sistem.
                </p>
            </div>

            <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i> Tambah Barang
            </button>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="content-card">

        <div class="d-flex justify-content-between align-items-center mb-3 gap-3">
            <div class="text-muted small">
                @if($barangs->total() > 0)
                    Menampilkan <strong>{{ $barangs->firstItem() }}</strong>
                    sampai <strong>{{ $barangs->lastItem() }}</strong>
                    dari <strong>{{ $barangs->total() }}</strong> data
                @else
                    Tidak ada data
                @endif
            </div>

            @include('components.list-controls', [
                'action' => route('barang.index'),
                'perPage' => $perPage,
            ])
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th>Keterangan</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($barangs as $barang)
                        <tr>
                            <td>{{ $barangs->firstItem() + $loop->index }}</td>
                            <td><span class="fw-semibold">{{ $barang->kode_barang }}</span></td>
                            <td>{{ $barang->nama_barang }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $barang->kategori->nama ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $barang->satuan }}</td>
                            <td>{{ $barang->keterangan ?: '-' }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button type="button"
                                        class="btn btn-sm btn-warning"
                                        title="Edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit"
                                        data-id="{{ $barang->id }}"
                                        data-kategori="{{ $barang->kategori_barang_id }}"
                                        data-kode="{{ $barang->kode_barang }}"
                                        data-nama="{{ $barang->nama_barang }}"
                                        data-satuan="{{ $barang->satuan }}"
                                        data-keterangan="{{ $barang->keterangan }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        title="Hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapus"
                                        data-id="{{ $barang->id }}"
                                        data-nama="{{ $barang->nama_barang }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
                                Belum ada data barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barangs->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Halaman <strong>{{ $barangs->currentPage() }}</strong>
                    dari <strong>{{ $barangs->lastPage() }}</strong>
                </div>
                <div>
                    {{ $barangs->links('components.pagination.limited') }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('barang.store') }}" method="POST" data-barang-ajax>
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-form-errors"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                                <select name="kategori_barang_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategoriBarangs as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                                <input type="text" name="kode_barang" class="form-control"
                                    placeholder="Contoh: BRG-001" required>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input type="text" name="nama_barang" class="form-control"
                                    placeholder="Masukkan nama barang" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input type="text" name="satuan" class="form-control"
                                    placeholder="Contoh: Pcs" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3"
                                    placeholder="Keterangan tambahan (opsional)"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <form id="formEditBarang" method="POST" data-barang-ajax>
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>Edit Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-form-errors"></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Kategori Barang <span class="text-danger">*</span></label>
                                <select id="editKategori" name="kategori_barang_id" class="form-select" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategoriBarangs as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kode Barang <span class="text-danger">*</span></label>
                                <input id="editKode" type="text" name="kode_barang" class="form-control" required>
                            </div>

                            <div class="col-md-8">
                                <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                                <input id="editNama" type="text" name="nama_barang" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Satuan <span class="text-danger">*</span></label>
                                <input id="editSatuan" type="text" name="satuan" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea id="editKeterangan" name="keterangan" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                <form id="formHapusBarang" method="POST" data-barang-ajax>
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>Hapus Barang
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <div class="ajax-form-errors text-start"></div>
                        <i class="bi bi-trash3 text-danger fs-1"></i>
                        <p class="mt-3 mb-1">Apakah yakin ingin menghapus barang?</p>
                        <strong id="hapusNama"></strong>
                        <p class="text-muted small mt-2 mb-0">
                            Data yang sudah digunakan dalam transaksi tidak dapat dihapus.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
    .pagination { margin-bottom: 0; }
    .pagination .page-link {
        min-width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        margin-left: 3px;
        font-size: 13px;
        color: #495057;
    }
    .pagination .page-item.active .page-link { color: #fff; }

    @media (max-width: 767.98px) {
        #barangContent .content-card > .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 12px;
        }
        #barangContent .content-card > .d-flex form { width: 100%; }
        #barangContent .content-card > .d-flex form select { width: 100% !important; }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    // Hindari memasang event listener berulang ketika konten tabel diperbarui.
    if (window.barangAjaxInitialized) return;
    window.barangAjaxInitialized = true;

    const contentSelector = '#barangContent';

    function showAlert(message, type = 'success') {
        const container = document.querySelector('#barangAlert');
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

        if (!response.ok) throw new Error('Gagal memperbarui daftar barang.');

        const html = await response.text();
        const parsed = new DOMParser().parseFromString(html, 'text/html');
        const freshContent = parsed.querySelector(contentSelector);
        const currentContent = document.querySelector(contentSelector);

        if (freshContent && currentContent) {
            currentContent.replaceWith(freshContent);
        }
    }

    // Isi modal edit berdasarkan tombol yang ditekan.
    document.addEventListener('show.bs.modal', event => {
        if (event.target.id === 'modalEdit') {
            const button = event.relatedTarget;
            if (!button) return;

            const form = document.querySelector('#formEditBarang');
            form.action = @json(url('/barang')) + '/' + button.dataset.id;

            document.querySelector('#editKategori').value = button.dataset.kategori || '';
            document.querySelector('#editKode').value = button.dataset.kode || '';
            document.querySelector('#editNama').value = button.dataset.nama || '';
            document.querySelector('#editSatuan').value = button.dataset.satuan || '';
            document.querySelector('#editKeterangan').value = button.dataset.keterangan || '';
            form.querySelector('.ajax-form-errors').replaceChildren();
        }

        if (event.target.id === 'modalHapus') {
            const button = event.relatedTarget;
            if (!button) return;

            document.querySelector('#formHapusBarang').action =
                @json(url('/barang')) + '/' + button.dataset.id;

            document.querySelector('#hapusNama').textContent = button.dataset.nama || '';
            document.querySelector('#formHapusBarang .ajax-form-errors').replaceChildren();
        }

        if (event.target.matches('.modal')) {
            const errors = event.target.querySelectorAll('.ajax-form-errors');
            errors.forEach(element => element.replaceChildren());
        }
    });

    // Submit tambah, edit, dan hapus dengan AJAX.
    document.addEventListener('submit', async event => {
        const form = event.target.closest('form[data-barang-ajax]');
        if (!form) return;

        event.preventDefault();

        const submitButton = form.querySelector('[type="submit"]');
        const originalButtonText = submitButton?.innerHTML;

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML =
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
                if (response.status === 422 && data.errors) {
                    showErrors(form, data.errors);
                } else {
                    showErrors(form, {
                        request: [data.message || 'Permintaan gagal diproses.']
                    });
                }
                return;
            }

            const modalElement = form.closest('.modal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement);

            if (modalInstance) modalInstance.hide();

            const message = data.message || 'Data barang berhasil diproses.';
            await refreshContent();
            showAlert(message, 'success');
        } catch (error) {
            showErrors(form, {
                request: ['Terjadi gangguan saat menghubungi server. Silakan coba lagi.']
            });
            console.error(error);
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        }
    });

    // Pencarian, jumlah data, dan pagination tanpa reload halaman.
    document.addEventListener('submit', async event => {
        const form = event.target;
        if (!form.closest(contentSelector) || form.matches('[data-barang-ajax]')) return;
        if (form.method.toLowerCase() !== 'get') return;

        event.preventDefault();
        const url = new URL(form.action || window.location.href);
        new FormData(form).forEach((value, key) => url.searchParams.set(key, value));

        try {
            await refreshContent(url.toString());
            history.pushState({}, '', url.toString());
        } catch (error) {
            console.error(error);
            showAlert('Gagal memuat data barang.', 'danger');
        }
    });

    document.addEventListener('change', async event => {
        const select = event.target;
        if (!select.closest(contentSelector) || select.name !== 'per_page') return;

        const form = select.closest('form');
        if (form) {
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
            showAlert('Gagal memuat data barang.', 'danger');
        }
    });

    document.addEventListener('click', async event => {
        const link = event.target.closest(`${contentSelector} .pagination a`);
        if (!link) return;

        event.preventDefault();

        try {
            await refreshContent(link.href);
            history.pushState({}, '', link.href);
        } catch (error) {
            console.error(error);
            showAlert('Gagal membuka halaman data barang.', 'danger');
        }
    });

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