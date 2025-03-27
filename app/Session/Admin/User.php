<?php

namespace App\Session\User;

class Login
{
    /**
     * Método responsável por iniciar a sessão
     */
    private static function init()
    {
        //VERIFICA SE A SESSÃO NÃO ESTÁ ATIVA
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Método responsável por criar o login do usuário
     * @param User $obUser
     * @return boolean
     */
    public static function login($obUser)
    {

        //INICIA A SESSÃO
        self::init();


        /**
         * DEFINE A SESSÃO DO USUÁRIO
         */
        $_SESSION['user']['usuario'] = [
            'id'    => $obUser->id,
            'nome'  => $obUser->nome,
            'email' => $obUser->email,
        ];

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por executar o logout do uusário
     * @return boolean
     */
    public static function logout()
    {
        //INICIA A SESSÃO
        self::init();

        //DESLOGA  O USUÁRIO
        unset($_SESSION['user']['usuario']);

        //SUCESS
        return true;
    }

    /**
     * Método responsável por verificar se o usuário está logado
     * @return boolean
     */
    public static function isLogged()
    {
        //INICIA A SESSÃO
        self::init();

        //RETORNA A VERIFICAÇÃO
        return isset($_SESSION['user']['usuario']['id']);
    }
}
