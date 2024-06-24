<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('chat_id', 50)->index();
            $table->string('chat_username', 50)->index();
            $table->bigInteger('user_id')->unsigned()->default(0);
            $table->integer('budget')->unsigned()->default(0);
            $table->integer('done_now')->unsigned()->default(0);
            $table->integer('done_score')->unsigned()->default(0);
            $table->integer('ref_score')->unsigned()->default(0);
            $table->string('type', 2)->default('tf');//telegram follow,if[instagram follow],ic[instagram comment],

            $table->boolean('active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
