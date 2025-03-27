<?php

namespace App\Model\Entity;

use WilliamCosta\DatabaseManager\Database;

class Announce
{
    /**
     * ID do depoimento.
     *
     * @var int
     */
    public $id;

    /**
     * ID do usuário.
     *
     * @var int
     */
    public $id_user;

    /**
     * ID da loja.
     *
     * @var int
     */
    public $id_store;

    /**
     * Nome do usuário.
     *
     * @var string
     */
    public $titulo;

    /**
     * Mensagem do depoimento.
     *
     * @var string
     */
    public $placa;

    /**
     * Categoria do anúncio.
     *
     * @var string
     */
    public $categoria;

    /**
     * Valor do anúncio.
     *
     * @var string
     */
    public $cod_marca;

    /**
     * Forma de envio.
     *
     * @var string
     */
    public $nome_marca;

    /**
     * Cep de envio.
     *
     * @var string
     */
    public $cod_modelo;
    public $nome_modelo;
    public $ano_fab;
    public $ano_modelo;
    public $versao;
    public $motor;
    public $idValvula;
    public $cambio;
    public $cor;
    public $portas;
    public $combustivel;
    public $kilometragem;
    public $observacoes;
    public $valor;

    /**
     * Cep de envio.
     *
     * @var string
     */
    public $url;
    public $tipo_veic;

    /**
     * Cep de envio.
     *
     * @var string
     */
    public $nome_arquivo;

    /**
     * Cep de envio.
     *
     * @var string
     */
    public $vendido;

    /**
     * Cep de envio.
     *
     * @var string
     */
    public $visualizada;

    /**
     * ID da loja.
     *
     * @var int
     */
    public $id_seguidor;

    public $id_opcional;

