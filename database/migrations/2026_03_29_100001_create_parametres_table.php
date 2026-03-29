<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 100)->unique();
            $table->string('groupe', 60)->index();
            $table->string('type', 30)->default('string');
            $table->text('valeur')->nullable();
            $table->text('valeur_defaut')->nullable();
            $table->string('libelle', 200);
            $table->string('description', 500)->nullable();
            $table->boolean('est_systeme')->default(false);
            $table->boolean('est_sensible')->default(false);
            $table->foreignId('modifie_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['groupe', 'cle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres');
    }
};
