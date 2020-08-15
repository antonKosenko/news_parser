<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentNewsUaTable extends Migration
{
    public $tableName = 'content_news_ua';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->integer('news_id')->unsigned()->comment('id news');
            $table->string('url', 255)->nullable()->default(null);
            $table->string('title', 255)->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->dateTime('date_public')->nullable()->default(null);

            $table->primary(["news_id"], 'news_id');

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
