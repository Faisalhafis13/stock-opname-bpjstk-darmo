
@extends('layouts.app')

@section('title', 'Barang')
@section('page-title', 'Barang')

@section('content')
<div id="barang-user-wrapper">

    {{-- HEADER --}}
    <div class="content-card mb-4">
        <h5 class="mb-1 fw-semibold">Data Barang</h5>
        <p class="text-muted mb-0">
            Informasi ketersediaan barang yang tersedia.
        </p>
    </div>

    {{-- DATA BARANG --}}
    <div class="content-card">

        {{-- TOOLBAR --}}
        <div class="d-flex justify-content-between align-items-center
                    mb-3 flex-wrap gap-3">

            <div>
                <h5 class="mb-1 fw-semibold">Daftar Barang</h5>
                <p class="text-muted mb-0">
                    Informasi jumlah stok dan ketersediaan barang.
                </p>
            </div>

            <form
                class="barang-user-filter d-flex flex-wrap gap-2"
                method="GET"
                action="{{ route('barang.user') }}"
            >
                <input
                    type="search"
                    name="search"
                    class="form-control form-control-sm"
                    placeholder="Cari nama barang..."
                    aria-label="Cari nama barang"
                    value="{{ $search }}"
                >

                <select
                    name="per_page"
                    class="form-select form-select-sm"
                    aria-label="Jumlah data per halaman"
                >
                    @foreach([10, 25, 50, 100] as $jumlah)
                        <option
                            value="{{ $jumlah }}"
                            @selected($perPage == $jumlah)
                        >
                            {{ $jumlah }} data
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>
            </form>
        </div>

        {{-- INFO JUMLAH DATA --}}
        <div class="d-flex justify-content-between align-items-center
                    flex-wrap gap-2 mb-3">

            <small class="text-muted">
                Menampilkan
                <strong>{{ $barangs->firstItem() ?? 0 }}</strong>
                sampai
                <strong>{{ $barangs->lastItem() ?? 0 }}</strong>
                dari
                <strong>{{ $barangs->total() }}</strong>
                data
            </small>

            <small class="text-muted">
                Total data sesuai pencarian
            </small>
        </div>

        {{-- ALERT AJAX --}}
        <div id="barang-user-alerts" aria-live="polite"></div>

        {{-- LOADING --}}
        <div class="barang-user-loading d-none text-center py-3">
            <span class="spinner-border spinner-border-sm text-primary"
                  role="status"></span>
            <span class="text-muted small ms-2">Memuat data barang...</span>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th width="60" class="text-center">No</th>
                        <th>Nama Barang</th>
                        <th class="text-end">Jumlah Stok</th>
                        <th class="text-center">Status Ketersediaan</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($barangs as $index => $barang)

                        @php
                            $penambahan = (float) ($barang->total_penambahan ?? 0);
                            $pemakaian = (float) ($barang->total_pemakaian ?? 0);

                            $stok = max(0, $penambahan - $pemakaian);
                        @endphp

                        <tr>
                            {{-- NOMOR --}}
                            <td class="text-center">
                                {{ $barangs->firstItem() + $index }}
                            </td>

                            {{-- NAMA BARANG --}}
                            <td>
                                <div class="fw-semibold">
                                    {{ $barang->nama_barang }}
                                </div>
                            </td>

                            {{-- JUMLAH STOK --}}
                            <td class="text-end">
                                <span class="fw-semibold">
                                    {{ number_format($stok, 0, ',', '.') }}
                                </span>

                                <span class="text-muted">
                                    {{ $barang->satuan }}
                                </span>
                            </td>

                            {{-- STATUS KETERSEDIAAN --}}
                            <td class="text-center">
                                @if($stok > 0)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Habis
                                    </span>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="barang-user-empty">
                                    <div class="barang-user-empty-icon mb-3">
                                        <i class="bi bi-box-seam"></i>
                                    </div>

                                    <div class="fw-semibold">
                                        @if($search !== '')
                                            Barang tidak ditemukan
                                        @else
                                            Belum ada data barang
                                        @endif
                                    </div>

                                    <small class="text-muted">
                                        @if($search !== '')
                                            Coba gunakan kata kunci pencarian lain.
                                        @else
                                            Data barang yang tersedia akan ditampilkan di sini.
                                        @endif
                                    </small>
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
                {{ $barangs->appends(request()->query())->links('components.pagination.limited') }}
            </div>
        @endif

    </div>
