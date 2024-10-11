<?php
require_once '../data/Director.php';
require_once './utilidades.php';

header('Content-Type: application/json');
$director = new Director();

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$parametros = Utilidades::parseUriParameters($uri);
$id = Utilidades::getParameterValue($parametros, 'id');

switch ($method) {
    case 'GET':
        if ($id) {
            $respuesta = getMovieByID($director, $id);
        } else {
            $respuesta = getAllMovies($director);
        }
        echo json_encode($respuesta);
        break;
    case 'POST':
        setMovie($director);
        break;
    case 'PUT':
        if ($id) {
            updateDirector($director, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error'=> 'Id no proporcionado']);
        }
        break;
    case 'DELETE':
        if ($id) {
            deleteDirector($director, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error'=> 'Id no proporcionado']);
        }
        break;
}

function setDirector($director)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data["nombre"]) && isset($data["apellido"]) && isset($data["biografia"])) {
        $id = $director->createDirector($data["nombre"], $data["apellido"], $data["biografia"]);
        echo json_encode($id);
    }else{
        echo json_encode(["error"=> "faltan datos"]);
    }

}

function updateDirector($director,$id){
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data["nombre"]) && isset($data["apellido"]) && isset($data["biografia"])) {
        $affected = $director->updateDirector($id, $data["nombre"], $data["apellido"], $data["biografia"]);
        echo json_encode(["affected"=>$affected]);
    }else{
        echo json_encode(["error"=> "datos incorrectos"]);
    }

}

function deleteDirector($director,$id){
    $affected = $director->deleteDirector($id);
    echo json_encode(["affected"=>$affected]);
}