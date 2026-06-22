import { Component, signal, inject, OnInit } from '@angular/core';
import { CurrencyPipe, NgClass } from '@angular/common';
import { ActivatedRoute } from '@angular/router';
import { ScrollingModule } from '@angular/cdk/scrolling';
import { A11yModule } from '@angular/cdk/a11y';
import { OverlayModule, Overlay, type ConnectedPosition } from '@angular/cdk/overlay';
import { TablerIconComponent } from '@tabler/icons-angular';
import { PedidoService } from '../../../core/services/pedido.service';
import type { Pedido, EstadoPedido } from '../../../core/models';

type PedidoRow = Pedido & { isUpdating: boolean };

@Component({
  selector: 'app-reservation-list',
  standalone: true,
  imports: [CurrencyPipe, NgClass, ScrollingModule, A11yModule, OverlayModule, TablerIconComponent],
  templateUrl: './reservation-list.html',
})
export class ReservationList implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly pedidoService = inject(PedidoService);
  private readonly overlay = inject(Overlay);

  localId = 0;
  reservations: PedidoRow[] = [];
  loading = false;
  error: string | null = null;

  sortKey: 'reserva' | 'hora' | 'estado' | 'cliente' = 'reserva';
  sortDir: 'asc' | 'desc' = 'asc';

  readonly activeDropdownId = signal<number | null>(null);
  readonly activeOptionIndex = signal(0);

  readonly estados: EstadoPedido[] = [
    'Pendiente',
    'Confirmado',
    'En preparación',
    'Listo',
    'Entregado',
    'Cancelado',
  ];

  readonly positions: ConnectedPosition[] = [
    { originX: 'start', originY: 'bottom', overlayX: 'start', overlayY: 'top', offsetY: 8 },
    { originX: 'start', originY: 'top', overlayX: 'start', overlayY: 'bottom', offsetY: -8 },
  ];

  get scrollStrategy() {
    return this.overlay.scrollStrategies.reposition();
  }

  ngOnInit(): void {
    this.localId = Number(this.route.snapshot.paramMap.get('id') ?? '0');
    this.cargarReservas();
  }

  cargarReservas(): void {
    this.loading = true;
    this.error = null;
    this.pedidoService.getPedidosAdmin(this.localId).subscribe({
      next: (pedidos) => {
        this.reservations = pedidos.map((p) => ({ ...p, isUpdating: false }));
        this.loading = false;
      },
      error: () => {
        this.error = 'No se pudieron cargar las reservas.';
        this.loading = false;
      },
    });
  }

  trackReservation(_index: number, r: PedidoRow): number {
    return r.id;
  }

  getSortIcon(key: 'reserva' | 'hora' | 'estado' | 'cliente'): string {
    if (this.sortKey !== key) return 'arrow-down';
    return this.sortDir === 'asc' ? 'arrow-down' : 'arrow-up';
  }

  toggleSort(key: 'reserva' | 'hora' | 'estado' | 'cliente'): void {
    if (this.sortKey !== key) {
      this.sortKey = key;
      this.sortDir = 'asc';
    } else {
      this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
    }
  }

  sortedReservations(): PedidoRow[] {
    const data = [...this.reservations];
    const key = this.sortKey;
    const dir = this.sortDir;
    const order: Record<EstadoPedido, number> = {
      Pendiente: 0,
      Confirmado: 1,
      'En preparación': 2,
      Listo: 3,
      Entregado: 4,
      Cancelado: 5,
    };
    data.sort((a, b) => {
      let res = 0;
      switch (key) {
        case 'reserva': res = a.reserva - b.reserva; break;
        case 'hora': res = a.hora.localeCompare(b.hora); break;
        case 'cliente': res = a.cliente.localeCompare(b.cliente); break;
        case 'estado': res = order[a.estado] - order[b.estado]; break;
      }
      return dir === 'asc' ? res : -res;
    });
    return data;
  }

  calcularTotal(reserva: PedidoRow): number {
    return reserva.total ?? reserva.productos.reduce((sum, p) => sum + p.cantidad * p.precio, 0);
  }

  getTotalesPorProductoPendiente(): Array<{ nombre: string; cantidad: number }> {
    const resumen: Record<string, number> = {};
    for (const r of this.reservations) {
      if (r.estado === 'Cancelado' || r.estado === 'Entregado') continue;
      for (const p of r.productos) {
        resumen[p.nombre] = (resumen[p.nombre] || 0) + p.cantidad;
      }
    }
    return Object.entries(resumen)
      .map(([nombre, cantidad]) => ({ nombre, cantidad }))
      .sort((a, b) => b.cantidad - a.cantidad);
  }

  getMetricasCargaTrabajo(): { totalPendientes: number; totalCompletadas: number; totalProgreso: number } {
    const total = this.reservations.length;
    const activos: EstadoPedido[] = ['Pendiente', 'Confirmado', 'En preparación'];
    const completados: EstadoPedido[] = ['Listo', 'Entregado'];
    const totalPendientes = this.reservations.filter((r) => activos.includes(r.estado)).length;
    const totalCompletadas = this.reservations.filter((r) => completados.includes(r.estado)).length;
    const totalProgreso = total > 0 ? Math.round((totalCompletadas / total) * 100) : 0;
    return { totalPendientes, totalCompletadas, totalProgreso };
  }

  getEstadoClases(estado: EstadoPedido): Record<string, boolean> {
    return {
      'bg-yellow-400 border-yellow-500 text-black': estado === 'Pendiente',
      'bg-blue-500 border-blue-600 text-white': estado === 'Confirmado',
      'bg-orange-500 border-orange-600 text-white': estado === 'En preparación',
      'bg-green-500 border-green-600 text-white': estado === 'Listo',
      'bg-green-700 border-green-800 text-white': estado === 'Entregado',
      'bg-brand border-brand-hover text-white': estado === 'Cancelado',
    };
  }

  actualizarEstadoReserva(reserva: PedidoRow): void {
    reserva.isUpdating = true;
    this.pedidoService.actualizarEstado(reserva.id, reserva.estado).subscribe({
      next: (updated) => {
        reserva.estado = updated.estado;
        reserva.isUpdating = false;
      },
      error: () => {
        reserva.isUpdating = false;
      },
    });
  }

  isDropdownOpen(id: number): boolean {
    return this.activeDropdownId() === id;
  }

  toggleDropdown(id: number): void {
    const current = this.activeDropdownId();
    this.activeDropdownId.set(current === id ? null : id);
    if (current !== id) this.activeOptionIndex.set(0);
  }

  closeDropdown(): void {
    this.activeDropdownId.set(null);
  }

  selectStatus(reserva: PedidoRow, estado: EstadoPedido): void {
    reserva.estado = estado;
    this.actualizarEstadoReserva(reserva);
    this.activeDropdownId.set(null);
  }

  onTriggerKeydown(event: KeyboardEvent, id: number): void {
    if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      this.toggleDropdown(id);
    }
    if (event.key === 'ArrowDown') {
      event.preventDefault();
      if (!this.isDropdownOpen(id)) this.toggleDropdown(id);
      this.activeOptionIndex.update((i) => (i + 1) % this.estados.length);
    }
    if (event.key === 'ArrowUp') {
      event.preventDefault();
      if (!this.isDropdownOpen(id)) this.toggleDropdown(id);
      const len = this.estados.length;
      this.activeOptionIndex.update((i) => (i - 1 + len) % len);
    }
    if (event.key === 'Escape') this.closeDropdown();
  }

  onListboxKeydown(event: KeyboardEvent): void {
    const len = this.estados.length;
    switch (event.key) {
      case 'ArrowDown':
        event.preventDefault();
        this.activeOptionIndex.update((i) => (i + 1) % len);
        break;
      case 'ArrowUp':
        event.preventDefault();
        this.activeOptionIndex.update((i) => (i - 1 + len) % len);
        break;
      case 'Enter': {
        event.preventDefault();
        const r = this.reservations.find((r) => r.id === this.activeDropdownId());
        if (r) this.selectStatus(r, this.estados[this.activeOptionIndex()]);
        break;
      }
      case 'Escape':
        event.preventDefault();
        this.closeDropdown();
        break;
    }
  }
}
