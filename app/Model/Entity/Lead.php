<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class Lead
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
    public $fullname;

    /**
     * Email usuário.
     *
     * @var string
     */
    public $email;

    /**
     * ID do usuário.
     *
     * @var string
     */
    public $phone;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados.
     *
     * @return bool
     */
    public function cadastrar()
    {
        // INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('leads'))->insert([
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        // SUCESSO
        return $this->id;
    }

    /**
     * Método responsável por atualizar os dados no banco.
     *
     * @return bool
     */
    public function atualizar()
    {
        return (new Database('leads'))->update('id = '.$this->id, [
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);
    }

    /**
     * Método responsável por excluir os dados no banco.
     *
     * @return bool
     */
    public function excluir()
    {
        return (new Database('leads'))->delete('id = '.$this->id);
    }

    /**
     * Método responsável por retornar uma instância com base no ID.
     *
     * @param int $id
     *
     * @return User
     */
    public static function getLeadById($id)
    {
        return self::getLeads('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar um usuário com base no seu e-mail.
     *
     * @param string $email
     *
     * @return User
     */
    public static function getLeadByEmail($email)
    {
        return self::getLeads('email = "'.$email.'"')->fetchObject(self::class);
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
    public static function getLeads($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('leads'))->select($where, $order, $limit, $fields);
    }
}
