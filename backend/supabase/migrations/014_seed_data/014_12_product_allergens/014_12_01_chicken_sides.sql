-- =====================================================
-- 014.12.01 PRODUCT TRACES
-- CATEGORY: CHICKEN & SIDES
-- =====================================================
-- Posibles trazas de alérgenos para productos de la
-- categoría Pollo y Acompañamientos
-- =====================================================

-- =====================================================
-- Pollo Asado Entero / Medio Pollo / Pollo + Patatas
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('apio', 'mostaza', 'sulfitos', 'gluten')
where p.slug in (
    'whole-roast-chicken',
    'half-roast-chicken',
    'roast-chicken-with-bakery-potatoes'
);

-- =====================================================
-- Costillas Asadas
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('mostaza', 'soja', 'apio', 'gluten')
where p.slug = 'roasted-pork-ribs';

-- =====================================================
-- Patatas Asadas
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('sulfitos')
where p.slug = 'roasted-potatoes';

-- =====================================================
-- Patatas Fritas Caseras / Media Ración de Patatas
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('gluten', 'huevos', 'lacteos', 'pescado')
where p.slug in (
    'homemade-french-fries',
    'half-portion-of-potatoes'
);

-- =====================================================
-- Pimientos Asados / Verduras Asadas
-- =====================================================
-- Sin alérgenos directos ni trazas relevantes según receta base.

-- =====================================================
-- END 014.12.01 PRODUCT TRACES CHICKEN AND SIDES
-- =====================================================