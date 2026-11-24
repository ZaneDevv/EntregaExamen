<?php
declare(strict_types=1);

namespace Controllers;

use Core\Pages;
use Models\Libro;
use Requests\LibroRequest;

/** Controlador del modelo Libro. */
final class LibroController
{
    /** Muestra el formulario de alta. (YA HECHO) */
    public function create(): void
    {
        new Pages()->render('libro/formulario');
    }

    /** Procesa el alta de un libro. */
    public function store(): void
    {
        // TODO (apartado 4): crear new LibroRequest($_POST, $_FILES).
        //      Si no es válido -> render('libro/formulario') con 'errores' y 'datos', y return.
        //
        // TODO (apartado 5):
        //      1. Generar el nombre único de la imagen (todavía no se mueve).
        //      2. Crear el objeto Libro con ese nombre de portada.
        //      3. Si existe() -> render('libro/formulario') con $errores['general'], y return.
        //      4. Mover la imagen a RUTA_IMG.
        //
        // TODO (apartado 6):
        //      guardar() -> render('libro/formulario') con 'mensaje' y 'libro'.
        //      Si falla -> borrar la imagen que ya se había movido y mostrar un error.

        $pages = new Pages();
        $libro_request = new LibroRequest($_POST, $_FILES);
        
        if (!$libro_request->es_valido())
        {
            $pages->render('libro/formulario', [
                'errores' => $libro_request->errores(),
                'datos' => $libro_request->datos()
            ]);
            return;
        }

        $datos = $libro_request->datos();

        $nombre_imagen = RUTA_IMG . date('Ymd-His') . bin2hex(random_bytes(4)) . '.' . $datos['formato'];

        $libro = Libro::crear_desde_array($datos);
        if ($libro->existe())
        {            
            $pages->render('libro/formulario', [
                'errores' => ['general' => 'El libro ya está registrado'],
                'datos' => $datos
            ]);

            return;   
        }

        if (!is_dir(RUTA_IMG))
            mkdir(RUTA_IMG, 755);

        if (!move_uploaded_file($datos['portada']['tmp_name'], $nombre_imagen))
        {
            $pages->render('libro/formulario', [
                'errores' => ['general' => 'El libro no se ha podido subir correctamente. Por favor, inténtelo de nuevo'],
                'datos' => $datos
            ]);

            return;
        }

        if ($libro->guardar())
        {
            $pages->render('libro/formulario', ['mensaje' => '¡Libro registrado correctamente!']);
            $pages->render('libro/imprimeFicha', ['libro' => $libro]);
        }
    }

    /** Muestra el catálogo. */
    public function index(): void
    {
        $libros = [];

        // TODO (apartado 3): rellenar $libros leyendo RUTA_BIBLIOTECA línea por línea
        //      (un array de objetos Libro).

        if (file_exists(RUTA_BIBLIOTECA) && is_readable(RUTA_BIBLIOTECA))
        {
            $datos = fopen(RUTA_BIBLIOTECA, 'r');

            while (!feof($datos))
            {
                $json = json_decode(fgets($datos));
                array_push($json);
            }
        }

        new Pages()->render('libro/catalogo', ['libros' => $libros]);
    }
}
