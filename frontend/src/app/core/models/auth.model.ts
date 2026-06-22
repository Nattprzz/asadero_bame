// ─────────────────────────────────────────────────────────────────────────────
// auth.dto.ts — contratos de autenticación.
//
// Este archivo define las estructuras de datos utilizadas durante los procesos
// de registro, inicio de sesión y gestión de usuarios autenticados. Permite
// mantener una comunicación tipada entre el frontend Angular y la API.
// ─────────────────────────────────────────────────────────────────────────────

// Datos necesarios para iniciar sesión.
export interface LoginDto {
  email: string;
  password: string;
}

// Datos necesarios para registrar un nuevo usuario.
export interface RegisterDto {
  name: string;
  email: string;
  phone?: string;
  password: string;
}

// Estructura estándar devuelta por la API tras autenticarse.
export interface AuthResponse {
  success: boolean;

  data: {
    token: string;

    // Información del usuario autenticado.
    user: UsuarioAutenticado;
  };
}

// Representa el usuario autenticado devuelto por el backend.
export interface UsuarioAutenticado {
  id: number;
  name: string;
  surname?: string;
  username?: string;
  email: string;
  phone?: string | null;

  // Roles asignados al usuario dentro de la aplicación.
  roles: string[];

  // Local asociado al usuario (responsable, store, manager).
  localId?: number | null;

  // Fechas enviadas por la API en formato ISO 8601.
  createdAt?: string;
  updatedAt?: string;
}