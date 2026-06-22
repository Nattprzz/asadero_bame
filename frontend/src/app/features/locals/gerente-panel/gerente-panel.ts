import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { CurrencyPipe, DatePipe } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { TablerIconComponent } from '@tabler/icons-angular';
import { AuthService } from '../../../core/services/auth-service';
import { PedidoService } from '../../../core/services/pedido.service';
import { AdminProductService } from '../../../core/services/admin-product.service';
import { LocalService } from '../../../core/services/local.service';
import type { Pedido, EstadoPedido, HoursMap } from '../../../core/models';
import type { AdminProducto } from '../../../core/services/admin-product.service';
import { AccessibilityMenu } from '../../../shared/components/accessibility-menu/accessibility-menu';
import { forkJoin } from 'rxjs';

type Tab = 'reservas' | 'productos' | 'local';

interface DayHours {
  day: string;
  open: boolean;
  from: string;
  to: string;
}

const DAYS = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
const DAY_KEYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

@Component({
  selector: 'app-gerente-panel',
  standalone: true,
  imports: [CurrencyPipe, DatePipe, TablerIconComponent, AccessibilityMenu],
  templateUrl: './gerente-panel.html',
  styleUrl: './gerente-panel.css',
})
export class GerentePanel implements OnInit {
  private readonly auth = inject(AuthService);
  private readonly pedidoService = inject(PedidoService);
  private readonly adminProductService = inject(AdminProductService);
  private readonly localService = inject(LocalService);
  private readonly route = inject(ActivatedRoute);

  readonly localId: number | null;
  readonly hoy = new Date();
  readonly fechaHoy = this.hoy.toLocaleDateString('sv'); // "YYYY-MM-DD" en hora local

  // ── UI ────────────────────────────────────────────────────────────────
  readonly activeTab = signal<Tab>('reservas');
  readonly sidebarOpen = signal(false);
  readonly dirty = signal(false);
  readonly saving = signal(false);
  readonly toast = signal<string | null>(null);
  readonly user = this.auth.user;

  readonly pageTitle = computed(() => {
    const titles: Record<Tab, string> = {
      reservas: 'Gestión de reservas',
      productos: 'Gestión de productos',
      local: 'Configuración del local',
    };
    return titles[this.activeTab()];
  });

  readonly pageSubtitle = computed(() => {
    const subtitles: Record<Tab, string> = {
      reservas: 'Cambia el estado de cada pedido',
      productos: 'Edita el stock y la disponibilidad',
      local: 'Horarios, reservas e información',
    };
    return subtitles[this.activeTab()];
  });

  readonly userInitials = computed(() =>
    (this.user()?.name ?? 'Gerente')
      .split(/\s+/)
      .slice(0, 2)
      .map((part) => part.charAt(0).toUpperCase())
      .join(''),
  );

  // ── Reservas ──────────────────────────────────────────────────────────
  readonly pedidosLoading = signal(true);
  readonly pedidosError = signal<string | null>(null);
  readonly pedidosHoy = signal<Pedido[]>([]);
  readonly updatingId = signal<number | null>(null);
  readonly resFilter = signal<string>('all');

  readonly estadoOptions = [
    { value: 'pendiente', label: 'Pendiente' },
    { value: 'preparando', label: 'Preparando' },
    { value: 'listo', label: 'Listo' },
    { value: 'recogido', label: 'Recogido' },
    { value: 'cancelado', label: 'Cancelar' },
  ];

  // ── Productos ─────────────────────────────────────────────────────────
  readonly productosLoading = signal(true);
  readonly productosError = signal<string | null>(null);
  readonly productos = signal<AdminProducto[]>([]);
  readonly pendingStock = signal<Record<number, number>>({});
  readonly stockFilter = signal<string>('all');
  readonly query = signal('');

