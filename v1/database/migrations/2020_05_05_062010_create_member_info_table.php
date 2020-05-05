<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_info', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('user_name');
            $table->String('department');
            $table->String('profile_img');
            $table->String('full_name');
            $table->String('father_name');
            $table->String('mother_name');
            $table->String('current_address');
            $table->String('parmanent_address');
            $table->String('contact_number');
            $table->String('email');
            $table->String('social_media');
            $table->String('git_office');
            $table->String('school');
            $table->String('college');
            $table->String('diploma_degree');
            $table->String('bachelor_degree');
            $table->String('ms_degree');
            $table->String('phd_degree');
            $table->String('work');
            $table->String('work_rank');
            $table->String('expertise');
            $table->String('extracurricular_activity');
            $table->String('about');
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
        Schema::dropIfExists('member_info');
    }
}
