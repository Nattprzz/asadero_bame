-- =====================================================
-- 014.12.08 PRODUCT ALLERGENS
-- CATEGORY: DESSERTS
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Postres
-- =====================================================

-- =====================================================
-- Paparajotes
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
where p.slug = 'paparajotes'
and a.slug in (
    'gluten',
    'huevos',
    'lacteos'
);

-- =====================================================
-- Pan de Calatrava
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
where p.slug = 'calatrava-bread-pudding'
and a.slug in (
    'gluten',
    'huevos',
    'lacteos'
);

-- =====================================================
-- Tocino de Cielo
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
where p.slug = 'tocino-de-cielo'
and a.slug = 'huevos';

-- =====================================================
-- Leche Frita
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
where p.slug = 'fried-milk'
and a.slug in (
    'gluten',
    'lacteos',
    'huevos'
);

-- =====================================================
-- Arroz con Leche
-- Alérgenos Directos:
-- Lácteos
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
where p.slug = 'rice-pudding'
and a.slug = 'lacteos';

-- =====================================================
-- Natillas Caseras
-- Alérgenos Directos:
-- Lácteos, Huevos, Gluten
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
where p.slug = 'homemade-custard'
and a.slug in (
    'lacteos',
    'huevos',
    'gluten'
);

-- =====================================================
-- Cuajada
-- Alérgenos Directos:
-- Lácteos
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
where p.slug = 'curd-dessert'
and a.slug = 'lacteos';

-- =====================================================
-- Pastel de Cierva Dulce
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
where p.slug = 'sweet-cierva-pie'
and a.slug in (
    'gluten',
    'huevos',
    'lacteos'
);

-- =====================================================
-- Tarta de Queso
-- Alérgenos Directos:
-- Lácteos, Huevos, Gluten
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
where p.slug = 'cheesecake'
and a.slug in (
    'lacteos',
    'huevos',
    'gluten'
);

-- =====================================================
-- Torrija Casera
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
where p.slug = 'traditional-torrija'
and a.slug in (
    'gluten',
    'lacteos',
    'huevos'
);

-- =====================================================
-- TRAZAS
-- =====================================================

-- =====================================================
-- Tocino de Cielo
-- Posibles Trazas:
-- Gluten, Lácteos
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
where p.slug = 'tocino-de-cielo'
and a.slug in (
    'gluten',
    'lacteos'
);

-- =====================================================
-- Natillas Caseras
-- Posibles Trazas:
-- Soja
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
where p.slug = 'homemade-custard'
and a.slug = 'soja';

-- =====================================================
-- Cuajada
-- Posibles Trazas:
-- Frutos de Cáscara
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
where p.slug = 'curd-dessert'
and a.slug = 'frutos-cascara';

-- =====================================================
-- Torrija Casera
-- Posibles Trazas:
-- Sulfitos
--
-- Nota:
-- Solo aplicaría si se hace versión con vino.
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
where p.slug = 'traditional-torrija'
and a.slug = 'sulfitos';

-- =====================================================
-- END 014.12.08 PRODUCT ALLERGENS DESSERTS
-- =====================================================