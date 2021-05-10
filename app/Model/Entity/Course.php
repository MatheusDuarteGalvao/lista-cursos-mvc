<?php

namespace App\Model\Entity;

use \App\Database\Database;

class Course{

    /**
     * ID do curso
     * @var integer
     */
    public $id;

    /**
     * Nome do curso
     * @var string
     */
    public $nome;

    /**
     * Descrição do curso
     * @var string
     */
    public $descricao;

     /**
     * Data de publicação do post
     * @var string
     */
    public $data;

    /**
     * Nota do curso
     * @var int
     */
    public $nota;

    /**
     * Nome do usuário que fez o curso
     * @var string
     */
    public $usuario;

    /**
     * Método responsável por cadastrar a instância atual no banco de dados
     * @return boolean
    */
    public function cadastrar(){
        //DEFINE A DATA
        $this->data = date('Y-m-d H:i:s');

        //INSERE O DEPOIMENTO NO BANCO DE DADOS
        $this->id = (new Database('cursos'))->insert([
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'data' => $this->data,
            'nota' => $this->nota,
            'usuario' => $this->usuario
        ]);

        //SUCESSO
        return true;
    }

    /**
     * Método responsável retornar Depoimentos
     * @param string $where
     * @param string $order
     * @param string $limit
     * @param string $field
     * @return PDOStatement
    */
    public static function getCourses($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('cursos'))->select($where,$order,$limit,$fields);
    }
}