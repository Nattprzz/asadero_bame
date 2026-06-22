// ─────────────────────────────────────────────────────────────────────────────
// local.model.ts — modelo de locales.
//
// Este archivo define la estructura de datos utilizada para representar los
// locales de la aplicación, así como algunas utilidades relacionadas con su
// estado y disponibilidad.
// ─────────────────────────────────────────────────────────────────────────────

// Estados posibles de un local dentro de la aplicación.
export type EstadoLocal = '1' | '2' | '3' | '4' | '5' | '6';

// Representa un tramo horario de apertura.
export interface HoraTramo {
  open: string;
  close: string;
}

// Agrupa los horarios por día de la semana.
export type HoursMap = Record<string, HoraTramo[]>;

// Modelo principal de un local.
export interface Local {
  id: number;
  nombre: string;
  ubicacion: string;
  horario: string;

  // Horarios mostrados en formato simplificado.
  horarios?: string[];

  // Horarios estructurados para funcionalidades avanzadas.
  hours?: HoursMap;
  reservationHours?: HoursMap;

  foto: string;
  telefono: string;
  estado: EstadoLocal;

  email?: string | null;
  whatsapp?: string | null;
  latitud?: number | null;
  longitud?: number | null;
}

// Etiquetas mostradas al usuario para cada estado.
export const ESTADO_LOCAL_LABEL: Record<EstadoLocal, string> = {
  '1': 'Abierto',
  '2': 'Cerrado',
  '3': 'Abre Pronto',
  '4': 'Cierra Pronto',
  '5': 'Agotado',
  '6': 'No Disponible',
};

// Determina si un local puede recibir pedidos.
export function isLocalDisponible(estado: EstadoLocal): boolean {
  return estado === '1' || estado === '3' || estado === '4';
}

// Determina si un local no está disponible para operar.
export function isLocalDeshabilitado(estado: EstadoLocal): boolean {
  return estado === '2' || estado === '5' || estado === '6';
}
