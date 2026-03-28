<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actions_prioritaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('papa_id')->constrained('papas')->restrictOnDelete();
            $table->foreignId('departement_id')->constrained('departements')->restrictOnDelete();
            $table->string('code', 30)->unique();           // Ex: AP-2025-01
            $table->string('libelle', 500);
            $table->text('description')->nullable();
            $table->enum('qualification', [
                'technique',    // Action technique sectorielle
                'appui',        // Action d'appui et de soutien
                'transversal',  // Action transversale multi-directions
            ])->default('technique');
            $table->integer('ordre')->default(0);
            $table->enum('priorite', ['critique', 'haute', 'normale', 'basse'])->default('normale');
            $table->enum('statut', [
                'planifie',
                'en_cours',
                'suspendu',
                'termine',
                'abandonne',
            ])->default('planifie');

            // Taux dénormalisés
            $table->decimal('taux_realisation', 5, 2)->default(0);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['papa_id', 'statut']);
            $table->index(['departement_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions_prioritaires');
    }
};
