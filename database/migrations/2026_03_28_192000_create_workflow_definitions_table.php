<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('libelle', 255);
            $table->text('description')->nullable();
            $table->string('module_cible', 100);
            $table->string('type_objet', 150);
            $table->boolean('actif')->default(true);
            $table->unsignedInteger('version')->default(1);
            $table->foreignId('cree_par')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('maj_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('module_cible');
            $table->index('type_objet');
            $table->index('actif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_definitions');
    }
};
