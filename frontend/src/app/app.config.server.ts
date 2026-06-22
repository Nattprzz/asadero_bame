// ─────────────────────────────────────────────────────────────────────────────
// app.config.server.ts — configuración específica del servidor.
//
// Este archivo amplía la configuración principal de Angular cuando la
// aplicación se ejecuta mediante Server Side Rendering (SSR).
//
// Aquí se registran los proveedores necesarios para que Angular pueda
// renderizar páginas en el servidor y utilizar las rutas definidas para SSR.
// ─────────────────────────────────────────────────────────────────────────────

import { ApplicationConfig, mergeApplicationConfig } from '@angular/core';
import { provideServerRendering, withRoutes } from '@angular/ssr';

import { appConfig } from './app.config';
import { serverRoutes } from './app.routes.server';

// Configuración exclusiva del entorno de servidor.
const serverConfig: ApplicationConfig = {
  providers: [
    // Activa el renderizado en servidor utilizando las rutas SSR.
    provideServerRendering(withRoutes(serverRoutes)),
  ],
};

// Combina la configuración general de la aplicación con la específica de SSR.
export const config = mergeApplicationConfig(appConfig, serverConfig);