<?php

namespace App\Repositories;

use App\Models\PemakaianBarang;
use App\Repositories\Contracts\PemakaianBarangRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PemakaianBarangRepository implements PemakaianBarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return PemakaianBarang::with([
            'pengajuan',
            'barang',
            'barang.kategori',
            'user',
        ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('tanggal', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('barang', function ($query) use ($search) {
                            $query->where('kode_barang', 'like', "%{$search}%")
                                ->orWhere('nama_barang', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?PemakaianBarang
    {
        return PemakaianBarang::with([
            'pengajuan',
            'barang',
            'user',
        ])->find($id);
    }

    public function create(array $data): PemakaianBarang
    {
        return PemakaianBarang::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $pemakaian = PemakaianBarang::findOrFail($id);

        return $pemakaian->update($data);
    }

    public function delete(int $id): bool
    {
        $pemakaian = PemakaianBarang::findOrFail($id);

        return $pemakaian->delete();
    }
}