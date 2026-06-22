<?php

// ─────────────────────────────────────────────────────────────────────────────
// bundles.php — registro de bundles de Symfony.
//
// Este archivo indica qué bundles estarán disponibles en la aplicación y en
// qué entornos deben cargarse. Symfony los registra automáticamente durante
// el arranque del proyecto.
// ─────────────────────────────────────────────────────────────────────────────

return [
    // Funcionalidades principales del framework.
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],

    // Sistema de autenticación y control de acceso.
    Symfony\Bundle\SecurityBundle\SecurityBundle::class => ['all' => true],

    // Integración de Doctrine ORM.
    Doctrine\Bundle\DoctrineBundle\DoctrineBundle::class => ['all' => true],

    // Gestión de migraciones de base de datos.
    Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle::class => ['all' => true],

    // Configuración de políticas CORS para la API.
    Nelmio\CorsBundle\NelmioCorsBundle::class => ['all' => true],

    // Carga de datos de prueba en desarrollo y testing.
    Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],

    // Generación automática de código durante el desarrollo.
    Symfony\Bundle\MakerBundle\MakerBundle::class => ['dev' => true],
];