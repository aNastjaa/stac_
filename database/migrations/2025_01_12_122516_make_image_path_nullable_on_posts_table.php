<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeImagePathNullableOnPostsTable extends Migration
{
    public function up(): void
{
    Schema::table('posts', function (Blueprint $table) {
        $table->string('image_path')->nullable(false)->change();  // Set image_path to NOT nullable
    });
}

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image_path')->nullable(false)->change(); // Revert back if needed
        });
    }
}
