
@extends('layouts.app')

@section('title', 'Pengajuan')
@section('page-title', 'Pengajuan Barang')

@section('content')
<div id="pengajuan-user-wrapper">
    <div id="ajax-alerts">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="fw-semibold mb-1">Pengajuan Barang</h5>
                <p class="text-muted mb-0">Ajukan kebutuhan barang yang diperlukan.</p>
            </div>

            <button type="button" class="btn btn-primary"
                    data-bs-toggle="modal" data-bs-target="#modalPengajuan">
                <i class="bi bi-plus-lg me-1"></i> Ajukan Barang
            </button>
        </div>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h5 class="fw-semibold mb-1">Riwayat Pengajuan</h5>
                <p class="text-muted mb-0">Daftar barang yang pernah diajukan.</p>
            </div>

            <form class="ajax-filter d-flex flex-wrap gap-2"
                  method="GET" action="{{ route('pengajuan.index') }}">
                <input type="search" name="search" value="{{ $search }}"
                       class="form-control form-control-sm"
                       placeholder="Cari pengajuan..." aria-label="Cari pengajuan">

                <select name="per_page" class="form-select form-select-sm"
                        aria-label="Jumlah data per halaman">
                    @foreach([10, 25, 50, 100] as $jumlah)
                        <option value="{{ $jumlah }}" @selected($perPage == $jumlah)>
                            {{ $jumlah }} data
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i> Cari
                </button>
            </form>
        </div>

        <div class="small text-muted mb-3">
            Menampilkan {{ $pengajuans->firstItem() ?? 0 }}–{{ $pengajuans->lastItem() ?? 0 }}
            dari {{ $pengajuans->total() }} data
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Barang</th>
                        <th class="text-end">Jumlah</th>
                        <th>Keterangan</th>
                        <th>Tanggal Pengajuan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $index => $pengajuan)
                        <tr>
                            <td class="text-center">
                                {{ $pengajuans->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $pengajuan->barang->nama_barang ?? '-' }}
                                </div>
                                <small class="text-muted">
                                    {{ $pengajuan->barang->kode_barang ?? '-' }}
                                </small>
                            </td>
                            <td class="text-end">
                                <span class="fw-semibold">
                                    {{ number_format($pengajuan->jumlah, 2, ',', '.') }}
                                </span>
                                <small class="text-muted">
                                    {{ $pengajuan->barang->satuan ?? '' }}
                                </small>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block"
                                      style="max-width:260px"
                                      title="{{ $pengajuan->keterangan }}">
                                    {{ $pengajuan->keterangan ?: '-' }}
                                </span>
                            </td>
                            <td>
                                {{ $pengajuan->created_at?->format('d/m/Y') ?? '-' }}
                                @if($pengajuan->created_at)
                                    <br>
                                    <small class="text-muted">
                                        {{ $pengajuan->created_at->format('H:i') }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-secondary"></i>
                                <div class="fw-semibold mt-2">Belum ada pengajuan barang</div>
                                <small class="text-muted">
                                    Klik tombol Ajukan Barang untuk mengirim pengajuan.
                                </small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuans->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $pengajuans->appends(request()->query())->links('components.pagination.limited') }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalPengajuan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form class="ajax-action-form" method="POST"
                      action="{{ route('pengajuan.store') }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Ajukan Barang</h5>
                        <button type="button" class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="barang_id" class="form-label">Barang</label>
                            <select name="barang_id" id="barang_id"
                                    class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}">
                                        {{ $barang->nama_barang }}
                                        @if($barang->kode_barang)
                                            — {{ $barang->kode_barang }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" id="jumlah"
                                   class="form-control" min="0.01" step="0.01"
                                   placeholder="Masukkan jumlah barang" required>
                        </div>

                        <div class="mb-0">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="keterangan"
                                      class="form-control" rows="4"
                                      placeholder="Keterangan pengajuan (opsional)"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i> Kirim Pengajuan
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
    #pengajuan-user-wrapper .table th {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
    }

    #pengajuan-user-wrapper .table td { font-size: 14px; }

    #pengajuan-user-wrapper .modal-content {
        border: 0;
        border-radius: 12px;
    }

    #pengajuan-user-wrapper .form-control,
    #pengajuan-user-wrapper .form-select {
        border-radius: 8px;
    }

    #pengajuan-user-wrapper .ajax-filter { max-width: 440px; }

    #pengajuan-user-wrapper .ajax-filter input { min-width: 180px; }

    #pengajuan-user-wrapper .ajax-filter select { width: 110px; }

    #pengajuan-user-wrapper.is-loading {
        opacity: .6;
        pointer-events: none;
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const wrapperId = 'pengajuan-user-wrapper';
    const wrapper = () => document.getElementById(wrapperId);

    function showMessage(message, type = 'success') {
        const alerts = wrapper()?.querySelector('#ajax-alerts');
        if (!alerts) return;

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.setAttribute('role', 'alert');
        alert.append(document.createTextNode(message));

        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'btn-close';
        close.setAttribute('data-bs-dismiss', 'alert');
        alert.append(close);

        alerts.replaceChildren(alert);
    }

    async function loadPage(url, push = true) {
        const current = wrapper();
        if (!current) return;

        current.classList.add('is-loading');

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Gagal memuat riwayat pengajuan.');

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const next = doc.getElementById(wrapperId);

            if (!next) throw new Error('Konten riwayat pengajuan tidak ditemukan.');

            current.replaceWith(next);

            if (push) history.pushState({}, '', url);
        } catch (error) {
            showMessage(error.message, 'danger');
        } finally {
            wrapper()?.classList.remove('is-loading');
        }
    }

    document.addEventListener('submit', async event => {
        const filter = event.target.closest(`#${wrapperId} .ajax-filter`);

        if (filter) {
            event.preventDefault();

            const url = new URL(filter.action, location.origin);

            new FormData(filter).forEach((value, key) => {
                if (String(value).trim() !== '') {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            });

            url.searchParams.delete('page');
            await loadPage(url.toString());
            return;
        }

        const form = event.target.closest(`#${wrapperId} .ajax-action-form`);
        if (!form) return;

        event.preventDefault();

        const submit = form.querySelector('[type="submit"]');
        if (submit?.disabled) return;

        const originalText = submit?.innerHTML;

        if (submit) {
            submit.disabled = true;
            submit.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
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
                const message = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Pengajuan gagal dikirim.');

                throw new Error(message);
            }

            const modalElement = form.closest('.modal');

            if (modalElement) {
                bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            }

            await loadPage(location.href, false);
            showMessage(data.message || 'Pengajuan berhasil dikirim.');

        } catch (error) {
            showMessage(error.message, 'danger');
        } finally {
            if (submit) {
                submit.disabled = false;
                submit.innerHTML = originalText;
            }
        }
    });

    document.addEventListener('click', event => {
        const link = event.target.closest(`#${wrapperId} .pagination a`);

        if (!link || link.target === '_blank' || event.ctrlKey || event.metaKey) {
            return;
        }

        event.preventDefault();
        loadPage(link.href);
    });

    document.addEventListener('change', event => {
        const select = event.target.closest(`#${wrapperId} .ajax-filter select`);

        if (select) select.form.requestSubmit();
    });

    window.addEventListener('popstate', () => {
        loadPage(location.href, false);
    });
})();
</script>
@endpush