// ─────────────────────────────────────────────────────────────────────────────
// app.routes.ts — rutas principales de la aplicación.
//
// Este archivo define el sistema de navegación del frontend. Agrupa las rutas
// públicas, las rutas de usuario, las rutas de administración, las páginas de
// error y las redirecciones generales.
//
// También aplica el guard de autenticación por grupos de rutas y utiliza lazy
// loading para cargar cada página solo cuando se necesita.
// ─────────────────────────────────────────────────────────────────────────────

import { inject } from '@angular/core';
import { Router, type Routes } from '@angular/router';

import { authGuard } from './core/guards/auth-guard';

export const routes: Routes = [
  // ─── Rutas generales ───────────────────────────────────────────────────────
  {
    path: 'acerca-de-nosotros',
    redirectTo: '/home/acerca-de-nosotros',
    pathMatch: 'full',
  },
  {
    path: 'legal/:section',
    title: 'Legal | Bame',
    loadComponent: () => import('./features/legal/legal-page/legal-page').then((m) => m.LegalPage),
  },

  // ─── Rutas públicas y de usuario ───────────────────────────────────────────
  {
    path: 'home',
    canActivateChild: [authGuard],
    children: [
      {
        path: '',
        title: 'Inicio | Bame',
        loadComponent: () => import('./features/home/home').then((m) => m.Home),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'carta',
        title: 'Carta | Bame — Asador de pollo en Murcia',
        loadComponent: () => import('./features/carta/carta').then((m) => m.CartaPage),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'locales',
        title: 'Nuestros locales | Bame',
        loadComponent: () =>
          import('./features/locals/locals-page/locals-page').then((m) => m.LocalsPage),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'locales/:id/reserva',
        title: 'Carta y reserva | Bame',
        loadComponent: () =>
          import('./features/reservations/reservation/reservation').then((m) => m.Reservation),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'locales/:id/reserva/pago/success',
        title: 'Pago completado | Bame',
        loadComponent: () =>
          import('./features/reservations/payment-success/payment-success').then(
            (m) => m.PaymentSuccessPage,
          ),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'locales/:id/reserva/pago/cancel',
        title: 'Pago cancelado | Bame',
        loadComponent: () =>
          import('./features/reservations/payment-cancel/payment-cancel').then(
            (m) => m.PaymentCancelPage,
          ),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'locales/:id/reserva/pago',
        title: 'Confirmar pedido | Bame',
        loadComponent: () =>
          import('./features/reservations/checkout/checkout').then((m) => m.CheckoutPage),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'acerca-de-nosotros',
        title: 'Acerca de Nosotros | Bame',
        loadComponent: () => import('./features/about/about').then((m) => m.AboutPage),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'contacto',
        title: 'Contacto | Bame',
        loadComponent: () => import('./features/contact/contact').then((m) => m.ContactPage),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'perfil',
        title: 'Mi perfil | Bame',
        loadComponent: () => import('./features/profile/profile').then((m) => m.ProfilePage),
        data: { roles: ['USER'] },
      },
      {
        path: 'login',
        title: 'Iniciar sesión | Bame',
        loadComponent: () =>
          import('./features/auth/login-page/login-page').then((m) => m.LoginPage),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
      {
        path: 'registro',
        redirectTo: ({ queryParams }) =>
          inject(Router).createUrlTree(['/home/login'], {
            queryParams: { ...queryParams, mode: 'register' },
          }),
      },
      {
        path: 'recuperar-contrasena',
        title: 'Recuperar contraseña | Bame',
        loadComponent: () =>
          import('./features/auth/recuperar-contrasena/recuperar-contrasena').then(
            (m) => m.RecuperarContrasena,
          ),
        data: { roles: ['ANONYMOUS', 'USER'] },
      },
    ],
  },

  // ─── Rutas de administración ───────────────────────────────────────────────
  {
    path: 'admin',
    loadComponent: () =>
      import('./shared/components/admin-layout/admin-layout').then((m) => m.AdminLayout),
    canActivateChild: [authGuard],
    children: [
      {
        path: '',
        title: 'Panel de administración | Bame',
        loadComponent: () => import('./features/admin/admin').then((m) => m.Admin),
        data: { roles: ['ADMIN'] },
      },
      {
        path: 'local/:id',
        title: 'Gestión de local | Bame',
        loadComponent: () =>
          import('./features/locals/local-admin/local-admin').then((m) => m.LocalAdmin),
        data: { roles: ['ADMIN', 'MANAGER', 'STORE'] },
      },
      {
        path: 'local/:id/disponibilidad',
        title: 'Disponibilidad | Bame',
        loadComponent: () =>
          import('./shared/components/product-availability/product-availability').then(
            (m) => m.ProductAvailability,
          ),
        data: { roles: ['ADMIN', 'MANAGER', 'STORE'] },
      },
      {
        path: 'local/:id/reservas',
        title: 'Reservas | Bame',
        loadComponent: () =>
          import('./features/reservations/reservation-list/reservation-list').then(
            (m) => m.ReservationList,
          ),
        data: { roles: ['ADMIN', 'MANAGER', 'STORE'] },
      },
      {
        path: 'local/:id/avanzado',
        title: 'Configuración avanzada | Bame',
        loadComponent: () =>
          import('./features/locals/local-adv-admin/local-adv-admin').then(
            (m) => m.LocalAdvAdmin,
          ),
        data: { roles: ['ADMIN', 'MANAGER'] },
      },
      {
        path: 'local/:id/historial',
        title: 'Historial de reservas | Bame',
        loadComponent: () =>
          import('./features/reservations/reservation-history/reservation-history').then(
            (m) => m.ReservationHistory,
          ),
        data: { roles: ['ADMIN', 'MANAGER'] },
      },
      {
        path: 'local/:id/resumen-dia',
        title: 'Resumen del día | Bame',
        loadComponent: () =>
          import('./features/admin/resumen-dia/resumen-dia').then((m) => m.ResumenDia),
        data: { roles: ['ADMIN', 'RESPONSABLE'] },
      },
      {
        path: 'local/:id/gerente',
        title: 'Panel gerente | Bame',
        loadComponent: () =>
          import('./features/locals/gerente-panel/gerente-panel').then((m) => m.GerentePanel),
        data: { roles: ['ADMIN', 'MANAGER'] },
      },
    ],
  },

  // ─── Páginas de error ──────────────────────────────────────────────────────
  {
    path: '401',
    title: 'No autorizado | Bame',
    loadComponent: () => import('./pages/page401/page401').then((m) => m.Page401),
  },
  {
    path: '403',
    title: 'Acceso denegado | Bame',
    loadComponent: () => import('./pages/page403/page403').then((m) => m.Page403),
  },
  {
    path: '500',
    title: 'Error del servidor | Bame',
    loadComponent: () => import('./pages/page500/page500').then((m) => m.Page500),
  },
  {
    path: '503',
    title: 'Servicio no disponible | Bame',
    loadComponent: () => import('./pages/page503/page503').then((m) => m.Page503),
  },

  // ─── Redirecciones finales ─────────────────────────────────────────────────
  {
    path: '',
    redirectTo: 'home',
    pathMatch: 'full',
  },
  {
    path: '**',
    title: 'Página no encontrada | Bame',
    loadComponent: () => import('./pages/page404/page404').then((m) => m.Page404),
  },
];
