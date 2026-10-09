@extends('layouts.app')

@section('title', 'Role Menu')
@section('page-title', 'Role Menu')

@section('content')
@php
    $selectedMenuIds = collect($selectedMenuIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

{{-- ALERT --}}
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

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <div class="fw-semibold mb-1">Terjadi kesalahan:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- HEADER --}}
<div class="content-card mb-4">
    <div class="d-flex align-items-center gap-3">
        <div class="role-page-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <div>
            <h5 class="mb-1 fw-semibold">Pengaturan Role Menu</h5>
            <p class="text-muted mb-0">
                Atur hak akses menu berdasarkan role pengguna.
            </p>
        </div>
    </div>
</div>

{{-- PILIH ROLE --}}
<div class="content-card mb-4">
    <div class="mb-3">
        <h6 class="fw-semibold mb-1">Pilih Role</h6>
        <p class="text-muted small mb-0">
            Pilih role untuk melihat dan mengubah hak aksesnya.
        </p>
    </div>

    <form method="GET" action="{{ route('role-menu.index') }}">
        <div class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-5">
                <label for="role_id" class="form-label">Role Pengguna</label>
                <select
                    name="role_id"
                    id="role_id"
                    class="form-select"
                    onchange="this.form.submit()"
                    required
                >
                    @forelse($roles as $role)
                        <option
                            value="{{ $role->id }}"
                            @selected((int) $selectedRoleId === (int) $role->id)
                        >
                            {{ $role->name }}
                        </option>
                    @empty
                        <option value="">Belum ada role</option>
                    @endforelse
                </select>
            </div>
        </div>
    </form>
</div>

{{-- DAFTAR HAK AKSES --}}
@if($selectedRoleId)
    <form
        method="POST"
        action="{{ route('role-menu.update', $selectedRoleId) }}"
        id="roleMenuForm"
    >
        @csrf
        @method('PUT')

        <div class="content-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                <div>
                    <h5 class="fw-semibold mb-1">Daftar Menu</h5>
                    <p class="text-muted small mb-0">
                        Centang menu yang boleh diakses oleh role ini.
                    </p>
                </div>

                <div class="d-flex gap-2 flex-wrap">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        id="checkAll"
                    >
                        <i class="bi bi-check-all me-1"></i>
                        Pilih Semua
                    </button>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        id="uncheckAll"
                    >
                        <i class="bi bi-x-lg me-1"></i>
                        Hapus Semua
                    </button>
                </div>
            </div>

            <div class="role-menu-summary mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <span id="selectedMenuCount">0</span> menu dipilih
            </div>

            <div class="role-menu-list">
                @forelse($menus as $menu)
                    @php
                        $children = $menu->children ?? collect();
                    @endphp

                    @if($children->isNotEmpty())
                        <section class="menu-group">
                            <div class="menu-group-title">
                                <i class="bi bi-folder2-open me-2"></i>
                                {{ $menu->name }}
                            </div>

                            <div class="menu-group-items">
                                @foreach($children as $child)
                                    <label
                                        class="menu-access-item"
                                        for="menu_{{ $child->id }}"
                                    >
                                        <input
                                            class="form-check-input menu-checkbox"
                                            type="checkbox"
                                            name="menu_ids[]"
                                            value="{{ $child->id }}"
                                            id="menu_{{ $child->id }}"
                                            @checked(in_array((int) $child->id, $selectedMenuIds, true))
                                        >

                                        <span class="menu-access-name">
                                            {{ $child->name }}
                                        </span>

                                        <i class="bi bi-check-circle-fill menu-check-icon"></i>
                                    </label>
                                @endforeach
                            </div>
                        </section>
                    @else
                        <label
                            class="menu-access-item menu-direct-item"
                            for="menu_{{ $menu->id }}"
                        >
                            <input
                                class="form-check-input menu-checkbox"
                                type="checkbox"
                                name="menu_ids[]"
                                value="{{ $menu->id }}"
                                id="menu_{{ $menu->id }}"
                                @checked(in_array((int) $menu->id, $selectedMenuIds, true))
                            >

                            <span class="menu-access-name">
                                <i class="bi bi-list-ul me-2"></i>
                                {{ $menu->name }}
                            </span>

                            <i class="bi bi-check-circle-fill menu-check-icon"></i>
                        </label>
                    @endif
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-menu-button-wide empty-menu-icon"></i>
                        <h6 class="mt-3 mb-1">Belum ada data menu</h6>
                        <p class="text-muted small mb-0">
                            Tambahkan data menu terlebih dahulu.
                        </p>
                    </div>
                @endforelse
            </div>

            @if($menus->isNotEmpty())
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-4 border-top">
                    <small class="text-muted">
                        Periksa pilihan sebelum menyimpan perubahan.
                    </small>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>
                        Simpan Hak Akses
                    </button>
                </div>
            @endif
        </div>
    </form>
