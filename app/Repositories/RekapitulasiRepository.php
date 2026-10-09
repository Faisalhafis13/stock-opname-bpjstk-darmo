<?php

namespace App\Repositories;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\PemakaianBarang;
use App\Models\PenambahanBarang;
use App\Models\PeriodeStokOpname;
use App\Repositories\Contracts\RekapitulasiRepositoryInterface;
use Illuminate\Support\Collection;

class RekapitulasiRepository
    implements RekapitulasiRepositoryInterface
{
public function getSummary(): array
{
    $totalPenambahan = PenambahanBarang::sum('jumlah');

    $totalPemakaian = PemakaianBarang::sum('jumlah');

    $totalStok = max(
        0,
        $totalPenambahan - $totalPemakaian
    );

    $nilaiOpnameTerakhir = PeriodeStokOpname::query()
        ->where('status', 'selesai')
        ->with('details')
        ->orderByDesc('tanggal_opname')
        ->orderByDesc('id')
        ->first();

    $totalNilaiOpname = $nilaiOpnameTerakhir
        ? $nilaiOpnameTerakhir->details->sum('total_nilai')
        : 0;

    return [
        'total_barang' => Barang::count(),

        'total_kategori' => KategoriBarang::count(),

        'total_penambahan' => $totalPenambahan,

        'total_pemakaian' => $totalPemakaian,

        'total_stok' => $totalStok,

        'total_periode_opname' =>
            PeriodeStokOpname::count(),

        'total_opname_selesai' =>
            PeriodeStokOpname::where(
                'status',
                'selesai'
            )->count(),

        'nilai_opname_terakhir' =>
            $totalNilaiOpname,
    ];
}

    public function getByKategori(): Collection
    {
        return KategoriBarang::query()
            ->withCount('barangs')
            ->with([
                'barangs' => function ($query) {
                    $query
                        ->withSum(
                            'penambahanBarangs as total_penambahan',
                            'jumlah'
                        )
                        ->withSum(
                            'pemakaianBarangs as total_pemakaian',
                            'jumlah'
                        );
                },
            ])
            ->orderBy('nama')
            ->get()
            ->map(function ($kategori) {

                $penambahan = $kategori->barangs->sum(
                    fn ($barang) =>
                        (int) ($barang->total_penambahan ?? 0)
                );

                $pemakaian = $kategori->barangs->sum(
                    fn ($barang) =>
                        (int) ($barang->total_pemakaian ?? 0)
                );

                return [
                    'nama' => $kategori->nama,
                    'jumlah_barang' => $kategori->barangs_count,
                    'penambahan' => $penambahan,
                    'pemakaian' => $pemakaian,
                    'stok' => max(
                        0,
                        $penambahan - $pemakaian
                    ),
                ];
            });
    }

    public function getByBarang(): Collection
    {
        return Barang::query()
            ->with('kategori')
            ->withSum(
                'penambahanBarangs as total_penambahan',
                'jumlah'
            )
            ->withSum(
                'pemakaianBarangs as total_pemakaian',
                'jumlah'
            )
            ->orderBy('nama_barang')
            ->get()
            ->map(function ($barang) {

                $penambahan =
                    (int) ($barang->total_penambahan ?? 0);

                $pemakaian =
                    (int) ($barang->total_pemakaian ?? 0);

                return [
                    'kode' => $barang->kode_barang,
                    'nama' => $barang->nama_barang,
                    'kategori' =>
                        $barang->kategori->nama ?? '-',
                    'satuan' => $barang->satuan,
                    'penambahan' => $penambahan,
                    'pemakaian' => $pemakaian,
                    'stok' => max(
                        0,
                        $penambahan - $pemakaian
                    ),
                ];
            });
    }

    public function getByPeriode(): Collection
    {
        return PeriodeStokOpname::query()
            ->with('details')
            ->withCount('details')
            ->orderByDesc('tanggal_opname')
            ->get()
            ->map(function ($periode) {

                return [
                    'periode' => $periode->periode,
                    'tanggal' =>
                        $periode->tanggal_opname,
                    'status' => $periode->status,
                    'jumlah_barang' =>
                        $periode->details_count,
                    'total_nilai' =>
                        $periode->details->sum('total_nilai'),
                ];
            });
    }
}