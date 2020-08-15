<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{
    public $tableName = 'news';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('news_id')->unsigned()->comment('id news');
            $table->string('image_origin_path', 255)->nullable()->default(null);
            $table->string('image_local_name', 255)->nullable()->default(null);

            $table->unique(["news_id"], 'news_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */

    public function down()
    {
        Schema::dropIfExists($this->tableName);
    }
}
