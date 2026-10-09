<?php

namespace App\Repositories;

use App\Models\KategoriBarang;
use App\Repositories\Contracts\KategoriBarangRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class KategoriBarangRepository implements KategoriBarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return KategoriBarang::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kode', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?KategoriBarang
    {
        return KategoriBarang::find($id);
    }

    public function create(array $data): KategoriBarang
    {
        return KategoriBarang::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $kategori = $this->findById($id);

        if (!$kategori) {
            return false;
        }

        return $kategori->update($data);
    }

    public function delete(int $id): bool
    {
        $kategori = $this->findById($id);

        if (!$kategori) {
            return false;
        }

        return $kategori->delete();
    }
}