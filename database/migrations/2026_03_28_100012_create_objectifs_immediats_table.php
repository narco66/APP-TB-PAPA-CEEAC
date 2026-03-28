<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('objectifs_immediats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_prioritaire_id')->constrained('actions_prioritaires')->restrictOnDelete();
            $table->string('code', 40)->unique();
            $table->string('libelle', 500);
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);
            $table->enum('statut', [
                'planifie',
                'en_cours',
                'atteint',
                'partiellement_atteint',
                'non_atteint',
            ])->default('planifie');

            // Contribution mesurable
            $table->decimal('taux_atteinte', 5, 2)->default(0);

            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('action_prioritaire_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objectifs_immediats');
    }
};
