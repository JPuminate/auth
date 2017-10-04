<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use JPuminate\Auth\Identity\Identity;

class UpdateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(Identity::$userTable, function (Blueprint $table) {
            $table->integer(Identity::$foreignKey)->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(Identity::$userTable, function (Blueprint $table) {
            $table->dropColumn([\JPuminate\Auth\Identity\Identity::$foreignKey]);
        });

    }
}
