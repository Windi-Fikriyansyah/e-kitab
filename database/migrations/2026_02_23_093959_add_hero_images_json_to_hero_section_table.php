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
        Schema::table('hero_section', function (Blueprint $table) {
            $table->json('hero_images')->nullable()->after('hero_image_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hero_section', function (Blueprint $table) {
            $table->dropColumn('hero_images');
        });
    }
};
