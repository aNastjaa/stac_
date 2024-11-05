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
        Schema::create('archives', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('post_id'); // Foreign key referencing posts
            $table->timestamp('moved_at');
            $table->string('theme');
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archive');
    }
};
