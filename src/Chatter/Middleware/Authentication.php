<?php

namespace Chatter\Middleware;


use Chatter\Models\User;

class Authentication
{
    public function __invoke($request, $response, $next)
    {
        $auth = $request->getHeader('Authorization');
        if(!$auth) {
            return  $response->withStatus(401);
        }
        $apikey = $auth[0];

        $user = new User();
        if(!$user->authenticate($apikey)){
            return $response->withStatus(401);;
        }


        $response = $next($request, $response);

        return $response;
    }
}