<?php

// ─────────────────────────────────────────────────────────────────────────────
// preload.php — precarga de archivos optimizados.
//
// En producción, Symfony puede cargar determinados archivos antes de recibir
// peticiones para reducir tiempos de respuesta y mejorar el rendimiento.
// ─────────────────────────────────────────────────────────────────────────────

if (file_exists(dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php')) {
    require dirname(__DIR__).'/var/cache/prod/App_KernelProdContainer.preload.php';
}