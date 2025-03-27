<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Pagamento
{


    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * ID do depoimento
     * @var integer
     */
    public $id_user;

    /**
     * Nome do usuário
     * @var string
     */
    public $status;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $metodo;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $data;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $expiracao;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $cartao_id;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {

        //DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('pagamentos'))->insert([
            'id_user'   => $this->id_user,
            'status'    => $this->status,
            'metodo'    => $this->metodo,
            'data'      => $this->data,
            'expiracao' => $this->expiracao,
            'cartao_id' => $this->cartao_id
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
        return (new Database('pagamentos'))->update('id = '.$this->id, [
            'id_user'   => $this->id_user,
            'status'    => $this->status,
            'metodo'    => $this->metodo,
            'data'      => $this->data,
            'expiracao' => $this->expiracao,
            'cartao_id' => $this->cartao_id
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
        return (new Database('pagamentos'))->delete('id = '.$this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Pagamento
     */
    public static function getPagamentoById($id){
        return self::getPagamentos('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getPagamentos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('pagamentos'))->select($where, $order, $limit, $fields);
    }
}
