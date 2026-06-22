-- =====================================================
-- 014.08 PRODUCTS SAUCES
-- =====================================================
-- Productos de la categoría Salsas
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
-- Alioli de Ajo Asado
-- =====================================================
(
    (select id from categories where slug = 'sauces'),
    'Alioli de Ajo Asado',
    'Roasted Garlic Aioli',
    'Aïoli à l’ail rôti',
    'Aioli mit geröstetem Knoblauch',
    'Aioli all’aglio arrosto',
    'roasted-garlic-aioli',
    'Elaboración casera.',
    'Homemade preparation.',
    'Préparation maison.',
    'Hausgemachte Zubereitung.',
    'Preparazione fatta in casa.',
    2.20,
    true,
    'available',
    false,
    10
),

-- =====================================================
-- Alioli de Limón
-- =====================================================
(
    (select id from categories where slug = 'sauces'),
    'Alioli de Limón',
    'Lemon Aioli',
    'Aïoli au citron',
    'Zitronen-Aioli',
    'Aioli al limone',
    'lemon-aioli',
    'Muy típico para arroces y pescados.',
    'Very typical with rice dishes and fish.',
    'Très typique avec les plats de riz et le poisson.',
    'Sehr typisch zu Reisgerichten und Fisch.',
    'Molto tipico con piatti di riso e pesce.',
    2.20,
    true,
    'available',
    false,
    20
),

-- =====================================================
-- Chimichurri Casero
-- =====================================================
(
    (select id from categories where slug = 'sauces'),
    'Chimichurri Casero',
    'Homemade Chimichurri',
    'Chimichurri maison',
    'Hausgemachtes Chimichurri',
    'Chimichurri fatto in casa',
    'homemade-chimichurri',
    'Perejil, ajo y aceite.',
    'Parsley, garlic and oil.',
    'Persil, ail et huile.',
    'Petersilie, Knoblauch und Öl.',
    'Prezzemolo, aglio e olio.',
    2.20,
    true,
    'available',
    false,
    30
),

-- =====================================================
-- Salsa Brava Casera
-- =====================================================
(
    (select id from categories where slug = 'sauces'),
    'Salsa Brava Casera',
    'Homemade Brava Sauce',
    'Sauce brava maison',
    'Hausgemachte Brava-Sauce',
    'Salsa brava fatta in casa',
    'homemade-brava-sauce',
    'Picante suave.',
    'Mildly spicy.',
    'Légèrement piquante.',
    'Leicht scharf.',
    'Leggermente piccante.',
    2.20,
    true,
    'available',
    false,
    40
),

-- =====================================================
-- Tomate Casero
-- =====================================================
(
    (select id from categories where slug = 'sauces'),
    'Tomate Casero',
    'Homemade Tomato Sauce',
    'Sauce tomate maison',
    'Hausgemachte Tomatensauce',
    'Salsa di pomodoro fatta in casa',
    'homemade-tomato-sauce',
    'Salsa de tomate tradicional.',
    'Traditional tomato sauce.',
    'Sauce tomate traditionnelle.',
    'Traditionelle Tomatensauce.',
    'Salsa di pomodoro tradizionale.',
    1.80,
    true,
    'available',
    false,
    50
);

-- =====================================================
-- END 014.08 PRODUCTS SAUCES
-- =====================================================