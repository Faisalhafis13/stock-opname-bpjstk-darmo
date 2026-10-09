<?php

namespace App\Repositories;

use App\Models\Barang;
use App\Repositories\Contracts\LaporanStokRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class LaporanStokRepository implements LaporanStokRepositoryInterface
{
    public function getAll(
        ?string $search = null,
        ?int $kategoriId = null,
        int $perPage = 10
    ): LengthAwarePaginator {
        return Barang::query()
            ->with('kategori')
            ->withSum(
                'penambahanBarangs as total_penambahan',
                'jumlah'
            )
            ->withSum(
                'pemakaianBarangs as total_pemakaian',
                'jumlah'
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where(
                        'kode_barang',
                        'like',
                        "%{$search}%"
                    )->orWhere(
                        'nama_barang',
                        'like',
                        "%{$search}%"
                    );
                });
            })
            ->when($kategoriId, function ($query) use ($kategoriId) {
                $query->where(
                    'kategori_barang_id',
                    $kategoriId
                );
            })
            ->orderBy('nama_barang')
            ->paginate($perPage)
            ->withQueryString();
    }
}