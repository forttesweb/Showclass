<?php

namespace App\Http;

class Request
{
    /**
     * Instância do Router
     * @var Router
     */
    private $router;

    /**
     * Método HTTP da requisição
     * @var string
     */
    private $httpMethod;

    /**
     * URI da página
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da URL ($_GET)
     */
    private $queryParams = [];

    
    /**
     * Variáveis recebidas no POST da páginua ($_POST)
     * @var array
     */
    private $postVars = [];

    /**
     * Variáveis recebidas no POST da páginua ($_POST)
     * @var array
     */
    private $fileVars = [];

    /**
     * Cabeçalho da reqquisição
     * @var array
     */
    private $headers = [];

    /**
     * Construtor da classe
     */
    public function __construct($router)
    {
        $this->router      = $router;
        $this->queryParams = $_GET                      ?? [];
        
        $this->headers     = getallheaders();
        $this->httpMethod  = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();
        $this->setFileVars();
        // $this->postVars    = $_POST                     ?? [];
        
    }

    /**
     * Método responsável por definir as variáveis do post
     */
    private function setPostVars(){
        if($this->httpMethod == 'GET') return false;

        //POST PADRÃO
        $this->postVars    = $_POST                     ?? [];
        // $this->postVars    .= $_FILES                     ?? [];

        //POST JSON
        $inputRaw = file_get_contents('php://input');
        $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
    }
    /**
     * Método responsável por definir as variáveis do post
     */
    private function setFileVars(){
        if($this->httpMethod == 'GET') return false;

        //POST PADRÃO
        $this->fileVars    = $_FILES                     ?? [];
        // $this->postVars    .= $_FILES                     ?? [];

        //POST JSON
        $inputRaw = file_get_contents('php://input');
        $this->fileVars = (strlen($inputRaw) && empty($_FILES)) ? json_decode($inputRaw, true) : $this->fileVars;
    }

    /**
     * Método responsável por definir a URI
     */
    private function setUri(){
        
        //URI COMPLETA (COM GETS)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        //REMOVE GETS DA URI
        $xUri = explode('?',$this->uri);
        $this->uri = $xUri[0];
    }

    /**
     * Método responsável por retornar a instancia de Router
     * @return Router
     */
    public function getRouter () {
        return $this->router;
    }

    /**
     * Método responsável por retornar o método http da requisição
     * @return string
     */
    public function getHttpMethod() {
        return $this->httpMethod;
    }

    /**
     * Método responsável por retornar a URI da requisição
     * @return string
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * Método responsável por retornar os headers da requisição
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * Método responsável por retornar os parâmetros da URL da requisição
     * @return array
     */
    public function getQueryParams() {
        return $this->queryParams;
    }

    /**
     * Método responsável por retornar as variáveis POST da requisição
     * @return array
     */
    public function getPostVars() {
        return $this->postVars;
    }

    /**
     * Método responsável por retornar as variáveis POST da requisição
     * @return array
     */
    public function getFileVars() {
        return $this->fileVars;
    }
}
