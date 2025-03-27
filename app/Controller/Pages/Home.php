<?php

namespace App\Controller\Pages;

use App\Model\Entity\Announce as EntityAnnounce;
use App\Model\Entity\Categoria as EntityCategoria;
use App\Model\Entity\Store as EntityStore;
use App\Model\Entity\User as EntityUser;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class Home extends Page
{
    /**
     * Método responsável por obter a renderização dos itens de anuncios para página.
     *
     * @param Request    $request
     * @param Pagination $obPagination
     *
     * @return string
     */
    private static function getAnnounceItems($request, &$obPagination)
    {
        // DEPOIMENTOS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityAnnounce::getAnuncios('vendido = 0', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);

        // RESULTADOS DA PAGINA
        $results = EntityAnnounce::getAnuncios('vendido = 0', 'id DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while ($obAnnounce = $results->fetchObject(EntityAnnounce::class)) {
            $obStore = EntityStore::getStoreById($obAnnounce->id_store);

            // RESULTADOS DA PAGINA
            $obFoto = EntityAnnounce::getAnnounceFotoById($obAnnounce->id);

            $nome_marca = $obAnnounce->nome_marca;
            $nome_modelo = $obAnnounce->nome_modelo;

            $teexte = explode(' ', $nome_marca);
            $teexte2 = explode(' ', $nome_modelo);

            $nome_marca1 = $teexte[0];
            $nome_modelo1 = $teexte2[0];

            $titulooooo = $nome_marca.' '.$nome_modelo1;
            
            $foto = $obFoto->foto_01;
            if (empty($foto)) {
                $foto = 'produtosemfoto2.png';
            }
            // VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/home/item', [
                'nome_url' => $obStore->nome_url,
                'url' => $obAnnounce->url,
                'titulo' => $titulooooo,
                'nome_modelo' => $nome_modelo,
                // 'titulo' => substr($obAnnounce->titulo, 0, 40) . '...',
                'descricao' => $obAnnounce->descricao,
                'preco' => number_format($obAnnounce->valor, 2, ',', '.'),
                // 'preco' => number_format($obAnnounce->preco,2,",","."),
                'foto' => $foto,
                'data' => date('d/m/Y H:i:s', strtotime($obAnnounce->data)),
            ]);
        }

        // RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por obter a renderização dos itens de anuncios para página.
     *
     * @param Request    $request
     * @param Pagination $obPagination
     *
     * @return string
     */
    private static function getCategoriaItems($request, &$obPagination)
    {
        // DEPOIMENTOS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityCategoria::getCategorias(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 6);

        // RESULTADOS DA PAGINA
        $results = EntityCategoria::getCategorias(null, 'id ASC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while ($obCategoria = $results->fetchObject(EntityCategoria::class)) {
            // VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/home/item_categoria', [
                'nome_url' => $obCategoria->nome_url,
                'nome' => $obCategoria->nome,
                'imagem' => $obCategoria->imagem,
            ]);
        }

        // RETORNA OS DEPOIMENTOS
        return $itens;
    }

    private static function getCategoriaItemsMob($request, &$obPagination)
    {
        // DEPOIMENTOS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityCategoria::getCategorias(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 6);

        // RESULTADOS DA PAGINA
        $results = EntityCategoria::getCategorias(null, 'id ASC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while ($obCategoria = $results->fetchObject(EntityCategoria::class)) {
            // VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/home/item_categoriamob', [
                'nome_url' => $obCategoria->nome_url,
                'nome' => $obCategoria->nome,
                'imagem' => $obCategoria->imagem,
            ]);
        }

        // RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por pegar os usuários assinantes da home.
     */
    public static function getUsersPremium($request)
    {
        $itens = '';
        $postVars = $request->getPostVars();
    
        // RESULTADOS DA PAGINA
        $results = EntityUser::getUsersAssinantes(null, 'id DESC', null);
    
        // RENDERIZA O ITEM
        while ($obUsersAssi = $results->fetchObject(EntityUser::class)) {
            $dataAtual = date('Y-m-d H:i:s');
    
            // VERIFICA SE O USUÁRIO POSSUI STORIES ATIVOS
            $resultsstories = EntityUser::getStories('id_user = "' . $obUsersAssi->user . '" AND expira > "' . $dataAtual . '"', 'id DESC', null);
    
            // SE O USUÁRIO POSSUI STORIES ATIVOS, PROSSEGUE
            if ($resultsstories->rowCount() > 0) {
                $obStore = EntityStore::getStoreByUser($obUsersAssi->user);
    
                // EVITA ERROS CASO NÃO ENCONTRE UMA LOJA
                $nomeLoja = $obStore->nome_loja ?? 'Loja não encontrada';
                $logotipo = $obStore->logotipo ?? '';
    
                $itens .= View::render('pages/home/item_perfil', [
                    'id' => $obUsersAssi->id,
                    'id_user' => $obUsersAssi->user,
                    'nome_loja' => $nomeLoja,
                    'logotipo' => $logotipo,
                ]);
            }
        }
    
        // RETORNA OS ITENS RENDERIZADOS
        return $itens;
    }
    

    /**
     * Método responsável por retornar o conteúdo (view) da home.
     *
     * @return string
     */
    public static function getHome($request)
    {
        // VIEW DA HOME
        $content = View::render('pages/home', [
            'itens' => self::getAnnounceItems($request, $obPagination),
            'itens_perfil' => self::getUsersPremium($request),
            'categorias' => self::getCategoriaItems($request, $obPagination),
            'categorias_mob' => self::getCategoriaItemsMob($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
        ]);

        // RETORNA A VIEW DA PÁGINA
        return parent::getPage('Home', $content);
    }

    /**
     * Método responsável por retornar o conteúdo (view) da home.
     *
     * @return string
     */
    public static function getHomeStories($request)
    {
        $postVars = $request->getPostVars();

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();

        $data = date('Y-m-d H:i:s');

        $id = $postVars['id'];
        $itens = [];

        // RESULTADOS DA PAGINA
        $results = EntityUser::getStories('id_user = "'.$id.'" AND expira > "'.$data.'"', 'id DESC', null);
        $reassadasults = EntityUser::getStories('id_user = "'.$id.'"', 'id DESC', null)->fetchObject();

        $obStore = EntityStore::getStoreByUser($reassadasults->id_user);

        $nome = $obStore->nome_loja;
        $logotipo = $obStore->logotipo;
        $url = $obStore->nome_url;

        while ($obStories = $results->fetchObject(EntityUser::class)) {
            $itens[] = '<img class="imgslides" src="'.getenv('URL').'/publico/stories/'.$obStories->imagem.'" alt="Img 1">';
        }

        $output = [
            'nome' => $nome,
            'imagens' => $itens,
            'logotipo' => $logotipo,
            'url' => $url,
        ];

        return $output;

        // // Converte o array em JSON
        // $jsonArray = json_encode($itens);

        // // Retorna o JSON
        // echo $jsonArray;
        // VIEW DA HOME
        // $content = View::render('pages/home/stories/item', [
        //     'itens' => self::getAnnounceItems($request, $obPagination),
        //     'pagination' => parent::getPagination($request, $obPagination)
        // ]);
        // RETORNA A VIEW DA PÁGINA
        // return parent::getPage('Home', $content);
        // return json_encode($itens);
    }
}
