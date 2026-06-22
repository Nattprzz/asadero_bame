# BAME API Symfony

Backend REST para Asadero BAME. Usa Symfony, Doctrine ORM y PostgreSQL compatible con Supabase. La autenticacion se gestiona en Symfony con tokens Bearer propios, no con Supabase Auth.

## Estructura

- `src/Entity`: entidades Doctrine.
- `src/Repository`: consultas de lectura.
- `src/Controller/Api/V1`: endpoints REST.
- `src/Service`: reglas de negocio, validacion simple, presentacion JSON y auth.
- `src/Security`: autenticador Bearer.
- `src/EventListener/ApiExceptionListener.php`: errores JSON homogeneos.
- `migrations`: migraciones Doctrine para PostgreSQL.
- `src/DataFixtures`: datos realistas para demo.

## Variables de entorno

```dotenv
APP_ENV=dev
APP_SECRET=change-me
DATABASE_URL="postgresql://USER:PASSWORD@HOST:5432/postgres?serverVersion=16&charset=utf8"
CORS_ALLOW_ORIGIN="^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$|^https://.*\.vercel\.app$"
BAME_TOKEN_TTL_DAYS=30
```

En Supabase copia la cadena de conexion PostgreSQL desde Project Settings > Database. Si usas el pooler, conserva `serverVersion=16&charset=utf8` al final.

## Instalacion y arranque

```bash
composer install
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
php -S 127.0.0.1:8000 -t public
```

Credenciales de fixtures:

- Admin: `admin@bame.test` o `admin` / `Admin1234!`
- Cliente: `cliente@bame.test` o `cliente` / `Cliente1234!`

## Formato JSON

Respuesta correcta:

```json
{ "success": true, "data": {}, "message": "Texto opcional" }
```

Error:

```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Texto del error"
  }
}
```

## Endpoints principales

Publicos:

- `GET /api/v1/locales`
- `GET /api/v1/locales/{id}`
- `GET /api/v1/productos`
- `GET /api/v1/productos/{id}`
- `GET /api/v1/categorias`

Auth:

- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`
- `GET /api/v1/me`

Pedidos:

- `POST /api/v1/pedidos`
- `GET /api/v1/pedidos/mis-pedidos`
- `GET /api/v1/pedidos/{id}`

Admin:

- `GET /api/v1/admin/pedidos`
- `GET /api/v1/admin/pedidos/{id}`
- `PATCH /api/v1/admin/pedidos/{id}/estado`
- `POST /api/v1/admin/productos`
- `PATCH /api/v1/admin/productos/{id}`
- `DELETE /api/v1/admin/productos/{id}`
- `POST /api/v1/admin/locales`
- `PATCH /api/v1/admin/locales/{id}`

Las rutas antiguas en ingles siguen disponibles para no romper integraciones existentes.

## Pruebas rapidas con Postman

Login:

```http
POST http://127.0.0.1:8000/api/v1/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "Admin1234!"
}
```

Usa el token devuelto como `Authorization: Bearer <token>`.

Crear pedido como cliente:

```json
{
  "localId": 1,
  "notas": "Recoger a las 14:30",
  "items": [
    { "productoId": 1, "cantidad": 1 },
    { "productoId": 4, "cantidad": 2 }
  ]
}
```

## Angular

Configura la URL base del servicio HTTP como:

```ts
export const environment = {
  apiUrl: 'http://127.0.0.1:8000/api/v1'
};
```

En Vercel usa la URL real del backend y deja el dominio incluido por `CORS_ALLOW_ORIGIN`.

## Comprobar Supabase

1. Configura `DATABASE_URL` con la cadena de Supabase.
2. Ejecuta `php bin/console doctrine:query:sql "select version()"`.
3. Ejecuta `php bin/console doctrine:migrations:migrate`.
4. Comprueba en Supabase Table Editor que existen `users`, `locals`, `categories`, `products`, `orders` y `order_lines`.
