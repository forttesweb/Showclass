<?php

namespace App\Controller\Api;

use \App\Model\Entity\Testimony as EntityTestimony;
use \WilliamCosta\DatabaseManager\Pagination;

class Testimony extends Api
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
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityTestimony::getTestimonies(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 3);


        //RESULTADOS DA PAGINA
        $results = EntityTestimony::getTestimonies(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obTestimony = $results->fetchObject(EntityTestimony::class)) {
            //VIEW DE DEPOIMENTOS
            $itens[] = [
                'id' => (int)$obTestimony->id,
                'nome' => $obTestimony->nome,
                'mensagem' => $obTestimony->mensagem,
                'data' => $obTestimony->data,
            ];
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por retornar os depoimentos cadastrados
     *
     * @param Request $request
     * @return array
     */
    public static function getTestimonies($request)
    {
        return [
            'depoimentos'   => self::getTestimonyItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os detalhes de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return array
     */
    public static function getTestimony($request, $id) {
        //VALIDA O ID DO DEPOIMENTO
        if(!is_numeric($id)) {
            throw new \Exception("O id '".$id."' não é válido.", 400);
        }

        //BUSCA DEPOIMENTO
        $obTestimony = EntityTestimony::getTestimonyById($id);

        //VALIDA SE O DEPOIMENTO EXISTE
        if(!$obTestimony instanceof EntityTestimony){
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }

        //RETORNA OS DETALHES DO DEPOIMENTO
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data,
        ];
    }

    /**
     * Método responsável por cadastrar um novo depoimento
     *
     * @param Request $request
     * @return void
     */
    public static function setNewTestimony($request){
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['mensagem'])) {
            throw new \Exception("Os campos 'nome' e 'mensagem' não obrigatórios", 400);
            
        }

        //NOVO DEPOIMENTO
        $obTestimony = new EntityTestimony;
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->cadastrar();
        
        //RETORNA OS DETALHES DO DEPOIMENTO CADASTRADO
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data,
        ];
    }

    /**
     * Método responsável por atualizar um novo depoimento
     *
     * @param Request $request
     * @return void
     */
    public static function setEditTestimony($request, $id){
        //POST VARS
        $postVars = $request->getPostVars();

        //VALIA OS CAMPOS OBRIGATÓRIOS
        if(!isset($postVars['nome']) or !isset($postVars['mensagem'])) {
            throw new \Exception("Os campos 'nome' e 'mensagem' não obrigatórios", 400);
        }

        //BUSCA O DEPOIMENTO NO BANCO
        $obTestimony = EntityTestimony::getTestimonyById($id);
        if(!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }

        //ATUALIZA O DEPOIMENTO
        $obTestimony->nome = $postVars['nome'];
        $obTestimony->mensagem = $postVars['mensagem'];
        $obTestimony->atualizar();
        
        //RETORNA OS DETALHES DO DEPOIMENTO ATUALIZADO
        return [
            'id' => (int)$obTestimony->id,
            'nome' => $obTestimony->nome,
            'mensagem' => $obTestimony->mensagem,
            'data' => $obTestimony->data,
        ];
    }

    /**
     * Método responsável por excluir um novo depoimento
     *
     * @param Request $request
     * @return void
     */
    public static function setDeleteTestimony($request, $id){

        //BUSCA O DEPOIMENTO NO BANCO
        $obTestimony = EntityTestimony::getTestimonyById($id);
        if(!$obTestimony instanceof EntityTestimony) {
            throw new \Exception("O depoimento ".$id." não foi encontrado", 404);
        }

        //EXCLUIR O DEPOIMENTO
        $obTestimony->excluir();
        
        //RETORNA O SUCESSO DA EXCLUSÃO
        return [
            'sucesso' => true
        ];
    }
}
