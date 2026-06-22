// ─────────────────────────────────────────────────────────────────────────────
// admin.guard.ts — protección de rutas administrativas.
//
// Este guard controla el acceso a las zonas reservadas para usuarios
// autenticados. Si el usuario no ha iniciado sesión, se le redirige a la
// página principal de la aplicación.
// ─────────────────────────────────────────────────────────────────────────────

import { inject } from '@angular/core';
import { type CanActivateFn, Router } from '@angular/router';
import { AuthService } from '../services/auth-service';

export const adminGuard: CanActivateFn = () => {
  const auth = inject(AuthService);
  const router = inject(Router);

  // Impide el acceso a usuarios no autenticados.
  if (auth.currentRole() === 'anonymous') {
    return router.parseUrl('/home');
  }

  return true;
};