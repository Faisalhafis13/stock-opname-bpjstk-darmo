<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\PeriodeStokOpnameRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class PeriodeStokOpnameController extends Controller
{
    public function __construct(
        protected PeriodeStokOpnameRepositoryInterface $repository
    ) {
    }

    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $search = trim((string) $request->input('search', ''));

        $periodes = $this->repository->getAll($perPage, $search);

        return view('periode-stok-opname.index', compact(
            'periodes',
            'perPage',
            'search'
        ));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'periode' => [
                'required',
                'string',
                'max:20',
                'unique:periode_stok_opnames,periode',
            ],
            'tanggal_opname' => [
                'required',
                'date',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'periode.required' => 'Periode wajib diisi.',
            'periode.max' => 'Periode maksimal 20 karakter.',
            'periode.unique' => 'Periode tersebut sudah tersedia.',
            'tanggal_opname.required' => 'Tanggal opname wajib diisi.',
            'tanggal_opname.date' => 'Tanggal opname tidak valid.',
        ]);

        try {
            $validated['status'] = 'draft';
            $validated['user_id'] = auth()->id();

            $this->repository->create($validated);

            return $this->success(
                $request,
                'Periode stok opname berhasil dibuat.'
            );
        } catch (Throwable $e) {
            Log::error('Gagal membuat periode stok opname.', [
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                $request,
                'Periode stok opname gagal dibuat. Silakan coba kembali.',
                500
            );
        }
    }

    public function update(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        $periode = $this->repository->findById($id);

        if (!$periode) {
            return $this->error(
                $request,
                'Periode stok opname tidak ditemukan.',
                404
            );
        }

        if ($periode->status === 'selesai') {
            return $this->error(
                $request,
                'Periode yang sudah selesai tidak dapat diubah.',
                422
            );
        }

        $validated = $request->validate([
            'periode' => [
                'required',
                'string',
                'max:20',
                Rule::unique('periode_stok_opnames', 'periode')
                    ->ignore($id),
            ],
            'tanggal_opname' => [
                'required',
                'date',
            ],
            'keterangan' => [
                'nullable',
                'string',
            ],
        ], [
            'periode.required' => 'Periode wajib diisi.',
            'periode.max' => 'Periode maksimal 20 karakter.',
            'periode.unique' => 'Periode tersebut sudah tersedia.',
            'tanggal_opname.required' => 'Tanggal opname wajib diisi.',
            'tanggal_opname.date' => 'Tanggal opname tidak valid.',
        ]);

        try {
            $this->repository->update($id, $validated);

            return $this->success(
                $request,
                'Periode stok opname berhasil diperbarui.'
            );
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui periode stok opname.', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                $request,
                'Periode stok opname gagal diperbarui. Silakan coba kembali.',
                500
            );
        }
    }

    public function destroy(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        $periode = $this->repository->findById($id);

        if (!$periode) {
            return $this->error(
                $request,
                'Periode stok opname tidak ditemukan.',
                404
            );
        }

        if ($periode->status === 'selesai') {
            return $this->error(
                $request,
                'Periode yang sudah selesai tidak dapat dihapus.',
                422
            );
        }

        if ($periode->details()->exists()) {
            return $this->error(
                $request,
                'Periode tidak dapat dihapus karena sudah memiliki data opname.',
                422
            );
        }

        try {
            $this->repository->delete($id);

            return $this->success(
                $request,
                'Periode stok opname berhasil dihapus.'
            );
        } catch (Throwable $e) {
            Log::error('Gagal menghapus periode stok opname.', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                $request,
                'Periode stok opname gagal dihapus. Pastikan periode belum digunakan.',
                500
            );
        }
    }

    private function success(
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
            ->route('periode-stok-opname.index')
            ->with('success', $message);
    }

    private function error(
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
            ->route('periode-stok-opname.index')
            ->with('error', $message);
    }
}