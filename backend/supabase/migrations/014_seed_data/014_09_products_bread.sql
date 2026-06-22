-- =====================================================
-- 014.09 PRODUCTS BREAD
-- =====================================================
-- Productos de la categoría Panes
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
-- Pan Rústico Artesano
-- =====================================================
(
    (select id from categories where slug = 'bread'),
    'Pan Rústico Artesano',
    'Artisan Rustic Bread',
    'Pain rustique artisanal',
    'Handwerkliches Bauernbrot',
    'Pane rustico artigianale',
    'artisan-rustic-bread',
    'Hogaza individual.',
    'Individual artisan loaf.',
    'Pain artisanal individuel.',
    'Individuelles handwerkliches Brot.',
    'Pagnotta artigianale individuale.',
    1.20,
    true,
    'available',
    false,
    10
),

-- =====================================================
-- Barra de Pueblo
-- =====================================================
(
    (select id from categories where slug = 'bread'),
    'Barra de Pueblo',
    'Country Bread Loaf',
    'Pain de campagne',
    'Landbrot',
    'Pane casereccio',
    'country-bread-loaf',
    'Pan tradicional.',
    'Traditional bread.',
    'Pain traditionnel.',
    'Traditionelles Brot.',
    'Pane tradizionale.',
    1.10,
    true,
    'available',
    false,
    20
),

-- =====================================================
-- Pan de Pasas y Nueces
-- =====================================================
(
    (select id from categories where slug = 'bread'),
    'Pan de Pasas y Nueces',
    'Raisin and Walnut Bread',
    'Pain aux raisins et aux noix',
    'Rosinen-Walnuss-Brot',
    'Pane con uvetta e noci',
    'raisin-walnut-bread',
    'Especialidad artesanal.',
    'Artisan specialty.',
    'Spécialité artisanale.',
    'Handwerkliche Spezialität.',
    'Specialità artigianale.',
    3.90,
    true,
    'available',
    true,
    30
);

-- =====================================================
-- END 014.09 PRODUCTS BREAD
-- =====================================================