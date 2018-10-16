<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_accounts', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->primary();
            $table->decimal('cash', 20, 3)->default(0.000);
            $table->decimal('score', 20, 3)->default(0.000);
            $table->decimal('act_a', 20, 3)->default(0.000);
            $table->decimal('act_b', 20, 3)->default(0.000);
            $table->decimal('act_c', 20, 3)->default(0.000);
            $table->decimal('act_d', 20, 3)->default(0.000);
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
        Schema::drop('user_accounts');
    }

}
