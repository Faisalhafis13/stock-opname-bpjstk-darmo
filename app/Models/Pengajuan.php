<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pengajuan extends Model
{
    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlah',
        'keterangan',
        'status',
        'catatan_admin',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'processed_by'
        );
    }

    public function pemakaianBarang(): HasOne
    {
        return $this->hasOne(
            PemakaianBarang::class,
            'pengajuan_id'
        );
    }
}