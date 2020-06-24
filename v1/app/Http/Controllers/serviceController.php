<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\admin;//model

use \Firebase\JWT\JWT; //Custom add for enciding Token

use Illuminate\Support\Str;

class serviceController extends Controller
{
    
    function Signin(Request $request){
        $userName = $request->input('userName') ;
        $password = $request->input('password') ;

        $countUser = admin::where(['user_name' => $userName,'password'=>$password])->count();
        
        if($countUser == 1){

            $privateKey = Str::random(40);
            
            $publicKey = env ('TOKEN_KEY');

            $key = '["'.$privateKey.'"]'.$publicKey;
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

                return response()->json(['Token'=>$jwt,'response'=>'As-salamu Alaykum','randomKey'=>$privateKey])->header('User', $userName)->header('Login Session', '1 Hour');
           
            }
            else{
                return "Update fail";
            }

               
        }
        else{
            return "Shoitan!";
        }
    }

    public static function getMemberPrivateKey($userName){
       // $member_id = $request->input('user_name');
        $member_id = $userName;
        $result  = admin::where('user_name',$member_id)->pluck('token_element');
        //I am using 'pluck' because I just want the value.'get' returns the Key and Value both.  
        return $result;
    }


    /**               Just for testing
     * public static function MemberPrivateKey(Request $request){
     *$member_id = $request->input('user_name');
     *$result  = admin::where('user_name',$member_id)->pluck('token_element');
     *return $result;
     *}
     */


    function updatePassword(Request $request){
        $requestedToken =  $request->header('access_token') ;

        $userName = $request->input('user_name') ;
        $newPassword = $request->input('new_password') ;

        $updateStatus=admin::where('user_name',$userName)->update(['password' =>$newPassword]);

        if($updateStatus==true){
            return response()->json(['updated_password'=>$newPassword]) ;
        }
        else{
          return "Update fail";
        }
       
    }

}
