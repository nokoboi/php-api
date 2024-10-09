<?php

class ValidatorException extends Exception {
    protected $errors;

    public function __construct($errors) {
        $this->errors = $errors;
        parent::__construct("Erro de validacion");
    }

    public function getErrors() {
        return $this->errors;
    }
}