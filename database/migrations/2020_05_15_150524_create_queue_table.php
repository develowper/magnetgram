<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divar_queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('chat_id', 50);
            $table->string('chat_type', 20);
            $table->string('chat_username', 50);
            $table->boolean('is_vip')->default(false);
            $table->string('chat_title', 255)->nullable();
            $table->string('chat_description', 2048)->nullable();
            $table->string('chat_main_color', 20)->nullable();
            $table->integer('show_time')->default(0);//min


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
        Schema::dropIfExists('queue');
    }
}
