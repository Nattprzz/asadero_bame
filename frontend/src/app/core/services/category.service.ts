// ─────────────────────────────────────────────────────────────────────────────
// category.service.ts — servicio de categorías.
//
// Obtiene las categorías desde el backend para mantener una única fuente de
// verdad y evitar acceso directo desde el navegador a Supabase.
// ─────────────────────────────────────────────────────────────────────────────

import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, type Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import type { Categoria } from '../models';

type BackendCategory = {
  id: number;
  name: string;
  slug: string | null;
  description: string | null;
  imageUrl: string | null;
  active: boolean;
  sortOrder: number;
};

interface ApiResponse<T> {
  success: boolean;
  data: T;
}

@Injectable({ providedIn: 'root' })
export class CategoryService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl.replace(/\/$/, '')}/api/v1`;

  // Obtiene todas las categorías activas y las transforma al modelo del frontend.
  getCategories(): Observable<Categoria[]> {
    return this.http
      .get<ApiResponse<BackendCategory[]>>(`${this.base}/categories`)
      .pipe(map((res) => res.data.map((row) => this.mapCategory(row))));
  }

  // Convierte una fila del backend al modelo utilizado por Angular.
  private mapCategory(row: BackendCategory): Categoria {
    return {
      id: row.id,
      nombre: row.name ?? '',
      slug: row.slug ?? undefined,
      descripcion: row.description,
      imagen: row.imageUrl ?? null,
      activa: row.active,
      orden: row.sortOrder,
    };
  }
}
