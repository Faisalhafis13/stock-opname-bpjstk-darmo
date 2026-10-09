<?php

namespace App\Exports;

use App\Repositories\Contracts\RekapitulasiRepositoryInterface;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapitulasiExport implements
    FromArray,
    ShouldAutoSize,
    WithTitle
{
    protected array $data;

    public function __construct(
        RekapitulasiRepositoryInterface $repository
    ) {
        $summary = $repository->getSummary();
        $byKategori = $repository->getByKategori();
        $byBarang = $repository->getByBarang();
        $byPeriode = $repository->getByPeriode();

        $rows = [
            ['REKAPITULASI PERSEDIAAN BARANG'],
            [],
            ['RINGKASAN'],
            ['Keterangan', 'Jumlah'],
            ['Total Barang', $summary['total_barang']],
            ['Total Kategori', $summary['total_kategori']],
            ['Total Penambahan', $summary['total_penambahan']],
            ['Total Pemakaian', $summary['total_pemakaian']],
            ['Total Stok', $summary['total_stok']],
            ['Total Periode Opname', $summary['total_periode_opname']],
            ['Periode Opname Selesai', $summary['total_opname_selesai']],
            ['Nilai Opname Terakhir', $summary['nilai_opname_terakhir']],
            [],
            ['REKAP PER KATEGORI'],
            [
                'Kategori',
                'Jumlah Barang',
                'Penambahan',
                'Pemakaian',
                'Stok',
            ],
        ];

        foreach ($byKategori as $kategori) {
            $rows[] = [
                $kategori['nama'],
                $kategori['jumlah_barang'],
                $kategori['penambahan'],
                $kategori['pemakaian'],
                $kategori['stok'],
            ];
        }

        $rows[] = [];
        $rows[] = ['REKAP PER BARANG'];
        $rows[] = [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Satuan',
            'Penambahan',
            'Pemakaian',
            'Stok',
        ];

        foreach ($byBarang as $barang) {
            $rows[] = [
                $barang['kode'],
                $barang['nama'],
                $barang['kategori'],
                $barang['satuan'],
                $barang['penambahan'],
                $barang['pemakaian'],
                $barang['stok'],
            ];
        }

        $rows[] = [];
        $rows[] = ['REKAP PERIODE STOK OPNAME'];
        $rows[] = [
            'Periode',
            'Tanggal Opname',
            'Status',
            'Jumlah Barang',
            'Total Nilai',
        ];

        foreach ($byPeriode as $periode) {
            $rows[] = [
                $periode['periode'],
                $periode['tanggal'],
                $periode['status'],
                $periode['jumlah_barang'],
                $periode['total_nilai'],
            ];
        }

        $this->data = $rows;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Rekapitulasi';
    }
}