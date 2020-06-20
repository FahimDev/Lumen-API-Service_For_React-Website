<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberAchievementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member-achievement', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('userName');
            $table->String('type');
            $table->String('title');
            $table->String('institution');
            $table->String('instructor');
            $table->String('prizePosition');
            $table->String('prizeCategory');
            $table->String('membership');
            $table->String('year');
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
        Schema::dropIfExists('member-achievement');
    }
}
