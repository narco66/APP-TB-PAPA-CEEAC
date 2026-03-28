<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risques', function (Blueprint $table) {
            $table->id();
            // Le risque peut être rattaché à différents niveaux
            $table->string('entite_type', 100);  // Polymorphique simplifié
            $table->unsignedBigInteger('entite_id');
            $table->foreignId('papa_id')->constrained('papas')->restrictOnDelete();

            $table->string('code', 40)->unique();
            $table->string('libelle', 400);
            $table->text('description')->nullable();

            $table->enum('categorie', [
                'strategique',
                'operationnel',
                'financier',
                'juridique',
                'reputationnel',
                'securitaire',
                'naturel',
                'autre',
            ])->default('operationnel');

            $table->enum('probabilite', ['tres_faible', 'faible', 'moyenne', 'elevee', 'tres_elevee'])->default('moyenne');
            $table->enum('impact', ['negligeable', 'mineur', 'modere', 'majeur', 'catastrophique'])->default('modere');

            // Score calculé (probabilité × impact)
            $table->tinyInteger('score_risque')->default(0);  // 1-25
            $table->enum('niveau_risque', ['vert', 'jaune', 'orange', 'rouge'])->default('jaune');

            $table->enum('statut', [
                'identifie',
                'en_traitement',
                'residu',
                'clos',
            ])->default('identifie');

            $table->text('mesures_mitigation')->nullable();
            $table->text('plan_contingence')->nullable();

            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_echeance_traitement')->nullable();
            $table->date('date_derniere_revue')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['entite_type', 'entite_id']);
            $table->index(['papa_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risques');
    }
};
