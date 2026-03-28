<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Départements de la Commission (chapeautés par un Commissaire)
        // et Département transversal du Secrétariat Général
        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('libelle', 200);
            $table->string('libelle_court', 80)->nullable();
            $table->enum('type', [
                'technique',    // Département sectoriel chapeauté par un Commissaire
                'appui',        // Secrétariat Général et ses Directions d'appui
                'transversal',  // Transversal multi-départements
            ])->default('technique');
            $table->string('description')->nullable();
            $table->integer('ordre_affichage')->default(0);
            $table->boolean('actif')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departements');
    }
};
