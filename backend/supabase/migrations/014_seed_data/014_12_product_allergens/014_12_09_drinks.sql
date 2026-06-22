-- =====================================================
-- 014.12.09 PRODUCT ALLERGENS
-- CATEGORY: DRINKS
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Bebidas
-- =====================================================

-- =====================================================
-- Refresco Lata
-- Alérgenos Directos:
-- Ninguno
--
-- Posibles Trazas:
-- Ninguna relevante
-- =====================================================

-- No insert required.

-- =====================================================
-- Refresco 1,5L
-- Alérgenos Directos:
-- Ninguno
--
-- Posibles Trazas:
-- Ninguna relevante
-- =====================================================

-- No insert required.

-- =====================================================
-- Agua 0,5L
-- Alérgenos Directos:
-- Ninguno
--
-- Posibles Trazas:
-- Ninguna relevante
-- =====================================================

-- No insert required.

-- =====================================================
-- Agua 1,5L
-- Alérgenos Directos:
-- Ninguno
--
-- Posibles Trazas:
-- Ninguna relevante
-- =====================================================

-- No insert required.

-- =====================================================
-- Cerveza Lata
-- Alérgenos Directos:
-- Gluten
-- =====================================================

insert into product_allergen (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
cross join allergens a
where p.slug = 'canned-beer'
and a.slug = 'gluten';

-- =====================================================
-- END 014.12.09 PRODUCT ALLERGENS DRINKS
-- =====================================================