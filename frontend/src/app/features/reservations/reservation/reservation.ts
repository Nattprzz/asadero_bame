import {
  Component,
  HostListener,
  OnDestroy,
  OnInit,
  inject,
  signal,
} from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { forkJoin } from 'rxjs';
import { LiveAnnouncer } from '@angular/cdk/a11y';
import { LocalService } from '../../../core/services/local.service';
import { ProductService } from '../../../core/services/product.service';
import { CartService } from '../../../core/services/cart.service';
import { LanguageService } from '../../../core/services/language.service';
import { localizeText } from '../../../core/i18n/localize';
import type { Alergeno, Local, Producto, ProductosPorCategoria } from '../../../core/models';

interface Product extends Producto {
  precioOriginal: number;
  imagen?: string;
  cantidad: number;
}

interface RailCat {
  id: string;
  label: string;
  count: number;
}

@Component({
  selector: 'app-reservation',
  standalone: true,
  imports: [TablerIconComponent],
  templateUrl: './reservation.html',
})
export class Reservation implements OnInit, OnDestroy {
  private readonly liveAnnouncer = inject(LiveAnnouncer);
  private readonly route = inject(ActivatedRoute);
  private readonly router = inject(Router);
  private readonly localService = inject(LocalService);
  private readonly productService = inject(ProductService);
  private readonly langService = inject(LanguageService);
  protected readonly cart = inject(CartService);

  readonly MAX_QUANTITY = 10;

  localData = {
    nombre: 'Asador BAME',
    ubicacion: '',
    horarios: [] as string[],
  };

  groupedProducts: Array<{
    categoria: ProductosPorCategoria['categoria'];
    productos: Product[];
  }> = [];

  products: Product[] = [];
  loading = true;
  error: string | null = null;

  readonly query = signal('');
  readonly activeCat = signal('destacados');
  readonly detailProduct = signal<Product | null>(null);
  readonly detailQty = signal(1);
  readonly cartOpen = signal(false);
  readonly isMobile = signal(false);
  readonly toastMsg = signal<string | null>(null);

  private toastTimer?: ReturnType<typeof setTimeout>;

  readonly skeletons = [1, 2, 3, 4, 5, 6];

  private readonly ALLERGEN_ABBR: Record<string, string> = {
    gluten: 'GLU',
    huevos: 'HUE',
    huevo: 'HUE',
    lacteos: 'LAC',
    lácteos: 'LAC',
    pescado: 'PES',
    soja: 'SOJ',
    'frutos-cascara': 'FRU',
    frutos_cascara: 'FRU',
    sesamo: 'SES',
    sésamo: 'SES',
    sulfitos: 'SUL',
    mostaza: 'MOS',
    apio: 'API',
    altramuces: 'ALT',
    moluscos: 'MOL',
    crustaceos: 'CRU',
    cacahuetes: 'CAC',
  };

  // ── Computed getters ─────────────────────────────────────────────────────────

  get railCats(): RailCat[] {
    const featured = this.products.filter((p) => p.destacado);
    const cats: RailCat[] = [
      { id: 'destacados', label: 'Destacados', count: featured.length },
    ];
    for (const g of this.groupedProducts) {
      cats.push({
        id: String(g.categoria.id),
        label: this.getCategoryDisplayName(g.categoria),
        count: g.productos.length,
      });
    }
    return cats;
  }

  get filteredItems(): Product[] {
    const q = this.query().trim().toLowerCase();
    if (q) {
      return this.products.filter((p) =>
        (this.getProductDisplayName(p) + ' ' + this.getProductDisplayDescription(p))
          .toLowerCase()
          .includes(q),
      );
    }
    const cat = this.activeCat();
    if (cat === 'destacados') return this.products.filter((p) => p.destacado);
    const group = this.groupedProducts.find((g) => String(g.categoria.id) === cat);
    return group?.productos ?? [];
  }

  get sectionTitle(): string {
    if (this.query().trim()) return 'Resultados';
    const cat = this.activeCat();
    if (cat === 'destacados') return 'Destacados';
    const g = this.groupedProducts.find((g) => String(g.categoria.id) === cat);
    return g ? this.getCategoryDisplayName(g.categoria) : '';
  }

  get sectionCount(): string {
    const n = this.filteredItems.length;
    return n + (n === 1 ? ' plato' : ' platos');
  }

