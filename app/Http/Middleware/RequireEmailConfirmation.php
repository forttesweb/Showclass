<?php

namespace App\Http\Middleware;

use \App\Session\User\Login as SessionUserLogin;

class RequireEmailConfirmation
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
        if(SessionUserLogin::isConfirmed() == 0){
            $request->getRouter()->redirect('/account?status=error');
        }
        //CONTINUA A EXECUÇÃO
        return $next($request);
    }
}