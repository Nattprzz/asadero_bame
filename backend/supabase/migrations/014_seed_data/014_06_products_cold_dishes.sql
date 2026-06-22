-- =====================================================
-- 014.06 PRODUCTS COLD DISHES
-- =====================================================
-- Productos de la categoría Platos Fríos
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
-- Ensaladilla Rusa
-- =====================================================
(
    (select id from categories where slug = 'cold-dishes'),
    'Ensaladilla Rusa',
    'Russian Salad',
    'Salade russe',
    'Russischer Salat',
    'Insalata russa',
    'russian-salad',
    'Clásico imprescindible de cualquier asadero murciano.',
    'A must-have classic in any Murcian rotisserie.',
    'Un classique incontournable de toute rôtisserie murcienne.',
    'Ein unverzichtbarer Klassiker jeder Rotisserie in Murcia.',
    'Un classico immancabile di ogni rosticceria murciana.',
    4.90,
    true,
    'available',
    true,
    10
),

-- =====================================================
-- Ensalada Murciana
-- =====================================================
(
    (select id from categories where slug = 'cold-dishes'),
    'Ensalada Murciana',
    'Murcian Salad',
    'Salade murcienne',
    'Murcianischer Salat',
    'Insalata murciana',
    'murcian-salad',
    'Tomate, atún, huevo y aceitunas negras.',
    'Tomato, tuna, egg and black olives.',
    'Tomate, thon, œuf et olives noires.',
    'Tomaten, Thunfisch, Ei und schwarze Oliven.',
    'Pomodoro, tonno, uovo e olive nere.',
    4.90,
    true,
    'available',
    true,
    20
),

-- =====================================================
-- Marineras (2 uds)
-- =====================================================
(
    (select id from categories where slug = 'cold-dishes'),
    'Marineras (2 uds)',
    'Marineras (2 pcs)',
    'Marineras (2 pcs)',
    'Marineras (2 Stk)',
    'Marineras (2 pz)',
    'marineras',
    'Rosquilla, ensaladilla y anchoa.',
    'Bread ring topped with Russian salad and anchovy.',
    'Rosquilla garnie de salade russe et d’anchois.',
    'Gebäckring mit russischem Salat und Sardelle.',
    'Ciambellina con insalata russa e acciuga.',
    3.20,
    true,
    'available',
    false,
    30
),

-- =====================================================
-- Marineros (2 uds)
-- =====================================================
(
    (select id from categories where slug = 'cold-dishes'),
    'Marineros (2 uds)',
    'Marineros (2 pcs)',
    'Marineros (2 pcs)',
    'Marineros (2 Stk)',
    'Marineros (2 pz)',
    'marineros',
    'Rosquilla, ensaladilla y boquerón.',
    'Bread ring topped with Russian salad and anchovy fillet.',
    'Rosquilla garnie de salade russe et de filet d’anchois.',
    'Gebäckring mit russischem Salat und Sardellenfilet.',
    'Ciambellina con insalata russa e filetto di acciuga.',
    3.20,
    true,
    'available',
    false,
    40
),

-- =====================================================
-- Bicicletas (2 uds)
-- =====================================================
(
    (select id from categories where slug = 'cold-dishes'),
    'Bicicletas (2 uds)',
    'Bicicletas (2 pcs)',
    'Bicicletas (2 pcs)',
    'Bicicletas (2 Stk)',
    'Bicicletas (2 pz)',
    'bicicletas',
    'Rosquilla con ensaladilla.',
    'Bread ring topped with Russian salad.',
    'Rosquilla garnie de salade russe.',
    'Gebäckring mit russischem Salat.',
    'Ciambellina con insalata russa.',
    2.80,
    true,
    'available',
    false,
    50
),

-- =====================================================
-- Huevos Rellenos
-- =====================================================
(
    (select id from categories where slug = 'cold-dishes'),
    'Huevos Rellenos',
    'Stuffed Eggs',
    'Œufs farcis',
    'Gefüllte Eier',
    'Uova ripiene',
    'stuffed-eggs',
    'Atún, mayonesa y huevo.',
    'Eggs stuffed with tuna and mayonnaise.',
    'Œufs farcis au thon et à la mayonnaise.',
    'Mit Thunfisch und Mayonnaise gefüllte Eier.',
    'Uova ripiene di tonno e maionese.',
    4.50,
    true,
    'available',
    false,
    60
);

-- =====================================================
-- END 014.06 PRODUCTS COLD DISHES
-- =====================================================