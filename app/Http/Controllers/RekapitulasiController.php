<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\RekapitulasiRepositoryInterface;
use Illuminate\View\View;
use App\Exports\RekapitulasiExport;
use Maatwebsite\Excel\Facades\Excel;

class RekapitulasiController extends Controller
{
    public function __construct(
        protected RekapitulasiRepositoryInterface $repository
    ) {
    }

    public function index(): View
    {
        $summary = $this->repository->getSummary();

        $byKategori = $this->repository->getByKategori();

        $byBarang = $this->repository->getByBarang();

        $byPeriode = $this->repository->getByPeriode();

        return view('rekapitulasi.index', [
            'summary' => $summary,
            'byKategori' => $byKategori,
            'byBarang' => $byBarang,
            'byPeriode' => $byPeriode,
        ]);
    }

    public function exportExcel()
{
    return Excel::download(
        new RekapitulasiExport($this->repository),
        'rekapitulasi-' . now()->format('Y-m-d_His') . '.xlsx'
    );
}
}