@else
    <div class="content-card text-center py-5">
        <i class="bi bi-shield-lock empty-menu-icon"></i>
        <h6 class="mt-3">Role belum tersedia</h6>
        <p class="text-muted mb-0">
            Tambahkan role terlebih dahulu sebelum mengatur hak akses.
        </p>
    </div>
@endif
@endsection

@push('styles')
<style>
    .role-page-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #eaf2ff;
        color: #0d6efd;
        font-size: 23px;
        flex-shrink: 0;
    }

    .role-menu-summary {
        padding: 10px 14px;
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        font-size: 13px;
        color: #495057;
    }

    .role-menu-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .menu-group {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .menu-group-title {
        padding: 13px 16px;
        background: #f8f9fa;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        font-size: 14px;
        color: #343a40;
    }

    .menu-group-items {
        padding: 4px 0;
    }

    .menu-access-item {
        display: flex;
        align-items: center;
        gap: 11px;
        width: 100%;
        padding: 12px 16px;
        margin: 0;
        cursor: pointer;
        transition: background-color .15s ease;
    }

    .menu-access-item:hover {
        background: #f6f9ff;
    }

    .menu-access-item .form-check-input {
        width: 17px;
        height: 17px;
        margin: 0;
        flex-shrink: 0;
        cursor: pointer;
    }

    .menu-access-name {
        flex: 1;
        color: #343a40;
        font-size: 14px;
    }

    .menu-check-icon {
        display: none;
        color: #0d6efd;
        font-size: 15px;
    }

    .menu-access-item:has(.menu-checkbox:checked) {
        background: #f0f6ff;
    }

    .menu-access-item:has(.menu-checkbox:checked) .menu-access-name {
        color: #0d6efd;
    }

    .menu-access-item:has(.menu-checkbox:checked) .menu-check-icon {
        display: inline-block;
    }

    .menu-direct-item {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
    }

    .empty-menu-icon {
        font-size: 38px;
        color: #adb5bd;
    }

    @media (max-width: 767.98px) {
        .role-menu-summary {
            font-size: 12px;
        }

        .menu-group-title,
        .menu-access-item {
            padding: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = () => document.querySelectorAll('.menu-checkbox');
    const countElement = document.getElementById('selectedMenuCount');

    function updateSelectedCount() {
        if (!countElement) return;

        countElement.textContent = Array.from(checkboxes())
            .filter(checkbox => checkbox.checked).length;
    }

    document.getElementById('checkAll')?.addEventListener('click', function () {
        checkboxes().forEach(checkbox => checkbox.checked = true);
        updateSelectedCount();
    });

    document.getElementById('uncheckAll')?.addEventListener('click', function () {
        checkboxes().forEach(checkbox => checkbox.checked = false);
        updateSelectedCount();
    });

    document.getElementById('roleMenuForm')?.addEventListener('change', function (event) {
        if (event.target.matches('.menu-checkbox')) {
            updateSelectedCount();
        }
    });

    updateSelectedCount();
});
</script>
@endpush