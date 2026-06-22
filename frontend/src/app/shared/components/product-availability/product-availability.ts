import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { TablerIconComponent } from '@tabler/icons-angular';
import { ActivatedRoute } from '@angular/router';
import { forkJoin } from 'rxjs';
import { AdminProductService } from '@core/services/admin-product.service';

type AvailabilityFilter = 'todos' | 'disponibles' | 'agotados';

export interface Product {
  id: number;
  catId: number;
  catName: string;
  nombre: string;
  imagen?: string;
  disponibilidad: boolean;
  isUpdating: boolean;
  autoDisableThreshold: number | null;
}

interface ProductGroup {
  id: number;
  name: string;
  products: Product[];
}

@Component({
  selector: 'app-product-availability',
  standalone: true,
  imports: [FormsModule, TablerIconComponent],
  templateUrl: './product-availability.html',
})
export class ProductAvailability implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly adminProductService = inject(AdminProductService);

  localId = '';
  isLoading = true;
  loadError: string | null = null;

  readonly products = signal<Product[]>([]);
  readonly searchQuery = signal('');
  readonly activeFilter = signal<AvailabilityFilter>('todos');
  readonly activeCatId = signal<number | 'todos'>('todos');

  private readonly originalStates = new Map<number, boolean>();
  readonly pendingChanges = signal(new Set<number>());
  readonly hasPendingChanges = computed(() => this.pendingChanges().size > 0);
  readonly isSaving = signal(false);
  readonly saveNotification = signal<{ type: 'success' | 'error'; text: string } | null>(null);

  readonly categories = computed(() => {
    const seen = new Set<number>();
    const cats: { id: number; name: string }[] = [];
    for (const p of this.products()) {
      if (!seen.has(p.catId)) {
        seen.add(p.catId);
        cats.push({ id: p.catId, name: p.catName });
      }
    }
    return cats;
  });

  readonly visibleGroups = computed<ProductGroup[]>(() => {
    const catId = this.activeCatId();
    const query = this.searchQuery().toLowerCase().trim();
    const filter = this.activeFilter();

    let items = this.products().filter((p) => {
      const matchesQuery = !query || p.nombre.toLowerCase().includes(query);
      const matchesFilter =
        filter === 'todos' ||
        (filter === 'disponibles' && p.disponibilidad) ||
        (filter === 'agotados' && !p.disponibilidad);
      return matchesQuery && matchesFilter;
    });

    if (catId !== 'todos') {
      items = items.filter((p) => p.catId === catId);
    }

    const groupMap = new Map<number, ProductGroup>();
    for (const p of items) {
      if (!groupMap.has(p.catId)) {
        groupMap.set(p.catId, { id: p.catId, name: p.catName, products: [] });
      }
      groupMap.get(p.catId)!.products.push(p);
    }
    return [...groupMap.values()];
  });

  ngOnInit(): void {
    this.localId = this.route.snapshot.paramMap.get('id') ?? '';
    this.loadProducts();
  }

  private loadProducts(): void {
    this.isLoading = true;
    this.loadError = null;

    this.adminProductService.getProductos(this.localId).subscribe({
      next: (productos) => {
        const catIdMap = new Map<string, number>();
        let nextCatId = 1;

        const mapped = productos.map((p) => {
          const catName = p.categoria ?? 'Sin categoría';
          if (!catIdMap.has(catName)) catIdMap.set(catName, nextCatId++);
          return {
            id: p.id,
            catId: catIdMap.get(catName)!,
            catName,
            nombre: p.nombre,
            imagen: p.foto ?? undefined,
            disponibilidad: p.disponible,
            isUpdating: false,
            autoDisableThreshold: null,
          };
        });

        this.products.set(mapped);
        this.originalStates.clear();
        for (const p of mapped) this.originalStates.set(p.id, p.disponibilidad);
        this.pendingChanges.set(new Set());
        this.isLoading = false;
      },
      error: () => {
        this.loadError = 'No se pudieron cargar los productos. Inténtalo de nuevo.';
        this.isLoading = false;
      },
    });
  }

  retryLoad(): void {
    this.loadProducts();
  }

  setSearchQuery(value: string): void {
    this.searchQuery.set(value);
  }

  setFilter(filter: AvailabilityFilter): void {
    this.activeFilter.set(filter);
  }

  setCategory(catId: number | 'todos'): void {
    this.activeCatId.set(catId);
  }

  toggleLocalDisponibilidad(product: Product, value: boolean): void {
    this.products.update((products) =>
      products.map((p) => (p.id === product.id ? { ...p, disponibilidad: value } : p)),
    );

    const original = this.originalStates.get(product.id);
    this.pendingChanges.update((set) => {
      const next = new Set(set);
      if (original !== undefined && original !== value) {
        next.add(product.id);
      } else {
        next.delete(product.id);
      }
      return next;
    });
  }

  saveChanges(): void {
    const pending = this.pendingChanges();
    if (!pending.size || this.isSaving()) return;

    this.isSaving.set(true);
    this.saveNotification.set(null);

    const toSave = this.products().filter((p) => pending.has(p.id));

    this.products.update((products) =>
      products.map((p) => (pending.has(p.id) ? { ...p, isUpdating: true } : p)),
    );

    const saves = toSave.map((p) =>
      this.adminProductService.actualizarDisponibilidad(this.localId, p.id, p.disponibilidad),
    );

    forkJoin(saves).subscribe({
      next: () => {
        for (const p of toSave) {
          this.originalStates.set(p.id, p.disponibilidad);
        }
        this.pendingChanges.set(new Set());
        this.products.update((products) => products.map((p) => ({ ...p, isUpdating: false })));
        this.isSaving.set(false);
        this.saveNotification.set({ type: 'success', text: 'Cambios guardados correctamente' });
        setTimeout(() => this.saveNotification.set(null), 3000);
      },
      error: () => {
        this.products.update((products) => products.map((p) => ({ ...p, isUpdating: false })));
        this.isSaving.set(false);
        this.saveNotification.set({
          type: 'error',
          text: 'No se pudieron guardar los cambios. Inténtalo de nuevo.',
        });
      },
    });
  }

  guardarUmbral(product: Product, value: number | string | null): void {
    const threshold = this.normalizeThreshold(value);

    this.products.update((products) =>
      products.map((item) =>
        item.id === product.id ? { ...item, autoDisableThreshold: threshold } : item,
      ),
    );
  }

  private normalizeThreshold(value: number | string | null): number | null {
    if (value === null || value === '') return null;
    const parsed = Number(value);
    if (!Number.isFinite(parsed) || parsed < 0) return null;
    return parsed;
  }
}
