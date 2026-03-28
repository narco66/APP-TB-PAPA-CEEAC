<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partenaires', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('libelle', 200);
            $table->string('sigle', 50)->nullable();
            $table->enum('type', [
                'multilateral',    // Banques de développement, agences ONU
                'bilateral',       // Coopérations bilatérales
                'fonds',           // Fonds dédiés
                'prive',           // Secteur privé
                'autre',
            ])->default('multilateral');
            $table->string('pays_origine')->nullable();
            $table->string('contact_nom')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_telephone', 30)->nullable();
            $table->string('site_web')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('actif')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partenaires');
    }
};
