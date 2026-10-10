
@extends('layouts.app')

@section('title', 'Role User')
@section('page-title', 'Role User')

@section('content')
{{-- ALERT SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ALERT ERROR --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="user-page-icon">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <h5 class="mb-1 fw-semibold">Data Pengguna</h5>
                <p class="text-muted mb-0">
                    Kelola akun pengguna dan hak akses role.
                </p>
            </div>
        </div>

        <button
            type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#modalTambah"
        >
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Pengguna
        </button>
    </div>
</div>

{{-- DAFTAR PENGGUNA --}}
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
        <div>
            <h5 class="fw-semibold mb-1">Daftar Pengguna</h5>
            <p class="text-muted small mb-0">
                Informasi akun dan role yang terdaftar di sistem.
            </p>
        </div>

        @include('components.list-controls', [
            'action' => route('role-user.index'),
            'perPage' => $perPage,
        ])
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="text-muted small">
            @if($users->total() > 0)
                Menampilkan
                <strong>{{ $users->firstItem() }}</strong>
                sampai
                <strong>{{ $users->lastItem() }}</strong>
                dari
                <strong>{{ $users->total() }}</strong> pengguna
            @else
                Belum ada pengguna yang sesuai.
            @endif
        </div>

    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 user-table">
            <thead class="table-light">
                <tr>
                    <th class="text-center" style="width: 65px;">No</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td class="text-center">
                            {{ $users->firstItem() + $index }}
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-avatar">
                                    {{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="fw-semibold">
                                    {{ $user->name }}
                                </span>
                            </div>
                        </td>

                        <td>{{ $user->email }}</td>

                        <td>
                            @if($user->role?->name === 'Admin')
                                <span class="badge rounded-pill bg-primary-subtle text-primary role-badge">
                                    <i class="bi bi-shield-check me-1"></i>Admin
                                </span>
                            @elseif($user->role?->name === 'User')
                                <span class="badge rounded-pill bg-success-subtle text-success role-badge">
                                    <i class="bi bi-person me-1"></i>User
                                </span>
                            @else
                                <span class="badge rounded-pill bg-secondary-subtle text-secondary role-badge">
                                    {{ $user->role?->name ?? 'Belum ditentukan' }}
                                </span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEdit{{ $user->id }}"
                                    title="Edit pengguna"
                                    aria-label="Edit {{ $user->name }}"
                                >
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                @if((int) $user->id !== (int) auth()->id())
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalHapus{{ $user->id }}"
                                        title="Hapus pengguna"
                                        aria-label="Hapus {{ $user->name }}"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-secondary"
                                        disabled
                                        title="Akun yang sedang digunakan tidak dapat dihapus"
                                    >
                                        <i class="bi bi-lock"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="empty-state-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="fw-semibold mt-3">Data pengguna tidak ditemukan</div>
                            <p class="text-muted small mb-0">
                                Coba ubah kata kunci pencarian atau tambahkan pengguna baru.
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $users->appends(request()->query())->links('components.pagination.limited') }}
        </div>
    @endif
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form
            method="POST"
            action="{{ route('role-user.store') }}"
            class="modal-content"
            id="formTambahPengguna"
        >
            @csrf

            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-semibold">Tambah Pengguna</h5>
                    <small class="text-muted">Lengkapi informasi akun pengguna.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label for="create_name" class="form-label">Nama Pengguna</label>
                    <input
                        type="text"
                        name="name"
                        id="create_name"
                        class="form-control"
                        value="{{ old('name') }}"
                        maxlength="255"
                        placeholder="Masukkan nama lengkap"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="create_email" class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="create_email"
                        class="form-control"
                        value="{{ old('email') }}"
                        maxlength="255"
                        placeholder="nama@email.com"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="create_role_id" class="form-label">Role</label>
                    <select
                        name="role_id"
                        id="create_role_id"
                        class="form-select"
                        required
                    >
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option
                                value="{{ $role->id }}"
                                @selected((string) old('role_id') === (string) $role->id)
                            >
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="create_password" class="form-label">Password</label>
                    <div class="input-group">
                        <input
                            type="password"
                            name="password"
                            id="create_password"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            required
                        >
                        <button
                            type="button"
                            class="btn btn-outline-secondary toggle-password"
                            data-target="create_password"
                            aria-label="Tampilkan password"
                        >
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-0">
                    <label for="create_password_confirmation" class="form-label">
                        Konfirmasi Password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        id="create_password_confirmation"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password"
                        placeholder="Masukkan ulang password"
                        required
                    >
                </div>

                <div class="form-text mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Password minimal 8 karakter dan harus sesuai dengan konfirmasi.
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Simpan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT DAN HAPUS --}}
@foreach($users as $user)
    {{-- MODAL EDIT --}}
    <div
        class="modal fade"
        id="modalEdit{{ $user->id }}"
        tabindex="-1"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <form
                method="POST"
                action="{{ route('role-user.update', $user->id) }}"
                class="modal-content"
            >
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-semibold">Edit Pengguna</h5>
                        <small class="text-muted">Perbarui data akun dan role.</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_{{ $user->id }}" class="form-label">
                            Nama Pengguna
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="edit_name_{{ $user->id }}"
                            class="form-control"
                            value="{{ $user->name }}"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="edit_email_{{ $user->id }}" class="form-label">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="edit_email_{{ $user->id }}"
                            class="form-control"
                            value="{{ $user->email }}"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="edit_role_{{ $user->id }}" class="form-label">
                            Role
                        </label>
                        <select
                            name="role_id"
                            id="edit_role_{{ $user->id }}"
                            class="form-select"
                            required
                        >
                            <option value="">-- Pilih Role --</option>
                            @foreach($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    @selected((int) $user->role_id === (int) $role->id)
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-light border small">
                        <i class="bi bi-info-circle me-1"></i>
                        Kosongkan password jika tidak ingin mengubahnya.
                    </div>

                    <div class="mb-3">
                        <label for="edit_password_{{ $user->id }}" class="form-label">
                            Password Baru
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="edit_password_{{ $user->id }}"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Kosongkan jika tidak diubah"
                        >
                    </div>

                    <div class="mb-0">
                        <label
                            for="edit_password_confirmation_{{ $user->id }}"
                            class="form-label"
                        >
                            Konfirmasi Password Baru
                        </label>
                        <input
                            type="password"
                            name="password_confirmation"
                            id="edit_password_confirmation_{{ $user->id }}"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Ulangi password baru"
                        >
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL HAPUS --}}
    @if((int) $user->id !== (int) auth()->id())
        <div
            class="modal fade"
            id="modalHapus{{ $user->id }}"
            tabindex="-1"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <form
                    method="POST"
                    action="{{ route('role-user.destroy', $user->id) }}"
                    class="modal-content"
                >
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold text-danger">
                            Hapus Pengguna
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body text-center py-4">
                        <div class="delete-icon mb-3">
                            <i class="bi bi-trash"></i>
                        </div>

                        <p class="mb-1">Yakin ingin menghapus akun ini?</p>
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <div class="text-muted small mt-2">
                            Tindakan ini tidak dapat dibatalkan.
                        </div>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>Ya, Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endforeach
