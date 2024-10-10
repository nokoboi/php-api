<?php
require_once("database.php");
require_once 'Validator.php';
require_once 'ValidatorException.php';

class Pelicula
{
    private Database $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getMovies()
    {
        $result = $this->db->query('SELECT * FROM pelicula');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMovieId($id)
    {

        $idSaneado = Validator::Sanitize([$id]);

        $result = $this->db->query('SELECT * from pelicula where id=?', [$idSaneado[0]]);
        return $result->fetch_assoc();
    }

    public function createMovie($titulo, $precio, $idDirector)
    {
        $data = ["titulo" => $titulo, "precio" => $precio, "id_director" => $idDirector];
        $sanitizedData = Validator::Sanitize($data);
        $errors = Validator::ValidateMovie($sanitizedData);

        if (!empty($errors)) {
            $errores = new ValidatorException($errors);
            return $errores->getErrors();
        }

        $tituloSaneado = $sanitizedData['titulo'];
        $precioSaneado = $sanitizedData['precio'];
        $idSaneado = $sanitizedData['id_director'];

        $existeDirector = $this->db->query("SELECT id from director where id=?", [$idSaneado]);
        if ($existeDirector->num_rows > 0) {
            $this->db->query('INSERT into pelicula (titulo,precio,id_director) values(?,?,?)', [$tituloSaneado, $precioSaneado, $idSaneado]);
            return $this->db->query('SELECT LAST_INSERT_ID() as id')->fetch_assoc()['id'];
        }else{
            return ['error'=> 'el director no existe'];
        }
    }

    public function updateMovie($id, $titulo, $precio, $idDirector){
        $data = ["id"=> $id, "titulo" => $titulo, "precio" => $precio, "id_director" => $idDirector];
        $sanitizedData = Validator::Sanitize($data);
        $errors = Validator::ValidateMovie($sanitizedData);

        if (!empty($errors)) {
            $errores = new ValidatorException($errors);
            return $errores->getErrors();
        }

        $tituloSaneado = $sanitizedData['titulo'];
        $precioSaneado = $sanitizedData['precio'];
        $idPeliSaneado = $sanitizedData['id'];
        $idSaneado = $sanitizedData['id_director'];

        $existeDirector = $this->db->query("SELECT id from director where id=?", [$idSaneado]);
        if ($existeDirector->num_rows > 0) {
            $this->db->query("UPDATE pelicula SET titulo = ?, precio = ?, id_director=?
             where id=? ", [$tituloSaneado, $precioSaneado, $idSaneado,$idPeliSaneado]);
            return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
        }else{
            return ['error'=> 'el director no existe'];
        }
    }

    public function deleteMovie($id){
        $idSaneado = Validator::Sanitize([$id]);

        $result = $this->db->query('DELETE from pelicula where id=?', [$idSaneado[0]]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
    }
}