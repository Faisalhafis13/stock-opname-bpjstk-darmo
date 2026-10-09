<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface RekapitulasiRepositoryInterface
{
    public function getSummary(): array;

    public function getByKategori(): Collection;

    public function getByBarang(): Collection;

    public function getByPeriode(): Collection;
}