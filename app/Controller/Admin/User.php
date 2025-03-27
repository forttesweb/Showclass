<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use EnchantDictionary;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de usuarios para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUsersItems($request, &$obPagination)
    {
        //USUARIOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityUser::getUsers(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obUser = $results->fetchObject(EntityUser::class)) {
            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/users/item', [
                'id' => $obUser->id,
                'nome' => $obUser->nome,
                'email' => $obUser->email
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a view de listagem de usuarios
     *
     * @param Request $request
     * @return string
     */
    public static function getUsers($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/users/index', [
            'itens' => self::getUsersItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Usuários', $content, 'users');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo usuario
     *
     * @param Request $request
     * @return string
     */
    public static function getNewUser($request)
    {
        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/users/form', [
            'title'    => 'Cadastrar usuário',
            'nome'     => '',
            'email' => '',
            'status'   => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar usuário', $content, 'users');
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewUser($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÁRIO
        $obUser = EntityUser::getUserByEmail($email);
        if ($obUser instanceof EntityUser) {
            //REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/admin-panel/users/new?status=duplicated');
        }


        //NOVA INSTÃNCIA DE DEPOIMENTO
        $obUser        = new EntityUser;
        $obUser->nome  = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->cadastrar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/users/' . $obUser->id . '/edit?status=created');
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
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluido com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O e-mail digitado já está sendo digitado por outro usuário.');
                break;
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditUser($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin-panel/users');
        }


        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/users/form', [
            'title'    => 'Editar usuário',
            'nome'     => $obUser->nome,
            'email'    => $obUser->email,
            'status'   => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar usuário', $content, 'users');
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditUser($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin-panel/users');
        }

        //POST VARS
        $postVars = $request->getPostVars();
        $nome     = $postVars['nome']  ?? '';
        $email    = $postVars['email'] ?? '';
        $senha    = $postVars['senha'] ?? '';

        //VALIDA O EMAIL DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($email);
        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) {
            //REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/admin-panel/users/'.$id.'/edit?status=duplicated');
        }

        //ATUALIZA A INSTÂNCIA
        $obUser->nome = $nome;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/users/' . $obUser->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteUser($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin-panel/users');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/users/delete', [
            'nome'     => $obUser->nome,
            'email'    => $obUser->email
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir usuário', $content, 'users');
    }

    /**
     * Método responsável por excluir um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteUser($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id);

        //VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/admin-panel/users');
        }

        //EXCLUIR O USUÁRIO
        $obUser->excluir();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/users?status=deleted');
    }
}
