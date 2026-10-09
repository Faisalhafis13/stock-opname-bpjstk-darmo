<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Repositories\Contracts\PenambahanBarangRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PenambahanBarangController extends Controller
{
    public function __construct(
        protected PenambahanBarangRepositoryInterface $repository
    ) {
    }

    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = trim((string) $request->input('search', ''));

        $penambahanBarangs = $this->repository->getAll(
            $perPage,
            $search
        );

        $barangs = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();

        return view('penambahan-barang.index', compact(
            'penambahanBarangs',
            'barangs',
            'perPage',
            'search'
        ));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga_satuan' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.exists' => 'Barang tidak valid.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'harga_satuan.min' => 'Harga satuan tidak boleh kurang dari 0.',
        ]);

        $validated['total_harga'] =
            $validated['jumlah'] * $validated['harga_satuan'];

        // Identitas pengguna selalu berasal dari akun yang login.
        $validated['user_id'] = auth()->id();

        $this->repository->create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data penambahan barang berhasil ditambahkan.',
            ]);
        }

        return redirect()
            ->route('penambahan-barang.index')
            ->with('success', 'Data penambahan barang berhasil ditambahkan.');
    }

    public function update(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'barang_id' => ['required', 'exists:barangs,id'],
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'harga_satuan' => ['required', 'numeric', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.exists' => 'Barang tidak valid.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.integer' => 'Jumlah harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 1.',
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'harga_satuan.min' => 'Harga satuan tidak boleh kurang dari 0.',
        ]);

        $validated['total_harga'] =
            $validated['jumlah'] * $validated['harga_satuan'];

        $this->repository->update($id, $validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data penambahan barang berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('penambahan-barang.index')
            ->with('success', 'Data penambahan barang berhasil diperbarui.');
    }

    public function destroy(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        try {
            $this->repository->delete($id);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data penambahan barang berhasil dihapus.',
                ]);
            }

            return redirect()
                ->route('penambahan-barang.index')
                ->with('success', 'Data penambahan barang berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);

            $message = 'Data penambahan barang gagal dihapus.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()
                ->route('penambahan-barang.index')
                ->with('error', $message);
        }
    }
}