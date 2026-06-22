-- =====================================================
-- 014.04 PRODUCTS CROQUETTES
-- =====================================================
-- Productos de la categoría Croquetas
-- =====================================================

insert into products (
    category_id,
    name,
    name_en,
    name_fr,
    name_de,
    name_it,
    slug,
    description,
    desc_en,
    desc_fr,
    desc_de,
    desc_it,
    price,
    available,
    availability,
    featured,
    image_path,
    sort_order
)

values

-- =====================================================
-- Croquetas de Jamón Serrano
-- =====================================================
(
    (select id from categories where slug = 'croquettes'),
    'Croquetas de Jamón Serrano (6 uds)',
    'Serrano Ham Croquettes (6 pcs)',
    'Croquettes au jambon serrano (6 pcs)',
    'Serrano-Schinken-Kroketten (6 Stk)',
    'Crocchette al prosciutto serrano (6 pz)',
    'serrano-ham-croquettes',
    'Caseras y cremosas.',
    'Homemade and creamy.',
    'Faites maison et crémeuses.',
    'Hausgemacht und cremig.',
    'Fatte in casa e cremose.',
    5.50,
    true,
    'available',
    false,
    './ham_croquettes.jpg',
    10
),

-- =====================================================
-- Croquetas de Pollo Asado
-- =====================================================
(
    (select id from categories where slug = 'croquettes'),
    'Croquetas de Pollo Asado (6 uds)',
    'Roast Chicken Croquettes (6 pcs)',
    'Croquettes au poulet rôti (6 pcs)',
    'Brathähnchen-Kroketten (6 Stk)',
    'Crocchette di pollo arrosto (6 pz)',
    'roast-chicken-croquettes',
    'Elaboradas con nuestro pollo.',
    'Made with our signature roast chicken.',
    'Préparées avec notre poulet rôti maison.',
    'Hergestellt aus unserem Brathähnchen.',
    'Preparati con il nostro pollo arrosto.',
    5.50,
    true,
    'available',
    false,
    './chicken_croquettes.jpg',
    20
),

-- =====================================================
-- Croquetas de Bacalao
-- =====================================================
(
    (select id from categories where slug = 'croquettes'),
    'Croquetas de Bacalao (6 uds)',
    'Cod Croquettes (6 pcs)',
    'Croquettes de morue (6 pcs)',
    'Kabeljau-Kroketten (6 Stk)',
    'Crocchette di baccalà (6 pz)',
    'cod-croquettes',
    'Receta tradicional.',
    'Traditional recipe.',
    'Recette traditionnelle.',
    'Traditionelles Rezept.',
    'Ricetta tradizionale.',
    5.90,
    true,
    'available',
    false,
    './cod_croquettes.jpg',
    30
),

-- =====================================================
-- Croquetas de Carne Mechada
-- =====================================================
(
    (select id from categories where slug = 'croquettes'),
    'Croquetas de Carne Mechada (6 uds)',
    'Shredded Beef Croquettes (6 pcs)',
    'Croquettes au bœuf effiloché (6 pcs)',
    'Kroketten mit gezupftem Rindfleisch (6 Stk)',
    'Crocchette di manzo sfilacciato (6 pz)',
    'shredded-beef-croquettes',
    'Muy melosas.',
    'Very tender and creamy.',
    'Très fondantes et crémeuses.',
    'Sehr zart und cremig.',
    'Molto morbide e cremose.',
    5.90,
    true,
    'available',
    false,
    './shredded_beef_croquettes.jpg',
    40
);

-- =====================================================
-- END 014.04 PRODUCTS CROQUETTES
-- =====================================================