<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodeStokOpname extends Model
{
    protected $fillable = [
        'periode',
        'tanggal_opname',
        'status',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(
            StokOpnameDetail::class,
            'periode_stok_opname_id'
        );
    }
    
}