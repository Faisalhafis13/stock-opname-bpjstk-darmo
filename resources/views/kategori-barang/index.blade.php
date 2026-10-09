
@extends('layouts.app')

@section('title', 'Kategori Barang')
@section('page-title', 'Kategori Barang')

@section('content')
<div id="kategoriContent">

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3">
            <div>
                <h5 class="mb-1 fw-semibold">Kategori Barang</h5>
                <p class="text-muted mb-0">
                    Kelola kategori barang yang tersedia pada sistem.
                </p>
            </div>

            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
            </button>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="content-card">

        <div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
            <div class="text-muted small">
                @if($kategoriBarangs->total() > 0)
                    Menampilkan <strong>{{ $kategoriBarangs->firstItem() }}</strong>
                    sampai <strong>{{ $kategoriBarangs->lastItem() }}</strong>
                    dari <strong>{{ $kategoriBarangs->total() }}</strong> data
                @else
                    Tidak ada data
                @endif
            </div>

            @include('components.list-controls', [
                'action' => route('kategori-barang.index'),
                'perPage' => $perPage,
            ])
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($kategoriBarangs as $index => $kategori)
                        <tr>
                            <td>{{ $kategoriBarangs->firstItem() + $index }}</td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $kategori->kode }}
                                </span>
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $kategori->nama }}</span>
                            </td>

                            <td>{{ $kategori->keterangan ?: '-' }}</td>

                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-warning"
                                        title="Edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit"
                                        data-id="{{ $kategori->id }}"
                                        data-kode="{{ $kategori->kode }}"
                                        data-nama="{{ $kategori->nama }}"
                                        data-keterangan="{{ $kategori->keterangan }}"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        title="Hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapus"
                                        data-id="{{ $kategori->id }}"
                                        data-nama="{{ $kategori->nama }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada kategori barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($kategoriBarangs->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4 gap-3 flex-wrap">
                <div class="text-muted small">
                    Halaman <strong>{{ $kategoriBarangs->currentPage() }}</strong>
                    dari <strong>{{ $kategoriBarangs->lastPage() }}</strong>
                </div>

                <div>
                    {{ $kategoriBarangs->links('components.pagination.limited') }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('kategori-barang.store') }}"
                      method="POST" data-ajax>
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
                        </h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-errors alert alert-danger d-none"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Kode Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="kode" class="form-control"
                                       maxlength="20" placeholder="Contoh: ATK" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama" class="form-control"
                                       maxlength="100"
                                       placeholder="Contoh: Alat Tulis Kantor" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control"
                                          rows="3"
                                          placeholder="Keterangan tambahan (opsional)"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            <span class="button-label">Simpan</span>
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
                <form id="formEditKategori" method="POST" data-ajax>
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>Edit Kategori
                        </h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-errors alert alert-danger d-none"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    Kode Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="kode" class="form-control"
                                       maxlength="20" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">
                                    Nama Kategori <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nama" class="form-control"
                                       maxlength="100" required>
                                <div class="invalid-feedback"></div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control"
                                          rows="3"></textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            <span class="button-label">Simpan Perubahan</span>
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
                <form id="formHapusKategori" method="POST" data-ajax>
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Hapus Kategori
                        </h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <div class="ajax-errors alert alert-danger d-none text-start"></div>
                        <i class="bi bi-trash3 text-danger fs-1"></i>
                        <p class="mt-3 mb-1">
                            Apakah yakin ingin menghapus kategori ini?
                        </p>
                        <strong id="namaKategoriHapus"></strong>
                        <p class="text-muted small mt-2 mb-0">
                            Kategori yang masih digunakan oleh data barang
                            tidak dapat dihapus.
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>
                            <span class="button-label">Hapus</span>
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
    #kategoriContent .pagination {
        margin-bottom: 0;
    }

    #kategoriContent .pagination .page-link {
        min-width: 38px;
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 3px;
        border-radius: 6px;
        font-size: 13px;
        color: #495057;
    }

    #kategoriContent .pagination .page-item.active .page-link {
        color: #fff;
    }

    #kategoriContent .is-loading {
        opacity: .65;
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    // Mencegah listener terpasang berulang kali.
    if (window.kategoriAjaxInitialized) return;
    window.kategoriAjaxInitialized = true;

    const endpoint = @json(route('kategori-barang.index'));
    const wrapperSelector = '#kategoriContent';

    function getWrapper() {
        return document.querySelector(wrapperSelector);
    }

    function showNotice(message, type = 'success') {
        const wrapper = getWrapper();
        if (!wrapper) return;

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
        close.setAttribute('aria-label', 'Tutup');

        alert.append(icon, text, close);
        wrapper.prepend(alert);

        window.setTimeout(() => {
            if (alert.isConnected) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 5000);
    }

    function clearErrors(form) {
        form.querySelectorAll('.is-invalid').forEach(input => {
            input.classList.remove('is-invalid');
        });

        form.querySelectorAll('.invalid-feedback').forEach(element => {
            element.textContent = '';
        });

        const summary = form.querySelector('.ajax-errors');
        if (summary) {
            summary.classList.add('d-none');
            summary.textContent = '';
        }
    }

    function displayErrors(form, errors, message) {
        clearErrors(form);

        let messages = [];

        Object.entries(errors || {}).forEach(([field, fieldMessages]) => {
            const input = form.querySelector(`[name="${field}"]`);
            const list = Array.isArray(fieldMessages)
                ? fieldMessages
                : [fieldMessages];

            messages.push(...list);

            if (input) {
                input.classList.add('is-invalid');

                const feedback = input.parentElement.querySelector(
                    '.invalid-feedback'
                );

                if (feedback) {
                    feedback.textContent = list[0] || '';
                }
            }
        });

        const summary = form.querySelector('.ajax-errors');

        if (summary) {
            summary.textContent = '';

            const heading = document.createElement('strong');
            heading.textContent = message || 'Periksa kembali data yang dimasukkan.';
            summary.appendChild(heading);

            if (messages.length) {
                const listElement = document.createElement('ul');
                listElement.className = 'mb-0 mt-2';

                messages.forEach(item => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    listElement.appendChild(li);
                });

                summary.appendChild(listElement);
            }

            summary.classList.remove('d-none');
        } else {
            showNotice(message || messages.join(' '), 'danger');
        }
    }

    async function refreshContent(url = window.location.href) {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            credentials: 'same-origin'
        });

        if (!response.ok) {
            throw new Error('Gagal memperbarui tabel kategori barang.');
        }

        const html = await response.text();
        const documentBaru = new DOMParser().parseFromString(html, 'text/html');
        const contentBaru = documentBaru.querySelector(wrapperSelector);
        const contentLama = getWrapper();

        if (!contentBaru || !contentLama) {
            throw new Error('Area tabel kategori barang tidak ditemukan.');
        }

        contentLama.innerHTML = contentBaru.innerHTML;
    }

    function closeModal(form) {
        const modalElement = form.closest('.modal');
        if (!modalElement) return;

        const modal = bootstrap.Modal.getInstance(modalElement);

        if (modal) {
            modal.hide();
        }
    }

    function setLoading(form, loading) {
        const button = form.querySelector('[type="submit"]');
        if (!button) return;

        if (loading) {
            button.dataset.originalHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
        } else {
            button.disabled = false;

            if (button.dataset.originalHtml) {
                button.innerHTML = button.dataset.originalHtml;
                delete button.dataset.originalHtml;
            }
        }
    }

    // Isi modal edit.
    document.addEventListener('show.bs.modal', event => {
        const trigger = event.relatedTarget;
        const modal = event.target;

        if (!trigger) return;

        if (modal.id === 'modalEdit') {
            const form = modal.querySelector('form');

            form.action = `${endpoint}/${encodeURIComponent(trigger.dataset.id)}`;
            form.elements.namedItem('kode').value = trigger.dataset.kode || '';
            form.elements.namedItem('nama').value = trigger.dataset.nama || '';
            form.elements.namedItem('keterangan').value =
                trigger.dataset.keterangan || '';

            clearErrors(form);
        }

        if (modal.id === 'modalHapus') {
            const form = modal.querySelector('form');

            form.action = `${endpoint}/${encodeURIComponent(trigger.dataset.id)}`;

            modal.querySelector('#namaKategoriHapus').textContent =
                trigger.dataset.nama || '';

            clearErrors(form);
        }

        if (modal.id === 'modalTambah') {
            clearErrors(modal.querySelector('form'));
        }
    });

    // AJAX untuk tambah, edit, dan hapus.
    document.addEventListener('submit', async event => {
        const form = event.target.closest('form[data-ajax]');
        if (!form) return;

        event.preventDefault();
        clearErrors(form);
        setLoading(form, true);

        try {
            const response = await fetch(form.action, {
                method: form.querySelector('input[name="_method"]')?.value
                    || form.method
                    || 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: new FormData(form),
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (response.status === 422 && result.errors) {
                displayErrors(
                    form,
                    result.errors,
                    result.message || 'Validasi gagal.'
                );
                return;
            }

            if (!response.ok || result.success === false) {
                throw new Error(result.message || 'Permintaan gagal diproses.');
            }

            const message = result.message || 'Data berhasil diproses.';
            closeModal(form);

            // Tunggu modal menutup sebelum mengganti isi area.
            await new Promise(resolve => window.setTimeout(resolve, 200));

            await refreshContent(window.location.href);
            showNotice(message, 'success');
        } catch (error) {
            showNotice(error.message || 'Terjadi kesalahan pada server.', 'danger');
        } finally {
            setLoading(form, false);
        }
    });

    // AJAX untuk form GET seperti pencarian dan jumlah data per halaman.
    document.addEventListener('submit', async event => {
        const form = event.target;
        const wrapper = getWrapper();

        if (
            !wrapper ||
            !wrapper.contains(form) ||
            form.matches('[data-ajax]') ||
            (form.method || 'get').toLowerCase() !== 'get'
        ) {
            return;
        }

        event.preventDefault();

        const url = new URL(form.action || window.location.href);
        new FormData(form).forEach((value, key) => {
            url.searchParams.set(key, value);
        });

        try {
            await refreshContent(url.toString());
            window.history.pushState({}, '', url.toString());
        } catch (error) {
            showNotice(error.message, 'danger');
        }
    });

    // AJAX pagination.
    document.addEventListener('click', async event => {
        const link = event.target.closest(`${wrapperSelector} .pagination a`);
        if (!link) return;

        event.preventDefault();

        try {
            await refreshContent(link.href);
            window.history.pushState({}, '', link.href);
        } catch (error) {
            showNotice(error.message, 'danger');
        }
    });

    // Dukungan tombol Back/Forward browser.
    window.addEventListener('popstate', async () => {
        try {
            await refreshContent(window.location.href);
        } catch (error) {
            showNotice(error.message, 'danger');
        }
    });
})();
</script>
@endpush