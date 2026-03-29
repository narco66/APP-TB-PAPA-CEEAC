<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('decision_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('decision_id')->constrained('decisions')->cascadeOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->string('titre', 255);
            $table->string('type_piece', 100);
            $table->string('version', 20)->nullable();
            $table->boolean('obligatoire')->default(true);
            $table->boolean('valide')->default(false);
            $table->text('commentaire_validation')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();

            $table->index('decision_id');
            $table->index('document_id');
            $table->index('type_piece');
            $table->index('valide');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('decision_attachments');
    }
};
