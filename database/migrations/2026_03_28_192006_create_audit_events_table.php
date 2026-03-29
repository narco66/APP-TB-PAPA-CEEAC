<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_uuid')->unique();
            $table->string('module', 100);
            $table->string('event_type', 100);
            $table->string('auditable_type', 150);
            $table->unsignedBigInteger('auditable_id');
            $table->foreignId('papa_id')->nullable()->constrained('papas')->nullOnDelete();
            $table->foreignId('acteur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->text('description')->nullable();
            $table->string('niveau', 20)->default('info');
            $table->json('donnees_avant')->nullable();
            $table->json('donnees_apres')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('horodate_evenement');
            $table->string('checksum', 128)->nullable();
            $table->timestamps();

            $table->index('module');
            $table->index('event_type');
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('acteur_id');
            $table->index('papa_id');
            $table->index('horodate_evenement');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');
    }
};
