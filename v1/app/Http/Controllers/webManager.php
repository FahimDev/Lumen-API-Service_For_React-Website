<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\services;//model
use App\research;//model
use App\contact;//model
use App\about;//model
use App\webTitle;//model
use App\member_info;//model
use App\member_earn;//model
use App\member_exp;//model
use App\member_edu;//model
use App\member_work;//model
use App\member_url;//model
use App\member_hashTag;//model
use App\member_hobby;//model
use App\member_network;//model



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
        $page_id = $request->input('id');
        $result  = webTitle::where('id',$page_id)->get();
        return $result;
    }

    function selectMembers(){
        $result  = member_info::all();
        return $result;
    }

    function selectMemberInfo($memberID){
        $member_id = $memberID;
        $result  = member_info::where('userName',$member_id)->get();
        return $result;
    }

    function selectMemberEducation($memberID){
        $member_id = $memberID;
        $result  = member_edu::where('userName',$member_id)->get();
        return $result;
    }

    function selectMemberWorkHistory($memberID){
        $member_id = $memberID;
        $result  = member_work::where('userName',$member_id)->get();
        return $result;
    }

    function selectMemberHashTag($memberID){
        $member_id = $memberID;
        $result  = member_hashTag::where('userName',$member_id)->get();
        return $result;
    }

    function selectMemberHobby($memberID){
        $member_id = $memberID;
        $result  = member_hobby::where('userName',$member_id)->get();
        return $result;
    }

    function selectMemberURL($memberID){
        $member_id = $memberID;
        $result  = member_url::where('userName',$member_id)->get();
        return $result;
    }

    function selectMemberEarn($memberID,$type){
        $member_id = $memberID;
        $result  = member_earn::where('userName',$member_id)->where('type',$type)->get();
        return $result;
    }

    function selectMemberExp($memberID,$type){
        $member_id = $memberID;
        $result  = member_exp::where('userName',$member_id)->where('type',$type)->get();
        return $result;
    }

    function selectMemberNetwork($memberID){
        $member_id = $memberID;
        $result  = member_network::where('userName',$member_id)->where('status','1')->get();
        return $result;
    }


}
