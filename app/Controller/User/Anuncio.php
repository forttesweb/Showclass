<?php

namespace App\Controller\User;

use App\Model\Entity\Announce as EntityAnnounce;
use App\Model\Entity\Categoria as EntityCategoria;
use App\Model\Entity\Store as EntityStore;
use App\Services\Upload;
use App\Utils\View;

class Anuncio extends Page
{
    public static function getImagensAnuncio($request, $id_anuncio)
    {
        $obFotos = EntityAnnounce::getAnnounceFotoById($id_anuncio);
        // $results = EntityAnnounce::getFotos('id_anuncio = '.$id_anuncio, null, null);

        $foto_01 = 'https://i.ibb.co/ZVFsg37/default.png';
        $foto_02 = 'https://i.ibb.co/ZVFsg37/default.png';
        $foto_03 = 'https://i.ibb.co/ZVFsg37/default.png';
        $foto_04 = 'https://i.ibb.co/ZVFsg37/default.png';
        $foto_05 = 'https://i.ibb.co/ZVFsg37/default.png';
        $foto_06 = 'https://i.ibb.co/ZVFsg37/default.png';
        $foto_07 = 'https://i.ibb.co/ZVFsg37/default.png';
        $foto_08 = 'https://i.ibb.co/ZVFsg37/default.png';
        $ocultar01 = 'style=display:none;';
        $ocultar02 = 'style=display:none;';
        $ocultar03 = 'style=display:none;';
        $ocultar04 = 'style=display:none;';
        $ocultar05 = 'style=display:none;';
        $ocultar06 = 'style=display:none;';
        $ocultar07 = 'style=display:none;';
        $ocultar08 = 'style=display:none;';

        if ($obFotos instanceof EntityAnnounce) {
            if ($obFotos->foto_01 != '') {
                $foto_01 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_01 . '';
                $ocultar01 = '';
            }

            if ($obFotos->foto_02 != '') {
                $foto_02 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_02 . '';
                $ocultar02 = '';
            }

            if ($obFotos->foto_03 != '') {
                $foto_03 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_03 . '';
                $ocultar03 = '';
            }

            if ($obFotos->foto_04 != '') {
                $foto_04 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_04 . '';
                $ocultar04 = '';
            }

            if ($obFotos->foto_05 != '') {
                $foto_05 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_05 . '';
                $ocultar05 = '';
            }

            if ($obFotos->foto_06 != '') {
                $foto_06 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_06 . '';
                $ocultar06 = '';
            }

            if ($obFotos->foto_07 != '') {
                $foto_07 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_07 . '';
                $ocultar07 = '';
            }

            if ($obFotos->foto_08 != '') {
                $foto_08 = '' . getenv('URL') . '/publico/lojas/' . $obFotos->foto_08 . '';
                $ocultar08 = '';
            }
        }

        // $foto_02 = ''.URL.'/publico/produtos/'.$obFotos->foto_02.'';
        // $foto_03 = ''.URL.'/publico/produtos/'.$obFotos->foto_03.'';
        // $foto_04 = ''.URL.'/publico/produtos/'.$obFotos->foto_04.'';
        // $foto_05 = ''.URL.'/publico/produtos/'.$obFotos->foto_05.'';
        // $foto_06 = ''.URL.'/publico/produtos/'.$obFotos->foto_06.'';
        // $foto_07 = ''.URL.'/publico/produtos/'.$obFotos->foto_07.'';
        // $foto_08 = ''.URL.'/publico/produtos/'.$obFotos->foto_08 ?? 'https://i.ibb.co/ZVFsg37/default.png';

        $boxfotos = View::render('account/modules/home/boxfotos', [
            'foto_01' => $foto_01,
            'foto_02' => $foto_02,
            'foto_03' => $foto_03,
            'foto_04' => $foto_04,
            'foto_05' => $foto_05,
            'foto_06' => $foto_06,
            'foto_07' => $foto_07,
            'foto_08' => $foto_08,
            'ocultar01' => $ocultar01,
            'ocultar02' => $ocultar02,
            'ocultar03' => $ocultar03,
            'ocultar04' => $ocultar04,
            'ocultar05' => $ocultar05,
            'ocultar06' => $ocultar06,
            'ocultar07' => $ocultar07,
            'ocultar08' => $ocultar08,
        ]);

        return $boxfotos;
    }
    // public static function getImagensAnuncio($request, $id_anuncio)
    // {
    //     $results = EntityAnnounce::getFotos('id_anuncio = '.$id_anuncio, null, null);

