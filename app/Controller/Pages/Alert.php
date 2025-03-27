<?php

namespace App\Controller\Pages;

use \App\Utils\View;

class Alert
{

    /**
     * Método responsável por retornar uma mensagem de erro
     * @param string
     * @return string
     */
    public static function getError($message)
    {
        return View::render('account/alert/status', [
            'tipo'     => 'danger',
            'mensagem' => $message
        ]);
    }
    
    /**
     * Método responsável por retornar uma mensagem de sucesso
     * @param string
     * @return string
     */
    public static function getSuccess($message)
    {
        return View::render('account/alert/status', [
            'tipo'     => 'success',
            'mensagem' => $message
        ]);
    }
}
