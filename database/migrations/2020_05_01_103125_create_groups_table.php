<?php

use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->string('emoji', 20);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        Group::truncate();
        DB::table('groups')->insert([
            ['id' => 1, 'name' => 'آزاد', 'emoji' => '❓'],
            ['id' => 2, 'name' => 'کسب-و-کار', 'emoji' => '💸'],
            ['id' => 3, 'name' => 'سرگرمی', 'emoji' => '🎪'],
            ['id' => 4, 'name' => 'ورزشی', 'emoji' => '⚽'],
            ['id' => 5, 'name' => 'ادبیات', 'emoji' => '🎭'],
            ['id' => 6, 'name' => 'هنری', 'emoji' => '🎨'],
            ['id' => 7, 'name' => 'خبری', 'emoji' => '📡'],
            ['id' => 8, 'name' => 'فیلم-موسیقی', 'emoji' => '🔊'],
            ['id' => 9, 'name' => 'تصویر', 'emoji' => '📷'],
            ['id' => 10, 'name' => 'علمی', 'emoji' => '🔭'],
            ['id' => 11, 'name' => 'آموزشی', 'emoji' => '🎓'],
            ['id' => 12, 'name' => 'مذهبی', 'emoji' => '🙏'],


        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
