<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        Schema::table('applications', function (Blueprint $table) {
            if (! Schema::hasColumn('applications', 'estimate_total')) {
                $table->decimal('estimate_total', 12, 2)->nullable();
            }

            if (! Schema::hasColumn('applications', 'estimate_details')) {
                $table->json('estimate_details')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'estimate_details')) {
                $table->dropColumn('estimate_details');
            }

            if (Schema::hasColumn('applications', 'estimate_total')) {
                $table->dropColumn('estimate_total');
            }
        });

        Schema::dropIfExists('password_reset_tokens');
    }
};