    //     $itens = '';
    //     // RENDERIZA O ITEM
    //     while ($obAnnounce = $results->fetchObject(EntityAnnounce::class)) {
    //         $itens .= "<div class='boximagensthumb'><img class='ml-4 mb-2' src='".getenv('URL')."/publico/lojas/$obAnnounce->foto_01' width='70'>
    //         <a href='#' style='color:red' onClick='deletarImg(".$obAnnounce->id.")'>Remover</a></div>";
    //     }

    //     return $itens;
    // }

    /**
     * Método responsável por renderizar a vie de home do painel.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function getDadosAnuncio($request, $id_anuncio)
    {
        $obAnuncio = EntityAnnounce::getAnnounceById($id_anuncio);

        echo json_encode($obAnuncio);
    }

    public static function setImagensAnuncio($request)
    {
        $postVars = $request->getPostVars();

        $fileVars = $request->getfileVars();

        $filefoto = $fileVars['imgproduto'];

        Upload::uploadFile($filefoto);

        // $obAnuncioFoto = EntityAnnounce::getAnnounceById($postVars['id22']);
        $obAnnounce = new EntityAnnounce();

        $obAnnounce->nome_arquivo = $filefoto['name'];
        $obAnnounce->cadastrar_fotos($postVars['id22']);

        $output = [
            'msg' => 'Salvo com Sucesso!!',
            'id' => $postVars['id22'],
        ];

        echo json_encode($output);
    }

    // public static function deleteFotos($request)
    // {
    //     $postVars = $request->getPostVars();

    //     $id_foto = $postVars['id_foto'];

    //     $obFoto = EntityAnnounce::getFotoById($id_foto);

    //     // EXCLUIR O DEPOIMENTO
    //     $obFoto->excluir_foto();

    //     $output = [
    //         'msg' => 'Excluído com Sucesso!!',
    //         'id' => $obFoto->id_anuncio,
    //     ];

    //     echo json_encode($output);
    // }

    public static function deleteProdutosFoto($request)
    {
        $postVars = $request->getPostVars();

        $id_anuncio = $postVars['id_anuncio'];
        $id_foto = $postVars['id_foto'];

        $obProduto = EntityAnnounce::getFotos('id_anuncio = ' . $id_anuncio, null, null, $id_foto)->fetchObject();
        // $obProduto = EntityProduto::getFotoByIdAndKey($id_anuncio, $id_foto);

        $nome_foto = $obProduto->$id_foto;

        // unlink(''.PUBLICO.''.$obProduto->$id_foto);

        $obProd = new EntityAnnounce();
        $obProd->atualizar_foto($id_anuncio, $id_foto);

        return $id_anuncio;
    }

    /**
     * Método responsável por marcar um anúncio como vendido.
     */
    public static function editarAnuncio($request)
    {
        $postVars = $request->getPostVars();

        $anuncio_id = $postVars['anuncio_id'];

        $obAnnounce = EntityAnnounce::getAnnounceById($anuncio_id);

        $nome_novo = strtolower(preg_replace(
            '[^a-zA-Z0-9-]',
            '-',
            strtr(
                utf8_decode(trim($postVars['tituloanuncio'])),
                utf8_decode('áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ'),
                'aaaaeeiooouuncAAAAEEIOOOUUNC-'
            )
        ));
        $url = preg_replace('/[ -]+/', '-', $nome_novo);

        if (!$obAnnounce instanceof EntityAnnounce) {
            $request->getRouter()->redirect('/account/');
        }

        $obAnnounce->id = $anuncio_id;
        $obAnnounce->titulo = $postVars['tituloanuncio'];
        $obAnnounce->descricao = $postVars['descricao'];
        $obAnnounce->categoria = $postVars['categoria'];
        $obAnnounce->preco = $postVars['preco'];
        $obAnnounce->forma_envio = $postVars['forma_envio'];
        $obAnnounce->cep = $postVars['cep'];
        $obAnnounce->url = $url;
        $obAnnounce->atualizar();

        // REDIRECIONA  O USUARIO
        // $request->getRouter()->redirect('/account?status=sold');
        echo 'success';
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um usuário.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function getDeleteAnuncio($request, $nome_url)
    {
        // OBTÉM O USUÁRIO DO BANCO DE DADOS
        $obAnnounce = EntityAnnounce::getAnnounceByUrl($nome_url);

        // VALIDA A INSTANCIA
        if (!$obAnnounce instanceof EntityAnnounce) {
            $request->getRouter()->redirect('/account');
        }

        // CONTEUDO DO FURMLÁRIO
        $content = View::render('account/modules/home/delete_anuncio', [
            'id' => $obAnnounce->id,
            'titulo' => $obAnnounce->titulo,
        ]);

        // RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir anúncio', $content, 'users');
    }

    /**
     * Método responsável por excluir um depoimento.
     *
     * @param Request $request
     *
     * @return string
     */
    public static function setDeleteAnuncio($request)
    {
        $postVars = $request->getPostVars();
        $id_anuncio = $postVars['id_anuncio'];
        // OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obAnnounce = EntityAnnounce::getAnnounceById($id_anuncio);

        // VALIDA A INSTANCIA
        if (!$obAnnounce instanceof EntityAnnounce) {
            $request->getRouter()->redirect('/account');
        }

        $results_fotos = EntityAnnounce::getFotos('id_anuncio = ' . $obAnnounce->id, null, null);
        // RENDERIZA O ITEM
        while ($obFotos = $results_fotos->fetchObject(EntityAnnounce::class)) {
            unlink(PUBLICO . '/' . $obFotos->nome_arquivo);
            $obAnnounce->excluir_foto_anuncio($obFotos->id_anuncio);
        }

        $obAnnounce->excluir();

        // REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/account/?status=deleted');
    }

    public static function getEditAnuncio($request, $id_anuncio)
    {
        $id_user = $_SESSION['user']['usuario']['id'];

        $obAnuncio = EntityAnnounce::getAnnounceById($id_anuncio);

        // VALIDA A INSTANCIA
        if (!$obAnuncio instanceof EntityAnnounce) {
            $request->getRouter()->redirect('/account');
        }
        
        if($id_user != $obAnuncio->id_user) {
            $request->getRouter()->redirect('/account');
        }

        


        $results_cat = EntityCategoria::getCategorias(null, null, null);
        $itens_cat = '';
        while ($obCategoria = $results_cat->fetchObject(EntityCategoria::class)) {

            // Faça algo com os resultados
            $itens_cat .= '<option value="' . $obCategoria->nome_url . '">' . $obCategoria->nome . '</option>';
        }

        // CONTEUDO DO FURMLÁRIO
        $content = View::render('account/modules/home/editar_anuncio', [
            'id' => $obAnuncio->id,
            'placa' => $obAnuncio->placa,
            'categoria' => $obAnuncio->categoria,
            'cod_marca' => $obAnuncio->cod_marca,
            'nome_marca' => $obAnuncio->nome_marca,
            'cod_modelo' => $obAnuncio->cod_modelo,
            'nome_modelo' => $obAnuncio->nome_modelo,
            'ano_fab' => $obAnuncio->ano_fab,
            'ano_modelo' => $obAnuncio->ano_modelo,
            'versao' => $obAnuncio->versao,
            'motor' => $obAnuncio->motor,
            'idValvula' => $obAnuncio->idValvula,
            'cambio' => $obAnuncio->cambio,
            'cor' => $obAnuncio->cor,
            'portas' => $obAnuncio->portas,
            'combustivel' => $obAnuncio->combustivel,
            'kilometragem' => $obAnuncio->kilometragem,
            'valor' => $obAnuncio->valor,
            'placa' => $obAnuncio->placa,
            'placa' => $obAnuncio->placa,
            'placa' => $obAnuncio->placa,
            'observacoes' => $obAnuncio->observacoes,
            'tipo_veic' => $obAnuncio->tipo_veic,
            'itens_cat' => $itens_cat,
            'status' => self::getStatus($request)
        ]);

        // RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar anúncio', $content, 'users');
    }

    public static function setEditAnuncio($request, $id_anuncio)
    {
        $id_user = $_SESSION['user']['usuario']['id'];

        $obAnnounce = EntityAnnounce::getAnnounceById($id_anuncio);

        // VALIDA A INSTANCIA
        if (!$obAnnounce instanceof EntityAnnounce) {
            $request->getRouter()->redirect('/account');
        }

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

        $explode = explode("-", $obAnnounce->url);
        $id_key_url = $explode['6'];

        if ($nome_marca != $obAnnounce->nome_marca) {
            $id_key = self::buildIdKey(10);

            $titulobd = $nome_marca . '-' . $nome_modelo;

            $titulo = $nome_marca . '-' . $nome_modelo . '-' . $id_key;
        } else {

            $titulobd = $obAnnounce->nome_marca . '-' . $obAnnounce->nome_modelo;

            $titulo = $obAnnounce->url;
        }

        



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
        $obAnnounce->atualizar();

        $id_annnun = $obAnnounce->id;


        $request->getRouter()->redirect('/editar_anuncio/' . $id_anuncio . '?status=updated');
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

    private static function getStatus($request)
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if (!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Anúncio criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Anúncio atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Anúncio excluido com sucesso!');
                break;
        }
    }
}
