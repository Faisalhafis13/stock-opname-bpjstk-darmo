<?php

namespace App\Exports;

use App\Models\StokOpnameDetail;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanStokOpnameExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize
{
    protected ?int $periodeId;
    protected ?string $search;

    public function __construct(
        ?int $periodeId = null,
        ?string $search = null
    ) {
        $this->periodeId = $periodeId;
        $this->search = $search;
    }

    public function collection(): Collection
    {
        return StokOpnameDetail::query()
            ->with([
                'periode',
                'barang',
                'barang.kategori',
            ])
            ->when($this->periodeId, function ($query) {
                $query->where(
                    'periode_stok_opname_id',
                    $this->periodeId
                );
            })
            ->when($this->search, function ($query) {
                $search = $this->search;

                $query->whereHas('barang', function ($q) use ($search) {
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
            ->orderByDesc('periode_stok_opname_id')
            ->orderBy('barang_id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Periode',
            'Tanggal Opname',
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Saldo Awal',
            'Penambahan',
            'Pemakaian',
            'Saldo Akhir',
            'Harga Satuan',
            'Total Nilai',
        ];
    }

    public function map($detail): array
    {
        return [
            $detail->periode->periode ?? '-',
            $detail->periode->tanggal_opname ?? null,
            $detail->barang->kode_barang ?? '-',
            $detail->barang->nama_barang ?? '-',
            $detail->barang->kategori->nama ?? '-',
            $detail->barang->satuan ?? '-',
            $detail->saldo_awal,
            $detail->penambahan,
            $detail->pemakaian,
            $detail->saldo_akhir,
            $detail->harga_satuan,
            $detail->total_nilai,
        ];
    }
}