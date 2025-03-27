<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\About as EntityAbout;
use \App\Model\Entity\Pagina as EntityPagina;

class Ajuda extends Page
{

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
     * @return string
     */
    public static function getAjuda()
    {
        //$obOrganization = new Organization;

        //$obAbout = new About;


        //RESULTADOS DA PAGINA
        $obAJuda = EntityPagina::getPaginaByUrl("'ajuda'");
        //VIEW DA HOME
        $content = View::render('pages/ajuda', [
            'title'        => $obAJuda->nome,
            'titlesect1'        => $obAJuda->titlesect1,
            'contentsect1' => $obAJuda->contentsect1,
            'titlesect2'        => $obAJuda->titlesect2,
            'contentsect2' => $obAJuda->contentsect2,
            'titlesect3'        => $obAJuda->titlesect3,
            'contentsect3' => $obAJuda->contentsect3
        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Ajuda e Contato', $content);
    }
}
