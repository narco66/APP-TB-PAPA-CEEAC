<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valeurs_indicateurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indicateur_id')->constrained('indicateurs')->restrictOnDelete();
            $table->enum('periode_type', [
                'mensuelle',
                'trimestrielle',
                'semestrielle',
                'annuelle',
            ])->default('trimestrielle');
            $table->string('periode_libelle', 30);    // Ex: "T1-2025", "S1-2025", "Jan-2025"
            $table->year('annee');
            $table->tinyInteger('mois')->nullable();        // 1-12
            $table->tinyInteger('trimestre')->nullable();   // 1-4
            $table->tinyInteger('semestre')->nullable();    // 1-2

            $table->decimal('valeur_realisee', 15, 4)->nullable();
            $table->decimal('valeur_cible_periode', 15, 4)->nullable();
            $table->decimal('taux_realisation', 5, 2)->default(0);
            $table->enum('tendance', ['hausse', 'stable', 'baisse', 'na'])->default('na');

            $table->text('commentaire')->nullable();
            $table->text('analyse_ecart')->nullable();

            // Validation de la saisie
            $table->enum('statut_validation', [
                'brouillon',
                'soumis',
                'valide',
                'rejete',
            ])->default('brouillon');
            $table->foreignId('saisi_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->text('motif_rejet')->nullable();

            $table->timestamps();

            $table->unique(['indicateur_id', 'periode_type', 'annee', 'mois', 'trimestre', 'semestre'], 'uk_valeur_indicateur_periode');
            $table->index(['indicateur_id', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valeurs_indicateurs');
    }
};
