-- =====================================================
-- 014.11 PRODUCTS DRINKS
-- =====================================================
-- Productos de la categoría Bebidas
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
    sort_order
)

values

-- =====================================================
-- Refresco Lata
-- =====================================================
(
    (select id from categories where slug = 'drinks'),
    'Refresco Lata',
    'Soft Drink Can',
    'Canette de soda',
    'Erfrischungsgetränk in Dose',
    'Bibita in lattina',
    'soft-drink-can',
    'Coca-Cola, Fanta, Aquarius.',
    'Coca-Cola, Fanta, Aquarius.',
    'Coca-Cola, Fanta, Aquarius.',
    'Coca-Cola, Fanta, Aquarius.',
    'Coca-Cola, Fanta, Aquarius.',
    1.50,
    true,
    'available',
    false,
    10
),

-- =====================================================
-- Refresco 1,5L
-- =====================================================
(
    (select id from categories where slug = 'drinks'),
    'Refresco 1,5L',
    'Soft Drink 1.5L',
    'Soda 1,5L',
    'Erfrischungsgetränk 1,5L',
    'Bibita 1,5L',
    'soft-drink-15l',
    'Formato familiar.',
    'Family-size bottle.',
    'Format familial.',
    'Familiengröße.',
    'Formato famiglia.',
    2.20,
    true,
    'available',
    false,
    20
),

-- =====================================================
-- Agua 0,5L
-- =====================================================
(
    (select id from categories where slug = 'drinks'),
    'Agua 0,5L',
    'Water 0.5L',
    'Eau 0,5L',
    'Wasser 0,5L',
    'Acqua 0,5L',
    'water-05l',
    'Agua mineral.',
    'Mineral water.',
    'Eau minérale.',
    'Mineralwasser.',
    'Acqua minerale.',
    1.00,
    true,
    'available',
    false,
    30
),

-- =====================================================
-- Agua 1,5L
-- =====================================================
(
    (select id from categories where slug = 'drinks'),
    'Agua 1,5L',
    'Water 1.5L',
    'Eau 1,5L',
    'Wasser 1,5L',
    'Acqua 1,5L',
    'water-15l',
    'Agua mineral.',
    'Mineral water.',
    'Eau minérale.',
    'Mineralwasser.',
    'Acqua minerale.',
    1.50,
    true,
    'available',
    false,
    40
),

-- =====================================================
-- Cerveza Lata
-- =====================================================
(
    (select id from categories where slug = 'drinks'),
    'Cerveza Lata',
    'Canned Beer',
    'Bière en canette',
    'Dosenbier',
    'Birra in lattina',
    'canned-beer',
    'Rubia nacional.',
    'National lager beer.',
    'Bière blonde nationale.',
    'Nationales Lagerbier.',
    'Birra chiara nazionale.',
    1.50,
    true,
    'available',
    false,
    50
);

-- =====================================================
-- END 014.11 PRODUCTS DRINKS
-- =====================================================