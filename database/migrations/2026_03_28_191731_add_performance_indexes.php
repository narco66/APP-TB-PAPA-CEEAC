<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            if (! Schema::hasIndex('activites', 'activites_statut_index')) {
                $table->index('statut', 'activites_statut_index');
            }

            if (! Schema::hasIndex('activites', 'activites_direction_id_index')) {
                $table->index('direction_id', 'activites_direction_id_index');
            }
        });

        Schema::table('budgets_papa', function (Blueprint $table) {
            if (! Schema::hasIndex('budgets_papa', 'budgets_papa_papa_id_index')) {
                $table->index('papa_id', 'budgets_papa_papa_id_index');
            }
        });

        Schema::table('indicateurs', function (Blueprint $table) {
            if (! Schema::hasIndex('indicateurs', 'indicateurs_direction_id_index')) {
                $table->index('direction_id', 'indicateurs_direction_id_index');
            }

            if (! Schema::hasIndex('indicateurs', 'indicateurs_actif_index')) {
                $table->index('actif', 'indicateurs_actif_index');
            }
        });

        Schema::table('alertes', function (Blueprint $table) {
            if (! Schema::hasIndex('alertes', 'alertes_created_at_index')) {
                $table->index('created_at', 'alertes_created_at_index');
            }

            if (! Schema::hasIndex('alertes', 'alertes_papa_niveau_index')) {
                $table->index(['papa_id', 'niveau', 'statut'], 'alertes_papa_niveau_index');
            }
        });

        Schema::table('activity_log', function (Blueprint $table) {
            if (! Schema::hasIndex('activity_log', 'activity_log_causer_index')) {
                $table->index(['causer_type', 'causer_id'], 'activity_log_causer_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activites', function (Blueprint $table) {
            if (Schema::hasIndex('activites', 'activites_statut_index')) {
                $table->dropIndex('activites_statut_index');
            }

            if (Schema::hasIndex('activites', 'activites_direction_id_index')) {
                $table->dropIndex('activites_direction_id_index');
            }
        });

        Schema::table('budgets_papa', function (Blueprint $table) {
            if (Schema::hasIndex('budgets_papa', 'budgets_papa_papa_id_index')) {
                $table->dropIndex('budgets_papa_papa_id_index');
            }
        });

        Schema::table('indicateurs', function (Blueprint $table) {
            if (Schema::hasIndex('indicateurs', 'indicateurs_direction_id_index')) {
                $table->dropIndex('indicateurs_direction_id_index');
            }

            if (Schema::hasIndex('indicateurs', 'indicateurs_actif_index')) {
                $table->dropIndex('indicateurs_actif_index');
            }
        });

        Schema::table('alertes', function (Blueprint $table) {
            if (Schema::hasIndex('alertes', 'alertes_created_at_index')) {
                $table->dropIndex('alertes_created_at_index');
            }

            if (Schema::hasIndex('alertes', 'alertes_papa_niveau_index')) {
                $table->dropIndex('alertes_papa_niveau_index');
            }
        });

        Schema::table('activity_log', function (Blueprint $table) {
            if (Schema::hasIndex('activity_log', 'activity_log_causer_index')) {
                $table->dropIndex('activity_log_causer_index');
            }
        });
    }
};
