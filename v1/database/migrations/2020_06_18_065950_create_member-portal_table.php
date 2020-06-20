<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberPortalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member-portal', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('userName');
            $table->String('password');
            $table->String('fullName');
            $table->String('title');
            $table->String('department');
            $table->String('imgPath');
            $table->String('fatherName');
            $table->String('motherName');
            $table->String('religion');
            $table->String('relationship');
            $table->String('currentLoc');
            $table->String('parmanentLoc');
            $table->String('about');
            $table->String('contact');
            $table->String('eMail');
            $table->String('socialFB');
            $table->String('socialTwit');
            $table->String('gitHub');
            $table->String('expSummary');
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
        Schema::dropIfExists('member-portal');
    }
}
