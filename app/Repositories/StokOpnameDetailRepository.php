<?php

namespace App\Repositories;

use App\Models\StokOpnameDetail;
use App\Repositories\Contracts\StokOpnameDetailRepositoryInterface;
use Illuminate\Support\Collection;

class StokOpnameDetailRepository
    implements StokOpnameDetailRepositoryInterface
{
    public function getByPeriode(int $periodeId): Collection
    {
        return StokOpnameDetail::with([
                'barang',
                'barang.kategori',
            ])
            ->where('periode_stok_opname_id', $periodeId)
            ->orderBy('id')
            ->get();
    }

    public function findById(int $id): ?StokOpnameDetail
    {
        return StokOpnameDetail::with([
            'barang',
            'barang.kategori',
        ])->find($id);
    }

    public function create(array $data): StokOpnameDetail
    {
        return StokOpnameDetail::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $detail = $this->findById($id);

        if (!$detail) {
            return false;
        }

        return $detail->update($data);
    }

    public function delete(int $id): bool
    {
        $detail = $this->findById($id);

        if (!$detail) {
            return false;
        }

        return $detail->delete();
    }
}