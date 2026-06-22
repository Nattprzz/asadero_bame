// ─────────────────────────────────────────────────────────────────────────────
// allergen.service.ts — servicio de alérgenos.
//
// Usa el backend como fuente de verdad para evitar lecturas directas desde
// Supabase en el navegador.
// ─────────────────────────────────────────────────────────────────────────────

import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import type { Alergeno } from '../models';

type BackendAllergen = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  iconUrl: string | null;
};

type BackendProduct = {
  id: number;
  allergens?: BackendAllergen[];
};

interface ApiResponse<T> {
  success: boolean;
  data: T;
}

@Injectable({ providedIn: 'root' })
export class AllergenService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl.replace(/\/$/, '')}/api/v1`;

  // Devuelve todos los alérgenos ordenados y adaptados al modelo del frontend.
  getAllergens(): Observable<Alergeno[]> {
    return this.http
      .get<ApiResponse<BackendAllergen[]>>(`${this.base}/allergens`)
      .pipe(map((res) => res.data.map((row) => this.mapAllergen(row))));
  }

  // Obtiene los alérgenos directos agrupados por producto.
  getDirectAllergensByProduct(productIds: number[]): Observable<Map<number, Alergeno[]>> {
    return this.loadAllergensByProduct(productIds);
  }

  // El backend actual no expone trazas de alérgenos separadas.
  // Se devuelve un mapa vacío para mantener compatibilidad con la API del servicio.
  getTracesByProduct(productIds: number[]): Observable<Map<number, Alergeno[]>> {
    void productIds;
    return new Observable((subscriber) => {
      subscriber.next(new Map<number, Alergeno[]>());
      subscriber.complete();
    });
  }

  private loadAllergensByProduct(productIds: number[]): Observable<Map<number, Alergeno[]>> {
    return this.http
      .get<ApiResponse<BackendProduct[]>>(`${this.base}/productos`)
      .pipe(
        map((res) => {
          const grouped = new Map<number, Alergeno[]>();
          if (productIds.length === 0) {
            return grouped;
          }

          const allowed = new Set(productIds);

          for (const product of res.data) {
            if (!allowed.has(product.id)) continue;
            const allergens = (product.allergens ?? []).map((row) => this.mapAllergen(row));
            if (allergens.length > 0) {
              grouped.set(product.id, allergens);
            }
          }

          return grouped;
        }),
      );
  }

  private mapAllergen(row: BackendAllergen): Alergeno {
    return {
      id: row.id,
      nombre: row.name ?? '',
      slug: row.slug ?? '',
      descripcion: row.description,
      icono: row.iconUrl ?? null,
    };
  }
}
