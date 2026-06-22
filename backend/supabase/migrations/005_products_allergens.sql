-- =====================================================
-- 005. PRODUCT ALLERGENS
-- =====================================================
--------------------------------------------------------

-- Relación N:M entre productos y alérgenos.

-- Ejemplos:

-- - Croquetas de jamón -> Gluten, Huevos, Lácteos
-- - Pan de Calatrava -> Gluten, Huevos, Lácteos
-- - Ensalada Murciana -> Huevos, Pescado
-----------------------------------------

-- Un producto puede contener varios alérgenos.
-- Un alérgeno puede aparecer en varios productos.
--------------------------------------------------

-- =====================================================

create table product_allergen (
product_id bigint not null
references products(id)
on delete cascade,


allergen_id bigint not null
    references allergens(id)
    on delete cascade,

primary key (
    product_id,
    allergen_id
)


);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_product_allergen_product
on product_allergen(product_id);

create index idx_product_allergen_allergen
on product_allergen(allergen_id);

-- =====================================================
-- SEED DATA
-- =====================================================
--------------------------------------------------------

-- Se recomienda insertar las relaciones una vez
-- cargados todos los productos.
--------------------------------

-- Ejemplo:

-- insert into product_allergen (
--     product_id,
--     allergen_id
-- )
-- values (
--     (select id from products
--      where slug = 'serrano-ham-croquettes'),
-----------------------------------------------

--     (select id from allergens
--      where slug = 'gluten')
-- );
-----

-- =====================================================
-- END SECTION 005
-- =====================================================
