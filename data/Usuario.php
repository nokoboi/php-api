<?php
require_once("database.php");

class Usuario{
    private Database $db;
    public function __construct() {
        $this->db = new Database();
    }

    public function getAll(){
        $result = $this->db->query('select * from usuario;');
        return $result->fetch_all(MYSQLI_ASSOC); // Devuelve el resultado en un JSON
    }

    public function getById($id){
        $result = $this->db->query("SELECT * from usuario where id=?",[$id]);
        return $result->fetch_assoc();
    }
}

$usuarios = new Usuario();
$usu = $usuarios->getAll();

// var_dump($usu);