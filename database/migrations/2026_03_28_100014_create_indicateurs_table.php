<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indicateurs', function (Blueprint $table) {
            $table->id();
            // Rattachement : l'indicateur peut être lié à différents niveaux
            $table->foreignId('resultat_attendu_id')->nullable()->constrained('resultats_attendus')->nullOnDelete();
            $table->foreignId('objectif_immediat_id')->nullable()->constrained('objectifs_immediats')->nullOnDelete();
            $table->foreignId('action_prioritaire_id')->nullable()->constrained('actions_prioritaires')->nullOnDelete();

            $table->string('code', 50)->unique();
            $table->string('libelle', 500);
            $table->text('definition')->nullable();         // Définition normalisée (SMART)
            $table->string('unite_mesure', 50)->nullable(); // %, nombre, XAF, etc.
            $table->enum('type_indicateur', [
                'quantitatif',
                'qualitatif',
                'binaire',   // Oui/Non
            ])->default('quantitatif');

            // Valeurs de référence
            $table->decimal('valeur_baseline', 15, 4)->nullable();
            $table->decimal('valeur_cible_annuelle', 15, 4)->nullable();

            // Méthode de calcul et collecte
            $table->text('methode_calcul')->nullable();
            $table->enum('frequence_collecte', [
                'mensuelle',
                'trimestrielle',
                'semestrielle',
                'annuelle',
                'ponctuelle',
            ])->default('trimestrielle');
            $table->string('source_donnees')->nullable();
            $table->string('outil_collecte')->nullable();

            // Responsabilité
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('direction_id')->nullable()->constrained('directions')->nullOnDelete();

            // Seuils d'alerte
            $table->decimal('seuil_alerte_rouge', 5, 2)->nullable();   // En dessous : critique
            $table->decimal('seuil_alerte_orange', 5, 2)->nullable();  // En dessous : attention
            $table->decimal('seuil_alerte_vert', 5, 2)->nullable();    // Au dessus : bon

            // Taux courant (dénormalisé)
            $table->decimal('taux_realisation_courant', 5, 2)->default(0);
            $table->enum('tendance', ['hausse', 'stable', 'baisse', 'na'])->default('na');

            $table->boolean('actif')->default(true);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['resultat_attendu_id', 'type_indicateur']);
            $table->index('action_prioritaire_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indicateurs');
    }
};
