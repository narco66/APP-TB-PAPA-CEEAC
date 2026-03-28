<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('papa_id')->constrained('papas')->restrictOnDelete();

            // Entité source de l'alerte
            $table->string('alertable_type', 100);
            $table->unsignedBigInteger('alertable_id');

            $table->enum('type_alerte', [
                'retard_activite',
                'taux_realisation_faible',
                'budget_depasse',
                'indicateur_hors_cible',
                'jalon_manque',
                'document_manquant',
                'risque_eleve',
                'echeance_proche',
                'autre',
            ])->default('retard_activite');

            $table->enum('niveau', ['info', 'attention', 'critique'])->default('attention');
            $table->string('titre', 300);
            $table->text('message');

            $table->enum('statut', [
                'nouvelle',
                'vue',
                'en_traitement',
                'resolue',
                'ignoree',
            ])->default('nouvelle');

            // Circuit d'escalade
            $table->foreignId('destinataire_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('direction_id')->nullable()->constrained('directions')->nullOnDelete();

            $table->boolean('escaladee')->default(false);
            $table->foreignId('escaladee_vers_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('escaladee_le')->nullable();

            $table->timestamp('lue_le')->nullable();
            $table->foreignId('traitee_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('traitee_le')->nullable();
            $table->text('resolution')->nullable();

            // Génération
            $table->boolean('auto_generee')->default(false);  // true si générée par un job
            $table->timestamps();

            $table->index(['alertable_type', 'alertable_id']);
            $table->index(['papa_id', 'statut', 'niveau']);
            $table->index(['destinataire_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
