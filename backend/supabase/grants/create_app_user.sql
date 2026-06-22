-- Revisar antes de ejecutar.
-- Ejecutar solo como administrador de la base de datos.
-- Rotar la contraseña tras aprovisionar el usuario.
-- No versionar una contraseña real en este archivo.
--
-- Usuario de runtime recomendado para el backend Symfony:
--   bame_app
-- Ajusta el nombre de la base si no es "postgres" antes de ejecutar.

DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM pg_roles
        WHERE rolname = 'bame_app'
    ) THEN
        CREATE ROLE bame_app LOGIN PASSWORD 'CHANGE_ME';
    END IF;
END
$$;

ALTER ROLE bame_app
    NOSUPERUSER
    NOCREATEDB
    NOCREATEROLE
    NOREPLICATION
    NOBYPASSRLS
    LOGIN;

GRANT CONNECT ON DATABASE postgres TO bame_app;
GRANT USAGE ON SCHEMA public TO bame_app;

GRANT SELECT, INSERT, UPDATE, DELETE ON TABLE
    public.users,
    public.categories,
    public.allergens,
    public.products,
    public.product_allergen,
    public.locals,
    public.local_product,
    public.orders,
    public.order_lines,
    public.personal_access_tokens,
    public.password_reset_tokens
TO bame_app;

GRANT USAGE, SELECT ON ALL SEQUENCES IN SCHEMA public TO bame_app;

-- Si el usuario de mantenimiento crea nuevas tablas/secuencias, rerun grants
-- o adapta los siguientes privilegios por objeto/owner segun el entorno.
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO bame_app;

ALTER DEFAULT PRIVILEGES IN SCHEMA public
    GRANT USAGE, SELECT ON SEQUENCES TO bame_app;
