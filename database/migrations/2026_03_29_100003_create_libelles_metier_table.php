<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('libelles_metier', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 100)->unique();
            $table->string('module', 60)->index();
            $table->string('valeur_defaut', 300);
            $table->string('valeur_courante', 300)->nullable();
            $table->string('valeur_courte', 100)->nullable();
            $table->boolean('est_systeme')->default(false);
            $table->boolean('traductible')->default(false);
            $table->string('locale', 5)->default('fr');
            $table->foreignId('modifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['module', 'cle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('libelles_metier');
    }
};
