
@extends('layouts.app')

@section('title', 'Laporan Stok Opname')
@section('page-title', 'Laporan Stok Opname')

@section('content')

<div id="laporanOpnameContent">

    {{-- ALERT --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- FILTER --}}
    <div class="content-card mb-4">
        <div class="mb-3">
            <h5 class="mb-1 fw-semibold">Filter Laporan Stok Opname</h5>
            <p class="text-muted mb-0">
                Pilih periode dan cari barang untuk melihat hasil stok opname.
            </p>
        </div>

        <form
            method="GET"
            action="{{ route('laporan-stok-opname.index') }}"
            data-opname-filter
            id="filterLaporanOpnameForm"
        >
            <input type="hidden" name="per_page" value="{{ $perPage }}">

            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="periode_id" class="form-label">
                        Periode Stok Opname
                    </label>

                    <select name="periode_id" id="periode_id" class="form-select">
                        <option value="">Semua Periode</option>

                        @foreach($periodes as $periode)
                            <option
                                value="{{ $periode->id }}"
                                @selected((string) $periodeId === (string) $periode->id)
                            >
                                {{ $periode->periode }}
                                — {{ $periode->tanggal_opname->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Barang</label>
                    <input
                        type="search"
                        name="search"
                        id="search"
                        class="form-control"
                        placeholder="Kode atau nama barang..."
                        value="{{ $search }}"
                        autocomplete="off"
                    >
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Tampilkan
                        </button>

                        <a
                            href="{{ route('laporan-stok-opname.index') }}"
                            class="btn btn-outline-secondary"
                            data-opname-reset
                            title="Reset Filter"
                            aria-label="Reset filter"
                        >
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- DATA LAPORAN --}}
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
            <div>
                <h5 class="mb-1 fw-semibold">Laporan Stok Opname</h5>

                <p class="text-muted mb-0">
                    @if($periodeId)
                        @php
                            $periodeTerpilih = $periodes->firstWhere('id', $periodeId);
                        @endphp

                        Periode:
                        <strong>{{ $periodeTerpilih?->periode ?? '-' }}</strong>

                        @if($periodeTerpilih?->tanggal_opname)
                            <span>
                                ({{ $periodeTerpilih->tanggal_opname->format('d/m/Y') }})
                            </span>
                        @endif
                    @else
                        Seluruh periode stok opname
                    @endif
                </p>
            </div>

<a
    href="{{ route('laporan-stok-opname.export', request()->query()) }}"
    class="btn btn-success"
>
    <i class="bi bi-file-earmark-excel"></i>
    Export Excel
