<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\admin;//model

use \Firebase\JWT\JWT; //Custom add for enciding Token

class serviceController extends Controller
{
    
    function Signin(Request $request){
        $userName = $request->input('userName') ;
        $password = $request->input('password') ;

        $countUser = admin::where(['user_name' => $userName,'password'=>$password])->count();
        
        if($countUser == 1){
            $key = env ('TOKEN_KEY');
            $payload = array(
                "iss" => "http://glitch-innovations.com/",
                "aud" =>  $userName,
                "iat" => time(),
                "exp" => time()+3600 //unit is second. 3600 = 1 hour
            );

            $jwt = JWT::encode($payload, $key);

            return response()->json(['Token'=>$jwt,'response'=>'As-salamu Alaykum'])->header('User', $userName)->header('Login Session', '1 Hour');
        }
        else{
            return "Shoitan!";
        }
    }

    function updatePassword(Request $request){
        $requestedToken =  $request->header('access_token') ;
        $key = env ('TOKEN_KEY');
        $decoded = JWT::decode($requestedToken, $key, array('HS256'));
        return response()->json(['result'=>$decoded]) ;
    }

}
