// ─────────────────────────────────────────────────────────────────────────────
// email-verification.ts — modal de verificación por correo electrónico.
//
// Este componente gestiona el proceso de validación mediante un código
// enviado al correo del usuario. También controla la cuenta atrás para
// limitar el reenvío de códigos y comunica al componente padre las acciones
// realizadas por el usuario.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, OnInit, OnDestroy, output } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { A11yModule } from '@angular/cdk/a11y';

@Component({
  selector: 'app-email-verification',
  standalone: true,
  imports: [FormsModule, A11yModule],
  templateUrl: './email-verification.html',
})
export class EmailVerification implements OnInit, OnDestroy {

  // Eventos emitidos al componente padre.
  readonly closed = output<void>();
  readonly verified = output<void>();
  readonly backToRegister = output<void>();

  // Estado actual del proceso de verificación.
  verificationCode = '';
  timeLeft = 60;
  canResend = false;

  // Referencia al temporizador utilizado para la cuenta atrás.
  private interval: ReturnType<typeof setInterval> | null = null;

  ngOnInit(): void {
    this.startTimer();
  }

  ngOnDestroy(): void {
    this.stopTimer();
  }

  // Inicia o reinicia la cuenta atrás para el reenvío del código.
  startTimer(): void {
    this.canResend = false;
    this.timeLeft = 60;

    this.stopTimer();

    this.interval = setInterval(() => {
      if (this.timeLeft > 0) {
        this.timeLeft--;
      } else {
        this.canResend = true;
        this.stopTimer();
      }
    }, 1000);
  }

  // Detiene el temporizador activo para evitar ejecuciones innecesarias.
  stopTimer(): void {
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
    }
  }

  // Solicita el envío de un nuevo código cuando finaliza la espera.
  resendCode(): void {
    if (!this.canResend) return;

    console.log('Reenviando código...');
    this.startTimer();
  }

  // Envía el código introducido y notifica la validación al componente padre.
  onSubmit(): void {
    console.log('Código introducido:', this.verificationCode);
    this.verified.emit();
  }
}