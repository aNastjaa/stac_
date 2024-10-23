<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            // Primary key as UUID
            $table->uuid('id')->primary();

            // Foreign key to users table (using uuid)
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Other columns
            $table->string('full_name')->nullable();
            $table->text('bio')->nullable();
            $table->json('external_links')->nullable(); // JSON field for external links

            // Foreign key to uploads table
            $table->uuid('avatar_id')->nullable();
            $table->foreign('avatar_id')->references('id')->on('uploads')->onDelete('set null');
            $table->json('external_links')->nullable(); // JSON field for external links

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
}
