@extends('layouts.app')

@section('title', 'Pengajuan Barang')
@section('page-title', 'Pengajuan Barang')

@section('content')
<div id="pengajuan-admin-wrapper">
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
        <h5 class="fw-semibold mb-1">Pengajuan Barang</h5>
        <p class="text-muted mb-0">Kelola pengajuan barang dari pengguna.</p>
    </div>

    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h5 class="fw-semibold mb-1">Daftar Pengajuan</h5>
                <p class="text-muted mb-0">Periksa, setujui, atau tolak pengajuan.</p>
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

                <button class="btn btn-sm btn-primary" type="submit">
                    <i class="bi bi-search"></i> Cari
                </button>
            </form>
        </div>

        <div class="small text-muted mb-3">
            Menampilkan {{ $pengajuans->firstItem() ?? 0 }}–{{ $pengajuans->lastItem() ?? 0 }}
            dari {{ $pengajuans->total() }} data
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">No</th>
                        <th>Pengaju</th>
                        <th>Barang</th>
                        <th class="text-end">Jumlah</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $index => $pengajuan)
                        <tr>
                            <td class="text-center">
                                {{ $pengajuans->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $pengajuan->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $pengajuan->user->email ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $pengajuan->barang->nama_barang ?? '-' }}</div>
                                <small class="text-muted">{{ $pengajuan->barang->kode_barang ?? '-' }}</small>
                            </td>
                            <td class="text-end">
                                {{ number_format($pengajuan->jumlah, 2, ',', '.') }}
                                {{ $pengajuan->barang->satuan ?? '' }}
                            </td>
                            <td>
                                <span title="{{ $pengajuan->keterangan }}"
                                      class="text-truncate d-inline-block" style="max-width:200px">
                                    {{ $pengajuan->keterangan ?: '-' }}
                                </span>
                            </td>
                            <td>
                                {{ $pengajuan->created_at?->format('d/m/Y') ?? '-' }}
                                @if($pengajuan->created_at)
                                    <br><small class="text-muted">{{ $pengajuan->created_at->format('H:i') }}</small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($pengajuan->status === 'menunggu')
                                    <span class="badge bg-warning text-dark">Menunggu</span>
                                @elseif($pengajuan->status === 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($pengajuan->status === 'ditolak')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-secondary">{{ $pengajuan->status ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($pengajuan->status === 'menunggu')
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-outline-success"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalSetujui{{ $pengajuan->id }}"
                                                title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalTolak{{ $pengajuan->id }}"
                                                title="Tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                @elseif($pengajuan->status === 'disetujui')
                                    <span class="small text-success">Selesai</span>
                                @else
                                    <span class="small text-danger">Ditolak</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox fs-2 text-secondary"></i>
                                <div class="fw-semibold mt-2">Belum ada pengajuan barang</div>
                                <small class="text-muted">Data pengajuan akan muncul di sini.</small>
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

    @foreach($pengajuans as $pengajuan)
        @if($pengajuan->status === 'menunggu')
            <div class="modal fade" id="modalSetujui{{ $pengajuan->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form class="ajax-action-form" method="POST"
                              action="{{ route('pengajuan.approve', $pengajuan->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">Setujui Pengajuan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="bg-light border rounded p-3 mb-3">
                                    <div class="mb-2"><small class="text-muted">Pengaju</small>
                                        <div class="fw-semibold">{{ $pengajuan->user->name ?? '-' }}</div>
                                    </div>
                                    <div class="mb-2"><small class="text-muted">Barang</small>
                                        <div class="fw-semibold">{{ $pengajuan->barang->nama_barang ?? '-' }}</div>
                                    </div>
                                    <div><small class="text-muted">Jumlah</small>
                                        <div class="fw-semibold">
                                            {{ number_format($pengajuan->jumlah, 2, ',', '.') }}
                                            {{ $pengajuan->barang->satuan ?? '' }}
                                        </div>
                                    </div>
                                </div>
                                <label class="form-label">Catatan Admin (opsional)</label>
                                <textarea name="catatan_admin" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-lg me-1"></i> Setujui
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalTolak{{ $pengajuan->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form class="ajax-action-form" method="POST"
                              action="{{ route('pengajuan.reject', $pengajuan->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title text-danger">Tolak Pengajuan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>Yakin ingin menolak pengajuan dari
                                    <strong>{{ $pengajuan->user->name ?? '-' }}</strong>
                                    untuk barang
                                    <strong>{{ $pengajuan->barang->nama_barang ?? '-' }}</strong>?
                                </p>
                                <label class="form-label">Alasan / Catatan</label>
                                <textarea name="catatan_admin" class="form-control" rows="3"
                                          placeholder="Masukkan alasan penolakan (opsional)"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection

@push('styles')
<style>
    #pengajuan-admin-wrapper .table th { white-space: nowrap; font-size: 13px; }
    #pengajuan-admin-wrapper .table td { font-size: 14px; }
    #pengajuan-admin-wrapper .modal-content { border: 0; border-radius: 12px; }
    #pengajuan-admin-wrapper .form-control,
    #pengajuan-admin-wrapper .form-select { border-radius: 8px; }
    #pengajuan-admin-wrapper .ajax-filter { max-width: 440px; }
    #pengajuan-admin-wrapper .ajax-filter input { min-width: 180px; }
    #pengajuan-admin-wrapper .ajax-filter select { width: 110px; }
    #pengajuan-admin-wrapper .is-loading { opacity: .6; pointer-events: none; }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const wrapperId = 'pengajuan-admin-wrapper';
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
            if (!response.ok) throw new Error('Gagal memuat data pengajuan.');

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const next = doc.getElementById(wrapperId);
            if (!next) throw new Error('Konten pengajuan tidak ditemukan.');

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
                if (String(value).trim() !== '') url.searchParams.set(key, value);
                else url.searchParams.delete(key);
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

        const original = submit?.innerHTML;
        if (submit) {
            submit.disabled = true;
            submit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';
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
                const errors = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'Permintaan gagal diproses.');
                throw new Error(errors);
            }

            const modalElement = form.closest('.modal');
            if (modalElement) bootstrap.Modal.getOrCreateInstance(modalElement).hide();

            await loadPage(location.href, false);
            showMessage(data.message || 'Berhasil diproses.');
        } catch (error) {
            showMessage(error.message, 'danger');
        } finally {
            if (submit) {
                submit.disabled = false;
                submit.innerHTML = original;
            }
        }
    });

    document.addEventListener('click', event => {
        const link = event.target.closest(`#${wrapperId} .pagination a`);
        if (!link || link.target === '_blank' || event.ctrlKey || event.metaKey) return;
        event.preventDefault();
        loadPage(link.href);
    });

    document.addEventListener('change', event => {
        const select = event.target.closest(`#${wrapperId} .ajax-filter select`);
        if (select) select.form.requestSubmit();
    });

    window.addEventListener('popstate', () => loadPage(location.href, false));
})();
</script>
@endpush