@endsection

@push('styles')
<style>
    .user-page-icon {
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

    .user-search-form {
        width: min(100%, 380px);
    }

    .user-table th {
        white-space: nowrap;
        font-size: 13px;
        font-weight: 600;
        padding: 13px 12px;
    }

    .user-table td {
        font-size: 14px;
        padding: 13px 12px;
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 50%;
        background: #eaf2ff;
        color: #0d6efd;
        font-weight: 700;
    }

    .role-badge {
        padding: 7px 10px;
        font-weight: 500;
    }

    .empty-state-icon {
        width: 62px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        border-radius: 50%;
        background: #f1f3f5;
        color: #6c757d;
        font-size: 27px;
    }

    .delete-icon {
        width: 62px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        border-radius: 50%;
        background: #fff0f0;
        color: #dc3545;
        font-size: 26px;
    }

    @media (max-width: 767.98px) {
        .user-search-form {
            width: 100%;
        }

        .user-table {
            min-width: 680px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.target);

            if (!input) return;

            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';

            button.innerHTML = showPassword
                ? '<i class="bi bi-eye-slash"></i>'
                : '<i class="bi bi-eye"></i>';

            button.setAttribute(
                'aria-label',
                showPassword ? 'Sembunyikan password' : 'Tampilkan password'
            );
        });
    });

    // Validasi konfirmasi password di browser.
    document.querySelectorAll('#formTambahPengguna, #modalEdit' + ' form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            const password = form.querySelector('[name="password"]');
            const confirmation = form.querySelector('[name="password_confirmation"]');

            if (!password || !confirmation) return;

            // Pada form edit, konfirmasi hanya wajib jika password baru diisi.
            if (password.value === '' && !password.required) {
                confirmation.setCustomValidity('');
                return;
            }

            if (password.value !== confirmation.value) {
                confirmation.setCustomValidity('Konfirmasi password tidak cocok.');
            } else {
                confirmation.setCustomValidity('');
            }

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.reportValidity();
            }
        });

        form.querySelectorAll('[name="password"], [name="password_confirmation"]')
            .forEach(function (input) {
                input.addEventListener('input', function () {
                    const password = form.querySelector('[name="password"]');
                    const confirmation = form.querySelector('[name="password_confirmation"]');

                    if (password && confirmation) {
                        confirmation.setCustomValidity(
                            password.value !== confirmation.value
                                ? 'Konfirmasi password tidak cocok.'
                                : ''
                        );
                    }
                });
            });
    });
});
</script>
@endpush