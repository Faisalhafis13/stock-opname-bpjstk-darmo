<?php

namespace App\Repositories\Contracts;

use App\Models\Pengajuan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PengajuanRepositoryInterface
{
    public function getUserPengajuan(
        int $userId,
        int $perPage = 10,
        ?string $search = null
    ): LengthAwarePaginator;

    public function getAll(
        int $perPage = 10,
        ?string $search = null
    ): LengthAwarePaginator;

    public function create(array $data): Pengajuan;

    public function approve(
        int $id,
        int $adminId,
        ?string $catatan = null
    ): bool;

    public function reject(
        int $id,
        int $adminId,
        ?string $catatan = null
    ): bool;
}