// ─────────────────────────────────────────────────────────────────────────────
// pedido.model.ts — modelo de pedidos.
//
// Este archivo define las estructuras de datos relacionadas con los pedidos,
// incluyendo sus estados, líneas de productos y los datos necesarios para
// crear nuevos pedidos desde el frontend.
// ─────────────────────────────────────────────────────────────────────────────

// Método de pago elegido por el cliente al confirmar el pedido.
export type MetodoPago = 'stripe' | 'pay_at_store';

// Estado del cobro del pedido.
export type EstadoPago = 'pending' | 'paid' | 'cancelled';

// Estados posibles por los que puede pasar un pedido.
export type EstadoPedido =
  | 'Pendiente'
  | 'Confirmado'
  | 'En preparación'
  | 'Listo'
  | 'Entregado'
  | 'Cancelado';

// Representa un producto incluido dentro de un pedido.
export interface LineaPedido {
  productoId: number;
  nombre: string;
  cantidad: number;
  precio: number;
}

// Modelo principal de un pedido.
export interface Pedido {
  id: number;

  // Número identificativo de la reserva o pedido.
  reserva: number;

  clienteId: number;
  cliente: string;

  localId: number;

  // Productos incluidos en el pedido.
  productos: LineaPedido[];

  estado: EstadoPedido;

  // Importe total calculado del pedido.
  total: number;

  fecha: string;
  hora: string;

  paymentMethod?: MetodoPago;
  paymentStatus?: EstadoPago;

  // Indicador utilizado por la interfaz durante actualizaciones.
  isUpdating?: boolean;
}

// Datos necesarios para crear un pedido desde el frontend.
export interface CrearPedidoDto {
  localId: number;

  type: 'takeaway' | 'delivery';

  lines: {
    productId: number;
    quantity: number;
  }[];

  paymentMethod: MetodoPago;
}
