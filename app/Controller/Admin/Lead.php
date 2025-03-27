<?php

namespace App\Controller\Admin;

use App\Model\Entity\Lead as EntityLead;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class Lead extends Page
{
    /**
     * Método responsável por obter a renderização dos itens de leads para página.
     *
     * @param Request    $request
     * @param Pagination $obPagination
     *
     * @return string
     */
    private static function getLeadItems($request, &$obPagination)
    {
        // DEPOIMENTOS
        $itens = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityLead::getLeads(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);

        // RESULTADOS DA PAGINA
        $results = EntityLead::getLeads(null, 'id DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while ($obLead = $results->fetchObject(EntityLead::class)) {
            // VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/leads/item', [
                'id' => $obLead->id,
                'fullname' => $obLead->nome,
                'phone' => $obLead->phone,
                'email' => $obLead->email,
                'data' => date('d/m/Y H:i:s', strtotime($obLead->created_at)),
            ]);
        }

        // RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a vie de listagem de leads.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function getLeads($request)
    {
        // CONTEUDO DA HOME
        $content = View::render('admin/modules/leads/index', [
            'itens' => self::getLeadItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
        ]);

        // RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Leads', $content, 'leads');
    }

    /**
     * Método responsável por retornar a mensagem de status.
     *
     * @param Request $request
     *
     * @return string
     */
    private static function getStatus($request)
    {
        // QUERY PARAMS
        $queryParams = $request->getQueryParams();

        // STATUS
        if (!isset($queryParams['status'])) {
            return '';
        }

        // MENSAGENS DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('FAQ criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('FAQ atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('FAQ excluido com sucesso!');
                break;
        }
    }
}
