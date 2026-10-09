
@extends('layouts.app')

@section('title', 'Periode Stok Opname')
@section('page-title', 'Periode Stok Opname')

@section('content')

<div id="periodeContent">

    <div id="periodeAlert" aria-live="polite">
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
    </div>

    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
            <div>
                <h5 class="mb-1 fw-semibold">Periode Stok Opname</h5>
                <p class="text-muted mb-0">
                    Kelola periode pelaksanaan stok opname.
                </p>
            </div>

            <button type="button" class="btn btn-primary"
                data-bs-toggle="modal" data-bs-target="#modalTambahPeriode">
                <i class="bi bi-plus-lg me-1"></i> Tambah Periode
            </button>
        </div>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-3">
            <div class="text-muted small" id="periodeInfo">
                @if($periodes->total() > 0)
                    Menampilkan <strong>{{ $periodes->firstItem() }}</strong>
                    sampai <strong>{{ $periodes->lastItem() }}</strong>
                    dari <strong>{{ $periodes->total() }}</strong> data
                @else
                    Tidak ada data
                @endif
            </div>

            @include('components.list-controls', [
                'action' => route('periode-stok-opname.index'),
                'perPage' => $perPage,
            ])
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="60">No</th>
                        <th>Periode</th>
                        <th>Tanggal Opname</th>
                        <th>Status</th>
                        <th class="text-center">Data Opname</th>
                        <th>Dibuat Oleh</th>
                        <th>Keterangan</th>
                        <th class="text-center" width="130">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($periodes as $periode)
                        <tr>
                            <td class="text-center">
                                {{ $periodes->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $periode->periode }}</span>
                            </td>

                            <td>
                                {{ $periode->tanggal_opname?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                @if($periode->status === 'selesai')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Selesai
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-pencil me-1"></i>Draft
                                    </span>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-dark border">
                                    {{ $periode->details_count ?? $periode->details()->count() }}
                                </span>
                            </td>

                            <td>{{ $periode->user?->name ?? '-' }}</td>

                            <td>{{ $periode->keterangan ?: '-' }}</td>

                            <td class="text-center">
                                @if($periode->status === 'draft')
                                    <div class="d-flex gap-1 justify-content-center">
                                        <button type="button"
                                            class="btn btn-sm btn-warning"
                                            title="Edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditPeriode{{ $periode->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button type="button"
                                            class="btn btn-sm btn-danger"
                                            title="Hapus"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalHapusPeriode{{ $periode->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted small">
                                        <i class="bi bi-lock-fill me-1"></i>Terkunci
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                Belum ada periode stok opname.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($periodes->hasPages())
            <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mt-4">
                <div class="text-muted small">
                    Halaman <strong>{{ $periodes->currentPage() }}</strong>
                    dari <strong>{{ $periodes->lastPage() }}</strong>
                </div>

                <div>
                    {{ $periodes->withQueryString()->links('components.pagination.limited') }}
                </div>
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH --}}
    <div class="modal fade" id="modalTambahPeriode" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('periode-stok-opname.store') }}"
                    data-periode-ajax>
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-calendar-plus me-2"></i>Tambah Periode Stok Opname
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="ajax-form-errors"></div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Periode <span class="text-danger">*</span></label>
                                <input type="text" name="periode" class="form-control"
                                    maxlength="20" placeholder="Contoh: Januari 2026" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tanggal Opname <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_opname" class="form-control" required>
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

    {{-- MODAL EDIT DAN HAPUS --}}
    @foreach($periodes as $periode)
        @if($periode->status === 'draft')

            <div class="modal fade" id="modalEditPeriode{{ $periode->id }}"
                tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST"
                            action="{{ route('periode-stok-opname.update', $periode->id) }}"
                            data-periode-ajax>
                            @csrf
                            @method('PUT')

                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Periode Stok Opname
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <div class="ajax-form-errors"></div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Periode <span class="text-danger">*</span></label>
                                        <input type="text" name="periode" class="form-control"
                                            maxlength="20" value="{{ $periode->periode }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Opname <span class="text-danger">*</span></label>
                                        <input type="date" name="tanggal_opname" class="form-control"
                                            value="{{ $periode->tanggal_opname?->format('Y-m-d') }}" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Keterangan</label>
                                        <textarea name="keterangan" class="form-control" rows="3">{{ $periode->keterangan }}</textarea>
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

            <div class="modal fade" id="modalHapusPeriode{{ $periode->id }}"
                tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form method="POST"
                            action="{{ route('periode-stok-opname.destroy', $periode->id) }}"
                            data-periode-ajax>
                            @csrf
                            @method('DELETE')

                            <div class="modal-header">
                                <h5 class="modal-title text-danger">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Hapus Periode
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body text-center py-4">
                                <i class="bi bi-trash3 text-danger fs-1"></i>
                                <p class="mt-3 mb-1">Yakin ingin menghapus periode ini?</p>
                                <strong>{{ $periode->periode }}</strong>
                                <p class="text-muted small mt-2 mb-0">
                                    Periode yang sudah memiliki data opname tidak dapat dihapus.
                                </p>
                                <div class="ajax-form-errors text-start mt-3"></div>
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

        @endif
    @endforeach

