<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Plano as EntityPlano;
use \WilliamCosta\DatabaseManager\Pagination;
use \App\Http\Response;

class Plano extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getPlanoItems($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityPlano::getPlanos(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);


        //RESULTADOS DA PAGINA
        $results = EntityPlano::getPlanos(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obPlano = $results->fetchObject(EntityPlano::class)) {
            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/planos/item', [
                'id' => $obPlano->id,
                'titulo' => $obPlano->titulo,
                'descricao' => $obPlano->descricao,
                'valor' => number_format($obPlano->valor,2,".",","),
                'data' => date('d/m/Y H:i:s', strtotime($obPlano->data)),
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
    public static function getPlanos($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/planos/index', [
            'itens' => self::getPlanoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Planos', $content, 'planos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     * @param Request $request
     * @return string
     */
    public static function getNewPlano($request)
    {
        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/planos/form', [
            'title'    => 'Cadastrar plano',
            'titulo'     => '',
            'descricao' => '',
            'valor' => '',
            'status'   => ''
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar depoimento', $content, 'planos');
    }

    /**
     * Método responsável por cadastrar um depoimento no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewPlano($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();

        //NOVA INSTÃNCIA DE DEPOIMENTO
        $obPlano = new EntityPlano;
        $obPlano->titulo = $postVars['titulo'] ?? '';
        $obPlano->descricao = $postVars['descricao'] ?? '';
        $obPlano->valor = $postVars['valor'] ?? '';
        $obPlano->status = 'A';
        $obPlano->recorrente = 'S';
        $obPlano->cadastrar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/planos/' . $obPlano->id . '/edit?status=created');
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
    public static function getEditPlano($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPlano = EntityPlano::getPlanoById($id);

        //VALIDA A INSTANCIA
        if (!$obPlano instanceof EntityPlano) {
            $request->getRouter()->redirect('/admin-panel/planos');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/planos/form', [
            'title'    => 'Editar depoimento',
            'titulo'     => $obPlano->titulo,
            'descricao' => $obPlano->descricao,
            'valor' => $obPlano->valor,
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar plano', $content, 'planos');
    }

    /**
     * Método responsável por gravar a atualizaçõ de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditPlano($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPlano = EntityPlano::getPlanoById($id);

        //VALIDA A INSTANCIA
        if (!$obPlano instanceof EntityPlano) {
            $request->getRouter()->redirect('/admin-panel/planos');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTÂNCIA
        $obPlano->titulo = $postVars['titulo'] ?? $obPlano->titulo;
        $obPlano->descricao = $postVars['descricao'] ?? $obPlano->descricao;
        $obPlano->valor = $postVars['valor'] ?? $obPlano->valor;
        $obPlano->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/planos/' . $obPlano->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeletePlano($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPlano = EntityPlano::getPlanoById($id);

        //VALIDA A INSTANCIA
        if (!$obPlano instanceof EntityPlano) {
            $request->getRouter()->redirect('/admin-panel/planos');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/planos/delete', [
            'nome'     => $obPlano->nome,
            'mensagem' => $obPlano->mensagem
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir depoimento', $content, 'planos');
    }

    /**
     * Método responsável por excluir um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeletePlano($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPlano = EntityPlano::getPlanoById($id);

        //VALIDA A INSTANCIA
        if (!$obPlano instanceof EntityPlano) {
            $request->getRouter()->redirect('/admin-panel/planos');
        }

        //EXCLUIR O DEPOIMENTO
        $obPlano->excluir();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/planos?status=deleted');
    }

    /**
     * Método responsável por retornar o formulário de edição de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getRecursosPlano($request, $id)
    {
        
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPlano = EntityPlano::getPlanoById($id);

        // $obRecursos = EntityPlano::getPlanoRecursos($obPlano->id);

        //RESULTADOS DA PAGINA
        $results = EntityPlano::getRecursos('id_plano = '.$obPlano->id, 'id ASC');

        $itens = '';

        //PERCORRE OS BENEFICIOS DO PLANO
        while ($obPlanoFeat = $results->fetchObject(EntityPlano::class)) {

            $tipo = $obPlanoFeat->slug;

            // Faça algo com os resultados
            $teste[] = [
                $tipo => $obPlanoFeat->value
            ];
        }

        $output = [
            'itens_loja' => $teste[0]['itens_loja'],
            'fotos_itens_loja' => $teste[1]['itens_loja'],
            'descricao' => $teste[2]['descricao'],
            'forma_pagamento' => $teste[3]['forma_pagamento'],
            'contato_vendedor' => $teste[4]['contato_vendedor'],
            'destaque_items_semana' => $teste[5]['destaque_items_semana'],
            'story_dia' => $teste[6]['story_dia']
        ];

        echo json_encode($output);
    }
 
    public static function updateRecursosPlano($request, $id)
    {
        $obPlano = EntityPlano::getPlanoById($id);
        error_log("ID do Plano: " . $id);
            error_log("Dados do Request: " . print_r($request->getPostVars(), true));

        // Verifica se o plano existe
        if (!$obPlano instanceof EntityPlano) {
            return new Response(404, 'Plano não encontrado');
        }

        // Obtém os dados do formulário
        $postVars = $request->getPostVars();

        // Define um array com os recursos que queremos atualizar
        $recursosAtualizados = [
            'itens_loja' => $postVars['itens_loja'] ?? null,
            'descricao' => $postVars['descricao'] ?? null,
            'destaque_items_semana' => $postVars['destaque_items_semana'] ?? null,
            'story_dia' => $postVars['story_dia'] ?? null
        ];

        // Atualiza cada recurso usando o método updateRecurso
        foreach ($recursosAtualizados as $slug => $value) {
            if (!is_null($value)) {
          $success =  EntityPlano::updateRecurso($id, $slug, $value);
                if (!$success) {
                    return false;
                }
            }
        }

        // Retorna uma resposta de sucesso
        return true;
    }

}
