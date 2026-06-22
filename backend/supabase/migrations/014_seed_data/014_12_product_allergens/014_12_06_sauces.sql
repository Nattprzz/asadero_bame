-- =====================================================
-- 014.12.06 PRODUCT ALLERGENS
-- CATEGORY: SAUCES
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Salsas
-- =====================================================

-- =====================================================
-- Alioli de Ajo Asado
-- Alérgenos Directos:
-- Huevos, Lácteos
--
-- Nota:
-- Se marcan ambos porque en hostelería puede elaborarse
-- con base de mayonesa/huevina o lactonesa.
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
where p.slug = 'roasted-garlic-aioli'
and a.slug in (
    'huevos',
    'lacteos'
);

-- =====================================================
-- Alioli de Limón
-- Alérgenos Directos:
-- Huevos, Lácteos
--
-- Nota:
-- Se marcan ambos por el mismo motivo que el alioli
-- de ajo asado.
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
where p.slug = 'lemon-aioli'
and a.slug in (
    'huevos',
    'lacteos'
);

-- =====================================================
-- TRAZAS
-- =====================================================

-- =====================================================
-- Alioli de Limón
-- Posibles Trazas:
-- Sulfitos
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
cross join allergens a
where p.slug = 'lemon-aioli'
and a.slug = 'sulfitos';

-- =====================================================
-- Chimichurri Casero
-- Posibles Trazas:
-- Sulfitos
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
cross join allergens a
where p.slug = 'homemade-chimichurri'
and a.slug = 'sulfitos';

-- =====================================================
-- Salsa Brava Casera
-- Posibles Trazas:
-- Gluten, Apio
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
cross join allergens a
where p.slug = 'homemade-brava-sauce'
and a.slug in (
    'gluten',
    'apio'
);

-- =====================================================
-- Tomate Casero
-- Posibles Trazas:
-- Sulfitos
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
cross join allergens a
where p.slug = 'homemade-tomato-sauce'
and a.slug = 'sulfitos';

-- =====================================================
-- END 014.12.06 PRODUCT ALLERGENS SAUCES
-- =====================================================