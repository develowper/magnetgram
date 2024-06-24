<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('shop_id')->unsigned();
            $table->string('name', 100)->nullable();
            $table->string('slug', 100)->nullable();
            $table->string('description', 250)->nullable();
            $table->integer('price')->unsigned()->default(0);
            $table->integer('discount_price')->unsigned()->default(0);
            $table->smallInteger('count')->unsigned()->default(0);
            $table->string('tags', 100)->nullable();
            $table->boolean('reminder')->default(true);

            $table->timestamp('created_at')->useCurrent();

            $table->foreign('shop_id')->references('id')->on('shops');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
