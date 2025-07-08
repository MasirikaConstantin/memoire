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
        Schema::create('plates', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('proprietaire')->nullable();
            $table->string('type_vehicle')->nullable();
            $table->boolean('est_volee')->default(false);
            $table->string('image')->nullable();
            // Migration
            $table->string('normalized_number')->virtualAs("UPPER(REPLACE(REPLACE(number, ' ', ''), '-', ''))");
            $table->index('normalized_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plates');
    }
};
