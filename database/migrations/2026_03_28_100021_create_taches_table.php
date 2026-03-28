<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activite_id')->constrained('activites')->restrictOnDelete();
            $table->foreignId('parent_tache_id')->nullable()->constrained('taches')->nullOnDelete();
            $table->string('code', 60)->unique();
            $table->string('libelle', 500);
            $table->text('description')->nullable();
            $table->integer('ordre')->default(0);

            $table->date('date_debut_prevue')->nullable();
            $table->date('date_fin_prevue')->nullable();
            $table->date('date_debut_reelle')->nullable();
            $table->date('date_fin_reelle')->nullable();

            $table->enum('statut', [
                'a_faire',
                'en_cours',
                'en_revue',
                'terminee',
                'bloquee',
                'abandonnee',
            ])->default('a_faire');

            $table->decimal('taux_realisation', 5, 2)->default(0);
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['activite_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taches');
    }
};
