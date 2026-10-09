<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface LaporanStokRepositoryInterface
{
    public function getAll(
        ?string $search = null,
        ?int $kategoriId = null,
        int $perPage = 10
    ): LengthAwarePaginator;
}