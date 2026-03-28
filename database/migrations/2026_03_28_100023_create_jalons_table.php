<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jalons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activite_id')->constrained('activites')->restrictOnDelete();
            $table->string('code', 60)->unique();
            $table->string('libelle', 300);
            $table->text('description')->nullable();
            $table->date('date_prevue');
            $table->date('date_reelle')->nullable();
            $table->enum('statut', [
                'planifie',
                'atteint',
                'non_atteint',
                'reporte',
            ])->default('planifie');
            $table->boolean('est_critique')->default(false);  // Jalon sur le chemin critique
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['activite_id', 'date_prevue']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jalons');
    }
};
