<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('papa_id')->constrained('papas')->restrictOnDelete();
            $table->foreignId('direction_id')->nullable()->constrained('directions')->nullOnDelete();
            $table->foreignId('departement_id')->nullable()->constrained('departements')->nullOnDelete();

            $table->string('titre', 400);
            $table->enum('type_rapport', [
                'mensuel',
                'trimestriel',
                'semestriel',
                'annuel',
                'ad_hoc',
                'flash',
            ])->default('trimestriel');

            $table->string('periode_couverte', 50);  // Ex: "T1-2025"
            $table->year('annee');
            $table->tinyInteger('numero_periode')->nullable();  // 1, 2, 3, 4

            $table->decimal('taux_execution_physique', 5, 2)->default(0);
            $table->decimal('taux_execution_financiere', 5, 2)->default(0);
            $table->text('faits_saillants')->nullable();
            $table->text('difficultes_rencontrees')->nullable();
            $table->text('recommandations')->nullable();
            $table->text('perspectives')->nullable();

            $table->enum('statut', [
                'brouillon',
                'soumis',
                'valide',
                'publie',
            ])->default('brouillon');

            $table->foreignId('redige_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->timestamp('publie_le')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['papa_id', 'type_rapport', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};
