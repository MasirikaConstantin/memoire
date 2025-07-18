<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Plate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Plate::class)->constrained()->cascadeOnDelete();
            $table->enum('type', ['feu_rouge', 'exces_de_vitesse', 'autre'])->default('autre');
            $table->string('localisation')->nullable();
            $table->string('photo_preuve')->nullable();
            $table->boolean('traiter')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
