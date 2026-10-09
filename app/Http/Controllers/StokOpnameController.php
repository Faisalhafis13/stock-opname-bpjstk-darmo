<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PemakaianBarang;
use App\Models\PenambahanBarang;
use App\Models\PeriodeStokOpname;
use App\Repositories\Contracts\StokOpnameDetailRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class StokOpnameController extends Controller
{
    public function __construct(
        protected StokOpnameDetailRepositoryInterface $repository
    ) {
    }

    public function index(Request $request): View
    {
        $periodes = PeriodeStokOpname::withCount('details')
            ->orderByDesc('tanggal_opname')
            ->orderByDesc('id')
            ->get();

        $periodeId = $request->integer('periode_id');

        $periode = $periodeId
            ? $periodes->firstWhere('id', $periodeId)
            : $periodes->first();

        $details = collect();

        if ($periode) {
            $details = $this->repository->getByPeriode($periode->id);
        }

        return view('stok-opname.index', compact(
            'periodes',
            'periode',
            'details'
        ));
    }

    public function generate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'periode_stok_opname_id' => [
                'required',
                'integer',
                'exists:periode_stok_opnames,id',
            ],
        ], [
            'periode_stok_opname_id.required' => 'Periode stok opname wajib dipilih.',
            'periode_stok_opname_id.exists' => 'Periode stok opname tidak ditemukan.',
        ]);

        try {
            $periode = PeriodeStokOpname::findOrFail(
                $validated['periode_stok_opname_id']
            );

            if ($periode->status === 'selesai') {
                return $this->error(
                    $request,
                    'Periode opname sudah selesai dan terkunci.',
                    422,
                    $periode->id
                );
            }

            DB::transaction(function () use ($periode) {
                $startDate = $periode->tanggal_opname
                    ->copy()
                    ->startOfMonth();

                $endDate = $periode->tanggal_opname
                    ->copy()
                    ->endOfMonth();

                $previousDate = $startDate->copy()->subDay();

                $barangs = Barang::orderBy('nama_barang')->get();

                foreach ($barangs as $barang) {
                    $saldoAwalPenambahan = PenambahanBarang::where(
                        'barang_id',
                        $barang->id
                    )
                        ->whereDate('tanggal', '<=', $previousDate)
                        ->sum('jumlah');

                    $saldoAwalPemakaian = PemakaianBarang::where(
                        'barang_id',
                        $barang->id
                    )
                        ->whereDate('tanggal', '<=', $previousDate)
                        ->sum('jumlah');

                    $saldoAwal = max(
                        0,
                        (float) $saldoAwalPenambahan - (float) $saldoAwalPemakaian
                    );

                    $penambahan = PenambahanBarang::where(
                        'barang_id',
                        $barang->id
                    )
                        ->whereBetween('tanggal', [$startDate, $endDate])
                        ->sum('jumlah');

                    $pemakaian = PemakaianBarang::where(
                        'barang_id',
                        $barang->id
                    )
                        ->whereBetween('tanggal', [$startDate, $endDate])
                        ->sum('jumlah');

                    $saldoAkhir = max(
                        0,
                        $saldoAwal + (float) $penambahan - (float) $pemakaian
                    );

                    $existingDetail = $periode->details()
                        ->where('barang_id', $barang->id)
                        ->first();

                    if (
                        $existingDetail &&
                        (float) $existingDetail->harga_satuan > 0
                    ) {
                        $hargaSatuan = (float) $existingDetail->harga_satuan;
                    } else {
                        $hargaSatuan = (float) (
                            PenambahanBarang::where('barang_id', $barang->id)
                                ->whereDate('tanggal', '<=', $endDate)
                                ->where('harga_satuan', '>', 0)
                                ->orderByDesc('tanggal')
                                ->orderByDesc('id')
                                ->value('harga_satuan') ?? 0
                        );
                    }

                    $data = [
                        'saldo_awal' => $saldoAwal,
                        'penambahan' => $penambahan,
                        'pemakaian' => $pemakaian,
                        'saldo_akhir' => $saldoAkhir,
                        'harga_satuan' => $hargaSatuan,
                        'total_nilai' => $saldoAkhir * $hargaSatuan,
                    ];

                    if ($existingDetail) {
                        $this->repository->update(
                            $existingDetail->id,
                            $data
                        );
                    } else {
                        $this->repository->create(array_merge($data, [
                            'periode_stok_opname_id' => $periode->id,
                            'barang_id' => $barang->id,
                        ]));
                    }
                }
            });

            return $this->success(
                $request,
                'Data stok opname berhasil dibuat berdasarkan transaksi.',
                $periode->id
            );
        } catch (Throwable $e) {
            Log::error('Gagal melakukan generate stok opname.', [
                'periode_id' => $request->input('periode_stok_opname_id'),
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                $request,
                'Data stok opname gagal dibuat. Silakan coba kembali.',
                500,
                (int) $request->input('periode_stok_opname_id')
            );
        }
    }

    public function update(
        Request $request,
        int $id
    ): JsonResponse|RedirectResponse {
        $detail = $this->repository->findById($id);

        if (!$detail) {
            return $this->error(
                $request,
                'Data stok opname tidak ditemukan.',
                404
            );
        }

        $periode = $detail->periode;

        if (!$periode || $periode->status === 'selesai') {
            return $this->error(
                $request,
                'Periode opname sudah selesai dan terkunci.',
                422,
                $periode?->id
            );
        }

        $validated = $request->validate([
            'harga_satuan' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], [
            'harga_satuan.required' => 'Harga satuan wajib diisi.',
            'harga_satuan.numeric' => 'Harga satuan harus berupa angka.',
            'harga_satuan.min' => 'Harga satuan tidak boleh kurang dari nol.',
        ]);

        try {
            $hargaSatuan = (float) $validated['harga_satuan'];
            $totalNilai = (float) $detail->saldo_akhir * $hargaSatuan;

            $this->repository->update($id, [
                'harga_satuan' => $hargaSatuan,
                'total_nilai' => $totalNilai,
            ]);

            return $this->success(
                $request,
                'Harga satuan berhasil diperbarui.',
                $periode->id
            );
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui harga stok opname.', [
                'detail_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                $request,
                'Harga satuan gagal diperbarui. Silakan coba kembali.',
                500,
                $periode->id
            );
        }
    }

    public function selesai(
        Request $request,
        int $periodeId
    ): JsonResponse|RedirectResponse {
        $periode = PeriodeStokOpname::find($periodeId);

        if (!$periode) {
            return $this->error(
                $request,
                'Periode stok opname tidak ditemukan.',
                404,
                $periodeId
            );
        }

        if ($periode->status === 'selesai') {
            return $this->error(
                $request,
                'Periode opname sudah selesai.',
                422,
                $periode->id
            );
        }

        if ($periode->details()->count() === 0) {
            return $this->error(
                $request,
                'Data opname belum dibuat. Silakan generate data terlebih dahulu.',
                422,
                $periode->id
            );
        }

        try {
            DB::transaction(function () use ($periode) {
                $periodeTerkunci = PeriodeStokOpname::whereKey($periode->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($periodeTerkunci->status === 'selesai') {
                    throw new \RuntimeException(
                        'Periode opname sudah selesai.'
                    );
                }

                if ($periodeTerkunci->details()->count() === 0) {
                    throw new \RuntimeException(
                        'Data opname belum dibuat.'
                    );
                }

                $periodeTerkunci->update([
                    'status' => 'selesai',
                ]);
            });

            return $this->success(
                $request,
                'Periode stok opname berhasil diselesaikan dan dikunci.',
                $periode->id
            );
        } catch (Throwable $e) {
            Log::error('Gagal menyelesaikan stok opname.', [
                'periode_id' => $periodeId,
                'error' => $e->getMessage(),
            ]);

            return $this->error(
                $request,
                'Periode stok opname gagal diselesaikan.',
                500,
                $periode->id
            );
        }
    }

    private function success(
        Request $request,
        string $message,
        ?int $periodeId = null
    ): JsonResponse|RedirectResponse {
        $url = route('stok-opname.index', $periodeId
            ? ['periode_id' => $periodeId]
            : []);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect_url' => $url,
            ]);
        }

        return redirect()->to($url)->with('success', $message);
    }

    private function error(
        Request $request,
        string $message,
        int $status = 422,
        ?int $periodeId = null
    ): JsonResponse|RedirectResponse {
        $url = route('stok-opname.index', $periodeId
            ? ['periode_id' => $periodeId]
            : []);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'redirect_url' => $url,
            ], $status);
        }

        return redirect()->to($url)->with('error', $message);
    }
}