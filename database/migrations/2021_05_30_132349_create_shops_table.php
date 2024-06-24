<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('channel_address', 30)->index()->nullable();//
            $table->string('page_address', 30)->index()->nullable();
            $table->string('site_address', 30)->index()->nullable();
            $table->string('name', 50)->nullable();
            $table->string('description', 255)->nullable();
            $table->tinyInteger('group_id')->nullable();
            $table->tinyInteger('timestamp')->default(30);
            $table->boolean('active')->default(true);
            $table->timestamp('subscribe')->nullable();
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
        Schema::dropIfExists('shops');
    }
}
