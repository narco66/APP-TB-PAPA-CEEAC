<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_definition_id')->constrained('workflow_definitions')->restrictOnDelete();
            $table->string('objet_type', 150);
            $table->unsignedBigInteger('objet_id');
            $table->foreignId('papa_id')->nullable()->constrained('papas')->nullOnDelete();
            $table->string('statut', 50);
            $table->foreignId('etape_courante_id')->nullable()->constrained('workflow_steps')->nullOnDelete();
            $table->foreignId('demarre_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_demarrage')->nullable();
            $table->timestamp('date_cloture')->nullable();
            $table->text('motif_cloture')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['objet_type', 'objet_id']);
            $table->index('papa_id');
            $table->index('statut');
            $table->index('etape_courante_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_instances');
    }
};
