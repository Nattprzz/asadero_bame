// ─────────────────────────────────────────────────────────────────────────────
// producto.model.ts — modelos de catálogo.
//
// Este archivo define las estructuras de datos relacionadas con categorías,
// productos, alérgenos y carrito de compra. Se utilizan para intercambiar
// información entre el frontend y la API.
// ─────────────────────────────────────────────────────────────────────────────

// Representa una categoría de productos.
export interface Categoria {
  id: number;

  // Nombre principal en español.
  nombre: string;

  // Traducciones disponibles para otros idiomas.
  nombreEn?: string | null;
  nombreFr?: string | null;
  nombreIt?: string | null;
  nombreDe?: string | null;

  slug?: string;
  descripcion?: string | null;
  imagen?: string | null;

  activa?: boolean;
  orden?: number;
}

// Representa un alérgeno asociado a un producto.
export interface Alergeno {
  id: number;
  nombre: string;
  slug: string;
  descripcion?: string | null;
  icono?: string | null;
}

// Modelo principal de un producto.
export interface Producto {
  id: number;

  // Nombre principal en español.
  nombre: string;

  // Traducciones del nombre.
  nombreEn?: string | null;
  nombreFr?: string | null;
  nombreIt?: string | null;
  nombreDe?: string | null;

  // Descripción principal en español.
  descripcion: string;

  // Traducciones de la descripción.
  descripcionEn?: string | null;
  descripcionFr?: string | null;
  descripcionIt?: string | null;
  descripcionDe?: string | null;

  precio: number;
  foto: string;

  categoriaId: number;

  // Listado simplificado de alérgenos.
  alergenos: string[];

  // Información detallada de alérgenos y posibles trazas.
  alergenosDetalle?: Alergeno[];
  trazas?: Alergeno[];

  disponible: boolean;
  disponibilidad?: string;

  destacado?: boolean;

  peso?: string | null;
  tiempoPreparacion?: number | null;

  // Campos utilizados principalmente en carrito y gestión de stock.
  cantidad?: number;
  stock?: number | null;
}

// Representa una línea dentro del carrito de compra.
export interface LineaCarrito {
  producto: Producto;
  cantidad: number;
}

// Agrupa productos según su categoría.
export interface ProductosPorCategoria {
  categoria: Categoria;
  productos: Producto[];
}