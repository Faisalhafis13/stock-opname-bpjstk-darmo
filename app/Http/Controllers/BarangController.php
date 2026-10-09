<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Repositories\Contracts\BarangRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function __construct(
        protected BarangRepositoryInterface $barangRepository
    ) {
    }

    /**
     * Menampilkan data barang.
     */
    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = trim((string) $request->input('search', ''));

        $barangs = $this->barangRepository->getAll(
            $perPage,
            $search
        );

        $kategoriBarangs = KategoriBarang::orderBy('nama')->get();

        return view('barang.index', compact(
            'barangs',
            'kategoriBarangs',
            'perPage',
            'search'
        ));
    }

    /**
     * Menyimpan barang baru.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'kategori_barang_id' => [
                'required',
                'exists:kategori_barangs,id',
            ],
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                'unique:barangs,kode_barang',
            ],
            'nama_barang' => [
                'required',
                'string',
                'max:255',
            ],
            'satuan' => [
                'required',
                'string',
                'max:50',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'kategori_barang_id.required' => 'Kategori barang wajib dipilih.',
            'kategori_barang_id.exists' => 'Kategori barang tidak valid.',
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
        ]);

        $this->barangRepository->create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data barang berhasil ditambahkan.',
            ]);
        }

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil ditambahkan.');
    }

    /**
     * Memperbarui barang.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'kategori_barang_id' => [
                'required',
                'exists:kategori_barangs,id',
            ],
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barangs', 'kode_barang')->ignore($id),
            ],
            'nama_barang' => [
                'required',
                'string',
                'max:255',
            ],
            'satuan' => [
                'required',
                'string',
                'max:50',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'kategori_barang_id.required' => 'Kategori barang wajib dipilih.',
            'kategori_barang_id.exists' => 'Kategori barang tidak valid.',
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
        ]);

        $this->barangRepository->update($id, $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data barang berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('barang.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    /**
     * Menghapus barang.
     */
    public function destroy(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        try {
            $this->barangRepository->delete($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data barang berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('barang.index')
                ->with('success', 'Data barang berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);

            $message = 'Data barang tidak dapat dihapus karena sudah digunakan pada transaksi.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('barang.index')
                ->with('error', $message);
        }
    }
}