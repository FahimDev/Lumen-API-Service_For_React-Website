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
            $table->String('userName')->nullable();
            $table->String('fullName')->nullable();
            $table->String('title')->nullable();
            $table->String('department')->nullable();
            $table->String('imgPath')->nullable();
            $table->String('fatherName')->nullable();
            $table->String('motherName')->nullable();
            $table->String('gender')->nullable();
            $table->String('blood')->nullable();
            $table->String('religion')->nullable();
            $table->String('relationship')->nullable();
            $table->String('currentLoc')->nullable();
            $table->String('parmanentLoc')->nullable();
            $table->String('about')->nullable();
            $table->String('contact')->nullable();
            $table->String('eMail')->nullable();
            $table->String('socialFB')->nullable();
            $table->String('socialTwit')->nullable();
            $table->String('gitHub')->nullable();
            $table->String('linkedIN')->nullable();
            $table->String('expSummary')->nullable();
            $table->String('status')->nullable();
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
