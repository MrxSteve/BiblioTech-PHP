<?php
require_once "Autor.php";
require_once "Libro.php";
require_once "Usuario.php";
require_once "Prestamo.php";
require_once "Biblioteca.php";

session_start();
if (!isset($_SESSION['biblioteca'])) {
    $_SESSION['biblioteca'] = new Biblioteca();
}
$biblioteca = $_SESSION['biblioteca'];

$message = '';
$pagina = isset($_GET['p']) ? $_GET['p'] : 'libros';

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $accion = $_POST['accion'];
        
        if ($accion === 'agregar') {
            $autor = new Autor($_POST["autor_nombre"], $_POST["autor_nacionalidad"]);
            $libro = new Libro($_POST["libro_titulo"], $autor, $_POST["libro_categoria"]);
            $biblioteca->agregarLibro($libro);
            $message = "✅ Libro agregado";
            
        } elseif ($accion === 'editar') {
            $autor = new Autor($_POST["autor_nombre"], $_POST["autor_nacionalidad"]);
            $biblioteca->editarLibro($_POST["libro_id"], $_POST["libro_titulo"], $autor, $_POST["libro_categoria"]);
            $message = "✅ Libro editado";
            
        } elseif ($accion === 'prestamo') {
            $usuario = new Usuario($_POST["usuario_nombre"], $_POST["usuario_email"]);
            $biblioteca->crearPrestamo($_POST["libro_id"], $usuario);
            $message = "✅ Préstamo creado";
        }
    } catch (Exception $e) {
        $message = "❌ " . $e->getMessage();
    }
}

if (isset($_GET['eliminar'])) {
    $biblioteca->eliminarLibro($_GET['eliminar']);
    $message = "✅ Libro eliminado";
}

