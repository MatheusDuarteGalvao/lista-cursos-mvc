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
}