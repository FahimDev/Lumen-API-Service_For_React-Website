<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberAcademicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member-Academic', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->String('userName');
            $table->String('school');
            $table->String('sBatch');
            $table->String('college');
            $table->String('cBatch');
            $table->String('diploma');
            $table->String('dSub');
            $table->String('dBatch');
            $table->String('bachelor');
            $table->String('baSub');
            $table->String('baBatch');
            $table->String('masters');
            $table->String('maSub');
            $table->String('msBatch');
            $table->String('phd');
            $table->String('phdSub');
            $table->String('passYear');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {Schema::dropIfExists('member-Academic');//
    }
}
