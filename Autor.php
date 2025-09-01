<?php
class Autor {
    private $nombre;
    private $nacionalidad;
    
    public function __construct($nombre, $nacionalidad) {
        $this->setNombre($nombre);
        $this->setNacionalidad($nacionalidad);
    }
    
    public function getNombre() {
        return $this->nombre;
    }
    
    public function getNacionalidad() {
        return $this->nacionalidad;
    }
    
    public function setNombre($nombre) {
        if (empty(trim($nombre))) {
            throw new Exception("El nombre del autor no puede estar vacío");
        }
        $this->nombre = trim($nombre);
    }
    
    public function setNacionalidad($nacionalidad) {
        if (empty(trim($nacionalidad))) {
            throw new Exception("La nacionalidad no puede estar vacía");
        }
        $this->nacionalidad = trim($nacionalidad);
    }
    
    public function __toString() {
        return $this->nombre . " (" . $this->nacionalidad . ")";
    }
}