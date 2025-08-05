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
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // exemplo: "normal", "flag_1", "flag_2"
            $table->decimal('base_fare', 8, 2);
            $table->decimal('price_per_km', 8, 2);
            $table->boolean('is_rush_hour')->default(false);
            $table->boolean('is_flag_2')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_rules');
    }
};
