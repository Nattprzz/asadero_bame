-- =====================================================
-- 014.05 PRODUCTS HOT DISHES
-- =====================================================
-- Productos de la categoría Platos Calientes
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
-- Arroz y Conejo
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Arroz y Conejo',
    'Rabbit Rice',
    'Riz au lapin',
    'Reis mit Kaninchen',
    'Riso con coniglio',
    'rabbit-rice',
    'Receta tradicional murciana.',
    'Traditional Murcian recipe.',
    'Recette traditionnelle murcienne.',
    'Traditionelles Rezept aus Murcia.',
    'Ricetta tradizionale murciana.',
    6.90,
    true,
    'available',
    true,
    './rice_rabbit.jpg',
    10
),

-- =====================================================
-- Arroz y Costillejas
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Arroz y Costillejas',
    'Rice with Pork Ribs',
    'Riz aux côtes de porc',
    'Reis mit Schweinerippchen',
    'Riso con costine di maiale',
    'rice-with-pork-ribs',
    'Uno de los arroces más típicos de la huerta.',
    'One of the most traditional rice dishes from Murcia.',
    'L’un des plats de riz les plus typiques de Murcie.',
    'Eines der typischsten Reisgerichte Murcias.',
    'Uno dei piatti di riso più tipici della Murcia.',
    6.90,
    true,
    'available',
    false,
    './rice_costillejas.jpg',
    20
),

-- =====================================================
-- Arroz con Pollo
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Arroz con Pollo',
    'Chicken Rice',
    'Riz au poulet',
    'Reis mit Hähnchen',
    'Riso con pollo',
    'chicken-rice',
    'Elaboración casera diaria.',
    'Freshly prepared every day.',
    'Préparé chaque jour de façon artisanale.',
    'Täglich frisch hausgemacht.',
    'Preparato fresco ogni giorno.',
    5.90,
    true,
    'available',
    false,
    './rice_chicken.jpg',
    30
),

-- =====================================================
-- Michirones
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Michirones',
    'Michirones',
    'Michirones',
    'Michirones',
    'Michirones',
    'michirones',
    'Habas secas guisadas con chorizo y jamón.',
    'Dried broad beans stewed with chorizo and ham.',
    'Fèves sèches mijotées avec chorizo et jambon.',
    'Getrocknete Saubohnen mit Chorizo und Schinken.',
    'Fave secche stufate con chorizo e prosciutto.',
    5.90,
    true,
    'available',
    false,
    './michirones.jpg',
    40
),

-- =====================================================
-- Pelotas en Caldo
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Pelotas en Caldo',
    'Murcian Meatballs in Broth',
    'Boulettes murciennes en bouillon',
    'Murcianische Fleischbällchen in Brühe',
    'Polpette murciane in brodo',
    'murcian-meatballs-in-broth',
    'Pelotas murcianas tradicionales.',
    'Traditional Murcian meatballs served in broth.',
    'Boulettes murciennes traditionnelles servies en bouillon.',
    'Traditionelle murcianische Fleischbällchen in Brühe.',
    'Polpette tradizionali murciane servite in brodo.',
    6.90,
    true,
    'available',
    false,
    './pelotas_broth.jpg',
    50
),

-- =====================================================
-- Magra con Tomate
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Magra con Tomate',
    'Pork in Tomato Sauce',
    'Porc à la sauce tomate',
    'Schweinefleisch in Tomatensauce',
    'Maiale al pomodoro',
    'pork-in-tomato-sauce',
    'Magro de cerdo cocinado en salsa de tomate casera.',
    'Lean pork cooked in homemade tomato sauce.',
    'Porc maigre cuisiné dans une sauce tomate maison.',
    'Mageres Schweinefleisch in hausgemachter Tomatensauce.',
    'Maiale magro cotto in salsa di pomodoro fatta in casa.',
    6.50,
    true,
    'available',
    false,
    './magra_tomato.jpg',
    60
),

-- =====================================================
-- Callos Caseros
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Callos Caseros',
    'Homemade Tripe Stew',
    'Tripes maison',
    'Hausgemachter Kuttel-Eintopf',
    'Trippa fatta in casa',
    'homemade-tripe-stew',
    'Guisados lentamente al estilo tradicional.',
    'Slow-cooked in the traditional way.',
    'Mijotés lentement selon la recette traditionnelle.',
    'Langsam nach traditioneller Art gekocht.',
    'Cotti lentamente secondo la tradizione.',
    6.90,
    true,
    'available',
    false,
    './tripe.jpg',
    70
),

