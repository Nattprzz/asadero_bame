<div align="center">

# 🍗 BAME — Backend

**API REST del Asador de Pollo BAME · Murcia**

[![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?style=flat-square&logo=symfony&logoColor=white)](https://symfony.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?style=flat-square&logo=postgresql&logoColor=white)](https://postgresql.org)
[![Doctrine ORM](https://img.shields.io/badge/Doctrine_ORM-3.x-FC6A31?style=flat-square)](https://www.doctrine-project.org)
[![Stripe](https://img.shields.io/badge/Stripe-Checkout-635BFF?style=flat-square&logo=stripe&logoColor=white)](https://stripe.com)
[![Docker](https://img.shields.io/badge/Docker-Railway-2496ED?style=flat-square&logo=docker&logoColor=white)](https://railway.app)

</div>

---

## Tabla de contenidos

- [Descripción](#-descripción)
- [Stack tecnológico](#-stack-tecnológico)
- [Estructura del proyecto](#-estructura-del-proyecto)
- [Requisitos previos](#-requisitos-previos)
- [Instalación y puesta en marcha](#-instalación-y-puesta-en-marcha)
- [Variables de entorno](#-variables-de-entorno)
- [Autenticación](#-autenticación)
- [Roles y permisos](#-roles-y-permisos)
- [Referencia de endpoints](#-referencia-de-endpoints)
- [Pagos con Stripe](#-pagos-con-stripe)
- [Formato de respuesta JSON](#-formato-de-respuesta-json)
- [Tests](#-tests)
- [Despliegue](#-despliegue)

---

## 📋 Descripción

API REST stateless construida con **Symfony 7.4** que alimenta la plataforma BAME. Gestiona el catálogo de productos, la autenticación de usuarios con tokens Bearer propios, los pedidos de recogida (*takeaway*) y los pagos mediante Stripe Checkout.

**Características principales:**

| Área | Detalle |
|---|---|
| Auth | Tokens Bearer personalizados (SHA-256) — sin JWT ni Supabase Auth |
| Pagos | Stripe Checkout Sessions + Webhooks con ledger de deduplicación |
| Scoping | Los roles operacionales quedan acotados al local asignado |
| Rate limiting | Límites por IP y ventana de tiempo con Symfony Cache |
| CORS | Configurado con NelmioCorsBundle, lista de orígenes explícita |
| Imágenes | Almacenadas en Supabase Storage; URLs resueltas por el backend |
| Aliases | Rutas en inglés y español para compatibilidad con el frontend |

---

## 🛠 Stack tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Framework | [Symfony](https://symfony.com) | 7.4 |
| Lenguaje | PHP | ≥ 8.3 |
| ORM | [Doctrine ORM](https://www.doctrine-project.org) | 3.x |
| Migraciones | Doctrine Migrations Bundle | 3.x |
| Base de datos | PostgreSQL | 16 |
| Pagos | [Stripe PHP SDK](https://github.com/stripe/stripe-php) | 20.x |
| Storage | [Supabase](https://supabase.com) | — |
| CORS | [NelmioCorsBundle](https://github.com/nelmio/cors-bundle) | 2.x |
| Tests | PHPUnit | 12.5 |
| Contenedor | Docker (PHP 8.3 CLI) | — |
| Plataforma | [Railway](https://railway.app) | — |

---

## 📁 Estructura del proyecto

```
src/
├── Controller/
│   └── Api/
│       ├── V1/                         # Endpoints REST v1
│       │   ├── Admin/                  # Rutas protegidas de administración
│       │   │   ├── AllergenAdminController.php
│       │   │   ├── CategoryAdminController.php
│       │   │   ├── LocalAdminController.php
│       │   │   ├── MetricsController.php
│       │   │   ├── OrderAdminController.php
│       │   │   ├── ProductAdminController.php
│       │   │   └── UserAdminController.php
│       │   ├── AuthController.php      # Registro, login, recuperación contraseña
│       │   ├── LocalController.php     # Locales públicos
│       │   ├── ProductController.php   # Catálogo público con filtros
│       │   ├── CategoryController.php  # Categorías
│       │   ├── AllergenController.php  # Alérgenos
│       │   ├── OrderController.php     # Pedidos del cliente autenticado
│       │   └── MeController.php        # Perfil del usuario autenticado
│       └── Payments/
│           └── StripePaymentController.php  # Checkout Session + Webhook
│
├── Entity/                             # Entidades Doctrine
│   ├── User.php
│   ├── Local.php
│   ├── Product.php
│   ├── Category.php
│   ├── Allergen.php
│   ├── LocalProduct.php               # Precio/stock por local
│   ├── CustomerOrder.php
│   ├── OrderLine.php
│   ├── PersonalAccessToken.php        # Tokens Bearer
│   ├── PasswordResetToken.php
│   └── StripeEventLedger.php          # Ledger de deduplicación de webhooks
│
├── Service/                            # Lógica de negocio
│   ├── AuthService.php
│   ├── OrderService.php               # Creación y ciclo de vida de pedidos
│   ├── OrderStockService.php          # Reserva y liberación de stock
│   ├── StripePaymentService.php       # Creación de sesiones de pago
│   ├── StripeWebhookHandler.php       # Procesado de eventos Stripe
│   ├── StripeEventLedgerService.php   # Deduplicación de webhooks
│   ├── RateLimitService.php           # Rate limiting por IP
│   ├── EntityPresenter.php            # Serialización a JSON
│   ├── ApiResponseFactory.php         # Envoltura estándar de respuestas
│   ├── AdminLocalScopeResolver.php    # Acota queries al local del usuario
│   └── ImageUrlResolver.php           # URLs de Supabase Storage
│
├── Security/
│   └── BearerTokenAuthenticator.php   # Autenticador Bearer personalizado
│
├── Enum/
│   ├── Roles.php                      # ROLE_USER, ROLE_ADMIN, ROLE_GERENTE…
│   ├── OrderStatus.php                # pending → confirmed → preparing → ready…
│   ├── LocalStatus.php
│   ├── PaymentMethod.php
│   ├── PaymentStatus.php
│   └── ProductAvailability.php
│
├── Repository/                         # Queries Doctrine personalizadas
├── DataFixtures/
│   └── AppFixtures.php                # Datos de demostración realistas
└── EventSubscriber/
    └── ApiExceptionSubscriber.php     # Errores HTTP → JSON homogéneo

migrations/                             # Migraciones Doctrine para PostgreSQL
tests/
├── Unit/                               # Tests unitarios (sin base de datos)
└── Functional/                         # Tests funcionales (HTTP real)
```

---

## ✅ Requisitos previos

- **PHP** `≥ 8.3` con extensiones `pdo`, `pdo_pgsql`, `ctype`, `iconv`
- **Composer** `2.x`
- **PostgreSQL** `16` (local o via Docker)
- **Symfony CLI** *(opcional, recomendado)*

```bash
# Verificar versión de PHP
php -v

# Instalar Symfony CLI
curl -sS https://get.symfony.com/cli/installer | bash
```

---

## 🚀 Instalación y puesta en marcha

### 1. Clonar e instalar dependencias

```bash
git clone <url-del-repositorio>
cd bame/backend
composer install
```

### 2. Configurar variables de entorno

```bash
cp .env .env.local
# Edita .env.local con tus credenciales locales
```

### 3. Levantar PostgreSQL con Docker

```bash
docker compose up -d
```

### 4. Ejecutar migraciones y cargar datos de prueba

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### 5. Arrancar el servidor de desarrollo

```bash
# Con Symfony CLI
symfony server:start

# O con el servidor integrado de PHP
php -S 127.0.0.1:8000 -t public
```

La API queda disponible en `http://127.0.0.1:8000`.

---

### Credenciales de fixtures

| Rol | Email | Usuario | Contraseña |
|---|---|---|---|
| Admin | `admin@bame.test` | `admin` | `Admin1234!` |
| Cliente | `cliente@bame.test` | `cliente` | `Cliente1234!` |

---

## 🔑 Variables de entorno

Copia `.env` a `.env.local` para desarrollo local. **Nunca versiones credenciales reales.**

```dotenv
# ── Symfony ──────────────────────────────────────────────────────────────────
APP_ENV=dev
APP_SECRET=change-me-in-production
APP_DEBUG=1

# ── Base de datos ─────────────────────────────────────────────────────────────
DATABASE_URL="postgresql://app:app@127.0.0.1:5432/bame?serverVersion=16&charset=utf8"

# ── CORS ──────────────────────────────────────────────────────────────────────
CORS_ALLOW_ORIGIN="http://localhost:4200"

# ── Supabase (Storage de imágenes) ────────────────────────────────────────────
SUPABASE_URL=https://<proyecto>.supabase.co
SUPABASE_ANON_KEY=sb_publishable_xxxx
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOiJI...
SUPABASE_FUNCTIONS_URL=https://<proyecto>.functions.supabase.co

# ── Auth ──────────────────────────────────────────────────────────────────────
BAME_TOKEN_TTL_DAYS=30

# ── Stripe ────────────────────────────────────────────────────────────────────
STRIPE_SECRET_KEY=sk_test_xxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxx

# ── Checkout ──────────────────────────────────────────────────────────────────
CHECKOUT_ALLOWED_ORIGINS="http://localhost:4200,http://127.0.0.1:4200"
```

| Variable | Descripción |
|---|---|
| `DATABASE_URL` | Conexión Doctrine a PostgreSQL |
| `CORS_ALLOW_ORIGIN` | Origen(es) permitidos. En producción: `https://asaderobame.com` |
| `SUPABASE_SERVICE_ROLE_KEY` | Clave con permisos completos — solo backend, nunca exponer |
| `BAME_TOKEN_TTL_DAYS` | Caducidad de los tokens Bearer (defecto: 30 días) |
| `STRIPE_SECRET_KEY` | `sk_test_*` en dev, `sk_live_*` en producción |
| `STRIPE_WEBHOOK_SECRET` | Secreto para validar la firma de los webhooks de Stripe |
| `CHECKOUT_ALLOWED_ORIGINS` | URLs permitidas para `successUrl`/`cancelUrl` de Stripe |

> **Producción:** `CHECKOUT_ALLOWED_ORIGINS` debe usar `https`, sin comodines ni slash final. El usuario de runtime de la base de datos (`bame_app`) debe tener permisos mínimos. Mantén un usuario separado para migraciones.

---

## 🔐 Autenticación

El sistema usa **tokens Bearer propios** almacenados en base de datos. No se usa JWT ni Supabase Auth.

**Flujo:**

```
POST /api/v1/auth/login
  → Genera un token aleatorio
  → Guarda su hash SHA-256 en la tabla personal_access_token
  → Devuelve el token en texto plano al cliente

Peticiones autenticadas:
  Authorization: Bearer <token>
  → BearerTokenAuthenticator hace SHA-256 del token recibido
  → Busca el hash en BD y valida caducidad
  → Inyecta el User en el contexto de seguridad de Symfony
```

**Endpoints de autenticación:**

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/api/v1/auth/register` | Registro (rate limit: 5/5min) |
| `POST` | `/api/v1/auth/login` | Login (rate limit: 10/5min) |
| `POST` | `/api/v1/auth/logout` | Invalida el token actual |
| `GET` | `/api/v1/auth/me` | Datos del usuario autenticado |
| `POST` | `/api/v1/auth/forgot-password` | Solicitar reset de contraseña (rate limit: 5/15min) |
| `POST` | `/api/v1/auth/reset-password` | Cambiar contraseña con token |

---

## 👥 Roles y permisos

```php
// src/Enum/Roles.php
ROLE_USER        // Cliente registrado
ROLE_RESPONSABLE // Personal del local — acceso operacional acotado a su local
ROLE_GERENTE     // Gerente del local — mismo alcance que responsable
ROLE_ADMIN       // Acceso global completo
```

**Matriz de acceso:**

| Recurso | `ROLE_USER` | `ROLE_RESPONSABLE` | `ROLE_GERENTE` | `ROLE_ADMIN` |
|---|:---:|:---:|:---:|:---:|
| Catálogo público | ✅ | ✅ | ✅ | ✅ |
| Crear pedido | ✅ | — | — | ✅ |
| Ver pedidos de su local | — | ✅ | ✅ | ✅ |
| Cambiar estado de pedido | — | ✅ | ✅ | ✅ |
| Gestionar productos | — | — | ✅ | ✅ |
| CRUD de locales | — | — | — | ✅ |
| Gestión de usuarios | — | — | — | ✅ |
| Métricas globales | — | — | — | ✅ |

> `ROLE_RESPONSABLE` y `ROLE_GERENTE` solo ven los recursos del local al que están asignados. `AdminLocalScopeResolver` aplica este filtro automáticamente.

---

## 📡 Referencia de endpoints

### Públicos

```
GET  /api/v1/locales                    Lista de locales activos
GET  /api/v1/locales/{id}               Detalle de un local
GET  /api/v1/productos                  Catálogo con filtros (category, search, allergens, minPrice, maxPrice…)
GET  /api/v1/productos/{id}             Detalle de un producto
GET  /api/v1/categorias                 Lista de categorías
GET  /api/v1/alergenos                  Lista de alérgenos
```

### Pedidos de cliente `ROLE_USER`

```
GET   /api/v1/pedidos                   Pedidos del usuario autenticado
POST  /api/v1/pedidos                   Crear pedido
GET   /api/v1/pedidos/{id}              Detalle de un pedido
PATCH /api/v1/pedidos/{id}/cancelar     Cancelar un pedido
```

### Administración de pedidos `ROLE_RESPONSABLE+`

```
GET   /api/v1/admin/pedidos             Pedidos del local (acotados por rol)
GET   /api/v1/admin/pedidos/{id}        Detalle completo
PATCH /api/v1/admin/pedidos/{id}/estado Cambiar estado
PATCH /api/v1/admin/pedidos/{id}/status (alias inglés)
```

### Productos admin `ROLE_GERENTE+`

```
GET    /api/v1/admin/productos          Productos del local con stock
POST   /api/v1/admin/productos          Crear producto
PATCH  /api/v1/admin/productos/{id}     Actualizar disponibilidad / stock
DELETE /api/v1/admin/productos/{id}     Eliminar producto
```

### Locales admin `ROLE_ADMIN`

```
GET    /api/v1/admin/locales            Lista completa de locales
POST   /api/v1/admin/locales            Crear local
PUT    /api/v1/admin/locales/{id}       Reemplazar local
PATCH  /api/v1/admin/locales/{id}       Actualizar campos del local
DELETE /api/v1/admin/locales/{id}       Eliminar local
```

### Usuarios y métricas `ROLE_ADMIN`

```
GET   /api/v1/admin/usuarios            Lista de usuarios
POST  /api/v1/admin/usuarios            Crear usuario con rol
PATCH /api/v1/admin/usuarios/{id}       Actualizar usuario / rol
GET   /api/v1/admin/metrics             Métricas globales del sistema
```

### Pagos Stripe

```
POST /api/payments/stripe/create-checkout-session   Crear sesión de pago
POST /api/payments/stripe/webhook                   Recibir eventos de Stripe
```

> **Aliases:** Todas las rutas están disponibles en inglés (`/products`, `/orders`, `/locals`) y en español (`/productos`, `/pedidos`, `/locales`) para compatibilidad con el frontend Angular.

---

## 💳 Pagos con Stripe

### Crear sesión de checkout

```http
POST /api/payments/stripe/create-checkout-session
Authorization: Bearer <token>
Content-Type: application/json
Idempotency-Key: <uuid>           (opcional — evita sesiones duplicadas)

{
  "localId": 1,
  "items": [
    { "productId": 1, "quantity": 2 },
    { "productId": 4, "quantity": 1 }
  ],
  "successUrl": "https://asaderobame.com/pago/ok",
  "cancelUrl":  "https://asaderobame.com/pago/cancelado"
}
```

**Respuesta:**

```json
{
  "success": true,
  "data": {
    "order": { "id": 42, "status": "pending", "total": "18.50", "...": "..." },
    "paymentMethod": "online",
    "paymentStatus": "pending",
    "requiresOnlinePayment": true,
    "checkoutUrl": "https://checkout.stripe.com/c/pay/cs_test_..."
  }
}
```

> El backend recalcula los precios desde la BD y valida el stock en `local_product` antes de crear la sesión. `successUrl` y `cancelUrl` solo se aceptan si coinciden exactamente con `CHECKOUT_ALLOWED_ORIGINS`.

### Webhook

```http
POST /api/payments/stripe/webhook
Stripe-Signature: t=...,v1=...
```

El backend verifica la firma con `STRIPE_WEBHOOK_SECRET`, confirma el pago, actualiza el pedido a `confirmed` y escribe en el ledger para evitar reprocesar el mismo evento. Configura el webhook en Stripe Dashboard para el evento `checkout.session.completed`.

### Tarjetas de prueba

| Caso | Número de tarjeta |
|---|---|
| Pago correcto | `4242 4242 4242 4242` |
| Requiere 3D Secure | `4000 0025 0000 3155` |
| Pago rechazado | `4000 0000 0000 9995` |

Usa cualquier fecha futura, CVC de 3 dígitos y código postal válido.

---

## 📦 Formato de respuesta JSON

Todas las respuestas siguen el mismo envoltorio:

**Éxito:**

```json
{
  "success": true,
  "data": { }
}
```

**Error:**

```json
{
  "success": false,
  "message": "Descripción del error",
  "errors": { "campo": ["mensaje"] },
  "code": "VALIDATION_ERROR",
  "error": {
    "message": "Descripción del error",
    "code": "VALIDATION_ERROR"
  }
}
```

Códigos de error habituales: `VALIDATION_ERROR`, `UNAUTHORIZED`, `FORBIDDEN`, `NOT_FOUND`, `TOO_MANY_REQUESTS`, `PAYMENT_ERROR`.

### Ciclo de vida de un pedido

```
pending → confirmed → preparing → ready → completed
   └────────────────────────────────────────→ cancelled
```

---

## 🧪 Tests

### Entorno de test

Configura una base de datos **local y aislada** para tests. Nunca apuntes la suite a Supabase ni a producción.

```dotenv
# .env.test.local
DATABASE_URL="postgresql://app:app@127.0.0.1:5432/bame_test?serverVersion=16&charset=utf8"
```

```bash
# Crear la base de datos de test y ejecutar migraciones
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```

### Ejecutar los tests

```bash
# Suite completa
php bin/phpunit

# Solo tests unitarios (sin BD)
php bin/phpunit tests/Unit

# Solo tests funcionales (requiere PostgreSQL)
php bin/phpunit tests/Functional

# Validar configuración antes de testear
php bin/console lint:yaml config
php bin/console lint:container
```

### Cobertura de tests

| Suite | Ficheros | Qué cubren |
|---|---|---|
| `Unit` | `ApiExceptionSubscriberTest`, `CheckoutOriginValidationTest`, `StripeConfigurationTest`… | Lógica de servicio sin BD |
| `Functional` | `AuthTest`, `OrderTest`, `PaymentTest`, `ProductTest`, `SecurityTest` | Flujos HTTP end-to-end |

---

## 🚢 Despliegue

### Docker

La imagen usa `php:8.3-cli` con el servidor integrado de PHP escuchando en el puerto `10000` (configurable con `$PORT`).

```bash
# Build local
docker build -t bame-api .

# Ejecutar
docker run -p 10000:10000 \
  -e DATABASE_URL="..." \
  -e STRIPE_SECRET_KEY="..." \
  bame-api
```

### Railway (producción)

El backend se despliega automáticamente en Railway desde la rama `main`. El `Dockerfile` gestiona la instalación de Composer, el *warm-up* de caché de Symfony y la exposición del puerto.

### Checklist pre-despliegue

- [ ] `APP_ENV=prod` y `APP_DEBUG=0`
- [ ] `APP_SECRET` cambiado por un valor seguro
- [ ] `DATABASE_URL` apuntando a Supabase con usuario de runtime (`bame_app`) de permisos mínimos
- [ ] `CORS_ALLOW_ORIGIN` con la URL exacta del frontend en producción (`https://asaderobame.com`)
- [ ] `STRIPE_SECRET_KEY=sk_live_*` y `STRIPE_WEBHOOK_SECRET` del entorno de producción de Stripe
- [ ] Webhook de Stripe configurado para `checkout.session.completed` apuntando al endpoint de producción
- [ ] Migraciones aplicadas: `php bin/console doctrine:migrations:migrate --env=prod`
- [ ] Grants de base de datos aplicados desde `supabase/grants/create_app_user.sql`

---

## 🔗 Repositorios relacionados

| Repositorio | Descripción |
|---|---|
| `bame/frontend` | SPA Angular 21 que consume esta API |
| `bame/docs` | Manual de usuario y documentación del proyecto |

---

<div align="center">

Desarrollado como Trabajo de Fin de Grado · Junio 2026

</div>
