
@extends('layouts.app')

@section('title', 'Laporan Stok')
@section('page-title', 'Laporan Stok')

@section('content')

<div id="laporanStokContent">

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
            <h5 class="mb-1 fw-semibold">Filter Laporan</h5>
            <p class="text-muted mb-0">
                Cari barang berdasarkan kode, nama, atau kategori.
            </p>
        </div>

        <form
            id="filterLaporanStokForm"
            method="GET"
            action="{{ route('laporan-stok.index') }}"
            data-laporan-filter
        >
            <input type="hidden" name="per_page" value="{{ $perPage }}">

            <div class="row g-3 align-items-end">
                <div class="col-md-5">
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

                <div class="col-md-4">
                    <label for="kategori_id" class="form-label">Kategori Barang</label>
                    <select name="kategori_id" id="kategori_id" class="form-select">
                        <option value="">Semua Kategori</option>

                        @foreach($kategoriBarangs as $kategori)
                            <option
                                value="{{ $kategori->id }}"
                                @selected((string) $kategoriId === (string) $kategori->id)
                            >
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>

                        <a
                            href="{{ route('laporan-stok.index') }}"
                            class="btn btn-outline-secondary"
                            data-laporan-reset
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
                <h5 class="mb-1 fw-semibold">Laporan Stok Barang</h5>
                <p class="text-muted mb-0">
                    Kondisi stok berdasarkan seluruh transaksi penambahan dan pemakaian.
                </p>
            </div>

<a
    href="{{ route('laporan-stok.export', request()->query()) }}"
    class="btn btn-success"
>
    <i class="bi bi-file-earmark-excel"></i>
    Export Excel
