<?php

declare(strict_types = 1);

namespace Requests;

use \finfo;

class LibroRequest
{
    private static int $BYTES_MAXIMOS = 500 * 1024;    
    private static array $MIMOS_ACEPTADOS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];

    private array $datos = [];
    private array $errores = [];

    public function __construct(array $datos, array $archivos)
    {
        $this->sanear($datos, $archivos);
        $this->validar();
    }

    /**
     * Convierte un valor arbitrario dado en un string
     * @param mixed $valor Valor a convertir a string
     * @return string El valor dado convertido a string. Si no es un string, devuelve un string vacío
     */
    private static function convertir_a_string(mixed $valor) : string
    {
        return is_string($valor) ? $valor : '';
    }

    /**
     * Sanea los datos pasados y los guarda internamente
     * @param array $datos Datos que se han pasado a sanear
     * @param array $archivos Archivos subidos como la portada (solo se toma en cuenta el primero)
     */
    private function sanear(array $datos, array $archivos) : void
    {
        $this->datos = [
            'titulo' => self::convertir_a_string($datos['titulo'] ?? ''),
            'autor' => self::convertir_a_string($datos['autor'] ?? ''),
            'genero' => self::convertir_a_string($datos['genero'] ?? ''),
            'anio' => (int)($datos['anio'] ?? 0),
            'paginas' => (int)($datos['paginas'] ?? 0),
            'portada' => $archivos[0] ?? []
        ];
    }

    /**
     * Valida los datos guardado junto con los ficheros aportados
     */
    private function validar() : void
    {
        if (mb_strlen($this->datos['titulo']) === 0)
            $this->errores['titulo'] = 'El título no puede estar vacío';

        if (mb_strlen($this->datos['autor']) === 0)
            $this->errores['autor'] = 'El autor no puede estar vacío';

        if (!preg_match('/^[a-zA-ZñÑáÁéÉíÍóÓúÚ ]+$/', $this->datos['autor']))
            $this->errores['autor'] = 'El autor solo puede tener caracteres';

        if (!in_array($this->datos['genero'], GENEROS))
            $this->errores['genero'] = 'El genero aportado no es valido';

        if ($this->datos['anio'] <= 0 || !is_int($this->datos['anio']))
            $this->errores['anio'] = 'El número de páginas debe ser un entero positivo';

        if ($this->datos['paginas'] <= 0 || !is_int($this->datos['paginas']))
            $this->errores['paginas'] = 'El número de páginas debe ser un entero positivo';

        $this->validar_portada();
    }

    /**
     * Valida la portada aportada
     */
    private function validar_portada() : void
    {
        if (empty($this->datos['portada']))
        {
            $this->errores['portada'] = 'No se ha encontrado la portada';
            return;
        }

        if ($this->datos['portada']['errors'] !== UPLOAD_ERR_OK)
        {
            $this->errores['portada'] = 'No se ha subido correctamente la portada';
            return;
        }

        if ($this->datos['portada']['size'] > self::$BYTES_MAXIMOS)
        {
            $this->errores['portada'] = 'La portada pesa demasiado';
            return;
        }

        if (!is_uploaded_file($this->datos['portada']['tmp_name']))
        {
            $this->errores['portada'] = 'La portada no se ha subido correctamente al servidor por los protocolos permitidos';
            return;
        }

        $file_info = new finfo(FILEINFO_MIME_TYPE);
        $mimo_real = $file_info->file($this->datos['portada']['tmp_name']);

        if (!key_exists($mimo_real, self::$MIMOS_ACEPTADOS))
        {
            $this->errores['portada'] = 'El formato de la portada dado no es válido';
            return;
        }

        $this->datos['formato'] = self::$MIMOS_ACEPTADOS[$mimo_real];
    }

    /**
     * Revisa si todo lo verificado han sido valores válidos o si ha encontrado algún fallo en estos
     * @return bool True si todo es válido, false si han habido fallos
     */
    public function es_valido() : bool
    {
        return count($this->errores) === 0;
    }

    /**
     * Obtiene todos los errores
     * @return array Errores encontrados
     */
    public function errores() : array
    {
        return $this->errores;
    }

    /**
     * Obtiene los valores saneados
     * @return array Datos saneados
     */
    public function datos() : array
    {
        return $this->datos;
    }
}