if (isset($_GET['devolver'])) {
    $biblioteca->devolverLibro($_GET['devolver']);
    $message = "✅ Libro devuelto";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioTech</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="main-container">
        <header class="header">
            <h1>📚 BiblioTech</h1>
        </header>

        <nav style="text-align: center; margin: 20px;">
            <a href="?p=libros" class="btn btn-primary">📖 Libros</a>
            <a href="?p=agregar" class="btn btn-primary">➕ Agregar</a>
            <a href="?p=buscar" class="btn btn-primary">🔍 Buscar</a>
            <a href="?p=prestamos" class="btn btn-primary">📋 Préstamos</a>
        </nav>

        <main class="content-container">
            <?php if ($message): ?>
                <div class="alert alert-success"><?= $message ?></div>
            <?php endif; ?>

            <div class="form-section">
                <?php if ($pagina === 'agregar'): ?>
                    <h3>➕ Agregar Libro</h3>
                    <form method="post">
                        <input type="hidden" name="accion" value="agregar">
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="libro_titulo" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Autor</label>
                            <input type="text" name="autor_nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nacionalidad</label>
                            <input type="text" name="autor_nacionalidad" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Categoría</label>
                            <select name="libro_categoria" class="form-control">
                                <option>General</option>
                                <option>Literatura</option>
                                <option>Ciencia</option>
                                <option>Historia</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </form>

                <?php elseif ($pagina === 'editar' && isset($_GET['id'])): ?>
                    <?php $libro = $biblioteca->buscarLibro($_GET['id']); ?>
                    <h3>✏️ Editar Libro</h3>
                    <form method="post">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="libro_id" value="<?= $libro->getId() ?>">
                        <div class="form-group">
                            <label>Título</label>
                            <input type="text" name="libro_titulo" class="form-control" value="<?= $libro->getTitulo() ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Autor</label>
                            <input type="text" name="autor_nombre" class="form-control" value="<?= $libro->getAutor()->getNombre() ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nacionalidad</label>
                            <input type="text" name="autor_nacionalidad" class="form-control" value="<?= $libro->getAutor()->getNacionalidad() ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Categoría</label>
                            <select name="libro_categoria" class="form-control">
                                <option <?= $libro->getCategoria() === 'General' ? 'selected' : '' ?>>General</option>
                                <option <?= $libro->getCategoria() === 'Literatura' ? 'selected' : '' ?>>Literatura</option>
                                <option <?= $libro->getCategoria() === 'Ciencia' ? 'selected' : '' ?>>Ciencia</option>
                                <option <?= $libro->getCategoria() === 'Historia' ? 'selected' : '' ?>>Historia</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>

                <?php elseif ($pagina === 'prestamo' && isset($_GET['id'])): ?>
                    <?php $libro = $biblioteca->buscarLibro($_GET['id']); ?>
                    <h3>📚 Prestar: <?= $libro->getTitulo() ?></h3>
                    <form method="post">
                        <input type="hidden" name="accion" value="prestamo">
                        <input type="hidden" name="libro_id" value="<?= $libro->getId() ?>">
                        <div class="form-group">
                            <label>Nombre del usuario</label>
                            <input type="text" name="usuario_nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="usuario_email" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Crear Préstamo</button>
                    </form>

                <?php elseif ($pagina === 'buscar'): ?>
                    <h3>🔍 Buscar Libros</h3>
                    <form method="get">
                        <input type="hidden" name="p" value="buscar">
                        <div class="form-group">
                            <input type="text" name="q" class="form-control" placeholder="Buscar..." 
                                value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>
                    
                    <?php if (isset($_GET['q'])): ?>
                        <?php foreach ($biblioteca->buscarLibros($_GET['q']) as $libro): ?>
                            <div class="card">
                                <h4><?= $libro->getTitulo() ?></h4>
                                <p><?= $libro->getAutor() ?> - <?= $libro->getCategoria() ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                <?php elseif ($pagina === 'prestamos'): ?>
                    <h3>📋 Préstamos Activos</h3>
                    <?php foreach ($biblioteca->getPrestamos() as $prestamo): ?>
                        <div class="card">
                            <h4><?= $prestamo->getLibro()->getTitulo() ?></h4>
                            <p><strong>Usuario:</strong> <?= $prestamo->getUsuario()->getNombre() ?></p>
                            <p><strong>Email:</strong> <?= $prestamo->getUsuario()->getCorreo() ?></p>
                            <p><strong>Fecha:</strong> <?= $prestamo->getFechaPrestamo() ?></p>
                            <a href="?devolver=<?= $prestamo->getLibro()->getId() ?>" class="btn btn-success">Devolver</a>
                        </div>
                    <?php endforeach; ?>

                <?php else: ?>
                    <h3>📚 Catálogo de Libros</h3>
                    <?php if (empty($biblioteca->getLibros())): ?>
                        <p>No hay libros. <a href="?p=agregar">Agregar el primero</a></p>
                    <?php else: ?>
                        <?php foreach ($biblioteca->getLibros() as $libro): ?>
                            <div class="card">
                                <h4><?= $libro->getTitulo() ?></h4>
                                <p><strong>Autor:</strong> <?= $libro->getAutor() ?></p>
                                <p><strong>Categoría:</strong> <?= $libro->getCategoria() ?></p>
                                <p><strong>Estado:</strong> 
                                    <?php if ($libro->isDisponible()): ?>
                                        <span class="badge badge-available">Disponible</span>
                                    <?php else: ?>
                                        <span class="badge badge-borrowed">Prestado</span>
                                    <?php endif; ?>
                                </p>
                                <div style="margin-top: 10px;">
                                    <a href="?p=editar&id=<?= $libro->getId() ?>" class="btn btn-warning">Editar</a>
                                    <?php if ($libro->isDisponible()): ?>
                                        <a href="?p=prestamo&id=<?= $libro->getId() ?>" class="btn btn-info">Prestar</a>
                                        <a href="?eliminar=<?= $libro->getId() ?>" class="btn btn-danger" 
                                            onclick="return confirm('¿Eliminar?')">Eliminar</a>
                                    <?php else: ?>
                                        <a href="?devolver=<?= $libro->getId() ?>" class="btn btn-success">Devolver</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>