<?php

namespace App\Repositories;

use App\Models\StokOpnameDetail;
use App\Repositories\Contracts\LaporanStokOpnameRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LaporanStokOpnameRepository
    implements LaporanStokOpnameRepositoryInterface
{
    public function getAll(
        ?int $periodeId = null,
        ?string $search = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return StokOpnameDetail::query()
            ->with([
                'periode',
                'barang',
                'barang.kategori',
            ])

            ->when($periodeId, function ($query) use ($periodeId) {
                $query->where(
                    'periode_stok_opname_id',
                    $periodeId
                );
            })

            ->when($search, function ($query) use ($search) {
                $query->whereHas('barang', function ($q) use ($search) {
                    $q->where('kode_barang', 'like', "%{$search}%")
                        ->orWhere(
                            'nama_barang',
                            'like',
                            "%{$search}%"
                        );
                });
            })

            ->orderByDesc('periode_stok_opname_id')
            ->orderBy('barang_id')
            ->paginate($perPage)
            ->withQueryString();
    }
}