
@extends('layouts.app')

@section('title', 'Rekapitulasi')
@section('page-title', 'Rekapitulasi')

@section('content')

@php
    // Nilai default mencegah tampilan error jika key ringkasan tidak tersedia.
    $totalBarang = $summary['total_barang'] ?? 0;
    $totalKategori = $summary['total_kategori'] ?? 0;
    $totalPenambahan = $summary['total_penambahan'] ?? 0;
    $totalPemakaian = $summary['total_pemakaian'] ?? 0;
    $totalStok = $summary['total_stok'] ?? 0;
    $totalPeriode = $summary['total_periode_opname'] ?? 0;
    $totalOpnameSelesai = $summary['total_opname_selesai'] ?? 0;
    $nilaiOpnameTerakhir = $summary['nilai_opname_terakhir'] ?? 0;
@endphp

<div id="rekapitulasiContent">

    {{-- HEADER --}}
    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="mb-1 fw-semibold">Rekapitulasi Data Stok</h5>
                <p class="text-muted mb-0">
                    Ringkasan barang, kategori, transaksi, dan stok opname.
                </p>
            </div>

<a
    href="{{ route('rekapitulasi.export') }}"
    class="btn btn-success"
>
    <i class="bi bi-file-earmark-excel"></i>
    Export Excel
