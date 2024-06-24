<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDivarTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('divar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('chat_id', 30)->index();
            $table->string('chat_username', 50);
            $table->string('message_id', 30)->index();
            $table->string('chat_type', 10);
            $table->tinyInteger('group_id')->unsigned()->default(0);
            $table->boolean('is_vip')->default(false);
            $table->boolean('processed')->default(false);
            $table->boolean('blocked')->default(false);
            $table->boolean('validated')->default(false);
            $table->integer('members')->nullable();
            $table->smallInteger('follow_score')->unsigned()->default(0);
            $table->smallInteger('ref_score')->unsigned()->default(0);
            $table->string('chat_title', 255)->nullable();
            $table->string('chat_description', 2048)->nullable();
            $table->string('chat_main_color', 20)->nullable();
            $table->timestamp('expire_time');
            $table->timestamp('start_time')->useCurrent();

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
        Schema::dropIfExists('divar');
    }
}
