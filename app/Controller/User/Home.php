<?php

namespace App\Controller\User;

use App\Model\Entity\Announce as EntityAnnounce;
use App\Model\Entity\Assinatura as EntityAssinatura;
use App\Model\Entity\Categoria as EntityCategoria;
use App\Model\Entity\Plan as EntityPlano;
use App\Model\Entity\Store as EntityStore;
use App\Model\Entity\Storie as EntityStorie;
use App\Model\Entity\User as EntityUser;
use App\Services\Upload;
use App\Utils\View;
use WilliamCosta\DatabaseManager\Pagination;

class Home extends Page
{
    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página.
     *
     * @param Request    $request
     * @param Pagination $obPagination
     *
     * @return string
     */
    private static function getAnnounceItems($request, &$obPagination)
    {
        $id_user = $_SESSION['user']['usuario']['id'];

        // DEPOIMENTOS
        $anuncios = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityAnnounce::getAnuncios('id_user = '.$id_user, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 6);

        // RESULTADOS DA PAGINA
        $results = EntityAnnounce::getAnuncios('id_user = '.$id_user, 'id DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while ($obAnnounce = $results->fetchObject(EntityAnnounce::class)) {
            $vendido = '';
            $marcarvendido = "<a class='btn btn-info mt-3' onclick='marcarVendido($obAnnounce->id)' href='#'>Marcar como vendido</a>";
            if ($obAnnounce->vendido == 1) {
                $vendido = "- <span class='text-danger'><strong>Vendido</strong></span>";
                $marcarvendido = '';
            }

            $obStore = EntityStore::getStoreById($obAnnounce->id_store);

            // RESULTADOS DA PAGINA
            $obFoto = EntityAnnounce::getAnnounceFotoById($obAnnounce->id);
            $foto = $obFoto->foto_01;
            if (empty($foto)) {
                $foto = 'produtosemfoto2.png';
            }

            // CONTEUDO DA HOME
            $anuncios .= View::render('account/modules/home/anuncios', [
                'id' => $obAnnounce->id,
                'url_anuncio' => $obAnnounce->url,
                'nome_url' => $obStore->nome_url,
                'titulo' => $obAnnounce->titulo,
                'categoria' => $obAnnounce->categoria,
                'preco' => number_format($obAnnounce->valor, 2, ',', '.'),
                // 'preco' => number_format($obAnnounce->preco, 2, ",", "."),
                'url' => $obAnnounce->url,
                'foto' => $foto,
                'marcarvendido' => $marcarvendido,
                'vendido' => $vendido,
            ]);
        }

        if (empty($anuncios)) {
            $anuncios .= View::render('account/modules/home/semanuncio');
        }

        // RETORNA OS DEPOIMENTOS
        return $anuncios;
    }

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página.
     *
     * @param Request    $request
     * @param Pagination $obPagination
     *
     * @return string
     */
    private static function getAnnounceVendidoItems($request, &$obPagination)
    {
        $id_user = $_SESSION['user']['usuario']['id'];

        // DEPOIMENTOS
        $anuncios = '';

        // QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityAnnounce::getAnuncios('id_user = '.$id_user.' AND vendido = 1', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        // INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 6);

        // RESULTADOS DA PAGINA
        $results = EntityAnnounce::getAnuncios('id_user = '.$id_user.' AND vendido = 1', 'id DESC', $obPagination->getLimit());

        // RENDERIZA O ITEM
        while ($obAnnounce = $results->fetchObject(EntityAnnounce::class)) {
            $vendido = '';
            $marcarvendido = "<a class='btn btn-info mt-3' onclick='marcarVendido($obAnnounce->id)' href='#'>Marcar como vendido</a>";
            if ($obAnnounce->vendido == 1) {
                $vendido = "- <span class='text-danger'><strong>Vendido</strong></span>";
                $marcarvendido = '';
            }

            $obStore = EntityStore::getStoreById($obAnnounce->id_store);

            // RESULTADOS DA PAGINA
            $obFoto = EntityAnnounce::getAnnounceFotoById($obAnnounce->id);

            // CONTEUDO DA HOME
            $anuncios .= View::render('account/modules/home/anuncios', [
                'id' => $obAnnounce->id,
                'nome_url' => $obStore->nome_url,
                'titulo' => $obAnnounce->titulo,
                'categoria' => $obAnnounce->categoria,
                'preco' => number_format($obAnnounce->preco, 2, ',', '.'),
                'url' => $obAnnounce->url,
                'foto' => $obFoto->nome_arquivo,
                'marcarvendido' => $marcarvendido,
                'vendido' => $vendido,
            ]);
        }

        if (empty($anuncios)) {
            $anuncios .= View::render('account/modules/home/semvenda');
        }

        // RETORNA OS DEPOIMENTOS
        return $anuncios;
    }

