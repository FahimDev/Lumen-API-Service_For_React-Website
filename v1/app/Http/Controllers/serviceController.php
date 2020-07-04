<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\admin;//model

use \Firebase\JWT\JWT; //Custom add for enciding Token

use Illuminate\Support\Str; //custom import for random string generator

use Illuminate\Support\Facades\Hash; //custom import for hashing user password

use Crypt;
use App\User;

class serviceController extends Controller
{
    
    function Signin(Request $request){
        $userName = $request->input('userName') ;
        $password = $request->input('password') ;

        

        $userLogin  = admin::where('user_name',$userName)->first('password');
        $userPassword = $userLogin->password ;

        if (Hash::check($password, $userPassword)) {
            $privateKey = Str::random(40);
            
            $publicKey = env ('TOKEN_KEY');

            $key = $privateKey.$publicKey;
            /**
             * I am adding this type of extra elements with my $privateKey variable
             * because when the AuthServiceProvider get the private Key from the return 
             * valueof getMemberPrivateKey() function the structure of the string will be 
             * something like this. I could remove those characters from the sring but I think
             * this current process will make it more complex.Mybe it will be good for security :P 
             */
            $payload = array(
                "iss" => "http://glitch-innovations.com/",
                "aud" =>  $userName,
                "iat" => time(),
                "exp" => time()+3600 //unit is second. 3600 = 1 hour
            );

            $updateStatus=admin::where('user_name',$userName)->update(['token_element' =>$privateKey]);

            if($updateStatus==true){

                $jwt = JWT::encode($payload, $key);

                return response()->json(['Token'=>$jwt,'response'=>'As-salamu Alaykum'])->header('User', $userName)->header('Login-Session', '1 Hour');
           
            }
            else{
                return "Update fail";
            }
        }

        //$countUser = admin::where(['user_name' => $userName,'password'=>$password])->count();
        
        //if($countUser == 1){       }
        else{
            return "Shoitan!";
        }
    }
    function authAccessToken($getToken,$getUserName){
        $token = $getToken;
        $user = $getUserName;

        $privateKey = $this->getMemberPrivateKey($user);

        $publicKey = env ('TOKEN_KEY');

        $key = $privateKey.$publicKey;

        try{

            $decoded = JWT::decode($token, $key, array('HS256'));
            return "Halal";

        }catch(\Exception $e){
            return "Haram";
        }

    }
    function getMemberPrivateKey($userName){
       // $member_id = $request->input('user_name');
        $member_id = $userName;
        $result  = admin::where('user_name',$member_id)->first('token_element');
        $token = $result->token_element;
        return $token;
    }

    public static function getMemberHashPass($userName)
    {      
         $member_id = $userName;
         $result  = admin::where('user_name',$member_id)->first('password');
         $hashedPassword = $result->password;
         return $hashedPassword;
     }



    function updatePassword(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->input('userName') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $oldPassword = $request->input('oldPassword') ;
        $newPassword = $request->input('newPassword') ;

        if($authTokenStatus == "Halal"){
            $hashed = Hash::make($newPassword, [
                'memory' => 1024,
                'time' => 2,
                'threads' => 2,
            ]);
    
    
            $result  = admin::where('user_name',$userName)->first('password');
            $hashedPassword = $result->password;
    
            if (Hash::check($oldPassword, $hashedPassword)) {
                $updateStatus=admin::where('user_name',$userName)->update(['password' =>$hashed]);
    
                if($updateStatus==true){
                    return response()->json(['updated_password'=>$newPassword]) ;
                }
                else{
                    return "Update fail";
                }
            }
            else{
                return "Wrong Password !";
            }
        }
        else{
            return "Invalid Token !";
        }
     
       
    }

}



//eyJpc3MiOiJodHRwOlwvXC9nbGl0Y2gtaW5ub3ZhdGlvbnMuY29tXC8iLCJhdWQiOiJmYWhpbTAzNzMiLCJpYXQiOjE1OTMxNjgzNjEsImV4cCI6MTU5MzE3MTk2MX0
//eyJpc3MiOiJodHRwOlwvXC9nbGl0Y2gtaW5ub3ZhdGlvbnMuY29tXC8iLCJhdWQiOiJmYWhpbTAzNzMiLCJpYXQiOjE1OTMxNjgyODYsImV4cCI6MTU5MzE3MTg4Nn0