  // ── Local ─────────────────────────────────────────────────────────────
  readonly localLoading = signal(true);
  readonly localNombreField = signal('');
  readonly localUbicacionField = signal('');
  readonly localTelefonoField = signal('');
  readonly localIsOpen = signal(true);
  readonly openHours = signal<DayHours[]>(
    DAYS.map((d) => ({ day: d, open: true, from: '11:00', to: '23:00' })),
  );
  readonly resHours = signal<DayHours[]>(
    DAYS.map((d) => ({ day: d, open: true, from: '11:30', to: '22:30' })),
  );

  // ── Computed ──────────────────────────────────────────────────────────
  readonly resStats = computed(() => {
    const all = this.pedidosHoy();
    return [
      { label: 'Pedidos hoy', value: String(all.length), colorClass: 'text-text-primary' },
      {
        label: 'Pendientes',
        value: String(all.filter((p) => p.estado === 'Pendiente' || p.estado === 'Confirmado').length),
        colorClass: 'text-amber-500',
      },
      {
        label: 'Preparando',
        value: String(all.filter((p) => p.estado === 'En preparación').length),
        colorClass: 'text-brand',
      },
      {
        label: 'Facturación',
        value: '€' + all.filter((p) => p.estado === 'Entregado').reduce((s, p) => s + p.total, 0).toFixed(2).replace('.', ','),
        colorClass: 'text-success',
      },
    ];
  });

  readonly filteredReservas = computed(() => {
    const f = this.resFilter();
    const all = this.pedidosHoy();
    if (f === 'all') return all;
    const map: Record<string, EstadoPedido[]> = {
      pendiente: ['Pendiente', 'Confirmado'],
      preparando: ['En preparación'],
      listo: ['Listo'],
    };
    return all.filter((p) => (map[f] ?? []).includes(p.estado));
  });

