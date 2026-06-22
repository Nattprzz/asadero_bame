<?php

// ─────────────────────────────────────────────────────────────────────────────
// index.php — punto de entrada de la aplicación.
//
// Este archivo inicia Symfony, carga las dependencias necesarias y crea la
// instancia principal del kernel utilizando la configuración del entorno.
// ─────────────────────────────────────────────────────────────────────────────

use App\Kernel;

// Carga el sistema de ejecución y autoload generado por Composer.
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// Crea e inicia el kernel con el entorno y modo debug actuales.
return static fn (array $context) => new Kernel(
    $context['APP_ENV'],
    (bool) $context['APP_DEBUG']
);