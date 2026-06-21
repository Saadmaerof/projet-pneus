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
        Schema::create('ligne_rendezvous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rendezvous_id')->constrained('rendezvous')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
          $table->foreignId('technicien_id')->nullable()->constrained('techniciens')->onDelete('set null');//l'adminin affecte un technicien pour réaliser le service lors de la validation du rendezvous
            $table->integer('duree')->nullable(); // en minutes //le technicien qui a fait le service saisie la durée réelle du service
            $table->float('tarif')->nullable(); // le technicien qui a fait le service saisie le tarif réel du service
            $table->string('statut')->default('en attente'); // en attente,validé, en cours, terminé, annulé
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ligne_rendezvous');
        
    }
};
