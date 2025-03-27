<?php

namespace App\Controller\Admin;

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
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-home" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
            <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
            <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
         </svg>',
            'label' => 'Home',
            'link' => URL.'/admin-panel',
        ],
        'paginas' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clipboard-text" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"></path>
            <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"></path>
            <path d="M9 12h6"></path>
            <path d="M9 16h6"></path>
         </svg>',
            'label' => 'Páginas',
            'link' => URL.'/admin/paginas',
        ],
        'lojas' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-warehouse" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M3 21v-13l9 -4l9 4v13"></path>
            <path d="M13 13h4v8h-10v-6h6"></path>
            <path d="M13 21v-9a1 1 0 0 0 -1 -1h-2a1 1 0 0 0 -1 1v3"></path>
         </svg>',
            'label' => 'Lojas',
            'link' => URL.'/admin-panel/lojas',
        ],
        'planos' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-article" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z"></path>
            <path d="M7 8h10"></path>
            <path d="M7 12h10"></path>
            <path d="M7 16h10"></path>
         </svg>',
            'label' => 'Planos',
            'link' => URL.'/admin-panel/planos',
        ],
        'categorias' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-category" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M4 4h6v6h-6z"></path>
            <path d="M14 4h6v6h-6z"></path>
            <path d="M4 14h6v6h-6z"></path>
            <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0"></path>
         </svg>',
            'label' => 'Categorias',
            'link' => URL.'/admin-panel/categorias',
        ],
        'assinaturas' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-signature" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2"></path>
         </svg>',
            'label' => 'Assinaturas',
            'link' => URL.'/admin-panel/assinaturas',
        ],
        'faqs' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-hexagon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M19.875 6.27c.7 .398 1.13 1.143 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033z"></path>
            <path d="M12 16v.01"></path>
            <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
         </svg>',
            'label' => 'FAQs',
            'link' => URL.'/admin-panel/faqs',
        ],
        'leads' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-help-hexagon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M19.875 6.27c.7 .398 1.13 1.143 1.125 1.948v7.284c0 .809 -.443 1.555 -1.158 1.948l-6.75 4.27a2.269 2.269 0 0 1 -2.184 0l-6.75 -4.27a2.225 2.225 0 0 1 -1.158 -1.948v-7.285c0 -.809 .443 -1.554 1.158 -1.947l6.75 -3.98a2.33 2.33 0 0 1 2.25 0l6.75 3.98h-.033z"></path>
            <path d="M12 16v.01"></path>
            <path d="M12 13a2 2 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
         </svg>',
            'label' => 'Leads',
            'link' => URL.'/admin-panel/leads',
        ],
        // 'testimonies' => [
        //     'label' => 'Depoimentos',
        //     'link' => URL . '/admin-panel/testimonies'
        // ],
        'users' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users-group" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
            <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
            <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1"></path>
            <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
            <path d="M17 10h2a2 2 0 0 1 2 2v1"></path>
            <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"></path>
            <path d="M3 13v-1a2 2 0 0 1 2 -2h2"></path>
         </svg>',
            'label' => 'Usuários',
            'link' => URL.'/admin-panel/users',
        ],
    ];

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
        return View::render('admin/page', [
            'title' => $title,
            'content' => $content,
        ]);
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
            $links .= View::render('admin/menu/link', [
                'icon' => $module['icon'],
                'label' => $module['label'],
                'link' => $module['link'],
                'current' => $hash == $currentModule ? 'text-danger' : '',
            ]);
        }

        // RETORNA A RENDERIZAÇÃO DO MENU
        return View::render('admin/menu/box', [
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
        $contentPanel = View::render('admin/panel', [
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
            $links .= View::render('admin/pagination/link', [
                'page' => $page['page'],
                'link' => $link,
                'active' => $page['current'] ? 'active' : '',
            ]);
        }

        // RENDERIZA BOX PAGINACAO
        return View::render('admin/pagination/box', [
            'links' => $links,
        ]);
    }
}
