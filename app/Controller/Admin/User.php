<?php

namespace App\Controller\Admin;

use \App\Utils\View;
use \App\Utils\Upload;
use \App\Model\Entity\User as EntityUser;
use \App\Database\Pagination;

class User extends Page{

	/**
	 * Método responsável por obter a renderização dos items de usuários para a página
	 * @param Request $request
	 * @param Pagination $obPagination
	 * @return string
	*/
	private static function getUserItems($request,&$obPagination){
		//CURSOS
		$items = '';

		//QUANTIDADE TOTAL DE REGISTROS
		$quantidadeTotal = EntityUser::getUsers(null, null,null,'COUNT(*) as qtd')->fetchObject()->qtd;

		//PÁGINA ATUAL
		$queryParams = $request->getQueryParams();
		$paginaAtual = $queryParams['page'] ?? 1;

		//INSTÂNCIA DE PAGINAÇÃO
		$obPagination = new Pagination($quantidadeTotal,$paginaAtual,5);

		//RESULTADOS DA PÁGINA
		$results = EntityUser::getUsers(null,'id DESC',$obPagination->getLimit());

		//RENDERIZA O ITEM
		while($obUser = $results->fetchObject(EntityUser::class)){
			$items .= View::render('admin/modules/users/item',[
				'id'   	=> $obUser->id,
				'nome' 	=> $obUser->nome,
				'email'	=> $obUser->email,
			]);
		}

		//RETORNA OS CURSOS
		return $items;
	}

