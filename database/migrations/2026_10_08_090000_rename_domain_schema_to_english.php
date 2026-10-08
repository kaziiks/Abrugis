<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bruga_veids')) {
            return;
        }

        Schema::table('portfolio_info', function (Blueprint $table) {
            $table->dropForeign(['bruga_veids_id']);
        });

        Schema::table('pieteikums', function (Blueprint $table) {
            $table->dropForeign(['bruga_veids_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::table('atsauksme', function (Blueprint $table) {
            $table->dropForeign(['pieteikums_id']);
        });

        Schema::table('portfolio_bilde', function (Blueprint $table) {
            $table->dropForeign(['portfolio_info_id']);
        });

        Schema::rename('bruga_veids', 'paving_types');
        Schema::rename('pieteikums', 'applications');
        Schema::rename('atsauksme', 'reviews');
        Schema::rename('portfolio_bilde', 'portfolio_images');

        Schema::table('portfolio_info', function (Blueprint $table) {
            $table->renameColumn('bruga_veids_id', 'paving_type_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->renameColumn('bruga_veids_id', 'paving_type_id');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->renameColumn('pieteikums_id', 'application_id');
            $table->renameColumn('atsauksme', 'review');
        });

        Schema::table('portfolio_info', function (Blueprint $table) {
            $table->foreign('paving_type_id', 'portfolio_info_paving_type_id_foreign')
                ->references('id')
                ->on('paving_types')
                ->cascadeOnDelete();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreign('paving_type_id', 'applications_paving_type_id_foreign')
                ->references('id')
                ->on('paving_types')
                ->nullOnDelete();
            $table->foreign('user_id', 'applications_user_id_foreign')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('application_id', 'reviews_application_id_foreign')
                ->references('id')
                ->on('applications')
                ->cascadeOnDelete();
        });

        Schema::table('portfolio_images', function (Blueprint $table) {
            $table->foreign('portfolio_info_id', 'portfolio_images_portfolio_info_id_foreign')
                ->references('id')
                ->on('portfolio_info')
                ->cascadeOnDelete();
        });
    }

    public function down(): void {}
};
