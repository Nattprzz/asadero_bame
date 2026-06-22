<?php

// ─────────────────────────────────────────────────────────────────────────────
// Kernel.php — núcleo principal de la aplicación.
//
// Esta clase representa el punto de entrada de Symfony y se encarga de iniciar
// el framework, cargar la configuración y registrar los servicios necesarios
// para el funcionamiento de la aplicación.
// ─────────────────────────────────────────────────────────────────────────────

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    // Permite utilizar la configuración simplificada de Symfony.
    use MicroKernelTrait;
}