    /**
     * Método responsável por renderizar a vie de home do painel.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function getHome($request)
    {
        $id_user = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id_user);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/account/');
        }

        $status_conta = 'Não verificado';
        if ($obUser->status == 1) {
            $status_conta = 'verificado <b><i>V</i></b>';
        }

        $assinaturas = EntityAssinatura::getAssinaturas("user = {$obUser->id}");
        $retAssinaturas = '';
        while ($assinatura = $assinaturas->fetchObject(EntityAssinatura::class)) {
            $plano = EntityPlano::getPlanoById($assinatura->plano);
            $valor = number_format($plano->valor, 2, ',', '.');
            $recorrente = $assinatura->cartao_id !== null ? 'Sim' : 'Não';
            $data = \date('d/m/Y H:i:s', strtotime($assinatura->data));
            $dataExpiracao = $assinatura->expiracao !== null ? \date('d/m/Y H:i:s', strtotime($assinatura->expiracao)) : '';
            $acao = $assinatura->cartao_id !== null && $assinatura->status === 'PAID' ? "<button class='btn btn-sm btn-danger' onClick='cancelarRecorrencia({$assinatura->id});'>Cancelar Recorrencia</button>" : '';
            $retAssinaturas .= "
                        <tr>
                            <td>{$plano->titulo}</td>
                            <td class='text-center'>R$ {$valor}</td>
                            <td class='text-center'>{$assinatura->status()}</td>
                            <td class='text-center text-primary'>{$recorrente}</td>
                            <td>{$data}</td>
                            <td>{$dataExpiracao}</td>
                            <td>{$acao}</td>
                        </tr>
            ";
        }
        $display = '';
        if (empty($retAssinaturas)) {
            $retAssinaturas .= View::render('account/modules/home/semassinatura');
            $display = 'none';
        }

        $obStore = EntityStore::getStoreByUser($id_user);

        // OBTEM A ASSINATURA DO USUARIO
        $assinaturas = EntityAssinatura::getAssinaturaByUserId($obUser->id);
        // $assinaturas = EntityAssinatura::getAssinaturas("user = {$obUser->id}")->fetchObject();

        $plano_id = $assinaturas->plano;

        if (!$assinaturas instanceof EntityAssinatura) {
            $plano_id = 1;
        }

        // OBTEM O PLANO DE ACORDO COM ASSINATURA
        $obPlan = EntityPlano::getPlanById($plano_id);

        $hoje = date('Y-m-d');

        // QUANTIDADE TOTAL DE ANUNCIOS
        $qtdstories = EntityStorie::getStories('id_user = '.$id_user.' AND DATE(created_at) = DATE(NOW())', null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;
   

        
    //     $storiesTable = '';
    //     if ($storiesUser) {
    //         foreach ($storiesUser as $storiesUser) {
    //             // $deleteStore = EntityStorie::excluir($storiesUser->id);
    //             $storiesTable .= "
    //                 <tr>
    //                     <td>
    //                         <img src=".getenv('URL').'/publico/stories/'.$storiesUser->imagem. " alt='Banner' style='width: 100px; height: auto;' />
    //                     </td>
    //                     <td>{$storiesUser->created_at}</td>
    //                     <td>
    //                         <a class='btn botaoperfil' style='float: right;' href='account/storie/{$storiesUser->id}/deletar'>deleta
    //                     </td>
    //                        <td>
    //                         <form action='account/storie/{$storiesUser->id}/deletar' method='POST'>
    //     <button type='submit' class='btn btn-danger'>Deletar</button>
    // </form>
    //                        </td>
    //                 </tr>
    //             ";
    //         }

    //     } else {
    //         $storiesTable .= "<tr><td colspan='3' class='text-center'>Nenhum story encontrado</td></tr>";
    //     }
     

    //     $storiesHtml = "
    //         <table class='table table-bordered'>
    //             <thead>
    //                 <tr>
    //                     <th>Imagem</th>
    //                     <th>Data de Criação</th>
    //                     <th>Ação</th>
    //                 </tr>
    //             </thead>
    //             <tbody>
    //                 {$storiesTable}
    //             </tbody>
    //         </table>
    //     ";
        
        // OBTEM OS BENEFICIOS DO PLANO
        $results = EntityPlano::getPlansFeat('id_plano = "'.$obPlan->id.'"', 'id ASC', null);

        // PERCORRE OS BENEFICIOS DO PLANO
        while ($obPlanoFeat = $results->fetchObject(EntityPlano::class)) {
            $tipo = $obPlanoFeat->slug;

            // Faça algo com os resultados
            $teste[] = [
                $tipo => $obPlanoFeat->value,
            ];
        }
        // var_dump($storiesuser);
        // echo '<br><br>';
        // var_dump($teste);
        $storie = View::render('account/modules/home/form_storie', [
            // 'limite' => $teste[0]['story_dia'],
            // 'stories_table' => $storiesHtml,
            'itens' => self::getListStories($request, $id_user,  $obPagination),
        ]);



        $hoje = date('Y-m-d H:i:s');

        if ($qtdstories >= $teste[6]['story_dia']) {
            $storie = View::render('limites/limite_storie', [
                'limite' => $teste[6]['story_dia'],
            ]);
        }

        $results_cat = EntityCategoria::getCategorias(null, null, null);

        $itens_cat = '';
        while ($obCategoria = $results_cat->fetchObject(EntityPlano::class)) {
            $tipo = $obPlanoFeat->slug;

            $itens_cat .= '<option value="'.$obCategoria->nome_url.'">'.$obCategoria->nome.'</option>';
        }

        // CONTEUDO DA HOME
        $content = View::render('account/modules/home/index', [
            'id_user' => $id_user,
            'id_loja' => $obStore->id,
            'nome_loja' => $obStore->nome_loja,
            'nome_url' => $obStore->nome_url,
            'cidade' => $obStore->cidade,
            'estado2' => $obStore->estado,
            'cep' => $obUser->cep,
            'estado' => $obUser->estado,
            'endereco' => $obUser->endereco,
            'numero' => $obUser->numero,
            'bairro' => $obUser->bairro,
            'complemento' => $obUser->complemento,
            'logotipo' => $obStore->logotipo,
            'banner' => $obStore->banner,
            'nome' => $obUser->nome,
            'sobrenome' => $obUser->sobrenome,
            'email' => $obUser->email,
            'telefone' => $obUser->telefone,
            'cpf' => $obUser->cpf,
            'data_nascimento' => $obUser->data_nascimento,
            'status' => self::getStatus($request),
            'status_conta' => $status_conta,
            'assinaturas' => $retAssinaturas,
            'display' => $display,
            'storie' => $storie,
            'itens_cat' => $itens_cat,
            'anuncios' => self::getAnnounceItems($request, $obPagination),
            'anuncios_vendidos' => self::getAnnounceVendidoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            // ''
        ]);

        // RETORNAR A APGINA COMPLETA
        return parent::getPanel('Minha Conta', $content, 'home');
    }

    public static function getListStories($request, $id_user, &$obPagination) {
        $itens = '';
        $storiesUser = EntityStorie::getStoriesByUserId($id_user);
        // var_dump($storiesUser );
        // exit();
        foreach ($storiesUser as $obCategoria) {
            // VIEW DE DEPOIMENTOS
            $itens .= View::render('account/modules/home/stories_user', [
                'id' => $obCategoria->id,
                'imagem' => $obCategoria->imagem,
                'create_at' => $obCategoria->created_at, // Nome ajustado
            ]);
        }
        // <img src=".getenv('URL').'/publico/stories/'.$storiesUser->imagem. " alt='Banner' style='width: 100px; height: auto;' />
        // var_dump($itens);
        // exit();
        return $itens;
    }

    public static function getDeleteStorie($request, $id)
    {
        // OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obAnnounce = EntityStorie::getStorieById($id);

        // VALIDA A INSTANCIA
        if (!$obAnnounce instanceof EntityAnnounce) {
            $request->getRouter()->redirect('/account');
        }

        // CONTEUDO DO FURMLÁRIO
        $content = View::render('account/modules/home/delete_storie', [
            'id' => $obAnnounce->id,
            'imagem' => $obAnnounce->imagem,
        ]);

        // RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir anúncio', $content, 'users');
    }

    /**
     * Método responsável por gravar a atualização de um usuário.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function setEditUser($request)
    {
        $id_user = $_SESSION['user']['usuario']['id'];
        // OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id_user);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/account');
        }

        // POST VARS
        $postVars = $request->getPostVars();

        $nome = $postVars['nome'] ?? '';
        $sobrenome = $postVars['sobrenome'] ?? '';
        $email = $postVars['email'] ?? '';
        $telefone = $postVars['telefone'] ?? '';
        $cep = $postVars['cep'] ?? '';
        $estado = $postVars['estado'] ?? '';
        $endereco = $postVars['endereco'] ?? '';
        $numero = $postVars['numero'] ?? '';
        $bairro = $postVars['bairro'] ?? '';
        $complemento = $postVars['complemento'] ?? '';

        $cpf = $postVars['cpf'] ?? '';
        $data_nascimento = $postVars['data_nascimento'] ?? '';
        // $senha    = $postVars['senha'] ?? '';

        // VALIDA O EMAIL DO USUÁRIO
        $obUserEmail = EntityUser::getUserByEmail($email);
        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id_user) {
            // REDIRECIONA  O USUARIO
            $request->getRouter()->redirect('/account?status=duplicated');
        }

        // ATUALIZA A INSTÂNCIA
        $obUser->nome = $nome;
        $obUser->sobrenome = $sobrenome;
        $obUser->email = $email;
        $obUser->telefone = $telefone;
        // $obUser->cep = $cep;
        // $obUser->estado = $estado;
        // $obUser->endereco = $endereco;
        // $obUser->numero = $numero;
        // $obUser->bairro = $bairro;
        // $obUser->complemento = $complemento;
        $obUser->cpf = $cpf;
        $obUser->data_nascimento = $data_nascimento;
        // $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar();

        // REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/account?status=updated');
    }

    public static function setEditEndereco($request, $id_user)
    {
        $obUser = EntityUser::getUserById($id_user);

        // POST VARS
        $postVars = $request->getPostVars();

        $cep = $postVars['cep'] ?? '';
        $estado = $postVars['estado'] ?? '';
        $endereco = $postVars['endereco'] ?? '';
        $numero = $postVars['numero'] ?? '';
        $bairro = $postVars['bairro'] ?? '';
        $complemento = $postVars['complemento'] ?? '';

        // ATUALIZA A INSTÂNCIA
        $obUser->cep = $cep;
        $obUser->estado = $estado;
        $obUser->endereco = $endereco;
        $obUser->numero = $numero;
        $obUser->bairro = $bairro;
        $obUser->complemento = $complemento;
        // $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obUser->atualizar_endereco();

        // REDIRECIONA  O USUARIO
        // $request->getRouter()->redirect('/account?status=enderecoatt');
        echo 'success';
    }

    /**
     * Método responsvel por gravar a atualização de uma loja.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function setEditStore($request, $id_loja)
    {
        // OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obStore = EntityStore::getStoreById($id_loja);

        // VALIDA A INSTANCIA
        if (!$obStore instanceof EntityStore) {
            $request->getRouter()->redirect('/account');
        }

        // POST VARS
        $postVars = $request->getPostVars();

        $nome = $postVars['nome'] ?? '';
        $cidade = $postVars['cidade'] ?? '';
        $estado = $postVars['estado'] ?? '';

        $fileVars = $request->getFileVars();

        $logotipo = $fileVars['logotipo'];

        $arquivo = Upload::uploadFile($logotipo);

        $banner = $fileVars['banner'];
        $arquivo = Upload::uploadFile($banner);
        // $senha    = $postVars['senha'] ?? '';

        $logotipo2 = $logotipo['name'];
        if (empty($logotipo['name'])) {
            $logotipo2 = $obStore->logotipo;
        }

        $banner2 = $banner['name'];
        if (empty($banner['name'])) {
            $banner2 = $obStore->banner;
        }

        $nome_novo = strtolower(preg_replace(
            '[^a-zA-Z0-9-]',
            '-',
            strtr(
                utf8_decode(trim($nome)),
                utf8_decode('áàãéêíóôõúüñçÁÀÃÂÉÍÓÔÕÚÜÑÇ'),
                'aaaaeeiooouuncAAAAEEIOOOUUNC-'
            )
        ));
        $url = preg_replace('/[ -]+/', '-', $nome_novo);

        $id_user = $_SESSION['user']['usuario']['id'];

        // VALIDA O EMAIL DO USUÁRIO
        $obUserEmail = EntityStore::getStoreByUrl($url);
        if ($obUserEmail instanceof EntityStore && $obUserEmail->id_user != $id_user) {
            // if ($obUserEmail instanceof EntityStore && $obUserEmail->id != $id_user) {
            // REDIRECIONA  O USUARIO
            // $request->getRouter()->redirect('/account?status=duplicated');

            echo 'loja_duplicada';

            exit;
        }

        // ATUALIZA A INSTÂNCIA
        $obStore->nome_loja = $nome;
        $obStore->cidade = $cidade;
        $obStore->estado = $estado;
        $obStore->logotipo = $logotipo2;
        $obStore->banner = $banner2;
        $obStore->nome_url = $url;
        // $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        $obStore->atualizar();

        // REDIRECIONA  O USUARIO
        // $request->getRouter()->redirect('/account?status=updated');
        echo 'success';
    }

    /**
     * Método responsável por cadastrar o depoimento.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function setStories($request)
    {
        // DADOS DO POST
        $postVars = $request->getPostVars();

        $fileVars = $request->getFileVars();

        $id_user = $_SESSION['user']['usuario']['id'];

        $banner = $fileVars['banner'];
        $arquivo = Upload::uploadStories($banner);

        $data_expira = date('Y-m-d H:i:s', strtotime('+1 days'));

        // NOVA INSTANCIA DE DEPOIMENTO
        $obUser = new EntityStorie();
        $obUser->id_user = $id_user;
        $obUser->imagem = $banner['name'];
        $obUser->expira = $data_expira;
        $obUser->cadastrar();

        // REETORNA A PAGINA DE LISTAGEM DE DEPOIMENTOS
        // $request->getRouter()->redirect('/account?status=storieadd');
        echo 'success';
    }

    /**
     * Método responsável por marcar um anúncio como vendido.
     */
    public static function marcarVendido($request)
    {
        $postVars = $request->getPostVars();

        $anuncio_id = $postVars['anuncio_id'];

        $obAnnounce = EntityAnnounce::getAnnounceById($anuncio_id);

        if (!$obAnnounce instanceof EntityAnnounce) {
            $request->getRouter()->redirect('/account/');
        }

        $obAnnounce->id = $anuncio_id;
        $obAnnounce->vendido = 1;
        $obAnnounce->marcar_vendido();

        // REDIRECIONA  O USUARIO
        // $request->getRouter()->redirect('/account?status=sold');
        echo 'success';
    }

