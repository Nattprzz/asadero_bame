-- =====================================================
-- 014.12.05 PRODUCT ALLERGENS
-- CATEGORY: MURCIAN SPECIALTIES
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Especialidades Murcianas
-- =====================================================

-- =====================================================
-- Pastel de Carne Murciano
-- Alérgenos Directos:
-- Gluten, Huevos
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
where p.slug = 'murcian-meat-pie'
and a.slug in (
    'gluten',
    'huevos'
);

-- =====================================================
-- Pastel de Cierva
-- Alérgenos Directos:
-- Gluten, Huevos, Lácteos
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
where p.slug = 'cierva-pie'
and a.slug in (
    'gluten',
    'huevos',
    'lacteos'
);

-- =====================================================
-- Empanadilla de Atún
-- Alérgenos Directos:
-- Gluten, Huevos, Pescado
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
where p.slug = 'tuna-turnover'
and a.slug in (
    'gluten',
    'huevos',
    'pescado'
);

-- =====================================================
-- Empanadilla de Carne
-- Alérgenos Directos:
-- Gluten, Huevos
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
where p.slug = 'meat-turnover'
and a.slug in (
    'gluten',
    'huevos'
);

-- =====================================================
-- Empanada Murciana
-- Alérgenos Directos:
-- Gluten, Pescado, Huevos, Frutos de Cáscara
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
where p.slug = 'murcian-pie-slice'
and a.slug in (
    'gluten',
    'pescado',
    'huevos',
    'frutos-cascara'
);

-- =====================================================
-- Tortilla de Patatas
-- Alérgenos Directos:
-- Huevos
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
where p.slug = 'spanish-omelette'
and a.slug = 'huevos';

-- =====================================================
-- TRAZAS
-- =====================================================

-- =====================================================
-- Pastel de Carne Murciano
-- Posibles Trazas:
-- Lácteos, Soja, Apio
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
where p.slug = 'murcian-meat-pie'
and a.slug in (
    'lacteos',
    'soja',
    'apio'
);

-- =====================================================
-- Empanadilla de Carne
-- Posibles Trazas:
-- Sulfitos, Soja, Gluten
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
where p.slug = 'meat-turnover'
and a.slug in (
    'sulfitos',
    'soja',
    'gluten'
);

-- =====================================================
-- END 014.12.05 PRODUCT ALLERGENS MURCIAN SPECIALTIES
-- =====================================================