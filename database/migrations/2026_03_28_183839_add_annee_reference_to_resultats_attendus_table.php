<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resultats_attendus', function (Blueprint $table) {
            $table->unsignedSmallInteger('annee_reference')->nullable()->after('type_resultat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultats_attendus', function (Blueprint $table) {
            $table->dropColumn('annee_reference');
        });
    }
};
