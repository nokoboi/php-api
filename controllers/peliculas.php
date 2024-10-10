<?php
require_once '../data/Pelicula.php';
require_once './utilidades.php';

header('Content-Type: application/json');
$pelicula = new Pelicula();

$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$parametros = Utilidades::parseUriParameters($uri);
$id = Utilidades::getParameterValue($parametros, 'id');

switch ($method) {
    case 'GET':
        if ($id) {
            $respuesta = getMovieByID($pelicula, $id);
        } else {
            $respuesta = getAllMovies($pelicula);
        }
        echo json_encode($respuesta);
        break;
    case 'POST':
        setMovie($pelicula);
        break;
    case 'PUT':
        if ($id) {
            updateMovie($pelicula, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error'=> 'Id no proporcionado']);
        }
        break;
    case 'DELETE':
        if ($id) {
            deleteMovie($pelicula, $id);
        } else {
            http_response_code(400);
            echo json_encode(['error'=> 'Id no proporcionado']);
        }
        break;

}

function getAllMovies($pelicula)
{
    return $pelicula->getMovies($pelicula);
}

function getMovieByID($pelicula, $id)
{
    return $pelicula->getMovieID($id);
}

function setMovie($pelicula)
{
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data["titulo"]) && isset($data["precio"]) && isset($data["id_director"])) {
        $id = $pelicula->createMovie($data["titulo"], $data["precio"], $data["id_director"]);
        echo json_encode($id);
    }else{
        echo json_encode(["error"=> "faltan datos"]);
    }

}

function updateMovie($pelicula,$id){
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data["titulo"]) && isset($data["precio"]) && isset($data["id_director"])) {
        $affected = $pelicula->updateMovie($id, $data["titulo"], $data["precio"], $data["id_director"]);
        echo json_encode(["affected"=>$affected]);
    }else{
        echo json_encode(["error"=> "datos incorrectos"]);
    }

}

function deleteMovie($pelicula,$id){
    $affected = $pelicula->deleteMovie($id);
    echo json_encode(["affected"=>$affected]);
}