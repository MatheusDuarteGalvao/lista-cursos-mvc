<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Model\Entity\Course as EntityCourse;
use \App\Database\Pagination;

class Course extends Page{

	/**
	 * Método responsável por obter a renderização dos items de cursos para a página
	 * @param Request $request
	 * @param Pagination $obPagination
	 * @return string
	*/
	private static function getCourseItems($request,&$obPagination){
		//CURSOS
		$items = '';

		//QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityCourse::getCourses(null, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

		//PÁGINA ATUAL
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;

		//INSTÂNCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

		//RESULTADOS DA PÁGINA
		$results = EntityCourse::getCourses(null,'id DESC',$obPagination->getLimit());

		//RENDERIZA O ITEM
		while($obCourse = $results->fetchObject(EntityCourse::class)){
			$items .= View::render('admin/modules/courses/item',[
				'id'         => $obCourse->id,
				'nome'       => $obCourse->nome,
				'descricao'  => $obCourse->descricao,
				'nota'       => $obCourse->nota
			]);
		}

		//RETORNA OS CURSOS
		return $items;
	}

	/**
	 * Método responsável por por renderizar a view de listagem de cursos
	 * @param Request $request
	 * @return string
	 */
	public static function getCourses($request){
		//CONTEÚDO DA HOME
		$content = View::render('admin/modules/courses/index',[
			'items'      => self::getCourseItems($request,$obPagination),
			'pagination' => parent::getPagination($request,$obPagination),
			'status'     => self::getStatus($request)
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Cursos > Admin',$content, 'courses');
	}

	/**
	 * Método responsável por retornar o formulário de cadastro de um novo curso
	 * @param Request $request
	 * @return string
	 */
	public static function getNewCourse($request){
		//CONTEÚDO DO FORMULÁRIO
		$content = View::render('admin/modules/courses/form',[
			'title'     => 'Cadastrar curso',
			'nome'      => '',
			'descricao' => '',
			'nota'      => '',
			'usuario'   => '',
			'status'    => ''
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Cadastrar curso > Admin',$content, 'courses');
	}

	/**
	 * Método responsável por cadastrar um curso no banco
	 * @param Request $request
	 * @return string
	 */
	public static function setNewCourse($request){
		//POST VARS
		$postVars = $request->getPostVars();

		//NOVA INSTÂNCIA DE CURSO
		$obCourse            = new EntityCourse;
		$obCourse->nome      = $postVars['nome'] ?? '';
		$obCourse->descricao = $postVars['descricao'] ?? '';
		$obCourse->nota      = $postVars['nota'] ?? '';
		$obCourse->usuario   = $postVars['usuario'] ?? '';
		$obCourse->cadastrar();

		//REDIRECIONA O USUÁRIO
		$request->getRouter()->redirect('/admin/courses/'.$obCourse->id.'/edit?status=created');
	}

	/**
	 * Método responsável por retornar o formulário de edição
	 * @param Request $request
	 * @return string
	 */
	private static function getStatus($request){
		//QUERY PARAMS
		$queryParams = $request->getQueryParams();

		//STATUS
		if(!isset($queryParams['status'])) return '';

		//MENSAGENS DE STATUS
		switch ($queryParams['status']) {
			case 'created':
				return Alert::getSuccess('Curso criado com sucesso!');
				break;
			case 'updated':
				return Alert::getSuccess('Curso atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Curso excluído com sucesso!');
				break;
		}
	}

	/**
	 * Método responsável por retornar o formulário de edição de um curso
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function getEditCourse($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obCourse = EntityCourse::getCourseById($id);

		//VALIDA A INSTÂNCIA
		if(!$obCourse instanceof EntityCourse){
			$request->getRouter()->redirect('/admin/courses');
		}

		//CONTEÚDO DO FORMULÁRIO
		$content = View::render('admin/modules/courses/form',[
			'title'      => 'Editar curso',
			'nome'       => $obCourse->nome,
			'descricao'  => $obCourse->descricao,
			'nota'       => $obCourse->nota,
			'usuario'    => $obCourse->usuario,
			'status'     => self::getStatus($request)
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Editar curso > Admin',$content, 'courses');
	}

	/**
	 * Método responsável por gravar a atualização de um curso
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function setEditCourse($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obCourse = EntityCourse::getCourseById($id);

		//VALIDA A INSTÂNCIA
		if(!$obCourse instanceof EntityCourse){
			$request->getRouter()->redirect('/admin/courses');
		}

		//POST VARS
		$postVars = $request->getPostVars();

		//ATUALIZA A INSTÂNCIA
		$obCourse->nome         = $postVars['nome'] ?? $obCourse->nome;
		$obCourse->descricao    = $postVars['descricao'] ?? $obCourse->descricao;
		$obCourse->atualizar();

		//REDIRECIONA O USUÁRIO
		$request->getRouter()->redirect('/admin/courses/'.$obCourse->id.'/edit?status=updated');
	}

	/**
	 * Método responsável por retornar o formulário de exclusão de um curso
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function getDeleteCourse($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obCourse = EntityCourse::getCourseById($id);

		//VALIDA A INSTÂNCIA
		if(!$obCourse instanceof EntityCourse){
			$request->getRouter()->redirect('/admin/courses');
		}

		//CONTEÚDO DO FORMULÁRIO
		$content = View::render('admin/modules/courses/delete',[
			'nome'      => $obCourse->nome,
			'descricao'  => $obCourse->descricao
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Excluir curso > Admin',$content, 'courses');
	}

	/**
	 * Método responsável excluir um curso
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function setDeleteCourse($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obCourse = EntityCourse::getCourseById($id);

		//VALIDA A INSTÂNCIA
		if(!$obCourse instanceof EntityCourse){
			$request->getRouter()->redirect('/admin/courses');
		}

		//EXCLUI O CURSO
		$obCourse->excluir();

		//REDIRECIONA O USUÁRIO
		$request->getRouter()->redirect('/admin/courses?status=deleted');
	}
}