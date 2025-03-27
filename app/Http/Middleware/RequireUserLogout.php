<?php

namespace App\Http\Middleware;

use \App\Session\User\Login as SessioUserLogin;

class RequireUserLogout
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
        if(SessioUserLogin::isLogged()){
            $request->getRouter()->redirect('/account');
        }
        //CONTINUA A EXECUÇÃO
        return $next($request);
    }
}
