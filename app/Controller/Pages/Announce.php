<?php

namespace App\Controller\Pages;

use App\Model\Entity\Announce as EntityAnnounce;
use App\Model\Entity\Assinatura as EntityAssinatura;
use App\Model\Entity\Categoria as EntityCategoria;
use App\Model\Entity\Lead as EntityLead;
use App\Model\Entity\Plan as EntityPlano;
use App\Model\Entity\Store as EntityStore;
use App\Model\Entity\User as EntityUser;
use App\Services\Upload;
use App\Session\User\Login;
use App\Utils\View;

// require_once __DIR__ . "/../../../includes/websocket.php";

class Announce extends Page
{
    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de Anunciar.
     *
     * @return string
     */
    public static function getAnnounce($request)
    {
        // PEGA O ID DO USUÁRIO LOGADO
        $id_user = $_SESSION['user']['usuario']['id'];

        // OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obUser = EntityUser::getUserById($id_user);

        // OBTEM A ASSINATURA DO USUARIO
        $assinaturas = EntityAssinatura::getAssinaturaByUserId($obUser->id);
        // $assinaturas = EntityAssinatura::getAssinaturas("user = {$obUser->id}")->fetchObject();

        $plano_id = $assinaturas->plano;

        if (!$assinaturas instanceof EntityAssinatura) {
            $plano_id = 1;
        }

        // OBTEM O PLANO DE ACORDO COM ASSINATURA
        $obPlan = EntityPlano::getPlanById($plano_id);

        // QUANTIDADE TOTAL DE ANUNCIOS
        $qtdanuncions = EntityStore::getAnnounces('id_user = ' . $id_user, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        // OBTEM OS BENEFICIOS DO PLANO
        $results = EntityPlano::getPlansFeat('id_plano = "' . $obPlan->id . '"', 'id ASC', null);

        $itens = '';

        // PERCORRE OS BENEFICIOS DO PLANO
        while ($obPlanoFeat = $results->fetchObject(EntityPlano::class)) {
            $tipo = $obPlanoFeat->slug;

            // Faça algo com os resultados
            $teste[] = [
                $tipo => $obPlanoFeat->value,
            ];
        }

        // VERIFICA SE A QUANTIDADE DE ANUNCIOS É MAIOR OU IGUAL A QUANTIDADE DISPONIBILIZADA PELO PLANO
        if ($qtdanuncions >= $teste[0]['itens_loja']) {
            // REDIRECIONA  O USUARIO
            // $request->getRouter()->redirect('/anunciar/?lim=alcancado');
            // VIEW DA HOME
            $content = View::render('pages/anuncio/limite', [
                'limite' => $teste[0]['itens_loja'],
            ]);

            return parent::getPage('Limite atingido', $content);
        }

        $results_cat = EntityCategoria::getCategorias(null, null, null);

        $itens_cat = '';
        while ($obCategoria = $results_cat->fetchObject(EntityCategoria::class)) {
            $tipo = $obPlanoFeat->slug;

            // Faça algo com os resultados
            $itens_cat .= '<option value="' . $obCategoria->nome_url . '">' . $obCategoria->nome . '</option>';
        }

        //LIMITE DE FOTOS PERMITIDA PELO PLANO
        $qtd_fotos = $teste[1]['fotos_itens_loja'];
        $espacofotos = "";

        for ($i = 0; $i < $qtd_fotos; $i++) {
            $ids = $i + 1;

            switch ($ids) {
                case '1':
                    $nomefoto = "img_one";
                    break;
                case '2':
                    $nomefoto = "img_two";
                    break;
                case '3':
                    $nomefoto = "img_three";
                    break;
                case '4':
                    $nomefoto = "img_four";
                    break;
                case '5':
                    $nomefoto = "img_five";
                    break;
                case '6':
                    $nomefoto = "img_six";
                    break;
                case '7':
                    $nomefoto = "img_seven";
                    break;
                case '8':
                    $nomefoto = "img_eight";
                    break;
                case '9':
                    $nomefoto = "img_nine";
                    break;
                case '10':
                    $nomefoto = "img_ten";
                    break;
                case '11':
                    $nomefoto = "img_one";
                    break;
            }

            $espacofotos .= View::render('pages/anuncio/itemfotos', [
                'idii' => $ids,
                'nomefoto' => $nomefoto
            ]);
        }

        // VIEW DA HOME
        $content = View::render('pages/anunciar', [
            'espacofotos' => $espacofotos,
            'qtd_fotos' => $qtd_fotos,
            'itens_cat' => $itens_cat,
            'status' => self::getStatus($request),
        ]);

        // $content = View::render('pages/about', [
        //     'name'        => $obOrganization->name,
        //     'description' => $obOrganization->description,
        //     'site'        => $obOrganization->site
        // ]);
        // RETORNA A VIEW DA PÁGINA
        return parent::getPage('Criar anúncio', $content);
    }

    public static function setAnnounce($request)
    {
        $id_user = $_SESSION['user']['usuario']['id'];
        // POST VARS
        $postVars = $request->getPostVars();
        $placa = $postVars['placa'] ?? '';
        $categoria = $postVars['categoria'] ?? '';
        $cod_marca = $postVars['idMarca'] ?? '';
        $nome_marca = $postVars['nome_marca'] ?? '';

        $cod_modelo = $postVars['modeloCarro'] ?? '';
        $nome_modelo = $postVars['nome_modelo'] ?? '';

        $ano_fab = $postVars['anoFabricacao'] ?? '';
        $ano_modelo = $postVars['anoModelo'] ?? '';

        $versao = $postVars['versao'] ?? '';

        $motor = $postVars['motor'] ?? '';

        $idValvula = $postVars['idValvula'] ?? '';

        $cambio = $postVars['cambio'] ?? '';

        $cor = $postVars['cor'] ?? '';

        $portas = $postVars['portas'] ?? '';

        $combustivel = $postVars['combustivel'] ?? '';

        $kilometragem = $postVars['kilometragem'] ?? '';
        $observacoes = $postVars['observacoes'] ?? '';
        $valor = $postVars['valor'] ?? '';
        $tipo_veic = $postVars['tipo_veic'] ?? '';

        // $cep = $postVars['cep'] ?? '';

        $checkboxacessorios = $postVars['checkboxacessorios'];

        $id_key = self::buildIdKey(10);

        $titulobd = $nome_marca . '-' . $nome_modelo;

        $titulo = $nome_marca . '-' . $nome_modelo . '-' . $id_key;

        $nome_novo = strtolower(preg_replace(
            '[^a-zA-Z0-9-]',
            '-',
            strtr(
                utf8_decode(trim($titulo)),
                utf8_decode('áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ'),
                'aaaaeeiooouuncAAAAEEIOOOUUNC-'
            )
        ));
        $url = preg_replace('/[ -]+/', '-', $nome_novo);

        // VALIDA O E-MAIL DO USUÁRIO
        // $obAnnounce = EntityAnnounce::getAnnounceByTitle($titulo);
        // if ($obAnnounce instanceof EntityAnnounce) {
        //     //REDIRECIONA  O USUARIO
        //     $request->getRouter()->redirect('/anunciar?status=duplicated');
        // }

        $obStore = EntityStore::getStoreByUser($id_user);

        $id_store = $obStore->id;

        // NOVA INSTÃNCIA DE DEPOIMENTO
        $obAnnounce = new EntityAnnounce();
        $obAnnounce->id_user = $id_user;
        $obAnnounce->id_store = $id_store;
        $obAnnounce->vendido = '0';
        $obAnnounce->titulo = $titulobd;
        $obAnnounce->placa = $placa;
        $obAnnounce->categoria = $categoria;
        $obAnnounce->cod_marca = $cod_marca;
        $obAnnounce->nome_marca = $nome_marca;
        $obAnnounce->cod_modelo = $cod_modelo;
        $obAnnounce->nome_modelo = $nome_modelo;
        $obAnnounce->ano_fab = $ano_fab;
        $obAnnounce->ano_modelo = $ano_modelo;
        $obAnnounce->versao = $versao;
        $obAnnounce->motor = $motor;
        $obAnnounce->idValvula = $idValvula;
        $obAnnounce->cambio = $cambio;
        $obAnnounce->cor = $cor;
        $obAnnounce->portas = $portas;
        $obAnnounce->combustivel = $combustivel;
        $obAnnounce->kilometragem = $kilometragem;
        $obAnnounce->observacoes = $observacoes;
        $obAnnounce->valor = $valor;
        $obAnnounce->url = $url;
        $obAnnounce->tipo_veic = $tipo_veic;
        $obAnnounce->cadastrar();

        $id_annnun = $obAnnounce->id;

        $fileVars = $request->getFileVars();

        // $filefoto = $postVars['file'];

        $checkboxacessoriosarr = [];
        $id_anuncio = [];

        if (is_array($checkboxacessorios)) {
            for ($i = 0; $i < count($checkboxacessorios); ++$i) {
                $id_anuncio[$i] = $id_annnun;
                $checkboxacessoriosarr[$i] = $checkboxacessorios[$i];
            }
        }
        for ($i = 0; $i < count($checkboxacessorios); ++$i) {
            $obAnnounce->id_opcional = $checkboxacessoriosarr[$i];
            $obAnnounce->cadastrar_opcionais($id_anuncio[$i], $checkboxacessoriosarr[$i]);
        }

        $results_seguidor = EntityStore::getSeguidores('id_store = ' . $id_store, null, null);

        while ($obSeguidor = $results_seguidor->fetchObject(EntityAnnounce::class)) {
            $obAnnounce->id_seguidor = $obSeguidor->id_seguidor;
            $obAnnounce->visualizada = 0;
            $obAnnounce->cadastrar_notificacao($id_annnun);
        }

        return $id_annnun;

        // REDIRECIONA  O USUARIO
        // $request->getRouter()->redirect('/anunciar?status=created');
    }

    /**
     * Método responsável por retornar o formulário de edição de um depoimento.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function getAds($request, $loja, $anuncio)
    {
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obAnnounce = EntityAnnounce::getAnnounceByUrl($anuncio);
        // honda-civic-sedan-lxs-1.8/1.8-flex-16v-aut.-4p-x7qhpzu9e0

        $obStore = EntityStore::getStoreById($obAnnounce->id_store);

        $id_logado = Login::isLogged();

        $fotos = '';
        $fotosmain = '';
        $avaliacoes = '';

        // RESULTADOS DA PAGINA
        $results = EntityAnnounce::getFotos('id_anuncio = ' . $obAnnounce->id, 'id DESC', null);

        $results2 = EntityStore::getStoreReviews('id_store = ' . $obStore->id, null, null, 'AVG(rating) as totalrate')->fetchObject()->totalrate;
        $results22 = EntityStore::getStoreReviews('id_store = ' . $obStore->id, null, null, 'COUNT(*) as totalcoment')->fetchObject()->totalcoment;

        $results3 = EntityStore::getStoreReviews('id_store = ' . $obStore->id, 'id DESC', null);

        $rate_bg = ($results2 / 5) * 100;

        // RENDERIZA O ITEM
        while ($obFotos = $results->fetchObject(EntityAnnounce::class)) {
            // VIEW DE DEPOIMENTOS

            if ($obFotos->foto_01 != '') {
                $fotos .= '<div class="item">
                        <img src="' . URL . '/publico/lojas/' . $obFotos->foto_01 . '" alt=""
                            class="img-thumbnail">
                    </div>';
            }

            if ($obFotos->foto_02 != '') {
                $fotos .= '<div class="item">
                        <img src="' . URL . '/publico/lojas/' . $obFotos->foto_02 . '" alt=""
                            class="img-thumbnail">
                    </div>';
            }

            if ($obFotos->foto_03 != '') {
                $fotos .= '<div class="item">
                        <img src="' . URL . '/publico/lojas/' . $obFotos->foto_03 . '" alt=""
                            class="img-thumbnail">
                    </div>';
            }

            if ($obFotos->foto_04 != '') {
                $fotos .= '<div class="item">
                <img src="' . URL . '/publico/lojas/' . $obFotos->foto_04 . '" alt=""
                    class="img-thumbnail">
                </div>';
            }

            if ($obFotos->foto_05 != '') {
                $fotos .= '<div class="item">
                <img src="' . URL . '/publico/lojas/' . $obFotos->foto_05 . '" alt=""
                    class="img-thumbnail">
                </div>';
            }
            if ($obFotos->foto_06 != '') {
                $fotos .= '<div class="item">
                <img src="' . URL . '/publico/lojas/' . $obFotos->foto_06 . '" alt=""
                    class="img-thumbnail">
                </div>';
            }
            if ($obFotos->foto_07 != '') {
                $fotos .= '<div class="item">
                <img src="' . URL . '/publico/lojas/' . $obFotos->foto_07 . '" alt=""
                    class="img-thumbnail">
                </div>';
            }
            if ($obFotos->foto_08 != '') {
                $fotos .= '<div class="item">
                <img src="' . URL . '/publico/lojas/' . $obFotos->foto_08 . '" alt=""
                    class="img-thumbnail">
                </div>';
            }

            // $fotos .= '<div class="item">
            //             <img src="'.URL.'/publico/lojas/'.$obFotos->foto_05.'" alt=""
            //                 class="img-thumbnail">
            //         </div>';
            // $fotos .= '<div class="item">
            //             <img src="'.URL.'/publico/lojas/'.$obFotos->foto_06.'" alt=""
            //                 class="img-thumbnail">
            //         </div>';
            // $fotos .= '<div class="item">
            //             <img src="'.URL.'/publico/lojas/'.$obFotos->foto_07.'" alt=""
            //                 class="img-thumbnail">
            //         </div>';
            // $fotos .= '<div class="item">
            //             <img src="'.URL.'/publico/lojas/'.$obFotos->foto_08.'" alt=""
            //                 class="img-thumbnail">
            //         </div>';

            if ($obFotos->foto_01 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_01 . '" alt="">
                    </div>';
            }

            if ($obFotos->foto_02 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_02 . '" alt="">
                    </div>';
            }

            if ($obFotos->foto_03 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_03 . '" alt="">
                    </div>';
            }
            if ($obFotos->foto_04 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_04 . '" alt="">
                    </div>';
            }
            if ($obFotos->foto_05 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_05 . '" alt="">
                    </div>';
            }
            if ($obFotos->foto_06 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_06 . '" alt="">
                    </div>';
            }
            if ($obFotos->foto_07 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_07 . '" alt="">
                    </div>';
            }
            if ($obFotos->foto_08 != '') {
                $fotosmain .= '<div class="item">
                        <img class="imgmain" src="' . URL . '/publico/lojas/' . $obFotos->foto_08 . '" alt="">
                    </div>';
            }

            // $fotosmain .= '<div class="item">
            //             <img class="imgmain" src="'.URL.'/publico/lojas/'.$obFotos->foto_04.'" alt="">
            //         </div>';
            // $fotosmain .= '<div class="item">
            //             <img class="imgmain" src="'.URL.'/publico/lojas/'.$obFotos->foto_05.'" alt="">
            //         </div>';
            // $fotosmain .= '<div class="item">
            //             <img class="imgmain" src="'.URL.'/publico/lojas/'.$obFotos->foto_06.'" alt="">
            //         </div>';
            // $fotosmain .= '<div class="item">
            //             <img class="imgmain" src="'.URL.'/publico/lojas/'.$obFotos->foto_07.'" alt="">
            //         </div>';
            // $fotosmain .= '<div class="item">
            //             <img class="imgmain" src="'.URL.'/publico/lojas/'.$obFotos->foto_08.'" alt="">
            //         </div>';
        }

        $foto = $obFotos->foto_01;
        if (empty($fotosmain)) {
            $fotosmain = '<div class="item">
            <img class="imgmain" src="' . URL . '/publico/lojas/produtosemfoto2.png" alt="">
        </div>';
        }
        if (empty($fotos)) {
            $fotos = '<div class="item">
                        <img src="' . URL . '/publico/lojas/produtosemfoto2.png" alt=""
                            class="img-thumbnail">
                    </div>';
        }

        while ($obAvaliacoes = $results3->fetchObject(EntityStore::class)) {
            // $results22 = EntityStore::getStoreReviews('id_user = ' . $obAvaliacoes->id_user, null, null, 'AVG(rating) as totalrate')->fetchObject()->totalrate;

            switch ($obAvaliacoes->rating) {
                case '1':
                    $rate_bg2 = '20';
                    break;
                case '2':
                    $rate_bg2 = '40';
                    break;
                case '3':
                    $rate_bg2 = '60';
                    break;
                case '4':
                    $rate_bg2 = '80';
                    break;
                case '5':
                    $rate_bg2 = '100';
                    break;
            }

            // $rate_bg2 = ( / 5) * 100 . 0;

            $data_comentario = date('d/m/Y H:i:s', strtotime($obAvaliacoes->created_at));

            // VIEW DA HOME
            $avaliacoes .= View::render('pages/anuncio/item', [
                'comentario' => $obAvaliacoes->comentario,
                'data_comentario' => $data_comentario,
                'totalrate' => $rate_bg2,
            ]);
        }

        $results_opcionais = EntityAnnounce::getOpcionais('id_anuncio = ' . $obAnnounce->id, 'id DESC', null);

        $opcionais = '';

        while ($obOpcionais = $results_opcionais->fetchObject(EntityStore::class)) {
            $obOp = EntityAnnounce::getOpcionalById($obOpcionais->id_opcional);

            $opcionais .= '
            <li class="acessorio">
                        <span class="description-print">' . $obOp->nome_opcional . '</span>
                    </li>
            ';
        }

        if (empty($opcionais)) {
            $opcionais = '<span>Sem opcionais cadastrados.</span>';
        }

        $form_review = View::render('pages/anuncio/form_review', [
            'anuncio' => $anuncio,
            'id_user' => $id_logado,
            'id_loja' => $obAnnounce->id_store,
            'nome_loja' => $obStore->nome_url,
        ]);
        if (empty($id_logado)) {
            $form_review = 'Você precisa estar logado para avaliar !';
        }

        $anofab = explode('-', $obAnnounce->ano_fab);
        $anomodel = explode('-', $obAnnounce->ano_modelo);

        $anomodelooo = $anofab[0] . '/' . $anomodel[0];

        $obUserLogged = Login::userData();
        $obUser = EntityUser::getUserById($obStore->id_user);

        $itens_rela = '';
        $results_relacionados = EntityAnnounce::getAnuncios('nome_marca = "' . $obAnnounce->nome_marca . '" AND id <> ' . $obAnnounce->id, 'id DESC', null);
        while ($obRelacionados = $results_relacionados->fetchObject(EntityStore::class)) {
            // RESULTADOS DA PAGINA
            $obFoto = EntityAnnounce::getAnnounceFotoById($obRelacionados->id);

            // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
            $obStore = EntityStore::getStoreById($obRelacionados->id_store);
            $id_store = $obStore->id;

            $nome_marca = $obRelacionados->nome_marca;
            $nome_modelo = $obRelacionados->nome_modelo;

            $teexte = explode(' ', $nome_marca);
            $teexte2 = explode(' ', $nome_modelo);

            $nome_marca1 = $teexte[0];
            $nome_modelo1 = $teexte2[0];

            $titulooooo = $nome_marca . ' ' . $nome_modelo1;

            // VIEW DE DEPOIMENTOS
            $itens_rela .= View::render('pages/busca/item', [
                'nome_url' => $obStore->nome_url,
                'titulo' => $titulooooo,
                'nome_modelo' => $nome_modelo,
                'categoria' => $obRelacionados->categoria,
                // 'preco' => $obAccounces->preco,
                'preco' => number_format($obRelacionados->valor, 2, ',', '.'),
                'url' => $obRelacionados->url,
                'foto' => $obFoto->foto_01,
            ]);
        }
        if (empty($itens_rela)) {
            $itens_rela = '<p>Sem anuncios relacionados.</p>';
        }
        // var_dump($obStore);exit();
        // VIEW DA HOME
        $content = View::render('pages/announ', [
            'id_anuncio' => $obAnnounce->id,
            'nome_anunciante' => $obUser->nome,
            'telefone_anunciante' => $obUser->telefone,
            'cidade_anunciante' => $obStore->cidade,
            'nome_loja' => $obStore->nome_loja,
            'nome_url' => $obStore->nome_url,
            'totalcoment' => $results22,
            'totalrate' => $rate_bg,
            'fotos' => $fotos,
            'fotosmain' => $fotosmain,
            'titulo' => $obAnnounce->titulo,
            'descricao' => $obAnnounce->descricao,
            'categoria' => $obAnnounce->categoria,
            // 'preco' => $obAnnounce->valor,
            'kilometragem' => $obAnnounce->kilometragem,
            'cambio' => $obAnnounce->cambio,
            'anomodelo' => $anomodelooo,
            'portas' => $obAnnounce->portas,
            'combustivel' => $obAnnounce->combustivel,
            'placa' => ucfirst(mb_strcut($obAnnounce->placa, 0, -3)) . '***',
            'cor' => $obAnnounce->cor,
            'preco' => number_format($obAnnounce->valor, 2, ',', '.'),
            'avaliacoes' => $avaliacoes,
            'form_review' => $form_review,
            'id_user' => $id_logado,
            'id_user_store' => $obAnnounce->id_user,
            'token' => $obUserLogged['token'],
            'nome_online' => $obUserLogged['nome'],
            'opcionais' => $opcionais,
            'itens_rela' => $itens_rela,
            'status' => self::getStatus($request),
        ]);

        // $content = View::render('pages/about', [
        //     'name'        => $obOrganization->name,
        //     'description' => $obOrganization->description,
        //     'site'        => $obOrganization->site
        // ]);
        // RETORNA A VIEW DA PÁGINA
        return parent::getPage($obAnnounce->titulo, $content);
    }

    /**
     * Método responsável por fazer upload das fotos do anúncio.
     *
     * @param [type] $request
     *
     * @return void
     */
    public static function UploadFotos($request)
    {
        $postVars = $request->getPostVars();

        $id_anuncio = $postVars['id_anuncio'];
        // $foto = $postVars['foto_01'];

        $fileVars = $request->getFileVars();
        $primeiraChave = key($fileVars);

        $foto = $fileVars[$primeiraChave];

        $arquivo = Upload::uploadFile($foto);

        $obFoto = EntityAnnounce::getAnnounceFotoById($id_anuncio);
        if (!$obFoto instanceof EntityAnnounce) {
            $obFoto = new EntityAnnounce();
            $obFoto->$primeiraChave = $foto['name'];
            $obFoto->cadastrar_fotos($id_anuncio);

            return true;
        }

        $obFoto->$primeiraChave = $foto['name'];
        $obFoto->atualizar_fotos($id_anuncio);

        return true;
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
            case 'created':
                return Alert::getSuccess('Anúncio criado com sucesso!<br>');
                break;
            case 'updated':
                return Alert::getSuccess('Usuário atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Usuário excluido com sucesso!');
                break;
            case 'duplicated':
                return Alert::getError('O e-mail digitado já está sendo digitado por outro usuário.');
                break;
            case 'review':
                return Alert::getSuccess('Avaliação adicionada com sucesso !');
                break;
            case 'error':
                return Alert::getError('O código inserido está incorreto.');
                break;
            case 'alcancado':
                return Alert::getError('Você atingiu o limite de anúncios publicados.');
                break;
        }
    }

    public static function setContato($request, $nome_loja)
    {
        $postVars = $request->getPostVars();

        $fullName = $postVars['fullName'];
        $phone = $postVars['phone'];
        $email = $postVars['email'];
        $message = $postVars['message'];

        $obLead = new EntityLead();

        $obLead->fullname = $fullName;
        $obLead->phone = $phone;
        $obLead->email = $email;
        $obLead->cadastrar();

        return 'success';
    }

    public static function buildIdKey($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $idKeyLength = $length;
        $idKey = '';

        for ($i = 0; $i < $idKeyLength; ++$i) {
            $randomIndex = rand(0, strlen($characters) - 1);
            $idKey .= $characters[$randomIndex];
        }

        return $idKey;
    }
}