</a>
        </div>

        {{-- TOOLBAR --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="text-muted small" aria-live="polite">
                @if(method_exists($details, 'total'))
                    @if($details->total() > 0)
                        Menampilkan
                        <strong>{{ $details->firstItem() }}</strong>
                        sampai
                        <strong>{{ $details->lastItem() }}</strong>
                        dari
                        <strong>{{ $details->total() }}</strong>
                        data
                    @else
                        Tidak ada data
                    @endif
                @else
                    Menampilkan
                    <strong>{{ $details->count() }}</strong>
                    data
                @endif
            </div>

            @if(method_exists($details, 'total'))
                <form
                    method="GET"
                    action="{{ route('laporan-stok-opname.index') }}"
                    class="d-flex align-items-center gap-2"
                    data-opname-filter
                >
                    <input type="hidden" name="periode_id" value="{{ $periodeId }}">
                    <input type="hidden" name="search" value="{{ $search }}">

                    <label for="per_page" class="text-muted small mb-0">
                        Tampilkan
                    </label>

                    <select
                        name="per_page"
                        id="per_page"
                        class="form-select form-select-sm"
                        style="width: 85px;"
                        data-opname-per-page
                        aria-label="Jumlah data per halaman"
                    >
                        @foreach([10, 25, 50, 100] as $option)
                            <option
                                value="{{ $option }}"
                                @selected((int) $perPage === $option)
                            >
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>

                    <span class="text-muted small">data</span>
                </form>
            @endif
        </div>

        {{-- TABLE --}}
        @if($details->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelLaporanOpname">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">No</th>
                            <th>Periode</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th class="text-end">Saldo Awal</th>
                            <th class="text-end">Penambahan</th>
                            <th class="text-end">Pemakaian</th>
                            <th class="text-end">Saldo Akhir</th>
                            <th class="text-end">Harga Satuan</th>
                            <th class="text-end">Total Nilai</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($details as $detail)
                            <tr>
                                <td class="text-center">
                                    @if(method_exists($details, 'firstItem'))
                                        {{ $details->firstItem() + $loop->index }}
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $detail->periode?->periode ?? '-' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $detail->periode?->tanggal_opname?->format('d/m/Y') ?? '-' }}
                                    </small>
                                </td>

                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $detail->barang?->kode_barang ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $detail->barang?->nama_barang ?? '-' }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $detail->barang?->satuan ?? '-' }}
                                    </small>
                                </td>

                                <td class="text-end">
                                    {{ number_format((float) $detail->saldo_awal, 0, ',', '.') }}
                                </td>

                                <td class="text-end text-success">
                                    +{{ number_format((float) $detail->penambahan, 0, ',', '.') }}
                                </td>

                                <td class="text-end text-danger">
                                    -{{ number_format((float) $detail->pemakaian, 0, ',', '.') }}
                                </td>

                                <td class="text-end fw-bold">
                                    {{ number_format((float) $detail->saldo_akhir, 0, ',', '.') }}
                                </td>

                                <td class="text-end text-nowrap">
                                    Rp {{ number_format((float) $detail->harga_satuan, 2, ',', '.') }}
                                </td>

                                <td class="text-end text-nowrap fw-semibold">
                                    Rp {{ number_format((float) $detail->total_nilai, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    {{-- Total untuk data yang sedang ditampilkan --}}
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="9" class="text-end">
                                Total Nilai pada Halaman Ini
                            </th>
                            <th class="text-end text-nowrap">
                                Rp {{ number_format((float) $details->sum('total_nilai'), 2, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if(method_exists($details, 'hasPages') && $details->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $details->links('components.pagination.limited') }}
                </div>
            @endif
        @else
            <div class="empty-state py-5">
                <div class="empty-state-icon mb-3">
                    <i class="bi bi-clipboard-x"></i>
                </div>

                <h5 class="fw-semibold">Data Stok Opname Belum Ditemukan</h5>

                <p class="text-muted mb-0">
                    Coba pilih periode lain atau ubah kata pencarian.
                </p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    #laporanOpnameContent .content-card {
        min-width: 0;
    }

    #laporanOpnameContent .table th {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
    }

    #laporanOpnameContent .table td {
        font-size: 14px;
    }

    #laporanOpnameContent .table tfoot th {
        padding-top: 14px;
        padding-bottom: 14px;
    }

    #laporanOpnameContent .empty-state {
        min-height: 230px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    #laporanOpnameContent .empty-state-icon {
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

    #laporanOpnameContent .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
        gap: 3px;
    }

    #laporanOpnameContent.is-loading {
        opacity: .65;
        pointer-events: none;
        transition: opacity .15s ease;
    }

    @media (max-width: 767.98px) {
        #laporanOpnameContent .table {
            min-width: 1200px;
        }

        #laporanOpnameContent .pagination {
            justify-content: flex-start;
        }
    }

    @media print {
        .sidebar,
        .topbar,
        .btn,
        #laporanOpnameContent form,
        #laporanOpnameContent .pagination {
            display: none !important;
        }

        .main-wrapper {
            margin-left: 0 !important;
        }

        .content-wrapper {
            padding: 0 !important;
        }

        #laporanOpnameContent .content-card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        #laporanOpnameContent .table {
            min-width: 0 !important;
        }

        body {
            background: #fff !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    if (window.laporanOpnameAjaxInitialized) {
        return;
    }

    window.laporanOpnameAjaxInitialized = true;

    function getContent() {
        return document.getElementById('laporanOpnameContent');
    }

    function setLoading(loading) {
        const content = getContent();

        if (!content) return;

        content.classList.toggle('is-loading', loading);
        content.setAttribute('aria-busy', loading ? 'true' : 'false');
    }

    function buildUrl(form) {
        const url = new URL(form.action, window.location.origin);
        const formData = new FormData(form);

        url.search = '';

        for (const [key, value] of formData.entries()) {
            if (String(value).trim() !== '') {
                url.searchParams.set(key, value);
            }
        }

        return url;
    }

    async function loadPage(url, options = {}) {
        setLoading(true);

        try {
            const response = await fetch(url.toString(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) {
                throw new Error('Laporan gagal dimuat.');
            }

            const html = await response.text();
            const parsed = new DOMParser().parseFromString(html, 'text/html');
            const newContent = parsed.getElementById('laporanOpnameContent');
            const currentContent = getContent();

            if (!newContent || !currentContent) {
                throw new Error('Konten laporan tidak ditemukan.');
            }

            currentContent.replaceWith(newContent);

            if (options.updateHistory !== false) {
                window.history.pushState({}, '', url.toString());
            }

            if (options.scrollToTop) {
                newContent.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        } catch (error) {
            console.error(error);
            window.location.assign(url.toString());
        } finally {
            setLoading(false);
        }
    }

    // Submit filter dan pengaturan jumlah data melalui AJAX.
    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form[data-opname-filter]');

        if (!form) return;

        event.preventDefault();

        const url = buildUrl(form);

        // Filter baru selalu dimulai dari halaman pertama.
        url.searchParams.delete('page');

        loadPage(url);
    });

    // Pilih periode atau jumlah data langsung memperbarui hasil.
    document.addEventListener('change', function (event) {
        if (
            event.target.matches('#periode_id')
            || event.target.matches('[data-opname-per-page]')
        ) {
            const form = event.target.closest('form[data-opname-filter]');

            if (form) {
                form.requestSubmit();
            }
        }
    });

    // Reset filter.
    document.addEventListener('click', function (event) {
        const resetLink = event.target.closest('a[data-opname-reset]');

        if (resetLink) {
            event.preventDefault();

            loadPage(new URL(resetLink.href), {
                scrollToTop: true
            });

            return;
        }

        // Pagination tanpa reload seluruh halaman.
        const paginationLink = event.target.closest(
            '#laporanOpnameContent .pagination a'
        );

        if (paginationLink) {
            event.preventDefault();

            loadPage(new URL(paginationLink.href), {
                scrollToTop: true
            });
        }

        if (event.target.closest('#cetakLaporanOpname')) {
            window.print();
        }
    });

    // Mendukung tombol Back dan Forward browser.
    window.addEventListener('popstate', function () {
        loadPage(new URL(window.location.href), {
            updateHistory: false
        });
    });
})();
</script>
@endpush