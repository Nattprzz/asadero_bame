import { Component, inject, OnInit } from '@angular/core';
import { CurrencyPipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { ScrollingModule } from '@angular/cdk/scrolling';
import { TablerIconComponent } from '@tabler/icons-angular';
import { PedidoService } from '../../../core/services/pedido.service';
import type { Pedido, EstadoPedido } from '../../../core/models';

@Component({
  selector: 'app-reservation-history',
  standalone: true,
  imports: [CurrencyPipe, FormsModule, ScrollingModule, TablerIconComponent],
  templateUrl: './reservation-history.html',
})
export class ReservationHistory implements OnInit {
  private readonly route = inject(ActivatedRoute);
  private readonly pedidoService = inject(PedidoService);

  localId = 0;
  selectedDate: string;
  allReservations: Pedido[] = [];
  reservations: Pedido[] = [];
  sortKey: 'reserva' | 'hora' | 'estado' | 'cliente' = 'reserva';
  sortDir: 'asc' | 'desc' = 'asc';
  isLoading = false;
  error: string | null = null;

  constructor() {
    const today = new Date();
    this.selectedDate = today.toISOString().slice(0, 10);
  }

  ngOnInit(): void {
    this.localId = Number(this.route.snapshot.paramMap.get('id') ?? '0');
    this.cargarHistorial();
  }

  cargarHistorial(): void {
    this.isLoading = true;
    this.error = null;
    this.pedidoService.getPedidosAdmin(this.localId).subscribe({
      next: (pedidos) => {
        this.allReservations = pedidos;
        this.filtrarPorFecha();
        this.isLoading = false;
      },
      error: () => {
        this.error = 'No se pudo cargar el historial de reservas.';
        this.isLoading = false;
      },
    });
  }

  filtrarPorFecha(): void {
    this.reservations = this.allReservations.filter(
      (r) => r.fecha && r.fecha.startsWith(this.selectedDate),
    );
  }

  onDateChange(): void {
    this.filtrarPorFecha();
  }

  getSortIcon(key: 'reserva' | 'hora' | 'estado' | 'cliente'): string {
    if (this.sortKey !== key) return 'arrow-down';
    return this.sortDir === 'asc' ? 'arrow-down' : 'arrow-up';
  }

  trackReservation(_index: number, r: Pedido): number {
    return r.id;
  }

  toggleSort(key: 'reserva' | 'hora' | 'estado' | 'cliente'): void {
    if (this.sortKey !== key) {
      this.sortKey = key;
      this.sortDir = 'asc';
    } else {
      this.sortDir = this.sortDir === 'asc' ? 'desc' : 'asc';
    }
  }

  sortedReservations(): Pedido[] {
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
      if (res === 0) res = a.id - b.id;
      return dir === 'asc' ? res : -res;
    });
    return data;
  }

  calcularTotal(reserva: Pedido): number {
    return reserva.total ?? reserva.productos.reduce((sum, p) => sum + p.cantidad * p.precio, 0);
  }

  obtenerResumenProductosDia(): { nombre: string; pendiente: number; completada: number; total: number }[] {
    const resumen: Record<string, { pendiente: number; completada: number }> = {};
    for (const r of this.reservations) {
      for (const p of r.productos) {
        if (!resumen[p.nombre]) resumen[p.nombre] = { pendiente: 0, completada: 0 };
        if (r.estado === 'Pendiente' || r.estado === 'Confirmado' || r.estado === 'En preparación') {
          resumen[p.nombre].pendiente += p.cantidad;
        }
        if (r.estado === 'Listo' || r.estado === 'Entregado') {
          resumen[p.nombre].completada += p.cantidad;
        }
      }
    }
    return Object.keys(resumen)
      .map((nombre) => ({
        nombre,
        pendiente: resumen[nombre].pendiente,
        completada: resumen[nombre].completada,
        total: resumen[nombre].pendiente + resumen[nombre].completada,
      }))
      .sort((a, b) => a.nombre.localeCompare(b.nombre));
  }

  get countPendiente(): number {
    return this.reservations.filter(
      (r) => r.estado === 'Pendiente' || r.estado === 'Confirmado' || r.estado === 'En preparación',
    ).length;
  }

  get countCompletada(): number {
    return this.reservations.filter((r) => r.estado === 'Listo' || r.estado === 'Entregado').length;
  }

  get countCancelada(): number {
    return this.reservations.filter((r) => r.estado === 'Cancelado').length;
  }

  get totalCompletadas(): number {
    return this.reservations
      .filter((r) => r.estado === 'Entregado')
      .reduce((sum, r) => sum + this.calcularTotal(r), 0);
  }
}
