<?php

return [

    /**
     * Fecha específica para realizar pruebas con la API de Pydolar.
     * Debe estar en formato AAAA-MM-DD (ejemplo: 2025-01-31).
	 *
     * @var string
     */
    'date' => env('PYDOLAR_TEST_DATE', now()),

    /**
     * Fecha de inicio para un rango de pruebas con la API de Pydolar.
     * Debe estar en formato AAAA-MM-DD (ejemplo: 2025-01-31).
	 *
     * @var string
     */
    'start_date' => env('PYDOLAR_TEST_START_DATE', now()),

    /**
     * Fecha de fin para un rango de pruebas con la API de Pydolar.
     * Debe estar en formato AAAA-MM-DD (ejemplo: 2025-01-31).
	 *
     * @var string
     */
    'end_date' => env('PYDOLAR_TEST_END_DATE', now()),

];
