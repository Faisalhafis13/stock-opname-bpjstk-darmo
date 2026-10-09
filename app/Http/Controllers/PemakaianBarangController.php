<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PenambahanBarang;
use App\Models\PemakaianBarang;
use App\Repositories\Contracts\PemakaianBarangRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class PemakaianBarangController extends Controller
{
    public function __construct(
        protected PemakaianBarangRepositoryInterface $repository
    ) {}

    /**
     * Menampilkan daftar pemakaian barang.
     */
    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = trim((string) $request->input('search', ''));

        $pemakaianBarangs = $this->repository->getAll(
            $perPage,
            $search
        );

        $barangs = Barang::with('kategori')
            ->orderBy('nama_barang')
            ->get();

        return view('pemakaian-barang.index', compact(
            'pemakaianBarangs',
            'barangs',
            'perPage',
            'search'
        ));
    }

    /**
     * Menyimpan data pemakaian barang.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'barang_id' => [
                'required',
                'exists:barangs,id',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'jumlah.required' => 'Jumlah pemakaian wajib diisi.',
            'jumlah.integer' => 'Jumlah pemakaian harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah pemakaian minimal 1.',
        ]);

        try {
            $result = DB::transaction(function () use ($validated) {
                $stokTersedia = $this->hitungStok(
                    (int) $validated['barang_id'],
                    $validated['tanggal']
                );

                if ((int) $validated['jumlah'] > $stokTersedia) {
                    return [
                        'success' => false,
                        'message' => 'Jumlah pemakaian melebihi stok tersedia. Stok saat ini: '
                            . number_format($stokTersedia, 0, ',', '.'),
                        'status' => 422,
                    ];
                }

                $validated['user_id'] = auth()->id();

                // Pemakaian manual tidak berasal dari pengajuan.
                $validated['pengajuan_id'] = null;

                $this->repository->create($validated);

                return [
                    'success' => true,
                    'message' => 'Data pemakaian barang berhasil ditambahkan.',
                    'status' => 200,
                ];
            });

            if (!$result['success']) {
                return $this->respondError(
                    $request,
                    $result['message'],
                    $result['status']
                );
            }

            return $this->respondSuccess(
                $request,
                $result['message']
            );
        } catch (Throwable $e) {
            Log::error('Gagal menambahkan pemakaian barang.', [
                'error' => $e->getMessage(),
            ]);

            return $this->respondError(
                $request,
                'Data pemakaian barang gagal ditambahkan. Silakan coba kembali.',
                500
            );
        }
    }

    /**
     * Memperbarui data pemakaian barang.
     */
    public function update(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        $validated = $request->validate([
            'barang_id' => [
                'required',
                'exists:barangs,id',
            ],
            'tanggal' => [
                'required',
                'date',
            ],
            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.exists' => 'Barang yang dipilih tidak ditemukan.',
            'tanggal.required' => 'Tanggal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'jumlah.required' => 'Jumlah pemakaian wajib diisi.',
            'jumlah.integer' => 'Jumlah pemakaian harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah pemakaian minimal 1.',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $id) {
                $dataLama = $this->repository->findById($id);

                if (!$dataLama) {
                    return [
                        'success' => false,
                        'message' => 'Data pemakaian tidak ditemukan.',
                        'status' => 404,
                    ];
                }

                /*
                 * Pemakaian yang berasal dari pengajuan tidak boleh
                 * diedit melalui transaksi manual.
                 */
                if ($dataLama->pengajuan_id !== null) {
                    return [
                        'success' => false,
                        'message' => 'Pemakaian yang berasal dari pengajuan tidak dapat diubah secara manual.',
                        'status' => 422,
                    ];
                }

                /*
                 * Hitung stok tanpa memasukkan transaksi yang sedang
                 * diedit ke dalam total pemakaian.
                 */
                $stokTersedia = $this->hitungStok(
                    (int) $validated['barang_id'],
                    $validated['tanggal'],
                    $id
                );

                if ((int) $validated['jumlah'] > $stokTersedia) {
                    return [
                        'success' => false,
                        'message' => 'Jumlah pemakaian melebihi stok tersedia. Stok tersedia: '
                            . number_format($stokTersedia, 0, ',', '.'),
                        'status' => 422,
                    ];
                }

                $this->repository->update($id, $validated);

                return [
                    'success' => true,
                    'message' => 'Data pemakaian barang berhasil diperbarui.',
                    'status' => 200,
                ];
            });

            if (!$result['success']) {
                return $this->respondError(
                    $request,
                    $result['message'],
                    $result['status']
                );
            }

            return $this->respondSuccess(
                $request,
                $result['message']
            );
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui pemakaian barang.', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->respondError(
                $request,
                'Data pemakaian barang gagal diperbarui. Silakan coba kembali.',
                500
            );
        }
    }

    /**
     * Menghapus data pemakaian barang.
     */
    public function destroy(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        try {
            $result = DB::transaction(function () use ($id) {
                $data = $this->repository->findById($id);

                if (!$data) {
                    return [
                        'success' => false,
                        'message' => 'Data pemakaian tidak ditemukan.',
                        'status' => 404,
                    ];
                }

                /*
                 * Pemakaian hasil approval pengajuan tidak boleh
                 * dihapus secara manual.
                 */
                if ($data->pengajuan_id !== null) {
                    return [
                        'success' => false,
                        'message' => 'Pemakaian yang berasal dari pengajuan tidak dapat dihapus secara manual.',
                        'status' => 422,
                    ];
                }

                $this->repository->delete($id);

                return [
                    'success' => true,
                    'message' => 'Data pemakaian barang berhasil dihapus.',
                    'status' => 200,
                ];
            });

            if (!$result['success']) {
                return $this->respondError(
                    $request,
                    $result['message'],
                    $result['status']
                );
            }

            return $this->respondSuccess(
                $request,
                $result['message']
            );
        } catch (Throwable $e) {
            Log::error('Gagal menghapus pemakaian barang.', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->respondError(
                $request,
                'Data pemakaian barang gagal dihapus. Silakan coba kembali.',
                500
            );
        }
    }

    /**
     * Menghitung stok berdasarkan tanggal transaksi.
     *
     * Stok tersedia = total penambahan - total pemakaian.
     */
    private function hitungStok(
        int $barangId,
        string $tanggal,
        ?int $excludeId = null
    ): int {
        $totalPenambahan = PenambahanBarang::where(
            'barang_id',
            $barangId
        )
            ->whereDate('tanggal', '<=', $tanggal)
            ->sum('jumlah');

        $queryPemakaian = PemakaianBarang::where(
            'barang_id',
            $barangId
        )
            ->whereDate('tanggal', '<=', $tanggal);

        if ($excludeId !== null) {
            $queryPemakaian->where('id', '!=', $excludeId);
        }

        $totalPemakaian = $queryPemakaian->sum('jumlah');

        return max(
            0,
            (int) ($totalPenambahan - $totalPemakaian)
        );
    }

    /**
     * Respons sukses: JSON untuk AJAX, redirect untuk form biasa.
     */
    private function respondSuccess(
        Request $request,
        string $message
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('pemakaian-barang.index')
            ->with('success', $message);
    }

    /**
     * Respons gagal: JSON untuk AJAX, redirect untuk form biasa.
     */
    private function respondError(
        Request $request,
        string $message,
        int $status = 422
    ): JsonResponse|RedirectResponse {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $status);
        }

        return redirect()
            ->route('pemakaian-barang.index')
            ->with('error', $message);
    }
}