<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generated_reports', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('report_definition_id')->nullable()->constrained('report_definitions')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('papa_id')->nullable()->constrained('papas')->nullOnDelete();
            $table->string('titre');
            $table->string('format', 16)->index();
            $table->string('statut', 32)->default('generated')->index();
            $table->string('file_disk', 64)->default('local');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->json('filters')->nullable();
            $table->json('contexte')->nullable();
            $table->timestamp('generated_at')->nullable()->index();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('last_downloaded_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_reports');
    }
};
