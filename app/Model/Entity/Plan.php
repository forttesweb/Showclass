<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Plan
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
     * Nome do Plano
     * @var string
     */
    public $nome;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $descricao;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $valor;

    /**
     * Cep de envio
     * @var string
     */
    public $url;

    /**
     * Cep de envio
     * @var string
     */
    public $slug;
    /**
     * Cep de envio
     * @var string
     */
    public $value;

    public $status;
    public $recorrente;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {
        //DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('plans'))->insert([
            'titulo'     => $this->titulo,
            'descricao'     => $this->descricao,
            'valor' => $this->valor,
            'url'     => $this->url
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
        return (new Database('plans'))->update('id = ' . $this->id, [
            'titulo'     => $this->titulo,
            'descricao'     => $this->descricao,
            'valor' => $this->valor,
            'url'     => $this->url
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
        return (new Database('plans'))->delete('id = ' . $this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Plan
     */
    public static function getPlanById($id)
    {
        return self::getPlans('id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return User
     */
    public static function getPlanoById($id)
    {
        return self::getPlans('id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Plan
     */
    public static function getPlanByUser($id_user)
    {
        return self::getPlans('id = ' . $id_user)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Plan
     */
    public static function getPlanByTitle($titulo)
    {
        return self::getPlans('titulo = "' . $titulo . '"')->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Plan
     */
    public static function getPlanByUrl($url)
    {
        return self::getPlans('url = "' . $url . '"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return User
     */
    public static function getPlanoFeatures($id)
    {
        return self::getPlansFeat('id_plano = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getPlans($where = null, $order = null, $limit = null, $fields = '*')
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
    public static function getPlansFeat($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('plans_features'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar todos os planos
     * @return array
     */
    public static function getAllPlans()
    {
        $plans = self::getPlans()->fetchAll(\PDO::FETCH_CLASS, self::class);
        return $plans;
    }

    public static function getAllPlansFeat()
    {
        $plans = self::getPlansFeat()->fetchAll(\PDO::FETCH_CLASS, self::class);
        return $plans;
    }
}
