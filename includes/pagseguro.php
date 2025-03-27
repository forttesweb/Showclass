<?php
require_once __DIR__ . "/../vendor/autoload.php";
use \WilliamCosta\DotEnv\Environment;
use GuzzleHttp\Client;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use GuzzleLogMiddleware\LogMiddleware;
use GuzzleHttp\HandlerStack;

/**
 * Classe simples para integração com o checkout transparente do PagSeguro
 * Versão 2.1
 * Desenvolvedor: Gabriel Silva
 * Contato: 41 9 8788-5122
 */

class PagSeguro {

    private $_token = null;
    private $_email = null;
    public $_on = false;
    public $_logs = false;
    public $_sandbox = false;
    private $_logger = null;
    private $_loggerStack = null;
    private $_logname = '';

    private $client = null;

    private $_sdkUrl = 'sdk.pagseguro.com';
    private $_apiUrl = 'api.pagseguro.com';
    private $_notifyUrl = 'pagseguro.uol.com.br/v3/transactions/notifications';

	
	public function __construct() {

        //CARREGA VARIÁVEIS DE AMBIENTE
        Environment::load(__DIR__ . '/../');



        // Logs
        $this->_logs = (bool)getenv('PAGSEGURO_LOGS');
        // Sandbox
        $this->_sandbox = (bool)getenv('PAGSEGURO_SANDBOX');

        // Token
        $this->_token = '1319D7C807184AE4A945F0B957857B91';
        $this->_email = 'gui.s2.xp@gmail.com';

        // Configurar URLS
        $this->_sdkUrl = "https://" . ($this->_sandbox ? 'sandbox.' : "") . $this->_sdkUrl;
        $this->_apiUrl = "https://" . ($this->_sandbox ? 'sandbox.' : "") . $this->_apiUrl;
        // Url de consulta de notificações
        $this->_notifyUrl = "https://ws." . ($this->_sandbox ? 'sandbox.' : '') . $this->_notifyUrl;

        // Criar o Logger
        if($this->_logs){
            $this->_logger = new Logger("PAGSEGURO API");
            $this->_logname = $this->_sandbox ? 'pagseguro_sandbox.log' : 'pagseguro_producao.log';
            $this->_logger->pushHandler(new StreamHandler("./logs_pagseguro/{$this->_logname}"));
            $this->_loggerStack = HandlerStack::create();
            $this->_loggerStack->push(new LogMiddleware($this->_logger));
        }

        // Client do PagSeguro
        $this->client = new GuzzleHttp\Client([
            'handler' => $this->_logs ? $this->_loggerStack : '',
            'headers' => [
                'Authorization' => $this->_token,
                'Content-Type' => 'application/json',
                'x-api-version' => '4.0',
                //'x-idempotency-key' => ''
            ]
        ]);
	}

    /**
     * Checa se as configurações estão ativas.
     * Checa se o e-mail e token foram inseridos no painel de controle.
    */
    public function check(){

        if(empty($this->_token) || empty($this->_email))
            throw new Error("Por favor, informe o Token e Email da conta PagSeguro.");

        
    }


    
    /**
     * Solicita uma sessão no endpoint do pagseguro.
    */
    public function getSession(){

        $this->check();

        try{
            $sessaoResponse = $this->client->request('POST', "{$this->_sdkUrl}/checkout-sdk/sessions");
            $sessaoResponse = json_decode($sessaoResponse->getBody()->getContents());
            if($sessaoResponse && $sessaoResponse->session){
                return $sessaoResponse->session;
            }

            return null;

        }catch(Exception $e){
            throw new Error("Não foi possível solicitar a sessão junto ao PagSeguro.");
        }
    }

    /**
     * Retorna a chave pública necessária para o SDK e criptografia de cartão.
    */
    public function getPublicKey(){

        try{

            $publicKeys = $this->client->request('POST', "{$this->_apiUrl}/public-keys", [
                'body' => json_encode([
                    'type' => 'card'
                ])
            ]);

            $publicKeys = json_decode($publicKeys->getBody()->getContents());
            if($publicKeys && $publicKeys->public_key){
                return $publicKeys->public_key;
            }

            return null;

        }catch(Exception $e){
            throw new Error("Não foi possível obter a chave pública junto ao PagSeguro.");
        }

    }

    public function parcelas($bin, $valor = 0){
        try{

            $parcelas = $this->client->request('GET', "{$this->_apiUrl}/charges/fees/calculate", [
                'query' => [
                    'payment_methods' => 'CREDIT_CARD',
                    'value' => $valor,
                    'max_installments' => 12,
                    'max_installments_no_interest' => 0,
                    'credit_card_bin' => $bin
                ]
                ]);

            if($parcelas && $parcelas->getBody()){
                return json_decode($parcelas->getBody()->getContents());
            }

        }catch(Exception $e){
            throw new Error("Não foi possível obter as parcelas para este cartão.");
        }
    }


