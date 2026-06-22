-- =====================================================
-- BAME DATABASE SCHEMA
-- PostgreSQL / Supabase
-- Version: 1.0.0
-- =====================================================
--------------------------------------------------------

-- SECTION: 001. DROP EXISTING OBJECTS

-- Elimina todos los objetos de la base de datos en orden
-- seguro para evitar conflictos por claves foráneas.
-----------------------------------------------------

-- =====================================================

-- =====================================================
-- TABLES
-- =====================================================

drop table if exists order_lines cascade;
drop table if exists orders cascade;

drop table if exists local_product cascade;
drop table if exists locals cascade;

drop table if exists product_allergen cascade;
drop table if exists product_images cascade;

drop table if exists products cascade;
drop table if exists allergens cascade;
drop table if exists categories cascade;

drop table if exists profiles cascade;

-- =====================================================
-- FUNCTIONS
-- =====================================================

drop function if exists set_updated_at() cascade;
drop function if exists handle_new_user() cascade;
drop function if exists is_admin() cascade;
drop function if exists has_role(text) cascade;

-- =====================================================
-- TRIGGERS
-- =====================================================

drop trigger if exists on_auth_user_created on auth.users;

-- =====================================================
-- STORAGE OBJECTS
-- =====================================================

delete from storage.objects
where bucket_id in (
'products',
'categories',
'allergens',
'locals'
);

-- =====================================================
-- STORAGE BUCKETS
-- =====================================================

delete from storage.buckets
where id in (
'products',
'categories',
'allergens',
'locals'
);

-- =====================================================
-- DISABLE RLS
-- =====================================================

alter table if exists profiles disable row level security;
alter table if exists categories disable row level security;
alter table if exists products disable row level security;
alter table if exists allergens disable row level security;
alter table if exists locals disable row level security;
alter table if exists orders disable row level security;
alter table if exists order_lines disable row level security;
alter table if exists product_allergen disable row level security;
alter table if exists local_product disable row level security;
alter table if exists product_images disable row level security;

-- =====================================================
-- END SECTION 001
-- =====================================================