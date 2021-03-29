<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\admin;//model

use App\member_info;//model

use App\member_edu;//model

use App\member_work;//model

use App\member_hobby;//model

use App\member_hashTag;//model

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
        
        if($userLogin != true){
            return "wrongUser";
        }
        
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
                return "200";
            }
            else{
                return "304"; //Not Modified
            }
        }
        else{
            return "401";//Unauthorized header
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
        $id = $request->input('id');
        $type = $request->input('type');
        $orgName = $request->input('orgName');
        $rank = $request->input('rank');
        $start = $request->input('start');
        $end = $request->input('end');

        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addWork = member_work::insert(['userName'=>$userName,'type'=>$type,'orgName'=>$orgName,'rank'=>$rank,'started'=>$start,'end'=>$end]);
                if($addWork == true){
                    return "200"; //Success
                }
                else{
                    return "304"; //Not Modified
                }
            }
            else if($operationType == "PUT"){
                
                $updateKey = $request->input('changeKey');
                $updateVal = $request->input('changeVal');

                $updateWork = member_work::where(['userName'=>$userName,'type'=>$type,'orgName'=>$orgName,'id'=>$id])->update([$updateKey => $updateVal]);
                if($updateWork == true){
                    return "200";  //Success
                }
                else{
                    return "304"; //Not Modified
                }
                
            }
            else if($operationType == "DELETE"){
                $removeWork = member_work::where(['userName'=>$userName,'type'=>$type,'orgName'=>$orgName,'id'=>$id])->delete();
                if($removeWork == true){
                    return "200";  //Success
                }
                else{
                    return "304"; //Not Modified
                }
            }
            else{
                return '405'; //method not allowed
            }

        }
        else{
            return "401";//Unauthorized header
        }
    }

    
    function updateHobby(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();

        $hobby = $request->input('hobby');
        
        $id = $request->input('id');

        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addHobby = member_hobby::insert(['userName'=>$userName,'hobby'=>$hobby]);
                if($addHobby == true){
                    return "200";
                }
                else{
                    return "304";
                }
            }
            else if($operationType == "PUT"){
                
                $updateVal = $request->input('changeVal');

                $updatehobby = member_hobby::where(['userName'=>$userName,'id' => $id,'hobby' => $hobby])->update(['hobby' => $updateVal]);
                if($updatehobby == true){
                    return "200";
                }
                else{
                    return "304";
                }
                
            }
            else if($operationType == "DELETE"){
                $removeHobby = member_hobby::where(['userName'=>$userName,'id' => $id,'hobby' => $hobby])->delete();
                if($removeHobby == true){
                    return "200";
                }
                else{
                    return "304";
                }
            }
            else{
                return '405';
            }

        }
        else{
            return "401";
        }
    }




    function updateURL(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();

        $title = $request->input('title');
        $url = $request->input('url');
        
        $id = $request->input('id');

        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addHobby = member_url::insert(['userName'=>$userName,'buttonTitle'=>$title,'url'=>$url]);
                if($addHobby == true){
                    return "200";
                }
                else{
                    return "304";
                }
            }
            else if($operationType == "PUT"){
                
                $updateKey = $request->input('changeKey');
                $updateVal = $request->input('changeVal');

                $updatehobby = member_url::where(['userName'=>$userName,'buttonTitle'=>$title,'id'=>$id])->update(['buttonTitle' =>  $updateKey ,'url' => $updateVal]);
                if($updatehobby == true){
                    return "200";
                }
                else{
                    return "304";
                }
                
            }
            else if($operationType == "DELETE"){
                $removeHobby = member_url::where(['userName'=>$userName,'buttonTitle'=>$title,'id'=>$id])->delete();
                if($removeHobby == true){
                    return "200";
                }
                else{
                    return "304";
                }
            }
            else{
                return '405';
            }

        }
        else{
            return "401";
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
    
    
    function appShowEdu($type,$memberID){

         $member_id = $memberID;
         
         if($type == "scl"){
             
             $result  = member_edu::where('userName',$member_id)->whereNotNull('school')->select('id','school','sBatch')->get();
        return $result;
             
         }else if($type == "clg"){
             
              $result  = member_edu::where('userName',$member_id)->whereNotNull('college')->select('id','college','cBatch')->get();
        return $result;
             
         }else if($type == "dip"){
             
              $result  = member_edu::where('userName',$member_id)->whereNotNull('diploma')->select('id','diploma','dSub','dBatch')->get();
        return $result;
             
         }else if($type == "bs"){
             
              $result  = member_edu::where('userName',$member_id)->whereNotNull('bachelor')->select('id','bachelor','baSub','baBatch')->get();
        return $result;
             
         }else if($type == "ms"){
             
              $result  = member_edu::where('userName',$member_id)->whereNotNull('masters')->select('id','masters','maSub','msBatch')->get();
        return $result;
             
         }else{
             
             $result  = member_edu::where('userName',$member_id)->whereNotNull('phd')->select('id','phd','phdSub','passYear')->get();
        return $result;
             
         }
         
        
        
    }
    
    function memberEdu(Request $request){
        
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();

        $type = $request->input('type');

        $title = $request->input('institute');
        $name = $request->input('degree');
        $batch = $request->input('batch');

        if($authTokenStatus == "Halal"){
            if($operationType == "POST"){
                if($type == "scl"){
                    $addEdu = member_edu::insert(['userName'=>$userName,'school'=>$title,'sbatch'=>$batch]);
                }else if($type == "clg"){
                    $addEdu = member_edu::insert(['userName'=>$userName,'college'=>$title,'cBatch'=>$batch,]);
                }else if($type == "dip"){
                    $addEdu = member_edu::insert(['userName'=>$userName,'diploma'=>$title,'dSub'=>$name,'dBatch'=>$batch]);
                }else if($type == "bs"){
                    $addEdu = member_edu::insert(['userName'=>$userName,'bachelor'=>$title,'baSub'=>$name,'baBatch'=>$batch]);
                }else if($type == "ms"){
                    $addEdu = member_edu::insert(['userName'=>$userName,'masters'=>$title,'maSub'=>$name,'msBatch'=>$batch]);
                }else{
                    $addEdu = member_edu::insert(['userName'=>$userName,'phd'=>$title,'phdSub'=>$name,'passYear'=>$batch]);
                }
                
                
                 if($addEdu == true){
                     return "200";
                 }
                 else{
                     return "304";
                 }
             }else if($operationType == "PUT"){
                $updateKey = $request->input('id');
                    
                
                   if($type == "scl"){
                       
                       $updateEdu = member_edu::where(['userName'=>$userName,'id'=>$updateKey])->update(['school'=>$title,'sbatch'=>$batch]);
                       
                   }else if($type == "clg"){
                       
                        $updateEdu = member_edu::where(['userName'=>$userName,'id'=>$updateKey])->update(['college'=>$title,'cBatch'=>$batch]);
                        
                   }else if($type == "dip"){
                      
                       $updateEdu = member_edu::where(['userName'=>$userName,'id'=>$updateKey])->update(['diploma'=>$title,'dSub'=>$name,'dBatch'=>$batch]);
                       
                   }else if($type == "bs"){
                       
                       $updateEdu = member_edu::where(['userName'=>$userName,'id'=>$updateKey])->update(['bachelor'=>$title,'baSub'=>$name,'baBatch'=>$batch]);
                       
                   }else if($type == "ms"){
                       
                       $updateEdu = member_edu::where(['userName'=>$userName,'id'=>$updateKey])->update(['masters'=>$title,'maSub'=>$name,'msBatch'=>$batch]);
                   }else{
                       $updateEdu = member_edu::where(['userName'=>$userName,'id'=>$updateKey])->update(['phd'=>$title,'phdSub'=>$name,'passYear'=>$batch]);
                   }
    
                    
                    if($updateEdu == true){
                        return "200";
                    }
                    else{
                        return "304";
                    }
                }else if($operationType == "DELETE"){
                    $delKey = $request->input('id');
                   $removeEdu = member_edu::where(['userName'=>$userName,'id'=>$delKey])->delete();
                   if($removeEdu == true){
                       return "200";
                   }
                   else{
                       return "304";
                   }
                    

             }else{
                return '405';
             }
                
        }else{
            return '401';
        }  
    }

    
function hashTag(Request $request){
        $requestedToken =  $request->header('Access-Token') ;

        $userName = $request->header('User-Name') ;

        $authTokenStatus = $this->authAccessToken($requestedToken,$userName);

        $operationType = $request->method();


        
        $hashTag = $request->input('hashTag');
        $color = $request->input('color');
        
        if($color == "Red"){
            $color = "danger";
        }else if($color == "Green"){
            $color = "success";
        }else if($color == "Blue"){
            $color = "primary";
        }
        else if($color == "Yellow"){
            $color = "warning";
        }
        else if($color == "Grey"){
            $color = "secondary";
        }
        else if($color == "Black"){
            $color = "dark";
        }
        else{
            $color = "info";
        }

       
        if($authTokenStatus == "Halal"){

            if($operationType == "POST"){
               $addHashTag = member_hashTag::insert(['userName'=>$userName,'hashTag'=>$hashTag,'color'=>$color]);
                if($addHashTag == true){
                    return "200";
                }
                else{
                    return "304";
                }
            }
            else if($operationType == "PUT"){

                $id = $request->input('id');
                
                $updateHashTag = member_hashTag::where(['userName'=>$userName,'id'=>$id])->update(['hashTag'=>$hashTag,'color'=>$color]);
                if($updateHashTag == true){
                    return "200";
                }
                else{
                    return "304";
                }
                
            }
            else if($operationType == "DELETE"){

                $id = $request->input('id');

                $removeHashTag = member_hashTag::where(['userName'=>$userName,'id'=>$id])->delete();
                if($removeHashTag == true){
                    return "200";
                }
                else{
                    return "304";
                }
            }
            else{
                return '405';
            }

        }
        else{
            return "401";
        }
    }


}



//eyJpc3MiOiJodHRwOlwvXC9nbGl0Y2gtaW5ub3ZhdGlvbnMuY29tXC8iLCJhdWQiOiJmYWhpbTAzNzMiLCJpYXQiOjE1OTMxNjgzNjEsImV4cCI6MTU5MzE3MTk2MX0
//eyJpc3MiOiJodHRwOlwvXC9nbGl0Y2gtaW5ub3ZhdGlvbnMuY29tXC8iLCJhdWQiOiJmYWhpbTAzNzMiLCJpYXQiOjE1OTMxNjgyODYsImV4cCI6MTU5MzE3MTg4Nn0