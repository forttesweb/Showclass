<?php

namespace App\Controller\User;

use \App\Utils\View;
use \App\Model\Entity\User;
use \App\Session\User\Login as SessionUserLogin;
use Google\Client as GoogleClient;
use \App\Services\Email;

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
        $content = View::render('account/login', [
            'status' => $status,
            'statusacc' => self::getStatus($request)
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Login', $content);
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
        $obUser = User::getUserByEmail($email);


        if (!$obUser instanceof User) {
            return self::getLogin($request, 'E-mail ou senha inválidos');
        }

        //VERIFICA A SENHA DO USUÁRIO
        if (!password_verify($senha, $obUser->senha)) {
            return self::getLogin($request, 'E-mail ou senha inválidos');
        }

        //VERIFICA A SENHA DO USUÁRIO
        if ($obUser->status == 0) {
            return self::getLogin($request, 'Para logar, é necessário confirmar<br> seu e-mail.');
        }

        $user_token = md5(uniqid());

        $user_login_status = 'Login';

        $obUser->user_token = $user_token;
        $obUser->user_login_status = $user_login_status;
        // $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizarLoginStatus();

        //CRIAR A SESSÃO DE LOGIN
        SessionUserLogin::login($obUser);

        //REDIRECIONA O USUÁRIO PARA HOME DO ADMIN
        $request->getRouter()->redirect('/account');
    }

    /**
     * Método responsável por deslogar o usuário
     * @@param Request $request
     */
    public static function setLogout($request)
    {
        //DESTROI A SESSÃO DE LOGIN
        SessionUserLogin::logout();

        //REDIRECIONA O USUÁRIO PARA TELA DE LOGIn
        $request->getRouter()->redirect('/account/login');
    }

    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     */
    public static function setLoginGoogle($request)

    {

        //POST VARS
        $postVars = $request->getPostVars();

        //VERIFICA CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['credential']) || !isset($postVars['g_csrf_token'])) {
            return self::getLogin($request, 'Conta do google inválida');
        }

        //COOKIES
        $cookie = $_COOKIE['g_csrf_token'] ?? '';

        if ($postVars['g_csrf_token'] != $cookie) {
            return self::getLogin($request, 'Token google inválido');
        }


        //VALIDAÇÃO SECUNDÁRIA DO TOKEN


        // Get $id_token via HTTPS POST.

        //INSTÂNCIA DO CLIENTE GOOGLE
        $client = new GoogleClient(['client_id' => '782758127217-8g475iqhal5rd82l860cgqaoe4sfbi55.apps.googleusercontent.com']);  // Specify the CLIENT_ID of the app that accesses the backend

        //OBTEM OS DADOS DO USUARIO COM BASE NO JWT
        $payload = $client->verifyIdToken($postVars['credential']);

        //VERIFICA OS DADOS DO PAYLOAD
        if (isset($payload['email'])) {

            //BUSCA O USUÁRIO PELO E-MAIL
            $obUser = User::getUserByEmail($payload['email']);

            if (!$obUser instanceof User) {
                // return self::getLogin($request, 'E-mail ou senha inválidos');

                //NOVA INSTÃNCIA DE DEPOIMENTO
                $obUser        = new User;
                $obUser->nome  = $payload['given_name'];
                $obUser->sobrenome  = $payload['family_name'];
                $obUser->email = $payload['email'];
                $obUser->telefone = '';
                $obUser->cep = '';
                $obUser->estado = '';
                $obUser->endereco = '';
                $obUser->numero = '';
                $obUser->bairro = '';
                $obUser->complemento = '';
                $obUser->senha = '';
                $obUser->status = 1;
                $obUser->cadastrar();


                $nome_novo = strtolower(preg_replace(
                    "[^a-zA-Z0-9-]",
                    "-",
                    strtr(
                        utf8_decode(trim($payload['given_name'])),
                        utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"),
                        "aaaaeeiooouuncAAAAEEIOOOUUNC-"
                    )
                ));
                $nome_url = preg_replace('/[ -]+/', '-', $nome_novo);

                $obUser->cadastrar_loja($obUser->id, $nome_url);
            }

            $user_token = md5(uniqid());

            $user_login_status = 'Login';

            $obUser->user_token = $user_token;
            $obUser->user_login_status = $user_login_status;
            // $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
            $obUser->atualizarLoginStatus();

            //CRIAR A SESSÃO DE LOGIN
            SessionUserLogin::login($obUser);

            //REDIRECIONA O USUÁRIO PARA HOME DO ADMIN
            $request->getRouter()->redirect('/account');

            // echo "<pre>";
            // print_r($payload);
            // echo "</pre>";
            // exit;
        }

        // echo "<pre>";
        // print_r($cookie);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($postVars);
        // echo "</pre>";
        // exit;


        // $email = $postVars['email'] ?? '';
        // $senha = $postVars['senha'] ?? '';







        // if (!$obUser instanceof User) {
        //     return self::getLogin($request, 'E-mail ou senha inválidos');
        // }

        // //VERIFICA A SENHA DO USUÁRIO
        // if (!password_verify($senha, $obUser->senha)) {
        //     return self::getLogin($request, 'E-mail ou senha inválidos');
        // }

        // //VERIFICA A SENHA DO USUÁRIO
        // if ($obUser->status == 0) {
        //     return self::getLogin($request, 'Para logar, é necessário confirmar<br> seu e-mail.');
        // }
    }
    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getSenha($request, $errorMessage = null)
    {
        //STATUS
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('account/recuperar-senha', [
            'status' => $status
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Recuperar senha', $content);
    }


    /**
     * Método responsável por definir o login do usuário
     * @param Request $request
     */
    public static function setSenha($request)

    {

        //POST VARS
        $postVars = $request->getPostVars();
        $email = $postVars['email'] ?? '';

        
        //BUSCA O USUÁRIO PELO E-MAIL
        $obUser = User::getUserByEmail($email);


        if (!$obUser instanceof User) {
            return self::getLogin($request, 'E-mail ou senha inválidos');
        }

        $token = bin2hex(random_bytes(32));

        //NOVA INSTÃNCIA DE DEPOIMENTO
        $obUser        = new User;
        $obUser->token = $token;
        $obUser->email = $email;
        $obUser->cadastrar_token();

        $details = [
            'titulo' => 'Recuperação de senha',
            'para' => $email,
            'assunto' => 'Recuperação de senha - ' . getEnv('SITE_NAME'),
            'mensagem' => 'Clique no link abaixo para redefinir sua senha. <br> <a href="' . getEnv("URL") . '/account/nova-senha/?token=' . $token . '">Redefinir senha</a>',
            'nome' => $obUser->nome
        ];

        //NOVA INSTÃNCIA DE TO-DO
        Email::SendMails($details);

        //VERIFICA A SENHA DO USUÁRIO
        // if ($obUser->status == 0) {
        //     return self::getLogin($request, 'Para logar, é necessário confirmar<br> seu e-mail.');
        // }

        //REDIRECIONA O USUÁRIO PARA HOME DO ADMIN
        // $request->getRouter()->redirect('/account');
    }



    /**
     * Método responsável por retornar a renderização da página de login
     * @param Request $request
     * @param string $errorMessage
     * @return string
     */
    public static function getNovaSenha($request, $errorMessage = null)
    {

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $token = $queryParams['token'];

        $data_atual = date('H:i:s');



        $obUser = User::getToken($token);

        $data_criacao = strtotime("$obUser->data_criacao + 15 minutes");

        // $temp = strtotime("+15 minutes", $data_criacao);
        $horaNovaFormatada = date("H:i:s",$data_criacao);


        if ($horaNovaFormatada < $data_atual) {
            return self::getLogin($request, 'Token expirado');
        }

        if (!$obUser instanceof User) {
            return self::getLogin($request, 'Token inválido');
        }



        //STATUS
        $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        //CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('account/nova-senha', [
            'email' => $obUser->email,
            'status' => $status
        ]);

        //RETORNA A PÁGINA COMPLETA
        return parent::getPage('Nova senha', $content);
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setNovaSenha($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $email    = $postVars['email'] ?? '';
        $senha    = $postVars['senha'] ?? '';

        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = User::getUserByEmail($email);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof User) {
            $request->getRouter()->redirect('/account/login');
        }

        

        // //VALIDA O EMAIL DO USUÁRIO
        // $obUserEmail = User::getUserByEmail($email);
        // if ($obUserEmail instanceof User && $obUserEmail->id != $id) {
        //     //REDIRECIONA  O USUARIO
        //     $request->getRouter()->redirect('/account/login/?status=duplicated');
        // }

        //ATUALIZA A INSTÂNCIA
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/account/login/?status=updated');
    }

    /**

     * Método responsável por retornar a mensagem de status

     *

     * @param Request $request

     * @return string

     */

     private static function getStatus($request)

     {
 
         //QUERY PARAMS
 
         $queryParams = $request->getQueryParams();
 
 
 
         //STATUS
 
         if (!isset($queryParams['status'])) return '';
 
 
 
         //MENSAGENS DE STATUS
 
         switch ($queryParams['status']) {
 
             case 'created':
 
                 return Alert::getSuccess('Usuário criado com sucesso!');
 
                 break;
 
             case 'updated':
 
                 return Alert::getSuccess('Senha atualizada com sucesso');
 
                 break;
 
             case 'deleted':
 
                 return Alert::getSuccess('Usuário excluido com sucesso!');
 
                 break;
 
             case 'duplicated':
 
                 return Alert::getError('O e-mail digitado já está sendo digitado por outro usuário.');
 
                 break;
         }
     }
}
