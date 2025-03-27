<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Pagina
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
     * Nome do usuário
     * @var string
     */
    public $nome_url;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $titlesect1;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $contentsect1;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $titlesect2;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $contentsect2;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $titlesect3;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $contentsect3;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $titlesect4;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $contentsect4;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $titlesect5;
    /**
     * Mensagem do depoimento
     * @var string
     */
    public $contentsect5;

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
        $this->id = (new Database('paginas'))->insert([
            'nome'     => $this->nome,
            'nome_url'     => $this->nome_url
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
        return (new Database('paginas'))->update('id = '.$this->id, [
            'nome'     => $this->nome,
            'nome_url'     => $this->nome_url,
            'titlesect1' => $this->titlesect1,
            'contentsect1' => $this->contentsect1,
            'titlesect2' => $this->titlesect2,
            'contentsect2' => $this->contentsect2,
            'titlesect3' => $this->titlesect3,
            'contentsect3' => $this->contentsect3,
            'titlesect4' => $this->titlesect4,
            'contentsect4' => $this->contentsect4,
            'titlesect5' => $this->titlesect5,
            'contentsect5' => $this->contentsect5
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
        return (new Database('paginas'))->delete('id = '.$this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Pagina
     */
    public static function getPaginaById($id){
        return self::getPaginas('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Pagina
     */
    public static function getPaginaByUrl($id){
        return self::getPaginas('nome_url = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar paginas
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getPaginas($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('paginas'))->select($where, $order, $limit, $fields);
    }
}
