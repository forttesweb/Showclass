<?php

namespace App\Controller\Pages;
use \App\Utils\View;
use \App\Model\Entity\Plan as EntityPlan;
use \App\Model\Entity\User;
use \App\Model\Entity\Assinatura as EntityAssinatura;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use \WilliamCosta\DotEnv\Environment;

use App\Session\User\Login as SessionUserLogin;

require_once __DIR__ . "/../../../includes/pagseguro.php";

class Assinatura extends Page

{

    public static function parcelas($request){
        $pagseguro = new \PagSeguro();

        $parcelas = $pagseguro->parcelas($_POST['bin'], $_POST['valor']);
        return \json_encode($parcelas);
    }

    /**
     * Função do Webhook responsável por atualizar status das assinaturas após pagamento.
     */
    public static function webhook($request){

        $vars = $request->getPostVars();

        $assinatura = EntityAssinatura::getAssinaturaById($vars['reference_id']);

        if($assinatura){
            $usuario = User::getUserById($assinatura->user);
            if($vars['charges'][0]['status'] === 'PAID' || $vars['charges'][0]['status'] === 'AUTHORIZED'){
                if($assinatura->status !== 'PAID'){
                    $assinatura->status = 'PAID';
                    $dataExpiracao = new \DateTime();
                    $dataExpiracao->setTimezone(new \DateTimeZone("-03:00"));
                    $dataExpiracao->modify("+1 month");
                    $assinatura->expiracao = $dataExpiracao->format('Y-m-d H:i:s');
                    $assinatura->atualizar();
                }
            } else {
                $assinatura->status = 'CANCELLED';
                $assinatura->atualizar();
            }
        }

        \http_response_code(200);
        echo "Tudo certo.";

    }

    public static function cancelarRecorrencia($request){

        if(!SessionUserLogin::isLogged()){
            return \json_encode(['status' => false, 'msg' => 'Não autenticado.']);
        }

        $vars = $request->getPostVars();

        $assinatura = EntityAssinatura::getAssinaturaById($vars['id']);

 
        if(!$assinatura || $assinatura && $assinatura->user !== $_SESSION['user']['usuario']['id']){
            return \json_encode(['status' => false, 'msg' => 'Assinatura não encontrada.']);
        }

        $assinatura->cartao_id = null;
        $assinatura->atualizar();

        return \json_encode(['status' => true, 'msg' => 'Recorrencia desativada com sucesso.']);

    }

