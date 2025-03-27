<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Pagina as EntityPagina;
use \WilliamCosta\DatabaseManager\Pagination;

class Pagina extends Page
{

    /**
     * Método responsável por obter a renderização dos itens de depoimentos para página
     * @param Request $request
     * @param Pagination $obPagination
     * @return string
     */
    private static function getPaginaItems($request, &$obPagination)
    {
        //DEPOIMENTOS
        $itens = '';

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadetotal = EntityPagina::getPaginas(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PAGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;


        //INSTANCIA DE PAGINACAO
        $obPagination = new Pagination($quantidadetotal, $paginaAtual, 10);


        //RESULTADOS DA PAGINA
        $results = EntityPagina::getPaginas(null, 'id ASC', $obPagination->getLimit());

        //RENDERIZA O ITEM
        while ($obPagina = $results->fetchObject(EntityPagina::class)) {
            //VIEW DE DEPOIMENTOS
            $itens .= View::render('admin/modules/paginas/item', [
                'id' => $obPagina->id,
                'nome' => $obPagina->nome,
                'nome_url' => $obPagina->nome_url,
                'titlesect1' => $obPagina->titlesect1,
                'contentsect1' => $obPagina->contentsect1,
                'titlesect2' => $obPagina->titlesect2,
                'contentsect2' => $obPagina->contentsect2,
                'titlesect3' => $obPagina->titlesect3,
                'contentsect3' => $obPagina->contentsect3,
                'titlesect4' => $obPagina->titlesect4,
                'contentsect4' => $obPagina->contentsect4,
                'titlesect5' => $obPagina->titlesect5,
                'contentsect5' => $obPagina->contentsect5,
                'data' => date('d/m/Y H:i:s', strtotime($obPagina->created_at)),
            ]);
        }


        //RETORNA OS DEPOIMENTOS
        return $itens;
    }

    /**
     * Método responsável por renderizar a vie de listagem de depoimentos
     *
     * @param Request $request
     * @return string
     */
    public static function getPaginas($request)
    {
        //CONTEUDO DA HOME
        $content = View::render('admin/modules/paginas/index', [
            'itens' => self::getPaginaItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Páginas', $content, 'paginas');
    }
    /**
     * Método responsável por retornar o formulário de edição de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function getDynEditPagina($request, $nomeurl)
    {
        //PAGINA ATUAL

        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        // $obPagina = EntityPagina::getPaginaById($id);
        $obPagina = EntityPagina::getPaginaByUrl("'$nomeurl'");

        //VALIDA A INSTANCIA
        if (!$obPagina instanceof EntityPagina) {
            $request->getRouter()->redirect('/admin/paginas');
        }

        //CONTEUDO DO FURMLÁRIO
        $content = View::render('admin/modules/paginas/form_' . $nomeurl, [
            'title'    => 'Editar página',
            'nome'     => $obPagina->nome,
            'nome_url' => $obPagina->nome_url,
            'titlesect1' => $obPagina->titlesect1,
            'contentsect1' => $obPagina->contentsect1,
            'titlesect2' => $obPagina->titlesect2,
            'contentsect2' => $obPagina->contentsect2,
            'titlesect3' => $obPagina->titlesect3,
            'contentsect3' => $obPagina->contentsect3,
            'titlesect4' => $obPagina->titlesect4,
            'contentsect4' => $obPagina->contentsect4,
            'titlesect5' => $obPagina->titlesect5,
            'contentsect5' => $obPagina->contentsect5,
            'status' => self::getStatus($request)
        ]);

        //RETORNAR A PÁGINA COMPLETA
        return parent::getPanel('Editar página - '. $obPagina->nome, $content, 'paginas');
    }

    /**
     * Método responsável por gravar a atualizaçõ de um depoimento
     *
     * @param Request $request
     * @param integer $id
     * @return string
     */
    public static function setDynEditPagina($request, $nomeurl)
    {
        //OBTÉM O DEPOIMENTO DO BANCO DE DADOS
        // $obPagina = EntityPagina::getPaginaById($id);
        $obPagina = EntityPagina::getPaginaByUrl("'$nomeurl'");

        //VALIDA A INSTANCIA
        if (!$obPagina instanceof EntityPagina) {
            $request->getRouter()->redirect('/admin/paginas');
        }

        //POST VARS
        $postVars = $request->getPostVars();

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

        //ATUALIZA A INSTÂNCIA
        $obPagina->nome = $postVars['nome'] ?? $obPagina->nome;
        $obPagina->nome_url = $url ?? $obPagina->nome_url;
        $obPagina->titlesect1 = $postVars['titlesect1'] ?? $obPagina->titlesect1;
        $obPagina->contentsect1 = $postVars['contentsect1'] ?? $obPagina->contentsect1;
        $obPagina->titlesect2 = $postVars['titlesect2'] ?? $obPagina->titlesect2;
        $obPagina->contentsect2 = $postVars['contentsect2'] ?? $obPagina->contentsect2;
        $obPagina->titlesect3 = $postVars['titlesect3'] ?? $obPagina->titlesect3;
        $obPagina->contentsect3 = $postVars['contentsect3'] ?? $obPagina->contentsect3;
        $obPagina->titlesect4 = $postVars['titlesect4'] ?? $obPagina->titlesect4;
        $obPagina->contentsect4 = $postVars['contentsect4'] ?? $obPagina->contentsect4;
        $obPagina->titlesect5 = $postVars['titlesect5'] ?? $obPagina->titlesect5;
        $obPagina->contentsect5 = $postVars['contentsect5'] ?? $obPagina->contentsect5;
        $obPagina->atualizar();

        //REDIRECIONA  O USUARIO
        $request->getRouter()->redirect('/admin/pagina-' . $obPagina->nome_url . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar a conteudo de status
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
                return Alert::getSuccess('Página criado com sucesso!');
                break;
            case 'updated':
                return Alert::getSuccess('Página atualizado com sucesso!');
                break;
            case 'deleted':
                return Alert::getSuccess('Página excluido com sucesso!');
                break;
        }
    }
}