<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\services;//model
use App\research;//model
use App\contact;//model
use App\about;//model
use App\webTitle;//model

class webManager extends Controller
{
    function selectServices(){
        $result  = services::all();
        return $result;
    }

    function selectResearch(){
        $result  = research::all();
        return $result;
    }

    function selectContact(){
        $result  = contact::all();
        return $result;
    }

    function selectAbout(){
        $result  = about::all();
        return $result;
    }

    function selectWebTitles(){
        $result  = webTitle::all();
        return $result;
    }

    function selectWebTitle(Request $request){
        $id = $request->input('page');
        $result  = webTitle::where('title',$id)->get();
        return $result;
    }

}
