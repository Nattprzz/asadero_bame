import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { CurrencyPipe, DatePipe } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { AuthService } from '../../../core/services/auth-service';
import { PedidoService } from '../../../core/services/pedido.service';
import { AdminProductService } from '../../../core/services/admin-product.service';
import { LocalService } from '../../../core/services/local.service';
import type { Pedido, EstadoPedido } from '../../../core/models';
import type { AdminProducto } from '../../../core/services/admin-product.service';
import { AccessibilityMenu } from '../../../shared/components/accessibility-menu/accessibility-menu';

type Tab = 'reservas' | 'productos';

@Component({
  selector: 'app-resumen-dia',
  standalone: true,
  imports: [CurrencyPipe, DatePipe, AccessibilityMenu],
  templateUrl: './resumen-dia.html',
  styleUrl: './resumen-dia.css',
})
export class ResumenDia implements OnInit {
  private readonly auth = inject(AuthService);
  private readonly pedidoService = inject(PedidoService);
  private readonly adminProductService = inject(AdminProductService);
  private readonly localService = inject(LocalService);
  private readonly route = inject(ActivatedRoute);

  readonly localId: number | null;
  readonly hoy = new Date();
  readonly fechaHoy = this.hoy.toLocaleDateString('sv');

  readonly pedidosLoading = signal(true);
  readonly productosLoading = signal(true);
  readonly pedidosError = signal<string | null>(null);
  readonly productosError = signal<string | null>(null);

  readonly pedidosHoy = signal<Pedido[]>([]);
  readonly productos = signal<AdminProducto[]>([]);
  readonly localNombre = signal<string | null>(null);

  readonly activeTab = signal<Tab>('reservas');
  readonly sidebarOpen = signal(false);
  readonly resFilter = signal<string>('all');
  readonly stockFilter = signal<string>('all');
  readonly query = signal('');
  readonly user = this.auth.user;
  readonly pageTitle = computed(() =>
    this.activeTab() === 'reservas' ? 'Reservas y pedidos' : 'Productos y stock',
  );
  readonly pageSubtitle = computed(() =>
    this.activeTab() === 'reservas'
      ? 'Pedidos de recogida del día'
      : 'Disponibilidad y existencias en tiempo real',
  );
  readonly userInitials = computed(() =>
    (this.user()?.name ?? 'Responsable')
      .split(/\s+/)
      .slice(0, 2)
      .map((part) => part.charAt(0).toUpperCase())
      .join(''),
  );

  // ── Reservas stats ───────────────────────────────────────────────────
  readonly totalPedidos = computed(() => this.pedidosHoy().length);
  readonly countPendientes = computed(() =>
    this.pedidosHoy().filter(p => p.estado === 'Pendiente' || p.estado === 'Confirmado').length,
  );
  readonly countPreparando = computed(() =>
    this.pedidosHoy().filter(p => p.estado === 'En preparación').length,
  );
  readonly entregadosList = computed(() =>
    this.pedidosHoy().filter(p => p.estado === 'Entregado'),
  );
  readonly totalRecaudado = computed(() =>
    this.entregadosList().reduce((sum, p) => sum + p.total, 0),
  );

  // ── Productos stats ──────────────────────────────────────────────────
  readonly countActivos = computed(() =>
    this.productos().filter(p => p.disponible && (p.stock == null || p.stock > 0)).length,
  );
  readonly countBajoStock = computed(() =>
    this.productos().filter(p => p.disponible && p.stock != null && p.stock > 0 && p.stock <= 5).length,
  );
  readonly countAgotados = computed(() =>
    this.productos().filter(p => !p.disponible || p.stock === 0).length,
  );

  // ── Filtered lists ───────────────────────────────────────────────────
  readonly filteredReservas = computed(() => {
    const f = this.resFilter();
    const all = this.pedidosHoy();
    if (f === 'all') return all;
    const map: Record<string, EstadoPedido[]> = {
      pendiente: ['Pendiente', 'Confirmado'],
      preparando: ['En preparación'],
      listo: ['Listo'],
    };
    return all.filter(p => (map[f] ?? []).includes(p.estado));
  });

  readonly filteredProductos = computed(() => {
    const q = this.query().toLowerCase().trim();
    const f = this.stockFilter();
    return this.productos()
      .filter(p => !q || p.nombre.toLowerCase().includes(q))
      .filter(p => {
        if (f === 'all') return true;
        if (f === 'low') return p.disponible && p.stock != null && p.stock > 0 && p.stock <= 5;
        if (f === 'out') return !p.disponible || p.stock === 0;
        return true;
      });
  });

  constructor() {
    const idParam = this.route.snapshot.paramMap.get('id');
    const routeId =
      idParam != null && !isNaN(Number(idParam)) && Number(idParam) > 0
        ? Number(idParam)
        : null;
    this.localId = routeId ?? this.auth.currentLocalId();
  }

  ngOnInit(): void {
    this.cargarNombreLocal();
    this.cargarDatos();
  }

