<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referentiels', function (Blueprint $table) {
            $table->id();
            $table->string('type', 80)->index();
            $table->string('code', 40);
            $table->string('libelle', 200);
            $table->string('libelle_court', 60)->nullable();
            $table->text('description')->nullable();
            $table->string('couleur', 20)->nullable();
            $table->string('icone', 60)->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->boolean('actif')->default(true);
            $table->boolean('est_systeme')->default(false);
            $table->json('metadata')->nullable();
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('modifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['type', 'code']);
            $table->index(['type', 'actif', 'ordre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referentiels');
    }
};
