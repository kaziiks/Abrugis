<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = Schema::hasTable('applications') ? 'applications' : 'pieteikums';

        if (Schema::hasColumn($tableName, 'requested_date')) {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) {
            $table->date('requested_date')->nullable()->after('area_m2');
        });
    }

    public function down(): void
    {
        $tableName = Schema::hasTable('applications') ? 'applications' : 'pieteikums';

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn('requested_date');
        });
    }
};