</div>
@endsection

@push('styles')
<style>
    #barang-user-wrapper .table th {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
    }

    #barang-user-wrapper .table td {
        font-size: 14px;
    }

    #barang-user-wrapper .badge {
        font-weight: 500;
        padding: 7px 10px;
    }

    #barang-user-wrapper .barang-user-filter {
        max-width: 100%;
    }

    #barang-user-wrapper .barang-user-filter input {
        width: 210px;
        min-width: 150px;
    }

    #barang-user-wrapper .barang-user-filter select {
        width: 110px;
    }

    #barang-user-wrapper .barang-user-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    #barang-user-wrapper .barang-user-empty-icon {
        width: 64px;
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f1f3f5;
        color: #6c757d;
        font-size: 28px;
    }

    #barang-user-wrapper.is-loading {
        opacity: .65;
        pointer-events: none;
    }

    @media (max-width: 767.98px) {
        #barang-user-wrapper .table {
            min-width: 700px;
        }

        #barang-user-wrapper .barang-user-filter {
            width: 100%;
        }

        #barang-user-wrapper .barang-user-filter input {
            flex: 1;
        }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const wrapperId = 'barang-user-wrapper';

    function getWrapper() {
        return document.getElementById(wrapperId);
    }

    function showMessage(message, type = 'danger') {
        const alerts = getWrapper()?.querySelector('#barang-user-alerts');

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

    async function loadPage(url, updateHistory = true) {
        const current = getWrapper();

        if (!current) return;

        current.classList.add('is-loading');

        const loading = current.querySelector('.barang-user-loading');
        loading?.classList.remove('d-none');

        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            });

            if (!response.ok) {
                throw new Error('Gagal memuat data barang. Silakan coba lagi.');
            }

            const html = await response.text();
            const documentBaru = new DOMParser().parseFromString(
                html,
                'text/html'
            );

            const next = documentBaru.getElementById(wrapperId);

            if (!next) {
                throw new Error('Bagian data barang tidak ditemukan.');
            }

            current.replaceWith(next);

            if (updateHistory) {
                history.pushState({}, '', url);
            }
        } catch (error) {
            showMessage(error.message);
        } finally {
            getWrapper()?.classList.remove('is-loading');
        }
    }

    // PENCARIAN DAN FILTER
    document.addEventListener('submit', event => {
        const form = event.target.closest(
            `#${wrapperId} .barang-user-filter`
        );

        if (!form) return;

        event.preventDefault();

        const url = new URL(form.action, window.location.origin);
        const formData = new FormData(form);

        url.search = '';

        formData.forEach((value, key) => {
            const text = String(value).trim();

            if (text !== '') {
                url.searchParams.set(key, text);
            }
        });

        // Mulai dari halaman pertama setelah pencarian berubah.
        url.searchParams.delete('page');

        loadPage(url.toString());
    });

    // GANTI JUMLAH DATA PER HALAMAN
    document.addEventListener('change', event => {
        const select = event.target.closest(
            `#${wrapperId} .barang-user-filter select[name="per_page"]`
        );

        if (!select) return;

        select.form.requestSubmit();
    });

    // PAGINATION AJAX
    document.addEventListener('click', event => {
        const link = event.target.closest(
            `#${wrapperId} .pagination a`
        );

        if (!link) return;

        if (
            link.target === '_blank' ||
            event.ctrlKey ||
            event.metaKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        event.preventDefault();
        loadPage(link.href);
    });

    // TOMBOL KEMBALI/MAJU BROWSER
    window.addEventListener('popstate', () => {
        loadPage(window.location.href, false);
    });
})();
</script>
@endpush