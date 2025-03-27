<?php

namespace App\Http\Middleware;

class Maintenance
{
    /**
     * Método responsável por executar o middleware.
     *
     * @param Request $request
     * @param Closure next
     *
     * @return Response
     */
    public function handle($request, $next)
    {
        @session_start();

        // VERIFICA O ESTADO DE MANUTENÇÂO DA PÀGINA
        // if (empty($_SESSION['admin']['usuario'])) {
        //     throw new \Exception('Página em manutenção. Tente novamente mais tarde.', 200);
        // }
        if (getenv('MAINTENANCE') == 'true') {
            throw new \Exception('Página em manutenção. Tente novamente mais tarde.', 200);
        }

        // EXECUTA O PRÒXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}
