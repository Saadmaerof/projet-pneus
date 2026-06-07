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
        Schema::create('rendezvous', function (Blueprint $table) {
                $table->id();
            $table->foreignId('client_id')
                  ->constrained('clients')
                  ->onDelete('cascade');
                $table->foreignId('vehicule_id')
                  ->constrained('vehicules')
                  ->onDelete('cascade');
                $table->string('description')
                ->nullable();
            $table->foreignId('commande_id')
                  ->nullable()                 // [0..1] → optionnel
                  ->constrained('commandes')
                  ->onDelete('set null');
            $table->date('date');
            $table->time('heure');
            $table->float('tarifTotal')->default(0);
            $table->string('statut')->default('en attente'); // en attente, validé, en cours, terminé, annulé
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rendezvous');
    }
};
