<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Announce as EntityAnnounce;
use \App\Model\Entity\Store as EntityStore;
use \App\Model\Entity\User as EntityUser;
use App\Session\User\Login;
use \WilliamCosta\DatabaseManager\Pagination;

class Store extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getStoreItems($request, &$obPagination, $loja)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obStore = EntityStore::getStoreByUrl($loja);
        $id_store = $obStore->id;


        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityStore::getAnnounces('id_store = "' . $id_store . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 12;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityStore::getAnnounces('id_store = "' . $id_store . '" AND vendido = 0', 'id DESC', $obPagination->getLimit());



        //RENDERIZA O ITEM
        while ($obAccounces = $results->fetchObject(EntityStore::class)) {

            $nome_marca = $obAccounces->nome_marca;
            $nome_modelo = $obAccounces->nome_modelo;

            $teexte = explode(" ", $nome_marca);
            $teexte2 = explode(" ", $nome_modelo);

            $nome_marca1 = $teexte[0];
            $nome_modelo1 = $teexte2[0];

            $titulooooo = $nome_marca . " " . $nome_modelo1;

            //RESULTADOS DA PAGINA
            $obFoto = EntityAnnounce::getAnnounceFotoById($obAccounces->id);

            //VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/store/item', [
                'nome_url' => $obStore->nome_url,
                'titulo' => $titulooooo,
                'nome_modelo' => $nome_modelo,
                'categoria' => $obAccounces->categoria,
                'preco' => number_format($obAccounces->valor, 2, ",", "."),
                // 'preco' => number_format($obAccounces->preco,2,',','.'),
                'url' => $obAccounces->url,
                'foto' => $obFoto->foto_01,
                'vendido' => ''
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }


    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getStoreVendidoItems($request, &$obPagination, $loja)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obStore = EntityStore::getStoreByUrl($loja);
        $id_store = $obStore->id;


        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityStore::getAnnounces('id_store = "' . $id_store . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 8;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityStore::getAnnounces('id_store = "' . $id_store . '" AND vendido = 1', 'id DESC', $obPagination->getLimit());



        //RENDERIZA O ITEM
        while ($obAccounces = $results->fetchObject(EntityStore::class)) {

            //VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/store/item', [
                'nome_url' => $obStore->nome_url,
                'titulo' => $obAccounces->titulo,
                'categoria' => $obAccounces->categoria,
                'preco' => $obAccounces->preco,
                'url' => $obAccounces->url,
                'vendido' => 'Vendido'
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }


    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getStoreSeguidores($request, &$obPagination, $loja)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obStore = EntityStore::getStoreByUrl($loja);
        $id_store = $obStore->id;


        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityStore::getSeguidores('id_store = "' . $id_store . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 8;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityStore::getSeguidores('id_store = "' . $id_store . '"', 'id DESC', $obPagination->getLimit());



        //RENDERIZA O ITEM
        while ($obSeguidores = $results->fetchObject(EntityStore::class)) {

            $obUser = EntityUser::getUserById($obSeguidores->id_seguidor);


            $obStore = EntityStore::getStoreByUser($obUser->id);

            $nome_loja = $obStore->nome_loja;

            if ($obStore->nome_loja == null || $obStore->nome_loja == "") {
                $nome_loja = 'Não definido';
            }

            //VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/store/seguidores', [
                'nome' => $obUser->nome,
                'nome_loja' => $nome_loja,
                'nome_url' => $obStore->nome_url
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }
    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getStoreSeguindo($request, &$obPagination, $loja)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obStore = EntityStore::getStoreByUrl($loja);
        $id_store = $obStore->id;
        $id_user_store = $obStore->id_user;


        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityStore::getSeguidores('id_seguidor = "' . $id_user_store . '"', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityStore::getSeguidores('id_seguidor = "' . $id_user_store . '"', 'id DESC', $obPagination->getLimit());



        //RENDERIZA O ITEM
        while ($obSeguindo = $results->fetchObject(EntityStore::class)) {

            $obStore = EntityStore::getStoreById($obSeguindo->id_store);

            $obUser = EntityUser::getUserById($obStore->id_user);



            $nome_loja = $obStore->nome_loja;

            if ($obStore->nome_loja == null || $obStore->nome_loja == "") {
                $nome_loja = 'Não definido';
            }

            //VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/store/seguidores', [
                'nome' => $obUser->nome,
                'nome_loja' => $nome_loja,
                'nome_url' => $obStore->nome_url
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    // /**
    //  * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
    //  * @return string
    //  */
    // public static function getAnnounce($request)
    // {

    //     //VIEW DA HOME
    //     $content = View::render('pages/announce', [
    //         'status' => self::getStatus($request)
    //     ]);
    //     // $content = View::render('pages/about', [
    //     //     'name'        => $obOrganization->name,
    //     //     'description' => $obOrganization->description,
    //     //     'site'        => $obOrganization->site
    //     // ]);
    //     //RETORNA A VIEW DA PÁGINA
    //     return parent::getPage('Criar anúncio', $content);
    // }

    /**
     * Método responsável por retornar o conteúdo (view) da home
     * @return string
     */
    public static function getStoreAds($request, $loja)
    {

        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obStore = EntityStore::getStoreByUrl($loja);

        if (!$obStore instanceof EntityStore) {
            $request->getRouter()->redirect('/');
        }

        $data = date('Y-m-d H:i:s');

        //$obUserStories = EntityUser::getStories('id_user = "' . $obStore->id_user . '" AND expira > "' . $data . '"', 'id DESC', null)->fetchObject();
        
        $obUserStories = EntityUser::getStoriesByUserExpira($obStore->id_user, $data);
        
        $obUser = EntityUser::getUserById($obStore->id_user);


        $classstories = '';
        $verstories = '';
        if ($obUserStories instanceof EntityUser) {
            $classstories = "imgperfis";
            $verstories = "verstories";
        }

        $obUserLogged = Login::userData();

        $btnseguir = "";
        $btndeixarseguir = "";

        if (!empty($obUserLogged)) {
            // $obSeguidor = EntityStore::getSeguidores('id_seguidor = ' . $obUserLogged['id'] . ' AND id_store = ' . $obStore->id . '', null, null)->fetchObject(self::class);
            $obSeguidor = EntityStore::getSeguidorByUserId($obUserLogged['id'], $obStore->id);

            // echo "<pre>"; print_r($obSeguidor); echo "</pre>"; exit;


            $btnseguir = '<a
onclick="seguirLoja(' . $obUserLogged['id'] . ',' . $obStore->id . ')" href="#"
class="btn btn-sm btn-outline-primary">Seguir</a>';

            if ($obSeguidor instanceof EntityStore) {
                $btnseguir = '<a href="#" class="btn btn-sm btn-success">Seguindo</a>';
                $btndeixarseguir = '<a href="#" class="btn btn-sm btn-outline-danger" onclick="deixarDeSeguir(' . $obUserLogged['id'] . ',' . $obStore->id . ')">Deixar de seguir</a>';
            }

            if ($obStore->id_user == $obUserLogged['id']) {
                $btnseguir = '';
            }
        }




        //VIEW DE DEPOIMENTOS
        $content = View::render('pages/store/index', [
            'id' => $obStore->id,
            'id_logado' => $obUserLogged['id'],
            'id_user' => $obStore->id_user,
            'classstories' => $classstories,
            'verstories' => $verstories,
            'nome_loja' => $obStore->nome_loja,
            'nome_url' => $obStore->nome_url,
            'bairro' => $obUser->bairro,
            'rua' => $obUser->endereco,
            'cidade' => $obStore->cidade,
            'estado' => $obStore->estado,
            'telefone' => $obUser->telefone,
            'logotipo' => $obStore->logotipo,
            'banner' => $obStore->banner,
            'itens' => self::getStoreItems($request, $obPagination, $loja),
            'itensvendidos' => self::getStoreVendidoItems($request, $obPagination, $loja),
            'seguidores' => self::getStoreSeguidores($request, $obPagination, $loja),
            'seguindo' => self::getStoreSeguindo($request, $obPagination, $loja),
            'btnseguir' => $btnseguir,
            'btndeixarseguir' => $btndeixarseguir,
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('' . $obStore->nome_loja . '', $content);
    }

    // /**
    //  * Método responsável por retornar o formulário de edição de um depoimento
    //  *
    //  * @param Request $request
    //  * @param integer $id
    //  * @return string
    //  */
    // public static function getStoreAds($request, $loja)
    // {
    //     //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
    //     $obStore = EntityStore::getStoreByUrl($loja);
    //     echo "<pre>"; print_r($obStore); echo "</pre>"; exit;

    //      //VIEW DA HOME
    //      $content = View::render('pages/store', [
    //         'nome_loja' => $obStore->nome_loja,
    //         'cidade' => $obStore->cidade,
    //         'status' => $obStore->status,
    //         'status' => self::getStatus($request)
    //     ]);
    //     // $content = View::render('pages/about', [
    //     //     'name'        => $obOrganization->name,
    //     //     'description' => $obOrganization->description,
    //     //     'site'        => $obOrganization->site
    //     // ]);
    //     //RETORNA A VIEW DA PÁGINA
    //     return parent::getPage($obStore->titulo, $content);
    // }



    /**
     * Método responsável por inserir o review do anunciante
     * @param Request $request
     * @return string 
     */
    public static function insertReview($request)
    {



        //DADOS DO POST
        $postVars = $request->getPostVars();
        $id_user = $postVars['id_user'] ?? '';
        $id_loja = $postVars['id_loja'] ?? '';
        $nome_loja = $postVars['nome_loja'] ?? '';
        $comentario = $postVars['comentario'] ?? '';
        $rating = $postVars['rating'] ?? '';

        //NOVA INSTANCIA DE DEPOIMENTO
        $obStore = new EntityStore;
        $obStore->id_user = $id_user;
        $obStore->id_store = $id_loja;
        $obStore->nome_url = $nome_loja;
        $obStore->comentario = $comentario;
        $obStore->rating = $rating;
        $obStore->cadastrar_review();

        $anuncio = $postVars['anuncio'];

        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obAnnounce = EntityAnnounce::getAnnounceByUrl($anuncio);

        $obStore = EntityStore::getStoreById($obAnnounce->id_store);

        $request->getRouter()->redirect('/anuncio/' . $obStore->nome_url . '/' . $obAnnounce->url . '/?status=review');
    }
    /**
     * Método responsável por inserir o review do anunciante
     * @param Request $request
     * @return string 
     */
    public static function insertSeguidor($request)
    {



        //DADOS DO POST
        $postVars = $request->getPostVars();

        $id_seguidor = $postVars['id_seguidor'] ?? '';
        $id_loja = $postVars['id_loja'] ?? '';
        // $nome_loja = $postVars['nome_loja'] ?? '';
        // $comentario = $postVars['comentario'] ?? '';
        // $rating = $postVars['rating'] ?? '';

        //NOVA INSTANCIA DE DEPOIMENTO
        $obStore = new EntityStore;
        $obStore->id_seguidor = $id_seguidor;
        $obStore->id_store = $id_loja;
        $obStore->cadastrar_seguidor();

        $anuncio = $postVars['anuncio'];

        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS

        $obStore = EntityStore::getStoreById($id_loja);

        echo 'success';
        // $request->getRouter()->redirect('/anuncio/' . $obStore->nome_url . '/?status=seguindo');
    }

    /**
     * Método responsável por inserir o review do anunciante
     * @param Request $request
     * @return string 
     */
    public static function RemoverSeguidor($request)
    {



        //DADOS DO POST
        $postVars = $request->getPostVars();

        $id_seguidor = $postVars['id_seguidor'] ?? '';
        $id_loja = $postVars['id_loja'] ?? '';
        // $nome_loja = $postVars['nome_loja'] ?? '';
        // $comentario = $postVars['comentario'] ?? '';
        // $rating = $postVars['rating'] ?? '';

        //NOVA INSTANCIA DE DEPOIMENTO
        $obStore = new EntityStore;
        $obStore->id_seguidor = $id_seguidor;
        $obStore->id_store = $id_loja;
        $obStore->remover_seguidor();

        $anuncio = $postVars['anuncio'];

        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS

        $obStore = EntityStore::getStoreById($id_loja);

        echo 'success';
        // $request->getRouter()->redirect('/anuncio/' . $obStore->nome_url . '/?status=seguindo');
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
                return Alert::getSuccess('Anúncio criado com sucesso!<br> <a href="./account">Clique aqui</a> para visualizar seu anúncio');
                break;
            case 'seguindo':
                return Alert::getSuccess('Você agora está seguindo esta loja !');
                break;
            case 'removido':
                return Alert::getSuccess('Você deixou de seguir essa loja !');
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
            case 'error':
                return Alert::getError('O código inserido está incorreto.');
                break;
        }
    }
}
