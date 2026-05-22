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
        Schema::create('ligne_commandes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_id')
                  ->constrained('commandes')
                  ->onDelete('cascade');
            $table->foreignId('pneu_id')
                    ->constrained('pneus')
                    ->onDelete('cascade');
            $table->integer('quantite');
            $table->float('prix_unitaire');
            $table->float('total')->virtualAs('quantite * prix_unitaire');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_commandes');
    }
};
