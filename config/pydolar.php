<?php

return [

	/**
     * Token de autenticación proporcionado por la API de Pydolar.
     * Este token es necesario para realizar algunas solicitudes a la API.
	 *
     * @var string
     */
    'token' => env('PYDOLAR_TOKEN', ''),

    /**
     * Tiempo de espera global en segundos para las solicitudes a la API de Pydolar.
     * Si una solicitud tarda más de este tiempo, se considerará fallida.
     * Un valor de 0 significa que no hay tiempo de espera.
	 * El valor debe ser un entero positivo.
	 *
     * @var int
     */
    'timeout' => env('PYDOLAR_TIMEOUT', 0),

	/**
	 * Controla la verificación del certificado SSL en Guzzle.
	 * - `true`: Habilita la verificación SSL.
	 * - `false`: Desactiva la verificación SSL.
	 * - Ruta de archivo PEM: Especifica el archivo de certificados para la verificación.
	 *
	 * @var bool|string
	 */
	'verify_ssl' => env('PYDOLAR_VERIFY_SSL', true),

];
