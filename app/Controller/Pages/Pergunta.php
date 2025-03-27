<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Organization;
use \App\Model\Entity\Pagina as EntityPagina;
use \App\Model\Entity\Faq as EntityFaq;

class Pergunta extends Page
{

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
     * @return string
     */
    public static function getPergunta()
    {



        //RESULTADOS DA PAGINA
        $results = EntityFaq::getFaqs(null, 'id ASC', null);

        $itens = "";
        //RENDERIZA O ITEM
        while ($obFaq = $results->fetchObject(EntityFaq::class)) {
            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/faqs/itemspage', [
                'id' => $obFaq->id,
                'nome' => $obFaq->nome,
                'mensagem' => $obFaq->mensagem
            ]);
            // $itens .= View::render('admin/modules/vantagens/item', [
            //     'id' => $obPagina->id,
            //     'descricao' => $obPagina->descricao
            // ]);
        }

        
        $obAJuda = EntityPagina::getPaginaByUrl("'perguntas-frequentes'");
        //VIEW DA HOME
        $content = View::render('pages/perguntas', [
            'title'        => $obAJuda->nome,
            'titlesect1'        => $obAJuda->titlesect1,
            'contentsect1' => $obAJuda->contentsect1,
            'titlesect2'        => $obAJuda->titlesect2,
            'contentsect2' => $obAJuda->contentsect2,
            'titlesect3'        => $obAJuda->titlesect3,
            'contentsect3' => $obAJuda->contentsect3,
            'itens_faq' => $itens
        ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Perguntas Frequentes', $content);
    }
}