</a>
        </div>

        {{-- TOOLBAR --}}
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="text-muted small" aria-live="polite">
                @if($barangs->total() > 0)
                    Menampilkan
                    <strong>{{ $barangs->firstItem() }}</strong>
                    sampai
                    <strong>{{ $barangs->lastItem() }}</strong>
                    dari
                    <strong>{{ $barangs->total() }}</strong>
                    data
                @else
                    Tidak ada data
                @endif
            </div>

            <form
                method="GET"
                action="{{ route('laporan-stok.index') }}"
                class="d-flex align-items-center gap-2"
                data-laporan-filter
            >
                <input type="hidden" name="search" value="{{ $search }}">
                <input type="hidden" name="kategori_id" value="{{ $kategoriId }}">

                <label for="per_page" class="text-muted small mb-0">
                    Tampilkan
                </label>

                <select
                    name="per_page"
                    id="per_page"
                    class="form-select form-select-sm"
                    style="width: 85px;"
                    aria-label="Jumlah data per halaman"
                    data-laporan-per-page
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
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tabelLaporanStok">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-end">Penambahan</th>
                        <th class="text-end">Pemakaian</th>
                        <th class="text-end">Jumlah Stok</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($barangs as $barang)
                        @php
                            $totalPenambahan = (float) ($barang->total_penambahan ?? 0);
                            $totalPemakaian = (float) ($barang->total_pemakaian ?? 0);

                            // Jumlah stok tidak boleh negatif.
                            $jumlahStok = max(0, $totalPenambahan - $totalPemakaian);
                        @endphp

                        <tr>
                            <td class="text-center">
                                {{ $barangs->firstItem() + $loop->index }}
                            </td>

                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $barang->kode_barang }}
                                </span>
                            </td>

                            <td>
                                <span class="fw-semibold">{{ $barang->nama_barang }}</span>
                            </td>

                            <td>{{ $barang->kategori->nama ?? '-' }}</td>
                            <td>{{ $barang->satuan ?? '-' }}</td>

                            <td class="text-end text-success">
                                +{{ number_format($totalPenambahan, 0, ',', '.') }}
                            </td>

                            <td class="text-end text-danger">
                                -{{ number_format($totalPemakaian, 0, ',', '.') }}
                            </td>

                            <td class="text-end">
                                <span class="fw-bold">
                                    {{ number_format($jumlahStok, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($jumlahStok > 0)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Tersedia
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Habis
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-state-icon mb-3">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <h5 class="fw-semibold">Data Barang Tidak Ditemukan</h5>
                                    <p class="text-muted mb-0">
                                        Coba ubah kata pencarian atau pilih kategori lainnya.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($barangs->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $barangs->links('components.pagination.limited') }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
    #laporanStokContent .content-card {
        min-width: 0;
    }

    #laporanStokContent .table th {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
    }

    #laporanStokContent .table td {
        font-size: 14px;
    }

    #laporanStokContent .empty-state {
        min-height: 220px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    #laporanStokContent .empty-state-icon {
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

    #laporanStokContent .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
        gap: 3px;
    }

    #laporanStokContent .pagination .page-link {
        cursor: pointer;
    }

    #laporanStokContent.is-loading {
        opacity: .65;
        pointer-events: none;
        transition: opacity .15s ease;
    }

    @media (max-width: 767.98px) {
        #laporanStokContent .table {
            min-width: 1000px;
        }

        #laporanStokContent .pagination {
            justify-content: flex-start;
        }
    }

    @media print {
        .sidebar,
        .topbar,
        .btn,
        #laporanStokContent form,
        #laporanStokContent .pagination {
            display: none !important;
        }

        .main-wrapper {
            margin-left: 0 !important;
        }

        .content-wrapper {
            padding: 0 !important;
        }

        #laporanStokContent .content-card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        #laporanStokContent .table {
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

    // Mencegah event listener terpasang berulang kali.
    if (window.laporanStokAjaxInitialized) {
        return;
    }

    window.laporanStokAjaxInitialized = true;

    const endpoint = @json(route('laporan-stok.index'));

    function getContent() {
        return document.getElementById('laporanStokContent');
    }

    function setLoading(isLoading) {
        const content = getContent();

        if (!content) return;

        content.classList.toggle('is-loading', isLoading);
        content.setAttribute('aria-busy', isLoading ? 'true' : 'false');
    }

    function buildUrl(form) {
        const url = new URL(form.action || endpoint, window.location.origin);
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
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Gagal memuat laporan stok. Silakan coba kembali.');
            }

            const html = await response.text();
            const parsedDocument = new DOMParser().parseFromString(html, 'text/html');
            const newContent = parsedDocument.getElementById('laporanStokContent');

            if (!newContent) {
                throw new Error('Bagian laporan tidak ditemukan pada respons server.');
            }

            const currentContent = getContent();

            if (!currentContent) {
                window.location.href = url.toString();
                return;
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
            window.location.href = url.toString();
        } finally {
            setLoading(false);
        }
    }

    // Tangani form filter dan jumlah data tanpa reload seluruh halaman.
    document.addEventListener('submit', function (event) {
        const form = event.target.closest('form[data-laporan-filter]');

        if (!form) return;

        event.preventDefault();

        const url = buildUrl(form);

        // Saat mencari atau mengganti jumlah data, mulai lagi dari halaman pertama.
        url.searchParams.delete('page');

        loadPage(url, { scrollToTop: false });
    });

    // Perubahan kategori langsung menerapkan filter.
    document.addEventListener('change', function (event) {
        if (event.target.matches('#kategori_id')) {
            const form = event.target.closest('form');

            if (form) {
                form.requestSubmit();
            }
        }

        if (event.target.matches('[data-laporan-per-page]')) {
            const form = event.target.closest('form');

            if (form) {
                form.requestSubmit();
            }
        }
    });

    // Tangani tautan pagination.
    document.addEventListener('click', function (event) {
        const resetLink = event.target.closest('a[data-laporan-reset]');

        if (resetLink) {
            event.preventDefault();
            loadPage(new URL(resetLink.href), { scrollToTop: true });
            return;
        }

        const paginationLink = event.target.closest(
            '#laporanStokContent .pagination a'
        );

        if (!paginationLink) return;

        event.preventDefault();
        loadPage(new URL(paginationLink.href), { scrollToTop: true });
    });

    // Tombol cetak.
    document.addEventListener('click', function (event) {
        if (event.target.closest('#cetakLaporanStok')) {
            window.print();
        }
    });

    // Tombol Back/Forward browser tetap bekerja.
    window.addEventListener('popstate', function () {
        loadPage(new URL(window.location.href), {
            updateHistory: false,
            scrollToTop: false
        });
    });
})();
</script>
@endpush