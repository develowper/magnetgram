<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('user_telegram_id', 30)->index();
            $table->string('chat_id', 30)->index();
            $table->string('image', 30)->nullable();
            $table->string('message_id', 30)->index()->nullable();
            $table->string('chat_type', 10);
            $table->integer('group_id')->unsigned()->default(1);
            $table->string('chat_main_color', 20)->nullable();
            $table->string('chat_username', 50)->nullable();;
            $table->string('chat_title', 255)->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('auto_tag')->default(false);
            $table->boolean('auto_tab')->default(false);
            $table->boolean('auto_tab_day')->default(false);
            $table->string('tag', 100)->nullable();
            $table->string('chat_description', 2048)->nullable();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('no action');
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
        Schema::dropIfExists('chats');
    }
}
