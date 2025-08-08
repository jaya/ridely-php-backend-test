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
        Schema::create('ride_estimates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('ride_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('duration_min', 8, 2)->nullable();
            $table->decimal('price_estimate', 10, 2)->nullable();
            /**
             * Lógica típica no fluxo:
             * Criação da estimativa:
             * status = PENDING, campos de distância/duração/preço ainda null.
             *
             * Job inicia o processamento:
             * Atualiza para PROCESSING.
             *
             * Job finaliza com sucesso:
             * Preenche os campos distance_km, duration_min, price_estimate e marca status = READY.
             *
             * Job falha
             * Marca status = FAILED e pode armazenar logs ou mensagens de erro em outra tabela se quiser.
 */
            $table->enum('status', ['PENDING', 'PROCESSING', 'READY', 'FAILED'])->default('PENDING');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_estimates');
    }
};
