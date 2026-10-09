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
    Schema::create('periode_stok_opnames', function (Blueprint $table) {
        $table->id();

        $table->string('periode', 20);
        $table->date('tanggal_opname');

        $table->enum('status', [
            'draft',
            'selesai',
        ])->default('draft');

        $table->text('keterangan')->nullable();

        $table->foreignId('user_id')
            ->constrained('users')
            ->restrictOnDelete();

        $table->timestamps();

        $table->unique('periode');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_stok_opnames');
    }
};
