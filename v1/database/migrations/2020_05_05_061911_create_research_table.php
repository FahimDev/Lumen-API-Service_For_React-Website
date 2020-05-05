<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('research', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('title');
            $table->String('title_img');
            $table->String('summary');
            $table->String('cover_img');
            $table->String('details');
            $table->String('url_1_title');
            $table->String('url_1');
            $table->String('url_2_title');
            $table->String('url_2');
            $table->String('url_3_title');
            $table->String('url_3');
            $table->String('author');
            $table->String('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('research');
    }
}
