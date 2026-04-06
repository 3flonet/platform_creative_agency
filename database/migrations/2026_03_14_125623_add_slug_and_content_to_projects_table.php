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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('title');
            $table->longText('content')->nullable()->after('description');
            $table->string('banner_image')->nullable()->after('gallery');
            $table->date('completion_date')->nullable()->after('client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['slug', 'content', 'banner_image', 'completion_date']);
        });
    }
};
