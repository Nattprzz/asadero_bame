-- =====================================================
-- 014.03 PRODUCTS CHICKEN AND SIDES
-- =====================================================
-- Productos de la categoría Pollo y Acompañamientos
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
-- Pollo Asado Entero
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Pollo Asado Entero',
    'Whole Roast Chicken',
    'Poulet rôti entier',
    'Ganzes Brathähnchen',
    'Pollo arrosto intero',
    'whole-roast-chicken',
    'Nuestro pollo estrella, asado lentamente con especias y jugo propio.',
    'Our signature chicken, slowly roasted with spices and its own natural juices.',
    'Notre poulet signature, rôti lentement avec des épices et son propre jus.',
    'Unser beliebtes Brathähnchen, langsam mit Gewürzen und eigenem Saft gegart.',
    'Il nostro pollo di punta, arrostito lentamente con spezie e il suo sugo naturale.',
    12.90,
    true,
    'available',
    true,
    './whole_roast_chicken.jpg',
    10
),

-- =====================================================
-- Medio Pollo Asado
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Medio Pollo Asado',
    'Half Roast Chicken',
    'Demi-poulet rôti',
    'Halbes Brathähnchen',
    'Mezzo pollo arrosto',
    'half-roast-chicken',
    'Medio pollo recién asado.',
    'Half a freshly roasted chicken.',
    'Un demi-poulet fraîchement rôti.',
    'Ein halbes frisch gebratenes Hähnchen.',
    'Mezzo pollo appena arrostito.',
    6.90,
    true,
    'available',
    false,
    './half_roast_chicken.jpg',
    20
),

-- =====================================================
-- Pollo Asado + Patatas Panadera
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Pollo Asado + Patatas Panadera',
    'Roast Chicken with Bakery Potatoes',
    'Poulet rôti avec pommes de terre boulangères',
    'Brathähnchen mit Ofenkartoffeln',
    'Pollo arrosto con patate al forno',
    'roast-chicken-with-bakery-potatoes',
    'Pollo entero acompañado de patatas asadas.',
    'Whole roast chicken served with roasted bakery-style potatoes.',
    'Poulet entier accompagné de pommes de terre rôties façon boulangère.',
    'Ganzes Brathähnchen mit Ofenkartoffeln nach Bäckerart.',
    'Pollo intero accompagnato da patate arrosto.',
    15.90,
    true,
    'available',
    true,
    './chicken_with_potatoes.jpg',
    30
),

-- =====================================================
-- Costillas Asadas
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Costillas Asadas',
    'Roasted Pork Ribs',
    'Travers de porc rôtis',
    'Gebratene Schweinerippchen',
    'Costine di maiale arrosto',
    'roasted-pork-ribs',
    'Costilla de cerdo asada al horno.',
    'Oven-roasted pork ribs.',
    'Travers de porc rôtis au four.',
    'Im Ofen gebratene Schweinerippchen.',
    'Costine di maiale arrosto al forno.',
    9.90,
    true,
    'available',
    false,
    './pok_ribs.jpg',
    40
),

-- =====================================================
-- Patatas Asadas
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Patatas Asadas',
    'Roasted Potatoes',
    'Pommes de terre rôties',
    'Ofenkartoffeln',
    'Patate arrosto',
    'roasted-potatoes',
    'Patata panadera tradicional.',
    'Traditional bakery-style roasted potatoes.',
    'Pommes de terre rôties traditionnelles façon boulangère.',
    'Traditionelle Ofenkartoffeln nach Bäckerart.',
    'Patate arrosto tradizionali.',
    3.50,
    true,
    'available',
    false,
    './roasted_potatoes.jpg',
    50
),

-- =====================================================
-- Patatas Fritas Caseras
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Patatas Fritas Caseras',
    'Homemade French Fries',
    'Frites maison',
    'Hausgemachte Pommes frites',
    'Patatine fritte fatte in casa',
    'homemade-french-fries',
    'Recién hechas.',
    'Freshly made.',
    'Fraîchement préparées.',
    'Frisch zubereitet.',
    'Appena fatte.',
    3.80,
    true,
    'available',
    false,
    './french_fries.jpg',
    60
),

-- =====================================================
-- Media Ración de Patatas
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Media Ración de Patatas',
    'Half Portion of Potatoes',
    'Demi-portion de pommes de terre',
    'Halbe Portion Kartoffeln',
    'Mezza porzione di patate',
    'half-portion-of-potatoes',
    'Ideal para acompañar.',
    'Ideal as a side dish.',
    'Idéal en accompagnement.',
    'Ideal als Beilage.',
    'Ideale come contorno.',
    2.20,
    true,
    'available',
    false,
    './half_french_fries.jpg',
    70
),

-- =====================================================
-- Pimientos Asados
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Pimientos Asados',
    'Roasted Red Peppers',
    'Poivrons rouges rôtis',
    'Geröstete rote Paprika',
    'Peperoni rossi arrosto',
    'roasted-red-peppers',
    'Pimiento rojo asado al horno.',
    'Oven-roasted red peppers.',
    'Poivrons rouges rôtis au four.',
    'Im Ofen geröstete rote Paprika.',
    'Peperoni rossi arrostiti al forno.',
    4.50,
    true,
    'available',
    false,
    './red_peppers.jpg',
    80
),

-- =====================================================
-- Verduras Asadas
-- =====================================================
(
    (select id from categories where slug = 'chicken-sides'),
    'Verduras Asadas',
    'Roasted Seasonal Vegetables',
    'Légumes de saison rôtis',
    'Geröstetes Saisongemüse',
    'Verdure di stagione arrosto',
    'roasted-seasonal-vegetables',
    'Verduras de temporada asadas.',
    'Roasted seasonal vegetables.',
    'Légumes de saison rôtis.',
    'Geröstetes Gemüse der Saison.',
    'Verdure di stagione arrosto.',
    4.90,
    true,
    'available',
    false,
    './seasonal_vegetables.jpg',
    90
);

-- =====================================================
-- END 014.03 PRODUCTS CHICKEN AND SIDES
-- =====================================================