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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();

            $table->string('kode_transaksi')->unique();

            $table->json('jenis_pembayaran_id')->nullable();

            $table->datetime('tanggal');

            $table->foreignId('siswa_id')
                ->nullable()
                ->constrained('siswa')
                ->nullOnDelete();

            $table->enum('tipe', ['masuk', 'keluar']);
            $table->decimal('nominal', 12, 2);
            $table->enum('metode', ['tunai', 'transfer'])->default('tunai');

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->json('nominal_detail')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
