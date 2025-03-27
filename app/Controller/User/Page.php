<?php

namespace App\Controller\User;

use App\Model\Entity\Notificacao as EntityNotificacao;
use App\Session\User\Login as SessionUserLogin;
use App\Utils\View;

class Page
{
    /**
     * Módulos disponívels no painel.
     *
     * @var array
     */
    private static $modules = [
        'home' => [
            'label' => 'Home',
            'link' => URL.'/account',
        ],
        'testimonies' => [
            'label' => 'Depoimentos',
            'link' => URL.'/account/testimonies',
        ],
        'users' => [
            'label' => 'Usuários',
            'link' => URL.'/account/users',
        ],
    ];

    /**
     * Método responsvel por renderizar o topo da página.
     *
     * @return string
     */
    private static function getHeader()
    {
        $id_user = SessionUserLogin::isLogged();

        $notificacao = '';

        $menu = "<li class='nav-item hidemobile'><a href='".getenv('URL')."/account/login' class='nav-link buttonentrar btn btn-primary active'>ENTRAR</a></li>
        <li class='nav-item hidemobile'><a href='".getenv('URL')."/account/cadastro' class='nav-link buttonregistrar btn btn-primary active'>REGISTRAR-SE</a></li>
                <li class='nav-item hidedesk'><a href='".getenv('URL')."/account/login' class='nav-link'>
                    <img class='iconuser' src='".URL."/resources/view/pages/assets/images/userpng.png'>
                Entrar
                </a></li>
                                <li class='nav-item hidedesk'><a href='".getenv('URL')."/account/cadastro' class='nav-link'>
                 <img class='iconuser' src='".URL."/resources/view/pages/assets/images/userpng.png'>
                 Registrar-se</a></li>";

        if (SessionUserLogin::isLogged()) {
            $menu = View::render('pages/menu/box', [
                'nome' => $_SESSION['user']['usuario']['nome'],
            ]);

            // QUANTIDADE TOTAL DE REGISTROS
            $quantidadetotal = EntityNotificacao::getNotificacoes('id_seguidor = '.$id_user.' AND visualizada = 0', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

            if ($quantidadetotal != 0) {
                $notificacao = '<span class="badge bg-danger">'.$quantidadetotal.'</span>';
            }
        }

        return View::render('pages/header', [
            'menu' => $menu,
            'qntnotificacao' => $notificacao,
        ]);
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
     * Método responsável por retornar o conteúdo (view) da estrutura genérica de página do painel.
     *
     * @param string $title
     * @param string $content
     *
     * @return string
     */
    public static function getPage($title, $content)
    {
        return View::render('pages/page', [
            'title' => $title.' | '.getenv('SITE_NAME'),
            'header' => self::getHeader(),
            'content' => $content,
            'footer' => self::getFooter(),
        ]);
        // return View::render('pages/page', [
        //     'title'   => $title,
        //     'content' => $content
        // ]);
    }

    /**
     * Método responsável por renderizar a view do menu do painel.
     *
     * @param string $currentModule
     *
     * @return string
     */
    private static function getMenu($currentModule)
    {
        // LINKS DO MENU
        $links = '';

        // ITERA O MÓDULOS

        foreach (self::$modules as $hash => $module) {
            $links .= View::render('account/menu/link', [
                'label' => $module['label'],
                'link' => $module['link'],
                'current' => $hash == $currentModule ? 'text-danger' : '',
            ]);
        }

        // RETORNA A RENDERIZAÇÃO DO MENU
        return View::render('account/menu/box', [
            'links' => $links,
        ]);
    }

    /**
     * Método responsável por renderizar a view do painel com conteúdo dinâmicos.
     *
     * @param string $title
     * @param string $content
     * @param string $currentModule
     *
     * @return string
     */
    public static function getPanel($title, $content, $currentModule)
    {
        // RENDERIZA A VIEW DO PAINEL
        $contentPanel = View::render('account/panel', [
            'menu' => self::getMenu($currentModule),
            'content' => $content,
        ]);

        // RETORNAR A PÁGINA RENDERIZADA
        return self::getPage($title, $contentPanel);
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
            $links .= View::render('account/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'active' : '',
            ]);
        }

        // RENDERIZA BOX PAGINACAO
        return View::render('account/pagination/box', [
            'links' => $links,
        ]);
    }
}
