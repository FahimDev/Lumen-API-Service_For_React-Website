<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberQualificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member-qualification', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('userName');
            $table->String('type');
            $table->String('experience');
            $table->String('institution');
            $table->String('startYear');
            $table->String('endYear');
            $table->String('skillTitle');
            $table->String('skillList');
            $table->String('softwareAndTools');
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
        Schema::dropIfExists('member-qualification');
    }
}
