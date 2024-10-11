<?php
require_once("database.php");
require_once 'Validator.php';
require_once 'ValidatorException.php';

class Director
{
    private Database $db;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function getDirectores()
    {
        $result = $this->db->query('SELECT * FROM director');
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getMovieId($id)
    {

        $idSaneado = Validator::Sanitize([$id]);

        $result = $this->db->query('SELECT * from director where id=?', [$idSaneado[0]]);
        return $result->fetch_assoc();
    }

    public function createMovie($nombre, $apellido, $biografia)
    {
        $data = ["nombre" => $nombre, "apellido" => $apellido, "id_director" => $biografia];
        $sanitizedData = Validator::Sanitize($data);

        $nombreSaneado = $sanitizedData['nombre'];
        $apellidoSaneado = $sanitizedData['apellido'];
        $biografiaSaneado = $sanitizedData['biografia'];


        $this->db->query('INSERT into director (nombre,apellido,biografia) values(?,?,?)', [$nombreSaneado, $apellidoSaneado, $biografiaSaneado]);
        return $this->db->query('SELECT LAST_INSERT_ID() as id')->fetch_assoc()['id'];

    }
}