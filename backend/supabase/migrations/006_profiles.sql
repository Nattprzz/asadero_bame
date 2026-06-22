-- =====================================================
-- 006. PROFILES
-- =====================================================
--
-- Perfiles de usuario de la aplicación.
--
-- Supabase Auth gestiona:
-- - email
-- - contraseña
-- - sesiones
-- - recuperación de contraseña
--
-- Esta tabla guarda los datos propios de BAME:
-- - nombre
-- - apellidos
-- - teléfono
-- - rol
-- - estado de la cuenta
--
-- La columna id está vinculada a auth.users(id).
--
-- =====================================================

create table profiles (
    id uuid primary key
        references auth.users(id)
        on delete cascade,

    email text not null unique,

    name text not null,

    surname text not null default '',

    phone text,

    role text not null default 'ROLE_USER'
        check (
            role in (
                'ROLE_USER',
                'ROLE_ADMIN',
                'ROLE_RESPONSABLE',
                'ROLE_GERENTE'
            )
        ),

    active boolean not null default true,

    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_profiles_email
    on profiles(email);

create index idx_profiles_role
    on profiles(role);

create index idx_profiles_active
    on profiles(active);

-- =====================================================
-- NOTES
-- =====================================================
--
-- No insertar usuarios directamente en auth.users.
--
-- Los usuarios deben crearse desde:
-- - Supabase Dashboard
-- - Supabase Auth API
-- - Backend Symfony usando Supabase Auth
--
-- Después de crear un usuario en auth.users,
-- se puede crear su perfil usando el mismo UUID.
--
-- Ejemplo:
--
-- insert into profiles (
--     id,
--     email,
--     name,
--     surname,
--     phone,
--     role
-- )
-- values (
--     'uuid-del-usuario-auth',
--     'admin@bame.test',
--     'Administrador',
--     'BAME',
--     '600000000',
--     'ROLE_ADMIN'
-- );
--
-- =====================================================
-- END SECTION 006
-- =====================================================