<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Faq
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
    public $nome;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $mensagem;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $data;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {

        //DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('faqs'))->insert([
            'nome'     => $this->nome,
            'mensagem' => $this->mensagem,
            'data'     => $this->data
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
        return (new Database('faqs'))->update('id = '.$this->id, [
            'nome'     => $this->nome,
            'mensagem' => $this->mensagem
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
        return (new Database('faqs'))->delete('id = '.$this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Faq
     */
    public static function getFaqById($id){
        return self::getFaqs('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar faqs
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getFaqs($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('faqs'))->select($where, $order, $limit, $fields);
    }
}
