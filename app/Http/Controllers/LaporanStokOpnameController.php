<?php

namespace App\Http\Controllers;

use App\Models\PeriodeStokOpname;
use App\Repositories\Contracts\LaporanStokOpnameRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use App\Exports\LaporanStokOpnameExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanStokOpnameController extends Controller
{
    public function __construct(
        protected LaporanStokOpnameRepositoryInterface $repository
    ) {
    }

    public function index(Request $request): View
    {
        $periodeId = $request->filled('periode_id')
            ? (int) $request->input('periode_id')
            : null;

        // Abaikan ID periode yang tidak tersedia.
        if (
            $periodeId !== null
            && !PeriodeStokOpname::whereKey($periodeId)->exists()
        ) {
            $periodeId = null;
        }

        $search = trim((string) $request->input('search', ''));

        $perPage = (int) $request->input('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 10;
        }

        $details = $this->repository->getAll(
            $periodeId,
            $search !== '' ? $search : null,
            $perPage
        );

        // Pertahankan parameter filter saat berpindah halaman.
        if ($details instanceof LengthAwarePaginator) {
            $details->withQueryString();
        }

        $periodes = PeriodeStokOpname::query()
            ->orderByDesc('tanggal_opname')
            ->orderByDesc('id')
            ->get();

        return view(
            'laporan-stok-opname.index',
            compact(
                'details',
                'periodes',
                'periodeId',
                'search',
                'perPage'
            )
        );
    }

    public function exportExcel(Request $request)
{
    $validated = $request->validate([
        'periode_id' => ['nullable', 'integer'],
        'search' => ['nullable', 'string', 'max:255'],
    ]);

    $periodeId = filled($validated['periode_id'] ?? null)
        ? (int) $validated['periode_id']
        : null;

    $search = filled($validated['search'] ?? null)
        ? $validated['search']
        : null;

    return Excel::download(
        new LaporanStokOpnameExport($periodeId, $search),
        'laporan-stok-opname-' . now()->format('Y-m-d_His') . '.xlsx'
    );
}
}