import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, type Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import type { Alergeno, Categoria, Producto, ProductosPorCategoria } from '../models';

interface BackendAllergen {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  iconUrl: string | null;
}

interface BackendCategory {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  sortOrder: number;
  active: boolean;
}

interface BackendProduct {
  id: number;
  categoryId: number;
  category: BackendCategory;
  name: string;
  slug: string;
  description: string;
  price: number;
  available: boolean;
  availability: string | null;
  featured: boolean;
  weight: string | null;
  prepTime: number | null;
  imagePath: string | null;
  allergens: BackendAllergen[];
}

interface ApiResponse<T> {
  success: boolean;
  data: T;
}

@Injectable({ providedIn: 'root' })
export class ProductService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl.replace(/\/$/, '')}/api/v1`;

  getProducts(): Observable<Producto[]> {
    return this.http
      .get<ApiResponse<BackendProduct[]>>(`${this.base}/productos`)
      .pipe(map((res) => res.data.map((row) => this.mapProduct(row))));
  }

  getProductsGroupedByCategory(): Observable<ProductosPorCategoria[]> {
    return this.http
      .get<ApiResponse<BackendProduct[]>>(`${this.base}/productos`)
      .pipe(
        map((res) => {
          const rows = res.data;
          const categoryMap = new Map<number, Categoria>();
          for (const row of rows) {
            if (!categoryMap.has(row.categoryId)) {
              categoryMap.set(row.categoryId, this.mapCategory(row.category));
            }
          }
          const products = rows.map((row) => this.mapProduct(row));
          return this.groupByCategory(categoryMap, products);
        }),
      );
  }

  private mapProduct(row: BackendProduct): Producto {
    const allergens = (row.allergens ?? []).map((a) => this.mapAllergen(a));
    return {
      id: row.id,
      nombre: row.name,
      nombreEn: null,
      nombreFr: null,
      nombreIt: null,
      nombreDe: null,
      descripcion: row.description ?? '',
      descripcionEn: null,
      descripcionFr: null,
      descripcionIt: null,
      descripcionDe: null,
      precio: Number(row.price),
      foto: row.imagePath ?? '',
      categoriaId: row.categoryId,
      alergenos: allergens.map((a) => a.slug),
      alergenosDetalle: allergens,
      trazas: [],
      disponible: row.available,
      disponibilidad: row.availability ?? undefined,
      destacado: row.featured ?? false,
      peso: row.weight,
      tiempoPreparacion: row.prepTime,
      cantidad: 0,
    };
  }

  private mapAllergen(a: BackendAllergen): Alergeno {
    return {
      id: a.id,
      nombre: a.name,
      slug: a.slug,
      descripcion: a.description,
      icono: a.iconUrl,
    };
  }

  private mapCategory(c: BackendCategory): Categoria {
    return {
      id: c.id,
      nombre: c.name,
      slug: c.slug,
      descripcion: c.description,
      imagen: null,
      activa: c.active,
      orden: c.sortOrder,
    };
  }

  private groupByCategory(
    categoryMap: Map<number, Categoria>,
    products: Producto[],
  ): ProductosPorCategoria[] {
    const order = [...categoryMap.keys()];
    return order
      .map((catId) => ({
        categoria: categoryMap.get(catId)!,
        productos: products.filter((p) => p.categoriaId === catId),
      }))
      .filter((group) => group.productos.length > 0);
  }
}
