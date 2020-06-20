<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberWorkHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member-WorkHistory', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('userName');
            $table->String('type');
            $table->String('orgName');
            $table->String('rank');
            $table->String('started');
            $table->String('end');
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
        Schema::dropIfExists('member-WorkHistory');
    }
}
