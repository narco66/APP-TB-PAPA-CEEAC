<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('libelle', 255);
            $table->string('event_type', 100);
            $table->string('canal', 50);
            $table->string('role_cible', 100)->nullable();
            $table->string('permission_cible', 150)->nullable();
            $table->unsignedInteger('delai_minutes')->nullable();
            $table->boolean('escalade')->default(false);
            $table->string('template_sujet', 255)->nullable();
            $table->text('template_message');
            $table->boolean('actif')->default(true);
            $table->timestamps();

            $table->index('event_type');
            $table->index('canal');
            $table->index('actif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_rules');
    }
};
