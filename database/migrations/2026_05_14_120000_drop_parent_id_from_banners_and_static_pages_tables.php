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
        if (Schema::hasColumn('banners', 'parent_id')) {
            Schema::table('banners', function (Blueprint $table): void {
                $table->dropColumn('parent_id');
            });
        }

        if (Schema::hasColumn('static_pages', 'parent_id')) {
            Schema::table('static_pages', function (Blueprint $table): void {
                $table->dropColumn('parent_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('banners', 'parent_id')) {
            Schema::table('banners', function (Blueprint $table): void {
                $table->unsignedBigInteger('parent_id')->default(0)->index()->after('id');
            });
        }

        if (! Schema::hasColumn('static_pages', 'parent_id')) {
            Schema::table('static_pages', function (Blueprint $table): void {
                $table->unsignedBigInteger('parent_id')->default(0)->index()->after('id');
            });
        }
    }
};
