// ─────────────────────────────────────────────────────────────────────────────
// payment-cancel.ts — cancelación de pago.
//
// Esta página se muestra cuando el usuario abandona o cancela el proceso de
// pago en Stripe. El pedido se mantiene en el carrito para permitir que el
// usuario pueda volver a intentarlo sin tener que reconstruirlo.
// ─────────────────────────────────────────────────────────────────────────────

import { Component, computed, inject, OnInit } from '@angular/core';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { BreadcrumbBar } from '../../../shared/components/breadcrumb-bar/breadcrumb-bar';
import { LanguageService } from '../../../core/services/language.service';
import { getUiStrings } from '../../../core/i18n/ui-strings';

@Component({
  selector: 'app-payment-cancel',
  standalone: true,
  imports: [RouterLink, TablerIconComponent, BreadcrumbBar],
  templateUrl: './payment-cancel.html',
})
export class PaymentCancelPage implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly lang = inject(LanguageService);
  protected readonly t = computed(() => getUiStrings(this.lang.currentLang()));

  // Identificador del local asociado al pedido.
  localId = '';

  // Recupera el local actual desde la URL.
  // El carrito no se vacía para que el usuario pueda reintentar el pago.
  ngOnInit(): void {
    this.localId = this.route.snapshot.paramMap.get('id') ?? '';
  }

  // Redirige nuevamente a la pantalla de pago.
  retryPayment(): void {
    this.router.navigate(['/home/locales', this.localId, 'reserva', 'pago']);
  }
}