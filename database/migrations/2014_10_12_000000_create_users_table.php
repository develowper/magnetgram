<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->nullable();
            $table->string('telegram_username', 100)->nullable()->index();
            $table->string('telegram_id', 50)->nullable()->index();
            $table->string('role', 2)->nullable()->default("us");
            $table->string('password', 255)->nullable();
            $table->string('token')->default(bin2hex(openssl_random_pseudo_bytes(30)));
            $table->integer('limits')->default(1);
            $table->integer('score')->default(0);
            $table->smallInteger('step')->nullable()->default(0);
            $table->boolean('active')->default(true);
//            $table->softDeletes();
            $table->rememberToken();

            $table->dateTime('expires_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
