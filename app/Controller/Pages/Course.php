<?php

namespace App\Controller\Pages;

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
		$obPagination = new Pagination($quantidadeTotal,$paginaAtual,3);

		//RESULTADOS DA PÁGINA
		$results = EntityCourse::getCourses(null,'id DESC',$obPagination->getLimit());

		//RENDERIZA O ITEM
		while($obCourse = $results->fetchObject(EntityCourse::class)){
			$items .= View::render('pages/course/item',[
				'nome'       => $obCourse->nome,
				'descricao'  => $obCourse->descricao,
				'data'       => date('d/m/Y H:i:s' , strtotime($obCourse->data)),
				'nota'       => $obCourse->nota
			]);
		}

		//RETORNA OS CURSOS
		return $items;
	}

	/**
	 * Método responsável por retornar o conteúdo (view) de cursos 
	 * @param Request $request
	 * @return string
	 */
	public static function getCourses($request){
		//VIEW DE CURSOS
		$content = View::render('pages/courses', [
			'items' => self::getCourseItems($request,$obPagination),
			'pagination' => parent::getPagination($request,$obPagination)
		]);

		//RETORNA A VIEW DA PÁGINA
		return parent::getPage('Cursos', $content);
	}

	/**
	 * Método responsável por cadastrar um curso
	 * @param Request $request
	 * @return string
	 */
	public static function insertCourse($request){
		//DADOS DO POST
		$postVars = $request->getPostVars();

		//NOVA INSTÂNCIA DE CURSO
		$obCourse            = new EntityCourse;
		$obCourse->nome      = $postVars['nome'];
		$obCourse->descricao = $postVars['descricao'];
		$obCourse->usuario   = $postVars['usuario'];
		$obCourse->nota      = $postVars['nota'];

		$obCourse->cadastrar();

		//RETORNA A PÁGINA DE LISTAGEM DE CURSOS
		return self::getCourses($request);
	}
}