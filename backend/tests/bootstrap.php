<?php

// ─────────────────────────────────────────────────────────────────────────────
// bootstrap.php — inicialización de la aplicación.
//
// Este archivo prepara el entorno antes de que Symfony arranque. Se encarga
// de cargar el autoloader de Composer y las variables de entorno definidas
// en el archivo .env.
// ─────────────────────────────────────────────────────────────────────────────

use Symfony\Component\Dotenv\Dotenv;

// Carga automáticamente las dependencias instaladas mediante Composer.
require dirname(__DIR__).'/vendor/autoload.php';

// Carga las variables de entorno disponibles para la aplicación.
if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}