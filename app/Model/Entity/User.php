<?php

namespace App\Model\Entity;

use \App\Database\Database;

class User{

	/**
	 * ID do usuário
	 * @var integer
	 */
	public $id;

	/**
	 * Nome do usuário
	 * @var string
	 */
	public $nome;

	/**
	 * E-mail do usuário
	 * @var string
	 */
	public $email;

	/**
	 * Foto de perfil do usuário
	 * @var string
	 */
	public $foto;

	/**
	 * Senha do usuário
	 * @var string
	 */
	public $senha;

	/**
	 * Método responsável por retornar o usuário com base em seu e-mail
	 * @param string email
	 * @return User
	*/
	public static function getUserByEmail($email){
		return (new Database('usuarios'))->select('email = "'.$email.'"')->fetchObject(self::class);
	}

	/**
	 * Método responsável por cadastrar a instância atual no banco de dados
	 * @return boolean
	*/
	public function cadastrar(){
		//INSERE O USUÁRIO NO BANCO DE DADOS
		$this->id = (new Database('usuarios'))->insert([
			'nome'  => $this->nome,
			'email' => $this->email,
			'senha' => $this->senha,
			'foto'	=> $this->foto
		]);

		//SUCESSO
		return true;
	}

	/**
	 * Método responsável por atualizar os dados do banco com a instância atual
	 * @return boolean
	*/
	public function atualizar(){
		//ATUALIZA O USUÁRIO NO BANCO DE DADOS
		return (new Database('usuarios'))->update('id = '.$this->id,[
			'nome'   => $this->nome,
			'email'  => $this->email,
			'senha'  => $this->senha,
			'foto'	 => $this->foto,
		]);

		//SUCESSO
		return true;
	}

	/**
	 * Método responsável por excluir um usuário do banco de dados
	 * @return boolean
	*/
	public function excluir(){
		//EXCLUI O USUÁRIO NO BANCO DE DADOS
		return (new Database('usuarios'))->delete('id = '.$this->id);
	}

	/**
	 * Método responsável por retornar usuários
	 * @param string $where
	 * @param string $order
	 * @param string $limit
	 * @param string $field
	 * @return PDOStatement
	*/
	public static function getUsers($where = null, $order = null, $limit = null, $fields = '*'){
		return (new Database('usuarios'))->select($where,$order,$limit,$fields);
	}

	/**
	 * Método responsável por retornar um usuário com base no seu ID
	 * @param integer $id
	 * @return User
	 */
	public static function getUserById($id){
		return self::getUsers('id = '.$id)->fetchObject(self::class);
	}
}