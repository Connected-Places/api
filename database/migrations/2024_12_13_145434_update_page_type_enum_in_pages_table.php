<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Modify the enum column to add 'topic' using raw SQL
            DB::statement("ALTER TABLE pages MODIFY COLUMN page_type ENUM('information', 'landing', 'topic') DEFAULT 'information'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            // Revert the enum column to remove 'topic' using raw SQL
            DB::statement("ALTER TABLE pages MODIFY COLUMN page_type ENUM('information', 'landing') DEFAULT 'information'");
        });
    }
};
