<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokOpnameDetail extends Model
{
    protected $fillable = [
        'periode_stok_opname_id',
        'barang_id',
        'saldo_awal',
        'penambahan',
        'pemakaian',
        'saldo_akhir',
        'harga_satuan',
        'total_nilai',
    ];

    protected $casts = [
        'saldo_awal' => 'integer',
        'penambahan' => 'integer',
        'pemakaian' => 'integer',
        'saldo_akhir' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_nilai' => 'decimal:2',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(
            PeriodeStokOpname::class,
            'periode_stok_opname_id'
        );
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}