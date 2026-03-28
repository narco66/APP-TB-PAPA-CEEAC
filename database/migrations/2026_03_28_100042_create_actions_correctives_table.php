<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actions_correctives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alerte_id')->nullable()->constrained('alertes')->nullOnDelete();
            $table->foreignId('risque_id')->nullable()->constrained('risques')->nullOnDelete();
            $table->foreignId('papa_id')->constrained('papas')->restrictOnDelete();

            $table->string('code', 40)->unique();
            $table->string('libelle', 400);
            $table->text('description')->nullable();
            $table->date('date_echeance');
            $table->enum('priorite', ['basse', 'normale', 'haute', 'critique'])->default('normale');
            $table->enum('statut', [
                'planifiee',
                'en_cours',
                'terminee',
                'annulee',
            ])->default('planifiee');

            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('date_realisation_effective')->nullable();
            $table->text('resultat_obtenu')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['papa_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('actions_correctives');
    }
};
