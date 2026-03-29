<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_definition_id')->constrained('workflow_definitions')->cascadeOnDelete();
            $table->string('code', 100);
            $table->string('libelle', 255);
            $table->text('description')->nullable();
            $table->unsignedInteger('ordre');
            $table->string('role_requis', 100)->nullable();
            $table->string('permission_requise', 150)->nullable();
            $table->boolean('validation_multiple')->default(false);
            $table->unsignedInteger('nb_validateurs_min')->nullable();
            $table->boolean('est_etape_initiale')->default(false);
            $table->boolean('est_etape_finale')->default(false);
            $table->unsignedInteger('delai_jours')->nullable();
            $table->unsignedInteger('escalade_apres_jours')->nullable();
            $table->timestamps();

            $table->unique(['workflow_definition_id', 'code']);
            $table->unique(['workflow_definition_id', 'ordre']);
            $table->index('role_requis');
            $table->index('permission_requise');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
