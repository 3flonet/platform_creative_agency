<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
        });

        // Migrate existing service categories to the new table
        $existingCategories = DB::table('services')->distinct()->pluck('category')->filter();
        
        foreach ($existingCategories as $title) {
            $categoryId = DB::table('categories')->insertGetId([
                'title' => $title,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('services')->where('category', $title)->update(['category_id' => $categoryId]);
        }

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

        // Restore data back to string
        DB::table('services')->join('categories', 'services.category_id', '=', 'categories.id')
            ->update(['services.category' => DB::raw('categories.title')]);

        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
