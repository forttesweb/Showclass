<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \WilliamCosta\DatabaseManager\Pagination;
use \App\Services\Upload;
use \App\Model\Entity\Assinatura as EntityAssinatura;
use \App\Model\Entity\Plan as EntityPlan;
use \App\Model\Entity\User as EntityUser;

class Assinatura extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getAssinaturaItems($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';
        $assinatura = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityAssinatura::getAssinaturas(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityAssinatura::getAssinaturas(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obAssinatura = $results->fetchObject(EntityAssinatura::class)) {


            $ObUser = EntityUser::getUserById($obAssinatura->user);

            $obPlano = EntityPlan::getPlanById($obAssinatura->plano);


            // if ($user_assi == 'PAID') {
            //     $assinatura = 'Ativa';
            // } else {
            //     $assinatura = 'Aguardando Pagamento';
            // }


            switch ($obAssinatura->status) {
                case 'WAITING':
                case 'PENDING':
                    $assinatura = '<span class="text-info"><strong>Aguardando Pagamento</strong></span>';
                    $expiracao = 'N/A';
                    break;
                case "PAID":
                    $assinatura = '<span class="text-success"><strong>Ativa</strong></span>';
                    $expiracao = date('d/m/Y H:i:s', strtotime($obAssinatura->expiracao));
                    break;
                case 'CANCELLED':
                    $assinatura = '<span class="text-danger"><strong>Cancelada</strong></span>';
                    $expiracao = 'N/A';
                    break;
                case 'EXPIRED':
                    $assinatura = '<span class="text-warning"><strong>Expirada</strong></span>';
                    $expiracao = 'N/A';
                    break;
                default:
                    $assinatura = '<span class="text-info"><strong>Gratuito</strong></span>';
                    $expiracao = 'N/A';
                    break;
            }
            switch ($obAssinatura->metodo) {
                case 'creditCard':
                    $metodo = '<span class="text-secondary"><strong>Cartão de crédito</strong></span>';
                    break;
                case "pix":
                    $metodo = '<span class="text-secondary"><strong>Pix</strong></span>';
                    break;
                case 'boleto':
                    $metodo = '<span class="text-secondary"><strong>Boleto</strong></span>';
                    break;
                default:
                    $metodo = '<span class="text-secondary"><strong>Gratuito</strong></span>';
                    break;
            }





            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/assinaturas/item', [
                'id' => $obAssinatura->id,
                'nome' => $ObUser->nome,
                'email' => $ObUser->email,
                'status' => $assinatura,
                'metodo' => $metodo,
                'plano' => $obPlano->titulo,
                'expiracao' => date('d/m/Y H:i:s', strtotime($obAssinatura->expiracao)),
                'data' => date('d/m/Y H:i:s', strtotime($obAssinatura->data)),
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a vie de listagem de depoimentos
     *
     * @param Request $request
     * @return string
     */
    public static function getAssinaturas($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/assinaturas/index', [
            'itens' => self::getAssinaturaItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Assinaturas', $content, 'assinaturas');
    }

    /**
     * Método responsável por retornar a mensagem de status
     *
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if (!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Depoimento criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Depoimento atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Depoimento excluido com sucesso!');
                break;
        }
    }
}
