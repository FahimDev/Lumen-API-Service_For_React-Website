<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberNetworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member-network', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('userName');
            $table->String('title');
            $table->String('name');
            $table->String('position');
            $table->String('contact');
            $table->String('eMail');
            $table->String('url');
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
        Schema::dropIfExists('member-network');
    }
}
