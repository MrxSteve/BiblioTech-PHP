<?php
require_once 'Libro.php';
require_once 'Prestamo.php';

class Biblioteca {
    private $libros;
    private $prestamos;
    
    public function __construct() {
        $this->libros = array();
        $this->prestamos = array();
    }
    
    public function getLibros() {
        return $this->libros;
    }
    
    public function getPrestamos() {
        return $this->prestamos;
    }
    
    public function agregarLibro($libro) {
        if (!($libro instanceof Libro)) {
            throw new Exception("Debe ser un objeto Libro válido");
        }
        $this->libros[] = $libro;
    }
    
    public function buscarLibro($id) {
        foreach ($this->libros as $libro) {
            if ($libro->getId() == $id) {
                return $libro;
            }
        }
        return null;
    }
    
    public function editarLibro($id, $titulo, $autor, $categoria) {
        $libro = $this->buscarLibro($id);
        if ($libro) {
            $libro->setTitulo($titulo);
            $libro->setAutor($autor);
            $libro->setCategoria($categoria);
            return true;
        }
        return false;
    }
    
    public function eliminarLibro($id) {
        foreach ($this->libros as $index => $libro) {
            if ($libro->getId() == $id) {
                if (!$libro->isDisponible()) {
                    return false; // No se puede eliminar un libro prestado
                }
                unset($this->libros[$index]);
                $this->libros = array_values($this->libros); // Reindexar
                return true;
            }
        }
        return false;
    }
    
    public function buscarLibros($termino) {
        $resultados = array();
        foreach ($this->libros as $libro) {
            if ($libro->buscar($termino)) {
                $resultados[] = $libro;
            }
        }
        return $resultados;
    }
    
    public function crearPrestamo($libroId, $usuario) {
        $libro = $this->buscarLibro($libroId);
        if ($libro && $libro->isDisponible()) {
            $prestamo = new Prestamo($libro, $usuario);
            $this->prestamos[] = $prestamo;
            return $prestamo;
        }
        return false;
    }
    
    public function devolverLibro($libroId) {
        foreach ($this->prestamos as $index => $prestamo) {
            if ($prestamo->getLibro()->getId() == $libroId) {
                $prestamo->devolver();
                unset($this->prestamos[$index]);
                $this->prestamos = array_values($this->prestamos); // Reindexar
                return true;
            }
        }
        return false;
    }
}
