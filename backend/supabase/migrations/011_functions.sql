-- =====================================================
-- 011. FUNCTIONS
-- =====================================================
--
-- Funciones auxiliares utilizadas por triggers,
-- políticas RLS y lógica de negocio.
--
-- =====================================================

-- =====================================================
-- set_updated_at()
-- =====================================================
--
-- Actualiza automáticamente updated_at
-- antes de cada UPDATE.
--
-- =====================================================

create or replace function set_updated_at()
returns trigger
language plpgsql
as $$
begin
    new.updated_at = now();
    return new;
end;
$$;

-- =====================================================
-- has_role()
-- =====================================================
--
-- Comprueba si el usuario autenticado
-- tiene un rol concreto.
--
-- =====================================================

create or replace function has_role(
    required_role text
)
returns boolean
language sql
stable
security definer
set search_path = public
as $$
    select exists (
        select 1
        from profiles p
        where p.id = auth.uid()
        and p.role = required_role
        and p.active = true
    );
$$;

-- =====================================================
-- is_admin()
-- =====================================================
--
-- Comprueba si el usuario actual
-- es administrador.
--
-- =====================================================

create or replace function is_admin()
returns boolean
language sql
stable
security definer
set search_path = public
as $$
    select has_role('ROLE_ADMIN');
$$;

-- =====================================================
-- handle_new_user()
-- =====================================================
--
-- Se ejecuta automáticamente cuando
-- Supabase Auth crea un usuario.
--
-- Crea el perfil asociado.
--
-- =====================================================

create or replace function handle_new_user()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin

    insert into profiles (
        id,
        email,
        name,
        surname,
        role,
        active
    )
    values (
        new.id,
        new.email,
        coalesce(
            new.raw_user_meta_data ->> 'name',
            'Usuario'
        ),
        '',
        'ROLE_USER',
        true
    );

    return new;

end;
$$;

-- =====================================================
-- END SECTION 011
-- =====================================================