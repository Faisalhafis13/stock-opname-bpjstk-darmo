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
    Schema::create('pengajuans', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')
            ->constrained('users')
            ->restrictOnDelete();

        $table->foreignId('barang_id')
            ->constrained('barangs')
            ->restrictOnDelete();

        $table->decimal('jumlah', 15, 2);

        $table->text('keterangan')->nullable();

        $table->enum('status', [
            'menunggu',
            'disetujui',
            'ditolak',
        ])->default('menunggu');

        $table->text('catatan_admin')->nullable();

        $table->foreignId('processed_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete();

        $table->timestamp('processed_at')->nullable();

        $table->timestamps();

        $table->index('status');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
