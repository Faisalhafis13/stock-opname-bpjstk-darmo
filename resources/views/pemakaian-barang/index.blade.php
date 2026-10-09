
@extends('layouts.app')

@section('title', 'Pemakaian Barang')
@section('page-title', 'Pemakaian Barang')

@section('content')

<div id="pemakaianContent">

    {{-- ALERT --}}
    <div id="pemakaianAlert">
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

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- HEADER --}}
    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1 fw-semibold">Pemakaian Barang</h5>
                <p class="text-muted mb-0">
                    Catat penggunaan atau pengeluaran barang.
                </p>
            </div>

            <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Pemakaian
            </button>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="content-card">

        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div class="text-muted small" id="pemakaianInfo">
                @if($pemakaianBarangs->total() > 0)
                    Menampilkan
                    <strong>{{ $pemakaianBarangs->firstItem() }}</strong>
                    sampai
                    <strong>{{ $pemakaianBarangs->lastItem() }}</strong>
                    dari
                    <strong>{{ $pemakaianBarangs->total() }}</strong>
                    data
                @else
                    Tidak ada data
                @endif
            </div>

            @include('components.list-controls', [
                'action' => route('pemakaian-barang.index'),
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
                        <th>Keterangan</th>
                        <th>Dicatat Oleh</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pemakaianBarangs as $data)
                        <tr>
                            <td>
                                {{ $pemakaianBarangs->firstItem() + $loop->index }}
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
                                {{ $data->keterangan ?: '-' }}

                                @if($data->pengajuan_id !== null)
                                    <div class="mt-1">
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-link-45deg me-1"></i>
                                            Pengajuan #{{ $data->pengajuan_id }}
                                        </span>
                                    </div>
                                @endif
                            </td>

                            <td>{{ $data->user->name ?? '-' }}</td>

                            <td>
                                @if($data->pengajuan_id === null)
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
                                @else
                                    <span class="badge bg-light text-muted border"
                                        title="Data berasal dari pengajuan">
                                        <i class="bi bi-lock me-1"></i>Otomatis
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-box-arrow-up fs-2 d-block mb-2"></i>
                                Belum ada data pemakaian barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($pemakaianBarangs->hasPages())
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4">
                <div class="text-muted small">
                    Halaman
                    <strong>{{ $pemakaianBarangs->currentPage() }}</strong>
                    dari
                    <strong>{{ $pemakaianBarangs->lastPage() }}</strong>
                    <span class="ms-2">
                        Total {{ $pemakaianBarangs->total() }} data
                    </span>
                </div>

                <div>
                    {{ $pemakaianBarangs->appends(request()->query())->links('components.pagination.limited') }}
                </div>
            </div>
        @endif

    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('pemakaian-barang.store') }}"
                    method="POST" data-pemakaian-ajax>
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>
                            Tambah Pemakaian Barang
                        </h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-form-errors"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Barang <span class="text-danger">*</span>
                                </label>

                                <select name="barang_id" id="barangTambah"
                                    class="form-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}"
                                            data-satuan="{{ $barang->satuan }}">
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal"
                                    class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Jumlah <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="jumlah"
                                    class="form-control" min="1" step="1"
                                    placeholder="Masukkan jumlah" required>

                                <small class="text-muted">
                                    Jumlah tidak boleh melebihi stok tersedia.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan</label>
                                <input type="text" id="satuanTambah"
                                    class="form-control bg-light" value="-" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control"
                                    rows="3"
                                    placeholder="Keterangan pemakaian (opsional)"></textarea>
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
                <form id="formEditPemakaian" method="POST" data-pemakaian-ajax>
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>
                            Edit Pemakaian Barang
                        </h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-form-errors"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Barang <span class="text-danger">*</span>
                                </label>
                                <select name="barang_id" id="editBarang"
                                    class="form-select" required>
                                    <option value="">-- Pilih Barang --</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}"
                                            data-satuan="{{ $barang->satuan }}">
                                            {{ $barang->kode_barang }} - {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="tanggal" id="editTanggal"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Jumlah <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="jumlah" id="editJumlah"
                                    class="form-control" min="1" step="1" required>
                                <small class="text-muted">
                                    Jumlah tidak boleh melebihi stok tersedia.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Satuan</label>
                                <input type="text" id="satuanEdit"
                                    class="form-control bg-light" value="-" readonly>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" id="editKeterangan"
                                    class="form-control" rows="3"
                                    placeholder="Keterangan pemakaian (opsional)"></textarea>
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
                <form id="formHapusPemakaian" method="POST" data-pemakaian-ajax>
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Hapus Data Pemakaian
                        </h5>
                        <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <div class="ajax-form-errors text-start"></div>

                        <i class="bi bi-trash3 text-danger fs-1"></i>
                        <p class="mt-3 mb-1">
                            Yakin ingin menghapus data pemakaian ini?
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
@endsection

@push('styles')
<style>
    #pemakaianContent .pagination {
        margin-bottom: 0;
    }

    #pemakaianContent .pagination .page-link {
        min-width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 9px;
        margin-left: 3px;
        border-radius: 6px;
        font-size: 12px;
        box-shadow: none;
    }

    #pemakaianContent .pagination .page-item.active .page-link {
        color: #fff;
    }

    #pemakaianContent .ajax-form-errors .alert {
        margin-bottom: 1rem;
    }

    @media (max-width: 767.98px) {
        #pemakaianContent .content-card > .d-flex {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    if (window.pemakaianAjaxInitialized) return;
    window.pemakaianAjaxInitialized = true;

    const rootSelector = '#pemakaianContent';
    const baseUrl = @json(url('/pemakaian-barang'));

    function setErrors(form, errors) {
        const container = form.querySelector('.ajax-form-errors');
        if (!container) return;

        container.replaceChildren();

        if (!errors || Object.keys(errors).length === 0) return;

        const alert = document.createElement('div');
        alert.className = 'alert alert-danger';

        const title = document.createElement('strong');
        title.textContent = 'Data belum dapat diproses:';

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

    function showAlert(message, type = 'success') {
        const container = document.querySelector('#pemakaianAlert');
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

    async function fetchPage(url) {
        const response = await fetch(url, {
            headers: { 'Accept': 'text/html' }
        });

        if (!response.ok) {
            throw new Error('Gagal memuat halaman pemakaian barang.');
        }

        return response.text();
    }

    function replaceContent(html) {
        const parsed = new DOMParser().parseFromString(html, 'text/html');
        const newContent = parsed.querySelector(rootSelector);
        const oldContent = document.querySelector(rootSelector);

        if (!newContent || !oldContent) {
            throw new Error('Konten halaman pemakaian tidak ditemukan.');
        }

        oldContent.replaceWith(newContent);
    }

    async function refreshContent(url = window.location.href) {
        const html = await fetchPage(url);
        replaceContent(html);
    }

    function closeModal(form) {
        const modalElement = form.closest('.modal');
        if (!modalElement) return;

        const instance = bootstrap.Modal.getInstance(modalElement);
        if (instance) instance.hide();
    }

    function setButtonLoading(button, loading) {
        if (!button) return;

        if (loading) {
            button.dataset.originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
        } else {
            button.disabled = false;
            if (button.dataset.originalHtml !== undefined) {
                button.innerHTML = button.dataset.originalHtml;
                delete button.dataset.originalHtml;
            }
        }
    }

    // Isi modal edit dan hapus menggunakan data pada tombol.
    document.addEventListener('show.bs.modal', event => {
        const modal = event.target;
        const trigger = event.relatedTarget;

        if (!trigger) return;

        if (modal.id === 'modalEdit') {
            const form = document.querySelector('#formEditPemakaian');

            form.action = `${baseUrl}/${trigger.dataset.id}`;

            document.querySelector('#editBarang').value =
                trigger.dataset.barang || '';

            document.querySelector('#editTanggal').value =
                trigger.dataset.tanggal || '';

            document.querySelector('#editJumlah').value =
                trigger.dataset.jumlah || '';

            document.querySelector('#editKeterangan').value =
                trigger.dataset.keterangan || '';

            const select = document.querySelector('#editBarang');
            document.querySelector('#satuanEdit').value =
                select.selectedOptions[0]?.dataset.satuan || '-';

            form.querySelector('.ajax-form-errors').replaceChildren();
        }

        if (modal.id === 'modalHapus') {
            const form = document.querySelector('#formHapusPemakaian');

            form.action = `${baseUrl}/${trigger.dataset.id}`;

            document.querySelector('#hapusBarang').textContent =
                trigger.dataset.barang || '';

            document.querySelector('#hapusJumlah').textContent =
                `${trigger.dataset.jumlah || ''} ${trigger.dataset.satuan || ''}`;

            form.querySelector('.ajax-form-errors').replaceChildren();
        }

        if (modal.id === 'modalTambah') {
            modal.querySelector('.ajax-form-errors')?.replaceChildren();
        }
    });

    // Perbarui satuan berdasarkan barang yang dipilih.
    document.addEventListener('change', event => {
        const select = event.target;

        if (select.id === 'barangTambah') {
            document.querySelector('#satuanTambah').value =
                select.selectedOptions[0]?.dataset.satuan || '-';
        }

        if (select.id === 'editBarang') {
            document.querySelector('#satuanEdit').value =
                select.selectedOptions[0]?.dataset.satuan || '-';
        }
    });

    // AJAX tambah, edit, dan hapus.
    document.addEventListener('submit', async event => {
        const form = event.target.closest('form[data-pemakaian-ajax]');
        if (!form) return;

        event.preventDefault();

        const button = form.querySelector('[type="submit"]');
        setButtonLoading(button, true);
        form.querySelector('.ajax-form-errors')?.replaceChildren();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const contentType = response.headers.get('content-type') || '';

            // Jika controller sudah mengembalikan JSON.
            if (contentType.includes('application/json')) {
                const data = await response.json();

                if (!response.ok) {
                    setErrors(form, data.errors || {
                        request: [data.message || 'Permintaan gagal diproses.']
                    });
                    return;
                }

                closeModal(form);
                await refreshContent();
                showAlert(data.message || 'Data pemakaian berhasil diproses.');
                return;
            }

            /*
             * Kompatibilitas dengan controller lama yang melakukan redirect.
             * HTML hasil redirect dibaca untuk mengambil pesan dan konten baru.
             */
            if (response.ok) {
                const html = await response.text();
                const parsed = new DOMParser().parseFromString(html, 'text/html');
                const newContent = parsed.querySelector(rootSelector);

                if (!newContent) {
                    throw new Error('Respons server tidak sesuai dengan halaman pemakaian.');
                }

                const successMessage =
                    newContent.querySelector('.alert-success')?.innerText.trim();

                const errorMessage =
                    newContent.querySelector('.alert-danger')?.innerText.trim();

                closeModal(form);
                replaceContent(html);

                if (successMessage) {
                    showAlert(successMessage);
                } else if (errorMessage) {
                    showAlert(errorMessage, 'danger');
                } else {
                    showAlert('Permintaan selesai diproses.');
                }

                return;
            }

            setErrors(form, {
                request: ['Permintaan gagal diproses. Silakan coba lagi.']
            });
        } catch (error) {
            console.error(error);

            setErrors(form, {
                request: [
                    'Terjadi gangguan saat memproses data. Periksa koneksi dan coba lagi.'
                ]
            });
        } finally {
            setButtonLoading(button, false);
        }
    });

    // Pencarian menggunakan form GET.
    document.addEventListener('submit', async event => {
        const form = event.target;

        if (!form.closest(rootSelector)) return;
        if (form.matches('[data-pemakaian-ajax]')) return;
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
            showAlert('Gagal memuat data pemakaian.', 'danger');
        }
    });

    // Pilihan jumlah data per halaman.
    document.addEventListener('change', async event => {
        const select = event.target;

        if (!select.closest(rootSelector) || select.name !== 'per_page') return;

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

    // Navigasi pagination.
    document.addEventListener('click', async event => {
        const link = event.target.closest(`${rootSelector} .pagination a`);
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

    // Tombol kembali/maju browser.
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