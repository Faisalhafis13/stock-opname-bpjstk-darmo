<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $fillable = [
        'kategori_barang_id',
        'kode_barang',
        'nama_barang',
        'satuan',
        'keterangan',
    ];


    /*
    |--------------------------------------------------------------------------
    | RELASI KATEGORI
    |--------------------------------------------------------------------------
    */

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(
            KategoriBarang::class,
            'kategori_barang_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RELASI PENAMBAHAN BARANG
    |--------------------------------------------------------------------------
    */

    public function penambahanBarangs(): HasMany
    {
        return $this->hasMany(
            PenambahanBarang::class,
            'barang_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RELASI PEMAKAIAN BARANG
    |--------------------------------------------------------------------------
    */

    public function pemakaianBarangs(): HasMany
    {
        return $this->hasMany(
            PemakaianBarang::class,
            'barang_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RELASI STOK OPNAME
    |--------------------------------------------------------------------------
    */

    public function stokOpnameDetails(): HasMany
    {
        return $this->hasMany(
            StokOpnameDetail::class,
            'barang_id'
        );
    }
}