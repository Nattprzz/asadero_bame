// ─────────────────────────────────────────────────────────────────────────────
// app.config.ts — configuración global de la aplicación.
//
// Este archivo centraliza los proveedores principales utilizados por Angular.
// Desde aquí se configuran aspectos como el enrutado, las peticiones HTTP,
// los interceptores, la optimización de eventos y la librería de iconos.
//
// Se carga al arrancar la aplicación y actúa como punto principal de
// configuración del frontend.
// ─────────────────────────────────────────────────────────────────────────────

import {
  type ApplicationConfig,
  provideBrowserGlobalErrorListeners,
  provideZoneChangeDetection,
} from '@angular/core';

import { provideRouter, withInMemoryScrolling } from '@angular/router';
import { provideHttpClient, withFetch, withInterceptors } from '@angular/common/http';

import { authInterceptor } from './core/interceptors/auth.interceptor';

import {
  provideTablerIconConfig,
  provideTablerIcons,
} from '@tabler/icons-angular';

import {
  IconAccessible,
  IconAlertTriangle,
  IconArrowDown,
  IconArrowLeft,
  IconArrowRight,
  IconArrowUp,
  IconBasket,
  IconBookmark,
  IconBuilding,
  IconBuildingStore,
  IconCalendar,
  IconCheck,
  IconChevronDown,
  IconChevronLeft,
  IconChevronRight,
  IconChevronUp,
  IconCircle,
  IconCircleCheck,
  IconCircleMinus,
  IconClock,
  IconCookie,
  IconCreditCard,
  IconDeviceFloppy,
  IconEye,
  IconFileDescription,
  IconFileText,
  IconFlame,
  IconGauge,
  IconHome,
  IconLogin,
  IconLogout,
  IconMail,
  IconMan,
  IconMap,
  IconMapPin,
  IconMapPinFilled,
  IconMeat,
  IconMenu2,
  IconMinus,
  IconMoon,
  IconPackage,
  IconPhone,
  IconPhoto,
  IconPlus,
  IconRefresh,
  IconRotateClockwise,
  IconSearch,
  IconSearchOff,
  IconSettings,
  IconShieldCheck,
  IconShoppingCart,
  IconStar,
  IconSun,
  IconUser,
  IconUserCheck,
  IconUserCircle,
  IconX,
} from '@tabler/icons-angular';

import { routes } from './app.routes';

export const appConfig: ApplicationConfig = {
  providers: [
    // Gestiona errores globales no capturados por Angular.
    provideBrowserGlobalErrorListeners(),

    // Agrupa múltiples eventos en un único ciclo de detección de cambios
    // para mejorar el rendimiento de la aplicación.
    provideZoneChangeDetection({
      eventCoalescing: true,
    }),

    // Configuración global del cliente HTTP.
    // Se utiliza Fetch API y se registra el interceptor de autenticación.
    provideHttpClient(
      withFetch(),
      withInterceptors([authInterceptor]),
    ),

    // Configuración principal del sistema de rutas.
    provideRouter(
      routes,

      // Mantiene la posición de scroll al navegar entre páginas
      // y permite desplazarse automáticamente a anclas.
      withInMemoryScrolling({
        scrollPositionRestoration: 'enabled',
        anchorScrolling: 'enabled',
      }),
    ),

    // Configuración por defecto de los iconos Tabler.
    provideTablerIconConfig({
      size: 24,
      stroke: 2,
    }),

    // Registro global de todos los iconos utilizados en la aplicación.
    provideTablerIcons({
      IconAccessible,
      IconAlertTriangle,
      IconArrowDown,
      IconArrowLeft,
      IconArrowRight,
      IconArrowUp,
      IconBasket,
      IconBookmark,
      IconBuilding,
      IconBuildingStore,
      IconCalendar,
      IconCheck,
      IconChevronDown,
      IconChevronLeft,
      IconChevronRight,
      IconChevronUp,
      IconCircle,
      IconCircleCheck,
      IconCircleMinus,
      IconClock,
      IconCookie,
      IconCreditCard,
      IconDeviceFloppy,
      IconEye,
      IconFileDescription,
      IconFileText,
      IconFlame,
      IconGauge,
      IconHome,
      IconLogin,
      IconLogout,
      IconMail,
      IconMan,
      IconMap,
      IconMapPin,
      IconMapPinFilled,
      IconMeat,
      IconMenu2,
      IconMinus,
      IconMoon,
      IconPackage,
      IconPhone,
      IconPhoto,
      IconPlus,
      IconRefresh,
      IconRotateClockwise,
      IconSearch,
      IconSearchOff,
      IconSettings,
      IconShieldCheck,
      IconShoppingCart,
      IconStar,
      IconSun,
      IconUser,
      IconUserCheck,
      IconUserCircle,
      IconX,
    }),
  ],
};