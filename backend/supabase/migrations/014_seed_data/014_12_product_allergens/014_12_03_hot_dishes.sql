-- =====================================================
-- 014.12.03 PRODUCT ALLERGENS
-- CATEGORY: HOT DISHES
-- =====================================================
-- Alérgenos directos y posibles trazas de la
-- categoría Platos Calientes
-- =====================================================

-- =====================================================
-- Pelotas en Caldo
-- Alérgenos Directos:
-- Gluten, Huevos, Frutos de Cáscara
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
where p.slug = 'murcian-meatballs-in-broth'
and a.slug in (
    'gluten',
    'huevos',
    'frutos-cascara'
);

-- =====================================================
-- Trigo Guisado
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
where p.slug = 'wheat-stew'
and a.slug = 'gluten';

-- =====================================================
-- Empedrao
-- Alérgenos Directos:
-- Pescado
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
where p.slug = 'empedrao'
and a.slug = 'pescado';

-- =====================================================
-- Zarangollo
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
where p.slug = 'zarangollo'
and a.slug = 'huevos';

-- =====================================================
-- Pisto Murciano con Huevo
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
where p.slug = 'murcian-ratatouille-with-fried-egg'
and a.slug = 'huevos';

-- =====================================================
-- Albóndigas en Salsa
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
where p.slug = 'meatballs-in-sauce'
and a.slug in (
    'gluten',
    'huevos'
);

-- =====================================================
-- Lasaña Casera de Carne
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
where p.slug = 'homemade-beef-lasagna'
and a.slug in (
    'gluten',
    'lacteos',
    'huevos'
);

-- =====================================================
-- TRAZAS
-- =====================================================

-- =====================================================
-- Arroz y Conejo
-- Arroz con Pollo
-- Arroz y Costillejas
-- Posibles Trazas:
-- Apio, Sulfitos
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
where p.slug in (
    'rabbit-rice',
    'chicken-rice',
    'rice-with-pork-ribs'
)
and a.slug in (
    'apio',
    'sulfitos'
);

-- =====================================================
-- Michirones
-- Posibles Trazas:
-- Lácteos, Soja, Sulfitos
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
where p.slug = 'michirones'
and a.slug in (
    'lacteos',
    'soja',
    'sulfitos'
);

-- =====================================================
-- Pelotas en Caldo
-- Posibles Trazas:
-- Apio, Lácteos
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
where p.slug = 'murcian-meatballs-in-broth'
and a.slug in (
    'apio',
    'lacteos'
);

-- =====================================================
-- Magra con Tomate
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
where p.slug = 'pork-in-tomato-sauce'
and a.slug = 'sulfitos';

-- =====================================================
-- Callos Caseros
-- Posibles Trazas:
-- Lácteos, Soja, Sulfitos, Gluten
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
where p.slug = 'homemade-tripe-stew'
and a.slug in (
    'lacteos',
    'soja',
    'sulfitos',
    'gluten'
);

-- =====================================================
-- Trigo Guisado
-- Posibles Trazas:
-- Apio
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
where p.slug = 'wheat-stew'
and a.slug = 'apio';

-- =====================================================
-- Empedrao
-- Posibles Trazas:
-- Apio, Sulfitos
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
where p.slug = 'empedrao'
and a.slug in (
    'apio',
    'sulfitos'
);

-- =====================================================
-- Albóndigas en Salsa
-- Posibles Trazas:
-- Apio, Sulfitos
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
where p.slug = 'meatballs-in-sauce'
and a.slug in (
    'apio',
    'sulfitos'
);

-- =====================================================
-- Muslos al Ajillo
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
where p.slug = 'garlic-chicken-thighs'
and a.slug = 'sulfitos';

-- =====================================================
-- END 014.12.03 PRODUCT ALLERGENS HOT DISHES
-- =====================================================