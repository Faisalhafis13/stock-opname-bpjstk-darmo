<?php

namespace App\Repositories;

use App\Models\PenambahanBarang;
use App\Repositories\Contracts\PenambahanBarangRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PenambahanBarangRepository implements PenambahanBarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return PenambahanBarang::with([
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

    public function findById(int $id): ?PenambahanBarang
    {
        return PenambahanBarang::with([
            'barang',
            'user',
        ])->find($id);
    }

    public function create(array $data): PenambahanBarang
    {
        return PenambahanBarang::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $dataPenambahan = PenambahanBarang::findOrFail($id);

        return $dataPenambahan->update($data);
    }

    public function delete(int $id): bool
    {
        $dataPenambahan = PenambahanBarang::findOrFail($id);

        return $dataPenambahan->delete();
    }
}