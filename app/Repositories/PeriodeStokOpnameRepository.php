<?php

namespace App\Repositories;

use App\Models\PeriodeStokOpname;
use App\Repositories\Contracts\PeriodeStokOpnameRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PeriodeStokOpnameRepository
    implements PeriodeStokOpnameRepositoryInterface
{
    public function getAll(
        int $perPage = 10,
        ?string $search = null
    ): LengthAwarePaginator {
        return PeriodeStokOpname::with('user')
            ->withCount('details')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('periode', 'like', "%{$search}%")
                        ->orWhere('tanggal_opname', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('tanggal_opname')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(
        int $id
    ): ?PeriodeStokOpname {
        return PeriodeStokOpname::with([
            'user',
            'details',
        ])->find($id);
    }

    public function create(
        array $data
    ): PeriodeStokOpname {
        return PeriodeStokOpname::create($data);
    }

    public function update(
        int $id,
        array $data
    ): bool {
        $periode = PeriodeStokOpname::findOrFail($id);

        return $periode->update($data);
    }

    public function delete(
        int $id
    ): bool {
        $periode = PeriodeStokOpname::findOrFail($id);

        return $periode->delete();
    }
}