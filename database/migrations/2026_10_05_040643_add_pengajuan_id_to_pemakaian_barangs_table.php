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
        Schema::table('pemakaian_barangs', function (Blueprint $table) {
            $table->foreignId('pengajuan_id')
                ->nullable()
                ->after('barang_id')
                ->constrained('pengajuans')
                ->nullOnDelete();

            $table->index('pengajuan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemakaian_barangs', function (Blueprint $table) {
            $table->dropForeign(['pengajuan_id']);
            $table->dropIndex(['pengajuan_id']);
            $table->dropColumn('pengajuan_id');
        });
    }
};