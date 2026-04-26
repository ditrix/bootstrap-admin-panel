<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('category_trees', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
            $table->text('description')->nullable()->after('slug');
        });

        Schema::table('category_trees', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('category_trees', function (Blueprint $table) {
            $table->dropUnique(['slug']);
        });

        Schema::table('category_trees', function (Blueprint $table) {
            $table->dropColumn(['slug', 'description']);
        });
    }
};