  get isDesktop(): boolean {
    return !this.isMobile();
  }

  get showBottomBar(): boolean {
    return this.isMobile() && !this.cart.isEmpty() && !this.cartOpen() && !this.detailProduct();
  }

  get detailAddLabel(): string {
    const p = this.detailProduct();
    if (!p) return '';
    return 'Añadir · ' + this.moneyStr(p.precio * this.detailQty());
  }

  get cartItems(): Array<{ id: number; name: string; ini: string; qty: number; lineStr: string }> {
    return this.cart.items().map((item) => ({
      id: item.id,
      name: item.nombre,
      ini: this.getInitials(item.nombre),
      qty: item.cantidad,
      lineStr: this.moneyStr(item.precio * item.cantidad),
    }));
  }

  get subtotalStr(): string {
    return this.moneyStr(this.cart.totalPrice());
  }

  get totalStr(): string {
    return this.moneyStr(this.cart.totalPrice());
  }

  get cartCount(): number {
    return this.cart.totalItems();
  }

  // ── Formatting helpers ───────────────────────────────────────────────────────

  moneyStr(amount: number): string {
    return '€' + amount.toFixed(2).replace('.', ',');
  }

  allergenAbbr(allergen: Alergeno): string {
    const key = (allergen.slug ?? allergen.nombre ?? '').toLowerCase();
    return this.ALLERGEN_ABBR[key] ?? allergen.slug.slice(0, 3).toUpperCase();
  }

  allergenTitle(allergen: Alergeno): string {
    return allergen.nombre || allergen.slug;
  }

  getInitials(name: string): string {
    const words = name
      .replace(/\(.*?\)/g, '')
      .trim()
      .split(/\s+/)
      .filter((w) => w.length > 2);
    return ((words[0]?.[0] ?? name[0] ?? '') + (words[1]?.[0] ?? '')).toUpperCase();
  }

  // ── Category & search ────────────────────────────────────────────────────────

  setActiveCat(catId: string): void {
    this.activeCat.set(catId);
    this.query.set('');
  }

  setQuery(value: string): void {
    this.query.set(value);
  }

  clearQuery(): void {
    this.query.set('');
  }

  // ── Detail modal ─────────────────────────────────────────────────────────────

  openDetail(product: Product): void {
    this.detailProduct.set(product);
    this.detailQty.set(1);
  }

  closeDetail(): void {
    this.detailProduct.set(null);
  }

  incDetailQty(): void {
    this.detailQty.update((q) => q + 1);
  }

  decDetailQty(): void {
    this.detailQty.update((q) => Math.max(1, q - 1));
  }

  addFromDetail(): void {
    const p = this.detailProduct();
    if (!p) return;
    const qty = this.detailQty();
    this.cart.update(
      {
        id: p.id,
        nombre: p.nombre,
        nombreEn: p.nombreEn,
        nombreFr: p.nombreFr,
        nombreIt: p.nombreIt,
        nombreDe: p.nombreDe,
        precio: p.precio,
        imagen: p.imagen,
      },
      qty,
    );
    p.cantidad = this.cart.getQuantity(p.id);
    this.showToastMsg(this.getProductDisplayName(p));
    this.closeDetail();
  }

  // ── Mobile cart sheet ────────────────────────────────────────────────────────

  openCart(): void {
    this.cartOpen.set(true);
  }

  closeCart(): void {
    this.cartOpen.set(false);
  }

  // ── Toast ────────────────────────────────────────────────────────────────────

  showToastMsg(name: string): void {
    clearTimeout(this.toastTimer);
    this.toastMsg.set(name + ' añadido');
    this.toastTimer = setTimeout(() => this.toastMsg.set(null), 1600);
  }

  // ── Cart item controls ───────────────────────────────────────────────────────

  cartItemInc(itemId: number): void {
    const cartItem = this.cart.items().find((i) => i.id === itemId);
    if (cartItem) this.cart.update(cartItem, 1);
    const p = this.products.find((pr) => pr.id === itemId);
    if (p) p.cantidad = this.cart.getQuantity(itemId);
  }

