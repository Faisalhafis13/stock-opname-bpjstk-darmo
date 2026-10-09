<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\KategoriBarangRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriBarangController extends Controller
{
    public function __construct(
        protected KategoriBarangRepositoryInterface $repository
    ) {
    }

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = trim((string) $request->input('search', ''));

        $kategoriBarangs = $this->repository->getAll(
            $perPage,
            $search
        );

        return view('kategori-barang.index', compact(
            'kategoriBarangs',
            'perPage',
            'search'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                'unique:kategori_barangs,kode',
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $this->repository->create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori barang berhasil ditambahkan.',
            ]);
        }

        return redirect()
            ->route('kategori-barang.index')
            ->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        $kategori = $this->repository->findById($id);

        if (!$kategori) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori barang tidak ditemukan.',
                ], 404);
            }

            return redirect()
                ->route('kategori-barang.index')
                ->with('error', 'Kategori barang tidak ditemukan.');
        }

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('kategori_barangs', 'kode')->ignore($id),
            ],
            'nama' => [
                'required',
                'string',
                'max:100',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ]);

        $this->repository->update($id, $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kategori barang berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('kategori-barang.index')
            ->with('success', 'Kategori barang berhasil diperbarui.');
    }

    public function destroy(Request $request, int $id)
    {
        $kategori = $this->repository->findById($id);

        if (!$kategori) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori barang tidak ditemukan.',
                ], 404);
            }

            return redirect()
                ->route('kategori-barang.index')
                ->with('error', 'Kategori barang tidak ditemukan.');
        }

        try {
            $this->repository->delete($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Kategori barang berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('kategori-barang.index')
                ->with('success', 'Kategori barang berhasil dihapus.');
        } catch (\Throwable $e) {
            $message =
                'Kategori tidak dapat dihapus karena masih digunakan oleh data barang.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('kategori-barang.index')
                ->with('error', $message);
        }
    }
}