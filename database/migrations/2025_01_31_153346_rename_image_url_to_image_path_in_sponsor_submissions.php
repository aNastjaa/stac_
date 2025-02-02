<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sponsor_submissions', function (Blueprint $table) {
            // Rename the 'image_url' column to 'image_path'
            $table->renameColumn('image_url', 'image_path');

            // Optional: Adjust column type if needed, e.g., increase length if required
            $table->string('image_path')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sponsor_submissions', function (Blueprint $table) {
            // Reverse the column renaming
            $table->renameColumn('image_path', 'image_url');
        });
    }
};
