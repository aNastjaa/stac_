<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyImagePathNullableOnSponsorSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sponsor_submissions', function (Blueprint $table) {
            // Modify the image_path column to be non-nullable
            $table->string('image_path')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sponsor_submissions', function (Blueprint $table) {
            // Revert the image_path column back to nullable
            $table->string('image_path')->nullable()->change();
        });
    }
}
