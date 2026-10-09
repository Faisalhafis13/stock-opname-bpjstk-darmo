<?php

namespace App\Repositories;

use App\Models\Barang;
use App\Repositories\Contracts\BarangRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BarangRepository implements BarangRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return Barang::with('kategori')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kode_barang', 'like', "%{$search}%")
                        ->orWhere('nama_barang', 'like', "%{$search}%")
                        ->orWhere('satuan', 'like', "%{$search}%")
                        ->orWhere('keterangan', 'like', "%{$search}%")
                        ->orWhereHas('kategori', function ($query) use ($search) {
                            $query->where('nama', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('nama_barang')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?Barang
    {
        return Barang::with('kategori')->find($id);
    }

    public function create(array $data): Barang
    {
        return Barang::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $barang = Barang::findOrFail($id);

        return $barang->update($data);
    }

    public function delete(int $id): bool
    {
        $barang = Barang::findOrFail($id);

        return $barang->delete();
    }
}