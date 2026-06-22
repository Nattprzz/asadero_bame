import { Component, computed, inject, signal } from '@angular/core';
import { FormBuilder, ReactiveFormsModule, Validators } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { finalize } from 'rxjs';
import { A11yModule } from '@angular/cdk/a11y';
import { AuthService } from '../../../core/services/auth-service';
import { UserPrefService } from '../../../core/services/user-pref-service';
import { AccessibilityMenu } from '../../../shared/components/accessibility-menu/accessibility-menu';
import { LanguageService } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';

@Component({
  selector: 'app-recuperar-contrasena',
  standalone: true,
  imports: [ReactiveFormsModule, A11yModule, RouterLink, AccessibilityMenu],
  templateUrl: './recuperar-contrasena.html',
  styleUrl: '../login-form/login-form.css',
})
export class RecuperarContrasena {
  private readonly fb = inject(FormBuilder);
  private readonly auth = inject(AuthService);
  private readonly langService = inject(LanguageService);
  readonly userPref = inject(UserPrefService);

  readonly t = computed(() => getUiStrings(this.langService.currentLang()).auth);

  readonly form = this.fb.nonNullable.group({
    email: ['', [Validators.required, Validators.email]],
  });

  readonly loading = signal(false);
  readonly done = signal(false);
  readonly submitError = signal<string | null>(null);
  formSubmitted = false;

  toggleTheme(): void {
    this.userPref.setTheme(this.userPref.effectiveTheme() === 'dark' ? 'light' : 'dark');
  }

  isEmailInvalid(): boolean {
    const field = this.form.controls.email;
    return field.invalid && (field.touched || this.formSubmitted);
  }

  emailError(): string {
    const field = this.form.controls.email;
    if (field.hasError('required')) return 'Introduce tu correo electrónico.';
    if (field.hasError('email')) return 'Introduce un email válido.';
    return '';
  }

  onSubmit(): void {
    if (this.loading()) return;

    this.formSubmitted = true;
    this.submitError.set(null);

    if (this.form.invalid) {
      this.form.markAllAsTouched();
      return;
    }

    this.loading.set(true);
    const { email } = this.form.getRawValue();

    this.auth
      .requestPasswordReset(email.trim())
      .pipe(finalize(() => this.loading.set(false)))
      .subscribe({
        next: () => this.done.set(true),
        error: () => this.submitError.set(this.t().forgotPageError),
      });
  }
}
