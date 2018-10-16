<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAccountLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_account_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->integer('rule_id')->unsigned();
            $table->string('type', 20);
            $table->decimal('variable', 20, 3);
            $table->decimal('balance', 20, 3);
            $table->boolean('frozen')->default(0);
            $table->text('source')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'rule_id', 'created_at'], 'trigger_key');
            $table->index(['user_id', 'frozen'], 'user_frozen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_account_logs');
    }

}
