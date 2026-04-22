<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('generated_reports', 'scope_snapshot')) {
                $table->json('scope_snapshot')->nullable()->after('contexte');
            }

            if (! Schema::hasColumn('generated_reports', 'scope_label')) {
                $table->string('scope_label')->nullable()->after('scope_snapshot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('generated_reports', function (Blueprint $table) {
            if (Schema::hasColumn('generated_reports', 'scope_label')) {
                $table->dropColumn('scope_label');
            }

            if (Schema::hasColumn('generated_reports', 'scope_snapshot')) {
                $table->dropColumn('scope_snapshot');
            }
        });
    }
};
