<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasIndex('reviews', 'reviews_application_id_unique')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->unique('application_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasIndex('reviews', 'reviews_application_id_unique')) {
            return;
        }

        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique('reviews_application_id_unique');
        });
    }
};
