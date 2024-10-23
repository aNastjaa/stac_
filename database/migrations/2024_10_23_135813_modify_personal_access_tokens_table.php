<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPersonalAccessTokensTable extends Migration
{
    public function up()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Change the type of tokenable_id to string
            $table->string('tokenable_id')->change();
        });
    }

    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            // Revert the change if needed
            $table->integer('tokenable_id')->change();
        });
    }
}
