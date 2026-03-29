<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decisions', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 100)->unique();
            $table->string('titre', 255);
            $table->longText('description');
            $table->string('type_decision', 100);
            $table->string('niveau_decision', 100);
            $table->string('statut', 50);
            $table->date('date_decision')->nullable();
            $table->foreignId('papa_id')->nullable()->constrained('papas')->nullOnDelete();
            $table->foreignId('action_prioritaire_id')->nullable()->constrained('actions_prioritaires')->nullOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->foreignId('budget_papa_id')->nullable()->constrained('budgets_papa')->nullOnDelete();
            $table->foreignId('prise_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('validee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('impact_budgetaire', 18, 2)->nullable();
            $table->integer('impact_calendrier_jours')->nullable();
            $table->boolean('mise_en_oeuvre_obligatoire')->default(false);
            $table->date('date_effet')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('type_decision');
            $table->index('niveau_decision');
            $table->index('statut');
            $table->index('papa_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decisions');
    }
};