    /**
     * Função responsável por verificar a validade das assinaturas.
     * Para assinaturas recorrentes, o sistema tenta efetuar uma nova cobrança.
     */
    public static function verify(){

        $pagseguro = new \PagSeguro();
        $assinaturas = EntityAssinatura::getAssinaturas('expiracao < CURDATE() AND status = "PAID"');

        http_response_code(200);
        echo "OK";

        while ($assinatura = $assinaturas->fetchObject(EntityAssinatura::class)) {
            $usuario = User::getUserById($assinatura->user);
            $plano = EntityPlan::getPlanoById($assinatura->plano);
            
            // Se o cartão id for diferente de vazio, significa que é recorrente e tenta pagar a assinatura.
            if(!empty($assinatura->cartao_id)){
                // Tenta fazer o pagamento;
                // Array do comprador para o pagseguro
                $comprador = array(
                    'name' => $usuario->nome,
                    'email' => $usuario->email,
                    'tax_id' => $usuario->cpf,
                    'phones' => [
                        [
                            'country' => '55',
                            'area' => substr($usuario->telefone, 0, 2),
                            'number' => substr($usuario->telefone, 2, 11),
                            'type' => 'MOBILE'
                        ]
                    ]
                );

                $cartao = $pagseguro->pagamento($assinatura->id, $comprador, array(), $plano->valor, "CARTAO", "DecolandoJuntos", "https://webhook.site/2a4a329d-2cd0-40b1-bbbe-809c6133e1cc", $assinatura->cartao_id, 'CREDIT_CARD', true, true);

                $erro = false;
                if(isset($cartao->erro)){
                    //$assinatura->excluir();
                    $assinatura->status = 'EXPIRED';
                    $assinatura->atualizar();
                    $erro = true;
                }


                if(!$erro){
                    // Se foi autorizado ou foi pago, atualiza a assinatura imediatamente.
                    if($cartao->charges[0]->status === 'AUTHORIZED' || $cartao->charges[0]->status === 'PAID'){
                        $assinatura->status = 'PAID';
                        $dataExpiracao = new \DateTime();
                        $dataExpiracao->setTimezone(new \DateTimeZone("-03:00"));
                        $dataExpiracao->modify("+1 month");

                        $assinatura->expiracao = $dataExpiracao->format('Y-m-d H:i:s');
                        $assinatura->atualizar();
                    } else {
                        // Enviar e-mail de erro.
                        $erro = true;
                    }
                }

                // Enviar e-mail de erro.
                if($erro){
                    try{
                        $mail = new PHPMailer(true);
                        $mail->SMTPDebug = null;
                        $mail->isSMTP();
                        $mail->Host = getenv('SMTP_HOST');
                        $mail->SMTPAuth = true;
                        $mail->Username = getenv('SMTP_USERNAME');
                        $mail->Password = getenv('SMTP_PASSWORD');
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                        $mail->Port = 465;
                        $mail->CharSet = "UTF-8";
                        $mail->setFrom(getenv('SMTP_FROM'), getenv('SMTP_FROM_NAME'));

                        $mail->addAddress($usuario->email, $usuario->nome);

                        $mail->isHTML(true);
                        $mail->Subject = 'Renovação de Assinatura';
                        $mail->AltBody = 'Falha de renovação';

                        $content = View::render('email/assinatura_erro',['usuario_nome' => $usuario->nome, 'url' => getenv('URL') . '/assinar/' . $assinatura->plano]);

                        $mail->Body = $content;

                        $mail->send();

                    } catch(\Exception $e){
                        echo $e->getMessage();
                    }
                }
                


            }

            // Foi de outra maneira.
            $assinatura->status = 'EXPIRED';
            $assinatura->atualizar();

        }



    }

