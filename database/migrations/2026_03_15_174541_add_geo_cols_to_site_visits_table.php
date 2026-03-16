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
        Schema::table('site_visits', function (Blueprint $table) {
            $table->string('country_code', 2)->nullable()->after('ip_address');
            $table->string('country')->nullable()->after('country_code');
            $table->string('city')->nullable()->after('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_visits', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'country', 'city']);
        });
    }
};
