<?php

namespace App\Controller\Pages;

use \App\Utils\View;
use \App\Model\Entity\User as EntityUser;
use \App\Model\Entity\Chat as EntityChat;
use \App\Model\Entity\Announce as EntityAnnounce;

use App\Session\User\Login as SessionUserLogin;


class Chat extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getUsersItems($request, $userid)
    {

        // $query = "
        // SELECT id, nome, user_login_status, (SELECT COUNT(*) FROM chat_message WHERE to_user_id = :user_id AND from_user_id = usuarios.id AND status = 'No') 
        // AS count_status FROM usuarios
        // ";

        // $statement = $this->connect->prepare($query);

        // $statement->bindParam(':user_id', $this->user_id);

        // $statement->execute();

        // $data = $statement->fetchAll(PDO::FETCH_ASSOC);

        // return $data;


        //DEPOIMENTOS
        $itens = '';

        // //QUANTIDADE TOTAL DE REGISTROS
        
        // $count_status = EntityChat::getChats('to_user_id = "' . $userid . '" AND status = "No"', null, null, 'COUNT(*) as count_status')->fetchObject()->count_status;
        // //PAGINA ATUAL
        // $queryParams = $request->getQueryParams();
        // $paginaAtual = $queryParams['page'] ?? 1;


        // //INSTANCIA DE PAGINACAO
        // $obPagination = new Pagination($quantidadetotal, $paginaAtual, 3);

        //RESULTADOS DA PAGINA
        $results = EntityChat::getChats('(from_user_id = "' . $userid . '") OR (to_user_id = "' . $userid . '") AND id_anuncio IS NOT NULL GROUP BY id_anuncio', 'chat_message_id DESC', null);
        // echo "<pre>"; print_r($results); echo "</pre>"; exit;

        // $results_users = EntityUser::getUsersChat(null, 'id DESC', null);
        //RENDERIZA O ITEM
        while ($obChats = $results->fetchObject(EntityChat::class)) {

            

            $obUser = EntityUser::getUserById($obChats->to_user_id);
            $count_status = EntityChat::getChats('(from_user_id = "' . $obUser->id . '") OR (to_user_id = "' . $obUser->id . '") AND status = "No" AND id_anuncio IS NOT NULL GROUP BY id_anuncio', null, null, 'COUNT(*) as count_status')->fetchObject()->count_status;
            if($userid == $obChats->to_user_id) {
                $obUser = EntityUser::getUserById($obChats->from_user_id);
                $count_status = EntityChat::getChats('(from_user_id = "' . $obChats->to_user_id . '") OR (to_user_id = "' . $obChats->to_user_id . '") AND status = "No" AND id_anuncio IS NOT NULL GROUP BY id_anuncio', null, null, 'COUNT(*) as count_status')->fetchObject()->count_status;
            }

            

            $obAnnounce = EntityAnnounce::getAnnounceById($obChats->id_anuncio);

            

            $icon = '<i class="fa fa-circle text-danger"></i>';

            if ($obUser->user_login_status == 'Login') {
                $icon = '<i class="fa fa-circle text-success"></i>';
            }

            if ($obUser->id != $userid) {
                if ($count_status > 0) {
                    $total_unread_message = '<span class="badge badge-danger badge-pill">' . $count_status . '</span>';
                } else {
                    $total_unread_message = '';
                }
    
                
                
            }

            $obFoto = EntityAnnounce::getAnnounceFotoById($obAnnounce->id);


            //VIEW DE DEPOIMENTOS
            $itens .= View::render('pages/chat/item', [
                'id_anuncio' => $obChats->id_anuncio,
                'titulo' => $obAnnounce->titulo,
                'user_id' => $obUser->id,
                'user_name' => $obUser->nome,
                'total_unread_message' => $total_unread_message,
                'icon' => $icon,
                'foto' => $obFoto->nome_arquivo,
            ]);

            
        }
        if(empty($itens)){
            $itens = View::render('pages/chat/vazio');
        }
        // while ($obUser = $results_users->fetchObject(EntityChat::class)) {

        //     $icon = '<i class="fa fa-circle text-danger"></i>';

        //     if ($obUser->user_login_status == 'Login') {
        //         $icon = '<i class="fa fa-circle text-success"></i>';
        //     }

        //     if ($obUser->id != $userid) {
        //         if ($count_status > 0) {
        //             $total_unread_message = '<span class="badge badge-danger badge-pill">' . $count_status . '</span>';
        //         } else {
        //             $total_unread_message = '';
        //         }

        //         //VIEW DE DEPOIMENTOS
        //         $itens .= View::render('pages/chat/item', [
        //             'user_id' => $obUser->id,
        //             'user_name' => $obUser->nome,
        //             'mensagem' => $obUser->mensagem,
        //             'total_unread_message' => $total_unread_message,
        //             'icon' => $icon,
        //             'data' => date('d/m/Y H:i:s', strtotime($obUser->data)),
        //         ]);
        //     }
        // }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Sobre
     * @return string
     */
    public static function getChat($request)
    {

        $obUser = SessionUserLogin::userData();

        $userid = $obUser['id'];

        //VIEW DA HOME
        // $data = View::render('pages/chat/private', [
        //     'id' => $obUser['id'],
        //     'name' => $obUser['name'],
        //     'token' => $obUser['token'],
        // ]);

        //VIEW DE DEPOIMENTOS
        $content = View::render('pages/chat/private', [
            'id' => $obUser['id'],
            'name' => $obUser['name'],
            'token' => $obUser['token'],
            'itens' => self::getUsersItems($request, $userid)
        ]);
        // $content = View::render('pages/about', [
        //     'name'        => $obOrganization->name,
        //     'description' => $obOrganization->description,
        //     'site'        => $obOrganization->site
        // ]);
        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Meus Chats', $content);
    }

    public static function get_all_chat_data($from_user, $to_user, $id_anuncio)
    {
        // $query = "
        // SELECT a.user_name as from_user_name, b.user_name as to_user_name, chat_message, timestamp, status, to_user_id, from_user_id  
        // 	FROM chat_message 
        // INNER JOIN chat_user_table a 
        // 	ON chat_message.from_user_id = a.user_id 
        // INNER JOIN chat_user_table b 
        // 	ON chat_message.to_user_id = b.user_id 
        // WHERE (chat_message.from_user_id = :from_user_id AND chat_message.to_user_id = :to_user_id) 
        // OR (chat_message.from_user_id = :to_user_id AND chat_message.to_user_id = :from_user_id)
        // ";

        // $statement = $this->connect->prepare($query);

        // $statement->bindParam(':from_user_id', $this->from_user_id);

        // $statement->bindParam(':to_user_id', $this->to_user_id);

        // $statement->execute();

        // return $statement->fetchAll(PDO::FETCH_ASSOC);

        // $results_users = EntityUser::getUsers(null, 'id DESC', null);

        // $postVars = $request->getPostVars();

        // $obChat = new EntityChat;

        // $from_user = $postVars['from_user_id'];
        // $to_user = $postVars['to_user_id'];;

        // $obChat->from_user_id = $from_user;
        // $obChat->to_user_id = $to_user;
        // $obChat->atualizarChatStatus();



        $itens = [];
        $results = EntityChat::getChats2($from_user, $to_user, $id_anuncio);

        while ($obChats = $results->fetchObject(EntityChat::class)) {
            $chat_message = $obChats->chat_message;
            $to_user_id = $obChats->to_user_id;
            $timestamp = date('d/m/Y H:i:s', strtotime($obChats->timestamp));
            $itens[] = [
                'chat_message' => $obChats->chat_message,
                'from_user_id' => $obChats->from_user_id,
                'from_user_name' => $obChats->from_user_name,
                'status' => $obChats->status,
                'timestamp' => $timestamp,
                'to_user_name' => $obChats->to_user_name,
                'to_user_id' => $to_user_id

            ];
        }

        // $results = EntityChat::getChats2('(chat_message.from_user_id = 17 AND chat_message.to_user_id = 2) OR (chat_message.from_user_id = 2 AND chat_message.to_user_id = 17)', 'a.nome as from_user_name, b.nome as to_user_name, chat_message, timestamp, to_user_id, from_user_id FROM chat_message INNER JOIN usuarios a ON chat_message.from_user_id = a.id INNER JOIN usuarios b ON chat_message.to_user_id = b.id');
        echo json_encode($itens);
    }

    public static function change_chat_status($request)
    {

        $postVars = $request->getPostVars();

        $obChat = new EntityChat;

        $from_user = $postVars['from_user_id'];
        $to_user = $postVars['to_user_id'];;
        $id_anuncio = $postVars['id_anuncio'];;

        $obChat->from_user_id = $from_user;
        $obChat->to_user_id = $to_user;
        $obChat->id_anuncio = $id_anuncio;
        $obChat->atualizarChatStatus();

        return self::get_all_chat_data($from_user, $to_user, $id_anuncio);

        // return $alldata; 
    }
}
