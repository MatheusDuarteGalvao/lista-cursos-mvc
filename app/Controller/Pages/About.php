<?php

namespace App\Controller\Pages;

use App\Model\Entity\Organization;
use \App\Utils\View;

class About extends Page{

    /**
     * Método responsável por retornar o conteúdo (view) da nossa página de sobre
     * @return string
     */
    public static function getAbout(){
        //VIEW DA PÁGINA SOBRE
        $content = View::render('pages/about',[
            'name'          => 'Matheus Duarte',
            'description'   => 'Espaço para cadastro de cursos',
            'site'          => 'https://github.com/MatheusDuarteGalvao'
        ]);

        //RETORNA A VIEW DA PÁGINA
        return parent::getPage('Matheus Duarte - Home', $content);
    }
}