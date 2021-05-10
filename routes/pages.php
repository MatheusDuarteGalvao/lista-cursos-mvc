<?php

use \App\Http\Response;
use \App\Controller\Pages;

//ROTA HOME
$obRouter->get('/',[
    function(){
        return new Response(200,Pages\Home::getHome());
    }
]);

//ROTA SOBRE
$obRouter->get('/sobre',[
    function(){
        return new Response(200,Pages\About::getAbout());
    }
]);

//ROTA DE CURSOS
$obRouter->get('/cursos',[
    function($request){
        return new Response(200,Pages\Course::getCourses($request));
    }
]);

//ROTA DE CURSOS (INSERT)
$obRouter->post('/cursos',[
    function($request){
        return new Response(200,Pages\Course::insertCourse($request));
    }
]);