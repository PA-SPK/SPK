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
        Schema::create('kriteria_perbandingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_1_id')->constrained('kriterias')->onDelete('cascade');
            $table->foreignId('kriteria_2_id')->constrained('kriterias')->onDelete('cascade');
            $table->decimal('nilai', 10, 5)->default(1.0);
            $table->timestamps();

            $table->unique(['kriteria_1_id', 'kriteria_2_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriteria_perbandingans');
    }
};

