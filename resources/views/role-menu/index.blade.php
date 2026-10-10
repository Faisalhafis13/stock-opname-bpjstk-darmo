
@extends('layouts.app')

@section('title', 'Role Menu')
@section('page-title', 'Role Menu')

@section('content')

@php
    $selectedMenuIds = collect($selectedMenuIds ?? [])
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

{{-- ALERT DARI SESSION --}}
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

{{-- ALERT AJAX --}}
<div
    id="ajaxAlert"
    class="alert d-none"
    role="alert"
    aria-live="polite"
></div>

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

    <div class="row g-3 align-items-end">
        <div class="col-md-6 col-lg-5">
            <label for="role_id" class="form-label">
                Role Pengguna
            </label>

            <select
                name="role_id"
                id="role_id"
                class="form-select"
                data-index-url="{{ route('role-menu.index') }}"
                required
                @disabled($roles->isEmpty())
            >
                @forelse($roles as $role)
                    <option
                        value="{{ $role->id }}"
                        data-update-url="{{ route('role-menu.update', $role->id) }}"
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

            {{-- HEADER DAFTAR MENU --}}
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

            {{-- JUMLAH MENU TERPILIH --}}
            <div class="role-menu-summary mb-3">
                <i class="bi bi-info-circle me-2"></i>
                <span id="selectedMenuCount">0</span> menu dipilih
            </div>

            {{-- DAFTAR MENU --}}
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

            {{-- TOMBOL SIMPAN --}}
            @if($menus->isNotEmpty())
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-4 pt-4 border-top">

                    <small class="text-muted">
                        Periksa pilihan sebelum menyimpan perubahan.
                    </small>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveRoleMenuButton"
                    >
                        <span
                            id="saveRoleMenuSpinner"
                            class="spinner-border spinner-border-sm me-2 d-none"
                            aria-hidden="true"
                        ></span>

                        <i
                            class="bi bi-save me-2"
                            id="saveRoleMenuIcon"
                        ></i>

                        <span id="saveRoleMenuText">
                            Simpan Hak Akses
                        </span>
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

    #ajaxAlert {
        overflow-wrap: anywhere;
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
    const roleSelect = document.getElementById('role_id');
    const form = document.getElementById('roleMenuForm');
    const alertBox = document.getElementById('ajaxAlert');
    const countElement = document.getElementById('selectedMenuCount');

    const checkAllButton = document.getElementById('checkAll');
    const uncheckAllButton = document.getElementById('uncheckAll');

    const saveButton = document.getElementById('saveRoleMenuButton');
    const saveText = document.getElementById('saveRoleMenuText');
    const saveIcon = document.getElementById('saveRoleMenuIcon');
    const saveSpinner = document.getElementById('saveRoleMenuSpinner');

    if (!roleSelect || !alertBox) {
        return;
    }

    let isLoadingRole = false;
    let isSaving = false;
    let requestNumber = 0;

    const getCheckboxes = () => {
        return form
            ? [...form.querySelectorAll('.menu-checkbox')]
            : [];
    };

    function showAlert(message, type = 'success') {
        alertBox.textContent = message;
        alertBox.className = `alert alert-${type}`;
    }

    function hideAlert() {
        alertBox.textContent = '';
        alertBox.className = 'alert d-none';
    }

    function updateSelectedCount() {
        if (!countElement) {
            return;
        }

        const selectedCount = getCheckboxes()
            .filter(checkbox => checkbox.checked)
            .length;

        countElement.textContent = selectedCount;
    }

    function updateFormAction() {
        if (!form) {
            return;
        }

        const selectedOption = roleSelect.selectedOptions[0];
        const updateUrl = selectedOption?.dataset.updateUrl;

        form.action = updateUrl || '#';
    }

    function setControlsDisabled(disabled) {
        if (checkAllButton) {
            checkAllButton.disabled = disabled;
        }

        if (uncheckAllButton) {
            uncheckAllButton.disabled = disabled;
        }

        getCheckboxes().forEach(checkbox => {
            checkbox.disabled = disabled;
        });

        if (saveButton) {
            saveButton.disabled = disabled;
        }
    }

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(options.headers || {})
            }
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const firstValidationError = data.errors
                ? Object.values(data.errors).flat()[0]
                : null;

            throw new Error(
                firstValidationError ||
                data.message ||
                `Permintaan gagal (${response.status}).`
            );
        }

        return data;
    }

    async function loadRoleMenus() {
        if (!form) {
            return;
        }

        const roleId = roleSelect.value;
        const currentRequest = ++requestNumber;

        hideAlert();
        updateFormAction();

        if (!roleId) {
            getCheckboxes().forEach(checkbox => {
                checkbox.checked = false;
            });

            updateSelectedCount();
            return;
        }

        isLoadingRole = true;
        roleSelect.disabled = true;
        setControlsDisabled(true);

        try {
            const indexUrl = new URL(
                roleSelect.dataset.indexUrl,
                window.location.origin
            );

            indexUrl.searchParams.set('role_id', roleId);

            const data = await requestJson(indexUrl.toString());

            // Abaikan respons lama jika pengguna sudah memilih role lain.
            if (currentRequest !== requestNumber) {
                return;
            }

            const selectedIds = new Set(
                (data.selected_menu_ids || []).map(Number)
            );

            getCheckboxes().forEach(checkbox => {
                checkbox.checked = selectedIds.has(
                    Number(checkbox.value)
                );
            });

            updateSelectedCount();
        } catch (error) {
            if (currentRequest === requestNumber) {
                showAlert(
                    error.message || 'Gagal memuat hak akses menu.',
                    'danger'
                );
            }
        } finally {
            if (currentRequest === requestNumber) {
                isLoadingRole = false;
                roleSelect.disabled = false;
                setControlsDisabled(false);
            }
        }
    }

    // Ganti role tanpa memuat ulang halaman.
    roleSelect.addEventListener('change', loadRoleMenus);

    // Pilih semua menu.
    checkAllButton?.addEventListener('click', function () {
        if (isLoadingRole || isSaving) {
            return;
        }

        getCheckboxes().forEach(checkbox => {
            checkbox.checked = true;
        });

        updateSelectedCount();
    });

    // Hapus semua pilihan.
    uncheckAllButton?.addEventListener('click', function () {
        if (isLoadingRole || isSaving) {
            return;
        }

        getCheckboxes().forEach(checkbox => {
            checkbox.checked = false;
        });

        updateSelectedCount();
    });

    // Perbarui jumlah saat checkbox berubah.
    form?.addEventListener('change', function (event) {
        if (event.target.matches('.menu-checkbox')) {
            updateSelectedCount();
        }
    });

    // Simpan hak akses menggunakan AJAX.
    form?.addEventListener('submit', async function (event) {
        event.preventDefault();

        if (isLoadingRole || isSaving) {
            return;
        }

        if (!roleSelect.value || form.action.endsWith('#')) {
            showAlert('Pilih role terlebih dahulu.', 'warning');
            return;
        }

        const csrfToken = form.querySelector(
            'input[name="_token"]'
        )?.value;

        const menuIds = getCheckboxes()
            .filter(checkbox => checkbox.checked)
            .map(checkbox => Number(checkbox.value));

        isSaving = true;
        setControlsDisabled(true);

        if (saveText) {
            saveText.textContent = 'Menyimpan...';
        }

        saveIcon?.classList.add('d-none');
        saveSpinner?.classList.remove('d-none');

        hideAlert();

        try {
            const data = await requestJson(form.action, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    _token: csrfToken,
                    menu_ids: menuIds
                })
            });

            showAlert(
                data.message || 'Hak akses menu berhasil diperbarui.',
                'success'
            );

            updateSelectedCount();
        } catch (error) {
            showAlert(
                error.message || 'Gagal menyimpan hak akses menu.',
                'danger'
            );
        } finally {
            isSaving = false;
            setControlsDisabled(false);

            if (saveText) {
                saveText.textContent = 'Simpan Hak Akses';
            }

            saveIcon?.classList.remove('d-none');
            saveSpinner?.classList.add('d-none');
        }
    });

    updateFormAction();
    updateSelectedCount();
});
</script>
@endpush
