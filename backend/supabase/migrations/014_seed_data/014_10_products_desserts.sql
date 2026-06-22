-- =====================================================
-- 014.10 PRODUCTS DESSERTS
-- =====================================================
-- Productos de la categoría Postres
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
-- Paparajotes (3 uds)
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Paparajotes (3 uds)',
    'Paparajotes (3 pcs)',
    'Paparajotes (3 pcs)',
    'Paparajotes (3 Stk)',
    'Paparajotes (3 pz)',
    'paparajotes',
    'Hojas de limonero rebozadas con azúcar y canela.',
    'Lemon tree leaves coated in batter, sugar and cinnamon.',
    'Feuilles de citronnier enrobées de pâte, sucre et cannelle.',
    'Zitronenblätter mit Teig, Zucker und Zimt.',
    'Foglie di limone pastellate con zucchero e cannella.',
    3.50,
    true,
    'available',
    true,
    10
),

-- =====================================================
-- Pan de Calatrava
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Pan de Calatrava',
    'Calatrava Bread Pudding',
    'Pain de Calatrava',
    'Calatrava-Brotpudding',
    'Budino di pane Calatrava',
    'calatrava-bread-pudding',
    'Postre murciano por excelencia.',
    'The most iconic dessert from Murcia.',
    'Le dessert murcien par excellence.',
    'Das bekannteste Dessert aus Murcia.',
    'Il dolce murciano per eccellenza.',
    3.80,
    true,
    'available',
    true,
    20
),

-- =====================================================
-- Tocino de Cielo
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Tocino de Cielo',
    'Tocino de Cielo',
    'Tocino de Cielo',
    'Tocino de Cielo',
    'Tocino de Cielo',
    'tocino-de-cielo',
    'Receta tradicional.',
    'Traditional Spanish egg-yolk dessert.',
    'Dessert espagnol traditionnel à base de jaune d’œuf.',
    'Traditionelles spanisches Eigelb-Dessert.',
    'Dolce tradizionale spagnolo a base di tuorlo d’uovo.',
    3.50,
    true,
    'available',
    false,
    30
),

-- =====================================================
-- Leche Frita
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Leche Frita',
    'Fried Milk',
    'Lait frit',
    'Frittierte Milch',
    'Latte fritto',
    'fried-milk',
    'Casera y cremosa.',
    'Homemade and creamy.',
    'Fait maison et crémeux.',
    'Hausgemacht und cremig.',
    'Fatto in casa e cremoso.',
    3.50,
    true,
    'available',
    false,
    40
),

-- =====================================================
-- Arroz con Leche
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Arroz con Leche',
    'Rice Pudding',
    'Riz au lait',
    'Milchreis',
    'Riso al latte',
    'rice-pudding',
    'Elaboración tradicional.',
    'Traditional recipe.',
    'Recette traditionnelle.',
    'Traditionelles Rezept.',
    'Ricetta tradizionale.',
    3.20,
    true,
    'available',
    false,
    50
),

-- =====================================================
-- Natillas Caseras
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Natillas Caseras',
    'Homemade Custard',
    'Crème anglaise maison',
    'Hausgemachter Vanillepudding',
    'Crema pasticcera fatta in casa',
    'homemade-custard',
    'Con galleta María.',
    'Served with Maria biscuit.',
    'Servi avec un biscuit María.',
    'Mit María-Keks serviert.',
    'Servito con biscotto María.',
    3.20,
    true,
    'available',
    false,
    60
),

-- =====================================================
-- Cuajada
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Cuajada',
    'Curd Dessert',
    'Caillé traditionnel',
    'Traditionelles Milchdessert',
    'Cagliata tradizionale',
    'curd-dessert',
    'Postre lácteo tradicional.',
    'Traditional dairy dessert.',
    'Dessert lacté traditionnel.',
    'Traditionelles Milchdessert.',
    'Dessert tradizionale a base di latte.',
    3.00,
    true,
    'available',
    false,
    70
),

-- =====================================================
-- Pastel de Cierva Dulce
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Pastel de Cierva Dulce',
    'Sweet Cierva Pie',
    'Pastel de Cierva sucré',
    'Süße Cierva-Pastete',
    'Pastel de Cierva dolce',
    'sweet-cierva-pie',
    'Versión repostera tradicional.',
    'Traditional sweet pastry version.',
    'Version pâtissière traditionnelle.',
    'Traditionelle süße Gebäckversion.',
    'Versione tradizionale da pasticceria.',
    3.90,
    true,
    'available',
    false,
    80
),

-- =====================================================
-- Tarta de Queso
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Tarta de Queso',
    'Cheesecake',
    'Gâteau au fromage',
    'Käsekuchen',
    'Cheesecake',
    'cheesecake',
    'Horneada al estilo casero.',
    'Baked in a homemade style.',
    'Cuite au four façon maison.',
    'Hausgemacht gebacken.',
    'Cotta al forno in stile casalingo.',
    5.00,
    true,
    'available',
    true,
    90
),

-- =====================================================
-- Torrija Casera
-- =====================================================
(
    (select id from categories where slug = 'desserts'),
    'Torrija Casera',
    'Traditional Torrija',
    'Pain perdu traditionnel',
    'Traditionelle Torrija',
    'Torrija tradizionale',
    'traditional-torrija',
    'Receta tradicional.',
    'Traditional recipe.',
    'Recette traditionnelle.',
    'Traditionelles Rezept.',
    'Ricetta tradizionale.',
    3.50,
    true,
    'available',
    false,
    100
);

-- =====================================================
-- END 014.10 PRODUCTS DESSERTS
-- =====================================================