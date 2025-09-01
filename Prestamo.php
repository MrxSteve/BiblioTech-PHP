<?php
require_once 'Libro.php';
require_once 'Usuario.php';

class Prestamo {
    private $libro;
    private $usuario;
    private $fechaPrestamo;
    private $fechaDevolucion;
    
    public function __construct($libro, $usuario) {
        $this->setLibro($libro);
        $this->setUsuario($usuario);
        $this->fechaPrestamo = date("Y-m-d");
        $this->fechaDevolucion = null;
    }
    
    public function getLibro() {
        return $this->libro;
    }
    
    public function getUsuario() {
        return $this->usuario;
    }
    
    public function getFechaPrestamo() {
        return $this->fechaPrestamo;
    }
    
    public function getFechaDevolucion() {
        return $this->fechaDevolucion;
    }
    
    public function setLibro($libro) {
        if (!($libro instanceof Libro)) {
            throw new Exception("Debe ser un objeto Libro válido");
        }
        if (!$libro->isDisponible()) {
            throw new Exception("El libro no está disponible para préstamo");
        }
        $this->libro = $libro;
        $this->libro->prestar();
    }
    
    public function setUsuario($usuario) {
        if (!($usuario instanceof Usuario)) {
            throw new Exception("Debe ser un objeto Usuario válido");
        }
        $this->usuario = $usuario;
    }
    
    public function devolver() {
        $this->libro->devolver();
        $this->fechaDevolucion = date("Y-m-d");
    }
    
    public function __toString() {
        return "Préstamo: " . $this->libro->getTitulo() . " - " . $this->usuario->getNombre() . " (Fecha: " . $this->fechaPrestamo . ")";
    }
}