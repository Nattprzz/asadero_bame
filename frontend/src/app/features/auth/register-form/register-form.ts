// ─────────────────────────────────────────────────────────────────────────────
// register-form.ts — formulario de registro.
//
// Este componente gestiona el alta de nuevos usuarios desde el frontend.
// Valida los datos introducidos, envía la petición al servicio de autenticación
// y muestra tanto errores de validación del cliente como errores devueltos por
// el backend.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, inject, output } from '@angular/core';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { A11yModule } from '@angular/cdk/a11y';
import { AuthService } from '../../../core/services/auth-service';

// Relaciona los nombres de campos del backend con los controles del formulario.
const BACKEND_FIELD_MAP: Record<string, string> = {
  name: 'nombre',
};

@Component({
  selector: 'app-register-form',
  standalone: true,
  imports: [ReactiveFormsModule, A11yModule],
  templateUrl: './register-form.html',
})
export class RegisterForm {
  // Servicios utilizados para construir el formulario y registrar al usuario.
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);

  // Eventos emitidos al componente padre.
  readonly closed = output<void>();
  readonly switchToLogin = output<void>();
  readonly registered = output<void>();

  // Formulario reactivo con las validaciones principales del registro.
  readonly registerForm: FormGroup = this.fb.group({
    nombre: ['', [Validators.required]],
    email: ['', [Validators.required, Validators.email]],
    phone: ['', [Validators.required, Validators.pattern('^[6-9][0-9]{8}$')]],
    password: ['', [Validators.required, Validators.minLength(8)]],
    terminos: [false, [Validators.requiredTrue]],
  });

  // Estado visual y errores mostrados en la interfaz.
  formSubmitted = false;
  isRegistering = false;
  registerError: string | null = null;
  fieldErrors: Record<string, string> = {};

  // Valida el formulario y envía los datos al backend.
  onSubmit(): void {
    if (this.isRegistering) return;

    this.formSubmitted = true;
    this.registerError = null;
    this.fieldErrors = {};

    if (this.registerForm.invalid) {
      this.registerForm.markAllAsTouched();
      return;
    }

    this.isRegistering = true;

    const v = this.registerForm.value as {
      nombre: string;
      email: string;
      phone: string;
      password: string;
    };

    this.auth
      .register({ name: v.nombre, email: v.email, phone: v.phone, password: v.password })
      .subscribe({
        next: () => {
          this.isRegistering = false;
          this.registered.emit();
        },
        error: (err: { status: number; error?: Record<string, unknown> }) => {
          this.isRegistering = false;
          this.handleError(err);
        },
      });
  }

  // Indica si un campo debe mostrarse como inválido en la vista.
  isFieldInvalid(fieldName: string): boolean {
    const field = this.registerForm.get(fieldName);
    const hasClientError = !!(field?.invalid && (field.touched || this.formSubmitted));

    return hasClientError || !!this.fieldErrors[fieldName];
  }

  // Devuelve el error de servidor asociado a un campo concreto.
  getServerFieldError(fieldName: string): string | null {
    return this.fieldErrors[fieldName] ?? null;
  }

  // Normaliza los distintos formatos de error que puede devolver la API.
  private handleError(err: { status: number; error?: Record<string, unknown> }): void {
    if (err.status === 0) {
      this.registerError = 'No se pudo conectar con el servidor. Inténtalo más tarde.';
      return;
    }

    if (err.status === 422 || err.status === 400) {
      const body = err.error ?? {};

      const rawErrors =
        (body['errors'] as Record<string, string | string[]> | undefined) ??
        ((body['error'] as Record<string, unknown> | undefined)?.['details'] as
          | Record<string, string | string[]>
          | undefined) ??
        {};

      for (const [backendField, messages] of Object.entries(rawErrors)) {
        const formField = BACKEND_FIELD_MAP[backendField] ?? backendField;
        this.fieldErrors[formField] = Array.isArray(messages) ? messages[0] : String(messages);
      }

      this.registerError =
        (body['message'] as string | undefined) ?? 'Revisa los datos del formulario.';
      return;
    }

    this.registerError =
      ((err.error?.['message'] as string) || null) ??
      'Error al crear la cuenta. Inténtalo de nuevo.';
  }
}