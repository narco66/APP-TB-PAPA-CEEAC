<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel utilise déjà la table "notifications" pour les notifications,
        // on crée une table in-app supplémentaire structurée pour l'UI
        Schema::create('notifications_app', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('type', 100);           // Type de notification
            $table->string('titre', 300);
            $table->text('message');
            $table->string('lien')->nullable();     // URL de l'action liée
            $table->string('icone', 50)->nullable();
            $table->enum('niveau', ['info', 'succes', 'attention', 'erreur'])->default('info');

            // Entité source
            $table->string('notifiable_type', 100)->nullable();
            $table->unsignedBigInteger('notifiable_id')->nullable();

            $table->timestamp('lue_le')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'lue_le']);
            $table->index(['notifiable_type', 'notifiable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications_app');
    }
};
