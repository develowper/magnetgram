<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTabsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('chat_id', 30)->index();
            $table->string('chat_title',255)->nullable();
            $table->string('group', 50);
            $table->string('chat_type', 10);
            $table->string('chat_username', 50);
            $table->integer('members')->nullable();
            $table->string('message_id', 30)->index()->nullable();
            $table->boolean('processed')->default(false);
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
        Schema::dropIfExists('tabs');
    }
}
