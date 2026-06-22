# BAME

Proyecto principal del Asador de Pollo BAME, organizado como monorepo con dos aplicaciones:

- `frontend/`: SPA en Angular.
- `backend/`: API REST en Symfony.

Además incluye `docs/` con documentación del proyecto, `despliegue/` con material de despliegue y algunos scripts de apoyo en la raíz.

## Qué incluye

- Catálogo de productos y locales.
- Registro, login y gestión de usuario.
- Pedidos de recogida.
- Integración de pagos con Stripe.
- Paneles de administración por rol.

## Estructura

```text
.
├── backend/        # API REST Symfony + Doctrine + PostgreSQL
├── frontend/       # Aplicación Angular
├── docs/           # Documentación general
├── despliegue/     # Material de despliegue
└── extract_pdf.js  # Script auxiliar
```

## Requisitos generales

Para trabajar en el proyecto vas a necesitar, como mínimo:

- Node.js 22.x para el frontend.
- PHP 8.3+ y Composer para el backend.
- PostgreSQL 16.
- Docker, si vas a levantar servicios locales con contenedores.

## Puesta en marcha rápida

### Frontend

```bash
cd frontend
npm install
npm start
```

### Backend

```bash
cd backend
composer install
php bin/console doctrine:migrations:migrate
php -S 127.0.0.1:8000 -t public
```

## Documentación útil

- [README del backend](backend/README.md)
- [README del frontend](frontend/README.md)
- [Arquitectura del backend](backend/docs/ARCHITECTURE.md)

## Notas

- Este README de la raíz sirve como puerta de entrada al proyecto.
- La configuración real de entorno, scripts y despliegue está detallada en los README específicos de cada app.
