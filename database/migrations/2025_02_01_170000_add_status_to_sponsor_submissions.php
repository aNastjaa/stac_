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
        $table->string('status')->default('pending'); // Add a default value
    });
}

public function down()
{
    Schema::table('sponsor_submissions', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
