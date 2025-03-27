<?php

namespace App\Controller\Pages;

use App\Model\Entity\Assinatura;
use \App\Utils\View;
use \App\Model\Entity\Plan as EntityPlan;
use \App\Model\Entity\Pagina as EntityPagina;
use App\Model\Entity\User as EntityUser;
use App\Session\User\Login as SessionUserLogin;

require_once __DIR__ . '/../../../includes/pagseguro.php';

class Plans extends Page
{

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
     * @return string
     */
    public static function getPlans()
    {
        $obAJuda = EntityPagina::getPaginaByUrl("'planos'");
      
       $planos = self::getPlanos();

     
        // Renderizando a view com cada plano individualmente
        $content = View::render('pages/plans', [
            'title'        => $obAJuda->nome,
            'titlesect1'   => $obAJuda->titlesect1,
            'contentsect1' => $obAJuda->contentsect1,
            'titlesect2'   => $obAJuda->titlesect2,
            'contentsect2' => $obAJuda->contentsect2,
            'titlesect3'   => $obAJuda->titlesect3,
            'contentsect3' => $obAJuda->contentsect3,
            'planos'        => self::getPlanos(),
        ]);
    
        // Retorna a view da página
        return parent::getPage('Planos', $content);
    }
    
    private static function getPlanos() {
        $dataPlans = EntityPlan::getAllPlans();
        $features = EntityPlan::getAllPlansFeat();
        $userLogado = false;
        $assinatura = null;
    
        // Verificar se o usuário está logado e buscar a assinatura
        if (SessionUserLogin::isLogged()) {
            $userLogado = true;
            $assinatura = Assinatura::getAssinaturaByUserId($_SESSION['user']['usuario']['id']);
        }
    
        // Agrupa features por plano
        $featuresByPlan = [];
        foreach ($features as $feature) {
            $id_plano = $feature->id_plano;
            $featuresByPlan[$id_plano][] = $feature;
        }
    
        // Definir quais features mostrar e seus títulos descritivos
        $allowedFeatures = [
            'itens_loja' => 'Itens na loja',
            'descricao' => 'Descrição',
            'destaque_items_semana' => 'Destaques na semana',
            'story_dia' => 'Story por dia'
        ];
    
        // Construir o HTML dos planos
        $plansHtml = '';
        foreach ($dataPlans as $plan) {
            $planId = $plan->id;
            $featuresHtml = '';
    
            // Montar o HTML das features filtradas e com títulos ajustados
            foreach ($featuresByPlan[$planId] ?? [] as $feature) {
                if (isset($allowedFeatures[$feature->slug])) {
                    $featureTitle = $allowedFeatures[$feature->slug]; // Título ajustado
                    $featuresHtml .= '
                        <li class="list-group-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <path d="M10.4522 16.9268L18.5484 8.83058L16.9406 7.22282L10.4522 13.7113L7.17923 10.4383L5.57147 12.0461L10.4522 16.9268ZM12.0599 23.1282C10.4713 23.1282 8.97839 22.8265 7.58117 22.2232C6.18395 21.6207 4.96856 20.8026 3.935 19.7691C2.90144 18.7355 2.08339 17.5201 1.48087 16.1229C0.877574 14.7257 0.575928 13.2328 0.575928 11.6442C0.575928 10.0555 0.877574 8.56262 1.48087 7.1654C2.08339 5.76818 2.90144 4.55279 3.935 3.51923C4.96856 2.48567 6.18395 1.66724 7.58117 1.06395C8.97839 0.46142 10.4713 0.160156 12.0599 0.160156C13.6485 0.160156 15.1415 0.46142 16.5387 1.06395C17.9359 1.66724 19.1513 2.48567 20.1849 3.51923C21.2184 4.55279 22.0365 5.76818 22.639 7.1654C23.2423 8.56262 23.5439 10.0555 23.5439 11.6442C23.5439 13.2328 23.2423 14.7257 22.639 16.1229C22.0365 17.5201 21.2184 18.7355 20.1849 19.7691C19.1513 20.8026 17.9359 21.6207 16.5387 22.2232C15.1415 22.8265 13.6485 23.1282 12.0599 23.1282Z" fill="#0FA958" />
                            </svg>
                            ' . htmlspecialchars($featureTitle) . ': ' . htmlspecialchars($feature->value) . '
                        </li>';
                }
            }
    
            // Verificar se o plano já está assinado e ajustar o botão de acordo
            if ($userLogado && $assinatura && $assinatura->plano == $planId) {
                $botaoAssinarHtml = '<div class="btn btn-lg buttonplans" style="background-color: #0FA958; color: white; cursor: default;">Plano atual</div>';
            } else {
                $botaoAssinarHtml = '<a class="btn btn-lg btn-primary buttonplans" href="' . htmlspecialchars(URL) . '/assinar/' . strtolower($plan->url) . '">Assine agora!</a>';
            }
    
            // Montar o HTML do plano com as features e o botão de assinatura (ou "Plano atual")
            $plansHtml .= '
                <div class="col-md-3">
                    <div class="card cardplanos ' . strtolower($plan->titulo) . '">
                        <h3>' . htmlspecialchars($plan->titulo) . '</h3>
                        <span class="price">
                            <small class="cifraoprice">R$</small>
                            <big class="numprice">' . htmlspecialchars($plan->valor) . '</big>
                            <small class="periodprice">/mês</small>
                        </span>
                        <ul class="list-group list-group-flush">
                            ' . $featuresHtml . '
                        </ul>
                        ' . $botaoAssinarHtml . '
                    </div>
                </div>';
        }
    
        // Passa o HTML completo para a view
        return View::render('pages/modules/planoss', [
            'plansHtml' => $plansHtml
        ]);
    }
    
    