-- =====================================================
-- Trigo Guisado
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Trigo Guisado',
    'Stewed Wheat',
    'Blé mijoté',
    'Geschmorter Weizen',
    'Grano stufato',
    'wheat-stew',
    'Plato típico murciano de cuchara.',
    'Traditional Murcian spoon dish.',
    'Plat traditionnel murcien à la cuillère.',
    'Traditionelles murcianisches Löffelgericht.',
    'Piatto tradizionale murciano da cucchiaio.',
    5.90,
    true,
    'available',
    false,
    './stewed_wheat.jpg',
    80
),

-- =====================================================
-- Empedrao
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Empedrao',
    'Rice with Beans and Cod',
    'Riz aux haricots et morue',
    'Reis mit Bohnen und Kabeljau',
    'Riso con fagioli e baccalà',
    'empedrao',
    'Arroz con alubias y bacalao.',
    'Rice with beans and cod.',
    'Riz aux haricots et morue.',
    'Reis mit Bohnen und Kabeljau.',
    'Riso con fagioli e baccalà.',
    6.20,
    true,
    'available',
    false,
    './empedrao.jpg',
    90
),

-- =====================================================
-- Zarangollo
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Zarangollo',
    'Zarangollo',
    'Zarangollo',
    'Zarangollo',
    'Zarangollo',
    'zarangollo',
    'Calabacín, cebolla y huevo.',
    'Zucchini, onion and egg.',
    'Courgette, oignon et œuf.',
    'Zucchini, Zwiebel und Ei.',
    'Zucchine, cipolla e uovo.',
    4.90,
    true,
    'available',
    false,
    './zarangollo.jpg',
    100
),

-- =====================================================
-- Pisto Murciano
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Pisto Murciano',
    'Murcian Ratatouille',
    'Ratatouille murcienne',
    'Murcianisches Ratatouille',
    'Ratatouille murciana',
    'murcian-ratatouille',
    'Verduras pochadas lentamente.',
    'Vegetables slowly simmered.',
    'Légumes mijotés lentement.',
    'Langsam geschmortes Gemüse.',
    'Verdure cotte lentamente.',
    5.20,
    true,
    'available',
    false,
    './pisto.jpg',
    110
),

-- =====================================================
-- Pisto Murciano con Huevo
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Pisto Murciano con Huevo',
    'Murcian Ratatouille with Fried Egg',
    'Ratatouille murcienne avec œuf frit',
    'Murcianisches Ratatouille mit Spiegelei',
    'Ratatouille murciana con uovo fritto',
    'murcian-ratatouille-with-fried-egg',
    'Coronado con huevo frito.',
    'Topped with a fried egg.',
    'Servi avec un œuf frit.',
    'Mit einem Spiegelei serviert.',
    'Servito con un uovo fritto.',
    6.20,
    true,
    'available',
    false,
    './pisto_with_egg.jpg',
    120
),

-- =====================================================
-- Albóndigas en Salsa
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Albóndigas en Salsa',
    'Meatballs in Sauce',
    'Boulettes en sauce',
    'Fleischbällchen in Sauce',
    'Polpette al sugo',
    'meatballs-in-sauce',
    'Caseras.',
    'Homemade.',
    'Faites maison.',
    'Hausgemacht.',
    'Fatte in casa.',
    6.50,
    true,
    'available',
    false,
    './meatballs_sauce.jpg',
    130
),

-- =====================================================
-- Muslos al Ajillo
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Muslos al Ajillo',
    'Garlic Chicken Thighs',
    'Cuisses de poulet à l’ail',
    'Hähnchenschenkel mit Knoblauch',
    'Cosce di pollo all’aglio',
    'garlic-chicken-thighs',
    'Pollo guisado al estilo tradicional.',
    'Chicken cooked in a traditional garlic sauce.',
    'Poulet cuisiné à l’ail selon la tradition.',
    'Hähnchen nach traditioneller Art mit Knoblauch.',
    'Pollo cucinato all’aglio secondo la tradizione.',
    6.50,
    true,
    'available',
    false,
    './chicken_garlic.jpg',
    140
),

-- =====================================================
-- Lasaña Casera de Carne
-- =====================================================
(
    (select id from categories where slug = 'hot-dishes'),
    'Lasaña Casera de Carne',
    'Homemade Beef Lasagna',
    'Lasagne maison au bœuf',
    'Hausgemachte Rindfleischlasagne',
    'Lasagna di manzo fatta in casa',
    'homemade-beef-lasagna',
    'Elaboración diaria.',
    'Prepared fresh every day.',
    'Préparée chaque jour.',
    'Täglich frisch zubereitet.',
    'Preparata ogni giorno.',
    6.90,
    true,
    'available',
    true,
    './meat_lasagna.jpg',
    150
);

-- =====================================================
-- END 014.05 PRODUCTS HOT DISHES
-- =====================================================