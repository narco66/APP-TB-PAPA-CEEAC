<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_instance_id')->constrained('workflow_instances')->cascadeOnDelete();
            $table->foreignId('workflow_step_id')->nullable()->constrained('workflow_steps')->nullOnDelete();
            $table->foreignId('acteur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 50);
            $table->string('decision', 50)->nullable();
            $table->text('commentaire')->nullable();
            $table->text('motif_rejet')->nullable();
            $table->json('donnees_avant')->nullable();
            $table->json('donnees_apres')->nullable();
            $table->timestamp('effectue_le');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('workflow_instance_id');
            $table->index('workflow_step_id');
            $table->index('acteur_id');
            $table->index('action');
            $table->index('effectue_le');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_actions');
    }
};
