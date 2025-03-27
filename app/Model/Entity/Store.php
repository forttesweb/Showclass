<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Store
{


    /**
     * ID do depoimento
     * @var integer
     */
    public $id;

    /**
     * ID do usuário
     * @var integer
     */
    public $id_user;

    /**
     * ID da loja
     * @var integer
     */
    public $id_store;

    /**
     * Nome do usuário
     * @var string
     */
    public $nome_loja;

    /**
     * Mensagem do depoimento
     * @var string
     */
    public $nome_url;

    /**
     * Data de publicação do depoimento
     * @var string
     */
    public $cidade;

    /**
     * Forma de envio
     * @var string
     */
    public $estado;

    /**
     * Forma de envio
     * @var string
     */
    public $logotipo;

    /**
     * Forma de envio
     * @var string
     */
    public $banner;
    /**
     * Forma de envio
     * @var string
     */
    public $comentario;
    /**
     * Forma de envio
     * @var string
     */
    public $rating;

    /**
     * ID do depoimento
     * @var integer
     */
    public $id_seguidor;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar()
    {
        //DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('usuarios_lojas'))->insert([
            'id_user'     => $this->id_user,
            'id_store'     => $this->id_store,
            'nome_loja'     => $this->nome_loja,
            'nome_url'     => $this->nome_url,
            'cidade' => $this->cidade,
            'estado'     => $this->estado
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
        return (new Database('usuarios_lojas'))->update('id = ' . $this->id, [
            'id_user'     => $this->id_user,
            'nome_loja'     => $this->nome_loja,
            'nome_url'     => $this->nome_url,
            'cidade' => $this->cidade,
            'estado'     => $this->estado,
            'logotipo'     => $this->logotipo,
            'banner'     => $this->banner,
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar_review()
    {
        //DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('usuarios_lojas_reviews'))->insert([
            'id_user'     => $this->id_user,
            'id_store'     => $this->id_store,
            'nome_url'     => $this->nome_url,
            'comentario' => $this->comentario,
            'rating'     => $this->rating
        ]);

        //SUCESSO
        return true;
    }
    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function cadastrar_seguidor()
    {
        //DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('lojas_seguidores'))->insert([
            'id_seguidor'     => $this->id_seguidor,
            'id_store'     => $this->id_store
        ]);

        //SUCESSO
        return true;
    }
    /**
     * Método responsásel por cadastrar a instância atual no banco de dados
     * @return boolean
     */
    public function remover_seguidor()
    {
        //DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        // $this->id = (new Database('lojas_seguidores'))->insert([
        //     'id_seguidor'     => $this->id_seguidor,
        //     'id_store'     => $this->id_store
        // ]);
        return (new Database('lojas_seguidores'))->delete('id_seguidor = ' . $this->id_seguidor . ' AND id_store = ' . $this->id_store . '');

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
        return (new Database('usuarios_lojas'))->delete('id = ' . $this->id);

        //SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getAnnounceById($id_store)
    {
        return self::getAnuncios('id = ' . $id_store)->fetchObject(self::class);
    }
    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getStoreById($id_store)
    {
        return self::getStores('id = ' . $id_store)->fetchObject(self::class);
    }
    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getSeguidorByUserId($id_seguidor, $id_loja)
    {
        return self::getSeguidores('id_seguidor = ' . $id_seguidor . ' AND id_store = ' . $id_loja . '')->fetchObject(self::class);
    }

    // /**
    //  * Método responsáel por retornar um depoimento com base no seu ID
    //  *
    //  * @param integer $id
    //  * @return Testimony
    //  */
    // public static function getAdsByStore($id){
    //     return self::getStores('nome_url = '.$id)->fetchObject(self::class);
    // }

    // /**
    //  * Método responsáel por retornar um depoimento com base no seu ID
    //  *
    //  * @param integer $id
    //  * @return Testimony
    //  */
    // public static function getAnnounceByTitle($titulo){
    //     return self::getAnuncios('titulo = "'.$titulo.'"')->fetchObject(self::class);
    // }

    // /**
    //  * Método responsáel por retornar um depoimento com base no seu ID
    //  *
    //  * @param integer $id
    //  * @return Testimony
    //  */
    // public static function getAnnounceByUrl($url){
    //     return self::getAnuncios('url = "'.$url.'"')->fetchObject(self::class);
    // }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getStoreByUrl($url)
    {
        return self::getStores('nome_url = "' . $url . '"')->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID
     *
     * @param integer $id
     * @return Testimony
     */
    public static function getStoreByUser($url)
    {
        return self::getStores('id_user = "' . $url . '"')->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getAnnounces($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('anuncios'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getStores($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios_lojas'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getStoreReviews($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('usuarios_lojas_reviews'))->select($where, $order, $limit, $fields);
    }
    /**
     * Método responsável por retornar depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getSeguidores($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('lojas_seguidores'))->select($where, $order, $limit, $fields);
    }
}
