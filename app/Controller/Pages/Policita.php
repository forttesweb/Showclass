<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;

class Politica extends Page
{

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
     * @return string
     */
    public static function getPolicita()
    {
        $obOrganization = new Organization;

        //VIEW DA HOME
        $content = View::render('pages/politica-privacidade');
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Perguntas Frequentes', $content);
    }
}
