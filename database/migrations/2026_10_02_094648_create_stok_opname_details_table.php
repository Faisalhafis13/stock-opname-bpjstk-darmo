<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('stok_opname_details', function (Blueprint $table) {
        $table->id();

        $table->foreignId('periode_stok_opname_id')
            ->constrained('periode_stok_opnames')
            ->cascadeOnDelete();

        $table->foreignId('barang_id')
            ->constrained('barangs')
            ->restrictOnDelete();

        $table->decimal('saldo_awal', 15, 2)->default(0);
        $table->decimal('penambahan', 15, 2)->default(0);
        $table->decimal('pemakaian', 15, 2)->default(0);
        $table->decimal('saldo_akhir', 15, 2)->default(0);

        $table->decimal('harga_satuan', 15, 2)->default(0);
        $table->decimal('total_nilai', 18, 2)->default(0);

        $table->timestamps();

        $table->unique([
            'periode_stok_opname_id',
            'barang_id'
        ]);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname_details');
    }
};
