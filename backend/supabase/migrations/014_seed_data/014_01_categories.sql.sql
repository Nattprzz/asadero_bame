-- =====================================================
-- 014.01 CATEGORIES SEED DATA
-- =====================================================
-- Categorías principales del catálogo BAME
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

-- =====================================================
-- Pollo y Acompañamientos
-- =====================================================
(
    'Pollo y acompañamientos',
    'Chicken & Sides',
    'Poulet et accompagnements',
    'Hähnchen und Beilagen',
    'Pollo e contorni',
    'chicken-sides',
    10
),

-- =====================================================
-- Croquetas
-- =====================================================
(
    'Croquetas',
    'Croquettes',
    'Croquettes',
    'Kroketten',
    'Crocchette',
    'croquettes',
    20
),

-- =====================================================
-- Platos Calientes
-- =====================================================
(
    'Platos calientes',
    'Hot Dishes',
    'Plats chauds',
    'Warme Gerichte',
    'Piatti caldi',
    'hot-dishes',
    30
),

-- =====================================================
-- Platos Fríos
-- =====================================================
(
    'Platos fríos',
    'Cold Dishes',
    'Plats froids',
    'Kalte Gerichte',
    'Piatti freddi',
    'cold-dishes',
    40
),

-- =====================================================
-- Especialidades Murcianas
-- =====================================================
(
    'Especialidades murcianas',
    'Murcian Specialties',
    'Spécialités murciennes',
    'Murcianische Spezialitäten',
    'Specialità murciane',
    'murcian-specialties',
    50
),

-- =====================================================
-- Salsas
-- =====================================================
(
    'Salsas',
    'Sauces',
    'Sauces',
    'Saucen',
    'Salse',
    'sauces',
    60
),

-- =====================================================
-- Panes
-- =====================================================
(
    'Panes',
    'Bread',
    'Pain',
    'Brot',
    'Pane',
    'bread',
    70
),

-- =====================================================
-- Postres
-- =====================================================
(
    'Postres',
    'Desserts',
    'Desserts',
    'Desserts',
    'Dolci',
    'desserts',
    80
),

-- =====================================================
-- Bebidas
-- =====================================================
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
-- END 014.01 CATEGORIES SEED DATA
-- =====================================================