<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('papas', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();          // Ex: PAPA-2025
            $table->string('libelle', 255);
            $table->year('annee');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->text('description')->nullable();
            $table->enum('statut', [
                'brouillon',       // En préparation
                'soumis',          // Soumis pour validation
                'en_validation',   // En cours de validation hiérarchique
                'valide',          // Validé et actif
                'en_execution',    // En cours d'exécution
                'cloture',         // Clôturé en fin d'exercice
                'archive',         // Archivé (lecture seule)
            ])->default('brouillon');

            // Enveloppe budgétaire globale
            $table->decimal('budget_total_prevu', 18, 2)->default(0);
            $table->string('devise', 10)->default('XAF');    // Franc CFA BEAC

            // Taux de réalisation globaux (dénormalisés pour perf)
            $table->decimal('taux_execution_physique', 5, 2)->default(0);
            $table->decimal('taux_execution_financiere', 5, 2)->default(0);

            // Workflow
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->text('motif_archivage')->nullable();

            // Clonage d'un PAPA précédent
            $table->foreignId('clone_de_papa_id')->nullable()->constrained('papas')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->boolean('est_verrouille')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->index('annee');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papas');
    }
};
