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
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->string('satuan')->nullable();
            $table->enum('tipe', ['Benefit', 'Cost'])->default('Benefit');
            $table->decimal('bobot', 12, 10)->default(0);
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('alternatifs', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama');
            $table->string('kategori')->default('Desa');
            $table->text('deskripsi')->nullable();
            $table->integer('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('nilai_alternatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternatif_id')->constrained('alternatifs')->onDelete('cascade');
            $table->foreignId('kriteria_id')->constrained('kriterias')->onDelete('cascade');
            $table->decimal('nilai', 14, 4)->default(0);
            $table->timestamps();

            $table->unique(['alternatif_id', 'kriteria_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_alternatifs');
        Schema::dropIfExists('alternatifs');
        Schema::dropIfExists('kriterias');
    }
};

