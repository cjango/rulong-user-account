<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountRulesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 50);
            $table->string('name', 50)->index('name');
            $table->string('type', 20);
            $table->decimal('variable', 20, 3)->default(0.000);
            $table->integer('trigger')->default(0);
            $table->boolean('deductions')->default(0);
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_account_rules');
    }

}
