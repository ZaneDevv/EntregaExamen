<?php
// TODO (apartado 2): vista de la ficha de un libro.
// Recibe la variable $libro (objeto Libro). Solo HTML y PHP para pintar.
?>

<?php if(isset($libro)): ?>
    <article class="ficha">
        <img src="<?= $libro->portada ?>" alt="<?= e($libro->titulo) ?>">
        <h3><?= e($libro->titulo) ?></h3>
        <p class="autor">Por: <?= e($libro->autor) ?></p>
        <span class="genero"><?= e($libro->genero) ?></span>
    </article>
<?php endif; ?>