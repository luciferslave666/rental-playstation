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
    Schema::create('konsol', function (Blueprint $table) {
        $table->id('id_konsol'); // PDF: id_konsol
        // Relasi: Satu konsol berada di satu ruangan [cite: 137]
        $table->foreignId('id_ruangan')->unique()->constrained('ruangan', 'id_ruangan')->onDelete('cascade');
        $table->string('seri_konsol');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsol');
    }
};
