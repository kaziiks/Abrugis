<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pieteikums', function (Blueprint $table) {
            $table->date('requested_date')->nullable()->after('area_m2');
        });
    }

    public function down(): void
    {
        Schema::table('pieteikums', function (Blueprint $table) {
            $table->dropColumn('requested_date');
        });
    }
};