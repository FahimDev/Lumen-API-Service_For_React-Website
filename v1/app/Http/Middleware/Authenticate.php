<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        /**
         * in the type veriable I am checking the request type of Super Global Veriable.
         * the reason is this Luman application is handling my React application's GET request.
         * so if I didn't use this 'if' condition the React app's Get request will not get any response.
         * that is why I am giving open access to my GET request and for POST request I am authenticating.
         * POST is important because POST request can make change in my main database. 
         */
        $type = $request->method();
        if($type != 'GET'){


            if ($this->auth->guard($guard)->guest()) {
                return response('Unauthorized.', 401);
            }


        }
        

        return $next($request)
        ->header('Access-Control-Allow-Origin','*'); //this header is for REACT Dome's Axios request access
    }
}
