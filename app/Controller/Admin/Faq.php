<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Faq as EntityFaq;
use \WilliamCosta\DatabaseManager\Pagination;

class Faq extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de faqs para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getFaqItems($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityFaq::getFaqs(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);


        //RESULTADOS DA PAGINA
        $results = EntityFaq::getFaqs(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obFaq = $results->fetchObject(EntityFaq::class)) {
            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/faqs/item', [
                'id' => $obFaq->id,
                'nome' => $obFaq->nome,
                'mensagem' => $obFaq->mensagem,
                'data' => date('d/m/Y H:i:s', strtotime($obFaq->data)),
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a vie de listagem de faqs
     *
     * @param Request $request
     * @return string
     */
    public static function getFaqs($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/faqs/index', [
            'itens' => self::getFaqItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('FAQs', $content, 'faqs');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo faq
     *
     * @param Request $request
     * @return string
     */
    public static function getNewFaq($request)
    {
        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/faqs/form', [
            'title'    => 'Cadastrar faq',
            'nome'     => '',
            'mensagem' => '',
            'status'   => ''
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar faq', $content, 'faqs');
    }

    /**
     * Método responsável por cadastrar um faq no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewFaq($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //NOVA INSTÃNCIA DE DEPOIMENTO
        $obFaq = new EntityFaq;
        $obFaq->nome = $postVars['nome'] ?? '';
        $obFaq->mensagem = $postVars['mensagem'] ?? '';
        $obFaq->cadastrar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/faqs/' . $obFaq->id . '/edit?status=created');
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
                return Alert::getSuccess('FAQ criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('FAQ atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('FAQ excluido com sucesso!');
                break;
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um faq
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditFaq($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obFaq = EntityFaq::getFaqById($id);

        //VALIDA A INSTANCIA
        if (!$obFaq instanceof EntityFaq) {
            $request->getRouter()->redirect('/admin-panel/faqs');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/faqs/form', [
            'title'    => 'Editar faq',
            'nome'     => $obFaq->nome,
            'mensagem' => $obFaq->mensagem,
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar faq', $content, 'faqs');
    }

    /**
     * Método responsável por gravar a atualizaçõ de um faq
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditFaq($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obFaq = EntityFaq::getFaqById($id);

        //VALIDA A INSTANCIA
        if (!$obFaq instanceof EntityFaq) {
            $request->getRouter()->redirect('/admin-panel/faqs');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTÂNCIA
        $obFaq->nome = $postVars['nome'] ?? $obFaq->nome;
        $obFaq->mensagem = $postVars['mensagem'] ?? $obFaq->mensagem;
        $obFaq->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/faqs/' . $obFaq->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um faq
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteFaq($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obFaq = EntityFaq::getFaqById($id);

        //VALIDA A INSTANCIA
        if (!$obFaq instanceof EntityFaq) {
            $request->getRouter()->redirect('/admin-panel/faqs');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/faqs/delete', [
            'nome'     => $obFaq->nome,
            'mensagem' => $obFaq->mensagem
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir faq', $content, 'faqs');
    }

    /**
     * Método responsável por excluir um faq
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteFaq($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obFaq = EntityFaq::getFaqById($id);

        //VALIDA A INSTANCIA
        if (!$obFaq instanceof EntityFaq) {
            $request->getRouter()->redirect('/admin-panel/faqs');
        }

        //EXCLUIR O DEPOIMENTO
        $obFaq->excluir();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/faqs?status=deleted');
    }
}
