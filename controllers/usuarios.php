<?php

require_once '../data/Usuario.php';
require_once './utilidades.php';

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

// Obtener la URI de la petición
$uri = $_SERVER['REQUEST_URI'];

//obtener los parámetros de la petición
$parametros = Utilidades::parseUriParameters($uri);

//obtener el parámetro id
$id = Utilidades::getParameterValue($parametros, 'id');

switch ($method) {
    case 'GET':
        if ($id) {
            $respuesta = getUsuarioById($usuario, $id);
        } else {
            $respuesta = getAllUsuarios($usuario);
        }
        echo json_encode($respuesta);
        break;
    case 'POST':
        setUser($usuario);
        break;
    case 'PUT':
        if ($id) {
            updateUser($usuario, $id);
        } else {
            http_response_code(400);
            echo json_encode(['Error ' => 'ID no proporcionado']);
        }
        break;
    case 'DELETE':
        if ($id) {
            deleteUser($usuario, $id);
        } else {
            http_response_code(400);
            echo json_encode(['Error ' => 'ID no proporcionado']);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error ' => 'Metodo no permitido']);
}

function getUsuarioById($usuario, $id)
{
    return $usuario->getById($id);
}

function getAllUsuarios($usuario)
{
    return $usuario->getAll();
}

function setUser($usuario)
{
    $data = json_decode(file_get_contents('php://input'), true);

    // Comprobamos si el campo 'nombre' y 'email' del json existen, si no, no hacemos nada
    if (isset($data['nombre']) && isset($data['email'])) {
        $id = $usuario->createUser($data['nombre'], $data['email']);
        echo json_encode(['id' => $id]);
    } else {
        echo json_encode(['Error' => 'Faltan datos']);
    }

}

function updateUser($usuario, $id)
{
    // Lee los datos del cuerpo de la solicitud y los pasa a formato json
    $data = json_decode(file_get_contents('php://input'), true);

    // Comprobamos si el campo 'nombre' y 'email' del json existen, si no, no hacemos nada
    if (isset($data['nombre']) && isset($data['email'])) {
        $affected = $usuario->updateUser($id, $data['nombre'], $data['email']);
        echo json_encode(['affected' => $affected]);
    } else {
        echo json_encode(['Error' => 'datos incorrectos']);
    }
}

function deleteUser($usuario, $id)
{
    $affected = $usuario->deleteUser($id);
    echo json_encode(['affected' => $affected]);
}
