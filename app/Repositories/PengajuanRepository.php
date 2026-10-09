<?php

namespace App\Repositories;

use App\Models\Pengajuan;
use App\Models\PemakaianBarang;
use App\Repositories\Contracts\PengajuanRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PengajuanRepository implements PengajuanRepositoryInterface
{
    public function getUserPengajuan(
        int $userId,
        int $perPage = 10,
        ?string $search = null
    ): LengthAwarePaginator {
        return Pengajuan::with('barang')
            ->where('user_id', $userId)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhere('catatan_admin', 'like', "%{$search}%")
                        ->orWhere('created_at', 'like', "%{$search}%")
                        ->orWhereHas('barang', function ($query) use ($search) {
                            $query->where('kode_barang', 'like', "%{$search}%")
                                ->orWhere('nama_barang', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getAll(
        int $perPage = 10,
        ?string $search = null
    ): LengthAwarePaginator {
        return Pengajuan::with([
                'user',
                'barang',
                'processor',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('status', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhere('catatan_admin', 'like', "%{$search}%")
                        ->orWhere('created_at', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('processor', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('barang', function ($query) use ($search) {
                            $query->where('kode_barang', 'like', "%{$search}%")
                                ->orWhere('nama_barang', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): Pengajuan
    {
        return Pengajuan::create($data);
    }

    public function approve(
        int $id,
        int $adminId,
        ?string $catatan = null
    ): bool {
        return DB::transaction(function () use (
            $id,
            $adminId,
            $catatan
        ) {

            $pengajuan = Pengajuan::with('barang')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($pengajuan->status !== 'menunggu') {
                throw new RuntimeException(
                    'Pengajuan sudah diproses sebelumnya.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Hitung stok saat ini
            |--------------------------------------------------------------------------
            */

            $totalPenambahan = $pengajuan->barang
                ->penambahanBarangs()
                ->sum('jumlah');

            $totalPemakaian = $pengajuan->barang
                ->pemakaianBarangs()
                ->sum('jumlah');

            $stokSaatIni = max(
                0,
                $totalPenambahan - $totalPemakaian
            );

            /*
            |--------------------------------------------------------------------------
            | Pastikan stok mencukupi
            |--------------------------------------------------------------------------
            */

            if ((float) $pengajuan->jumlah > (float) $stokSaatIni) {
                throw new RuntimeException(
                    'Stok barang tidak mencukupi untuk menyetujui pengajuan.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Catat sebagai pemakaian barang
            |--------------------------------------------------------------------------
            */

PemakaianBarang::create([
    'pengajuan_id' => $pengajuan->id,
    'barang_id' => $pengajuan->barang_id,
    'tanggal' => now()->toDateString(),
    'jumlah' => $pengajuan->jumlah,
    'keterangan' => 'Pemakaian berdasarkan pengajuan #' . $pengajuan->id,
    'user_id' => $pengajuan->user_id,
]);
            /*
            |--------------------------------------------------------------------------
            | Update pengajuan
            |--------------------------------------------------------------------------
            */

            $pengajuan->update([
                'status' => 'disetujui',
                'catatan_admin' => $catatan,
                'processed_by' => $adminId,
                'processed_at' => now(),
            ]);

            return true;
        });
    }

    public function reject(
        int $id,
        int $adminId,
        ?string $catatan = null
    ): bool {
        $pengajuan = Pengajuan::findOrFail($id);

        if ($pengajuan->status !== 'menunggu') {
            throw new RuntimeException(
                'Pengajuan sudah diproses sebelumnya.'
            );
        }

        return $pengajuan->update([
            'status' => 'ditolak',
            'catatan_admin' => $catatan,
            'processed_by' => $adminId,
            'processed_at' => now(),
        ]);
    }
}