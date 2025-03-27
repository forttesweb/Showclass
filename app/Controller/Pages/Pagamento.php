<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\Pagamento as EntityPagamento;
use \App\Session\User\Login as SessionUserLogin;
use \WilliamCosta\DatabaseManager\Pagination;
use \App\Model\Entity\Assinatura as EntityAssinatura;
use \App\Model\Entity\Plan as EntityPlan;
use \App\Model\Entity\User as EntityUser;
use NumberFormatter;

use Gerencianet\Exception\GerencianetException;
use Gerencianet\Gerencianet;


class Pagamento extends Page
{


    public static function getNotification($request)
    {

        $postVars = $request->getPostVars();

        // $options = [
        //     "client_id" => "Client_Id_296b41368515dfab4c7b795a64012acc808b304f",
        //     "client_secret" => "Client_Secret_394dc1a6e20805e5fd171063ff53daabc33a934d",
        //     "certificate" => ("" . getEnv('URL') . "/public/certs/homologacao-314528-ESPLANDA.p12"), // Absolute path to the certificate in .pem or .p12 format
        //     "sandbox" => true,
        //     "debug" => false,
        //     "timeout" => 30
        // ];

        //MEUS DADOS (GUILHERME)
        // $options = [
        //     "client_id" => "Client_Id_6bc794e25552f00a70c0a25af83af90f5eea87eb",
        //     "client_secret" => "Client_Secret_adfbd943dbd14d117c0bfb549678409275088349",
        //     "certificate" => ("" . getEnv('URL') . "/public/certs/producao-314528-ESPLANADA_PROD.p12"), // Absolute path to the certificate in .pem or .p12 format
        //     "sandbox" => false,
        //     "debug" => false,
        //     "timeout" => 30
        // ];
        $options = [
            "client_id" => "Client_Id_b772ea1c5dbde585a6de36dfb245b9b4c1587646",
            "client_secret" => "Client_Secret_cc4e35d1c38a975136f61a631311ac6172fd2ec4",
            "certificate" => ("" . getEnv('URL') . "/public/certs/producao-505346-showclass"), // Absolute path to the certificate in .pem or .p12 format
            "sandbox" => false,
            "debug" => false,
            "timeout" => 30
        ];

        $params = [
            "token" => $postVars["notification"]
        ];

        try {
            $api = new Gerencianet($options);
            $chargeNotification = $api->getNotification($params, []);
            // $api = new Gerencianet($options);
            // $response = $api->getNotification($params);
            // Para identificar o status atual da sua transação você deverá contar o número de situações contidas no array, pois a última posição guarda sempre o último status. Veja na um modelo de respostas na seção "Exemplos de respostas" abaixo.

            // Veja abaixo como acessar o ID e a String referente ao último status da transação.

            // Conta o tamanho do array data (que armazena o resultado)
            $i = count($chargeNotification["data"]);
            // Pega o último Object chargeStatus
            $ultimoStatus = $chargeNotification["data"][$i - 1];
            // Acessando o array Status
            $status = $ultimoStatus["status"];
            // Obtendo o ID da transação    
            $charge_id = $ultimoStatus["identifiers"]["charge_id"];
            // Obtendo a String do status atual
            $statusAtual = $status["current"];

            $identifiers = $ultimoStatus["identifiers"];
            // Com estas informações, você poderá consultar sua base de dados e atualizar o status da transação especifica, uma vez que você possui o "charge_id" e a String do STATUS

            // echo "O id da transação é: " . $charge_id . " seu novo status é: " . $statusAtual;

            self::updateAssinatura($identifiers, $statusAtual);

            exit;
            //print_r($chargeNotification);
        } catch (EfíException $e) {
            print_r($e->code);
            print_r($e->error);
            print_r($e->errorDescription);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
    }

    public static function updateAssinatura($identifiers, $statusAtual)
    {


        $obAssinatura = EntityAssinatura::getAssinaturaBySubsId($identifiers['subscription_id']);
        $obAssinatura->status = $statusAtual;
        $obAssinatura->atualizar();
        
        return true;
        //self::updateUserMeta($obAssinatura->user, $statusAtual);
    }
    
    public static function updateUserMeta($user, $statusAtual){
        $user_id = $user;  // ID do usuário que você quer atualizar
        $meta_key = 'assinatura';  // A chave do metafield que você quer atualizar
        $meta_value = $statusAtual;  // O novo valor que você quer atribuir ao metafield
        
        $url = "https://esplanadaprojetos.com.br/wp-json/wp/v2/users/{$user_id}";
        
        $data = array(
            'meta' => array(
                $meta_key => $meta_value
            )
        );
        
        // Configurar a requisição
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');  // Você pode usar PUT ou POST aqui, dependendo da configuração da sua API
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            // Se necessário, inclua um cabeçalho de autenticação (por exemplo, Basic Auth ou Bearer Token)
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczovL2VzcGxhbmFkYXByb2pldG9zLmNvbS5iciIsImlhdCI6MTY5NTQxNTcyMiwibmJmIjoxNjk1NDE1NzIyLCJleHAiOjE2OTYwMjA1MjIsImRhdGEiOnsidXNlciI6eyJpZCI6IjI1NiJ9fX0.BumXMxxQtyNY54SiMlCp5HlrC7AAi8t-vKaNt4352BY'
        ));
        
