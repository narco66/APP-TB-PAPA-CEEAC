<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validations_workflow', function (Blueprint $table) {
            $table->id();

            // Entité soumise à validation (polymorphique)
            $table->string('validable_type', 100);
            $table->unsignedBigInteger('validable_id');

            $table->foreignId('papa_id')->nullable()->constrained('papas')->nullOnDelete();

            $table->enum('etape', [
                'soumission',
                'validation_direction',
                'validation_commissaire',
                'validation_sg',
                'validation_vp',
                'validation_president',
                'rejet',
                'cloture',
            ]);

            $table->enum('action', [
                'soumis',
                'approuve',
                'rejete',
                'demande_correction',
                'information',
            ])->default('soumis');

            $table->foreignId('acteur_id')->constrained('users')->restrictOnDelete();
            $table->text('commentaire')->nullable();
            $table->text('motif_rejet')->nullable();

            $table->string('statut_avant', 50)->nullable();
            $table->string('statut_apres', 50)->nullable();

            $table->timestamps();

            $table->index(['validable_type', 'validable_id']);
            $table->index(['papa_id', 'etape']);
            $table->index('acteur_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validations_workflow');
    }
};
