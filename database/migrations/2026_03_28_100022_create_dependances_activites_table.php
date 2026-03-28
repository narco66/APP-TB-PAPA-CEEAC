<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependances_activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activite_id')->constrained('activites')->restrictOnDelete();
            $table->foreignId('activite_predecesseur_id')->constrained('activites')->restrictOnDelete();
            $table->enum('type_dependance', [
                'fin_debut',   // Fin-Début (FD) : la plus courante
                'debut_debut', // Début-Début (DD)
                'fin_fin',     // Fin-Fin (FF)
                'debut_fin',   // Début-Fin (DF)
            ])->default('fin_debut');
            $table->integer('delai_jours')->default(0);  // Décalage en jours
            $table->timestamps();

            $table->unique(['activite_id', 'activite_predecesseur_id'], 'uk_dep_act_pred');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dependances_activites');
    }
};
