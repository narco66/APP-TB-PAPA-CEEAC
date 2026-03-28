<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultats_attendus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objectif_immediat_id')->constrained('objectifs_immediats')->restrictOnDelete();
            $table->string('code', 50)->unique();
            $table->string('libelle', 500);
            $table->text('description')->nullable();
            $table->enum('type_resultat', [
                'output',    // Extrant : produit direct de l'activité
                'outcome',   // Effet : changement à court/moyen terme
                'impact',    // Impact : changement à long terme
            ])->default('output');
            $table->integer('ordre')->default(0);
            $table->enum('statut', [
                'planifie',
                'en_cours',
                'atteint',
                'partiellement_atteint',
                'non_atteint',
            ])->default('planifie');
            $table->decimal('taux_atteinte', 5, 2)->default(0);

            // Preuve attendue
            $table->boolean('preuve_requise')->default(false);
            $table->string('type_preuve_attendue')->nullable();

            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('objectif_immediat_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultats_attendus');
    }
};