</a>
        </div>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="row g-3 mb-4">

        {{-- TOTAL BARANG --}}
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="content-card summary-card h-100">
                <div class="summary-card-body">
                    <div>
                        <span class="summary-label">Total Barang</span>
                        <h3 class="summary-value">
                            {{ number_format((float) $totalBarang, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="summary-icon icon-primary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- TOTAL KATEGORI --}}
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="content-card summary-card h-100">
                <div class="summary-card-body">
                    <div>
                        <span class="summary-label">Total Kategori</span>
                        <h3 class="summary-value">
                            {{ number_format((float) $totalKategori, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="summary-icon icon-success">
                        <i class="bi bi-tags"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- PENAMBAHAN --}}
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="content-card summary-card h-100">
                <div class="summary-card-body">
                    <div>
                        <span class="summary-label">Total Penambahan</span>
                        <h3 class="summary-value">
                            {{ number_format((float) $totalPenambahan, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="summary-icon icon-success">
                        <i class="bi bi-box-arrow-in-down"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- PEMAKAIAN --}}
        <div class="col-xl-3 col-md-6 col-sm-6">
            <div class="content-card summary-card h-100">
                <div class="summary-card-body">
                    <div>
                        <span class="summary-label">Total Pemakaian</span>
                        <h3 class="summary-value">
                            {{ number_format((float) $totalPemakaian, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="summary-icon icon-danger">
                        <i class="bi bi-box-arrow-up"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- STOK SAAT INI --}}
        <div class="col-xl-4 col-md-6">
            <div class="content-card summary-card h-100">
                <div class="summary-card-body">
                    <div>
                        <span class="summary-label">Jumlah Stok Saat Ini</span>
                        <h3 class="summary-value">
                            {{ number_format((float) $totalStok, 0, ',', '.') }}
                        </h3>
                        <small class="text-muted">Akumulasi stok seluruh barang</small>
                    </div>
                    <div class="summary-icon icon-primary">
                        <i class="bi bi-stack"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- PERIODE OPNAME --}}
        <div class="col-xl-4 col-md-6">
            <div class="content-card summary-card h-100">
                <div class="summary-card-body">
                    <div>
                        <span class="summary-label">Periode Stok Opname</span>
                        <h3 class="summary-value">
                            {{ number_format((float) $totalPeriode, 0, ',', '.') }}
                        </h3>
                        <small class="text-muted">
                            {{ number_format((float) $totalOpnameSelesai, 0, ',', '.') }}
                            periode selesai
                        </small>
                    </div>
                    <div class="summary-icon icon-warning">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- NILAI OPNAME TERAKHIR --}}
        <div class="col-xl-4 col-md-12">
            <div class="content-card summary-card h-100">
                <div class="summary-card-body">
                    <div class="min-w-0">
                        <span class="summary-label">Nilai Opname Terakhir</span>
                        <h3 class="summary-value summary-currency">
                            Rp {{ number_format((float) $nilaiOpnameTerakhir, 2, ',', '.') }}
                        </h3>
                    </div>
                    <div class="summary-icon icon-success">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- REKAP KATEGORI --}}
    <div class="content-card mb-4">
        <div class="section-heading">
            <div>
                <h5 class="mb-1 fw-semibold">Rekapitulasi Berdasarkan Kategori</h5>
                <p class="text-muted mb-0">
                    Jumlah barang dan pergerakan stok untuk setiap kategori.
                </p>
            </div>
            <span class="section-count">
                {{ is_countable($byKategori) ? count($byKategori) : 0 }} kategori
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Kategori</th>
                        <th class="text-end">Jumlah Barang</th>
                        <th class="text-end">Penambahan</th>
                        <th class="text-end">Pemakaian</th>
                        <th class="text-end">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byKategori as $kategori)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">
                                {{ $kategori['nama'] ?? '-' }}
                            </td>
                            <td class="text-end">
                                {{ number_format((float) ($kategori['jumlah_barang'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end text-success">
                                +{{ number_format((float) ($kategori['penambahan'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end text-danger">
                                -{{ number_format((float) ($kategori['pemakaian'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format((float) ($kategori['stok'] ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="bi bi-tags"></i>
                                </div>
                                <div class="fw-semibold">Belum ada data kategori</div>
                                <small class="text-muted">
                                    Data rekapitulasi kategori belum tersedia.
                                </small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- REKAP BARANG --}}
    <div class="content-card mb-4">
        <div class="section-heading">
            <div>
                <h5 class="mb-1 fw-semibold">Rekapitulasi Berdasarkan Barang</h5>
                <p class="text-muted mb-0">
                    Ringkasan pergerakan stok untuk setiap barang.
                </p>
            </div>
            <span class="section-count">
                {{ is_countable($byBarang) ? count($byBarang) : 0 }} barang
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-end">Penambahan</th>
                        <th class="text-end">Pemakaian</th>
                        <th class="text-end">Stok</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byBarang as $barang)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $barang['kode'] ?? '-' }}
                                </span>
                            </td>
                            <td class="fw-semibold">
                                {{ $barang['nama'] ?? '-' }}
                            </td>
                            <td>{{ $barang['kategori'] ?? '-' }}</td>
                            <td class="text-end text-success">
                                +{{ number_format((float) ($barang['penambahan'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end text-danger">
                                -{{ number_format((float) ($barang['pemakaian'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold">
                                {{ number_format((float) ($barang['stok'] ?? 0), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div class="fw-semibold">Belum ada data barang</div>
                                <small class="text-muted">
                                    Data rekapitulasi barang belum tersedia.
                                </small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- REKAP PERIODE --}}
    <div class="content-card">
        <div class="section-heading">
            <div>
                <h5 class="mb-1 fw-semibold">Rekapitulasi Periode Stok Opname</h5>
                <p class="text-muted mb-0">
                    Riwayat periode, status pelaksanaan, dan nilai stok opname.
                </p>
            </div>
            <span class="section-count">
                {{ is_countable($byPeriode) ? count($byPeriode) : 0 }} periode
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 60px;">No</th>
                        <th>Periode</th>
                        <th>Tanggal Opname</th>
                        <th class="text-center">Status</th>
                        <th class="text-end">Jumlah Barang</th>
                        <th class="text-end">Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byPeriode as $periode)
                        @php
                            $tanggalOpname = $periode['tanggal'] ?? null;

                            if ($tanggalOpname && !($tanggalOpname instanceof \Carbon\CarbonInterface)) {
                                try {
                                    $tanggalOpname = \Illuminate\Support\Carbon::parse($tanggalOpname);
                                } catch (\Throwable $e) {
                                    $tanggalOpname = null;
                                }
                            }

                            $statusPeriode = strtolower((string) ($periode['status'] ?? 'draft'));
                        @endphp

                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="fw-semibold">
                                {{ $periode['periode'] ?? '-' }}
                            </td>
                            <td>
                                {{ $tanggalOpname?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="text-center">
                                @if($statusPeriode === 'selesai')
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Selesai
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>Draft
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                {{ number_format((float) ($periode['jumlah_barang'] ?? 0), 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-semibold text-nowrap">
                                Rp {{ number_format((float) ($periode['total_nilai'] ?? 0), 2, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state-icon mb-3">
                                    <i class="bi bi-calendar3"></i>
                                </div>
                                <div class="fw-semibold">
                                    Belum ada periode stok opname
                                </div>
                                <small class="text-muted">
                                    Riwayat stok opname belum tersedia.
                                </small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection

@push('styles')
<style>
    #rekapitulasiContent .content-card {
        min-width: 0;
    }

    #rekapitulasiContent .summary-card {
        padding: 20px;
        min-height: 125px;
    }

    #rekapitulasiContent .summary-card-body {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    #rekapitulasiContent .summary-label {
        display: block;
        color: #6c757d;
        font-size: 13px;
        line-height: 1.5;
    }

    #rekapitulasiContent .summary-value {
        margin: 8px 0 0;
        font-size: clamp(22px, 2vw, 28px);
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    #rekapitulasiContent .summary-currency {
        font-size: clamp(17px, 1.5vw, 24px);
    }

    #rekapitulasiContent .summary-icon {
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 23px;
    }

    #rekapitulasiContent .icon-primary {
        color: #0d6efd;
        background: rgba(13, 110, 253, .10);
    }

    #rekapitulasiContent .icon-success {
        color: #198754;
        background: rgba(25, 135, 84, .10);
    }

    #rekapitulasiContent .icon-danger {
        color: #dc3545;
        background: rgba(220, 53, 69, .10);
    }

    #rekapitulasiContent .icon-warning {
        color: #997404;
        background: rgba(255, 193, 7, .18);
    }

    #rekapitulasiContent .section-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    #rekapitulasiContent .section-count {
        padding: 5px 10px;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        color: #6c757d;
        background: #f8f9fa;
        font-size: 12px;
        white-space: nowrap;
    }

    #rekapitulasiContent .table th {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
    }

    #rekapitulasiContent .table td {
        font-size: 14px;
    }

    #rekapitulasiContent .empty-state-icon {
        width: 64px;
        height: 64px;
        margin-left: auto;
        margin-right: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f3f5;
        color: #6c757d;
        font-size: 28px;
    }

    @media (max-width: 767.98px) {
        #rekapitulasiContent .table {
            min-width: 800px;
        }

        #rekapitulasiContent .summary-card {
            padding: 16px;
        }

        #rekapitulasiContent .summary-icon {
            width: 40px;
            height: 40px;
            flex-basis: 40px;
            font-size: 20px;
        }
    }

    @media print {
        @page {
            size: landscape;
            margin: 12mm;
        }

        body {
            background: #fff !important;
            color: #000 !important;
            font-size: 10px;
        }

        .sidebar,
        .topbar,
        .btn {
            display: none !important;
        }

        .main-wrapper {
            margin-left: 0 !important;
        }

        .content-wrapper {
            padding: 0 !important;
        }

        #rekapitulasiContent .content-card {
            padding: 12px !important;
            margin-bottom: 12px !important;
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            break-inside: avoid;
        }

        #rekapitulasiContent .summary-card {
            min-height: auto;
        }

        #rekapitulasiContent .summary-value {
            font-size: 16px;
        }

        #rekapitulasiContent .summary-icon {
            display: none;
        }

        #rekapitulasiContent .table {
            min-width: 0 !important;
            width: 100% !important;
        }

        #rekapitulasiContent .table th,
        #rekapitulasiContent .table td {
            padding: 5px !important;
            font-size: 9px !important;
        }

        #rekapitulasiContent .section-count {
            display: none;
        }

        #rekapitulasiContent .table-responsive {
            overflow: visible !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    'use strict';

    if (window.rekapitulasiPrintInitialized) {
        return;
    }

    window.rekapitulasiPrintInitialized = true;

    document.addEventListener('click', function (event) {
        if (event.target.closest('#cetakRekapitulasi')) {
            window.print();
        }
    });
})();
</script>
@endpush