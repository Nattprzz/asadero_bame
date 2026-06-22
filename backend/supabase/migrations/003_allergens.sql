-- =====================================================
-- 003. ALLERGENS
-- =====================================================
--------------------------------------------------------

-- Catálogo de alérgenos alimentarios.
-- Basado en los 14 alérgenos de declaración obligatoria
-- establecidos por la normativa europea.
-----------------------------------------

-- Cada producto puede tener varios alérgenos.
-- La relación se realiza mediante product_allergen.
----------------------------------------------------

-- =====================================================

create table allergens (
id bigint generated always as identity primary key,


-- Nombre multidioma
name text not null,
name_en text not null,
name_fr text not null,
name_de text not null,
name_it text not null,

-- Slug único
slug text not null unique,

-- Descripción multidioma
description text,
desc_en text,
desc_fr text,
desc_de text,
desc_it text,

-- Nombre interno del icono
icon_name text not null,

created_at timestamptz not null default now(),
updated_at timestamptz not null default now()


);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_allergens_slug
on allergens(slug);

-- =====================================================
-- SEED DATA
-- =====================================================

insert into allergens (
    name,
    name_en,
    name_fr,
    name_de,
    name_it,
    slug,
    icon_name,
    description,
    desc_en,
    desc_fr,
    desc_de,
    desc_it
)

values
-- Gluten
(
    'Gluten',
    'Gluten',
    'Gluten',
    'Gluten',
    'Glutine',
    'gluten',
    'gluten',
    'Cereales que contienen gluten y productos derivados.',
    'Cereals containing gluten and derived products.',
    'Céréales contenant du gluten et produits dérivés.',
    'Getreide mit Gluten und daraus hergestellte Produkte.',
    'Cereali contenenti glutine e prodotti derivati.'
),

-- Huevos
(
    'Huevos',
    'Eggs',
    'Œufs',
    'Eier',
    'Uova',
    'huevos',
    'eggs',
    'Huevos y productos elaborados a base de huevo.',
    'Eggs and egg-based products.',
    'Œufs et produits à base d''œufs.',
    'Eier und Produkte auf Eibasis.',
    'Uova e prodotti a base di uova.'
),

-- Lácteos
(
    'Lácteos',
    'Dairy',
    'Produits laitiers',
    'Milchprodukte',
    'Latticini',
    'lacteos',
    'dairy',
    'Leche y productos lácteos, incluida la lactosa.',
    'Milk and dairy products, including lactose.',
    'Lait et produits laitiers, y compris le lactose.',
    'Milch und Milchprodukte einschließlich Laktose.',
    'Latte e prodotti lattiero-caseari, compreso il lattosio.'
),

-- Pescado
(
    'Pescado',
    'Fish',
    'Poisson',
    'Fisch',
    'Pesce',
    'pescado',
    'fish',
    'Pescado y productos derivados del pescado.',
    'Fish and fish-derived products.',
    'Poisson et produits dérivés du poisson.',
    'Fisch und daraus hergestellte Produkte.',
    'Pesce e prodotti derivati dal pesce.'
),

-- Soja
(
    'Soja',
    'Soy',
    'Soja',
    'Soja',
    'Soia',
    'soja',
    'soy',
    'Soja y productos elaborados a base de soja.',
    'Soybeans and soy-based products.',
    'Soja et produits à base de soja.',
    'Soja und daraus hergestellte Produkte.',
    'Soia e prodotti a base di soia.'
),

-- Frutos de cáscara
(
    'Frutos de cáscara',
    'Tree Nuts',
    'Fruits à coque',
    'Schalenfrüchte',
    'Frutta a guscio',
    'frutos-cascara',
    'tree_nuts',
    'Frutos de cáscara y productos derivados.',
    'Tree nuts and derived products.',
    'Fruits à coque et produits dérivés.',
    'Schalenfrüchte und daraus hergestellte Produkte.',
    'Frutta a guscio e prodotti derivati.'
),

-- Crustáceos
(
    'Crustáceos',
    'Crustaceans',
    'Crustacés',
    'Krebstiere',
    'Crostacei',
    'crustaceos',
    'crustaceans',
    'Crustáceos y productos derivados.',
    'Crustaceans and derived products.',
    'Crustacés et produits dérivés.',
    'Krebstiere und daraus hergestellte Produkte.',
    'Crostacei e prodotti derivati.'
),

-- Dióxido de azufre y sulfitos
(
    'Dióxido de azufre y sulfitos',
    'Sulphur Dioxide and Sulphites',
    'Dioxyde de soufre et sulfites',
    'Schwefeldioxid und Sulfite',
    'Anidride solforosa e solfiti',
    'sulfitos',
    'sulfur_dioxide_sulphites',
    'Dióxido de azufre y sulfitos presentes en alimentos y bebidas.',
    'Sulphur dioxide and sulphites present in food and beverages.',
    'Dioxyde de soufre et sulfites présents dans les aliments et boissons.',
    'Schwefeldioxid und Sulfite in Lebensmitteln und Getränken.',
    'Anidride solforosa e solfiti presenti negli alimenti e nelle bevande.'
),

-- Moluscos
(
    'Moluscos',
    'Molluscs',
    'Mollusques',
    'Weichtiere',
    'Molluschi',
    'moluscos',
    'mollusks',
    'Moluscos y productos derivados.',
    'Molluscs and derived products.',
    'Mollusques et produits dérivés.',
    'Weichtiere und daraus hergestellte Produkte.',
    'Molluschi e prodotti derivati.'
),

-- Granos de sésamo
(
    'Granos de sésamo',
    'Sesame Seeds',
    'Graines de sésame',
    'Sesamsamen',
    'Semi di sesamo',
    'sesamo',
    'sesame_grains',
    'Semillas de sésamo y productos derivados.',
    'Sesame seeds and derived products.',
    'Graines de sésame et produits dérivés.',
    'Sesamsamen und daraus hergestellte Produkte.',
    'Semi di sesamo e prodotti derivati.'
),

-- Mostaza
(
    'Mostaza',
    'Mustard',
    'Moutarde',
    'Senf',
    'Senape',
    'mostaza',
    'mustard',
    'Mostaza y productos derivados.',
    'Mustard and derived products.',
    'Moutarde et produits dérivés.',
    'Senf und daraus hergestellte Produkte.',
    'Senape e prodotti derivati.'
),

-- Cacahuetes
(
    'Cacahuetes',
    'Peanuts',
    'Arachides',
    'Erdnüsse',
    'Arachidi',
    'cacahuetes',
    'peanuts',
    'Cacahuetes y productos derivados.',
    'Peanuts and derived products.',
    'Arachides et produits dérivés.',
    'Erdnüsse und daraus hergestellte Produkte.',
    'Arachidi e prodotti derivati.'
),

-- Apio
(
    'Apio',
    'Celery',
    'Céleri',
    'Sellerie',
    'Sedano',
    'apio',
    'celery',
    'Apio y productos derivados.',
    'Celery and derived products.',
    'Céleri et produits dérivés.',
    'Sellerie und daraus hergestellte Produkte.',
    'Sedano e prodotti derivati.'
),

-- Altramuces
(
    'Altramuces',
    'Lupins',
    'Lupins',
    'Lupinen',
    'Lupini',
    'altramuces',
    'lupins',
    'Altramuces y productos derivados.',
    'Lupins and derived products.',
    'Lupins et produits dérivés.',
    'Lupinen und daraus hergestellte Produkte.',
    'Lupini e prodotti derivati.'
);

-- =====================================================
-- END SECTION 003
-- =====================================================