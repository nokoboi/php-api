<?php

require_once '../data/Usuario.php';

/**
 * Establecemos el encabezado. El archivo este va a mandar una respuesta al cliente, aqui decimos que tipo de respuesta es.
 * La respuesta va a ser un objeto JSON.
 * 
 */

 header('Content-Type: application/json');
 $usuario = new Usuario();

 /**
  * La variable superglobal $_SERVER['REQUEST_METHOD']
  * REQUEST_METHOD:
  * -   POST    Para enviar datos al servidor
  * -   GET     Para solicitar datos al servidor
  * -   PUT     Para actualizar datos existentes
  * -   DELETE  Para eliminar datos
  */

  $method = $_SERVER['REQUEST_METHOD'];
//   echo($method);

 /**
  * $_SERVER['PATH_INFO]
  * Contiene información sobre la ruta de la solicitud actual
  * Ejemplo: si la url es https://ejemplo.com/script.php/usuarios/123 
  * Seria: $_SERVER['PATH_INFO] /usuarios/123
  * $_SERVER['SCRIPT_NAME'] /script.php
  */
//   $request = [];
//   if(isset($_SERVER['PATH_INFO'])){
//     $request = explode('/', trim($_SERVER['PATH_INFO']) ,'/');
//   }

// var_dump($usuario->getAll());

$request = explode('=', $_SERVER['REQUEST_URI'])[1];

// Obtener el id de la solicitud si está presente
$id = isset($request[0]) && is_numeric($request[0]) ? intval($request[0]) : null;

switch ($method) {
    case 'GET':
        if($id){
            $respuesta = getUsuarioById($usuario, $id);
        }else{
            $respuesta = getAllUsuarios($usuario);
        }
        echo json_encode($respuesta);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error ' => 'Metodo no permitido']);
}

function getUsuarioById($usuario, $id){
    return $usuario->getById($id);
}

function getAllUsuarios($usuario){
    return $usuario->getAll();
}