  cargarDatos(): void {
    this.cargarPedidos();
    this.cargarProductos();
  }

  private cargarNombreLocal(): void {
    if (this.localId == null) return;
    this.localService.getLocal(this.localId).subscribe(local => {
      this.localNombre.set(local?.nombre ?? null);
    });
  }

  private cargarPedidos(): void {
    if (this.localId == null) {
      this.pedidosLoading.set(false);
      this.pedidosError.set('No hay local asociado a este usuario.');
      return;
    }
    this.pedidosLoading.set(true);
    this.pedidosError.set(null);
    this.pedidoService.getPedidosAdmin(this.localId).subscribe({
      next: todos => {
        this.pedidosHoy.set(todos.filter(p => p.fecha?.startsWith(this.fechaHoy)));
        this.pedidosLoading.set(false);
      },
      error: () => {
        this.pedidosError.set('No se pudieron cargar los pedidos de hoy.');
        this.pedidosLoading.set(false);
      },
    });
  }

  private cargarProductos(): void {
    if (this.localId == null) {
      this.productosLoading.set(false);
      return;
    }
    this.productosLoading.set(true);
    this.productosError.set(null);
    this.adminProductService.getProductos(this.localId).subscribe({
      next: data => {
        this.productos.set(data);
        this.productosLoading.set(false);
      },
      error: () => {
        this.productosError.set('No se pudo cargar el stock.');
        this.productosLoading.set(false);
      },
    });
  }

  setTab(tab: Tab): void {
    this.activeTab.set(tab);
    this.sidebarOpen.set(false);
  }

  toggleSidebar(): void {
    this.sidebarOpen.update((open) => !open);
  }

  closeSidebar(): void {
    this.sidebarOpen.set(false);
  }

  logout(): void {
    this.auth.logout();
  }

  setResFilter(f: string): void {
    this.resFilter.set(f);
  }

  setStockFilter(f: string): void {
    this.stockFilter.set(f);
  }

  setQuery(v: string): void {
    this.query.set(v);
  }

  pendientesBadge(): number {
    return this.pedidosHoy().filter(p =>
      ['Pendiente', 'Confirmado', 'En preparación'].includes(p.estado),
    ).length;
  }

  resFilterCount(f: string): number {
    if (f === 'all') return this.pedidosHoy().length;
    const map: Record<string, EstadoPedido[]> = {
      pendiente: ['Pendiente', 'Confirmado'],
      preparando: ['En preparación'],
      listo: ['Listo'],
    };
    return this.pedidosHoy().filter(p => (map[f] ?? []).includes(p.estado)).length;
  }

  stockFilterCount(f: string): number {
    if (f === 'all') return this.productos().length;
    if (f === 'low') return this.countBajoStock();
    if (f === 'out') return this.countAgotados();
    return 0;
  }

  estadoBadgeClasses(estado: EstadoPedido): string {
    const map: Record<EstadoPedido, string> = {
      Pendiente: 'bg-surface-muted text-text-muted border border-border-default',
      Confirmado: 'bg-surface-muted text-text-muted border border-border-default',
      'En preparación': 'bg-accent text-[#141210] border border-transparent',
      Listo: 'bg-success/15 text-success border border-success/30',
      Entregado: 'bg-transparent text-text-muted border border-border-default',
      Cancelado: 'bg-brand/10 text-brand border border-brand/20',
    };
    return map[estado] ?? '';
  }

  estadoLabel(estado: EstadoPedido): string {
    const map: Record<EstadoPedido, string> = {
      Pendiente: 'Pendiente',
      Confirmado: 'Confirmado',
      'En preparación': 'Preparando',
      Listo: 'Listo',
      Entregado: 'Entregado',
      Cancelado: 'Cancelado',
    };
    return map[estado] ?? estado;
  }

  pedidoItems(p: Pedido): string {
    return p.productos
      .map(l => (l.cantidad > 1 ? `${l.nombre} ×${l.cantidad}` : l.nombre))
      .join(' · ');
  }

  stockBarWidth(p: AdminProducto): string {
    if (!p.stock || p.stock <= 0 || !p.disponible) return '4%';
    return Math.min(100, Math.max(4, Math.round((p.stock / 30) * 100))) + '%';
  }

  stockBarColorClass(p: AdminProducto): string {
    if (!p.disponible || !p.stock || p.stock === 0) return 'bg-brand';
    if (p.stock <= 5) return 'bg-brand';
    if (p.stock <= 12) return 'bg-accent';
    return 'bg-success';
  }

  stockTextColorClass(p: AdminProducto): string {
    if (!p.disponible || !p.stock || p.stock === 0) return 'text-brand';
    if (p.stock <= 5) return 'text-brand';
    if (p.stock <= 12) return 'text-amber-500';
    return 'text-success';
  }

  stockLabel(p: AdminProducto): string {
    if (!p.disponible || !p.stock || p.stock <= 0) return '0';
    return String(p.stock);
  }
}