    /**
     * Função responsável por processar o pagamento de uma assinatura.
     */
    public static function pagamento($request){

       
        $pagseguro = new \PagSeguro();
        $vars = $request->getPostVars();
        
        $metodo = $_POST['paymentMethod'] ?? '';

        if(empty($metodo) || empty($vars['plano'] ?? ''))
            return \json_encode(['status' => false, 'msg' => 'Método de pagamento inválido.']);
        
        $plano = EntityPlan::getPlanoById($vars['plano']);
        if(!$plano || $plano && $plano->status !== 'A')
            return \json_encode(['status' => false, 'msg' => 'Plano não encontrado ou não esta ativo.']);

        if($metodo === 'creditCard' && empty($vars['encryptedCard'] ?? ''))
            return \json_encode(['status' => false, 'msg' => 'Verifique os dados do cartão e tente novamente.']);

        $usuario = null;

        $reload = false;



        // Verifica se precisa criar um novo usuário ou se só precisa logar.
        if(!SessionUserLogin::isLogged2()){

            if(isset($vars['tipo_conta'])){
                $tipoConta = $vars['tipo_conta'];
                

                $user = User::getUserByEmail($vars['email'] ?? '');

                // Usuário existente
                if($tipoConta === 'existente'){
                    $user = User::getUserByEmail($vars['email'] ?? '');
                    if($user){
                        if(password_verify($vars['senha'], $user->senha)){
                            $usuario = $user;
                        } else {
                            return \json_encode(['status' => false, 'msg' => 'Usuário e/ou senha incorretos.']);
                        }
                    } else {
                        return \json_encode(['status' => false, 'msg' => 'Usuário e/ou senha incorretos.']);
                    }
                    // NOvo usuário
                } else if($tipoConta === 'novo'){
                    $email = $vars['email'] ?? '';
                    $senha = $vars['senha'] ?? '';
                    $senha_repetir = $vars['senha_repetir'] ?? '';
                    $cpf = $vars['senderCPF'] ?? '';
                    $ddd = $vars['senderAreaCode'] ?? '';
                    $telefone = $vars['senderPhone'] ?? '';
                    $nome = $vars['senderName'] ?? '';
                    $cep = $vars['shippingAddressPostalCode'] ?? '';
                    $endereco = $vars['shippingAddressStreet'] ?? '';
                    $numero = $vars['shippingAddressNumber'] ?? '';
                    $complemento = $vars['shippingAddressComplement'] ?? '';
                    $bairro = $vars['shippingAddressDistrict'] ?? '';
                    $cidade = $vars['shippingAddressCity'] ?? '';
                    $estado = $vars['shippingAddressState'] ?? '';

                    $alreadyExists = User::getUserByEmail($email);
                    if($alreadyExists){
                        return \json_encode(['status' => false , 'msg' => 'Já existe um usuário com este e-mail!']);
                    }

                    if($senha !== $senha_repetir){
                        return \json_encode(['status' => false, 'msg' => 'As senhas não conferem.']);
                    }

                    if(empty($email) || empty($senha) || empty($senha_repetir) ||
                     empty($cpf) || empty($ddd) || empty($telefone) || empty($nome) || empty($cep) || empty($endereco)
                        || empty($numero) || empty($complemento) || empty($bairro) || empty($cidade) || empty($estado)

                    ){
                        return \json_encode(['status' => false, 'msg' => 'Verifique todos os campos e tente novamente.']);
                    }

                    // Cadastrar o novo usuário
                    $userOb = new User;
                    $userOb->nome = $nome;
                    $userOb->email = $email;
                    $userOb->cpf = $cpf;
                    $userOb->telefone = "{$ddd}{$telefone}";
                    $userOb->senha = password_hash($senha, PASSWORD_DEFAULT);
                    $userOb->cep = $cep;
                    $userOb->estado = $estado;
                    $userOb->endereco = $endereco;
                    $userOb->numero = $numero;
                    $userOb->bairro = $bairro;
                    $userOb->complemento = $complemento;
                  	$userOb->status = '1';
                  
                     

                    if(!$userOb->cadastrar()){
                        return \json_encode(['status' => false, 'msg' => 'Falha ao cadastrar você no sistema. Tente novamente.']);
                    }
                  
                   $nome_novo = strtolower(preg_replace(
                        "[^a-zA-Z0-9-]",
                        "-",
                        strtr(
                            utf8_decode(trim($nome)),
                            utf8_decode("áàãâêíóôõúüñçÁÀÃÂÉÊÓÔÕÚÜÑÇ"),
                            "aaaaeeiooouuncAAAAEEIOOOUUNC-"
                        )
                    ));
                    $nome_url = preg_replace('/[ -]+/', '-', $nome_novo);

                    $userOb->cadastrar_loja($userOb->id, $nome_url);
                  
                  	$user_token = md5(uniqid());

                    $user_login_status = 'Login';

                    $userOb->user_token = $user_token;
                    $userOb->user_login_status = $user_login_status;

                    $usuario = User::getUserByEmail($email);
                  
                  	
                    // $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
                    $userOb->atualizarLoginStatus();
                  
                    SessionUserLogin::login($usuario);
                    $reload = true;


                } else {
                    return \json_encode(['status' => false, 'msg' => 'Tipo de conta inválido.']);
                }
            }
        } else {
           $usuario = User::getUserById($_SESSION['user']['usuario']['id']);
        }

        

        // Array do comprador para o pagseguro
        $comprador = array(
            'name' => $usuario->nome,
            'email' => $usuario->email,
            'tax_id' => $usuario->cpf,
            'phones' => [
                [
                    'country' => '55',
                    'area' => substr($usuario->telefone, 0, 2),
                    'number' => substr($usuario->telefone, 2, 11),
                    'type' => 'MOBILE'
                ]
            ]
        );

        // Array de endereço para o pagseguro.
        $endereco = array(
            'country' => 'Brasil',
            'region' => 'São Paulo',
            'region_code' => "SP",
            "city" => "São Paulo",
            "postal_code" => "01452002",
            "street" => "Avenida Brigadeiro Faria Lima",
            "number" => "1384",
            "locality" => "Pinheiros"
        );

        // Cria a assinatura inicial
        $assinatura = new EntityAssinatura;
        $assinatura->plano = $vars['plano'];
        $assinatura->user = $usuario->id;
        $assinatura->status = 'PENDING';
        $assinatura->metodo = $metodo;
        $assinatura->cadastrar();


        // Criar o pix
        if($metodo === 'pix'){

            $pix = $pagseguro->pagamento($assinatura->id, $comprador, array(), $plano->valor, "PIX", "ShowClass", getenv('URL') . '/assinatura/webhook');
            if(isset($pix->erro)){
                $assinatura->excluir();
                return \json_encode(['status' => false, 'msg' => $pix->erro, 'reload' => $reload]);
            }

            // Retorna o código pix
            return \json_encode(['status' => true, 'pix' => true, 'data' => ['qr_code' => $pix->qr_codes[0]->links[0]->href, 'copia_cola' => $pix->qr_codes[0]->text, 'expiracao' => '30 minutos de duração.']]);


        } else if($metodo === 'boleto'){
            $boleto = $pagseguro->pagamento($assinatura->id, $comprador, $endereco, $plano->valor, "BOLETO", "DecolandoJuntos", getenv('URL') . '/assinatura/webhook');
            if(isset($boleto->erro)){
                $assinatura->excluir();
                return \json_encode(['status' => false, 'msg' => $boleto->erro, 'reload' => $reload]);
            }
            // Retorna informações do boleto
            return \json_encode(['status' => true, 'boleto' => true, 'data' => ['cod_barra' => $boleto->charges[0]->payment_method->boleto->formatted_barcode, 'expiracao' => date('d/m/Y', strtotime($boleto->charges[0]->payment_method->boleto->due_date)), 'link' => $boleto->charges[0]->links[0]->href]]);
        } else if($metodo === 'creditCard'){

            $cartao = $pagseguro->pagamento($assinatura->id, $comprador, array(), $plano->valor, "CARTAO", "DecolandoJuntos", getenv('URL') . '/assinatura/webhook', $vars['encryptedCard'], 'CREDIT_CARD', $vars['recorrente'] ?? 'N' === 'S' ? true : false, false);

            
            if(isset($cartao->erro)){
                $assinatura->excluir();
                return \json_encode(['status' => false, 'msg' => $cartao->erro, 'reload' => $reload]);
            }

            // Se foi autorizado ou foi pago, atualiza a assinatura imediatamente.
            if($cartao->charges[0]->status === 'AUTHORIZED' || $cartao->charges[0]->status === 'PAID'){
                // Pagou
                $dataExpiracao = new \DateTime();
                $dataExpiracao->setTimezone(new \DateTimeZone("-03:00"));
                $dataExpiracao->modify("+1 month");

                $assinatura->expiracao = $dataExpiracao->format('Y-m-d H:i:s');
                $assinatura->status = 'PAID';
                // Somente exibe o id do cartão caso a assinatura seja recorrente.
                $assinatura->cartao_id = $cartao->charges[0]->payment_method->card->id ?? '';
                $assinatura->atualizar();

                return \json_encode(['status' => true, 'cartao' => true]);
            } else {
                $assinatura->excluir();
                return \json_encode(['status' => false, 'cartao' => true, 'reload' => $reload, 'msg' => $cartao->charges[0]->payment_response->message]);
            }

            
        }

        return \json_encode(['status' => false, 'msg' => 'Não reconhecido.']);

    }
    

}