</div>

@push('styles')
<style>
    #periodeContent .pagination {
        margin-bottom: 0;
    }

    #periodeContent .pagination .page-link {
        min-width: 38px;
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 3px;
        border-radius: 6px;
        font-size: 13px;
    }

    #periodeContent .table {
        font-size: 14px;
    }

    #periodeContent .table th {
        white-space: nowrap;
        font-weight: 600;
        color: #495057;
    }

    #periodeContent .ajax-form-errors .alert {
        margin-bottom: 12px;
    }

    @media (max-width: 767.98px) {
        #periodeContent .content-card > .d-flex {
            align-items: flex-start !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const wrapperId = 'periodeContent';

    if (window.periodeAjaxInitialized) return;
    window.periodeAjaxInitialized = true;

    function showAlert(message, type = 'success') {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) return;

        const alertBox = wrapper.querySelector('#periodeAlert');
        if (!alertBox) return;

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
        alertBox.replaceChildren(alert);
    }

    function showFormErrors(form, data) {
        const box = form.querySelector('.ajax-form-errors');
        if (!box) return;

        box.replaceChildren();

        const messages = [];
        if (data.errors) {
            Object.values(data.errors).forEach(items => {
                items.forEach(message => messages.push(message));
            });
        }

        if (!messages.length && data.message) {
            messages.push(data.message);
        }

        if (!messages.length) {
            messages.push('Permintaan gagal diproses. Silakan coba kembali.');
        }

        const alert = document.createElement('div');
        alert.className = 'alert alert-danger';

        const list = document.createElement('ul');
        list.className = 'mb-0';

        messages.forEach(message => {
            const item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });

        alert.appendChild(list);
        box.appendChild(alert);
    }

    async function refreshContent(message, type = 'success') {
        const currentWrapper = document.getElementById(wrapperId);
        if (!currentWrapper) return;

        const response = await fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) {
            showAlert(message + ' Namun, daftar gagal diperbarui. Muat ulang halaman untuk melihat perubahan.', 'warning');
            return;
        }

        const html = await response.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newWrapper = doc.getElementById(wrapperId);

        if (!newWrapper) {
            showAlert(message + ' Silakan muat ulang halaman untuk melihat perubahan.', 'warning');
            return;
        }

        currentWrapper.replaceWith(newWrapper);
        showAlert(message, type);
    }

    document.addEventListener('submit', async event => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) return;

        if (form.hasAttribute('data-periode-ajax')) {
            event.preventDefault();

            const submitButton = form.querySelector('[type="submit"]');
            const originalButtonHtml = submitButton?.innerHTML;

            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || data.success === false) {
                    showFormErrors(form, data);
                    return;
                }

                const modalElement = form.closest('.modal');
                if (modalElement && window.bootstrap) {
                    bootstrap.Modal.getOrCreateInstance(modalElement).hide();
                }

                await refreshContent(data.message || 'Data berhasil diproses.');
            } catch (error) {
                showFormErrors(form, {
                    message: 'Terjadi kesalahan koneksi. Periksa koneksi dan coba kembali.'
                });
            } finally {
                if (submitButton && submitButton.isConnected) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonHtml;
                }
            }

            return;
        }

        // Form pencarian dan jumlah data dari komponen list-controls.
        if (
            form.closest('#' + wrapperId) &&
            form.method.toLowerCase() === 'get'
        ) {
            event.preventDefault();

            const url = new URL(form.action || window.location.href, window.location.origin);
            new FormData(form).forEach((value, key) => {
                if (value !== '') url.searchParams.set(key, value);
                else url.searchParams.delete(key);
            });
            url.searchParams.delete('page');

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    }
                });

                const html = await response.text();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newWrapper = doc.getElementById(wrapperId);
                const currentWrapper = document.getElementById(wrapperId);

                if (!response.ok || !newWrapper || !currentWrapper) {
                    window.location.href = url.toString();
                    return;
                }

                currentWrapper.replaceWith(newWrapper);
                window.history.pushState({}, '', url.toString());
            } catch (error) {
                window.location.href = url.toString();
            }
        }
    });

    document.addEventListener('click', async event => {
        const link = event.target.closest('#' + wrapperId + ' .pagination a');
        if (!link) return;

        event.preventDefault();

        try {
            const response = await fetch(link.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newWrapper = doc.getElementById(wrapperId);
            const currentWrapper = document.getElementById(wrapperId);

            if (!response.ok || !newWrapper || !currentWrapper) {
                window.location.href = link.href;
                return;
            }

            currentWrapper.replaceWith(newWrapper);
            window.history.pushState({}, '', link.href);
        } catch (error) {
            window.location.href = link.href;
        }
    });

    window.addEventListener('popstate', async () => {
        try {
            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newWrapper = doc.getElementById(wrapperId);
            const currentWrapper = document.getElementById(wrapperId);

            if (response.ok && newWrapper && currentWrapper) {
                currentWrapper.replaceWith(newWrapper);
            } else {
                window.location.reload();
            }
        } catch (error) {
            window.location.reload();
        }
    });
})();
</script>
@endpush

@endsection