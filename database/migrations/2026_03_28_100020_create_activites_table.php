<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resultat_attendu_id')->constrained('resultats_attendus')->restrictOnDelete();
            $table->foreignId('direction_id')->constrained('directions')->restrictOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();

            $table->string('code', 50)->unique();
            $table->string('libelle', 500);
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);

            // Planification
            $table->date('date_debut_prevue')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_debut_reelle')->nullable();
            $table->date('date_fin_reelle')->nullable();

            $table->enum('statut', [
                'non_demarree',
                'planifiee',
                'en_cours',
                'suspendue',
                'terminee',
                'abandonnee',
            ])->default('non_demarree');

            // Avancement physique
            $table->decimal('taux_realisation', 5, 2)->default(0);

            // Responsabilités
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('point_focal_id')->nullable()->constrained('users')->nullOnDelete();

            // Budget de l'activité
            $table->decimal('budget_prevu', 18, 2)->default(0);
            $table->decimal('budget_engage', 18, 2)->default(0);
            $table->decimal('budget_consomme', 18, 2)->default(0);
            $table->string('devise', 10)->default('XAF');

            // Priorité et complexité
            $table->enum('priorite', ['critique', 'haute', 'normale', 'basse'])->default('normale');

            // Gantt - dépendances gérées dans table séparée
            $table->boolean('est_jalon')->default(false);  // Jalon = activité clé sans durée

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['resultat_attendu_id', 'statut']);
            $table->index(['direction_id', 'statut']);
            $table->index(['date_debut_prevue', 'date_fin_prevue']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activites');
    }
};
