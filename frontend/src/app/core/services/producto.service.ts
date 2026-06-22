import { Injectable, inject } from '@angular/core';
import { Observable, map, type Observable as ObservableType } from 'rxjs';
import type { Producto, Categoria } from '../models';
import { ProductService } from './product.service';

@Injectable({ providedIn: 'root' })
export class ProductoService {
  private readonly products = inject(ProductService);

  getProductos(localId?: number): ObservableType<Producto[]> {
    void localId;
    return this.products.getProducts();
  }

  getProducto(id: number): ObservableType<Producto | undefined> {
    return new Observable((subscriber) => {
      const subscription = this.products.getProducts().subscribe({
        next: (products) => {
          subscriber.next(products.find((p) => p.id === id));
          subscriber.complete();
        },
        error: (error) => subscriber.error(error),
      });
      return () => subscription.unsubscribe();
    });
  }

  getCategorias(): ObservableType<Categoria[]> {
    return this.products.getProductsGroupedByCategory().pipe(
      map((groups) => groups.map((g) => g.categoria)),
    );
  }
}