    public function pagamento(
        $pedido, 
        $comprador = array(),
        $endereco = array(), 
        $valor = 0, 
        $tipo = "PIX", 
        $estabelecimento = "Developer", 
        $webhook,
        $cartaoCriptografado = null, 
        $tipoCartao = "CREDIT_CARD",
        $recorrente = false,
        $subsequente = false
    ){
    
        $valorCentavos = $valor * 100;

        try{

            // Montar o objeto de  cobrança do Orders.
            $dadosCobranca = [
                "reference_id" => $pedido,
                "customer" => $comprador,
                "items" => [
                    [
                        "reference_id" => $pedido,
                        "name" => "Pagamento do pedido",
                        "quantity" => 1,
                        "unit_amount" => $valorCentavos
                    ]
                ],
                "qr_codes" => [
                    [
                        "amount" => ["value" => $valorCentavos]
                    ]
                ],
                
                "notification_urls" => [
                    $webhook
                    //"https://webhook.site/5e2d0553-ae4d-4d6c-b279-4d018a09c5ab"
                ]
            ];

            

            if($tipo === "PIX"){
                $data = new DateTime();
                $data->setTimezone(new DateTimeZone("-03:00"));
                $data->modify("+30 minutes");

                $dadosCobranca["qr_codes"][0]["expiration_date"] = $data->format("Y-m-d\TH:i:sP");
            }

            // Cartão de crédito ou débito.
            if($tipo === "CARTAO"){
                $dadosCobranca["charges"] = [
                    [
                        "reference_id" => "{$pedido}",
                        "description" => "Loja {$estabelecimento}",
                        "amount" => [
                            "value" => $valorCentavos,
                            "currency" => "BRL"
                        ],
                        "payment_method" => [

                            "type" => $tipoCartao,
                            "installments" => 1,
                            "capture" => true,
                            /*"authentication_method" => [
                                "type" => "THREEDS",
                                "id" => $idAutorizacao
                            ],*/
                            "soft_descriptor" => $estabelecimento,
                        ],
                    ]

                    
                ];

                if($recorrente){
                    $dadosCobranca["charges"][0]["recurring"] = [
                        "type" => $subsequente ? 'SUBSEQUENT' : 'INITIAL'
                    ];
                    if($subsequente){
                        $dadosCobranca["charges"][0]["payment_method"]["card"]["id"] = $cartaoCriptografado;
                    }else {
                        $dadosCobranca["charges"][0]["payment_method"]["card"] = [
                            "encrypted" => $cartaoCriptografado,
                            "store" => true,
                        ];
                    }
                }


            }

            if($tipo === 'BOLETO'){
                $data = new DateTime();
                $data->setTimezone(new DateTimeZone("-03:00"));
                $data->modify("+3 days");

                $dadosCobranca['charges'] = [
                    [
                        'reference_id' => $pedido,
                        'description' => 'Pagamento de assinatura',
                        'amount' => [
                            'value' => $valorCentavos,
                            'currency' => 'BRL'
                        ],
                        'payment_method' => [
                            'type' => 'BOLETO',
                            'boleto' => [
                                'due_date' => $data->format('Y-m-d'),
                                'holder' => [
                                    'name' => $comprador['name'],
                                    'tax_id' => $comprador['tax_id'],
                                    'email' => $comprador['email'],
                                    'address' => $endereco,
                                ]
                            ]
                        ]
                    ]
                ];
            
            }



            $order = $this->client->request("POST", "{$this->_apiUrl}/orders", [
                'body' => json_encode($dadosCobranca)
            ]);

            if($order && $order->getBody()){
                $respostaOrder = json_decode($order->getBody()->getContents());
            
                return $respostaOrder;
            }

        }catch(GuzzleHttp\Exception\ClientException $e){
            $response = $e->getResponse();
            $response = json_decode($response->getBody()->getContents());

            $erros = array();
            foreach($response->error_messages as $error){
                $erros[] = "Código: {$error->code}, descrição: {$error->description}, parâmetro: {$error->parameter_name}";
            }

            return (Object)["erro" => implode("<br>", $erros)];
        }catch(Exception $e){
            return (Object)["erro" => $e->getMessage()];
        }
    }

    public function consultarNotificacao($codigo){
        try{

            $notificacao = $this->client->request("GET", "{$this->_notifyUrl}/{$codigo}?email={$this->_email}&token={$this->_token}");

            $xml = simplexml_load_string($notificacao->getBody()->getContents());
            return $xml;

        }catch(Exception $e){
            return (Object)["erro" => $e->getMessage()];
        }
    }

    public function psStatus($statuss = 1, $text = false, $int = true){
        $status = "pending";
        $text = "Aguardando pagamento";
        switch($statuss){
            case 0:
                $status = 'pending';
                $text = 'Aguardando Pagamento';
                break;
            case 1: 
                $status = "pending";
                $text = "Aguardando pagamento";
                break;
            case 2: 
                $status = "in_process";
                $text = "Em análise";
                break;
            case 3: 
                $status = "authorized";
                $text = "Paga";
                break;
            case 4: 
                $status = "approved";
                $text = "Disponível";
                break;
            case 5: 
                $status = "in_mediation";
                $text = "Em disputa";
                break;
            case 6:
                $status = "refunded";
                $text = "Devolvida";
                break;
            case 7:
                $status = "cancelled";
                $text = "Cancelada";
                break;
            default: 
                $status = "undefined";
                $text = "Não tratado";
                break;
        }
    
        if($text) return $text;
        if($int) return $statuss;
        
        return $status;
    }

    



}
