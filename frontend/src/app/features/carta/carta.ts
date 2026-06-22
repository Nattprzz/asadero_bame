import { Component, OnInit, inject, signal } from '@angular/core';
import { RouterLink } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { Meta, Title } from '@angular/platform-browser';
import { LanguageService } from '../../core/services/language.service';
import { ProductService } from '../../core/services/product.service';
import { localizeText } from '../../core/i18n/localize';
import type { Alergeno, Categoria, Producto, ProductosPorCategoria } from '../../core/models';

interface RailCat {
  id: string;
  label: string;
  count: number;
}

const ALLERGEN_ABBR: Record<string, string> = {
  gluten: 'GLU', huevos: 'HUE', huevo: 'HUE',
  lacteos: 'LAC', lácteos: 'LAC', pescado: 'PES',
  soja: 'SOJ', 'frutos-cascara': 'FRU', frutos_cascara: 'FRU',
  sesamo: 'SES', sésamo: 'SES', sulfitos: 'SUL',
  mostaza: 'MOS', apio: 'API', altramuces: 'ALT',
  moluscos: 'MOL', crustaceos: 'CRU', cacahuetes: 'CAC',
};

@Component({
  selector: 'app-carta',
  standalone: true,
  imports: [RouterLink, TablerIconComponent],
  templateUrl: './carta.html',
})
export class CartaPage implements OnInit {
  private readonly meta = inject(Meta);
  private readonly title = inject(Title);
  private readonly lang = inject(LanguageService);
  private readonly productService = inject(ProductService);

  readonly query = signal('');
  readonly activeCat = signal('todos');
  readonly loading = signal(true);
  readonly error = signal<string | null>(null);

  readonly skeletons = [1, 2, 3, 4, 5, 6];

  private groupedProducts: ProductosPorCategoria[] = [];
  private allProducts: Producto[] = [];

  ngOnInit(): void {
    this.title.setTitle('Carta | Bame — Asador de pollo en Murcia');
    this.meta.updateTag({ name: 'description', content: 'Consulta la carta completa de Asador BAME: pollos asados, croquetas, postres y más. Precios actualizados.' });
    this.meta.updateTag({ name: 'robots', content: 'index, follow' });

    this.productService.getProductsGroupedByCategory().subscribe({
      next: (groups) => {
        this.groupedProducts = groups;
        this.allProducts = groups.flatMap((g) => g.productos);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('No se pudo cargar la carta. Inténtalo de nuevo.');
        this.loading.set(false);
      },
    });
  }

  get railCats(): RailCat[] {
    const cats: RailCat[] = [{ id: 'todos', label: 'Todos', count: this.allProducts.length }];
    for (const g of this.groupedProducts) {
      cats.push({
        id: String(g.categoria.id),
        label: this.getCategoryName(g.categoria),
        count: g.productos.length,
      });
    }
    return cats;
  }

  get filteredItems(): Producto[] {
    const q = this.query().trim().toLowerCase();
    const source = q
      ? this.allProducts.filter((p) =>
          (this.getProductName(p) + ' ' + this.getProductDesc(p)).toLowerCase().includes(q),
        )
      : this.activeCat() === 'todos'
        ? this.allProducts
        : (this.groupedProducts.find((g) => String(g.categoria.id) === this.activeCat())?.productos ?? []);
    return source.filter((p) => p.disponible);
  }

  get sectionTitle(): string {
    if (this.query().trim()) return 'Resultados';
    if (this.activeCat() === 'todos') return 'Toda la carta';
    const g = this.groupedProducts.find((g) => String(g.categoria.id) === this.activeCat());
    return g ? this.getCategoryName(g.categoria) : '';
  }

  get sectionCount(): string {
    const n = this.filteredItems.length;
    return n + (n === 1 ? ' plato' : ' platos');
  }

  setActiveCat(id: string): void {
    this.activeCat.set(id);
    this.query.set('');
  }

  setQuery(event: Event): void {
    this.query.set((event.target as HTMLInputElement).value);
  }

  clearQuery(): void { this.query.set(''); }

  getProductName(p: Producto): string {
    return localizeText(p.nombre, { en: p.nombreEn, fr: p.nombreFr, it: p.nombreIt, de: p.nombreDe }, this.lang.currentLang());
  }

  getProductDesc(p: Producto): string {
    return localizeText(p.descripcion, { en: p.descripcionEn, fr: p.descripcionFr, it: p.descripcionIt, de: p.descripcionDe }, this.lang.currentLang());
  }

  getCategoryName(cat: Categoria): string {
    return localizeText(cat.nombre, { en: cat.nombreEn, fr: cat.nombreFr, it: cat.nombreIt, de: cat.nombreDe }, this.lang.currentLang());
  }

  moneyStr(n: number): string {
    return '€' + n.toFixed(2).replace('.', ',');
  }

  getInitials(name: string): string {
    const words = name.replace(/\(.*?\)/g, '').trim().split(/\s+/).filter((w) => w.length > 2);
    return ((words[0]?.[0] ?? name[0] ?? '') + (words[1]?.[0] ?? '')).toUpperCase();
  }

  allergenAbbr(al: Alergeno): string {
    const key = (al.slug ?? al.nombre ?? '').toLowerCase();
    return ALLERGEN_ABBR[key] ?? al.slug.slice(0, 3).toUpperCase();
  }

  allergenTitle(al: Alergeno): string {
    return al.nombre || al.slug;
  }
}
