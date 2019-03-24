<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateLuckyTable
 */
class CreateLuckyTable extends Migration
{
    protected $connection = 'mysql';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lucky', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('constellation_id');
            $table->unsignedTinyInteger('average_fortune');
            $table->text('average_description');
            $table->unsignedTinyInteger('love_fortune');
            $table->text('love_description');
            $table->unsignedTinyInteger('career_fortune');
            $table->text('career_description');
            $table->unsignedTinyInteger('wealth_fortune');
            $table->text('wealth_description');
            $table->date('at_day');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->unique('constellation_id', 'uni_constellation');
            $table->unique('at_day', 'uni_at_day');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (env('APP_ENV') === 'production') {
            return;
        }
        Schema::drop('lucky');
    }
}
