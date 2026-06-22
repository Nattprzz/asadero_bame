import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, type Observable } from 'rxjs';
import { environment } from '../../../environments/environment';

export interface AdminProducto {
  id: number;
  nombre: string;
  foto?: string | null;
  disponible: boolean;
  stock?: number | null;
  precio?: number | null;
  categoria?: string | null;
}

interface BackendAdminProducto {
  id: number;
  name?: string;
  nombre?: string;
  imagePath?: string | null;
  foto?: string | null;
  available?: boolean;
  disponible?: boolean;
  stock?: number | null;
  price?: number | null;
  precio?: number | null;
  category?: { name?: string; nombre?: string } | null;
}

@Injectable({ providedIn: 'root' })
export class AdminProductService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl.replace(/\/$/, '')}/api/v1/admin`;

  getProductos(localId: string | number): Observable<AdminProducto[]> {
    return this.http
      .get<{ success: boolean; data: BackendAdminProducto[] }>(`${this.base}/productos?localId=${localId}`)
      .pipe(map((res) => res.data.map((row) => this.mapProducto(row))));
  }

  actualizarDisponibilidad(
    localId: string | number,
    productoId: number,
    disponible: boolean,
  ): Observable<AdminProducto> {
    return this.http
      .patch<{ success: boolean; data: BackendAdminProducto }>(
        `${this.base}/products/${productoId}/availability`,
        { disponible, localId },
      )
      .pipe(map((res) => this.mapProducto(res.data)));
  }

  actualizarStock(
    localId: string | number,
    productoId: number,
    stock: number,
  ): Observable<AdminProducto> {
    return this.http
      .patch<{ success: boolean; data: BackendAdminProducto }>(
        `${this.base}/products/${productoId}/stock`,
        { stock, localId },
      )
      .pipe(map((res) => this.mapProducto(res.data)));
  }

  private mapProducto(row: BackendAdminProducto): AdminProducto {
    return {
      id: row.id,
      nombre: row.nombre ?? row.name ?? `Producto ${row.id}`,
      foto: row.foto ?? row.imagePath ?? null,
      disponible: row.disponible ?? row.available ?? false,
      stock: row.stock ?? null,
      precio: row.precio ?? row.price ?? null,
      categoria: row.category?.nombre ?? row.category?.name ?? null,
    };
  }
}
