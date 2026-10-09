<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Repositories\Contracts\RoleUserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleUserController extends Controller
{
    public function __construct(
        protected RoleUserRepositoryInterface $roleUserRepository
    ) {
    }

    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = trim((string) $request->input('search', ''));

        $users = $this->roleUserRepository->getAll(
            $perPage,
            $search
        );

        $roles = Role::orderBy('name')->get();

        return view('role-user.index', compact(
            'users',
            'roles',
            'perPage',
            'search'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role_id.required' => 'Role pengguna wajib dipilih.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
        ]);

        $validated['password'] = Hash::make(
            $validated['password']
        );

        $this->roleUserRepository->create($validated);

        return redirect()
            ->route('role-user.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        int $id
    ): RedirectResponse {
        abort_unless(User::whereKey($id)->exists(), 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($id),
            ],
            'role_id' => [
                'required',
                'integer',
                'exists:roles,id',
            ],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
            ],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'role_id.required' => 'Role pengguna wajib dipilih.',
            'role_id.exists' => 'Role yang dipilih tidak valid.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make(
                $validated['password']
            );
        } else {
            unset($validated['password']);
        }

        $this->roleUserRepository->update(
            $id,
            $validated
        );

        return redirect()
            ->route('role-user.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        if ($id === (int) auth()->id()) {
            return redirect()
                ->route('role-user.index')
                ->with(
                    'error',
                    'Akun yang sedang digunakan tidak dapat dihapus.'
                );
        }

        if (!User::whereKey($id)->exists()) {
            abort(404);
        }

        try {
            $this->roleUserRepository->delete($id);

            return redirect()
                ->route('role-user.index')
                ->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('role-user.index')
                ->with(
                    'error',
                    'Pengguna tidak dapat dihapus. Periksa apakah akun masih digunakan oleh data lain.'
                );
        }
    }
}