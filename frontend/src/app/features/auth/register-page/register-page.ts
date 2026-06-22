// ─────────────────────────────────────────────────────────────────────────────
// register-page.ts — página de registro.
//
// Esta vista actúa como contenedor del formulario de creación de cuenta.
// También gestiona los cambios de estado una vez completado el registro y
// las distintas redirecciones disponibles desde la página.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, inject } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { BreadcrumbBar } from '../../../shared/components/breadcrumb-bar/breadcrumb-bar';
import { RegisterForm } from '../register-form/register-form';

@Component({
  selector: 'app-register-page',
  standalone: true,
  imports: [RouterLink, TablerIconComponent, BreadcrumbBar, RegisterForm],
  templateUrl: './register-page.html',
})
export class RegisterPage {
  // Servicio utilizado para la navegación entre páginas.
  private readonly router = inject(Router);

  // Controla la visualización del mensaje de registro completado.
  registered = false;

  // Se ejecuta cuando el formulario finaliza correctamente el registro.
  // La sesión ya ha sido creada por el servicio de autenticación.
  onRegistered(): void {
    this.registered = true;
  }

  // Redirige al usuario a la pantalla de inicio de sesión.
  onSwitchToLogin(): void {
    this.router.navigateByUrl('/home/login');
  }

  // Cierra la vista actual y vuelve a la página principal.
  onClosed(): void {
    this.router.navigateByUrl('/home');
  }
}