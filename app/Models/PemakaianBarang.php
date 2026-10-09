<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianBarang extends Model
{
    protected $fillable = [
        'pengajuan_id',
        'barang_id',
        'tanggal',
        'jumlah',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(
            Pengajuan::class,
            'pengajuan_id'
        );
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(
            Barang::class,
            'barang_id'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }
}