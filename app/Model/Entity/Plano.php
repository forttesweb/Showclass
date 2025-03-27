<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Plano
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
    public $titulo;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $descricao;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $valor;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $url;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $status;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $recorrente;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {

        //DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('plans'))->insert([
            'titulo'     => $this->titulo,
            'descricao' => $this->descricao,
            'valor' => $this->valor,
            'url' => $this->url,
            'status' => $this->status,
            'recorrente' => $this->recorrente
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
        return (new Database('plans'))->update('id = '.$this->id, [
            'titulo'     => $this->titulo,
            'descricao' => $this->descricao,
            'valor' => $this->valor,
            'url' => $this->url,
            'status' => $this->status,
            'recorrente' => $this->recorrente
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
        return (new Database('plans'))->delete('id = '.$this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Plano
     */
    public static function getPlanoById($id){
        return self::getPlanos('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Plano
     */
    public static function getPlanoRecursos($id){
        return self::getRecursos('id_plano = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getPlanos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('plans'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getRecursos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('plans_features'))->select($where, $order, $limit, $fields);
    }

    public static function updateRecurso($idPlano, $slug, $value)
    {
        // error_log("ID do Plano: " . $idPlano);
        // error_log($slug);
        // error_log($value);
        return (new Database('plans_features'))->update('id_plano = '.$idPlano.' AND slug = "'.$slug.'"', ['value' => $value]);
    }

}
