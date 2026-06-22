// ─────────────────────────────────────────────────────────────────────────────
// supabase.client.ts — cliente principal de Supabase.
//
// Centraliza la creación de la conexión con Supabase utilizando las variables
// de entorno definidas para cada entorno de ejecución. Este cliente es
// reutilizado por todos los servicios que acceden a la base de datos.
// ─────────────────────────────────────────────────────────────────────────────

import { createClient } from '@supabase/supabase-js';
import { environment } from '../../../environments/environment';

// Instancia global del cliente Supabase.
//
// Utiliza:
// - supabaseUrl: URL del proyecto Supabase.
// - supabaseAnonKey: clave pública para acceso desde el frontend.
//
// Todas las consultas a la base de datos se realizan a través de este cliente.
export const supabase = createClient(
  environment.supabaseUrl,
  environment.supabaseAnonKey,
);