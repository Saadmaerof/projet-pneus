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
        Schema::create('pneus', function (Blueprint $table) {
            $table->id();
            $table->string('marque');
            $table->string('modele');
            $table->integer('largeur');
            $table->integer('hauteur');
            $table->integer('diametre_pouces');
            $table->string('saison');
            $table->integer('indice_charge');
            $table->string('indice_vitesse');
            $table->double('prix');
            $table->text('description');
            $table->integer('quantite')->default(0);
            $table->string('image')->nullable();
            $table->foreignId('vehicule_id')
                  ->constrained('vehicules')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pneus');
    }
};
