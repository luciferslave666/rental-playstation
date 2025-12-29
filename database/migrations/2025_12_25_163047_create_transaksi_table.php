<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('transaksi', function (Blueprint $table) {
        $table->id('id_transaksi');
        // Foreign Keys sesuai skema relasi [cite: 58]
        $table->foreignId('id_user')->constrained('users', 'id_user'); // Operator
        $table->foreignId('id_ruangan')->constrained('ruangan', 'id_ruangan');
        $table->foreignId('id_pelanggan')->constrained('pelanggan', 'id_pelanggan');
        
        $table->dateTime('waktu_mulai');
        $table->dateTime('waktu_selesai')->nullable(); // Nullable jika baru mulai main
        $table->decimal('total_biaya', 10, 2)->default(0);
        $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas'])->default('Belum Lunas');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
