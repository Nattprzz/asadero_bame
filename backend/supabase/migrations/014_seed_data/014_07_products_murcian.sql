-- =====================================================
-- 014.07 PRODUCTS MURCIAN SPECIALTIES
-- =====================================================
-- Productos de la categoría Especialidades Murcianas
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
-- Pastel de Carne Murciano
-- =====================================================
(
    (select id from categories where slug = 'murcian-specialties'),
    'Pastel de Carne Murciano',
    'Murcian Meat Pie',
    'Tourte à la viande murcienne',
    'Murcianische Fleischpastete',
    'Torta salata murciana di carne',
    'murcian-meat-pie',
    'Hojaldre tradicional murciano.',
    'Traditional Murcian puff pastry filled with meat.',
    'Feuilleté traditionnel murcien garni de viande.',
    'Traditioneller murcianischer Blätterteig mit Fleischfüllung.',
    'Pasta sfoglia tradizionale murciana ripiena di carne.',
    3.50,
    true,
    'available',
    true,
    10
),

-- =====================================================
-- Pastel de Cierva
-- =====================================================
(
    (select id from categories where slug = 'murcian-specialties'),
    'Pastel de Cierva',
    'Cierva Pie',
    'Pastel de Cierva',
    'Cierva-Pastete',
    'Pastel de Cierva',
    'cierva-pie',
    'Dulce-salado típico del Mar Menor.',
    'Typical sweet-and-savoury pie from the Mar Menor area.',
    'Tourte sucrée-salée typique de la région du Mar Menor.',
    'Typische süß-herzhafte Pastete aus der Mar-Menor-Region.',
    'Torta dolce-salata tipica della zona del Mar Menor.',
    3.90,
    true,
    'available',
    false,
    20
),

-- =====================================================
-- Empanadilla de Atún
-- =====================================================
(
    (select id from categories where slug = 'murcian-specialties'),
    'Empanadilla de Atún',
    'Tuna Turnover',
    'Chausson au thon',
    'Thunfisch-Teigtasche',
    'Panzerotto al tonno',
    'tuna-turnover',
    'Tomate, huevo y atún.',
    'Tomato, egg and tuna.',
    'Tomate, œuf et thon.',
    'Tomate, Ei und Thunfisch.',
    'Pomodoro, uovo e tonno.',
    2.20,
    true,
    'available',
    false,
    30
),

-- =====================================================
-- Empanadilla de Carne
-- =====================================================
(
    (select id from categories where slug = 'murcian-specialties'),
    'Empanadilla de Carne',
    'Meat Turnover',
    'Chausson à la viande',
    'Fleisch-Teigtasche',
    'Panzerotto di carne',
    'meat-turnover',
    'Receta tradicional.',
    'Traditional recipe.',
    'Recette traditionnelle.',
    'Traditionelles Rezept.',
    'Ricetta tradizionale.',
    2.20,
    true,
    'available',
    false,
    40
),

-- =====================================================
-- Empanada Murciana
-- =====================================================
(
    (select id from categories where slug = 'murcian-specialties'),
    'Empanada Murciana',
    'Murcian Pie Slice',
    'Part de tourte murcienne',
    'Stück murcianische Pastete',
    'Porzione di empanada murciana',
    'murcian-pie-slice',
    'Porción individual.',
    'Individual portion.',
    'Portion individuelle.',
    'Einzelportion.',
    'Porzione individuale.',
    3.90,
    true,
    'available',
    false,
    50
),

-- =====================================================
-- Tortilla de Patatas
-- =====================================================
(
    (select id from categories where slug = 'murcian-specialties'),
    'Tortilla de Patatas',
    'Spanish Omelette',
    'Omelette espagnole',
    'Spanisches Omelett',
    'Frittata spagnola',
    'spanish-omelette',
    'Ración generosa.',
    'Generous portion.',
    'Portion généreuse.',
    'Großzügige Portion.',
    'Porzione abbondante.',
    3.50,
    true,
    'available',
    true,
    60
);

-- =====================================================
-- END 014.07 PRODUCTS MURCIAN SPECIALTIES
-- =====================================================