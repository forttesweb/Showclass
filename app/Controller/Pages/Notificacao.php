<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Notificacao as EntityNotificacao;
use \App\Model\Entity\Store as EntityStore;
use \App\Model\Entity\Announce as EntityAnnounce;
use \App\Session\User\Login as SessionUserLogin;
use \WilliamCosta\DatabaseManager\Pagination;

class Notificacao extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getNotificacaoItems($request, &$obPagination)
    {

        $id_user = SessionUserLogin::isLogged();
        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityNotificacao::getNotificacoes('id_seguidor = ' . $id_user, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);


        //RESULTADOS DA PAGINA
        $results = EntityNotificacao::getNotificacoes('id_seguidor = ' . $id_user, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obNotificacao = $results->fetchObject(EntityNotificacao::class)) {

            //CONSULTAR OS DADOS DA(s) LOJA(s)
            $obStore = EntityStore::getStoreById($obNotificacao->id_store);

            $obAnuncio = EntityAnnounce::getAnnounceById($obNotificacao->id_anuncio);

            //VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/notificacoes/item', [
                'nome' => 'Loja ' . $obStore->nome_loja,
                'mensagem' => 'Publicou o seguinte anúncio - <b>' . $obAnuncio->titulo . '</b>',
                'url_anuncio' => $obStore->nome_url . '/' . $obAnuncio->url,
                'data' => date('d/m/Y H:i:s', strtotime($obNotificacao->created_at)),
            ]);
        }

        if (empty($itens)) {
            $itens = View::render('pages/notificacoes/vazio');
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por retornar o conteúdo (view) da home
     * @return string
     */
    public static function getNotificacoes($request)
    {

        $id_user = SessionUserLogin::isLogged();

        //RESULTADOS DA PAGINA
        $results = EntityNotificacao::getNotificacoes('id_seguidor = ' . $id_user, 'id DESC');

        
        //RENDERIZA O ITEM
        while ($obNotificacao = $results->fetchObject(EntityNotificacao::class)) {
            
            //DADOS DO POST
            $postVars = $request->getPostVars();

            //NOVA INSTANCIA DE DEPOIMENTO
            $obNotificacao->visualizada = '1';
            $obNotificacao->atualizar_visualizacao();
        }

        //VIEW DE DEPOIMENTOS
        $content = View::render('pages/notificacoes', [
            'itens' => self::getNotificacaoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination)
        ]);

        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Notificações', $content);
    }

    /**
     * Método responsável por cadastrar o depoimento
     * @param Request $request
     * @return string
     */
    public static function insertNotificacao($request)
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();

        //NOVA INSTANCIA DE DEPOIMENTO
        $obNotificacao = new EntityNotificacao;
        $obNotificacao->nome = $postVars['nome'];
        $obNotificacao->mensagem = $postVars['mensagem'];
        $obNotificacao->cadastrar();

        //REETORNA A PAGINA DE LISTAGEM DE DEPOIMENTOS
        return self::getNotificacoes($request);
    }
}
