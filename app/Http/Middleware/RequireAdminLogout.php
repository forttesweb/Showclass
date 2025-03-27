<?php

namespace App\Http\Middleware;

use \App\Session\Admin\Login as SessionAdminLogin;

class RequireAdminLogout
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
        if(SessionAdminLogin::isLogged()){
            $request->getRouter()->redirect('/admin-panel');
        }
        //CONTINUA A EXECUÇÃO
        return $next($request);
    }
}
