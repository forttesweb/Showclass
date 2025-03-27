<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Categoria
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
    public $nome_url;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $imagem;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {


        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('categorias'))->insert([
            'nome'     => $this->nome,
            'nome_url' => $this->nome_url,
            'imagem' => $this->imagem
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
        return (new Database('categorias'))->update('id = '.$this->id, [
            'nome'     => $this->nome,
            'nome_url' => $this->nome_url,
            'imagem' => $this->imagem
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
        return (new Database('categorias'))->delete('id = '.$this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Categoria
     */
    public static function getCategoriaById($id){
        return self::getCategorias('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Categoria
     */
    public static function getCategoriaByUrl($id){
        return self::getCategorias('nome_url = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar categorias
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getCategorias($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('categorias'))->select($where, $order, $limit, $fields);
    }
}
