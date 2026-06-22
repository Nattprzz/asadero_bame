// ─────────────────────────────────────────────────────────────────────────────
// cart.service.ts — servicio del carrito.
//
// Gestiona los productos añadidos al carrito, el local seleccionado y los
// totales calculados para mostrar el resumen del pedido.
// ─────────────────────────────────────────────────────────────────────────────

import { Injectable, computed, signal } from '@angular/core';

export interface CartItem {
  id: number;
  nombre: string; // Nombre por defecto en español.
  nombreEn?: string | null;
  nombreFr?: string | null;
  nombreIt?: string | null;
  nombreDe?: string | null;
  precio: number;
  cantidad: number;
  imagen?: string;
}

type CartProductRef = Omit<CartItem, 'cantidad'>;

@Injectable({ providedIn: 'root' })
export class CartService {
  private readonly _items = signal<CartItem[]>([]);
  private _localId: string | null = null;
  private _localNombre = '';
  private _localNumericId: number | null = null;

  readonly items = this._items.asReadonly();

  // Calcula el número total de unidades del carrito.
  readonly totalItems = computed(() =>
    this._items().reduce((acc, i) => acc + i.cantidad, 0),
  );

  // Calcula el precio total del carrito.
  readonly totalPrice = computed(() =>
    this._items().reduce((acc, i) => acc + i.precio * i.cantidad, 0),
  );

  readonly isEmpty = computed(() => this._items().length === 0);

  get localId(): string | null {
    return this._localId;
  }

  get localNombre(): string {
    return this._localNombre;
  }

  get localNumericId(): number | null {
    return this._localNumericId;
  }

  // Cambia el local activo y limpia el carrito si se selecciona otro distinto.
  setLocal(localId: string, localNombre = '', numericId?: number): void {
    if (this._localId !== localId) {
      this._items.set([]);
      this._localId = localId;
    }

    this._localNombre = localNombre || localId;
    if (numericId !== undefined) {
      this._localNumericId = numericId;
    }
  }

  // Devuelve la cantidad actual de un producto dentro del carrito.
  getQuantity(productId: number): number {
    return this._items().find((i) => i.id === productId)?.cantidad ?? 0;
  }

  // Suma o resta unidades de un producto dentro del carrito.
  update(product: CartProductRef, delta: number): void {
    this._items.update((items) => {
      const idx = items.findIndex((i) => i.id === product.id);

      if (idx === -1) {
        if (delta <= 0) return items;

        return [...items, { ...product, cantidad: delta }];
      }

      const newQty = Math.max(0, items[idx].cantidad + delta);

      if (newQty === 0) {
        return items.filter((_, i) => i !== idx);
      }

      return items.map((item, i) =>
        i === idx ? { ...item, cantidad: newQty } : item,
      );
    });
  }

  // Vacía todos los productos del carrito.
  clear(): void {
    this._items.set([]);
  }
}