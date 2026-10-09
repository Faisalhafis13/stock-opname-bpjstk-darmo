<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Repositories\Contracts\PengajuanRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PengajuanController extends Controller
{
    public function __construct(
        protected PengajuanRepositoryInterface $pengajuanRepository
    ) {
    }

    public function index(Request $request): View
    {
        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $user = auth()->user();
        $search = trim((string) $request->input('search', ''));

        if ($user->role?->name === 'Admin') {
            $pengajuans = $this->pengajuanRepository
                ->getAll($perPage, $search);

            return view(
                'pengajuan.admin.index',
                compact('pengajuans', 'perPage', 'search')
            );
        }

        $pengajuans = $this->pengajuanRepository
            ->getUserPengajuan($user->id, $perPage, $search);

        $barangs = Barang::orderBy('nama_barang')->get();

        return view(
            'pengajuan.user.index',
            compact('pengajuans', 'barangs', 'perPage', 'search')
        );
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'barang_id' => ['required', 'integer', 'exists:barangs,id'],
            'jumlah' => ['required', 'numeric', 'gt:0'],
            'keterangan' => ['nullable', 'string'],
        ], [
            'barang_id.required' => 'Barang wajib dipilih.',
            'barang_id.integer' => 'Barang tidak valid.',
            'barang_id.exists' => 'Barang tidak valid.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.numeric' => 'Jumlah harus berupa angka.',
            'jumlah.gt' => 'Jumlah harus lebih dari 0.',
        ]);

        $validated['user_id'] = auth()->id();

        $this->pengajuanRepository->create($validated);

        $message = 'Pengajuan barang berhasil dikirim.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('pengajuan.index')
            ->with('success', $message);
    }

    public function approve(
        Request $request,
        int $id
    ): RedirectResponse|JsonResponse {
        $this->ensureAdmin();

        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string'],
        ]);

        try {
            $this->pengajuanRepository->approve(
                $id,
                auth()->id(),
                $validated['catatan_admin'] ?? null
            );

            $message = 'Pengajuan berhasil disetujui dan dicatat sebagai pemakaian barang.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()
                ->route('pengajuan.index')
                ->with('success', $message);
        } catch (RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('pengajuan.index')
                ->with('error', $e->getMessage());
        }
    }

    public function reject(
        Request $request,
        int $id
    ): RedirectResponse|JsonResponse {
        $this->ensureAdmin();

        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string'],
        ]);

        try {
            $this->pengajuanRepository->reject(
                $id,
                auth()->id(),
                $validated['catatan_admin'] ?? null
            );

            $message = 'Pengajuan berhasil ditolak.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            return redirect()
                ->route('pengajuan.index')
                ->with('success', $message);
        } catch (RuntimeException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()
                ->route('pengajuan.index')
                ->with('error', $e->getMessage());
        }
    }

    private function ensureAdmin(): void
    {
        if (auth()->user()->role?->name !== 'Admin') {
            abort(403, 'Anda tidak memiliki izin untuk memproses pengajuan.');
        }
    }
}