// ─────────────────────────────────────────────────────────────────────────────
// auth.guard.ts — protección de rutas según rol.
//
// Este guard controla el acceso a rutas privadas de la aplicación comprobando
// los roles definidos en la ruta. Si el usuario no tiene permisos suficientes,
// se le redirige a la página correspondiente de error.
// ─────────────────────────────────────────────────────────────────────────────

import { inject, isDevMode } from '@angular/core';
import {
  type CanActivateFn,
  type ActivatedRouteSnapshot,
  Router,
  type UrlTree,
} from '@angular/router';
import { AuthService, type UserRole, resolveRouteRole } from '../services/auth-service';

export const authGuard: CanActivateFn = (route: ActivatedRouteSnapshot): boolean | UrlTree => {
  const router = inject(Router);
  const auth = inject(AuthService);

  const requiredRoles = (route.data?.['roles'] as string[] | undefined) ?? [];

  // Si la ruta no define roles, se bloquea por seguridad.
  if (requiredRoles.length === 0) {
    return router.parseUrl('/home');
  }

  // Convierte los roles definidos en la ruta al formato interno de la aplicación.
  const resolvedRoles: UserRole[] = requiredRoles
    .map((r) => resolveRouteRole(r))
    .filter((r): r is UserRole => r != null);

  if (resolvedRoles.length === 0) {
    if (isDevMode()) {
      console.warn('[Guard] No se pudieron resolver los roles:', requiredRoles);
    }

    return router.parseUrl('/home');
  }

  const currentRole = auth.currentRole();
  const hasRole = resolvedRoles.some((role) => auth.hasRoleOrAbove(role));

  if (!hasRole) {
    // Guarda la ruta intentada para poder volver después del login.
    auth.setRedirectUrl(router.url);

    if (isDevMode()) {
      console.warn(
        '[Guard] Acceso denegado. Rol actual:',
        currentRole,
        'roles requeridos:',
        resolvedRoles
      );
    }

    // Diferencia entre usuario no autenticado y usuario sin permisos.
    if (currentRole === 'anonymous') {
      return router.parseUrl('/401');
    }

    return router.parseUrl('/403');
  }

  // En rutas de local, comprueba que el gestor pertenezca a ese local.
  if (route.params['id'] && !auth.canAccessAdmin()) {
    const localId = Number(route.params['id']);
    const userLocalId = auth.user()?.localId;

    if (userLocalId != null && userLocalId !== localId) {
      if (isDevMode()) {
        console.warn('[Guard] Acceso denegado — no eres gestor de este local');
      }

      return router.parseUrl(auth.getRedirectUrl(currentRole));
    }
  }

  return true;
};