  cartItemDec(itemId: number): void {
    const cartItem = this.cart.items().find((i) => i.id === itemId);
    if (cartItem) this.cart.update(cartItem, -1);
    const p = this.products.find((pr) => pr.id === itemId);
    if (p) p.cantidad = this.cart.getQuantity(itemId);
  }

  // ── Product quantity (on card) ───────────────────────────────────────────────

  updateQuantity(product: Product, change: number, event?: MouseEvent): void {
    event?.stopPropagation();
    const newQty = product.cantidad + change;
    if (newQty < 0 || newQty > this.effectiveMax(product)) return;
    product.cantidad = newQty;
    this.cart.update(
      {
        id: product.id,
        nombre: product.nombre,
        nombreEn: product.nombreEn,
        nombreFr: product.nombreFr,
        nombreIt: product.nombreIt,
        nombreDe: product.nombreDe,
        precio: product.precio,
        imagen: product.imagen,
      },
      change,
    );
    this.liveAnnouncer.announce(
      `${this.getProductDisplayName(product)}: ${product.cantidad} ${
        product.cantidad === 1 ? 'unidad' : 'unidades'
      }. Total: ${this.cart.totalItems()} productos.`,
      'polite',
    );
    if (change > 0) {
      this.showToastMsg(this.getProductDisplayName(product));
    }
  }

  effectiveMax(product: Product): number {
    const stockMax = product.stock != null ? product.stock : this.MAX_QUANTITY;
    return Math.min(stockMax, this.MAX_QUANTITY);
  }

  goToPayment(): void {
    if (this.cart.isEmpty()) return;
    const localId = this.route.snapshot.paramMap.get('id') ?? '';
    this.router.navigate(['/home/locales', localId, 'reserva', 'pago']);
    this.closeCart();
  }

  // ── i18n helpers ─────────────────────────────────────────────────────────────

  getProductDisplayName(product: Producto): string {
    return localizeText(
      product.nombre,
      { en: product.nombreEn, fr: product.nombreFr, it: product.nombreIt, de: product.nombreDe },
      this.langService.currentLang(),
    );
  }

  getProductDisplayDescription(product: Producto): string {
    return localizeText(
      product.descripcion,
      {
        en: product.descripcionEn,
        fr: product.descripcionFr,
        it: product.descripcionIt,
        de: product.descripcionDe,
      },
      this.langService.currentLang(),
    );
  }

  getCategoryDisplayName(cat: ProductosPorCategoria['categoria']): string {
    return localizeText(
      cat.nombre,
      { en: cat.nombreEn, fr: cat.nombreFr, it: cat.nombreIt, de: cat.nombreDe },
      this.langService.currentLang(),
    );
  }

  // ── Lifecycle ─────────────────────────────────────────────────────────────────

  @HostListener('window:resize')
  onResize(): void {
    this.isMobile.set(window.innerWidth < 900);
  }

  ngOnInit(): void {
    if (typeof window !== 'undefined') {
      this.isMobile.set(window.innerWidth < 900);
    }
    const localId = this.route.snapshot.paramMap.get('id') ?? '';
    this.cart.setLocal(localId);
    forkJoin({
      local: this.localService.getLocalByName(localId),
      groups: this.productService.getProductsGroupedByCategory(),
    }).subscribe({
      next: ({ local, groups }) => {
        if (local) {
          this.applyLocal(local);
          this.cart.setLocal(localId, local.nombre, local.id);
        }
        this.applyProducts(groups);
        this.loading = false;
      },
      error: (error) => {
        this.error = error instanceof Error ? error.message : 'No se pudo cargar la carta.';
        this.loading = false;
      },
    });
  }

  ngOnDestroy(): void {
    clearTimeout(this.toastTimer);
  }

  private applyLocal(local: Local): void {
    this.localData = {
      nombre: local.nombre,
      ubicacion: local.ubicacion,
      horarios: local.horarios?.length ? local.horarios : local.horario ? [local.horario] : [],
    };
  }

  private applyProducts(groups: ProductosPorCategoria[]): void {
    this.groupedProducts = groups.map((group) => ({
      categoria: group.categoria,
      productos: group.productos.map((product) => ({
        ...product,
        imagen: product.foto,
        precioOriginal: product.precio,
        cantidad: this.cart.getQuantity(product.id),
      })),
    }));
    this.products = this.groupedProducts.flatMap((group) => group.productos);
  }
}
