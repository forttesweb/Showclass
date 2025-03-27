<?php

namespace App\Controller\Api;

class Api {

    /**
     * Método responsável por retornar os detalhes da API
     *
     * @param Request $request
     * @return array
     */
    public static function getDetails($request){
        return [
            'nome'   => 'API - GUIDEV',
            'versao' => 'v1.0.0',
            'autor'  => 'Guilherme Dev',
            'email'  => 'guilherme.mayrink@outlook.com'
        ];
    }

    /**
     * Método responsável por retornar os detalhes da paginação
     *
     * @param Request $request
     * @param Pagination $obPagination
     * @return array
     */
    protected static function getPagination($request, $obPagination){
        //QUERY PARAMS
        $queryParams = $request->getQueryparams();

        //PAGINA
        $pages = $obPagination->getPages();

        //RETORNO
        return [
            'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'quantidadedePaginas' => !empty($pages) ? count($pages) : 1
        ];
    }

}