import { Component, computed, inject, output, signal } from '@angular/core';
import { A11yModule } from '@angular/cdk/a11y';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { ActivatedRoute, RouterLink } from '@angular/router';
import { finalize } from 'rxjs';

import { AuthService } from '../../../core/services/auth-service';
import { UserPrefService } from '../../../core/services/user-pref-service';
import { LanguageService } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';
import { AccessibilityMenu } from '../../../shared/components/accessibility-menu/accessibility-menu';

type AuthMode = 'login' | 'register';

interface ApiError {
  status: number;
  error?: {
    message?: string;
    errors?: Record<string, string | string[]>;
    error?: { details?: Record<string, string | string[]> };
  };
}

@Component({
  selector: 'app-login-form',
  standalone: true,
  imports: [
    ReactiveFormsModule,
    A11yModule,
    RouterLink,
    AccessibilityMenu,
  ],
  templateUrl: './login-form.html',
  styleUrl: './login-form.css',
})
export class LoginForm {
  private readonly fb   = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly route = inject(ActivatedRoute);
  private readonly lang = inject(LanguageService);

  readonly userPref = inject(UserPrefService);
  readonly closed   = output<void>();

  readonly mode          = signal<AuthMode>('login');
  readonly showPassword  = signal(false);
  readonly done          = signal(false);
  readonly loading       = signal(false);
  readonly submitError   = signal<string | null>(null);

  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));

  // Static array — no Signal, directly iterable in @for
  readonly perks = [
    { icon: '⭐', text: 'Acumula puntos en cada pedido' },
    { icon: '🔄', text: 'Repite tus pedidos favoritos' },
    { icon: '%',  text: 'Accede a ofertas exclusivas' },
  ];

  readonly socials = [
    { name: 'Google',   shortName: 'G' },
    { name: 'Apple',    shortName: 'A' },
    { name: 'Facebook', shortName: 'F' },
  ];

  readonly authForm = this.fb.nonNullable.group({
    name:     [''],
    email:    ['', [Validators.required, Validators.email]],
    phone:    [''],
    password: ['', [Validators.required]],
    remember: [false],
    terms:    [false],
  });

  readonly isRegister = computed(() => this.mode() === 'register');

  readonly passwordStrength = computed(() => {
    const pw = this.authForm.controls.password.value;
    if (!pw) return 0;
    let score = pw.length >= 8 ? 1 : 0;
    if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score += 1;
    if (/\d/.test(pw) || /[^A-Za-z0-9]/.test(pw)) score += 1;
    return score;
  });

  readonly strengthLabel = computed(() => {
    const labels = ['', 'Débil', 'Media', 'Fuerte'];
    return labels[this.passwordStrength()];
  });

  formSubmitted = false;
  fieldErrors: Record<string, string> = {};

  constructor() {
    if (this.route.snapshot.queryParamMap.get('mode') === 'register') {
      this.mode.set('register');
      this.applyModeValidators('register');
    }
  }

  setMode(mode: AuthMode): void {
    if (this.loading() || this.mode() === mode) return;
    this.mode.set(mode);
    this.done.set(false);
    this.formSubmitted = false;
    this.fieldErrors = {};
    this.submitError.set(null);
    this.applyModeValidators(mode);
  }

  togglePassword(): void {
    this.showPassword.update((v) => !v);
  }

  toggleTheme(): void {
    this.userPref.setTheme(this.userPref.effectiveTheme() === 'dark' ? 'light' : 'dark');
  }

  onSubmit(): void {
    if (this.loading()) return;

    this.formSubmitted = true;
    this.fieldErrors = {};
    this.submitError.set(null);
    this.applyModeValidators(this.mode());

    if (this.authForm.invalid) {
      this.authForm.markAllAsTouched();
      return;
    }

    this.loading.set(true);

    const { name, email, phone, password } = this.authForm.getRawValue();

    const request$ = this.isRegister()
      ? this.auth.register({ name: name.trim(), email: email.trim(), phone: phone.trim(), password })
      : this.auth.login({ email: email.trim(), password });

    request$.pipe(finalize(() => this.loading.set(false))).subscribe({
      next:  () => this.done.set(true),
      error: (err: ApiError) => this.handleError(err),
    });
  }

  continueAfterSuccess(): void {
    this.closed.emit();
  }

  isFieldInvalid(fieldName: 'name' | 'email' | 'phone' | 'password' | 'terms'): boolean {
    const field = this.authForm.controls[fieldName];
    return !!this.fieldErrors[fieldName] || (field.invalid && (field.touched || this.formSubmitted));
  }

  fieldError(fieldName: 'name' | 'email' | 'phone' | 'password' | 'terms'): string {
    if (this.fieldErrors[fieldName]) return this.fieldErrors[fieldName];

    const field = this.authForm.controls[fieldName];
    const a = this.t().auth;

    if (fieldName === 'email') {
      if (field.hasError('required')) return a.emailRequired;
      if (field.hasError('email'))    return a.emailInvalid;
    }
    if (fieldName === 'password') {
      if (field.hasError('minlength')) return 'La contraseña debe tener al menos 8 caracteres.';
      return a.passwordRequired;
    }
    if (fieldName === 'name')  return 'El nombre es obligatorio.';
    if (fieldName === 'phone') {
      if (field.hasError('pattern')) return 'Introduce un número válido (6–9 + 8 dígitos).';
      return 'El teléfono es obligatorio.';
    }
    if (fieldName === 'terms') return 'Debes aceptar los términos y condiciones.';

    return '';
  }

  private applyModeValidators(mode: AuthMode): void {
    const { name, phone, password, terms } = this.authForm.controls;

    name.setValidators(mode === 'register' ? [Validators.required] : []);

    phone.setValidators(
      mode === 'register'
        ? [Validators.required, Validators.pattern(/^[6-9][0-9]{8}$/)]
        : [],
    );

    password.setValidators(
      mode === 'register'
        ? [Validators.required, Validators.minLength(8)]
        : [Validators.required],
    );

    terms.setValidators(mode === 'register' ? [Validators.requiredTrue] : []);

    name.updateValueAndValidity({ emitEvent: false });
    phone.updateValueAndValidity({ emitEvent: false });
    password.updateValueAndValidity({ emitEvent: false });
    terms.updateValueAndValidity({ emitEvent: false });
  }

  private handleError(error: ApiError): void {
    if (error.status === 0) {
      this.submitError.set('No se pudo conectar con el servidor. Comprueba tu conexión.');
      return;
    }

    if (error.status === 401) {
      this.submitError.set(this.t().auth.credentialsError);
      return;
    }

    const details = error.error?.errors ?? error.error?.error?.details ?? {};

    for (const [field, messages] of Object.entries(details)) {
      const formField = field === 'nombre' ? 'name' : field;
      this.fieldErrors[formField] = Array.isArray(messages) ? messages[0] : String(messages);
    }

    this.submitError.set(error.error?.message ?? 'Se ha producido un error. Inténtalo de nuevo.');
  }
}
