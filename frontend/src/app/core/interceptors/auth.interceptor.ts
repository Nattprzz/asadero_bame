// ─────────────────────────────────────────────────────────────────────────────
// auth-interceptor.ts — gestión automática de autenticación HTTP.
//
// Este interceptor añade el token JWT a todas las peticiones dirigidas a la
// API protegida de la aplicación, evitando que cada servicio tenga que
// incorporarlo manualmente.
//
// Además, supervisa las respuestas del servidor para detectar errores de
// autenticación. Si una petición devuelve un código 401 (Unauthorized),
// la sesión del usuario se invalida automáticamente y se redirige al
// formulario de inicio de sesión.
//
// Su objetivo es centralizar la gestión de autenticación, mejorar la
// seguridad y mantener un comportamiento consistente en toda la aplicación.
// ─────────────────────────────────────────────────────────────────────────────

import { inject } from '@angular/core';
import { HttpInterceptorFn } from '@angular/common/http';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';
import { AuthService } from '../services/auth-service';
import { environment } from '../../../environments/environment';

const isOurApi = (url: string): boolean => {
  if (environment.apiUrl) {
    return url.startsWith(environment.apiUrl);
  }
  return url.startsWith('/api/');
};

const isAuthEndpoint = (url: string): boolean => url.includes('/v1/auth/');

export const authInterceptor: HttpInterceptorFn = (req, next) => {
  const auth = inject(AuthService);
  const router = inject(Router);
  const token = auth.token();

  const shouldAddToken = token && isOurApi(req.url) && !isAuthEndpoint(req.url);

  const outgoing = shouldAddToken
    ? req.clone({ setHeaders: { Authorization: `Bearer ${token}` } })
    : req;

  return next(outgoing).pipe(
    catchError((err) => {
      if (err.status === 401 && isOurApi(req.url) && !isAuthEndpoint(req.url)) {
        auth.clearSession();
        router.navigateByUrl('/home/login');
      }
      return throwError(() => err);
    }),
  );
};