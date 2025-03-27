<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Storie
{


    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * Nome do usuário
     * @var string
     */
    public $id_user;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $imagem;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $expira;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('usuarios_stories'))->insert([
            'id_user'     => $this->id_user,
            'imagem' => $this->imagem,
            'expira' => $this->expira
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsásel por atualizar  a instância atual
     * @return boolean
     */
    public function atualizar()
    {

        //ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('usuarios_stories'))->update('id = ' . $this->id, [
            'id_user'     => $this->id_user,
            'imagem' => $this->imagem,
            'expira' => $this->expira
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsásel por excluir um depoimento do banco de dados
     * @return boolean
     */
    public static function excluirStorie($idStorie)
    {

        //EXCLUI O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('usuarios_stories'))->delete('id = ' . $idStorie);

        //SUCESSO
        // return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getStorieById($id)
    {
        return self::getStories('id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getStorieByUserId($id)
    {
        return self::getStories('id_user = ' . $id)->fetchObject(self::class);
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
     * Método responsável por retornar todos os stories de um usuário pelo user_id
     *
     * @param integer $user_id
     * @return array
     */
    public static function getStoriesByUserId($user_id)
    {
        // Realiza a consulta ao banco de dados com o filtro pelo user_id
        $result = self::getStories('id_user = ' . $user_id);

        // Inicializa um array para armazenar os stories
        $stories = [];

        // Itera sobre os resultados e transforma em objetos da classe Storie
        while ($storie = $result->fetchObject(self::class)) {
            $stories[] = $storie;
        }
        
        // Retorna o array de stories
        return $stories;
    }
}
