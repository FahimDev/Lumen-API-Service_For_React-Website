<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\admin;//model

use App\member_info;//model

use App\member_edu;//model

use App\member_work;//model

use App\member_hobby;//model

use App\member_url;//model

use App\member_network;//model

use \Firebase\JWT\JWT; //Custom add for enciding Token

use Illuminate\Support\Str; //custom import for random string generator

use Illuminate\Support\Facades\Hash; //custom import for hashing user password

use Symfony\Component\HttpFoundation\File\UploadedFile;

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

                return response()->json(['Token'=>$jwt,'response'=>'As-Salamu Alaykum'])->header('User', $userName)->header('Login-Session', '1 Hour');
           
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

    function addUser(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $newUser = $request->input('newUser');
        $newUserPass = $request->input('newUserPass');

        if($authTokenStatus == "Halal"){

            $getUserType=admin::where('user_name',$userName)->first('type');

            if($getUserType->type=='1'){

                $hashedPass = Hash::make($newUserPass, [
                    'memory' => 1024,
                    'time' => 2,
                    'threads' => 2,
                ]);

                $createUser = admin::insert(['user_name' => $newUser,'password' => $hashedPass,'type' => "2",'status'=>'1']);
                $createProfile = member_info::insert(['userName' => $newUser]);
               
                    if($createUser== true && $createProfile == true){
                        return "success";
                    }
                    else{
                        return "error";
                    }


            }
            else{
                return $getUserType;
            }
        }
        else{
            return "Invalid Token !";
        }
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

    function updateProfile(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->input('userName') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $changeKey = $request->input('type');
        $changeVal = $request->input('data');


        if($authTokenStatus == "Halal"){

            $updateStatus=member_info::where('userName',$userName)->update([$changeKey =>$changeVal]);

            if($updateStatus==true){
                return response()->json(['updated_info'=>$changeVal]) ;
            }
            else{
                return "Update fail";
            }
        }
        else{
            return "Invalid Token !";
        }
    }


    function uploadImg(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->input('userName') ;

        $imgFile = $request->file('uploadImg') ;

        if ($request->hasFile('uploadImg')) {
            $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

                if($authTokenStatus == "Halal"){

                    $name =$userName.time().'.'.$imgFile->getClientOriginalExtension();
                    $destinationPath = $imgFile->move('membersImg',$name);
                                                    /**
                                                     * when you deploy this project to actual server
                                                     * replace the $destinationPath value with "public/membersImg"
                                                     * and remove the 'public/' portion from the value of
                                                     * $updateProImg 
                                                     */

                    $baseURL = env ('APP_URL');

                    $updateProImg = member_info::where('userName',$userName)->update(['imgPath' =>$baseURL.'public/'.$destinationPath]);

                    if($updateProImg == true){
                        return "success";
                    }
                    else{
                        return "error";
                    }
                    
                   // return $destinationPath;

                }
                else{
                    return "Invalid Token !";
                }
        }
        else{
            return "Image file not found !";
        }

        

    }

    function updateMemberEdu(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->input('userName') ;

        $operationType = $request->method();

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $nameKey = $request->input('nameKay');
        $nameVal = $request->input('nameVal');

        $batchKey = $request->input('batchKey');
        $batchVal = $request->input('batchVal');


        if($authTokenStatus == "Halal"){


            if($operationType == 'POST')
            {
                if($nameKey == 'school' || $nameKey == 'college')
                {
                    $updateStatus=member_edu::insert(['userName' =>$userName,$nameKey =>$nameVal,$batchKey =>$batchVal]);

                    if($updateStatus==true){
                        return "Success";
                    }
                    else{
                        return "Update fail";
                    }  
                }
                else 
                {
                    $subjectKey = $request->input('subjectKey');
                    $subjectVal = $request->input('subjectVal');

                    $updateStatus=member_edu::insert(['userName' =>$userName,$nameKey =>$nameVal,$batchKey =>$batchVal,$subjectKey =>$subjectVal]);

                    if($updateStatus==true){
                        return "Success";
                    }
                    else{
                        return "Update fail";
                    }    
                }
            }
            else if($operationType == 'PUT')
            {

                //------update:start(WHERE)-------------------
                $oldNameKey = $request->input('oldNameKey');
                $oldNameVal = $request->input('oldNameVal');

                $oldBatchKey = $request->input('oldBatchKey');
                $oldBatchVal = $request->input('oldBatchVal');
                //------update:end(WHERE)-------------------


                if($nameKey == 'school' || $nameKey == 'college')
                {
                    $updateStatus=member_edu::where(['userName' =>$userName,$oldNameKey =>$oldNameVal,$oldBatchKey =>$oldBatchVal])->update([$nameKey =>$nameVal,$batchKey =>$batchVal]);

                    if($updateStatus==true){
                        return "Success";
                    }
                    else{
                        return "Update fail";
                    }  
                }
                else
                {
                    $subjectKey = $request->input('subjectKey');
                    $subjectVal = $request->input('subjectVal');

                    $oldSubjectKey = $request->input('oldSubjectKey');
                    $oldSubjectVal = $request->input('oldSubjectVal');

                    $updateStatus=member_edu::where(['userName' =>$userName,$oldNameKey =>$oldNameVal,$oldBatchKey =>$oldBatchVal,$oldSubjectKey =>$oldSubjectVal])->update([$nameKey =>$nameVal,$batchKey =>$batchVal,$subjectKey =>$subjectVal]);

                    if($updateStatus==true){
                        return "Success";
                    }
                    else{
                        return "Update fail";
                    }    
                }
            }
            else if ($operationType == 'DELETE')
            {
                if($nameKey == 'school' || $nameKey == 'college')
                {
                    $deleteStatus=member_edu::where(['userName'=>$userName,$nameKey =>$nameVal,$batchKey =>$batchVal])->delete();

                    if($deleteStatus==true){
                        return "Delete Success";
                    }
                    else{
                        return "Delete fail";
                    }  
                }
                else
                {
                    $subjectKey = $request->input('subjectKey');
                    $subjectVal = $request->input('subjectVal');

                    $deleteStatus=member_edu::where(['userName'=>$userName,$nameKey =>$nameVal,$batchKey =>$batchVal,$subjectKey =>$subjectVal])->delete();

                    if($deleteStatus==true){
                        return "Delete Success";
                    }
                    else{
                        return "Delete fail";
                    }    
                }
            }
            else{
                return ' *******Super Global Variable ERROR!******* ';
                //****************The reason of taking HTTP request methods so serious****************
                /* 
                    The POST request is not Idempotent but the DELETE request is Idempotent.

                    An idempotent HTTP method is a HTTP method that can be called many times without different outcomes

                    Idempotency is important in building a fault-tolerant API.

                    Suppose a client wants to update a resource through POST. Since POST is not an idempotent method, 
                    calling it multiple times can result in wrong updates. What would happen if you sent out the POST 
                    request to the server, but you get a timeout. Is the resource actually updated? Does the timeout 
                    happened during sending the request to the server, or the response to the client? Can we safely retry again, 
                    or do we need to figure out first what has happened with the resource? By using idempotent methods, we do not 
                    have to answer this question, but we can safely resend the request until we actually get a response back from the server.

                    So, if you use POST for deleting, there will consequences.
                */

            }
     
        }
        else{
            return "Invalid Token !";
        }
    }

    function updateWork(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();
        $type = $request->input('type');
        $orgName = $request->input('orgName');
        $rank = $request->input('rank');
        $start = $request->input('start');
        $end = $request->input('end');

        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addWork = member_work::insert(['userName'=>$userName,'type'=>$type,'orgName'=>$orgName,'rank'=>$rank,'started'=>$start,'end'=>$end]);
                if($addWork == true){
                    return "success";
                }
                else{
                    return "not added!";
                }
            }
            else if($operationType == "PUT"){
                
                $updateKey = $request->input('changeKey');
                $updateVal = $request->input('changeVal');

                $updateWork = member_work::where(['userName'=>$userName,'type'=>$type,'orgName'=>$orgName,'rank'=>$rank,'started'=>$start,'end'=>$end])->update([$updateKey => $updateVal]);
                if($updateWork == true){
                    return "success";
                }
                else{
                    return "not removed!";
                }
                
            }
            else if($operationType == "DELETE"){
                $removeWork = member_work::where(['userName'=>$userName,'type'=>$type,'orgName'=>$orgName,'rank'=>$rank,'started'=>$start,'end'=>$end])->delete();
                if($removeWork == true){
                    return "success";
                }
                else{
                    return "not removed!";
                }
            }
            else{
                return ' *******Super Global Variable ERROR!******* ';
            }

        }
        else{
            return "Invalid Token !";
        }
    }

    
    function updateHobby(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();

        $hobby = $request->input('hobby');

        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addHobby = member_hobby::insert(['userName'=>$userName,'hobby'=>$hobby]);
                if($addHobby == true){
                    return "success";
                }
                else{
                    return "not added!";
                }
            }
            else if($operationType == "PUT"){
                
                $updateVal = $request->input('changeVal');

                $updatehobby = member_hobby::where(['userName'=>$userName,'hobby' => $hobby])->update(['hobby' => $updateVal]);
                if($updatehobby == true){
                    return "success";
                }
                else{
                    return "not removed!";
                }
                
            }
            else if($operationType == "DELETE"){
                $removeHobby = member_hobby::where(['userName'=>$userName,'hobby' => $hobby])->delete();
                if($removeHobby == true){
                    return "success";
                }
                else{
                    return "not removed!";
                }
            }
            else{
                return ' *******Super Global Variable ERROR!******* ';
            }

        }
        else{
            return "Invalid Token !";
        }
    }




    function updateURL(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();

        $title = $request->input('title');
        $url = $request->input('url');

        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addHobby = member_url::insert(['userName'=>$userName,'buttonTitle'=>$title,'url'=>$url]);
                if($addHobby == true){
                    return "success";
                }
                else{
                    return "not added!";
                }
            }
            else if($operationType == "PUT"){
                
                $updateKey = $request->input('changeKey');
                $updateVal = $request->input('changeVal');

                $updatehobby = member_url::where(['userName'=>$userName,'buttonTitle'=>$title,'url'=>$url])->update([$updateKey => $updateVal]);
                if($updatehobby == true){
                    return "success";
                }
                else{
                    return "not removed!";
                }
                
            }
            else if($operationType == "DELETE"){
                $removeHobby = member_url::where(['userName'=>$userName,'buttonTitle'=>$title,'url'=>$url])->delete();
                if($removeHobby == true){
                    return "success";
                }
                else{
                    return "not removed!";
                }
            }
            else{
                return ' *******Super Global Variable ERROR!******* ';
            }

        }
        else{
            return "Invalid Token !";
        }
    }



    function stateReference(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        if($authTokenStatus == "Halal"){

            $updateVal = $request->input('status');
            
            if($updateVal == '2'){
                $stateRef = member_network::where(['userName'=>$userName])->update(['status' => '0']);
                if($stateRef == true){
                    return "success";
                }
                else{
                    return "not updated!";
                }
            }
            else if ($updateVal == '3'){
                $stateRef = member_network::where(['userName'=>$userName])->update(['status' => '1']);
                if($stateRef == true){
                    return "success";
                }
                else{
                    return "not updated!";
                }
            }
            else{

                    $title = $request->input('title');
                    $name = $request->input('name');
                    $position = $request->input('position');
                    $contact = $request->input('contact');
                    $eMail = $request->input('eMail');
                    $url = $request->input('url');

                    $stateRef = member_network::where(['userName'=>$userName,'title'=>$title,'name'=>$name,'position'=>$position,'contact'=>$contact,'eMail'=>$eMail,'url'=>$url])->update(['status' => $updateVal]);
                    if($stateRef == true){
                        return "success";
                }
                else{
                    return "not updated!";
                }
            }
           

        }else{
            return "Invalid Token !";
        }
    }



    function updateReference(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();

        $title = $request->input('title');
        $name = $request->input('name');
        $position = $request->input('position');
        $contact = $request->input('contact');
        $eMail = $request->input('eMail');
        $url = $request->input('url');

        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addHobby = member_network::insert(['userName'=>$userName,'title'=>$title,'name'=>$name,'position'=>$position,'contact'=>$contact,'eMail'=>$eMail,'url'=>$url,'status'=>'1']);
                if($addHobby == true){
                    return "success";
                }
                else{
                    return "not added!";
                }
            }
            else if($operationType == "PUT"){
                
                $updateKey = $request->input('changeKey');
                $updateVal = $request->input('changeVal');

                $updatehobby = member_network::where(['userName'=>$userName,'title'=>$title,'name'=>$name,'position'=>$position,'contact'=>$contact,'eMail'=>$eMail,'url'=>$url])->update([$updateKey => $updateVal]);
                if($updatehobby == true){
                    return "success";
                }
                else{
                    return "not updated!";
                }
                
            }
            else if($operationType == "DELETE"){
                $removeHobby = member_network::where(['userName'=>$userName,'title'=>$title,'name'=>$name,'position'=>$position,'contact'=>$contact,'eMail'=>$eMail,'url'=>$url])->delete();
                if($removeHobby == true){
                    return "success";
                }
                else{
                    return "not removed!";
                }
            }
            else{
                return ' *******Super Global Variable ERROR!******* ';
            }

        }
        else{
            return "Invalid Token !";
        }
    }

    function appShowRef(Request $request){

        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);
        if($authTokenStatus == "Halal"){
            $member_id = $userName;
            $result  = member_network::where('userName',$member_id)->get();
            if($result == true){
                return $result;
            }
            else{
                return "SQL Error!";
            }
            return $result;
        }
        else{
            return "Invalid Token !";
        }
        
    }


}



//eyJpc3MiOiJodHRwOlwvXC9nbGl0Y2gtaW5ub3ZhdGlvbnMuY29tXC8iLCJhdWQiOiJmYWhpbTAzNzMiLCJpYXQiOjE1OTMxNjgzNjEsImV4cCI6MTU5MzE3MTk2MX0
//eyJpc3MiOiJodHRwOlwvXC9nbGl0Y2gtaW5ub3ZhdGlvbnMuY29tXC8iLCJhdWQiOiJmYWhpbTAzNzMiLCJpYXQiOjE1OTMxNjgyODYsImV4cCI6MTU5MzE3MTg4Nn0