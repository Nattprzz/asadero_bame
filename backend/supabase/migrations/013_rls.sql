-- =====================================================
-- 013. ROW LEVEL SECURITY
-- =====================================================

alter table categories enable row level security;
alter table allergens enable row level security;
alter table products enable row level security;
alter table product_allergen enable row level security;
alter table profiles enable row level security;
alter table locals enable row level security;
alter table local_product enable row level security;
alter table orders enable row level security;
alter table order_lines enable row level security;

-- =====================================================
-- PUBLIC CATALOG READ
-- =====================================================

create policy categories_public_read
on categories
for select
to anon, authenticated
using (active = true);

create policy allergens_public_read
on allergens
for select
to anon, authenticated
using (true);

create policy products_public_read
on products
for select
to anon, authenticated
using (
    available = true
    and availability <> 'hidden'
);

create policy product_allergen_public_read
on product_allergen
for select
to anon, authenticated
using (true);

create policy locals_public_read
on locals
for select
to anon, authenticated
using (active = true);

create policy local_product_public_read
on local_product
for select
to anon, authenticated
using (true);

-- =====================================================
-- PROFILES
-- =====================================================

-- Column protection for self-service profile updates.
--
-- RLS policies can restrict rows but cannot restrict which columns are changed.
-- This trigger therefore applies a whitelist to authenticated non-admin users.
-- Administrators and privileged backend connections keep their existing update
-- capabilities. The guard trigger name sorts before profiles_set_updated_at, so
-- users cannot submit updated_at while the timestamp trigger can still manage it.
create or replace function protect_profile_user_updates()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
    -- Supabase administrators are authorized by profiles_admin_update_all.
    -- Connections without auth.uid() are privileged backend/database sessions.
    if auth.uid() is null or is_admin() then
        return new;
    end if;

    -- updated_at is exclusively maintained by profiles_set_updated_at.
    if new.updated_at is distinct from old.updated_at then
        raise exception using
            errcode = '42501',
            message = 'updated_at cannot be changed directly';
    end if;

    -- Current user-editable columns: name, surname and phone.
    -- Every other current or future column is protected by default, including
    -- id, email, role, active, status (if added) and created_at. If username,
    -- avatar_url or backdrop_url are added later, they must be explicitly added
    -- to this whitelist before users can update them.
    if (to_jsonb(new) - array['name', 'surname', 'phone', 'updated_at'])
        is distinct from
       (to_jsonb(old) - array['name', 'surname', 'phone', 'updated_at']) then
        raise exception using
            errcode = '42501',
            message = 'protected profile columns cannot be changed';
    end if;

    return new;
end;
$$;

drop trigger if exists profiles_guard_sensitive_fields on profiles;

create trigger profiles_guard_sensitive_fields
before update on profiles
for each row
execute function protect_profile_user_updates();


-- This policy restricts users to their own row. Column-level protection is
-- enforced by profiles_guard_sensitive_fields above.
create policy profiles_read_own
on profiles
for select
to authenticated
using (id = auth.uid());

create policy profiles_update_own
on profiles
for update
to authenticated
using (id = auth.uid())
with check (id = auth.uid());

create policy profiles_admin_read_all
on profiles
for select
to authenticated
using (is_admin());

create policy profiles_admin_update_all
on profiles
for update
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- ADMIN CATALOG MANAGEMENT
-- =====================================================

create policy categories_admin_all
on categories
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy allergens_admin_all
on allergens
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy products_admin_all
on products
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy product_allergen_admin_all
on product_allergen
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy locals_admin_all
on locals
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy local_product_admin_all
on local_product
for all
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- ORDERS
-- =====================================================

create policy orders_insert_own
on orders
for insert
to authenticated
with check (user_id = auth.uid());

create policy orders_read_own
on orders
for select
to authenticated
using (user_id = auth.uid());

create policy orders_admin_read_all
on orders
for select
to authenticated
using (is_admin());

create policy orders_admin_update_all
on orders
for update
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- ORDER LINES
-- =====================================================

create policy order_lines_insert_own_order
on order_lines
for insert
to authenticated
with check (
    exists (
        select 1
        from orders o
        where o.id = order_lines.order_id
        and o.user_id = auth.uid()
    )
);

create policy order_lines_read_own_order
on order_lines
for select
to authenticated
using (
    exists (
        select 1
        from orders o
        where o.id = order_lines.order_id
        and o.user_id = auth.uid()
    )
);

create policy order_lines_admin_read_all
on order_lines
for select
to authenticated
using (is_admin());

create policy order_lines_admin_update_all
on order_lines
for update
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- END SECTION 013
-- =====================================================
