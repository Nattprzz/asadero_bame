// ─────────────────────────────────────────────────────────────────────────────
// payment.service.ts — integración con Stripe.
//
// Este servicio se encarga de comunicarse con el backend para crear una sesión
// de pago en Stripe. El frontend únicamente envía los datos del pedido, mientras
// que el servidor valida precios, stock y genera la sesión segura.
// ─────────────────────────────────────────────────────────────────────────────

import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, type Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

// Producto incluido en el proceso de pago.
export interface CheckoutItem {
  id: number;
  nombre: string;
  precio: number;
  cantidad: number;
}

// Datos necesarios para solicitar la creación de una sesión de pago.
export interface CreateCheckoutSessionPayload {
  localId: number;
  type: 'takeaway' | 'delivery';
  paymentMethod: 'stripe';

  // IMPORTANTE: el backend DEBE recalcular el total desde su propia base de datos.
  // Solo se envían items para que Stripe genere las líneas; el backend valida precios.
  items: CheckoutItem[];

  successUrl: string;
  cancelUrl: string;
}

// Respuesta devuelta por el backend con la URL de Stripe Checkout.
export interface CreateCheckoutSessionResponse {
  checkoutUrl: string;
}

@Injectable({ providedIn: 'root' })
export class PaymentService {
  // Cliente HTTP utilizado para comunicarse con la API.
  private readonly http = inject(HttpClient);

  // Endpoint encargado de generar la sesión de Stripe.
  //
  // Desarrollo:
  // http://localhost:8000/api/payments/stripe/create-checkout-session
  //
  // Producción:
  // /api/payments/stripe/create-checkout-session
  // (gestionado mediante proxy inverso en Nginx)
  private readonly API_URL =
    `${environment.apiUrl.replace(/\/$/, '')}/api/payments/stripe/create-checkout-session`;

  // Solicita al backend la creación de una sesión de pago y devuelve
  // la URL de Stripe a la que será redirigido el usuario.
  createCheckoutSession(
    payload: CreateCheckoutSessionPayload,
  ): Observable<CreateCheckoutSessionResponse> {
    return this.http.post<{ success: boolean; data: CreateCheckoutSessionResponse }>(
      this.API_URL,
      payload,
    ).pipe(
      map((res) => res.data),
    );
  }
}
