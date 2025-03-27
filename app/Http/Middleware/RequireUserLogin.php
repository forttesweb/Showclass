<?php

namespace App\Http\Middleware;

use \App\Session\User\Login as SessionUserLogin;

class RequireUserLogin
{
    /**
     * Método responsável por executar o middleware
     * @param Request $request
     * @param Closure next
     * @return Response
     */
    public function handle($request, $next)
    {
        //VERIFICA SE O USUÀRIO ESTÁ LOGADO
        if(!SessionUserLogin::isLogged()){
            $request->getRouter()->redirect('/account/login');
        }
        //CONTINUA A EXECUÇÃO
        return $next($request);
    }
}