    public $foto_01;
    public $foto_02;
    public $foto_03;
    public $foto_04;
    public $foto_05;
    public $foto_06;
    public $foto_07;
    public $foto_08;

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados.
     *
     * @return bool
     */
    public function cadastrar()
    {
        // DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        // INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('anuncios'))->insert([
            'id_user' => $this->id_user,
            'id_store' => $this->id_store,
            'vendido' => $this->vendido,
            'titulo' => $this->titulo,
            'placa' => $this->placa,
            'categoria' => $this->categoria,
            'cod_marca' => $this->cod_marca,
            'nome_marca' => $this->nome_marca,
            'cod_modelo' => $this->cod_modelo,
            'nome_modelo' => $this->nome_modelo,
            'ano_fab' => $this->ano_fab,
            'ano_modelo' => $this->ano_modelo,
            'versao' => $this->versao,
            'motor' => $this->motor,
            'idValvula' => $this->idValvula,
            'cambio' => $this->cambio,
            'cor' => $this->cor,
            'portas' => $this->portas,
            'combustivel' => $this->combustivel,
            'kilometragem' => $this->kilometragem,
            'observacoes' => $this->observacoes,
            'valor' => $this->valor,
            'url' => $this->url,
            'tipo_veic' => $this->tipo_veic,
        ]);

        // SUCESSO
        return true;
    }

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados.
     *
     * @return bool
     */
    public function cadastrar_fotos($id_anuncio)
    {
        // DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        // INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('anuncios_fotos'))->insert([
            'id_anuncio' => $id_anuncio,
            'foto_01' => $this->foto_01,
            'foto_02' => $this->foto_02,
            'foto_03' => $this->foto_03,
            'foto_04' => $this->foto_04,
            'foto_05' => $this->foto_05,
            'foto_06' => $this->foto_06,
            'foto_07' => $this->foto_07,
            'foto_08' => $this->foto_08,
        ]);

        // SUCESSO
        return true;
    }

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados.
     *
     * @return bool
     */
    public function atualizar_fotos($id_anuncio)
    {
        // DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        // INSERE O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('anuncios_fotos'))->update('id_anuncio = '.$id_anuncio, [
            'foto_01' => $this->foto_01,
            'foto_02' => $this->foto_02,
            'foto_03' => $this->foto_03,
            'foto_04' => $this->foto_04,
            'foto_05' => $this->foto_05,
            'foto_06' => $this->foto_06,
            'foto_07' => $this->foto_07,
            'foto_08' => $this->foto_08,
        ]);
    }

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados.
     *
     * @return bool
     */
    public function atualizar_foto($id_anuncio, $key_foto)
    {
        // DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        // INSERE O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('anuncios_fotos'))->update('id_anuncio = '.$id_anuncio, [
            $key_foto => null,
        ]);
    }

    /**
     * Método responsásel por cadastrar a instância atual no banco de dados.
     *
     * @return bool
     */
    public function cadastrar_notificacao($id_anuncio)
    {
        // DEFINE A DATA
        // $this->data = date('Y-m-d H:i:s');

        // INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('lojas_notificacoes'))->insert([
            'id_seguidor' => $this->id_seguidor,
            'id_store' => $this->id_store,
            'id_anuncio' => $id_anuncio,
            'visualizada' => $this->visualizada,
        ]);

        // SUCESSO
        return true;
    }

    /**
     * Método responsásel por atualizar  a instância atual.
     *
     * @return bool
     */
    public function atualizar()
    {
        // ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('anuncios'))->update('id = '.$this->id, [
            'id_user' => $this->id_user,
            'id_store' => $this->id_store,
            'vendido' => $this->vendido,
            'titulo' => $this->titulo,
            'placa' => $this->placa,
            'categoria' => $this->categoria,
            'cod_marca' => $this->cod_marca,
            'nome_marca' => $this->nome_marca,
            'cod_modelo' => $this->cod_modelo,
            'nome_modelo' => $this->nome_modelo,
            'ano_fab' => $this->ano_fab,
            'ano_modelo' => $this->ano_modelo,
            'versao' => $this->versao,
            'motor' => $this->motor,
            'idValvula' => $this->idValvula,
            'cambio' => $this->cambio,
            'cor' => $this->cor,
            'portas' => $this->portas,
            'combustivel' => $this->combustivel,
            'kilometragem' => $this->kilometragem,
            'observacoes' => $this->observacoes,
            'valor' => $this->valor,
            'url' => $this->url,
            'tipo_veic' => $this->tipo_veic,
        ]);

        // SUCESSO
        return true;
    }

    /**
     * Método responsásel por atualizar  a instância atual.
     *
     * @return bool
     */
    public function marcar_vendido()
    {
        // ATUALIZA O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('anuncios'))->update('id = '.$this->id, [
            'id_user' => $this->id_user,
            'vendido' => $this->vendido,
        ]);

        // SUCESSO
        return true;
    }

    public function cadastrar_opcionais($id_anuncio)
    {
        $this->id = (new Database('anuncios_opcionais'))->insert([
            'id_anuncio' => $id_anuncio,
            'id_opcional' => $this->id_opcional,
        ]);
    }

    /**
     * Método responsásel por excluir um depoimento do banco de dados.
     *
     * @return bool
     */
    public function excluir()
    {
        // EXCLUI O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('anuncios'))->delete('id = '.$this->id);

        // SUCESSO
        return true;
    }

    /**
     * Método responsásel por excluir um depoimento do banco de dados.
     *
     * @return bool
     */
    public function excluir_foto()
    {
        // EXCLUI O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('anuncios_fotos'))->delete('id = '.$this->id);

        // SUCESSO
        return true;
    }

    /**
     * Método responsásel por excluir um depoimento do banco de dados.
     *
     * @return bool
     */
    public function excluir_foto_anuncio($id_anuncio)
    {
        // EXCLUI O DEPOIMENTO NO BANCO DE DADOS
        return (new Database('anuncios_fotos'))->delete('id_anuncio = '.$id_anuncio);

        // SUCESSO
        return true;
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID.
     *
     * @param int $id
     *
     * @return Testimony
     */
    public static function getAnnounceById($id)
    {
        return self::getAnuncios('id = '.$id)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID.
     *
     * @return Testimony
     */
    public static function getAdsByUser($id_user)
    {
        return self::getAnuncios('id_user = '.$id_user)->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID.
     *
     * @return Testimony
     */
    public static function getAnnounceByTitle($titulo)
    {
        return self::getAnuncios('titulo = "'.$titulo.'"')->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID.
     *
     * @return Testimony
     */
    public static function getAnnounceByUrl($url)
    {
        return self::getAnuncios('url = "'.$url.'"')->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID.
     *
     * @return Testimony
     */
    public static function getAnnounceFotoById($url)
    {
        return self::getFotos('id_anuncio = "'.$url.'"')->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID.
     *
     * @return Testimony
     */
    public static function getFotoById($url)
    {
        return self::getFotos('id = "'.$url.'"')->fetchObject(self::class);
    }

    /**
     * Método responsáel por retornar um depoimento com base no seu ID.
     *
     * @return Testimony
     */
    public static function getOpcionalById($id_op)
    {
        return self::getOpcionaisDados('id_opcional = "'.$id_op.'"')->fetchObject(self::class);
    }
    // /**
    //  * Método responsáel por retornar um depoimento com base no seu ID
    //  *
    //  * @param integer $id
    //  * @return Testimony
    //  */
    // public static function getStoreByUrl($url){
    //     return self::getStores('nome_url = "'.$url.'"')->fetchObject(self::class);
    // }

    // /**
    //  * Método responsáel por retornar um depoimento com base no seu ID
    //  *
    //  * @param integer $id
    //  * @return Testimony
    //  */
    // public static function getStoreByUser($url){
    //     return self::getStores('id_user = "'.$url.'"')->fetchObject(self::class);
    // }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getAnuncios($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('anuncios'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getFotos($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('anuncios_fotos'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getOpcionais($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('anuncios_opcionais'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar depoimentos.
     *
     * @param string $where
     * @param string $order
     * @param string $limit
     *
     * @return PDOStatement
     */
    public static function getOpcionaisDados($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('opcionais'))->select($where, $order, $limit, $fields);
    }
    // /**
    //  * Método responsável por retornar depoimentos
    //  * @param string $where
    //  * @param string $order
    //  * @param string $limit
    //  * @param string $field
    //  * @return PDOStatement
    //  */
    // public static function getStores($where = null, $order = null, $limit = null, $fields = '*')
    // {
    //     return (new Database('usuarios_lojas'))->select($where, $order, $limit, $fields);
    // }
}
