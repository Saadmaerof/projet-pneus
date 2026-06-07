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
        Schema::table('techniciens', function (Blueprint $table) {
            if (!Schema::hasColumn('techniciens', 'statut')) {
                $table->string('statut')->default('actif')->after('users_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('techniciens', function (Blueprint $table) {
            if (Schema::hasColumn('techniciens', 'statut')) {
                $table->dropColumn('statut');
            }
        });
    }
};
