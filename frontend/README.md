<div align="center">

# 🍗 BAME — Frontend

**Plataforma digital del Asador de Pollo BAME · Murcia**

[![Angular](https://img.shields.io/badge/Angular-21-DD0031?style=flat-square&logo=angular&logoColor=white)](https://angular.dev)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)](https://tailwindcss.com)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.9-3178C6?style=flat-square&logo=typescript&logoColor=white)](https://www.typescriptlang.org)
[![Node.js](https://img.shields.io/badge/Node.js-22.x-339933?style=flat-square&logo=nodedotjs&logoColor=white)](https://nodejs.org)
[![Supabase](https://img.shields.io/badge/Supabase-Storage-3ECF8E?style=flat-square&logo=supabase&logoColor=white)](https://supabase.com)

</div>

---

## Tabla de contenidos

- [Descripción](#-descripción)
- [Stack tecnológico](#-stack-tecnológico)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Requisitos previos](#-requisitos-previos)
- [Puesta en marcha](#-puesta-en-marcha)
- [Variables de entorno](#-variables-de-entorno)
- [Roles y acceso](#-roles-y-acceso)
- [Rutas principales](#-rutas-principales)
- [Arquitectura](#-arquitectura)
- [Diseño y estilos](#-diseño-y-estilos)
- [Scripts disponibles](#-scripts-disponibles)

---

## 📋 Descripción

BAME es una SPA construida con **Angular 21** que permite a los clientes consultar la carta, hacer pedidos de recogida (*takeaway*) y pagar de forma segura. Incluye un sistema de paneles de gestión multi-rol para el personal del restaurante.

**Funcionalidades principales:**

| Para clientes | Para personal |
|---|---|
| Carta interactiva con filtros y alérgenos | Panel responsable — resumen diario (solo lectura) |
| Pedidos de recogida en local | Panel gerente — gestión de reservas, stock y local |
| Pago online con Stripe o en efectivo | Panel administrador — gestión multi-local |
| Historial de pedidos y perfil | Control de disponibilidad de productos en tiempo real |
| Mapa de locales con Leaflet | Configuración de horarios de apertura y reservas |
| Modo oscuro / claro | Historial completo de pedidos |

---

## 🛠 Stack tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Framework | [Angular](https://angular.dev) | 21 |
| Renderizado | Angular SSR + Express | 21 / 5 |
| Estilos | [Tailwind CSS v4](https://tailwindcss.com) | 4.x |
| Lenguaje | TypeScript | 5.9 |
| Iconos | [@tabler/icons-angular](https://tabler.io/icons) | 3.44 |
| Mapas | [Leaflet](https://leafletjs.com) | 1.9 |
| Storage | [Supabase](https://supabase.com) | 2.x |
| Pagos | [Stripe](https://stripe.com) | — (backend) |
| Fuentes | Poppins · Bebas Neue | Google Fonts |
| Tipado HTTP | Angular `HttpClient` + RxJS | 7.8 |
| Entorno | Node.js | 22.x |

---

## 📁 Estructura del proyecto

```
src/
├── app/
│   ├── core/                        # Núcleo de la aplicación
│   │   ├── guards/                  # authGuard, adminGuard
│   │   ├── interceptors/            # auth.interceptor (Bearer token)
│   │   ├── models/                  # Interfaces: Pedido, Producto, Local…
│   │   ├── services/                # Servicios HTTP: auth, pedidos, productos…
│   │   ├── supabase/                # Cliente Supabase (imágenes)
│   │   └── i18n/                    # Cadenas de texto localizadas
│   │
│   ├── features/                    # Módulos de funcionalidad
│   │   ├── auth/                    # Login, registro, verificación email
│   │   ├── home/                    # Página de inicio
│   │   ├── carta/                   # Carta de productos con filtros
│   │   ├── locals/                  # Locales, panel gerente, admin local
│   │   ├── reservations/            # Pedido, checkout, pago, historial
│   │   ├── admin/                   # Panel admin global + resumen día
│   │   ├── profile/                 # Perfil de usuario
│   │   ├── about/                   # Acerca de
│   │   ├── contact/                 # Contacto
│   │   └── legal/                   # Páginas legales
│   │
│   ├── shared/
│   │   └── components/
│   │       ├── admin-layout/        # Layout con sidebar para paneles
│   │       └── product-availability/# Control de disponibilidad
│   │
│   ├── pages/                       # Páginas de error (401/403/404/500/503)
│   └── app.routes.ts                # Árbol de rutas con lazy loading
│
├── environments/
│   ├── environment.ts               # Dev: API + Supabase
│   └── environment.prod.ts          # Prod: API + Supabase
└── styles.css                       # Tailwind @theme + tokens globales
```

---

## ✅ Requisitos previos

- **Node.js** `22.x` ([nvm](https://github.com/nvm-sh/nvm) recomendado)
- **npm** `10.x` o superior
- **Angular CLI** `21.x`

```bash
npm install -g @angular/cli@21
```

---

## 🚀 Puesta en marcha

### 1. Clonar el repositorio

```bash
git clone <url-del-repositorio>
cd bame/frontend
```

### 2. Instalar dependencias

```bash
npm install
```

### 3. Configurar variables de entorno

Edita `src/environments/environment.ts` con tus credenciales (ver sección siguiente).

### 4. Arrancar el servidor de desarrollo

```bash
npm start
# → http://localhost:4200
```

### 5. Build de producción

```bash
npm run build
# Salida en: dist/web/
```

---

## 🔑 Variables de entorno

Los ficheros de entorno se encuentran en `src/environments/`. **No se deben versionar** credenciales reales de producción.

```typescript
// src/environments/environment.ts
export const environment = {
  production: false,
  apiUrl: 'https://api.asaderobame.com',         // URL del backend Spring Boot
  supabaseUrl: 'https://<proyecto>.supabase.co', // URL del proyecto Supabase
  supabaseAnonKey: '<anon-key>',                  // Clave pública de Supabase
};
```

| Variable | Descripción |
|---|---|
| `apiUrl` | URL base de la API REST (Spring Boot, desplegada en Railway) |
| `supabaseUrl` | Proyecto Supabase para almacenamiento de imágenes |
| `supabaseAnonKey` | Clave pública de Supabase (`sb_publishable_*`) |

---

## 👥 Roles y acceso

El sistema define cinco roles con rutas protegidas mediante `authGuard`:

| Rol | Constante | Descripción | Redirección tras login |
|---|---|---|---|
| Anónimo | `anonymous` | Visitante sin cuenta | — |
| Cliente | `customer` | Usuario registrado | `/home` |
| Responsable | `responsable` | Personal del local (solo lectura) | `/admin/local/:id/resumen-dia` |
| Gerente | `manager` | Gerente del local (edición completa) | `/admin/local/:id/gerente` |
| Administrador | `admin` | Acceso total al sistema | `/admin` |

Los roles se mapean desde los claims `ROLE_*` del JWT emitido por el backend:

```
ROLE_ADMIN                    → admin
ROLE_MANAGER / ROLE_GERENTE   → manager
ROLE_RESPONSABLE              → responsable
ROLE_STORE                    → store
```

---

## 🗺 Rutas principales

### Públicas y de cliente (`/home/...`)

| Ruta | Componente | Descripción |
|---|---|---|
| `/home` | `Home` | Página de inicio |
| `/home/carta` | `CartaPage` | Carta interactiva con categorías y filtros |
| `/home/locales` | `LocalsPage` | Mapa y listado de locales |
| `/home/locales/:id/reserva` | `Reservation` | Carta del local + carrito de compra |
| `/home/locales/:id/reserva/pago` | `CheckoutPage` | Confirmación y método de pago |
| `/home/locales/:id/reserva/pago/success` | `PaymentSuccessPage` | Confirmación de pago OK |
| `/home/locales/:id/reserva/pago/cancel` | `PaymentCancelPage` | Pago cancelado o fallido |
| `/home/perfil` | `ProfilePage` | Perfil e historial de pedidos |
| `/home/login` | `LoginPage` | Inicio de sesión |
| `/home/registro` | `RegisterPage` | Registro de cuenta nueva |

### Paneles de gestión (`/admin/...`)

| Ruta | Componente | Roles |
|---|---|---|
| `/admin` | `Admin` | `admin` |
| `/admin/local/:id` | `LocalAdmin` | `admin`, `manager`, `store` |
| `/admin/local/:id/resumen-dia` | `ResumenDia` | `admin`, `responsable` |
| `/admin/local/:id/gerente` | `GerentePanel` | `admin`, `manager` |
| `/admin/local/:id/reservas` | `ReservationList` | `admin`, `manager`, `store` |
| `/admin/local/:id/disponibilidad` | `ProductAvailability` | `admin`, `manager`, `store` |
| `/admin/local/:id/historial` | `ReservationHistory` | `admin`, `manager` |
| `/admin/local/:id/avanzado` | `LocalAdvAdmin` | `admin`, `manager` |

---

## 🏗 Arquitectura

### Componentes standalone con Signals

Todos los componentes son `standalone: true` y usan la **API de Signals** de Angular (`signal`, `computed`, `effect`). No existe ningún `NgModule`.

```typescript
@Component({
  selector: 'app-ejemplo',
  standalone: true,
  imports: [CurrencyPipe, TablerIconComponent],
  templateUrl: './ejemplo.html',
})
export class Ejemplo implements OnInit {
  private readonly service = inject(EjemploService);

  readonly items  = signal<Item[]>([]);
  readonly total  = computed(() => this.items().length);
}
```

### Lazy loading en todas las rutas

Cada ruta carga su componente con `loadComponent()` para minimizar el bundle inicial:

```typescript
{
  path: 'carta',
  loadComponent: () =>
    import('./features/carta/carta').then(m => m.CartaPage),
  data: { roles: ['ANONYMOUS', 'USER'] },
}
```

### Interceptor de autenticación

`auth.interceptor.ts` inyecta el header `Authorization: Bearer <token>` en todas las peticiones dirigidas a la API. El token se persiste en `localStorage` y se gestiona a través de `AuthService` con señales reactivas.

### Layout compartido de paneles

Los paneles de gestión comparten el componente `AdminLayout`, que incluye:
- Sidebar fijo de **56px** en desktop (`lg:ml-56`)
- Overlay + drawer en móvil
- Visibilidad condicional de enlaces según el rol del usuario autenticado

---

## 🎨 Diseño y estilos

### Tokens con Tailwind CSS v4 `@theme`

Los colores y tipografías se declaran como custom tokens en `src/styles.css`:

```css
@theme {
  --font-sans:    'Poppins', system-ui, sans-serif;
  --font-display: 'Bebas Neue', sans-serif;

  --color-brand:   #FF4500;   /* naranja-rojo — CTA principal */
  --color-accent:  #FFD700;   /* dorado — acentos y elementos activos */
  --color-success: #16a34a;   /* verde — disponibilidad y confirmaciones */

  --color-surface-dark: #18181B;  /* fondos oscuros: navbar, paneles */
  --color-surface-card: #FFFFFF;  /* tarjetas */
  --color-surface-page: #F4F4F5;  /* fondo general de página */
}
```

### Modo oscuro

Se activa añadiendo `data-theme="dark"` al elemento `<html>`. Las variables se redefinen automáticamente:

```css
html[data-theme='dark'] {
  --color-brand:        #FF5722;
  --color-surface-page: #09090B;
  --color-text-primary: #FAFAFA;
}
```

### Paleta de colores

| Token | Color | Uso |
|---|:---:|---|
| `brand` | `#FF4500` | Botones CTA, badges de error, énfasis |
| `accent` | `#FFD700` | Indicadores activos, foco de inputs, sidebar |
| `success` | `#16a34a` | Disponibilidad, confirmaciones, estado Listo |
| `surface-dark` | `#18181B` | Navbar, sidebar, footer, paneles de gestión |

---

## 📦 Scripts disponibles

```bash
npm start          # Servidor de desarrollo en localhost:4200
npm run build      # Build de producción con SSR
npm run watch      # Build en modo watch (development)
npm test           # Tests unitarios con Karma + Jasmine
```

---

## 🔗 Repositorios relacionados

| Repositorio | Descripción |
|---|---|
| `bame/backend` | API REST con Spring Boot (Java) |
| `bame/docs` | Manual de usuario y documentación del proyecto |

---

<div align="center">

Desarrollado como Trabajo de Fin de Grado · Junio 2026

</div>
