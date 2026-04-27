<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seo_redirects')) {
            return;
        }

        Schema::create('seo_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('slug_from', 1000);
            $table->string('slug_to', 2000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['slug_from']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_redirects');
    }
};
