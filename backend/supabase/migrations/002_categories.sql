-- =====================================================
-- 002. CATEGORIES
-- =====================================================
--------------------------------------------------------

-- Categorías principales del catálogo.
-- Cada producto pertenece a una única categoría.
-------------------------------------------------

-- Categorías actuales:

-- - Pollo y acompañamientos
-- - Croquetas
-- - Platos calientes
-- - Platos fríos
-- - Especialidades murcianas
-- - Salsas
-- - Panes
-- - Postres
-- - Bebidas
------------

-- =====================================================

create table categories (
id bigint generated always as identity primary key,


-- Nombre multidioma
name text not null,
name_en text not null,
name_fr text not null,
name_de text not null,
name_it text not null,

-- Slug único para URLs y búsquedas
slug text not null unique,

-- Descripción multidioma
description text,
desc_en text,
desc_fr text,
desc_de text,
desc_it text,

-- Imagen representativa de la categoría
image_path text,

-- Visibilidad
active boolean not null default true,

-- Orden de visualización
sort_order integer not null default 0,

created_at timestamptz not null default now(),
updated_at timestamptz not null default now()


);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_categories_slug
on categories(slug);

create index idx_categories_active
on categories(active);

create index idx_categories_sort_order
on categories(sort_order);

-- =====================================================
-- SEED DATA
-- =====================================================

insert into categories (
    name,
    name_en,
    name_fr,
    name_de,
    name_it,
    slug,
    sort_order
)

values
-- Pollo y acompañamientos
(
    'Pollo y acompañamientos',
    'Chicken & Sides',
    'Poulet et accompagnements',
    'Hähnchen und Beilagen',
    'Pollo e contorni',
    'chicken-sides',
    10
),

-- Croquetas
(
    'Croquetas',
    'Croquettes',
    'Croquettes',
    'Kroketten',
    'Crocchette',
    'croquettes',
    20
),

-- Platos calientes
(
    'Platos calientes',
    'Hot Dishes',
    'Plats chauds',
    'Warme Gerichte',
    'Piatti caldi',
    'hot-dishes',
    30
),

-- Platos fríos
(
    'Platos fríos',
    'Cold Dishes',
    'Plats froids',
    'Kalte Gerichte',
    'Piatti freddi',
    'cold-dishes',
    40
),

-- Especialidades murcianas
(
    'Especialidades murcianas',
    'Murcian Specialties',
    'Spécialités murciennes',
    'Murcianische Spezialitäten',
    'Specialità murciane',
    'murcian-specialties',
    50
),

-- Salsas
(
    'Salsas',
    'Sauces',
    'Sauces',
    'Saucen',
    'Salse',
    'sauces',
    60
),

-- Panes
(
    'Panes',
    'Bread',
    'Pain',
    'Brot',
    'Pane',
    'bread',
    70
),

-- Postres
(
    'Postres',
    'Desserts',
    'Desserts',
    'Desserts',
    'Dolci',
    'desserts',
    80
),

-- Bebidas
(
    'Bebidas',
    'Drinks',
    'Boissons',
    'Getränke',
    'Bevande',
    'drinks',
    90
);

-- =====================================================
-- END SECTION 002
-- =====================================================