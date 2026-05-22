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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
          $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade');
          $table->datetime('date_commande')->useCurrent();
            $table->string('statut')->default('en attente'); // en attente, confirmée, livrée, annulée
            $table->float('montant_total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
