<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Admin
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
     * Email usuário
     * @var string
     */
    public $email;

    /**
     * ID do usuário
     * @var string
     */
    public $senha;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar()
    {

        //INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('admins'))->insert([
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);

        //SUCESSO
        return $this->id;
    }


    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizar() {
        return (new Database('admins'))->update('id = '.$this->id, [
            'nome'  => $this->nome,
            'email' => $this->email,
            'senha' => $this->senha
        ]);
    }

    /**
     * Método responsável por excluir os dados no banco
     *
     * @return boolean
     */
    public function excluir() {
        return (new Database('admins'))->delete('id = '.$this->id);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return User
     */
    public static function getAdminById($id) {
        return self::getAdmins('id = '.$id)->fetchObject(self::class);
    }
    
    

    /**
     * Método responsável por retornar um usuário com base no seu e-mail
     * @param string $email
     * @return User
     */
    public static function getAdminByEmail($email)
    {
        return self::getAdmins('email = "'.$email.'"')->fetchObject(self::class);
    }


    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getAdmins($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('admins'))->select($where, $order, $limit, $fields);
    }
}
