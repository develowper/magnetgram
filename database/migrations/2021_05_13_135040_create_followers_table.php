<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('telegram_id', 25)->index();
            $table->string('added_by', 25)->index()->nullable();
            $table->string('chat_id', 25)->index();
            $table->integer('follow_score')->unsigned();
            $table->integer('ref_score')->unsigned();
            $table->boolean('left')->default(false);
            $table->timestamp('created_at')->useCurrent();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('followers');
    }
}
