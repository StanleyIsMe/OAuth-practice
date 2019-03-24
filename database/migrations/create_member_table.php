<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateMemberTable
 */
class CreateMemberTable extends Migration
{
    protected $connection = 'mysql';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('fb_id', 190);
            $table->string('password', 255);
            $table->string('token', 255);
            $table->string('access_token', 255);
            $table->integer('expired_at');
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->unique('email', 'uni_email');
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
        Schema::drop('member');
    }
}
