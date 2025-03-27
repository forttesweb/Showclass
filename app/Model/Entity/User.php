<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class User
{
    /**
     * ID do usuário.
     *
     * @var int
     */
    public $id;

    /**
     * Nome do usuário.
     *
     * @var string
     */
    public $nome;

    /**
     * Sobrenome do usuário.
     *
     * @var string
     */
    public $sobrenome;

    /**
     * Email usuário.
     *
     * @var string
     */
    public $email;

    /**
     * Telefone usuário.
     *
     * @var string
     */
    public $telefone;

    /**
     * Telefone usuário.
     *
     * @var string
     */
    public $cpf;

    /**
     * Email usuário.
     *
     * @var string
     */
    public $cep;
    /**
     * Email usurio.
     *
     * @var string
     */
    public $estado;
    /**
     * Email usuário.
     *
     * @var string
     */
    public $endereco;
    /**
     * Email usuário.
     *
     * @var string
     */
    public $numero;
    /**
     * Email usuário.
     *
     * @var string
     */
    public $bairro;
    /**
     * Email usuário.
     *
     * @var string
     */
    public $complemento;

    /**
     * Telefone usuário.
     *
     * @var string
     */
    public $data_nascimento;

    /**
     * ID do usuário.
     *
     * @var string
     */
    public $senha;

    /**
     * Código do e-mail.
     *
     * @var string
     */
    public $code;
    /**
     * Código do e-mail.
     *
     * @var string
     */
    public $status;

    /**
     * Código do e-mail.
     *
     * @var string
     */
    public $user_login_status;

    /**
     * Código do e-mail.
     *
     * @var string
     */
    public $user_token;

    /**
     * ID do usuário.
     *
     * @var string
     */
    public $token;
    /**
     * ID do usuário.
     *
     * @var string
     */
    public $data_criacao;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados.
     *
     * @return bool
     */
    public function cadastrar()
    {
        // INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('usuarios'))->insert([
            'nome' => $this->nome,
            'sobrenome' => $this->sobrenome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'cpf' => $this->cpf,
            'cep' => $this->cep,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'complemento' => $this->complemento,
            'senha' => $this->senha,
            'status' => $this->status,
        ]);

        // SUCESSO
        return $this->id;
    }

    /**
     * Método responsável por cadastrar a loja do usuário no banco de dados.
     *
     * @return bool
     */
    public function cadastrar_loja($id, $nome_loja, $nome_url)
    {
        // INSERE A INSTÂNCIA NO BANCO
        (new Database('usuarios_lojas'))->insert([
            'id_user' => $id,
            'nome_loja' => $nome_loja,
            'nome_url' => $nome_url,
        ]);

        // SUCESSO
        return true;
    }

    /**
     * Método responsável por cadastrar o código do e-mail no banco de dados.
     *
     * @return bool
     */
    public function cadastrar_codigo($id)
    {
        // INSERE A INSTÂNCIA NO BANCO
        (new Database('email_confirm'))->insert([
            'user_id' => $id,
            'user_email' => $this->email,
            'code' => $this->code,
        ]);

        // SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados no banco.
     *
     * @return bool
     */
    public function atualizar()
    {
        return (new Database('usuarios'))->update('id = '.$this->id, [
            'nome' => $this->nome,
            'sobrenome' => $this->sobrenome,
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
            'senha' => $this->senha,
        ]);
    }

    /**
     * Método responsável por atualizar os dados no banco.
     *
     * @return bool
     */
    public function atualizar_endereco()
    {
        return (new Database('usuarios'))->update('id = '.$this->id, [
            'cep' => $this->cep,
            'estado' => $this->estado,
            'endereco' => $this->endereco,
            'numero' => $this->numero,
            'bairro' => $this->bairro,
            'complemento' => $this->complemento,
        ]);
    }

    /**
     * Método responsável por atualizar os dados no banco.
     *
     * @return bool
     */
    public function atualizarStatus()
    {
        return (new Database('usuarios'))->update('id = '.$this->id, [
            'id' => $this->id,
            'email' => $this->email,
            'status' => 1,
        ]);
    }

    /**
     * Método responsável por atualizar os dados no banco.
     *
     * @return bool
     */
    public function atualizarLoginStatus()
    {
        return (new Database('usuarios'))->update('id = '.$this->id, [
            'id' => $this->id,
            'user_login_status' => $this->user_login_status,
            'user_token' => $this->user_token,
        ]);
    }

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados.
     *
     * @return bool
     */
    public function cadastrar_token()
    {
        // INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('tokens_recuperacao'))->insert([
            'token' => $this->token,
            'email' => $this->email,
        ]);

        // SUCESSO
        return true;
    }

    /**
     * Método responsável por retornar uma instância com base no ID.
     *
     * @return User
     */
    public static function getToken($token)
    {
        return self::getTokens('token = "'.$token.'"')->fetchObject(self::class);
    }

    /**
     * Método responsável por excluir os dados no banco.
     *
     * @return bool
     */
    public function excluir()
    {
        return (new Database('usuarios'))->delete('id = '.$this->id);
    }

    /**
     * Método responsável por retornar uma instância com base no ID.
     *
     * @param int $id
     *
     * @return User
     */
    public static function getUserById($id)
    {
        return self::getUsers('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsvel por retornar um usuário com base no seu e-mail.
     *
     * @param string $email
     *
     * @return User
     */
    public static function getUserByEmail($email)
    {
        return self::getUsers('email = "'.$email.'"')->fetchObject(self::class);
    }

    public static function getUserByCnpj($cnpj)
    {
        return self::getUsers('cpf = "'.$cnpj.'"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instância com base no ID.
     *
     * @return User
     */
    public static function getCodeByCode($codigo)
    {
        return self::getCode('code = '.$codigo)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instância com base no ID.
     *
     * @return User
     */
    public static function getStoriesByUser($codigo)
    {
        return self::getStories('id_user = '.$codigo)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instância com base no ID.
     *
     * @return User
     */
    public static function getStoriesByUserExpira($id_user, $data)
    {
        return self::getStories('id_user = "'.$id_user.'" AND expira > "'.$data.'"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getUsersChat($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getCode($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('email_confirm'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getStories($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios_stories'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getUsersAssinantes($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('plans_subscriptions'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getTokens($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('tokens_recuperacao'))->select($where, $order, $limit, $fields);
    }
}
