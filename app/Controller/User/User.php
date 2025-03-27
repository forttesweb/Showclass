<?php

namespace App\Controller\User;

use App\Model\Entity\Assinatura as EntityAssinatura;
use App\Model\Entity\User as EntityUser;
use App\Services\Email;
use App\Session\User\Login as SessionUserLogin;
use App\Utils\View;

class User extends Page
{
    /**
     * Método responsável por retornar a renderização da página de login.
     *
     * @param Request $request
     * @param string  $errorMessage
     *
     * @return string
     */
    public static function getNewUser($request, $errorMessage = null)
    {
        // //CONTEUDO DO FURMLÁRIO
        // $content = View::render('account/cadastro', [
        //     'title'    => 'Cadastrar usuário',
        //     'nome'     => '',
        //     'email' => '',
        //     'status'   => self::getStatus($request)
        // ]);

        // //RETORNAR A PÁGINA COMPLETA
        // return parent::getPage('Cadastrar usuário > GUIDEV', $content);
        // STATUS
        // $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        // CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('account/cadastro', [
            'status' => self::getStatus($request),
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPage('Cadastrar usuário', $content);
    }

    /**
     * Método responsável por cadastrar um usuário no banco.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function setNewUser($request)
    {
        // POST VARS
        $postVars = $request->getPostVars();
        // $codigo = substr(uniqid(), 7, 6);

        // Gera um código de 6 dígitos aleatório
        $codigo1 = rand(100000, 999999);

        $nome = $postVars['nome'] ?? '';
        $sobrenome = $postVars['sobrenome'] ?? '';
        $email = $postVars['email'] ?? '';
        $telefone = $postVars['telefone'] ?? '';
        $cep = $postVars['cep'] ?? '';
        $estado = $postVars['estado'] ?? '';
        $endereco = $postVars['endereco'] ?? '';
        $numero = $postVars['numero'] ?? '';
        $bairro = $postVars['bairro'] ?? '';
        $complemento = $postVars['complemento'] ?? '';
        $senha = $postVars['senha'] ?? '';

        $cnpj = $postVars['cnpj'] ?? '';
        $nome_empresa = $postVars['nome_empresa'] ?? '';

        // VALIDA O E-MAIL DO USUÁRIO
        $obUser = EntityUser::getUserByEmail($email);
        if ($obUser instanceof EntityUser) {
            // REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/account/cadastro/?status=duplicated');
        }
        $obUserCn = EntityUser::getUserByCnpj($cnpj);
        if ($obUserCn instanceof EntityUser) {
            // REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/account/cadastro/?status=cnpjduplicated');
        }

        // NOVA INSTÃNCIA DE DEPOIMENTO
        $obUser = new EntityUser();
        $obUser->nome = $nome;
        $obUser->sobrenome = $sobrenome;
        $obUser->email = $email;
        $obUser->telefone = $telefone;
        $obUser->cpf = $cnpj;
        $obUser->cep = $cep;
        $obUser->estado = $estado;
        $obUser->endereco = $endereco;
        $obUser->numero = $numero;
        $obUser->bairro = $bairro;
        $obUser->complemento = $complemento;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->status = 0;
        $obUser->cadastrar();

        $obUser->code = $codigo1;
        $obUser->cadastrar_codigo($obUser->id);

        $nome_novo = strtolower(preg_replace(
            '[^a-zA-Z0-9-]',
            '-',
            strtr(
                utf8_decode(trim($nome_empresa)),
                utf8_decode('áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ'),
                'aaaaeeiooouuncAAAAEEIOOOUUNC-'
            )
        ));
        $nome_url = preg_replace('/[ -]+/', '-', $nome_novo);

        $obUser->cadastrar_loja($obUser->id, $nome_empresa, $nome_url);

        $details = [
            'para' => $email,
            'assunto' => 'Confirme seu cadastro' . getenv('SITE_NAME'),
            'mensagem' => "Obrigado por se cadastrar! confirme o código a seguir no site para validar seu cadastro: $codigo1",
        ];

        // NOVA INSTÃNCIA DE TO-DO
        Email::SendMails($details);
        // Email::build($details);

        // Envia um e-mail de confirmação
        // $assunto = 'Confirme seu cadastro' . getEnv('SITE_NAME');
        // $mensagem = "Obrigado por se cadastrar! confirme o código a seguir no site para validar seu cadastro: $codigo1";
        // $headers = 'From: ShowCLass <' . getEnv('ENVIA_EMAIL') . '>';
        // 'Reply-To: webmaster@example.com' . "\r\n" .
        //     'X-Mailer: PHP/' . phpversion();

        // mail($email, $assunto, $mensagem, $headers);

        // REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/account/confirmar');
        // $request->getRouter()->redirect('/account/cadastro/?status=created');
    }

    /**
     * Método responsável por retornar a renderização da página de login.
     *
     * @param Request $request
     * @param string  $errorMessage
     *
     * @return string
     */
    public static function getConfirmEmail($request, $errorMessage = null)
    {
        // //CONTEUDO DO FURMLÁRIO
        // $content = View::render('account/cadastro', [
        //     'title'    => 'Cadastrar usuário',
        //     'nome'     => '',
        //     'email' => '',
        //     'status'   => self::getStatus($request)
        // ]);

        // //RETORNAR A PÁGINA COMPLETA
        // return parent::getPage('Cadastrar usuário > GUIDEV', $content);
        // STATUS
        // $status = !is_null($errorMessage) ? Alert::getError($errorMessage) : '';

        // CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('account/confirmar', [
            'status' => self::getStatus($request),
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPage('Cadastrar usuário', $content);
    }

