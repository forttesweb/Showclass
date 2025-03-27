<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \App\Model\Entity\Assinatura;
use \App\Model\Entity\Announce as EntityAnnounce;

class Home extends Page
{

    /**
     * Método responsável por renderizar a vie de home do painel
     *
     * @param Request $request
     * @return string
     */
    public static function getHome($request){


        $total_usuarios = EntityUser::getUsers(null, null, null, 'COUNT(*) as total_usuarios')->fetchObject()->total_usuarios;
        $total_assinantes = Assinatura::getAssinaturas('status = ' . "'PAID'", null, null, 'COUNT(*) as total_assinantes')->fetchObject()->total_assinantes;
        $total_anuncios = EntityAnnounce::getAnuncios(null, null, null, 'COUNT(*) as total_posts')->fetchObject()->total_posts;
        
        $assinaturas_pendentes = Assinatura::getAssinaturas('status = ' . "'PENDING'", null, null, 'COUNT(*) as assinaturas_pendentes')->fetchObject()->assinaturas_pendentes;


        $valor_total2 = $total_assinantes * 23.90;


        //CONTEUDO DA HOME
        $content = View::render('admin/modules/home/index', [
            'total_usuarios' => $total_usuarios,
            'total_assinantes' => $total_assinantes,
            'valor_total' => number_format($valor_total2, 2, ',', '.'),
            'total_posts' => $total_anuncios,
            'assinaturas_pendentes' => $assinaturas_pendentes
        ]);

        //RETORNAR A APGINA COMPLETA
        return parent::getPanel('HOME', $content, 'home');
    }
}
