-- =====================================================
-- 014.12.04 PRODUCT ALLERGENS
-- CATEGORY: COLD DISHES
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Platos Fríos
-- =====================================================

-- =====================================================
-- Ensaladilla Rusa
-- Alérgenos Directos:
-- Huevos, Pescado
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
where p.slug = 'russian-salad'
and a.slug in (
    'huevos',
    'pescado'
);

-- =====================================================
-- Ensalada Murciana
-- Alérgenos Directos:
-- Huevos, Pescado
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
where p.slug = 'murcian-salad'
and a.slug in (
    'huevos',
    'pescado'
);

-- =====================================================
-- Marineras
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
where p.slug = 'marineras'
and a.slug in (
    'gluten',
    'huevos',
    'pescado'
);

-- =====================================================
-- Marineros
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
where p.slug = 'marineros'
and a.slug in (
    'gluten',
    'huevos',
    'pescado'
);

-- =====================================================
-- Bicicletas
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
where p.slug = 'bicicletas'
and a.slug in (
    'gluten',
    'huevos',
    'pescado'
);

-- =====================================================
-- Huevos Rellenos
-- Alérgenos Directos:
-- Huevos, Pescado
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
where p.slug = 'stuffed-eggs'
and a.slug in (
    'huevos',
    'pescado'
);

-- =====================================================
-- TRAZAS
-- =====================================================

-- =====================================================
-- Ensaladilla Rusa
-- Posibles Trazas:
-- Mostaza, Lácteos, Sulfitos
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
where p.slug = 'russian-salad'
and a.slug in (
    'mostaza',
    'lacteos',
    'sulfitos'
);

-- =====================================================
-- Marineros
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
where p.slug = 'marineros'
and a.slug = 'sulfitos';

-- =====================================================
-- END 014.12.04 PRODUCT ALLERGENS COLD DISHES
-- =====================================================