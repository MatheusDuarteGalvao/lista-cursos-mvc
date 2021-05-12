<?php

namespace App\Controller\Pages;

use App\Model\Entity\Organization;
use \App\Utils\View;

class Home extends Page{

	/**
	 * Método responsável por retornar o conteúdo (view) da nossa home
	 * @return string
	 */
	public static function getHome(){
		//VIEW DA HOME
		$content = View::render('pages/home', [
			'name' => 'Recomendação de cursos',
			'text' => 'Este é um espaço para cadastrar e conhecer novos cursos que possam agregar conhecimento'
		]);

		//RETORNA A VIEW DA PÁGINA
		return parent::getPage('HOME > Lista de recomendações de cursos', $content);
	}
}