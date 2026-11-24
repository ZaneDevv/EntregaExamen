<?php

declare(strict_types = 1);

namespace Models;

class Libro
{
    private static string $archivo = BASE_PATH . '/data/biblioteca.txt';

    public function __construct(
        public readonly string $titulo = '',
        public readonly string $autor = '',
        public readonly string $genero = '',
        public readonly int $anio = 0,
        public readonly int $paginas = 0,
        public readonly string $portada = '',
    )
    {}

    /**
     * Comprueba si el libro existe dentro del fichero biblioteca.txt
     * @return bool True si se pudo encontrar, false si no se puede leer el fichero o si no ha sido encntrado el libro
     */
    public function existe() : bool
    {
        if (!file_exists(self::$archivo))
            return false;

        if (!is_readable(self::$archivo))
            return false;

        $datos = fopen(self::$archivo, 'r');
        $libro_encontrado = false;

        while (!($libro_encontrado || feof($datos)))
        {
            $linea = fgets($datos);
            $json = json_decode($linea, true);

            $libro_encontrado = strtoupper(trim($json['titulo'])) === strtoupper(trim($this->titulo));
        }

        return $libro_encontrado;
    }

    /**
     * Guarda el libro como json en una nueva línea en el archivo biblioteca.txt
     * @return bool True si se pudo guardar correctamente, false si no
     */
    public function guardar() : bool
    {
        if (!file_exists(self::$archivo))
            return false;

        $array = [];
        $this->convertirArray($array);

        return file_put_contents(self::$archivo, json_encode($array, JSON_UNESCAPED_UNICODE)) !== false;
    }

    /**
     * Convierte la instancia libro a una array asociativa con únicamente los atributos del libro
     */
    public function convertirArray(array &$array) : void
    {
        $array = [
            'titulo' => $this->titulo,
            'autor' => $this->autor,
            'genero' => $this->genero,
            'anio' => $this->anio,
            'paginas' => $this->paginas,
            'portada' => $this->portada
        ];
    }

    /**
     * Crea una instancia de libro a partir de los datos dados en la array
     * @param array $datos Atributos el libro
     * @return Libro Instancia de libro con los datos dados
     */
    public static function crear_desde_array(array $datos) : self
    {
        return new self(
            titulo: $datos['libro'] ?? '',
            autor: $datos['autor'] ?? '',
            genero: $datos['genero'] ?? '',
            anio: $datos['anio'] ?? 0,
            paginas: $datos['paginas'] ?? 0,
            portada: $datos['portada'] ?? ''
        );
    }
}