
@extends('layouts.app')

@section('title', 'Input Stok Opname')
@section('page-title', 'Input Stok Opname')

@section('content')

<div id="stokOpnameContent">

    <div id="stokOpnameAlert" aria-live="polite">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- PILIH PERIODE --}}
    <div class="content-card mb-4">
        <div class="mb-3">
            <h5 class="mb-1 fw-semibold">Pilih Periode Stok Opname</h5>
            <p class="text-muted mb-0">
                Pilih periode untuk melihat dan mengelola data stok opname.
            </p>
        </div>

        <form method="GET" action="{{ route('stok-opname.index') }}"
            id="pilihPeriodeForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-8">
                    <label for="periode_id" class="form-label">
                        Periode Stok Opname
                    </label>

                    <select name="periode_id" id="periode_id" class="form-select">
                        <option value="">-- Pilih Periode --</option>

                        @foreach($periodes as $item)
                            <option value="{{ $item->id }}"
                                @selected($periode?->id == $item->id)>
                                {{ $item->periode }}
                                - {{ $item->tanggal_opname?->format('d/m/Y') ?? '-' }}
                                ({{ ucfirst($item->status) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>
    </div>

    @if($periode)

        {{-- INFORMASI DAN AKSI PERIODE --}}
        <div class="content-card mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1 fw-semibold">
                        Stok Opname Periode {{ $periode->periode }}
                    </h5>

                    <p class="text-muted mb-0">
                        Tanggal Opname:
                        <strong>{{ $periode->tanggal_opname?->format('d F Y') ?? '-' }}</strong>
                        <span class="mx-2">|</span>
                        Status:

                        @if($periode->status === 'draft')
                            <span class="badge bg-warning text-dark">Draft</span>
                        @else
                            <span class="badge bg-success">Selesai</span>
                        @endif
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    @if($periode->status === 'draft')

                        <form method="POST"
                            action="{{ route('stok-opname.generate') }}"
                            data-stok-ajax>
                            @csrf
                            <input type="hidden" name="periode_stok_opname_id"
                                value="{{ $periode->id }}">

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Generate Data Opname
                            </button>
                        </form>

                        @if($details->count() > 0)
                            <form method="POST"
                                action="{{ route('stok-opname.selesai', $periode->id) }}"
                                data-stok-ajax
                                data-confirm="Apakah periode ini sudah benar dan ingin diselesaikan? Setelah selesai, data akan terkunci.">
                                @csrf

                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Selesaikan Opname
                                </button>
                            </form>
                        @endif

                    @else
                        <span class="badge bg-secondary px-3 py-2">
                            <i class="bi bi-lock me-1"></i>Periode Terkunci
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- TABEL DATA --}}
        <div class="content-card">
            <div class="mb-3">
                <h5 class="mb-1 fw-semibold">Data Stok Opname</h5>
                <p class="text-muted mb-0">
                    Nilai stok dihitung berdasarkan saldo awal dan transaksi pada bulan opname.
                </p>
            </div>

            @if($details->count() > 0)

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th class="text-end">Saldo Awal</th>
                                <th class="text-end">Penambahan</th>
                                <th class="text-end">Pemakaian</th>
                                <th class="text-end">Saldo Akhir</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Total Nilai</th>
                                @if($periode->status === 'draft')
                                    <th class="text-center">Aksi</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($details as $detail)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $detail->barang?->kode_barang ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $detail->barang?->nama_barang ?? 'Barang tidak ditemukan' }}
                                        </div>
                                        <small class="text-muted">
                                            {{ $detail->barang?->satuan ?? '-' }}
                                        </small>
                                    </td>

                                    <td class="text-end">
                                        {{ number_format($detail->saldo_awal, 0, ',', '.') }}
                                    </td>

                                    <td class="text-end text-success">
                                        +{{ number_format($detail->penambahan, 0, ',', '.') }}
                                    </td>

                                    <td class="text-end text-danger">
                                        -{{ number_format($detail->pemakaian, 0, ',', '.') }}
                                    </td>

                                    <td class="text-end fw-bold">
                                        {{ number_format($detail->saldo_akhir, 0, ',', '.') }}
                                    </td>

                                    <td class="text-end">
                                        Rp {{ number_format($detail->harga_satuan, 2, ',', '.') }}
                                    </td>

                                    <td class="text-end fw-semibold">
                                        Rp {{ number_format($detail->total_nilai, 2, ',', '.') }}
                                    </td>

                                    @if($periode->status === 'draft')
                                        <td class="text-center">
                                            <button type="button"
                                                class="btn btn-sm btn-warning"
                                                title="Edit Harga Satuan"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editStokModal{{ $detail->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot class="table-light">
                            <tr>
                                <th colspan="8" class="text-end">
                                    Total Nilai Stok
                                </th>
                                <th class="text-end fw-bold">
                                    Rp {{ number_format($details->sum('total_nilai'), 2, ',', '.') }}
                                </th>
                                @if($periode->status === 'draft')
                                    <th></th>
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>

            @else

                <div class="empty-state text-center py-5">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-clipboard-x"></i>
                    </div>

                    <h5 class="fw-semibold">Data Stok Opname Belum Ada</h5>

                    <p class="text-muted mb-3">
                        Generate data untuk menghitung stok berdasarkan transaksi.
                    </p>

                    @if($periode->status === 'draft')
                        <form method="POST"
                            action="{{ route('stok-opname.generate') }}"
                            data-stok-ajax>
                            @csrf
                            <input type="hidden" name="periode_stok_opname_id"
                                value="{{ $periode->id }}">

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Generate Data Opname
                            </button>
                        </form>
                    @endif
                </div>

            @endif
        </div>

        {{-- MODAL EDIT HARGA: DILETAKKAN DI LUAR TABEL --}}
        @if($periode->status === 'draft')
            @foreach($details as $detail)
                <div class="modal fade"
                    id="editStokModal{{ $detail->id }}"
                    tabindex="-1"
                    aria-hidden="true">

                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST"
                                action="{{ route('stok-opname.update', $detail->id) }}"
                                data-stok-ajax>
                                @csrf
                                @method('PUT')

                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="bi bi-pencil-square me-2"></i>
                                        Edit Harga Satuan
                                    </h5>
                                    <button type="button" class="btn-close"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="ajax-form-errors"></div>

                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Nama Barang</label>
                                            <input type="text"
                                                class="form-control bg-light"
                                                value="{{ $detail->barang?->nama_barang ?? '-' }}"
                                                readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Saldo Akhir</label>
                                            <input type="text"
                                                class="form-control bg-light"
                                                value="{{ number_format($detail->saldo_akhir, 0, ',', '.') }}"
                                                readonly>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Total Nilai Saat Ini</label>
                                            <input type="text"
                                                class="form-control bg-light"
                                                value="Rp {{ number_format($detail->total_nilai, 2, ',', '.') }}"
                                                readonly>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">
                                                Harga Satuan <span class="text-danger">*</span>
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number"
                                                    name="harga_satuan"
                                                    class="form-control"
                                                    min="0"
                                                    step="0.01"
                                                    value="{{ $detail->harga_satuan }}"
                                                    required>
                                            </div>

                                            <small class="text-muted">
                                                Total nilai dihitung otomatis oleh server:
                                                saldo akhir × harga satuan.
                                            </small>
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
            @endforeach
        @endif

    @else

        <div class="content-card">
            <div class="empty-state text-center py-5">
                <div class="empty-state-icon mb-3">
                    <i class="bi bi-calendar-x"></i>
                </div>

                <h5 class="fw-semibold">Belum Ada Periode Stok Opname</h5>

                <p class="text-muted">
                    Buat periode terlebih dahulu sebelum melakukan stok opname.
                </p>

                <a href="{{ route('periode-stok-opname.index') }}"
                    class="btn btn-primary">
                    <i class="bi bi-calendar-plus me-1"></i>Buat Periode
                </a>
            </div>
        </div>

    @endif

</div>

@push('styles')
<style>
    #stokOpnameContent .table {
        font-size: 14px;
    }

    #stokOpnameContent .table th {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
    }

    #stokOpnameContent .empty-state {
        min-height: 260px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    #stokOpnameContent .empty-state-icon {
        width: 70px;
        height: 70px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f3f5;
        color: #6c757d;
        font-size: 32px;
    }

    #stokOpnameContent .ajax-form-errors .alert {
        margin-bottom: 12px;
    }

    @media (max-width: 767.98px) {
        #stokOpnameContent .table-responsive table {
            min-width: 1100px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    if (window.stokOpnameAjaxInitialized) return;
    window.stokOpnameAjaxInitialized = true;

    const wrapperId = 'stokOpnameContent';

    function showAlert(message, type = 'success') {
        const wrapper = document.getElementById(wrapperId);
        const target = wrapper?.querySelector('#stokOpnameAlert');
        if (!target) return;

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
        target.replaceChildren(alert);
    }

    function showErrors(form, data) {
        const target = form.querySelector('.ajax-form-errors');

        if (!target) {
            showAlert(data.message || 'Permintaan gagal diproses.', 'danger');
            return;
        }

        target.replaceChildren();

        const messages = [];

        if (data.errors) {
            Object.values(data.errors).forEach(items => {
                items.forEach(item => messages.push(item));
            });
        }

        if (!messages.length && data.message) {
            messages.push(data.message);
        }

        if (!messages.length) {
            messages.push('Terjadi kesalahan. Silakan coba kembali.');
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
        target.appendChild(alert);
    }

    async function replaceContent(url, message = null, type = 'success') {
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        });

        if (!response.ok) {
            throw new Error('Gagal memuat ulang data stok opname.');
        }

        const html = await response.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newContent = doc.getElementById(wrapperId);
        const currentContent = document.getElementById(wrapperId);

        if (!newContent || !currentContent) {
            throw new Error('Bagian stok opname tidak ditemukan pada respons.');
        }

        currentContent.replaceWith(newContent);

        if (message) showAlert(message, type);
    }

    document.addEventListener('submit', async event => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) return;

        if (form.id === 'pilihPeriodeForm') {
            event.preventDefault();

            const select = form.querySelector('[name="periode_id"]');
            const url = new URL(form.action, window.location.origin);

            if (select?.value) {
                url.searchParams.set('periode_id', select.value);
            } else {
                url.searchParams.delete('periode_id');
            }

            try {
                await replaceContent(url.toString());
                window.history.pushState({}, '', url.toString());
            } catch (error) {
                window.location.href = url.toString();
            }

            return;
        }

        if (!form.matches('[data-stok-ajax]')) return;

        event.preventDefault();

        const confirmation = form.dataset.confirm;
        if (confirmation && !window.confirm(confirmation)) return;

        const button = form.querySelector('[type="submit"]');
        const originalHtml = button?.innerHTML;

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
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || data.success === false) {
                showErrors(form, data);
                return;
            }

            const modal = form.closest('.modal');

            if (modal && window.bootstrap) {
                bootstrap.Modal.getOrCreateInstance(modal).hide();
            }

            const url = data.redirect_url || window.location.href;

            await replaceContent(
                url,
                data.message || 'Data berhasil diproses.'
            );

            window.history.pushState({}, '', url);
        } catch (error) {
            showErrors(form, {
                message: error.message ||
                    'Terjadi kesalahan koneksi. Silakan coba kembali.'
            });
        } finally {
            if (button && button.isConnected) {
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        }
    });

    window.addEventListener('popstate', async () => {
        try {
            await replaceContent(window.location.href);
        } catch (error) {
            window.location.reload();
        }
    });
})();
</script>
@endpush

@endsection