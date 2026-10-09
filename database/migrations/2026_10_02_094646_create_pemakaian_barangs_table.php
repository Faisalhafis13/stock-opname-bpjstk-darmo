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
    Schema::create('pemakaian_barangs', function (Blueprint $table) {
        $table->id();

        $table->foreignId('barang_id')
            ->constrained('barangs')
            ->restrictOnDelete();

        $table->date('tanggal');
        $table->decimal('jumlah', 15, 2);

        $table->text('keterangan')->nullable();

        $table->foreignId('user_id')
            ->constrained('users')
            ->restrictOnDelete();

        $table->timestamps();

        $table->index('tanggal');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemakaian_barangs');
    }
};
