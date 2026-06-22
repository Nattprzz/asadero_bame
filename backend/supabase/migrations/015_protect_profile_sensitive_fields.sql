-- =====================================================
-- 015. PROTECT SENSITIVE PROFILE COLUMNS
-- =====================================================
--
-- Forward migration for databases where 013_rls.sql has already been applied.
-- RLS limits rows, not columns, so self-service updates require this trigger.
--

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


-- profiles_update_own remains responsible only for row ownership:
-- using (id = auth.uid()) / with check (id = auth.uid()).
-- The trigger above prevents normal users from changing protected columns.
