<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('engagements_financiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_papa_id')->constrained('budgets_papa')->restrictOnDelete();
            $table->foreignId('activite_id')->nullable()->constrained('activites')->nullOnDelete();
            $table->string('numero_engagement', 80)->nullable()->unique();
            $table->string('libelle', 300);
            $table->date('date_engagement');
            $table->decimal('montant_engage', 18, 2)->default(0);
            $table->decimal('montant_decaisse', 18, 2)->default(0);
            $table->string('fournisseur_beneficiaire')->nullable();
            $table->enum('statut', [
                'engage',
                'partiellement_decaisse',
                'totalement_decaisse',
                'annule',
            ])->default('engage');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['budget_papa_id', 'statut']);
            $table->index('date_engagement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engagements_financiers');
    }
};
