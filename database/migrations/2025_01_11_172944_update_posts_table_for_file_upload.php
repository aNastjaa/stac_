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
        // Rename 'image_url' to 'image_path' if it exists
        if (Schema::hasColumn('posts', 'image_url')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('image_url', 'image_path');
            });
        }

        // Add the 'status' column if it doesn't already exist
        if (!Schema::hasColumn('posts', 'status')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->string('status')->default('pending');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Rename 'image_path' back to 'image_url' if it exists
        if (Schema::hasColumn('posts', 'image_path')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->renameColumn('image_path', 'image_url');
            });
        }

        // Drop the 'status' column if it exists
        if (Schema::hasColumn('posts', 'status')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
