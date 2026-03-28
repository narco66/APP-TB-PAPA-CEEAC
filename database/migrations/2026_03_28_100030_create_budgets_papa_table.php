<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enveloppe budgétaire par action prioritaire et source
        Schema::create('budgets_papa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('papa_id')->constrained('papas')->restrictOnDelete();
            $table->foreignId('action_prioritaire_id')->nullable()->constrained('actions_prioritaires')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->foreignId('partenaire_id')->nullable()->constrained('partenaires')->nullOnDelete();

            $table->enum('source_financement', [
                'budget_ceeac',    // Budget propre de la CEEAC
                'contribution_etat_membre', // Contribution d'un État membre
                'partenaire_technique_financier', // PTF
                'fonds_propres',   // Fonds propres
                'autre',
            ])->default('budget_ceeac');

            $table->string('libelle_ligne', 300)->nullable();
            $table->year('annee_budgetaire');
            $table->string('devise', 10)->default('XAF');

            $table->decimal('montant_prevu', 18, 2)->default(0);
            $table->decimal('montant_engage', 18, 2)->default(0);
            $table->decimal('montant_decaisse', 18, 2)->default(0);
            $table->decimal('montant_solde', 18, 2)->default(0);  // Calculé : prevu - engage

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['papa_id', 'source_financement']);
            $table->index(['action_prioritaire_id']);
            $table->index(['activite_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets_papa');
    }
};
