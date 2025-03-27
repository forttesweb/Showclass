<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Admin;
use \App\Session\Admin\Login as SessionAdminLogin;

class Login extends Page
{

    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getLogin($request, $errorMessage = null)
    {
        //STATUS
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('admin/login', [
            'status' => $status
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('LOGIN', $content);
    }

    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     */
    public static function setLogin($request)

    {
        
        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        
        //BUSCA O USUÁRIO PELO E-MAIL
        $obUser = Admin::getAdminByEmail($email);


        if (!$obUser instanceof Admin) {
            return self::getLogin($request, 'E-mail1212 ou senha inválidos');
        }

        //VERIFICA A SENHA DO USUÁRIO
        if (!password_verify($senha, $obUser->senha)) {
            return self::getLogin($request, 'E-mail ou senha inválidos');
        }

        //CRIAR A SESSÃO DE LOGIN
        SessionAdminLogin::login($obUser);

        //REDIRECIONA O USUÁRIO PARA HOME DO ADMIN
        $request->getRouter()->redirect('/admin-panel');
    }

    /**
     * Método responsável por deslogar o usuário
     * @@param Request $request
     */
    public static function setLogout($request)
    {
        //DESTROI A SESSÃO DE LOGIN
        SessionAdminLogin::logout();

        //REDIRECIONA O USUÁRIO PARA TELA DE LOGIn
        $request->getRouter()->redirect('/admin-panel/login');
    }
}
