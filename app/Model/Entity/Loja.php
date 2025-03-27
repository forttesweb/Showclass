<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Loja
{
    /**
     * ID do usuário
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $nome;

    /**
     * Sobrenome do usuário
     * @var string
     */
    public $sobrenome;

    /**
     * Email usuário
     * @var string
     */
    public $email;

    /**
     * Telefone usuário
     * @var string
     */
    public $telefone;

    /**
     * Telefone usuário
     * @var string
     */
    public $cpf;

    /**
     * Email usuário
     * @var string
     */
    public $cep;
    /**
     * Email usuário
     * @var string
     */
    public $estado;
    /**
     * Email usuário
     * @var string
     */
    public $endereco;
    /**
     * Email usuário
     * @var string
     */
    public $numero;
    /**
     * Email usuário
     * @var string
     */
    public $bairro;
    /**
     * Email usuário
     * @var string
     */
    public $complemento;

    /**
     * Telefone usuário
     * @var string
     */
    public $data_nascimento;

    /**
     * ID do usuário
     * @var string
     */
    public $senha;

    /**
     * Código do e-mail
     * @var string
     */

    public $code;
    /**
     * Código do e-mail
     * @var string
     */
    public $status;

    /**
     * Código do e-mail
     * @var string
     */
    public $loja_login_status;

    /**
     * Código do e-mail
     * @var string
     */
    public $loja_token;

    /**
     * ID do usuário
     * @var string
     */
    public $token;
    /**
     * ID do usuário
     * @var string
     */
    public $data_criacao;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar()
    {

        //INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('usuarios_lojas'))->insert([
            'nome'  => $this->nome,
            'sobrenome'  => $this->sobrenome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'cep' => $this->cep,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'complemento' => $this->complemento,
            'senha' => $this->senha,
            'status' => $this->status
        ]);

        //SUCESSO
        return $this->id;
    }

    /**
     * Método responsável por cadastrar a loja do usuário no banco de dados
     *
     * @return boolean
     */
    public function cadastrar_loja($id, $nome_url)
    {

        //INSERE A INSTÂNCIA NO BANCO
        (new Database('usuarios_lojas'))->insert([
            'id_loja'  => $id,
            'nome_url'  => $nome_url,
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por cadastrar o código do e-mail no banco de dados
     *
     * @return boolean
     */
    public function cadastrar_codigo($id)
    {

        //INSERE A INSTÂNCIA NO BANCO
        (new Database('email_confirm'))->insert([
            'loja_id'  => $id,
            'loja_email' => $this->email,
            'code' => $this->code
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizar() {
        return (new Database('usuarios_lojas'))->update('id = '.$this->id, [
            'nome'  => $this->nome,
            'sobrenome'  => $this->sobrenome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'cpf' => $this->cpf,
            'cep' => $this->cep,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'complemento' => $this->complemento,
            'data_nascimento' => $this->data_nascimento,
            'senha' => $this->senha
        ]);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return Loja
     */
    public static function getToken($token)
    {
        return self::getTokens('token = "' . $token . '"')->fetchObject(self::class);
    }

    /**
     * Método responsável por excluir os dados no banco
     *
     * @return boolean
     */
    public function excluir() {
        return (new Database('usuarios'))->delete('id = '.$this->id);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return Loja
     */
    public static function getLojaById($id) {
        return self::getLojas('id = '.$id)->fetchObject(self::class);
    }
    
    

    /**
     * Método responsável por retornar um usuário com base no seu e-mail
     * @param string $email
     * @return Loja
     */
    public static function getLojaByEmail($email)
    {
        return self::getLojas('email = "'.$email.'"')->fetchObject(self::class);
    }


    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getLojas($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios_lojas'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getStories($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios_stories'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getLojasAssinantes($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('plans_subscriptions'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getTokens($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('tokens_recuperacao'))->select($where, $order, $limit, $fields);
    }
}
