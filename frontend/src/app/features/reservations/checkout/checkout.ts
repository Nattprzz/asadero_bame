import { Component, computed, inject, OnInit } from '@angular/core';
import { CurrencyPipe } from '@angular/common';
import { ActivatedRoute, Router, RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { CartService, type CartItem } from '../../../core/services/cart.service';
import { PaymentService } from '../../../core/services/payment.service';
import { PedidoService } from '../../../core/services/pedido.service';
import { AuthService } from '../../../core/services/auth-service';
import { LanguageService } from '../../../core/services/language.service';
import { localizeText } from '../../../core/i18n/localize';
import { getUiStrings } from '../../../core/i18n/ui-strings';
import type { MetodoPago } from '../../../core/models';

@Component({
  selector: 'app-checkout',
  standalone: true,
  imports: [CurrencyPipe, TablerIconComponent, RouterLink],
  templateUrl: './checkout.html',
})
export class CheckoutPage implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly cartService = inject(CartService);
  private readonly paymentService = inject(PaymentService);
  private readonly pedidoService = inject(PedidoService);
  private readonly langService = inject(LanguageService);
  readonly auth = inject(AuthService);
  protected readonly t = computed(() => getUiStrings(this.langService.currentLang()));

  localId = '';
  localNombre = '';
  orderItems: CartItem[] = [];
  orderTotal = 0;

  // Método seleccionado por el usuario (por defecto: tarjeta online)
  selectedMethod: MetodoPago = 'stripe';

  isLoading = false;
  paymentError: string | null = null;
  needsAuth = false;

  ngOnInit(): void {
    this.localId = this.route.snapshot.paramMap.get('id') ?? '';

    if (this.cartService.isEmpty()) {
      this.router.navigate(['/home/locales', this.localId, 'reserva']);
      return;
    }

    this.localNombre = this.cartService.localNombre || this.localId;
    this.orderItems = [...this.cartService.items()];
    this.orderTotal = this.cartService.totalPrice();
  }

  getInitials(name: string): string {
    const words = name
      .replace(/\(.*?\)/g, '')
      .trim()
      .split(/\s+/)
      .filter((w) => w.length > 2);
    return ((words[0]?.[0] ?? name[0] ?? '') + (words[1]?.[0] ?? '')).toUpperCase();
  }

  getCartItemName(item: CartItem): string {
    return localizeText(
      item.nombre,
      { en: item.nombreEn, fr: item.nombreFr, it: item.nombreIt, de: item.nombreDe },
      this.langService.currentLang(),
    );
  }

  selectMethod(method: MetodoPago): void {
    this.selectedMethod = method;
    this.paymentError = null;
    this.needsAuth = false;
  }

  confirmarPedido(): void {
    if (!this.validateOrder()) return;

    if (this.selectedMethod === 'stripe') {
      this.pagarConStripe();
    } else {
      this.pagarEnLocal();
    }
  }

  volverAReserva(): void {
    this.router.navigate(['/home/locales', this.localId, 'reserva']);
  }

  private pagarConStripe(): void {
    if (this.isLoading || this.orderItems.length === 0) return;

    this.needsAuth = false;
    this.paymentError = null;

    if (!this.auth.isLoggedIn()) {
      this.needsAuth = true;
      return;
    }

    const localIdNum = this.cartService.localNumericId;
    if (!localIdNum || localIdNum <= 0) {
      this.paymentError = 'No se ha podido identificar el local seleccionado.';
      return;
    }

    this.isLoading = true;

    const origin = window.location.origin;
    const basePath = `/home/locales/${this.localId}/reserva/pago`;

    this.paymentService
      .createCheckoutSession({
        localId: localIdNum,
        type: 'takeaway',
        paymentMethod: 'stripe',
        items: this.orderItems.map((item) => ({
          id: item.id,
          nombre: item.nombre,
          precio: item.precio,
          cantidad: item.cantidad,
        })),
        successUrl: `${origin}${basePath}/success`,
        cancelUrl: `${origin}${basePath}/cancel`,
      })
      .subscribe({
        next: ({ checkoutUrl }) => {
          if (checkoutUrl) {
            window.location.href = checkoutUrl;
          } else {
            this.isLoading = false;
            this.paymentError = 'Respuesta inválida del servidor. Inténtalo de nuevo.';
          }
        },
        error: (err) => {
          this.isLoading = false;

          if (err?.status === 401) {
            this.needsAuth = true;
          } else if (err?.error) {
            this.paymentError = this.readableError(err.error);
          } else if (err?.status === 0) {
            this.paymentError = this.t().checkout.errorConnect;
          } else {
            this.paymentError = this.t().checkout.errorGeneric;
          }
        },
      });
  }

  private pagarEnLocal(): void {
    if (this.isLoading || this.orderItems.length === 0) return;

    this.needsAuth = false;
    this.paymentError = null;

    if (!this.auth.isLoggedIn()) {
      this.needsAuth = true;
      return;
    }

    const localIdNum = this.cartService.localNumericId;
    if (!localIdNum || localIdNum <= 0) {
      this.paymentError = 'Error: local no válido.';
      return;
    }

    this.isLoading = true;

    this.pedidoService
      .crearPedido({
        localId: localIdNum,
        type: 'takeaway',
        lines: this.orderItems.map((item) => ({
          productId: item.id,
          quantity: item.cantidad,
        })),
        paymentMethod: 'pay_at_store',
      })
      .subscribe({
        next: () => {
          this.cartService.clear();
          this.router.navigate(
            ['/home/locales', this.localId, 'reserva', 'pago', 'success'],
            { queryParams: { via: 'local' } },
          );
        },
        error: (err) => {
          this.isLoading = false;

          if (err?.status === 401) {
            this.needsAuth = true;
          } else if (err?.error) {
            this.paymentError = this.readableError(err.error);
          } else if (err?.status === 0) {
            this.paymentError = 'No se pudo conectar con el servidor. Inténtalo más tarde.';
          } else {
            this.paymentError = this.t().checkout.errorGeneric;
          }
        },
      });
  }

  private validateOrder(): boolean {
    this.paymentError = null;

    if (this.orderItems.length === 0) {
      this.paymentError = 'El carrito está vacío.';
      return false;
    }

    if (this.orderItems.some((item) => !Number.isInteger(item.id) || item.id <= 0 || !Number.isInteger(item.cantidad) || item.cantidad <= 0)) {
      this.paymentError = 'El pedido contiene artículos no válidos.';
      return false;
    }

    return true;
  }

  private readableError(error: unknown): string {
    if (!error || typeof error !== 'object') return this.t().checkout.errorGeneric;

    const response = error as {
      message?: unknown;
      code?: unknown;
      errors?: Record<string, unknown>;
      error?: { details?: Record<string, unknown> };
    };
    let details = response.errors ?? response.error?.details ?? {};

    if (Object.keys(details).length === 0 && typeof response.message === 'string') {
      try {
        const decoded = JSON.parse(response.message) as unknown;
        if (decoded && typeof decoded === 'object' && !Array.isArray(decoded)) {
          details = decoded as Record<string, unknown>;
        }
      } catch {
        // El mensaje no es JSON: se tratará como texto legible más abajo.
      }
    }
    const messages: string[] = [];

    if ('type' in details) messages.push('Falta el tipo de pedido.');
    if ('lines' in details) messages.push('El carrito está vacío.');
    if ('paymentMethod' in details) messages.push('Selecciona un método de pago válido.');

    if (messages.length > 0) return messages.join(' ');
    if (response.code === 'INTERNAL_ERROR') {
      return 'No se pudo crear el pedido. Inténtalo de nuevo en unos minutos.';
    }
    if (typeof response.message === 'string' && !response.message.trim().startsWith('{')) {
      return response.message;
    }

    return this.t().checkout.errorGeneric;
  }
}
