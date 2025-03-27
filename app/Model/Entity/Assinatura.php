<?php

namespace App\Model\Entity;

use \WilliamCosta\DatabaseManager\Database;

class Assinatura
{
    /**
     * ID do plano
     * @var integer
     */
    public $id;

    /**
     * Nome do Plano
     * @var string
     */
    public $plano;

    /**
     * Descrição do Plano
     * @var string
     */
    public $user;
    
    public $metodo;

    public $data;

    public $expiracao;

    public $cartao_id;

    public $status;
    
    public $subscription_id;

    public function status(){

        switch($this->status){
            case 'WAITING':
                case 'PENDING':
                    return 'Aguardando Pagamento';
            case 'PAID':
                return 'Ativa';
            case 'CANCELLED':
                return 'Cancelada';
            case 'EXPIRED':
                return 'Expirada';
            default: 
                return $this->status;
        }

    }



    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     *
     * @return boolean
     */
    public function cadastrar()
    {
        //INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('plans_subscriptions'))->insert([
            'plano' => $this->plano,
            'user' => $this->user,
            'status' => $this->status,
            'metodo' => $this->metodo,
            'subscription_id' => $this->subscription_id

        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por atualizar os dados no banco
     *
     * @return boolean
     */
    public function atualizar()
    {
        return (new Database('plans_subscriptions'))->update('id = ' . $this->id, [
            'plano' => $this->plano,
            'user' => $this->user,
            'status' => $this->status,
            'metodo' => $this->metodo,
            'expiracao' => $this->expiracao,
            'cartao_id' => $this->cartao_id,
        ]);
    }

    /**
     * Método responsável por cadastrar a instancia atual no banco de dados
     *
     * @return boolean
     */
    public function inserir_premium()
    {

        //INSERE A INSTÂNCIA NO BANCO
        $this->id = (new Database('plans_subscriptions'))->insert([
            'plano' => $this->plano,
            'user' => $this->user,
            'status' => $this->status,
            'metodo' => $this->metodo,
            'expiracao' => $this->expiracao,
            'cartao_id' => $this->cartao_id,
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável por excluir os dados no banco
     *
     * @return boolean
     */
    public function excluir()
    {
        return (new Database('plans_subscriptions'))->delete('id = ' . $this->id);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return Assinatura
     */
    public static function getAssinaturaById($id)
    {
        return self::getplans_subscriptions('id = ' . $id)->fetchObject(self::class);
    }
    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return Assinatura
     */
    public static function getAssinaturaBySubsId($id)
    {
        return self::getplans_subscriptions('subscription_id = ' . $id)->fetchObject(self::class);
    }

    /**
     * Método responsável por retornar uma instância com base no ID
     *
     * @param integer $id
     * @return Assinatura
     */
    public static function getAssinaturaByUserId($id)
    {
        return self::getplans_subscriptions('user = ' . $id)->fetchObject(self::class);
    }

    /** 
     * 
     * Verifica se o usuário tem uma assinatura ativa independente do plano.
     * 
     */
    public static function getAcesso($id = null){

    
        $acesso = self::getplans_subscriptions("user = {$id} AND status = 'PAID'", null, 1)->fetchObject(self::class);
        if($acesso)
            return $acesso;
        
        return false;

    }

    /**
     * Método responsável por retornar planos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getplans_subscriptions($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('plans_subscriptions'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por retornar planos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
     */
    public static function getAssinaturas($where = null, $order = null, $limit = null, $fields = '*')
    {
        return (new Database('plans_subscriptions'))->select($where, $order, $limit, $fields);
    }
}
