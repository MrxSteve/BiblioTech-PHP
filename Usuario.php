<?php

class Usuario {
    private $nombre;
    private $correo;
    
    public function __construct($nombre, $correo) {
        $this->setNombre($nombre);
        $this->setCorreo($correo);
    }
    
    public function getNombre() {
        return $this->nombre;
    }
    
    public function getCorreo() {
        return $this->correo;
    }
    
    public function setNombre($nombre) {
        if (empty(trim($nombre))) {
            throw new Exception("El nombre del usuario no puede estar vacío");
        }
        $this->nombre = trim($nombre);
    }
    
    public function setCorreo($correo) {
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El correo electrónico no es válido");
        }
        $this->correo = strtolower(trim($correo));
    }
    
    public function __toString() {
        return $this->nombre . " (" . $this->correo . ")";
    }
}