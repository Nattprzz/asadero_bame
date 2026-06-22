# Asadero BAME Backend Architecture

## Applied Patterns

- MVC: Symfony routes requests to controllers, Doctrine entities model persistence, and JSON responses are returned to Angular views.
- Repository: each aggregate has a Doctrine repository under `src/Repository` for data access. `ProductRepository::search()` contains advanced catalog filtering.
- DTO: API responses use DTO classes under `src/Dto`; controllers do not expose Doctrine entities directly.
- Service Layer: business operations live under `src/Service`, for example `OrderService`, `ProductService`, `AuthService` and `AdminMetricsService`.
- Factory: `ApiResponseFactory` centralizes JSON success, validation and error response format.

## Menu Data Source

`AppFixtures` reads the official menu from the repository root file `bame.md`. Product and category fixtures are idempotent by slug and do not create products outside that file. Product images are resolved only against existing files in `public/uploads/products`; missing images remain `null`.

## Security Decisions

- Stateless Bearer JWT signed with HS256 and expiration.
- Passwords are hashed through Symfony password hashers.
- Password reset tokens are stored only as SHA-256 hashes and expire.
- Public endpoints are limited to auth entry points and catalog reads.
- Admin mutations are protected with `ROLE_ADMIN`.
- Sensitive fields such as passwords and reset token hashes are never serialized.

## Order Lines

`OrderLine` is exposed as a read resource, but creation and modification are handled through `OrderService` and `/api/v1/orders`. This keeps order totals consistent.
