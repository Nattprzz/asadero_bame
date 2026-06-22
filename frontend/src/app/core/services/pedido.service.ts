import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { map, type Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import type { Pedido, CrearPedidoDto, EstadoPedido, LineaPedido, MetodoPago, EstadoPago } from '../models';

interface BackendOrderLine {
  productId: number | null;
  product?: { id?: number; name?: string } | null;
  quantity: number;
  unitPrice: number;
}

interface BackendOrder {
  id: number;
  reference: string;
  userId: number | null;
  localId: number | null;
  clientName?: string | null;
  status: string;
  total: number;
  paymentMethod?: string | null;
  paymentStatus?: string | null;
  lines: BackendOrderLine[];
  createdAt: string;
}

interface ApiResponse<T> {
  success: boolean;
  data: T;
}

// Backend English status → frontend Spanish EstadoPedido
const STATUS_TO_FRONTEND: Record<string, EstadoPedido> = {
  pending:   'Pendiente',
  confirmed: 'Confirmado',
  preparing: 'En preparación',
  ready:     'Listo',
  completed: 'Entregado',
  cancelled: 'Cancelado',
};

// Frontend Spanish EstadoPedido → backend English status
const STATUS_TO_BACKEND: Record<EstadoPedido, string> = {
  'Pendiente':      'pending',
  'Confirmado':     'confirmed',
  'En preparación': 'preparing',
  'Listo':          'ready',
  'Entregado':      'completed',
  'Cancelado':      'cancelled',
};

function mapOrder(raw: BackendOrder): Pedido {
  const pad = (n: number) => String(n).padStart(2, '0');

  // Use slices of the ATOM string to stay in server-local timezone
  const fecha = raw.createdAt.slice(0, 10);        // "2026-06-22"
  const hora  = raw.createdAt.slice(11, 16);       // "10:30"

  const productos: LineaPedido[] = (raw.lines ?? []).map((l) => ({
    productoId: l.product?.id ?? l.productId ?? 0,
    nombre:     l.product?.name ?? `Producto ${l.productId ?? 0}`,
    cantidad:   l.quantity,
    precio:     l.unitPrice,
  }));

  return {
    id:            raw.id,
    reserva:       raw.id,
    clienteId:     raw.userId ?? 0,
    cliente:       raw.clientName?.trim() || `#${raw.userId ?? raw.id}`,
    localId:       raw.localId ?? 0,
    productos,
    estado:        STATUS_TO_FRONTEND[raw.status] ?? 'Pendiente',
    total:         raw.total,
    fecha,
    hora,
    paymentMethod: (raw.paymentMethod as MetodoPago) ?? undefined,
    paymentStatus: (raw.paymentStatus as EstadoPago) ?? undefined,
  };
}

@Injectable({ providedIn: 'root' })
export class PedidoService {
  private readonly http = inject(HttpClient);
  private readonly base = `${environment.apiUrl.replace(/\/$/, '')}/api/v1`;

  crearPedido(dto: CrearPedidoDto): Observable<Pedido> {
    return this.http
      .post<ApiResponse<BackendOrder>>(`${this.base}/pedidos`, dto)
      .pipe(map((res) => mapOrder(res.data)));
  }

  getMisPedidos(): Observable<Pedido[]> {
    return this.http
      .get<ApiResponse<BackendOrder[]>>(`${this.base}/pedidos/mis-pedidos`)
      .pipe(map((res) => res.data.map(mapOrder)));
  }

  getPedidosAdmin(localId: number): Observable<Pedido[]> {
    return this.http
      .get<ApiResponse<BackendOrder[]>>(`${this.base}/admin/pedidos?localId=${localId}`)
      .pipe(map((res) => res.data.map(mapOrder)));
  }

  actualizarEstado(id: number, estado: EstadoPedido): Observable<Pedido> {
    return this.http
      .patch<ApiResponse<BackendOrder>>(`${this.base}/admin/pedidos/${id}/estado`, {
        estado: STATUS_TO_BACKEND[estado] ?? estado,
      })
      .pipe(map((res) => mapOrder(res.data)));
  }
}
