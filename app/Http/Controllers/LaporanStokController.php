<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Repositories\Contracts\LaporanStokRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use App\Exports\LaporanStokExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanStokController extends Controller
{
    public function __construct(
        protected LaporanStokRepositoryInterface $repository
    ) {
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search', ''));

        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $kategoriId = $request->filled('kategori_id')
            ? (int) $request->input('kategori_id')
            : null;

        // Pastikan kategori yang dipilih benar-benar tersedia.
        if (
            $kategoriId !== null
            && !KategoriBarang::whereKey($kategoriId)->exists()
        ) {
            $kategoriId = null;
        }

        $barangs = $this->repository->getAll(
            $search !== '' ? $search : null,
            $kategoriId,
            $perPage
        );

        // Pertahankan filter pencarian saat berpindah halaman.
        if ($barangs instanceof LengthAwarePaginator) {
            $barangs->withQueryString();
        }

        $kategoriBarangs = KategoriBarang::query()
            ->orderBy('nama')
            ->get();

        return view('laporan-stok.index', compact(
            'barangs',
            'kategoriBarangs',
            'search',
            'kategoriId',
            'perPage'
        ));
    }

    public function exportExcel(Request $request)
{
    $validated = $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
        'kategori_id' => ['nullable', 'integer'],
    ]);

    $search = filled($validated['search'] ?? null)
        ? $validated['search']
        : null;

    $kategoriId = filled($validated['kategori_id'] ?? null)
        ? (int) $validated['kategori_id']
        : null;

    return Excel::download(
        new LaporanStokExport($search, $kategoriId),
        'laporan-stok-' . now()->format('Y-m-d_His') . '.xlsx'
    );
}
}