<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getTestimonyItems($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);


        //RESULTADOS DA PAGINA
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/testimonies/item', [
                'id' => $obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obTestimony->data)),
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a vie de listagem de depoimentos
     *
     * @param Request $request
     * @return string
     */
    public static function getTestimonies($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/testimonies/index', [
            'itens' => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('DEPOIMENTOS', $content, 'testimonies');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     * @param Request $request
     * @return string
     */
    public static function getNewTestimony($request)
    {
        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/testimonies/form', [
            'title'    => 'Cadastrar depoimento',
            'nome'     => '',
            'mensagem' => '',
            'status'   => ''
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar depoimento asasd', $content, 'testimonies');
    }

    /**
     * Método responsável por cadastrar um depoimento no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewTestimony($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //NOVA INSTÃNCIA DE DEPOIMENTO
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'] ?? '';
        $obTestimony->mensagem = $postVars['mensagem'] ?? '';
        $obTestimony->cadastrar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/testimonies/' . $obTestimony->id . '/edit?status=created');
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
                return Alert::getSuccess('Depoimento criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Depoimento excluido com sucesso!');
                break;
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditTestimony($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin-panel/testimonies');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/testimonies/form', [
            'title'    => 'Editar depoimento',
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar depoimento', $content, 'testimonies');
    }

    /**
     * Método responsável por gravar a atualizaçõ de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditTestimony($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin-panel/testimonies');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTÂNCIA
        $obTestimony->nome = $postVars['nome'] ?? $obTestimony->nome;
        $obTestimony->mensagem = $postVars['mensagem'] ?? $obTestimony->mensagem;
        $obTestimony->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/testimonies/' . $obTestimony->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteTestimony($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin-panel/testimonies');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/testimonies/delete', [
            'nome'     => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir depoimento', $content, 'testimonies');
    }

    /**
     * Método responsável por excluir um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteTestimony($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA A INSTANCIA
        if (!$obTestimony instanceof EntityTestimony) {
            $request->getRouter()->redirect('/admin-panel/testimonies');
        }

        //EXCLUIR O DEPOIMENTO
        $obTestimony->excluir();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/testimonies?status=deleted');
    }
}