        // Enviar a requisição
        $response = curl_exec($ch);
        
        // Verificar a resposta
        // $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // if ($http_code == 200) {
        //     echo 'Metafield atualizado com sucesso!';
        // } else {
        //     echo $response;
        //     echo '<br><br><br><br>Erro ao atualizar o metafield. Código de status: ' . $http_code;
        // }
        
        // Fechar a conexão
        curl_close($ch);
    }

    /**
     * Método responsável por obter a renderização dos itens de pagamentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getPagamentoItems($request, &$obPagination)
    {

        $dados_user = SessionUserLogin::dadosLogado();

        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityAssinatura::getAssinaturas('user = ' . $dados_user->id, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 5);


        //RESULTADOS DA PAGINA
        $results = EntityAssinatura::getAssinaturas('user = ' . $dados_user->id, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($ObAssinatura = $results->fetchObject(EntityPagamento::class)) {

            $obPlano = EntityPlan::getPlanByIdKey($ObAssinatura->plano);

            switch ($ObAssinatura->status) {
                case ("waiting"):
                    $status = "<span class='text-info'>Aguardando pagamento</span>";
                    break;
                case ("unpaid"):
                    $status = "<span class='text-danger'>Pagamento não aprovado</span>";
                    break;
                case ("paid"):
                    $status = "<span class='text-success'>Pagamento aprovado</span>";
                    break;
                case ("canceled"):
                    $status = "<span class='text-warning'>Assinatura cancelada</span>";
                    break;
                default:
                    $status = "Aguardando pagamento";
                    break;
            }



            //VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/modules/pagamentos/item', [
                'id' => $ObAssinatura->id,
                'subscription_id' => $ObAssinatura->subscription_id,
                'titulo' => $obPlano->titulo,
                'valor' => number_format($obPlano->valor, 2, ',', '.'),
                'status' => $status,
                'data' => date('d/m/Y H:i:s', strtotime($ObAssinatura->data)),
            ]);
        }

        if (empty($itens)) {
            return $itens = "<tr><td><h3>Nenhum pagamento encontrado</h3></td></tr>";
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a vie de listagem de pagamentos
     *
     * @param Request $request
     * @return string
     */
    public static function getPagamentos($request)
    {

        $dados_user = SessionUserLogin::dadosLogado();


        //CONTEUDO DA HOME
        $content = View::render('pages/modules/pagamentos/index', [
            'id' => $dados_user->id,
            'itens' => self::getPagamentoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status'     => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Pagamentos', $content, 'pagamentos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo pagamento
     *
     * @param Request $request
     * @return string
     */
    public static function getNewPagamento($request)
    {

        $dados_user = SessionUserLogin::dadosLogado();

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('pages/modules/pagamentos/form', [
            'title'    => 'Cadastrar pagamento',
            'nome_cliente'     => $dados_user->name,
            'email_cliente' => $dados_user->email,
            'status'   => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar pagamento', $content, 'pagamentos');
    }

    /**
     * Método responsável por cadastrar um pagamento no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewPagamento($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        // if (file_exists($options = realpath(__DIR__ . "/../../credentials/options.php"))) {
        // 	require_once $options;
        // }
        // $options = [
        //     "client_id" => "Client_Id_296b41368515dfab4c7b795a64012acc808b304f",
        //     "client_secret" => "Client_Secret_394dc1a6e20805e5fd171063ff53daabc33a934d",
        //     "certificate" => ("" . getEnv('URL') . "/public/certs/homologacao-314528-ESPLANDA.p12"), // Absolute path to the certificate in .pem or .p12 format
        //     "sandbox" => true,
        //     "debug" => false,
        //     "timeout" => 30
        // ];

        //MEUS DADOS (GUILHERME)
        // $options = [
        //     "client_id" => "Client_Id_6bc794e25552f00a70c0a25af83af90f5eea87eb",
        //     "client_secret" => "Client_Secret_adfbd943dbd14d117c0bfb549678409275088349",
        //     "certificate" => ("" . getEnv('URL') . "/public/certs/producao-314528-ESPLANADA_PROD.p12"), // Absolute path to the certificate in .pem or .p12 format
        //     "sandbox" => false,
        //     "debug" => false,
        //     "timeout" => 30
        // ];
        $options = [
            "client_id" => "Client_Id_b772ea1c5dbde585a6de36dfb245b9b4c1587646",
            "client_secret" => "Client_Secret_cc4e35d1c38a975136f61a631311ac6172fd2ec4",
            "certificate" => ("" . getEnv('URL') . "/public/certs/producao-505346-showclass"), // Absolute path to the certificate in .pem or .p12 format
            "sandbox" => false,
            "debug" => false,
            "timeout" => 30
        ];

        $params = [
            "limit" => 20,
            "offset" => 0,
            "name" => $postVars['plano']
        ];

        $body = [
            "name" => $postVars['plano'],
            "interval" => 1,
            "repeats" => null
        ];

        try {
            $api = new Gerencianet($options);


            $response = $api->listPlans($params);

            if (empty($response['data'])) {

                $response = $api->createPlan($params = [], $body);

                self::setPagamento($request, $response);
            }



            self::setPagamento($request, $response);


            //print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
        } catch (GerencianetException $e) {
            print_r($e->code);
            print_r($e->error);
            print_r($e->errorDescription);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }

        // $params = [
        //     "id" => 1 // plan_id
        // ];

        // $items = [
        //     [
        //         "name" => "Product 1",
        //         "amount" => 1,
        //         "value" => 1000
        //     ],
        //     [
        //         "name" => "Product 2",
        //         "amount" => 2,
        //         "value" => 2000
        //     ]
        // ];

        // $shippings = [ // Optional
        //     [
        //         "name" => "Shipping to City",
        //         "value" => 2000
        //     ]
        // ];

        // $metadata = [
        //     "custom_id" => "Order_00001",
        //     "notification_url" => "https://esplanada.mkwebdesigner.com//notification/"
        // ];

        // $body = [
        //     "items" => $items,
        //     "shippings" => $shippings,
        //     "metadata" => $metadata
        // ];

        // try {
        //     $api = new Gerencianet($options);
        //     $response = $api->createSubscription($params, $body);

        //     print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
        // } catch (GerencianetException $e) {
        //     print_r($e->code);
        //     print_r($e->error);
        //     print_r($e->errorDescription);
        // } catch (Exception $e) {
        //     print_r($e->getMessage());
        // }

        //NOVA INSTÃNCIA DE DEPOIMENTO
        // $obPagamento = new EntityPagamento;
        // $obPagamento->nome = $postVars['nome'] ?? '';
        // $obPagamento->mensagem = $postVars['mensagem'] ?? '';
        // $obPagamento->cadastrar();

        // //REDIRECIONA  O USUARIO
        // $request->getRouter()->redirect('/admin/pagamentos/' . $obPagamento->id . '/edit?status=created');
    }

    public static function setPagamento($request, $response)
    {

        $postVars = $request->getPostVars();

        $obPlano = EntityPlan::getPlanByUrl($response['data'][0]['name']);

        $dadosUser = SessionUserLogin::dadosLogado();

        // $options = [
        //     "client_id" => "Client_Id_296b41368515dfab4c7b795a64012acc808b304f",
        //     "client_secret" => "Client_Secret_394dc1a6e20805e5fd171063ff53daabc33a934d",
        //     "certificate" => ("" . getEnv('URL') . "/public/certs/homologacao-314528-ESPLANDA.p12"), // Absolute path to the certificate in .pem or .p12 format
        //     "sandbox" => true,
        //     "debug" => false,
        //     "timeout" => 30
        // ];

        //MEUS DADOS (GUILHERME)
        // $options = [
        //     "client_id" => "Client_Id_6bc794e25552f00a70c0a25af83af90f5eea87eb",
        //     "client_secret" => "Client_Secret_adfbd943dbd14d117c0bfb549678409275088349",
        //     "certificate" => ("" . getEnv('URL') . "/public/certs/producao-314528-ESPLANADA_PROD.p12"), // Absolute path to the certificate in .pem or .p12 format
        //     "sandbox" => false,
        //     "debug" => false,
        //     "timeout" => 30
        // ];
        $options = [
            "client_id" => "Client_Id_b772ea1c5dbde585a6de36dfb245b9b4c1587646",
            "client_secret" => "Client_Secret_cc4e35d1c38a975136f61a631311ac6172fd2ec4",
            "certificate" => ("" . getEnv('URL') . "/public/certs/producao-505346-showclass"), // Absolute path to the certificate in .pem or .p12 format
            "sandbox" => false,
            "debug" => false,
            "timeout" => 30
        ];

        $params = [
            "id" => $response['data'][0]['plan_id'] // plan_id
        ];

        $valor = str_replace(array('.', '-', '/'), "", $obPlano->valor);
        $cpf = str_replace(array('.', '-'), "", $postVars['customer-cpf']);
        
        $telefone = str_replace(array('(', '-',')'), "", $postVars['customer-phone_number']);

        $items = [
            [
                "name" => $response['data'][0]['name'],
                "amount" => 1,
                "value" => (int)$valor
            ]
        ];

        $paymentToken = $postVars['paymentToken'];

        $data_nascimento = date("Y-m-d", strtotime($postVars['customer-birth']));

        $customer = [
            "name" => $postVars['customer-name'],
            "cpf" => $cpf,
            "phone_number" => $telefone,
            "email" => $postVars['customer-email'],
            "birth" => $data_nascimento
        ];

        $billingAddress = [
            "street" => "Rua Itaú",
            "number" => "601",
            "neighborhood" => "Renascer",
            "zipcode" => "32687405",
            "city" => "Betim",
            "complement" => "Casa 42",
            "state" => "MG",
        ];

        $metadata = array('notification_url' => 'https://esplanada.mkwebdesigner.com/pages/pagamentos/notification');

        $body = [
            "items" => $items,
            "payment" => [
                "credit_card" => [
                    "billing_address" => $billingAddress,
                    "payment_token" => $paymentToken,
                    "customer" => $customer
                ]
            ],
            "metadata" => $metadata
        ];


        // Cria a assinatura inicial



        try {
            $api = new Gerencianet($options);
            $response = $api->createOneStepSubscription($params, $body);

            $diaexpiracao = date('Y-m-d H:i:s');

            $expira = strtotime("+30 day $diaexpiracao");

            $data_expiracao = date('Y-m-d H:i:s', $expira);

            $assinatura = new EntityAssinatura;
            $assinatura->plano = $obPlano->id_key;
            $assinatura->user = $dadosUser->id;
            $assinatura->status = $response['data']['charge']['status'];
            // $assinatura->status = $response['data']['charge']['status'];
            $assinatura->metodo = $response['data']['payment'];
            $assinatura->subscription_id = $response['data']['subscription_id'];
            $assinatura->expiracao = $data_expiracao;
            $assinatura->cartao_id = $postVars['mascaraCartao'];
            $assinatura->cadastrar();

            $obUser = EntityUser::getUserByWpId($dadosUser->id);

            if (!$obUser instanceof EntityUser) {
                $obUser = new EntityUser;
                $obUser->id_wordpress = $dadosUser->id;
                $obUser->nome = $dadosUser->name;
                $obUser->email = $dadosUser->email;
                $obUser->telefone = $postVars['customer-phone_number'];
                $obUser->cadastrar_wp();
            }



            switch ($response['data']['charge']['status']) {
                case ("waiting"):
                    $status = "Aguardando pagamento";
                    break;
                case ("unpaid"):
                    $status = "Pagamento não aprovado";
                    break;
                default:
                    $status = "Aguardando pagamentooo";
                    break;
            }

            $valor2 = str_replace('.', '', $response['data']['total']);

            $response['data']['status_pag'] = $status;
            //$response['data']['valor_total'] = self::brl2decimal($obPlano->valor, 2);
            $response['data']['valor_total'] = self::currency($obPlano->valor);
            //$response['data']['valor_total'] = number_format($valor2, 2, ',', '.');


            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            exit;
            //print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
        } catch (GerencianetException $e) {
            // echo json_encode($e->code);
            // echo json_encode($e->error);
            // echo json_encode($e->errorDescription);

            print_r($e->code);
            print_r($e->error);
            print_r($e->errorDescription);

            exit;
            // print_r($e->code);
            // print_r($e->error);
            // print_r($e->errorDescription);
        } catch (Exception $e) {
            echo json_encode($e->getMessage());
            //print_r($e->getMessage());
        }
    }

    public static function setCancelAssinatura($request)
    {

        $options = [
            "client_id" => "Client_Id_b772ea1c5dbde585a6de36dfb245b9b4c1587646",
            "client_secret" => "Client_Secret_cc4e35d1c38a975136f61a631311ac6172fd2ec4",
            "certificate" => ("" . getEnv('URL') . "/public/certs/producao-505346-showclass"), // Absolute path to the certificate in .pem or .p12 format
            "sandbox" => false,
            "debug" => false,
            "timeout" => 30
        ];


        $postVars = $request->getPostVars();

        $id_cliente = $postVars['id_cliente'];

        $obAssinatura = EntityAssinatura::getAssinaturaByUserId($id_cliente);

        if (!$obAssinatura instanceof EntityAssinatura) {
            http_response_code(400);
            $response['msg'] = 'Assinatura não encontrada.';
            exit(json_encode($response));
        }


        $params = [
            "id" => $obAssinatura->subscription_id
        ];

        try {
            $api = new Gerencianet($options);
            $response = $api->cancelSubscription($params);

            $response['data'] = $response;
            http_response_code(200);
            exit(json_encode($response));

            // print_r("<pre>" . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</pre>");
        } catch (GerencianetException $e) {
            print_r($e->code);
            print_r($e->error);
            print_r($e->errorDescription);
        } catch (Exception $e) {
            print_r($e->getMessage());
        }
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
            case 'noplan':
                return Alert::getError('Necessário um plano ativo para acessar as notificações!');
                break;
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um pagamento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditPagamento($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPagamento = EntityPagamento::getPagamentoById($id);

        //VALIDA A INSTANCIA
        if (!$obPagamento instanceof EntityPagamento) {
            $request->getRouter()->redirect('/admin/pagamentos');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/pagamentos/form', [
            'title'    => 'Editar pagamento',
            'nome'     => $obPagamento->nome,
            'mensagem' => $obPagamento->mensagem,
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar pagamento', $content, 'pagamentos');
    }

    /**
     * Método responsável por gravar a atualizaçõ de um pagamento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditPagamento($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPagamento = EntityPagamento::getPagamentoById($id);

        //VALIDA A INSTANCIA
        if (!$obPagamento instanceof EntityPagamento) {
            $request->getRouter()->redirect('/admin/pagamentos');
        }

        //POST VARS
        $postVars = $request->getPostVars();

        //ATUALIZA A INSTÂNCIA
        $obPagamento->nome = $postVars['nome'] ?? $obPagamento->nome;
        $obPagamento->mensagem = $postVars['mensagem'] ?? $obPagamento->mensagem;
        $obPagamento->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin/pagamentos/' . $obPagamento->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um pagamento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeletePagamento($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPagamento = EntityPagamento::getPagamentoById($id);

        //VALIDA A INSTANCIA
        if (!$obPagamento instanceof EntityPagamento) {
            $request->getRouter()->redirect('/admin/pagamentos');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/pagamentos/delete', [
            'nome'     => $obPagamento->nome,
            'mensagem' => $obPagamento->mensagem
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir pagamento > GUIDEV', $content, 'pagamentos');
    }

    /**
     * Método responsável por excluir um pagamento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeletePagamento($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obPagamento = EntityPagamento::getPagamentoById($id);

        //VALIDA A INSTANCIA
        if (!$obPagamento instanceof EntityPagamento) {
            $request->getRouter()->redirect('/admin/pagamentos');
        }

        //EXCLUIR O DEPOIMENTO
        $obPagamento->excluir();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin/pagamentos?status=deleted');
    }

    private static function brl2decimal($brl, $casasDecimais = 2)
    {
        // Se já estiver no formato USD, retorna como float e formatado
        if (preg_match('/^\d+\.{1}\d+$/', $brl))
            return (float) number_format($brl, $casasDecimais, '.', '');
        // Tira tudo que não for número, ponto ou vírgula
        $brl = preg_replace('/[^\d\.\,]+/', '', $brl);
        // Tira o ponto
        $decimal = str_replace('.', '', $brl);
        // Troca a vírgula por ponto
        $decimal = str_replace(',', '.', $decimal);
        return (float) number_format($decimal, $casasDecimais, ',', '');
    }
    private static function currency(float $val): string
    {
        setlocale(LC_ALL, 'pt_BR');

        $fmt = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
        $locale = localeconv();
        return $fmt->formatCurrency($val, "BRL");
    }
}