    public static function getDelete($request)
    {
        $id_user = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id_user);

        // VALIDA A INSTANCIA
        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/account/');
        }

        // CONTEUDO DA HOME
        $content = View::render('account/modules/home/delete', [
            'id_user' => $id_user,
        ]);

        // RETORNAR A APGINA COMPLETA
        return parent::getPanel('Deletar conta', $content, 'home');
    }

    /**
     * Método responsável por retornar a mensagem de status.
     *
     * @param Request $request
     *
     * @return string
     */
    private static function getStatus($request)
    {
        // QUERY PARAMS
        $queryParams = $request->getQueryParams();

        // STATUS
        if (!isset($queryParams['status'])) {
            return '';
        }

        // MENSAGENS DE STATUS
        switch ($queryParams['status']) {
            case 'sold':
                return Alert::getSuccess('Anúncio marcado como vendido!');
                break;
            case 'created':
                return Alert::getSuccess('Usuário criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'loja_atualizada':
                return Alert::getSuccess('Loja atualizada com sucesso!');
                break;
            case 'enderecoatt':
                return Alert::getSuccess('Endereço atualizada com sucesso!');
                break;
            case 'anuncioatt':
                return Alert::getSuccess('Anúncio atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluido com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O e-mail digitado já está sendo digitado por outro usuário.');
                break;
            case 'loja_duplicada':
                return Alert::getError('Nome da loja indisponível.');
                break;
            case 'storieadd':
                return Alert::getSuccess('Stories adicionado com sucesso.');
                break;
        }
    }


    public static function setDeleteStorie($request) {
        $postVars = $request->getPostVars();
var_dump($postVars);
exit();
        $id_user = $postVars['id_user'];
    }
}
