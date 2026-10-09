<?php

namespace App\Exports;

use App\Models\Barang;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanStokExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected ?string $search;
    protected ?int $kategoriId;

    public function __construct(
        ?string $search = null,
        ?int $kategoriId = null
    ) {
        $this->search = $search;
        $this->kategoriId = $kategoriId;
    }

    public function collection(): Collection
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
            ->when($this->search, function ($query) {
                $search = $this->search;

                $query->where(function ($q) use ($search) {
                    $q->where(
                        'kode_barang',
                        'like',
                        "%{$search}%"
                    )->orWhere(
                        'nama_barang',
                        'like',
                        "%{$search}%"
                    );
                });
            })
            ->when($this->kategoriId, function ($query) {
                $query->where(
                    'kategori_barang_id',
                    $this->kategoriId
                );
            })
            ->orderBy('nama_barang')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No.',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Total Penambahan',
            'Total Pemakaian',
            'Jumlah Stok',
            'Status Ketersediaan',
        ];
    }

public function map($barang): array
{
    $penambahan = (int) ($barang->total_penambahan ?? 0);
    $pemakaian = (int) ($barang->total_pemakaian ?? 0);
    $stok = max(0, $penambahan - $pemakaian);

    return [
        $barang->id,
        $barang->kode_barang,
        $barang->nama_barang,
        $barang->kategori->nama ?? '-',
        $barang->satuan,
        $penambahan,
        $pemakaian,
        $stok,
        $stok > 0 ? 'Tersedia' : 'Habis',
    ];
}}