<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->nullable()->constrained('categories_documents')->nullOnDelete();

            // Rattachement polymorphique à une entité métier
            $table->string('documentable_type', 100)->nullable();
            $table->unsignedBigInteger('documentable_id')->nullable();

            $table->string('titre', 400);
            $table->text('description')->nullable();
            $table->string('reference', 100)->nullable();      // Numéro de référence institutionnel
            $table->date('date_document')->nullable();

            // Fichier
            $table->string('chemin_fichier');                  // Chemin relatif sur disk GED
            $table->string('nom_fichier_original', 300);
            $table->string('extension', 20)->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('taille_octets')->nullable();

            // Version
            $table->string('version', 20)->default('1.0');
            $table->foreignId('version_precedente_id')->nullable()->constrained('documents')->nullOnDelete();

            // Accès et classification
            $table->enum('confidentialite', [
                'public',
                'interne',
                'confidentiel',
                'strictement_confidentiel',
            ])->default('interne');

            // Workflow
            $table->enum('statut', [
                'brouillon',
                'soumis',
                'valide',
                'archive',
                'obsolete',
            ])->default('brouillon');

            $table->foreignId('depose_par')->constrained('users')->restrictOnDelete();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();

            $table->boolean('est_archive')->default(false);
            $table->timestamp('archive_le')->nullable();

            $table->string('hash_sha256', 64)->nullable();  // Intégrité

            $table->softDeletes();
            $table->timestamps();

            $table->index(['documentable_type', 'documentable_id']);
            $table->index(['categorie_id', 'statut']);
            $table->index('depose_par');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
