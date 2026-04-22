<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'departement_id')) {
                $table->foreignId('departement_id')->nullable()->after('direction_id')->constrained('departements')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('departement_id')->constrained('services')->nullOnDelete();
            }

            if (! Schema::hasColumn('users', 'scope_level')) {
                $table->string('scope_level', 30)->nullable()->after('service_id');
            }

            if (! Schema::hasColumn('users', 'is_transversal')) {
                $table->boolean('is_transversal')->default(false)->after('scope_level');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_transversal')) {
                $table->dropColumn('is_transversal');
            }

            if (Schema::hasColumn('users', 'scope_level')) {
                $table->dropColumn('scope_level');
            }

            if (Schema::hasColumn('users', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }

            if (Schema::hasColumn('users', 'departement_id')) {
                $table->dropConstrainedForeignId('departement_id');
            }
        });
    }
};
