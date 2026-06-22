// ─────────────────────────────────────────────────────────────────────────────
// login-page.ts — página de inicio de sesión.
//
// Esta vista actúa como contenedor del formulario de acceso. También gestiona
// la navegación una vez completado el proceso de autenticación o cuando el
// usuario decide acceder al formulario de registro.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, inject } from '@angular/core';
import { Router } from '@angular/router';
import { LoginForm } from '../login-form/login-form';
import { AuthService } from '@core/services/auth-service';

@Component({
  selector: 'app-login-page',
  standalone: true,
  imports: [LoginForm],
  templateUrl: './login-page.html',
})
export class LoginPage {
  // Servicios utilizados para la navegación y la gestión de sesión.
  private readonly router = inject(Router);
  private readonly auth = inject(AuthService);

  // Se ejecuta al cerrar el formulario.
  // Si el usuario ha iniciado sesión correctamente se redirige a su destino,
  // en caso contrario vuelve a la página principal.
  onClosed(): void {
    if (this.auth.isLoggedIn()) {
      this.auth.navigateAfterLogin();
    } else {
      this.router.navigateByUrl('/home');
    }
  }

  // Redirige al formulario de registro.
  onSwitchToRegister(): void {
    this.router.navigateByUrl('/home/login?mode=register');
  }
}