    public static function getPlan2($request, $plano)
    {


        $plano = EntityPlan::getPlanByUrl($plano);
        
        if (!$plano || $plano && $plano->status !== 'A') {
            return $request->getRouter()->redirect('/');
        }

        $pagseguro = new \PagSeguro();
        $session_ps = $pagseguro->getSession();
        $public_ps = $pagseguro->getPublicKey();

        $results = EntityPlan::getPlansFeat('id_plano = "' . $plano->id . '"', 'id ASC', '7');


        $itens = '';

        //RENDERIZA O ITEM
        while ($obPlanoFeat = $results->fetchObject(EntityPlan::class)) {

            // Faça algo com os resultados
            $postTitle = $obPlanoFeat->slug;
            $metaValue = $obPlanoFeat->value;

            switch($postTitle){
                case('itens_loja'):
                    $titulo = 'Itens na Loja';
                    break;
                case('fotos_itens_loja'):
                    $titulo = 'Fotos dos Itens';
                    break;
                case('descricao'):
                    $titulo = 'Descrição';
                    break;
                case('forma_pagamento'):
                    $titulo = 'Forma de Pagamento';
                    break;
                case('contato_vendedor'):
                    $titulo = 'Contato';
                    break;
                case('destaque_items_semana'):
                    $titulo = 'Destaques nas Semana';
                    break;
                case('story_dia'):
                    $titulo = 'Stories no dia';
                    break;
            }

            $itens .= "<b>" . $titulo . "</b> - Qtd: " . $metaValue . "<br>";
            // $itens[] = 'Itens na loja = ' . $obPlanoFeat->value;
            // $itens[] = 'Fotos dos Itens = ' . $obPlanoFeat->value;
            // $itens[] = 'Descrição = ' . $obPlanoFeat->value;
            // $itens[] = 'Forma de Pagamento = ' . $obPlanoFeat->value;
            // $itens[] = 'Contato = ' . $obPlanoFeat->value;
            // $itens[] = 'Destaques nas Semana = ' . $obPlanoFeat->value;
            // $itens[] = 'Stories no dia = ' . $obPlanoFeat->value;
        }



        $arrayView = [
            'logado' => SessionUserLogin::isLogged2() ? 'S' : 'N',
            'PLANO' => $plano->id,
            'plano_valor' => $plano->valor,
            'plano_valor_formatado' => number_format($plano->valor, 2, ',', '.'),
            'plano_nome' => $plano->titulo,
            'plano_descricao' => $plano->descricao,
            'plano_features' => $itens,
            'PAGSEGURO_SESSION' => $session_ps,
            'PAGSEGURO_PUBLICKEY' => $public_ps,
            'PAGSEGURO_ENV' => $pagseguro->_sandbox ? 'SANDBOX' : 'LIVE'
        ];




        $arrayView['USER_EMAIL'] = $usuario->email ?? '';
        $arrayView['USER_NOME'] = $usuario->nome ?? '';
        $arrayView['USER_CPF'] = $usuario->cpf ?? '';
        $arrayView['USER_DDD'] = substr($usuario->telefone ?? '', 0, 2);
        $arrayView['USER_TELEFONE'] = substr($usuario->telefone ?? '', 2, 11);
        $arrayView['USER_CEP'] = $usuario->cep ?? '';
        $arrayView['USER_ENDERECO'] = $usuario->endereco ?? '';
        $arrayView['USER_NUMERO'] = $usuario->numero ?? '';
        $arrayView['USER_COMPLEMENTO'] = $usuario->complemento ?? '';
        $arrayView['USER_BAIRRO'] = $usuario->bairro ?? '';
        $arrayView['USER_ESTADO'] = $usuario->estado ?? '';
        $arrayView['USER_CIDADE'] = $usuario->cidade ?? '';
        $arrayView['status'] = '';

        $content = View::render('pages/assinarefi', $arrayView);

        //RETORNA A VIEW DA PÁGINA

        return parent::getPage('Assinar Plano', $content);
    }

