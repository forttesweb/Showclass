<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Chat
{
    /**
     * ID do usuário
     * @var integer
     */
    public $chat_message_id;

    /**
     * Nome do usuário
     * @var string
     */
    public $id_anuncio;

    /**
     * Nome do usuário
     * @var string
     */
    public $to_user_id;

    /**
     * Sobrenome do usuário
     * @var string
     */
    public $from_user_id;

    /**
     * Email usuário
     * @var string
     */
    public $chat_message;

    /**
     * Telefone usuário
     * @var string
     */
    public $timestamp;

    /**
     * Telefone usuário
     * @var string
     */
    public $status;

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar()
    {

        //INSERE A INSTÂNCIA NO BANCO
        $this->chat_message_id = (new Database('chat_message'))->insert([
            'to_user_id'  => $this->to_user_id,
            'from_user_id'  => $this->from_user_id,
            'chat_message' => $this->chat_message,
            'timestamp' => $this->timestamp,
            'status' => $this->status
        ]);

        //SUCESSO
        return $this->chat_message_id;
    }

    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizar()
    {
        return (new Database('chat_message'))->update('chat_message_id = ' . $this->chat_message_id, [
            'to_user_id'  => $this->to_user_id,
            'from_user_id'  => $this->from_user_id,
            'chat_message' => $this->chat_message,
            'timestamp' => $this->timestamp,
            'status' => $this->status
        ]);
    }

    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizarStatus()
    {
        return (new Database('chat_message'))->update('id = ' . $this->id, [
            'id'  => $this->id,
            'email' => $this->email,
            'status' => 1,
        ]);
    }
    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizarChatStatus()
    {
        return (new Database('chat_message'))->update('id_anuncio = ' . $this->id_anuncio . ' AND from_user_id = ' . $this->from_user_id . ' AND to_user_id = ' . $this->to_user_id . ' AND status = "No"', [
            'status'  => 'Yes'
        ]);
    }

    /**
     * Método responsável por excluir os dados no banco
     *
     * @return boolean
     */
    public function excluir()
    {
        return (new Database('usuarios'))->delete('id = ' . $this->id);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return User
     */
    public static function getChatById($id)
    {
        return self::getChats('id = ' . $id)->fetchObject(self::class);
    }



    /**
     * Método responsável por retornar um usuário com base no seu e-mail
     * @param string $email
     * @return User
     */
    public static function getChatByEmail($email)
    {
        return self::getChats('email = "' . $email . '"')->fetchObject(self::class);
    }


    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return User
     */
    public static function getCodeByCode($codigo)
    {
        return self::getCode('code = ' . $codigo)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return User
     */
    public static function getChatsByUser($codigo)
    {
        return self::getStories('id_user = ' . $codigo)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getChats($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('chat_message'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getChats2($from_user, $to_user, $id_anuncio)
    {

        // return (new Database('chat_message'))->select($where, $order, $limit, $fields);
        return (new Database('chat_message'))->select2("SELECT a.nome as from_user_name, b.nome as to_user_name, chat_message, timestamp, chat_message.status, to_user_id, from_user_id FROM chat_message INNER JOIN usuarios a ON chat_message.from_user_id = a.id INNER JOIN usuarios b ON chat_message.to_user_id = b.id WHERE (chat_message.from_user_id = '$from_user' AND chat_message.to_user_id = '$to_user' AND chat_message.id_anuncio = '$id_anuncio') OR (chat_message.from_user_id = '$to_user' AND chat_message.to_user_id = '$from_user' AND chat_message.id_anuncio = '$id_anuncio')");

        // echo "<pre>"; print_r($reste); echo "</pre>"; exit;
        // return (new Database('chat_message'))->select($where, $order, $limit, $fields);

    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getCode($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('chat_message'))->select($where, $order, $limit, $fields);
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
        return (new Database('chat_message'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getUsersAssinantes($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('chat_message'))->select($where, $order, $limit, $fields);
    }
}
