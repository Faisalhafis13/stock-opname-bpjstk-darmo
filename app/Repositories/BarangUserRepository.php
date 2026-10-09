<?php

namespace App\Repositories;

use App\Models\Barang;
use App\Repositories\Contracts\BarangUserRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BarangUserRepository implements BarangUserRepositoryInterface
{
    public function getAll(int $perPage = 10, ?string $search = null): LengthAwarePaginator
    {
        return Barang::query()
            ->with('kategori')
            ->withSum(
                'penambahanBarangs as total_penambahan',
                'jumlah'
            )
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
            ->withSum(
                'pemakaianBarangs as total_pemakaian',
                'jumlah'
            )
            ->orderBy('nama_barang')
            ->paginate($perPage)
            ->withQueryString();
    }
}