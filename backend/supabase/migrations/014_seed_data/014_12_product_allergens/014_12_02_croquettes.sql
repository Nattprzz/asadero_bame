-- =====================================================
-- 014.12.02 PRODUCT ALLERGENS
-- CATEGORY: CROQUETTES
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Croquetas
-- =====================================================

-- =====================================================
-- Croquetas de Jamón Serrano
-- Alérgenos Directos:
-- Gluten, Lácteos, Huevos
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
where p.slug = 'serrano-ham-croquettes'
and a.slug in (
    'gluten',
    'lacteos',
    'huevos'
);

-- =====================================================
-- Croquetas de Pollo Asado
-- Alérgenos Directos:
-- Gluten, Lácteos, Huevos
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
where p.slug = 'roast-chicken-croquettes'
and a.slug in (
    'gluten',
    'lacteos',
    'huevos'
);

-- =====================================================
-- Croquetas de Bacalao
-- Alérgenos Directos:
-- Gluten, Lácteos, Huevos, Pescado
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
where p.slug = 'cod-croquettes'
and a.slug in (
    'gluten',
    'lacteos',
    'huevos',
    'pescado'
);

-- =====================================================
-- Croquetas de Carne Mechada
-- Alérgenos Directos:
-- Gluten, Lácteos, Huevos
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
where p.slug = 'shredded-beef-croquettes'
and a.slug in (
    'gluten',
    'lacteos',
    'huevos'
);

-- =====================================================
-- TRAZAS
-- =====================================================

-- =====================================================
-- Croquetas de Pollo Asado
-- Posibles Trazas:
-- Apio, Mostaza, Sulfitos
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
where p.slug = 'roast-chicken-croquettes'
and a.slug in (
    'apio',
    'mostaza',
    'sulfitos'
);

-- =====================================================
-- Croquetas de Carne Mechada
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
where p.slug = 'shredded-beef-croquettes'
and a.slug = 'sulfitos';

-- =====================================================
-- END 014.12.02 PRODUCT ALLERGENS CROQUETTES
-- =====================================================