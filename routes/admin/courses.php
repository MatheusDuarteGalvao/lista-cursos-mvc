<?php

use \App\Http\Response;
use \App\Controller\Admin;

//ROTA LISTAGEM DE DEPOIMENTOS
$obRouter->get('/admin/courses',[
    'middlewares' => [
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Course::getCourses($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO DEPOIMENTO
$obRouter->get('/admin/courses/new',[
    'middlewares' => [
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Course::getNewCourse($request));
    }
]);

//ROTA DE CADASTRO DE UM NOVO DEPOIMENTO (POST)
$obRouter->post('/admin/courses/new',[
    'middlewares' => [
        'require-admin-login'
    ],
    function($request){
        return new Response(200,Admin\Course::setNewCourse($request));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTO
$obRouter->get('/admin/courses/{id}/edit',[
    'middlewares' => [
        'require-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Course::getEditCourse($request,$id));
    }
]);

//ROTA DE EDIÇÃO DE UM DEPOIMENTO (POST)
$obRouter->post('/admin/courses/{id}/edit',[
    'middlewares' => [
        'require-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Course::setEditCourse($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTO
$obRouter->get('/admin/courses/{id}/delete',[
    'middlewares' => [
        'require-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Course::getDeleteCourse($request,$id));
    }
]);

//ROTA DE EXCLUSÃO DE UM DEPOIMENTO (POST)
$obRouter->post('/admin/courses/{id}/delete',[
    'middlewares' => [
        'require-admin-login'
    ],
    function($request,$id){
        return new Response(200,Admin\Course::setDeleteCourse($request,$id));
    }
]);
