<h1>Catálogo</h1>

<?php
// TODO (apartado 3): recibe la variable $libros (array de objetos Libro).
// Recórrela y pinta cada ficha reutilizando imprimeFicha.php.
// Si no hay libros, muestra «Todavía no hay libros en la biblioteca».
    $hay_libros = isset($libros) && !empty($libros);
    $pages = new Core\Pages();

    if ($hay_libros)
    {
        foreach($libros as $libro)
        {
            $pages->render('libro/imprimeFicha', ['libro' => $libro]);
        }
    }
?>

<?php if(!$hay_libros): ?>
    <p>Todav&iacute;a no hay libros en la biblioteca</p>
<?php endif; ?>