    /**
     * Método responsável por retornar a renderização da página de login.
     *
     * @param Request $request
     * @param string  $errorMessage
     *
     * @return string
     */
    public static function getConfirmed($request, $errorMessage = null)
    {
        // CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('account/confirmado', [
            'status' => self::getStatus($request),
        ]);

        // RETORNA A PÁGINA COMPLETA
        return parent::getPage('Cadastro confirmado', $content);
    }

    /**
     * Método responsável por cadastrar um usuário no banco.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function ConfirmEmailUser($request)
    {
        // POST VARS
        $postVars = $request->getPostVars();
        // $codigo = substr(uniqid(), 7, 6);

        // RECEBER OS CÓDIGOS E CONCATENAR PARA VALIDAÇÃO
        // Obtém os valores do código de confirmação enviados pelo formulário
        $code1 = $postVars['code1'];
        $code2 = $postVars['code2'];
        $code3 = $postVars['code3'];
        $code4 = $postVars['code4'];
        $code5 = $postVars['code5'];
        $code6 = $postVars['code6'];

        // Concatena os valores para formar o código completo
        $codigo = $code1 . $code2 . $code3 . $code4 . $code5 . $code6;

        // VALIDA O E-MAIL DO USUÁRIO
        $obCode = EntityUser::getCodeByCode($codigo);

        if (!$obCode instanceof EntityUser) {
            // REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/account/confirmar/?status=error');
        }

        // NOVA INSTÃNCIA DE DEPOIMENTO
        $obUser = new EntityUser();
        // ATUALIZA A INSTÂNCIA
        $obUser->id = $obCode->user_id;
        $obUser->email = $obCode->user_email;
        $obUser->atualizarStatus();

        $data_atual = date("Y-m-d H:i:s");

        $expiration = date('Y-m-d H:i:s', strtotime("+30 days"));

        $data_expiracao = date('Y-m-d H:i:s', strtotime('+30 days', strtotime($data_atual))) . PHP_EOL;

        // Cria a assinatura inicial
        $assinatura = new EntityAssinatura();
        $assinatura->plano = 1;
        $assinatura->user = $obUser->id;
        $assinatura->status = 'PAID';
        $assinatura->metodo = 'FREE';
        $assinatura->expiracao = $expiration;
        $assinatura->cadastrar();

        $details = [
            'titulo' => 'Seja Bem Vindo',
            'nome' => $obUser->nome,
            'para' => $obUser->email,
            'assunto' => 'Bem vindo ao ' . getenv('SITE_NAME'),
            'mensagem' => "Seja bem vindo ao ShowClass! Acesse o site clicando <a target='_blank' href='" . getenv('URL') . "'>aqui</a>",
        ];

        // NOVA INSTÃNCIA DE TO-DO
        Email::build($details);

        // Envia um e-mail de confirmação
        // $assunto = 'Confirme seu cadastro' . getEnv('SITE_NAME');
        // $mensagem = "Obrigado por se cadastrar! confirme o código a seguir no site para validar seu cadastro: $codigo1";
        // $headers = 'From: ShowCLass <' . getEnv('ENVIA_EMAIL') . '>';
        // 'Reply-To: webmaster@example.com' . "\r\n" .
        //     'X-Mailer: PHP/' . phpversion();

        // mail($email, $assunto, $mensagem, $headers);

        // REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/account/confirmado');
        // $request->getRouter()->redirect('/account/cadastro/?status=created');
    }

    /**
     * Método responsável por excluir um depoimento.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function setDeleteUser($request)
    {
        $postVars = $request->getPostVars();
        $id_user = $postVars['id_user'];
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id_user);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/account');
        }

        SessionUserLogin::logout();

        // EXCLUIR O DEPOIMENTO
        $obUser->excluir();

        // REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/account/?status=deleted');
    }

    // /**
    //  * Método responsável por deslogar o usuário
    //  * @@param Request $request
    //  */
    // public static function setLogout($request)
    // {
    //     //DESTROI A SESSÃO DE LOGIN
    //     SessionUserLogin::logout();

    //     //REDIRECIONA O USUÁRIO PARA TELA DE LOGIn
    //     $request->getRouter()->redirect('/account/login');
    // }

    /**
     * Método responsável por retornar a mensagem de status.
     *
     * @param Request $request
     *
     * @return string
     */
    private static function getStatus($request)
    {
        // QUERY PARAMS
        $queryParams = $request->getQueryParams();

        // STATUS
        if (!isset($queryParams['status'])) {
            return '';
        }

        // MENSAGENS DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Cadastro realizado com sucesso! Um e-mail de confirmação foi enviado para o seu endereço de e-mail. Por favor, verifique a sua caixa de entrada e siga as instruções para confirmar o seu cadastro.');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluido com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O e-mail digitado já está sendo digitado por outro usuário.');
                break;
            case 'cnpjduplicated':
                return Alert::getError('O cnpj digitado já está sendo digitado por outro usuário.');
                break;
            case 'error':
                return Alert::getError('O código inserido está incorreto.');
                break;
        }
    }
}
