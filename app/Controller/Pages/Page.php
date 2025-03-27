<?php

namespace App\Controller\Pages;

use App\Model\Entity\Categoria as EntityCategoria;
use App\Model\Entity\Notificacao as EntityNotificacao;
use App\Session\User\Login as SessionUserLogin;
use App\Utils\View;

class Page
{
    /**
     * Método responsvel por renderizar o topo da página.
     *
     * @return string
     */
    private static function getHeader()
    {
        $id_user = SessionUserLogin::isLogged();

        $notificacao = '';

        $menu = "<li class='nav-item hidemobile'><a href='".getenv('URL')."/account/login' class='nav-link buttonentrar btn btn-primary'>ENTRAR</a></li>
        <li class='nav-item hidemobile'><a href='".getenv('URL')."/account/cadastro' class='nav-link btn btn-primary buttonregistrar'>REGISTRAR-SE</a></li>
                <li class='nav-item hidedesk'><a href='".getenv('URL')."/account/login' class='nav-link'>
                <img class='iconuser' src='".URL."/resources/view/pages/assets/images/userpng.png'>
                Entrar
                </a></li>
                <li class='nav-item hidedesk'><a href='".getenv('URL')."/account/cadastro' class='nav-link'>
                 <img class='iconuser' src='".URL."/resources/view/pages/assets/images/userpng.png'>
                 Registrar-se</a></li>
";

        if (SessionUserLogin::isLogged()) {
            $menu = View::render('pages/menu/box', [
                'nome' => $_SESSION['user']['usuario']['nome'],
            ]);

            // QUANTIDADE TOTAL DE REGISTROS
            $quantidadetotal = EntityNotificacao::getNotificacoes('id_seguidor = '.$id_user.' AND visualizada = 0', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            if ($quantidadetotal != 0) {
                $notificacao = '<span class="badge bg-danger">'.$quantidadetotal.'</span>';
            }

            // $obNotificacao = EntityNotificacao::getNotificacaoByUserId(SessionUserLogin::isLogged());

            // echo "<pre>"; print_r($obNotificacao); echo "</pre>"; exit;
        }

        $results_cat = EntityCategoria::getCategorias(null, null, null);

        $itens_cat = '';
        while ($obCategoria = $results_cat->fetchObject(EntityCategoria::class)) {
            // $tipo = $obPlanoFeat->slug;

            // Faça algo com os resultados
            $itens_cat .= '<li><a class="dropdown-item" href="'.getenv('URL').'/busca?term='.$obCategoria->nome_url.'">'.$obCategoria->nome.'</a></li>';
        }

        return View::render('pages/header', [
            'menu' => $menu,
            'qntnotificacao' => $notificacao,
            'itens_cat' => $itens_cat,
        ]);
        // return View::render('pages/header');
    }

    /**
     * Método responsvel por renderizar o footer da página.
     *
     * @return string
     */
    private static function getFooter()
    {
        return View::render('pages/footer');
    }

    /**
     * Método responsável por renderizar o layout de poaginação.
     *
     * @param Request    $request
     * @param Pagination $obPagination
     *
     * @return string
     */
    public static function getPagination($request, $obPagination)
    {
        // PÁGINAS
        $pages = $obPagination->getPages();

        // VERIFICA A QUANTIDADE DE PAGINAS
        if (count($pages) <= 1) {
            return '';
        }

        // LINKS
        $links = '';

        // URL ATUAL (SEM GETS)
        $url = $request->getRouter()->getCurrentUrl();

        // GET
        $queryParams = $request->getQueryParams();

        // RENDERIZA OS LINKS
        foreach ($pages as $page) {
            // ALTERA A PAGINA
            $queryParams['page'] = $page['page'];

            // LINK
            $link = $url.'?'.http_build_query($queryParams);

            // VIEW
            $links .= View::render('pages/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'active' : '',
            ]);
        }

        // RENDERIZA BOX PAGINACAO
        return View::render('pages/pagination/box', [
            'links' => $links,
        ]);
    }

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página genérica.
     *
     * @return string
     */
    public static function getPage($title, $content)
    {
        $obUserLogged = SessionUserLogin::userData();

        return View::render('pages/page', [
            'token' => $obUserLogged['token'],
            'title' => $title.' | '.getenv('SITE_NAME'),
            'header' => self::getHeader(),
            'content' => $content,
            'footer' => self::getFooter(),
        ]);
    }
}
