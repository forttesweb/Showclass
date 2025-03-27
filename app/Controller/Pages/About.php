<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\About as EntityAbout;
use \App\Model\Entity\Pagina as EntityPagina;

class About extends Page
{

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
     * @return string
     */
    public static function getAbout()
    {
        $obAbout = new About;


        //RESULTADOS DA PAGINA
        $obAbout = EntityPagina::getPaginaByUrl("'sobre'");
        //VIEW DA HOME
        $content = View::render('pages/about', [
            'title'        => $obAbout->nome,
            'titlesect1'        => $obAbout->titlesect1,
            'contentsect1' => $obAbout->contentsect1,
            'titlesect2'        => $obAbout->titlesect2,
            'contentsect2' => $obAbout->contentsect2,
            'titlesect3'        => $obAbout->titlesect3,
            'contentsect3' => $obAbout->contentsect3
        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Sobre', $content);
    }
}