	/**
	 * Método responsável por por renderizar a view de listagem de usuários
	 * @param Request $request
	 * @return string
	 */
	public static function getUsers($request){
		//CONTEÚDO DA HOME
		$content = View::render('admin/modules/users/index',[
			'items'      => self::getUserItems($request,$obPagination),
			'pagination' => parent::getPagination($request,$obPagination),
			'status'     => self::getStatus($request)
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Usuários > Admin',$content, 'users');
	}

	/**
	 * Método responsável por retornar o formulário de cadastro de um novo usuário
	 * @param Request $request
	 * @return string
	 */
	public static function getNewUser($request){
		//CONTEÚDO DO FORMULÁRIO
		$content = View::render('admin/modules/users/form',[
			'title'     => 'Cadastrar usuário',
			'nome'      => '',
			'email'     => '',
			'picture'		=> '',
			'status'    => self::getStatus($request)
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Cadastrar usuário > Admin',$content, 'users');
	}

	/**
	 * Método responsável por cadastrar um usuário no banco
	 * @param Request $request
	 * @return string
	 */
	public static function setNewUser($request){
		//POST VARS
		$postVars   = $request->getPostVars();
		$email      = $postVars['email'] ?? '';
		$nome       = $postVars['nome'] ?? '';
		$senha      = $postVars['senha'] ?? '';

		//VALIDA O E-MAIL DO USUÁRIO
		$obUser = EntityUser::getUserByEmail($email);
		if($obUser instanceof EntityUser){
			//REDIRECIONA O USUÁRIO
			$request->getRouter()->redirect('/admin/users/new?status=duplicated');
		}

		$fileVars   = $request->getFileVars();
		//SEPRANDO FOTO PARA UPLOAD
		if(isset($fileVars['foto'])){
			//INSTÂNCIA DE UPLOAD
			$obUpload = new Upload($fileVars['foto']);

			//ALTERA O NOME DO ARQUIVO
			$obUpload->generateNewName();

			//MOVE OS ARQUIVOS DE UPLOAD
			$success = $obUpload->upload(dirname(__DIR__,3).'/uploads',false);
			if($success){
				$foto = $obUpload->getBasename();
			}
		}

		//NOVA INSTÂNCIA DE CURSO
		$obUser         = new EntityUser;
		$obUser->nome   = $nome;
		$obUser->email  = $email;
		$obUser->foto		= $foto;
		$obUser->senha  = password_hash($senha,PASSWORD_DEFAULT);
		$obUser->cadastrar();

		//REDIRECIONA O USUÁRIO
		$request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=created');
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
				return Alert::getSuccess('Usuário criado com sucesso!');
				break;
			case 'updated':
				return Alert::getSuccess('Usuário atualizado com sucesso!');
				break;
			case 'deleted':
				return Alert::getSuccess('Usuário excluído com sucesso!');
				break;
			case 'duplicated':
				return Alert::getError('O e-mail digitado já está sendo utilizado por outro usuário!');
				break;
		}
	}

	/**
	 * Método responsável por retornar o formulário de edição de um usuário
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function getEditUser($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obUser = EntityUser::getUserById($id);

		//VALIDA A INSTÂNCIA
		if(!$obUser instanceof EntityUser){
				$request->getRouter()->redirect('/admin/users');
		}

		$picture = $obUser->foto ? View::render('admin/modules/users/picture',[
			'foto' => $obUser->foto,
		]) : '';

		//CONTEÚDO DO FORMULÁRIO
		$content = View::render('admin/modules/users/form',[
			'title'     => 'Editar usuário',
			'nome'      => $obUser->nome,
			'email'     => $obUser->email,
			'picture'		=> $picture,
			'status'    => self::getStatus($request)
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Editar usuário > Admin',$content, 'users');
	}

	/**
	 * Método responsável por gravar a atualização de um usuário
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function setEditUser($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obUser = EntityUser::getUserById($id);

		//VALIDA A INSTÂNCIA
		if(!$obUser instanceof EntityUser){
			$request->getRouter()->redirect('/admin/users');
		}

		//POST VARS
		$postVars = $request->getPostVars();
		$email    = $postVars['email'] ?? '';
		$nome     = $postVars['nome'] ?? '';
		$senha    = $postVars['senha'] ?? '';

			//VALIDA O E-MAIL DO USUÁRIO
		$obUserEmail = EntityUser::getUserByEmail($email);
		if($obUserEmail instanceof EntityUser && $obUserEmail->id != $id){
			//REDIRECIONA O USUÁRIO
			$request->getRouter()->redirect('/admin/users/'.$id.'/edit?status=duplicated');
		}

		$fileVars   = $request->getFileVars();
		//SEPRANDO FOTO PARA UPLOAD
		if(isset($fileVars['foto'])){
			//INSTÂNCIA DE UPLOAD
			$obUpload = new Upload($fileVars['foto']);

			//ALTERA O NOME DO ARQUIVO
			$obUpload->generateNewName();

			//MOVE OS ARQUIVOS DE UPLOAD
			$success = $obUpload->upload(dirname(__DIR__,3).'/uploads',false);
			if($success){
				$foto = $obUpload->getBasename();
			}
		}

		//ATUALIZA A INSTÂNCIA
		$obUser->nome  = $nome;
		$obUser->email = $email;
		$obUser->foto  = $foto ?? $obUser->foto;
		$obUser->senha = password_hash($senha,PASSWORD_DEFAULT);
		$obUser->atualizar();

		//REDIRECIONA O USUÁRIO
		$request->getRouter()->redirect('/admin/users/'.$obUser->id.'/edit?status=updated');
	}

	/**
	 * Método responsável por retornar o formulário de exclusão de um usuário
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function getDeleteUser($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obUser = EntityUser::getUserById($id);

		//VALIDA A INSTÂNCIA
		if(!$obUser instanceof EntityUser){
			$request->getRouter()->redirect('/admin/users');
		}

		//CONTEÚDO DO FORMULÁRIO
		$content = View::render('admin/modules/users/delete',[
			'nome'  => $obUser->nome,
			'email' => $obUser->email
		]);

		//RETORNA A PÁGINA COMPLETA
		return parent::getPanel('Excluir usuário > Admin',$content, 'users');
	}

	/**
	 * Método responsável excluir um usuário
	 * @param Request $request
	 * @param integer $id
	 * @return string
	 */
	public static function setDeleteUser($request,$id){
		//OBTÉM O CURSO DO BANCO DE DADOS
		$obUser = EntityUser::getUserById($id);

		//VALIDA A INSTÂNCIA
		if(!$obUser instanceof EntityUser){
			$request->getRouter()->redirect('/admin/users');
		}

		//EXCLUI O CURSO
		$obUser->excluir();

		//REDIRECIONA O USUÁRIO
		$request->getRouter()->redirect('/admin/users?status=deleted');
	}
}