    public static function getPlan($request, $plano)
    {
        $id_user = $_SESSION['user']['usuario']['id'];

        $usuario = EntityUser::getUserById($id_user);
        $plano = EntityPlan::getPlanByUrl($plano);
        
        if (!$plano || $plano && $plano->status !== 'A') {
            return $request->getRouter()->redirect('/');
        }


        $results = EntityPlan::getPlansFeat('id_plano = "' . $plano->id . '"', 'id ASC', '7');


        $itens = '';

        //RENDERIZA O ITEM
        while ($obPlanoFeat = $results->fetchObject(EntityPlan::class)) {

            // Faça algo com os resultados
            $postTitle = $obPlanoFeat->slug;
            $metaValue = $obPlanoFeat->value;

            switch($postTitle){
                case('itens_loja'):
                    $titulo = 'Itens na Loja';
                    break;
                case('fotos_itens_loja'):
                    $titulo = 'Fotos dos Itens';
                    break;
                case('descricao'):
                    $titulo = 'Descrição';
                    break;
                case('forma_pagamento'):
                    $titulo = 'Forma de Pagamento';
                    break;
                case('contato_vendedor'):
                    $titulo = 'Contato';
                    break;
                case('destaque_items_semana'):
                    $titulo = 'Destaques nas Semana';
                    break;
                case('story_dia'):
                    $titulo = 'Stories no dia';
                    break;
            }

            $itens .= "<b>" . $titulo . "</b> - Qtd: " . $metaValue . "<br>";
        }



        $arrayView = [
            'logado' => SessionUserLogin::isLogged2() ? 'S' : 'N',
            'PLANO' => $plano->id,
            'plano_valor' => $plano->valor,
            'plano_valor_formatado' => number_format($plano->valor, 2, ',', '.'),
            'plano_nome' => $plano->titulo,
            'plano_descricao' => $plano->descricao,
            'plano_features' => $itens,
        ];




        $arrayView['email_cliente'] = $usuario->email ?? '';
        $arrayView['nome_cliente'] = $usuario->nome ?? '';
        $arrayView['USER_CPF'] = $usuario->cpf ?? '';
        $arrayView['USER_DDD'] = substr($usuario->telefone ?? '', 0, 2);
        $arrayView['USER_TELEFONE'] = $usuario->telefone;
        // $arrayView['USER_TELEFONE'] = substr($usuario->telefone ?? '', 2, 11);
        $arrayView['USER_CEP'] = $usuario->cep ?? '';
        $arrayView['USER_ENDERECO'] = $usuario->endereco ?? '';
        $arrayView['USER_NUMERO'] = $usuario->numero ?? '';
        $arrayView['USER_COMPLEMENTO'] = $usuario->complemento ?? '';
        $arrayView['USER_BAIRRO'] = $usuario->bairro ?? '';
        $arrayView['USER_ESTADO'] = $usuario->estado ?? '';
        $arrayView['USER_CIDADE'] = $usuario->cidade ?? '';
        $arrayView['status'] = '';

        $content = View::render('pages/assinarefi', $arrayView);

        //RETORNA A VIEW DA PÁGINA

        return parent::getPage('Assinar Plano', $content);

    }
}
