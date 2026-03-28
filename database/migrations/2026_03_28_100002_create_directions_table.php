<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departement_id')->constrained('departements')->restrictOnDelete();
            $table->string('code', 20)->unique();
            $table->string('libelle', 200);
            $table->string('libelle_court', 80)->nullable();
            $table->enum('type_direction', [
                'technique',  // Direction technique sectorielle
                'appui',      // Direction d'appui et de soutien (relevant du SG)
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
        Schema::dropIfExists('directions');
    }
};
