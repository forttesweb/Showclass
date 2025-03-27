<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Dicas extends Page
{

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
     * @return string
     */
    public static function getDicas()
    {
        $obOrganization = new Organization;

        //VIEW DA HOME
        $content = View::render('pages/dicas');
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Dicas de Segurança', $content);
    }
}
