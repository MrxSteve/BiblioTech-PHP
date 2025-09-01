<?php
require_once 'Autor.php';

class Libro {
    private $id;
    private $titulo;
    private $autor;
    private $categoria;
    private $disponible;
    private static $contadorId = 1;
    
    public function __construct($titulo, $autor, $categoria = 'General') {
        $this->setTitulo($titulo);
        $this->setAutor($autor);
        $this->setCategoria($categoria);
        $this->id = self::$contadorId++;
        $this->disponible = true;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getTitulo() {
        return $this->titulo;
    }
    
    public function getAutor() {
        return $this->autor;
    }
    
    public function getCategoria() {
        return $this->categoria;
    }
    
    public function isDisponible() {
        return $this->disponible;
    }
    
    public function setTitulo($titulo) {
        if (empty(trim($titulo))) {
            throw new Exception("El título del libro no puede estar vacío");
        }
        $this->titulo = trim($titulo);
    }
    
    public function setAutor($autor) {
        if (!($autor instanceof Autor)) {
            throw new Exception("Debe ser un objeto Autor válido");
        }
        $this->autor = $autor;
    }
    
    public function setCategoria($categoria) {
        $this->categoria = empty(trim($categoria)) ? 'General' : trim($categoria);
    }
    
    public function prestar() {
        if (!$this->disponible) {
            return false;
        }
        $this->disponible = false;
        return true;
    }
    
    public function devolver() {
        $this->disponible = true;
    }
    
    public function buscar($termino) {
        $termino = strtolower(trim($termino));
        return strpos(strtolower($this->titulo), $termino) !== false ||
               strpos(strtolower($this->autor->getNombre()), $termino) !== false ||
               strpos(strtolower($this->categoria), $termino) !== false;
    }

    public function __toString() {
        return $this->titulo . " - " . $this->autor . " (" . $this->categoria . ")";
    }
}