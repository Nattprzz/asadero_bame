-- =====================================================
-- 012. TRIGGERS
-- =====================================================
--
-- Triggers automáticos del sistema.
--
-- Responsabilidades:
--
-- - Mantener updated_at actualizado.
-- - Crear perfiles automáticamente.
--
-- =====================================================

-- =====================================================
-- CATEGORIES
-- =====================================================

create trigger categories_set_updated_at
before update on categories
for each row
execute function set_updated_at();

-- =====================================================
-- ALLERGENS
-- =====================================================

create trigger allergens_set_updated_at
before update on allergens
for each row
execute function set_updated_at();

-- =====================================================
-- PRODUCTS
-- =====================================================

create trigger products_set_updated_at
before update on products
for each row
execute function set_updated_at();

-- =====================================================
-- PROFILES
-- =====================================================

create trigger profiles_set_updated_at
before update on profiles
for each row
execute function set_updated_at();

-- =====================================================
-- LOCALS
-- =====================================================

create trigger locals_set_updated_at
before update on locals
for each row
execute function set_updated_at();

-- =====================================================
-- LOCAL PRODUCT
-- =====================================================

create trigger local_product_set_updated_at
before update on local_product
for each row
execute function set_updated_at();

-- =====================================================
-- ORDERS
-- =====================================================

create trigger orders_set_updated_at
before update on orders
for each row
execute function set_updated_at();

-- =====================================================
-- ORDER LINES
-- =====================================================

create trigger order_lines_set_updated_at
before update on order_lines
for each row
execute function set_updated_at();

-- =====================================================
-- AUTH → PROFILES
-- =====================================================
--
-- Cuando se crea un usuario en auth.users
-- se genera automáticamente un perfil.
--
-- =====================================================

create trigger on_auth_user_created
after insert on auth.users
for each row
execute function handle_new_user();

-- =====================================================
-- END SECTION 012
-- =====================================================