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
    Schema::create('ruangan', function (Blueprint $table) {
        $table->id('id_ruangan'); // PDF: id_ruangan
        $table->string('nomor_ruangan', 10);
        $table->decimal('tarif_per_jam', 10, 2);
        $table->enum('tipe_ruangan', ['Reguler', 'VIP']); // PDF: tipe_ruangan
        $table->text('deskripsi_fasilitas')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ruangan');
    }
};
