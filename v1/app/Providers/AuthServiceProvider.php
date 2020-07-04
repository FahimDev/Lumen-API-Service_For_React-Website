<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

use Illuminate\Http\Request;
use App\Http\Controllers\serviceController;//Custom add for getting the private key
use \Firebase\JWT\JWT; //Custom add for decoding Token

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */

    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            //if ($request->input('api_token')) {
            //      return User::where('api_token', $request->input('api_token'))->first();
            //}
            $requestedToken = $request->header('access-token');
            $userName = $request->header('userName');
            
            $privateKey =serviceController::getMemberPrivateKey($userName); //getting private key from the controller
            $publicKey = env ('Token_KEY');

            $key =  $privateKey.$publicKey;

            try{
                
                $decoded = JWT::decode($requestedToken, $key, array('HS256'));
                return new User();
                
            }catch(\Exception $e){
                return null;
            }
        });
    }
}
