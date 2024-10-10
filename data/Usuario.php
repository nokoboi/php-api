<?php
require_once("database.php");
require_once 'Validator.php';
require_once 'ValidatorException.php';

class Usuario
{
    private Database $db;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAll()
    {
        $result = $this->db->query('select * from usuario;');
        return $result->fetch_all(MYSQLI_ASSOC); // Devuelve el resultado en un JSON
    }

    public function getById($id)
    {
        $idSaneado = Validator::Sanitize([$id]);
        $result = $this->db->query("SELECT * from usuario where id=?", [$idSaneado[0]]);
        return $result->fetch_assoc();
    }

    public function createUser($username, $email)
    {
        // Recogemos los datos y los limpiamos
        $data = ['nombre' => $username, 'email' => $email];
        $sanitizedData = Validator::Sanitize($data);
        $errors = Validator::Validate($sanitizedData);

        // Si hay errores lanzamos una excepcion
        if (!empty($errors)) {             
            $errores = new ValidatorException($errors);
            return $errores->getErrors();
        }

        $nombreSaneado = $sanitizedData['nombre'];
        $emailSaneado = $sanitizedData['email'];

        $result = $this->db->query('SELECT id from usuario where email=?', [$emailSaneado]);
        if($result->num_rows > 0) { 
            return "El usuario ya existe";
        }

        // Hacemos la consulta a la base 
        $this->db->query("INSERT INTO usuario (nombre,email) VALUES(?,?)", [$nombreSaneado, $emailSaneado]);

        return $this->db->query("SELECT LAST_INSERT_ID() as id")->fetch_assoc()['id'];
    }

    public function updateUser($id, $nombre, $email){
        // Recogemos los datos y los limpiamos
        $data = ['id' => $id, 'nombre' => $nombre, 'email' => $email];
        $sanitizedData = Validator::Sanitize($data);
        $errors = Validator::Validate($sanitizedData);

        // Si hay errores lanzamos una excepcion
        if (!empty($errors)) {             
            $errores = new ValidatorException($errors);
            return $errores->getErrors();
        }

        $nombreSaneado = $sanitizedData['nombre'];
        $emailSaneado = $sanitizedData['email'];
        $idSaneado = $sanitizedData['id'];

        $result = $this->db->query('SELECT id from usuario where email = ? and id != ?',[$emailSaneado, $idSaneado]);
        if($result->num_rows > 0) {
            return "El email ya está en uso por otro usuario";
        }

        $this->db->query("UPDATE usuario SET nombre = ?, email = ? where id=? ", [$nombreSaneado, $emailSaneado, $idSaneado]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
    }

    public function deleteUser($id){
        $this->db->query("DELETE from usuario where id=?", [$id]);
        return $this->db->query("SELECT ROW_COUNT() as affected")->fetch_assoc()['affected'];
    }
}