<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Loja as EntityLoja;
use \App\Model\Entity\User as EntityUser;
use \App\Model\Entity\Announce as EntityAnnounce;
use EnchantDictionary;
use \WilliamCosta\DatabaseManager\Pagination;

class Loja extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de usuarios para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getLojasItems($request, &$obPagination)
    {
        //USUARIOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityLoja::getLojas(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityLoja::getLojas(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obLoja = $results->fetchObject(EntityLoja::class)) {


            $obUser = EntityUser::getUserById($obLoja->id_user);

            //QUANTIDADE TOTAL DE REGISTROS
            $quantidadetotal = EntityAnnounce::getAnuncios('id_store = ' . $obLoja->id, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            $link_loja = getEnv('URL') . '/anuncio/' . $obLoja->nome_url;

            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/lojas/item', [
                'id' => $obLoja->id,
                'nome_loja' => ucfirst($obLoja->nome_loja),
                'nome' => $obUser->nome,
                'cidade' => $obLoja->cidade,
                'estado' => $obLoja->estado,
                'qtd_anuncios' => $quantidadetotal,
                'link_loja' => $link_loja
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
    public static function getLojas($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/lojas/index', [
            'itens' => self::getLojasItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Lojas', $content, 'lojas');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo usuario
     *
     * @param Request $request
     * @return string
     */
    public static function getNewLoja($request)
    {
        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/lojas/form', [
            'title'    => 'Cadastrar usuário',
            'nome'     => '',
            'email' => '',
            'status'   => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar usuário', $content, 'lojas');
    }

    /**
     * Método responsável por cadastrar um usuário no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewLoja($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDA O E-MAIL DO USUÁRIO
        $obLoja = EntityLoja::getLojaByEmail($email);
        if ($obLoja instanceof EntityLoja) {
            //REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/admin-panel/lojas/new?status=duplicated');
        }


        //NOVA INSTÃNCIA DE DEPOIMENTO
        $obLoja        = new EntityLoja;
        $obLoja->nome  = $nome;
        $obLoja->email = $email;
        $obLoja->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obLoja->cadastrar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/lojas/' . $obLoja->id . '/edit?status=created');
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
    public static function getEditLoja($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obLoja = EntityLoja::getLojaById($id);

        //VALIDA A INSTANCIA
        if (!$obLoja instanceof EntityLoja) {
            $request->getRouter()->redirect('/admin-panel/lojas');
        }


        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/lojas/form', [
            'title'    => 'Editar usuário',
            'nome'     => $obLoja->nome,
            'email'    => $obLoja->email,
            'status'   => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar usuário', $content, 'lojas');
    }

    /**
     * Método responsável por gravar a atualização de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditLoja($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obLoja = EntityLoja::getLojaById($id);

        //VALIDA A INSTANCIA
        if (!$obLoja instanceof EntityLoja) {
            $request->getRouter()->redirect('/admin-panel/lojas');
        }

        //POST VARS
        $postVars = $request->getPostVars();
        $nome     = $postVars['nome']  ?? '';
        $email    = $postVars['email'] ?? '';
        $senha    = $postVars['senha'] ?? '';

        //VALIDA O EMAIL DO USUÁRIO
        $obLojaEmail = EntityLoja::getLojaByEmail($email);
        if ($obLojaEmail instanceof EntityLoja && $obLojaEmail->id != $id) {
            //REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/admin-panel/lojas/' . $id . '/edit?status=duplicated');
        }

        //ATUALIZA A INSTÂNCIA
        $obLoja->nome = $nome;
        $obLoja->email = $email;
        $obLoja->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obLoja->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/lojas/' . $obLoja->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteLoja($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obLoja = EntityLoja::getLojaById($id);

        //VALIDA A INSTANCIA
        if (!$obLoja instanceof EntityLoja) {
            $request->getRouter()->redirect('/admin-panel/lojas');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/lojas/delete', [
            'nome'     => $obLoja->nome,
            'email'    => $obLoja->email
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir usuário', $content, 'lojas');
    }

    /**
     * Método responsável por excluir um usuário
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteLoja($request, $id)
    {
        //OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obLoja = EntityLoja::getLojaById($id);

        //VALIDA A INSTANCIA
        if (!$obLoja instanceof EntityLoja) {
            $request->getRouter()->redirect('/admin-panel/lojas');
        }

        //EXCLUIR O USUÁRIO
        $obLoja->excluir();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/lojas?status=deleted');
    }
}