  readonly filteredProductos = computed(() => {
    const q = this.query().toLowerCase().trim();
    const f = this.stockFilter();
    return this.productos()
      .filter((p) => !q || p.nombre.toLowerCase().includes(q))
      .filter((p) => {
        if (f === 'all') return true;
        const s = this.resolvedStock(p);
        if (f === 'low') return p.disponible && s > 0 && s <= 5;
        if (f === 'off') return !p.disponible || s === 0;
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
    this.cargarPedidos();
    this.cargarProductos();
    this.cargarLocal();
  }

  // ── Loaders ───────────────────────────────────────────────────────────

  private cargarPedidos(): void {
    if (this.localId == null) {
      this.pedidosLoading.set(false);
      this.pedidosError.set('No hay local asociado.');
      return;
    }
    this.pedidosLoading.set(true);
    this.pedidosError.set(null);
    this.pedidoService.getPedidosAdmin(this.localId).subscribe({
      next: (todos) => {
        this.pedidosHoy.set(todos.filter((p) => p.fecha?.startsWith(this.fechaHoy)));
        this.pedidosLoading.set(false);
      },
      error: () => {
        this.pedidosError.set('No se pudieron cargar los pedidos.');
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
      next: (data) => {
        this.productos.set(data);
        this.productosLoading.set(false);
      },
      error: () => {
        this.productosError.set('No se pudo cargar el stock.');
        this.productosLoading.set(false);
      },
    });
  }

  private cargarLocal(): void {
    if (this.localId == null) {
      this.localLoading.set(false);
      return;
    }
    this.localService.getLocal(this.localId).subscribe({
      next: (local) => {
        if (local) {
          this.localNombreField.set(local.nombre);
          this.localUbicacionField.set(local.ubicacion ?? '');
          this.localTelefonoField.set(local.telefono ?? '');
          this.localIsOpen.set(['1', '3', '4'].includes(local.estado));
          if (local.hours) this.openHours.set(this.rowsFromHours(local.hours));
          if (local.reservationHours) {
            this.resHours.set(this.rowsFromHours(local.reservationHours));
          }
        }
        this.localLoading.set(false);
      },
      error: () => {
        this.localLoading.set(false);
      },
    });
  }

  private rowsFromHours(hours: HoursMap): DayHours[] {
    return DAYS.map((d, i) => {
      const slots = hours[DAY_KEYS[i]] ?? [];
      return {
        day: d,
        open: slots.length > 0,
        from: slots[0]?.open ?? '11:00',
        to: slots[slots.length - 1]?.close ?? '23:00',
      };
    });
  }

  private hoursMap(rows: DayHours[]): HoursMap {
    return Object.fromEntries(
      rows.map((row, index) => [
        DAY_KEYS[index],
        row.open ? [{ open: row.from, close: row.to }] : [],
      ]),
    );
  }

  // ── Tab ───────────────────────────────────────────────────────────────
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

  // ── Reservas ──────────────────────────────────────────────────────────
  setResFilter(f: string): void {
    this.resFilter.set(f);
  }

  resFilterCount(f: string): number {
    if (f === 'all') return this.pedidosHoy().length;
    const map: Record<string, EstadoPedido[]> = {
      pendiente: ['Pendiente', 'Confirmado'],
      preparando: ['En preparación'],
      listo: ['Listo'],
    };
    return this.pedidosHoy().filter((p) => (map[f] ?? []).includes(p.estado)).length;
  }

  pendientesBadge(): number {
    return this.pedidosHoy().filter((p) =>
      ['Pendiente', 'Confirmado', 'En preparación'].includes(p.estado),
    ).length;
  }

  estadoApiToDesign(e: EstadoPedido): string {
    const map: Record<EstadoPedido, string> = {
      Pendiente: 'pendiente',
      Confirmado: 'pendiente',
      'En preparación': 'preparando',
      Listo: 'listo',
      Entregado: 'recogido',
      Cancelado: 'cancelado',
    };
    return map[e] ?? 'pendiente';
  }

  estadoDesignToApi(v: string): EstadoPedido {
    const map: Record<string, EstadoPedido> = {
      pendiente: 'Pendiente',
      preparando: 'En preparación',
      listo: 'Listo',
      recogido: 'Entregado',
      cancelado: 'Cancelado',
    };
    return map[v] ?? 'Pendiente';
  }

  selectBorderClass(e: EstadoPedido): string {
    const map: Record<EstadoPedido, string> = {
      Pendiente: 'border-l-text-muted',
      Confirmado: 'border-l-text-muted',
      'En preparación': 'border-l-accent',
      Listo: 'border-l-success',
      Entregado: 'border-l-border-default',
      Cancelado: 'border-l-brand',
    };
    return map[e] ?? 'border-l-text-muted';
  }

  onStatusChange(pedido: Pedido, event: Event): void {
    const val = (event.target as HTMLSelectElement).value;
    const nuevoEstado = this.estadoDesignToApi(val);
    this.updatingId.set(pedido.id);
    this.pedidoService.actualizarEstado(pedido.id, nuevoEstado).subscribe({
      next: (updated) => {
        this.pedidosHoy.update((list) =>
          list.map((p) => (p.id === updated.id ? { ...p, estado: updated.estado } : p)),
        );
        this.updatingId.set(null);
      },
      error: () => {
        this.updatingId.set(null);
      },
    });
  }

  pedidoItems(p: Pedido): string {
    return p.productos
      .map((l) => (l.cantidad > 1 ? `${l.nombre} ×${l.cantidad}` : l.nombre))
      .join(' · ');
  }

  // ── Productos ─────────────────────────────────────────────────────────
  setStockFilter(f: string): void {
    this.stockFilter.set(f);
  }

  setQuery(v: string): void {
    this.query.set(v);
  }

  stockFilterCount(f: string): number {
    if (f === 'all') return this.productos().length;
    if (f === 'low') {
      return this.productos().filter((p) => {
        const s = this.resolvedStock(p);
        return p.disponible && s > 0 && s <= 5;
      }).length;
    }
    if (f === 'off') {
      return this.productos().filter((p) => !p.disponible || this.resolvedStock(p) === 0).length;
    }
    return 0;
  }

  resolvedStock(p: AdminProducto): number {
    const pending = this.pendingStock();
    return pending[p.id] !== undefined ? pending[p.id] : (p.stock ?? 0);
  }

  stockBarWidth(p: AdminProducto): string {
    const s = this.resolvedStock(p);
    if (s <= 0) return '4%';
    return Math.min(100, Math.max(4, Math.round((s / 30) * 100))) + '%';
  }

  stockBarColorClass(p: AdminProducto): string {
    const s = this.resolvedStock(p);
    if (!p.disponible || s === 0) return 'bg-brand';
    if (s <= 5) return 'bg-brand';
    if (s <= 12) return 'bg-accent';
    return 'bg-success';
  }

  stockTextClass(p: AdminProducto): string {
    const s = this.resolvedStock(p);
    if (!p.disponible || s === 0) return 'text-brand';
    if (s <= 5) return 'text-brand';
    if (s <= 12) return 'text-amber-500';
    return 'text-success';
  }

  incStock(p: AdminProducto): void {
    this.pendingStock.update((m) => ({ ...m, [p.id]: this.resolvedStock(p) + 1 }));
    this.dirty.set(true);
  }

  decStock(p: AdminProducto): void {
    const cur = this.resolvedStock(p);
    if (cur <= 0) return;
    this.pendingStock.update((m) => ({ ...m, [p.id]: cur - 1 }));
    this.dirty.set(true);
  }

  toggleDisponible(p: AdminProducto): void {
    if (this.localId == null) return;
    const next = !p.disponible;
    this.adminProductService.actualizarDisponibilidad(this.localId, p.id, next).subscribe({
      next: (updated) => {
        this.productos.update((list) =>
          list.map((x) => (x.id === updated.id ? { ...x, disponible: updated.disponible } : x)),
        );
      },
    });
  }

  // ── Local ─────────────────────────────────────────────────────────────
  markDirty(): void {
    this.dirty.set(true);
  }

  toggleOpenDay(type: 'open' | 'res', i: number): void {
    const sig = type === 'open' ? this.openHours : this.resHours;
    sig.update((rows) => {
      const updated = [...rows];
      updated[i] = { ...updated[i], open: !updated[i].open };
      return updated;
    });
    this.dirty.set(true);
  }

  setFrom(type: 'open' | 'res', i: number, v: string): void {
    const sig = type === 'open' ? this.openHours : this.resHours;
    sig.update((rows) => {
      const updated = [...rows];
      updated[i] = { ...updated[i], from: v };
      return updated;
    });
    this.dirty.set(true);
  }

  setTo(type: 'open' | 'res', i: number, v: string): void {
    const sig = type === 'open' ? this.openHours : this.resHours;
    sig.update((rows) => {
      const updated = [...rows];
      updated[i] = { ...updated[i], to: v };
      return updated;
    });
    this.dirty.set(true);
  }

  // ── Save / Discard ────────────────────────────────────────────────────
  guardar(): void {
    if (this.localId == null || this.saving()) return;

    this.saving.set(true);
    const pendingStock = this.pendingStock();
    const stockRequests = Object.entries(pendingStock).map(([productId, stock]) =>
      this.adminProductService.actualizarStock(this.localId!, Number(productId), stock),
    );
    const localRequest = this.localService.updateLocal(this.localId, {
      name: this.localNombreField().trim(),
      address: this.localUbicacionField().trim(),
      phone: this.localTelefonoField().trim(),
      active: this.localIsOpen(),
      status: this.localIsOpen() ? 'open' : 'closed',
      hours: this.hoursMap(this.openHours()),
      reservationHours: this.hoursMap(this.resHours()),
    });

    forkJoin([...stockRequests, localRequest]).subscribe({
      next: () => {
        this.productos.update((products) =>
          products.map((product) => ({
            ...product,
            stock: pendingStock[product.id] ?? product.stock,
          })),
        );
        this.pendingStock.set({});
        this.dirty.set(false);
        this.saving.set(false);
        this.showToast('Cambios guardados correctamente');
      },
      error: () => {
        this.saving.set(false);
        this.showToast('No se pudieron guardar los cambios');
      },
    });
  }

  descartar(): void {
    this.pendingStock.set({});
    this.dirty.set(false);
    this.cargarLocal();
  }

  private showToast(msg: string): void {
    this.toast.set(msg);
    setTimeout(() => this.toast.set(null), 2500);
  }
}
