<?php

namespace App\Controller\Pages;

use App\Model\Entity\Announce as EntityAnnounce;
use App\Model\Entity\Categoria as EntityCategoria;
use App\Model\Entity\Store as EntityStore;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class Search extends Page
{
    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre.
     *
     * @return string
     */
    public static function getSearchItems($request, &$obPagination)
    {
        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();

        $paginaAtual = $queryParams['page'] ?? 1;

        $termo = $queryParams;

        $term = $queryParams['term'];
        $uf = $queryParams['uf'];
        $filtro_municipio = @$queryParams['filtro_municipio'];

        // $nometermo = key($term);
        if (!empty($term) && empty($uf) && empty($filtro_municipio)) {
            $termexplod = explode(' ', $term);

            $termimplode = implode('-', $termexplod);

            // if ($nometermo == 'marca') {
            // $nome_marca = $term['marca'];

            // QUANTIDADE TOTAL DE REGISTROS
            $quantidadetotal = EntityAnnounce::getAnuncios('nome_marca REGEXP "'.$term.'"' or 'titulo REGEXP "'.$term.'" or titulo REGEXP "'.$termimplode.'"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            // PAGINA ATUAL

            // INSTANCIA DE PAGINACAO
            $obPagination = new Pagination($quantidadetotal, $paginaAtual, 12);

            $results = EntityAnnounce::getAnuncios("nome_marca LIKE '%$term%' or titulo LIKE '%$term%' 
            or titulo LIKE '%$termimplode%' or categoria LIKE '%$term%' or cor LIKE '%$term%'", 'id DESC', 12);

            // if ($quantidadetotal == 0) {

            //     $busca_categoria = EntityCategoria::getCategorias('nome REGEXP "' . $term . '"', null, null)->fetchObject();

            //     $results = EntityAnnounce::getAnuncios('categoria = "' . $busca_categoria->nome_url . '"', 'id DESC', $obPagination->getLimit());
            // }
        } elseif (empty($term) && !empty($uf) && empty($filtro_municipio)) {
            $quantidadetotal = EntityAnnounce::getAnuncios('nome_marca REGEXP "'.$term.'"' or 'titulo REGEXP "'.$term.'"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            $obPagination = new Pagination($quantidadetotal, $paginaAtual, 12);

            // $results_estado = EntityStore::getStores('estado = "'.$uf.'"', null, null);

            // $id_store = [];
            // foreach ($results_estado as $adhuasd) {
            //     $id_store[] = $adhuasd['id'];
            // }
            $results = EntityAnnounce::getAnuncios('id_store IN (SELECT id FROM usuarios_lojas WHERE estado ="'.$uf.'")', 'id DESC', null);
        } elseif (empty($term) && !empty($uf) && !empty($filtro_municipio)) {
            $quantidadetotal = EntityAnnounce::getAnuncios('nome_marca REGEXP "'.$term.'"' or 'titulo REGEXP "'.$term.'"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            $obPagination = new Pagination($quantidadetotal, $paginaAtual, 12);

            $results = EntityAnnounce::getAnuncios('id_store IN (SELECT id FROM usuarios_lojas WHERE estado = "'.$uf.'" AND cidade = "'.$filtro_municipio.'")', 'id DESC', null);
        } elseif (!empty($term) && !empty($uf) && !empty($filtro_municipio)) {
            $quantidadetotal = EntityAnnounce::getAnuncios('nome_marca REGEXP "'.$term.'"' or 'titulo REGEXP "'.$term.'"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            $obPagination = new Pagination($quantidadetotal, $paginaAtual, 12);

            $results = EntityAnnounce::getAnuncios('nome_marca LIKE "%'.$term.'%" or titulo LIKE "%'.$term.'%" AND id_store IN (SELECT id FROM usuarios_lojas WHERE estado = "'.$uf.'" AND cidade = "'.$filtro_municipio.'")', 'id DESC', null);
        } else {
            // QUANTIDADE TOTAL DE REGISTROS
            $quantidadetotal = EntityAnnounce::getAnuncios(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            // PAGINA ATUAL
            $queryParams = $request->getQueryParams();
            $paginaAtual = $queryParams['page'] ?? 1;

            // INSTANCIA DE PAGINACAO
            $obPagination = new Pagination($quantidadetotal, $paginaAtual, 12);

            // RESULTADOS DA PAGINA
            $results = EntityAnnounce::getAnuncios(null, 'id DESC', $obPagination->getLimit());
        }

        // DEPOIMENTOS
        $itens = '';

        // if (!empty($term)) {
        //     //QUANTIDADE TOTAL DE REGISTROS
        //     $quantidadetotal = EntityAnnounce::getAnuncios('titulo REGEXP "' . $term . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //     //PAGINA ATUAL
        //     $queryParams = $request->getQueryParams();
        //     $paginaAtual = $queryParams['page'] ?? 1;

        //     //INSTANCIA DE PAGINACAO
        //     $obPagination = new Pagination($quantidadetotal, $paginaAtual, 12);

        //     $quantidadetotal = EntityAnnounce::getAnuncios('titulo REGEXP "' . $term . '" OR categoria REGEXP "' . $term . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //     if ($quantidadetotal == 0) {

        //         $busca_categoria = EntityCategoria::getCategorias('nome REGEXP "' . $term . '"', null, null)->fetchObject();

        //         $results = EntityAnnounce::getAnuncios('categoria = "' . $busca_categoria->nome_url . '"', 'id DESC', $obPagination->getLimit());
        //     } else {

        //         //RESULTADOS DA PAGINA
        //         $results = EntityAnnounce::getAnuncios('titulo REGEXP "' . $term . '" OR categoria REGEXP "' . $term . '"', 'id DESC', $obPagination->getLimit());
        //     }
        // } else {
        //     //QUANTIDADE TOTAL DE REGISTROS
        //     $quantidadetotal = EntityAnnounce::getAnuncios(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //     //PAGINA ATUAL
        //     $queryParams = $request->getQueryParams();
        //     $paginaAtual = $queryParams['page'] ?? 1;

        //     //INSTANCIA DE PAGINACAO
        //     $obPagination = new Pagination($quantidadetotal, $paginaAtual, 12);

        //     //RESULTADOS DA PAGINA
        //     $results = EntityAnnounce::getAnuncios(null, 'id DESC', $obPagination->getLimit());
        // }

        // RENDERIZA O ITEM
        while ($obAccounces = $results->fetchObject(EntityAnnounce::class)) {
            // RESULTADOS DA PAGINA
            $obFoto = EntityAnnounce::getAnnounceFotoById($obAccounces->id);

            // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
            $obStore = EntityStore::getStoreById($obAccounces->id_store);
            $id_store = $obStore->id;

            $nome_marca = $obAccounces->nome_marca;
            $nome_modelo = $obAccounces->nome_modelo;

            $teexte = explode(' ', $nome_marca);
            $teexte2 = explode(' ', $nome_modelo);

            $nome_marca1 = $teexte[0];
            $nome_modelo1 = $teexte2[0];

            $titulooooo = $nome_marca.' '.$nome_modelo1;

            // VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/busca/item', [
                'nome_url' => $obStore->nome_url,
                'titulo' => $titulooooo,
                'nome_modelo' => $nome_modelo,
                'categoria' => $obAccounces->categoria,
                'preco' => $obAccounces->valor,
                // 'preco' => number_format($obAccounces->valor, 2, ',', '.'),
                'url' => $obAccounces->url,
                'foto' => $obFoto->foto_01,
            ]);
        }
        if ($itens == '' || empty($itens)) {
            $itens = 'Sem veículos cadastrados com esses parametros';
        }

        return $itens;
        // $postVars = $request->getPostVars();

        // echo "<pre>"; print_r($busca); echo "</pre>"; exit;

        // $obOrganization = new Organization;

        // //VIEW DA HOME
        // $content = View::render('pages/about');
        // //RETORNA A VIEW DA PÁGINA
        // return parent::getPage('Sobre', $content);
    }

    /**
     * Método responsável por retornar o conteúdo (view) da home.
     *
     * @return string
     */
    public static function getSearch($request, $busca)
    {
        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();

        $term = $queryParams['term'];
        $nada = '';

        // if (!empty($term)) {
        //     // QUANTIDADE TOTAL DE REGISTROS
        //     $quantidadetotal = EntityAnnounce::getAnuncios('titulo REGEXP "'.$term.'" OR categoria REGEXP "'.$term.'"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //     if ($quantidadetotal == 0) {
        //         $nada = '<h3>Nenhum anuncio encontrado</h3>';
        //     }
        // }

        // $results = EntityCategoria::getCategorias(null, 'id ASC', null);
        // $items_categoria = '';
        // $items_categoria2 = "switch ('$term') {";
        // // RENDERIZA O ITEM
        // while ($obCategorias = $results->fetchObject(EntityAnnounce::class)) {
        //     $items_categoria .= View::render('pages/busca/item_categoria', [
        //         'id' => $obCategorias->id,
        //         'nome' => $obCategorias->nome,
        //         'nome_url' => $obCategorias->nome_url,
        //     ]);

        //     $items_categoria2 .= "case '$obCategorias->nome_url':
        //         $('#categoria$obCategorias->id').attr('checked', '');

        //         break;";
        // }
        // $items_categoria2 .= '}';
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        // $obStore = EntityStore::getStoreByUrl($loja);
        // if (!$obStore instanceof EntityStore) {
        //     $request->getRouter()->redirect('/');
        // }

        // VIEW DE DEPOIMENTOS
        $content = View::render('pages/busca/indexnew', [
            'busca' => $term,
            'nada' => $nada,
            // 'items_categoria' => $items_categoria,
            // 'items_categoria2' => $items_categoria2,
            'itens' => self::getSearchItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
        ]);

        // RETORNA A VIEW DA PÁGINA
        return parent::getPage(''.$term.'', $content);
    }

    public static function getSearchAjax($request)
    {
        $postVars = $request->getPostVars();

        $idMarca = $postVars['idMarca'] ? 'cod_marca = "'.$postVars['idMarca'].'" AND ' : '';
        $modeloCarro = $postVars['modeloCarro'] ?? '';
        $anoDe = $postVars['anoDe'] ? 'AND ano_fab >= '.$postVars['anoDe'].'-1' : '';
        $anoAte = $postVars['anoAte'] ? 'AND ano_fab <= '.$postVars['anoAte'].'-1' : '';
        $precoDe = $postVars['precoDe'] ? 'AND valor >= '.$postVars['precoDe'].'' : '';
        // $precoDe = $postVars['precoDe'] ? 'AND valor >= '.$postVars['precoDe'].' : ';
        $precoAte = $postVars['precoAte'] ? 'AND valor <= '.$postVars['precoAte'].'' : '';
        $kmDe = $postVars['kmDe'] ? 'AND kilometragem >= '.$postVars['kmDe'].'' : '';
        $kmAte = $postVars['kmAte'] ? 'AND kilometragem <= '.$postVars['kmAte'].'' : '';
        $cambio = $postVars['cambio'] ?? '';
        $motor = $postVars['motor'] ?? '';
        $combustivel = $postVars['combustivel'] ?? '';
        $cor = $postVars['cor'] ?? '';
        $portas = $postVars['portas'] ?? '';

        $listaAcessorios = $postVars['listaAcessorios'];

        $where = ''.$idMarca.' cod_modelo LIKE "%'.$modeloCarro.'%" 
        '.$anoDe.'
        '.$anoAte.'
        '.$precoDe.' 
        '.$precoAte.' 
        '.$kmDe.'
        '.$kmAte.' 
        AND cambio LIKE "%'.$cambio.'%" 
        AND motor LIKE "%'.$motor.'%" AND combustivel LIKE "%'.$combustivel.'%" AND cor LIKE "%'.$cor.'%" AND portas LIKE "%'.$portas.'%"';

        $results = EntityAnnounce::getAnuncios($where, 'id DESC', null);

        while ($obAccounces = $results->fetchObject(EntityAnnounce::class)) {
            // RESULTADOS DA PAGINA
            $obFoto = EntityAnnounce::getAnnounceFotoById($obAccounces->id);

            // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
            $obStore = EntityStore::getStoreById($obAccounces->id_store);
            $id_store = $obStore->id;

            $nome_marca = $obAccounces->nome_marca;
            $nome_modelo = $obAccounces->nome_modelo;

            $teexte = explode(' ', $nome_marca);
            $teexte2 = explode(' ', $nome_modelo);

            $nome_marca1 = $teexte[0];
            $nome_modelo1 = $teexte2[0];

            $titulooooo = $nome_marca.' '.$nome_modelo1;

            // VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/busca/item', [
                'nome_url' => $obStore->nome_url,
                'titulo' => $titulooooo,
                'nome_modelo' => $nome_modelo,
                'categoria' => $obAccounces->categoria,
                // 'preco' => $obAccounces->preco,
                'preco' => number_format($obAccounces->valor, 2, ',', '.'),
                'url' => $obAccounces->url,
                'foto' => $obFoto->foto_01,
            ]);
        }
        if ($itens == '' || empty($itens)) {
            $itens = '<div class="text-center"><h5>Sem veículos cadastrados com esses parâmetros.</h5></div>';
        }

        return $itens;
    }
}
