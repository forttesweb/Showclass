<?php

namespace App\Services;

use App\Utils\View;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    /**
     * Cria a mensagem.
     *
     * @return $this
     */
    public static function build($details)
    {
        // emails para quem será enviado o formulário
        $emailenviar = $details['para'];
        $destino = $emailenviar;
        $assunto = $details['assunto'];
        $body = $details['mensagem'];

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = false;
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.brevo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gui.s2.xp@gmail.com';
        $mail->Password = 'nfrZE71JKXHdL9mQ';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('email@showclass.com.br', 'Site ShowClass');

        $mail->addAddress($details['para']);

        $mail->isHTML(true);
        $mail->Subject = $details['assunto'];

        $content = View::render('email/cadastro', ['titulo' => $details['titulo'], 'usuario_nome' => $details['nome'], 'mensagem' => $details['mensagem']]);

        $mail->Body = $content;

        $mail->send();

        return true;

        // $from = getenv('ENVIA_EMAIL');

        // // É necessário indicar que o formato do e-mail é html
        // $headers = 'MIME-Version: 1.0'."\r\n";
        // $headers .= 'Content-type: text/html; charset=UTF-8'."\r\n";
        // $headers .= 'From: ShowClass <'.$from.'>';
        // // $headers .= "Bcc: $EmailPadrao\r\n";

        // mail($destino, $assunto, $body, $headers);

        // return true;
    }

    public static function SendMails($details)
    {
        $emailenviar = $details['para'];
        $destino = $emailenviar;
        $assunto = $details['assunto'];
        $body = $details['mensagem'];

        // $postVars = $request->getPostVars();

        // $mail = new PHPMailer(true);
        // $mail->SMTPDebug = true;
        // $mail->Host = 'smtp-relay.brevo.com';
        // $mail->SMTPAuth = true;
        // $mail->Username = 'guilherme.mayrink@outlook.com';
        // $mail->Password = '2FOZBJEMHU8Pyw75';
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        // $mail->Port = 465;
        // $mail->CharSet = 'UTF-8';
        // $mail->setFrom('email@showclass.com.br', 'Site ShowClass');
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = false;
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.brevo.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gui.s2.xp@gmail.com';
        $mail->Password = 'nfrZE71JKXHdL9mQ';
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('email@showclass.com.br', 'Site ShowClass');

        $mail->addAddress($details['para']);

        $mail->isHTML(true);
        $mail->Subject = $details['assunto'];

        $content = View::render('email/cod_cadastro', ['titulo' => $details['titulo'], 'usuario_nome' => $details['nome'], 'mensagem' => $details['mensagem']]);

        $mail->Body = $content;

        $mail->send();
    }
}
