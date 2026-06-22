-- =====================================================
-- 014.12.07 PRODUCT ALLERGENS
-- CATEGORY: BREAD
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Panes
-- =====================================================

-- =====================================================
-- Pan Rústico Artesano
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
where p.slug = 'artisan-rustic-bread'
and a.slug = 'gluten';

-- =====================================================
-- Barra de Pueblo
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
where p.slug = 'country-bread-loaf'
and a.slug = 'gluten';

-- =====================================================
-- Pan de Pasas y Nueces
-- Alérgenos Directos:
-- Gluten, Frutos de Cáscara
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
where p.slug = 'raisin-walnut-bread'
and a.slug in (
    'gluten',
    'frutos-cascara'
);

-- =====================================================
-- TRAZAS
-- =====================================================

-- =====================================================
-- Pan Rústico Artesano
-- Posibles Trazas:
-- Sésamo, Soja, Frutos de Cáscara
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
where p.slug = 'artisan-rustic-bread'
and a.slug in (
    'sesamo',
    'soja',
    'frutos-cascara'
);

-- =====================================================
-- Barra de Pueblo
-- Posibles Trazas:
-- Sésamo, Soja, Frutos de Cáscara
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
where p.slug = 'country-bread-loaf'
and a.slug in (
    'sesamo',
    'soja',
    'frutos-cascara'
);

-- =====================================================
-- Pan de Pasas y Nueces
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
where p.slug = 'raisin-walnut-bread'
and a.slug = 'sulfitos';

-- =====================================================
-- END 014.12.07 PRODUCT ALLERGENS BREAD
-- =====================================================