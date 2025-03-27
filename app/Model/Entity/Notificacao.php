<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Notificacao
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
    public $id_seguidor;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $id_store;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $id_anuncio;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $visualizada;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('lojas_notificacoes'))->insert([
            'id_seguidor'     => $this->id_seguidor,
            'id_store' => $this->id_store,
            'id_anuncio' => $this->id_anuncio,
            'visualizada' => $this->visualizada
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
        return (new Database('lojas_notificacoes'))->update('id = ' . $this->id, [
            'id_seguidor'     => $this->id_seguidor,
            'id_store' => $this->id_store,
            'id_anuncio' => $this->id_anuncio,
            'visualizada' => $this->visualizada
        ]);

        //SUCESSO
        return true;
    }
    /**
     * Método responsásel por atualizar  a instância atual
     * @return boolean
     */
    public function atualizar_visualizacao()
    {

        //ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('lojas_notificacoes'))->update('id = ' . $this->id, [
            'visualizada' => $this->visualizada
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsásel por excluir um depoimento do banco de dados
     * @return boolean
     */
    public function excluir()
    {

        //EXCLUI O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('lojas_notificacoes'))->delete('id = ' . $this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getNotificacaoByUserId($id)
    {
        return self::getNotificacoes('id_seguidor = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getNotificacaoById($id)
    {
        return self::getNotificacoes('id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getNotificacoes($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('lojas_notificacoes'))->select($where, $order, $limit, $fields);
    }
}
