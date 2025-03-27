<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Categoria as EntityCategoria;
use \App\Services\Upload;
use \WilliamCosta\DatabaseManager\Pagination;

class Categoria extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de categorias para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getCategoriaItems($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityCategoria::getCategorias(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 8);


        //RESULTADOS DA PAGINA
        $results = EntityCategoria::getCategorias(null, 'id DESC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obCategoria = $results->fetchObject(EntityCategoria::class)) {
            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/categorias/item', [
                'id' => $obCategoria->id,
                'nome' => $obCategoria->nome,
                'mensagem' => $obCategoria->mensagem,
                'imagem' => $obCategoria->imagem,
                'data' => date('d/m/Y H:i:s', strtotime($obCategoria->data)),
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a vie de listagem de categorias
     *
     * @param Request $request
     * @return string
     */
    public static function getCategorias($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/categorias/index', [
            'itens' => self::getCategoriaItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Categorias', $content, 'categorias');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo categoria
     *
     * @param Request $request
     * @return string
     */
    public static function getNewCategoria($request)
    {
        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/categorias/form', [
            'title'    => 'Cadastrar categoria',
            'nome'     => '',
            'mensagem' => '',
            'imagem' => '',
            'status'   => ''
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Cadastrar categoria asasd', $content, 'categorias');
    }

    /**
     * Método responsável por cadastrar um categoria no banco
     *
     * @param Request $request
     * @return string
     */
    public static function setNewCategoria($request)
    {
        //POST VARS
        $postVars = $request->getPostVars();
        $fileVars = $request->getFileVars();

        $nome_novo = strtolower(preg_replace(
            "[^a-zA-Z0-9-]",
            "-",
            strtr(
                utf8_decode(trim($postVars['nome'])),
                utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"),
                "aaaaeeiooouuncAAAAEEIOOOUUNC-"
            )
        ));
        $url = preg_replace('/[ -]+/', '-', $nome_novo);


        $imagemcat = $fileVars['imagem'];

        Upload::uploadFileCat($imagemcat);

        //NOVA INSTÃNCIA DE DEPOIMENTO
        $obCategoria = new EntityCategoria;
        $obCategoria->nome = $postVars['nome'] ?? '';
        $obCategoria->nome_url = $url;
        $obCategoria->imagem = $imagemcat['name'];
        $obCategoria->cadastrar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/categorias/' . $obCategoria->id . '/edit?status=created');
    }

    /**
     * Método responsável por retornar a mensagem de status
     *
     * @param Request $request
     * @return string
     */
    private static function getStatus($request)
    {
        //QUERY PARAMS
        $queryParams = $request->getQueryParams();

        //STATUS
        if (!isset($queryParams['status'])) return '';

        //MENSAGENS DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return Alert::getSuccess('Categoria criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Categoria atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Categoria excluido com sucesso!');
                break;
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um categoria
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getEditCategoria($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obCategoria = EntityCategoria::getCategoriaById($id);

        //VALIDA A INSTANCIA
        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin-panel/categorias');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/categorias/form', [
            'title'    => 'Editar categoria',
            'nome'     => $obCategoria->nome,
            'nome_url' => $obCategoria->nome_url,
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar categoria', $content, 'categorias');
    }

    /**
     * Método responsável por gravar a atualizaçõ de um categoria
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setEditCategoria($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obCategoria = EntityCategoria::getCategoriaById($id);

        //VALIDA A INSTANCIA
        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin-panel/categorias');
        }

        //POST VARS
        $postVars = $request->getPostVars();
        $fileVars = $request->getFileVars();
        

        $nome_novo = strtolower(preg_replace(
            "[^a-zA-Z0-9-]",
            "-",
            strtr(
                utf8_decode(trim($postVars['nome'])),
                utf8_decode("áàãâéêíóôõúüñçÁÀÃÂÉÊÍÓÔÕÚÜÑÇ"),
                "aaaaeeiooouuncAAAAEEIOOOUUNC-"
            )
        ));
        $url = preg_replace('/[ -]+/', '-', $nome_novo);

        $imagemcat = $fileVars['imagem'];

        Upload::uploadFileCat($imagemcat);

        //ATUALIZA A INSTÂNCIA
        $obCategoria->nome = $postVars['nome'] ?? $obCategoria->nome;
        $obCategoria->nome_url = $url;
        $obCategoria->imagem = $imagemcat['name'];
        $obCategoria->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/categorias/' . $obCategoria->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um categoria
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDeleteCategoria($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obCategoria = EntityCategoria::getCategoriaById($id);

        //VALIDA A INSTANCIA
        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin-panel/categorias');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/categorias/delete', [
            'nome'     => $obCategoria->nome,
            'nome_url' => $obCategoria->nome_url
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Excluir categoria', $content, 'categorias');
    }

    /**
     * Método responsável por excluir um categoria
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDeleteCategoria($request, $id)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        $obCategoria = EntityCategoria::getCategoriaById($id);

        //VALIDA A INSTANCIA
        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin-panel/categorias');
        }

        //EXCLUIR O DEPOIMENTO
        $obCategoria->excluir();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin-panel/categorias?status=deleted');
    }
}
