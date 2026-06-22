-- =====================================================
-- BAME DATABASE SCHEMA
-- PostgreSQL / Supabase
-- Version: 1.0.0
-- =====================================================
--------------------------------------------------------

-- SECTION: 001. DROP EXISTING OBJECTS

-- Elimina todos los objetos de la base de datos en orden
-- seguro para evitar conflictos por claves foráneas.
-----------------------------------------------------

-- =====================================================

-- =====================================================
-- TABLES
-- =====================================================

drop table if exists order_lines cascade;
drop table if exists orders cascade;

drop table if exists local_product cascade;
drop table if exists locals cascade;

drop table if exists product_allergen cascade;
drop table if exists product_images cascade;

drop table if exists products cascade;
drop table if exists allergens cascade;
drop table if exists categories cascade;

drop table if exists profiles cascade;

-- =====================================================
-- FUNCTIONS
-- =====================================================

drop function if exists set_updated_at() cascade;
drop function if exists handle_new_user() cascade;
drop function if exists is_admin() cascade;
drop function if exists has_role(text) cascade;

-- =====================================================
-- TRIGGERS
-- =====================================================

drop trigger if exists on_auth_user_created on auth.users;

-- =====================================================
-- STORAGE OBJECTS
-- =====================================================

delete from storage.objects
where bucket_id in (
'products',
'categories',
'allergens',
'locals'
);

-- =====================================================
-- STORAGE BUCKETS
-- =====================================================

delete from storage.buckets
where id in (
'products',
'categories',
'allergens',
'locals'
);

-- =====================================================
-- DISABLE RLS
-- =====================================================

alter table if exists profiles disable row level security;
alter table if exists categories disable row level security;
alter table if exists products disable row level security;
alter table if exists allergens disable row level security;
alter table if exists locals disable row level security;
alter table if exists orders disable row level security;
alter table if exists order_lines disable row level security;
alter table if exists product_allergen disable row level security;
alter table if exists local_product disable row level security;
alter table if exists product_images disable row level security;

-- =====================================================
-- END SECTION 001
-- =====================================================


-- =====================================================
-- 002. CATEGORIES
-- =====================================================
--------------------------------------------------------

-- Categorías principales del catálogo.
-- Cada producto pertenece a una única categoría.
-------------------------------------------------

-- Categorías actuales:

-- - Pollo y acompañamientos
-- - Croquetas
-- - Platos calientes
-- - Platos fríos
-- - Especialidades murcianas
-- - Salsas
-- - Panes
-- - Postres
-- - Bebidas
------------

-- =====================================================

create table categories (
id bigint generated always as identity primary key,

```
-- Nombre multidioma
name text not null,
name_en text not null,
name_fr text not null,
name_de text not null,
name_it text not null,

-- Slug único para URLs y búsquedas
slug text not null unique,

-- Descripción multidioma
description text,
desc_en text,
desc_fr text,
desc_de text,
desc_it text,

-- Imagen representativa de la categoría
image_path text,

-- Visibilidad
active boolean not null default true,

-- Orden de visualización
sort_order integer not null default 0,

created_at timestamptz not null default now(),
updated_at timestamptz not null default now()
```

);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_categories_slug
on categories(slug);

create index idx_categories_active
on categories(active);

create index idx_categories_sort_order
on categories(sort_order);

-- =====================================================
-- SEED DATA
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
(
'Pollo y acompañamientos',
'Chicken & Sides',
'Poulet et accompagnements',
'Hähnchen und Beilagen',
'Pollo e contorni',
'chicken-sides',
10
),
(
'Croquetas',
'Croquettes',
'Croquettes',
'Kroketten',
'Crocchette',
'croquettes',
20
),
(
'Platos calientes',
'Hot Dishes',
'Plats chauds',
'Warme Gerichte',
'Piatti caldi',
'hot-dishes',
30
),
(
'Platos fríos',
'Cold Dishes',
'Plats froids',
'Kalte Gerichte',
'Piatti freddi',
'cold-dishes',
40
),
(
'Especialidades murcianas',
'Murcian Specialties',
'Spécialités murciennes',
'Murcianische Spezialitäten',
'Specialità murciane',
'murcian-specialties',
50
),
(
'Salsas',
'Sauces',
'Sauces',
'Saucen',
'Salse',
'sauces',
60
),
(
'Panes',
'Bread',
'Pain',
'Brot',
'Pane',
'bread',
70
),
(
'Postres',
'Desserts',
'Desserts',
'Desserts',
'Dolci',
'desserts',
80
),
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
-- END SECTION 002
-- =====================================================

-- =====================================================
-- 003. ALLERGENS
-- =====================================================
--------------------------------------------------------

-- Catálogo de alérgenos alimentarios.
-- Basado en los 14 alérgenos de declaración obligatoria
-- establecidos por la normativa europea.
-----------------------------------------

-- Cada producto puede tener varios alérgenos.
-- La relación se realiza mediante product_allergen.
----------------------------------------------------

-- =====================================================

create table allergens (
id bigint generated always as identity primary key,

```
-- Nombre multidioma
name text not null,
name_en text not null,
name_fr text not null,
name_de text not null,
name_it text not null,

-- Slug único
slug text not null unique,

-- Descripción multidioma
description text,
desc_en text,
desc_fr text,
desc_de text,
desc_it text,

-- Nombre interno del icono
icon_name text not null,

created_at timestamptz not null default now(),
updated_at timestamptz not null default now()
```

);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_allergens_slug
on allergens(slug);

-- =====================================================
-- SEED DATA
-- =====================================================

insert into allergens (
name,
name_en,
name_fr,
name_de,
name_it,
slug,
icon_name,
description,
desc_en,
desc_fr,
desc_de,
desc_it
)
values
(
'Gluten',
'Gluten',
'Gluten',
'Gluten',
'Glutine',
'gluten',
'gluten',
'Cereales que contienen gluten y productos derivados.',
'Cereals containing gluten and derived products.',
'Céréales contenant du gluten et produits dérivés.',
'Getreide mit Gluten und daraus hergestellte Produkte.',
'Cereali contenenti glutine e prodotti derivati.'
),
(
'Huevos',
'Eggs',
'Œufs',
'Eier',
'Uova',
'huevos',
'eggs',
'Huevos y productos elaborados a base de huevo.',
'Eggs and egg-based products.',
'Œufs et produits à base d''œufs.',
'Eier und Produkte auf Eibasis.',
'Uova e prodotti a base di uova.'
),
(
'Lácteos',
'Dairy',
'Produits laitiers',
'Milchprodukte',
'Latticini',
'lacteos',
'dairy',
'Leche y productos lácteos, incluida la lactosa.',
'Milk and dairy products, including lactose.',
'Lait et produits laitiers, y compris le lactose.',
'Milch und Milchprodukte einschließlich Laktose.',
'Latte e prodotti lattiero-caseari, compreso il lattosio.'
),
(
'Pescado',
'Fish',
'Poisson',
'Fisch',
'Pesce',
'pescado',
'fish',
'Pescado y productos derivados del pescado.',
'Fish and fish-derived products.',
'Poisson et produits dérivés du poisson.',
'Fisch und daraus hergestellte Produkte.',
'Pesce e prodotti derivati dal pesce.'
),
(
'Soja',
'Soy',
'Soja',
'Soja',
'Soia',
'soja',
'soy',
'Soja y productos elaborados a base de soja.',
'Soybeans and soy-based products.',
'Soja et produits à base de soja.',
'Soja und daraus hergestellte Produkte.',
'Soia e prodotti a base di soia.'
),
(
'Frutos de cáscara',
'Tree Nuts',
'Fruits à coque',
'Schalenfrüchte',
'Frutta a guscio',
'frutos-cascara',
'tree_nuts',
'Frutos de cáscara y productos derivados.',
'Tree nuts and derived products.',
'Fruits à coque et produits dérivés.',
'Schalenfrüchte und daraus hergestellte Produkte.',
'Frutta a guscio e prodotti derivati.'
),
(
'Crustáceos',
'Crustaceans',
'Crustacés',
'Krebstiere',
'Crostacei',
'crustaceos',
'crustaceans',
'Crustáceos y productos derivados.',
'Crustaceans and derived products.',
'Crustacés et produits dérivés.',
'Krebstiere und daraus hergestellte Produkte.',
'Crostacei e prodotti derivati.'
),
(
'Dióxido de azufre y sulfitos',
'Sulphur Dioxide and Sulphites',
'Dioxyde de soufre et sulfites',
'Schwefeldioxid und Sulfite',
'Anidride solforosa e solfiti',
'sulfitos',
'sulfur_dioxide_sulphites',
'Dióxido de azufre y sulfitos presentes en alimentos y bebidas.',
'Sulphur dioxide and sulphites present in food and beverages.',
'Dioxyde de soufre et sulfites présents dans les aliments et boissons.',
'Schwefeldioxid und Sulfite in Lebensmitteln und Getränken.',
'Anidride solforosa e solfiti presenti negli alimenti e nelle bevande.'
),
(
'Moluscos',
'Molluscs',
'Mollusques',
'Weichtiere',
'Molluschi',
'moluscos',
'mollusks',
'Moluscos y productos derivados.',
'Molluscs and derived products.',
'Mollusques et produits dérivés.',
'Weichtiere und daraus hergestellte Produkte.',
'Molluschi e prodotti derivati.'
),
(
'Granos de sésamo',
'Sesame Seeds',
'Graines de sésame',
'Sesamsamen',
'Semi di sesamo',
'sesamo',
'sesame_grains',
'Semillas de sésamo y productos derivados.',
'Sesame seeds and derived products.',
'Graines de sésame et produits dérivés.',
'Sesamsamen und daraus hergestellte Produkte.',
'Semi di sesamo e prodotti derivati.'
),
(
'Mostaza',
'Mustard',
'Moutarde',
'Senf',
'Senape',
'mostaza',
'mustard',
'Mostaza y productos derivados.',
'Mustard and derived products.',
'Moutarde et produits dérivés.',
'Senf und daraus hergestellte Produkte.',
'Senape e prodotti derivati.'
),
(
'Cacahuetes',
'Peanuts',
'Arachides',
'Erdnüsse',
'Arachidi',
'cacahuetes',
'peanuts',
'Cacahuetes y productos derivados.',
'Peanuts and derived products.',
'Arachides et produits dérivés.',
'Erdnüsse und daraus hergestellte Produkte.',
'Arachidi e prodotti derivati.'
),
(
'Apio',
'Celery',
'Céleri',
'Sellerie',
'Sedano',
'apio',
'celery',
'Apio y productos derivados.',
'Celery and derived products.',
'Céleri et produits dérivés.',
'Sellerie und daraus hergestellte Produkte.',
'Sedano e prodotti derivati.'
),
(
'Altramuces',
'Lupins',
'Lupins',
'Lupinen',
'Lupini',
'altramuces',
'lupins',
'Altramuces y productos derivados.',
'Lupins and derived products.',
'Lupins et produits dérivés.',
'Lupinen und daraus hergestellte Produkte.',
'Lupini e prodotti derivati.'
);

-- =====================================================
-- END SECTION 003
-- =====================================================


-- =====================================================
-- 004. PRODUCTS
-- =====================================================

create table products (
    id bigint generated always as identity primary key,

    category_id bigint not null
        references categories(id)
        on delete restrict,

    name text not null,
    name_en text not null,
    name_fr text not null,
    name_de text not null,
    name_it text not null,

    slug text not null unique,

    description text,
    desc_en text,
    desc_fr text,
    desc_de text,
    desc_it text,

    price numeric(10,2) not null,

    available boolean not null default true,

    availability text not null default 'available'
        check (
            availability in (
                'available',
                'low_stock',
                'sold_out',
                'hidden'
            )
        ),

    featured boolean not null default false,

    weight text,
    prep_time smallint,
    image_path text,

    sort_order integer not null default 0,

    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),

    constraint products_price_positive check (price >= 0),
    constraint products_prep_time_positive check (
        prep_time is null or prep_time > 0
    )
);

create index idx_products_category_id on products(category_id);
create index idx_products_slug on products(slug);
create index idx_products_available on products(available);
create index idx_products_availability on products(availability);
create index idx_products_featured on products(featured);
create index idx_products_sort_order on products(sort_order);

-- =====================================================
-- END SECTION 004
-- =====================================================

-- =====================================================
-- 005. PRODUCT ALLERGENS
-- =====================================================
--------------------------------------------------------

-- Relación N:M entre productos y alérgenos.

-- Ejemplos:

-- - Croquetas de jamón -> Gluten, Huevos, Lácteos
-- - Pan de Calatrava -> Gluten, Huevos, Lácteos
-- - Ensalada Murciana -> Huevos, Pescado
-----------------------------------------

-- Un producto puede contener varios alérgenos.
-- Un alérgeno puede aparecer en varios productos.
--------------------------------------------------

-- =====================================================

create table product_allergen (
product_id bigint not null
references products(id)
on delete cascade,

```
allergen_id bigint not null
    references allergens(id)
    on delete cascade,

primary key (
    product_id,
    allergen_id
)
```

);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_product_allergen_product
on product_allergen(product_id);

create index idx_product_allergen_allergen
on product_allergen(allergen_id);

-- =====================================================
-- SEED DATA
-- =====================================================
--------------------------------------------------------

-- Se recomienda insertar las relaciones una vez
-- cargados todos los productos.
--------------------------------

## -- Ejemplo:

-- insert into product_allergen (
--     product_id,
--     allergen_id
-- )
-- values (
--     (select id from products
--      where slug = 'serrano-ham-croquettes'),
-----------------------------------------------

--     (select id from allergens
--      where slug = 'gluten')
-- );
-----

-- =====================================================
-- END SECTION 005
-- =====================================================


-- =====================================================
-- 006. PROFILES
-- =====================================================
--
-- Perfiles de usuario de la aplicación.
--
-- Supabase Auth gestiona:
-- - email
-- - contraseña
-- - sesiones
-- - recuperación de contraseña
--
-- Esta tabla guarda los datos propios de BAME:
-- - nombre
-- - apellidos
-- - teléfono
-- - rol
-- - estado de la cuenta
--
-- La columna id está vinculada a auth.users(id).
--
-- =====================================================

create table profiles (
    id uuid primary key
        references auth.users(id)
        on delete cascade,

    email text not null unique,

    name text not null,

    surname text not null default '',

    phone text,

    role text not null default 'ROLE_USER'
        check (
            role in (
                'ROLE_USER',
                'ROLE_ADMIN',
                'ROLE_RESPONSABLE',
                'ROLE_GERENTE'
            )
        ),

    active boolean not null default true,

    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_profiles_email
    on profiles(email);

create index idx_profiles_role
    on profiles(role);

create index idx_profiles_active
    on profiles(active);

-- =====================================================
-- NOTES
-- =====================================================
--
-- No insertar usuarios directamente en auth.users.
--
-- Los usuarios deben crearse desde:
-- - Supabase Dashboard
-- - Supabase Auth API
-- - Backend Symfony usando Supabase Auth
--
-- Después de crear un usuario en auth.users,
-- se puede crear su perfil usando el mismo UUID.
--
-- Ejemplo:
--
-- insert into profiles (
--     id,
--     email,
--     name,
--     surname,
--     phone,
--     role
-- )
-- values (
--     'uuid-del-usuario-auth',
--     'admin@bame.test',
--     'Administrador',
--     'BAME',
--     '600000000',
--     'ROLE_ADMIN'
-- );
--
-- =====================================================
-- END SECTION 006
-- =====================================================

-- =====================================================
-- 007. LOCALS
-- =====================================================
--
-- Locales físicos del asadero.
--
-- Cada local puede tener:
-- - dirección
-- - teléfono
-- - WhatsApp
-- - coordenadas
-- - horario en JSON
-- - estado operativo
--
-- =====================================================

create table locals (
    id bigint generated always as identity primary key,

    name text not null,

    address text not null,

    city text not null default 'Murcia',

    postal_code text,

    phone text not null,

    email text,

    whatsapp text,

    latitude numeric(10,7),

    longitude numeric(10,7),

    active boolean not null default true,

    status text not null default 'open'
        check (
            status in (
                'open',
                'closed',
                'closing_soon',
                'temporarily_closed'
            )
        ),

    hours jsonb not null default '{}'::jsonb,

    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now()
);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_locals_active
    on locals(active);

create index idx_locals_status
    on locals(status);

create index idx_locals_city
    on locals(city);

-- =====================================================
-- SEED DATA
-- =====================================================

insert into locals (
    name,
    address,
    city,
    postal_code,
    phone,
    email,
    whatsapp,
    latitude,
    longitude,
    active,
    status,
    hours
)
values (
    'Asadero BAME',
    'Dirección pendiente de completar',
    'Murcia',
    null,
    '+34000000000',
    'info@asaderobame.com',
    '+34000000000',
    null,
    null,
    true,
    'open',
    '{
        "monday": {
            "open": "09:00",
            "close": "16:00"
        },
        "tuesday": {
            "open": "09:00",
            "close": "16:00"
        },
        "wednesday": {
            "open": "09:00",
            "close": "16:00"
        },
        "thursday": {
            "open": "09:00",
            "close": "16:00"
        },
        "friday": {
            "open": "09:00",
            "close": "16:00"
        },
        "saturday": {
            "open": "09:00",
            "close": "16:00"
        },
        "sunday": {
            "open": "09:00",
            "close": "16:00"
        }
    }'::jsonb
);

-- =====================================================
-- END SECTION 007
-- =====================================================

-- =====================================================
-- 008. LOCAL PRODUCT STOCK
-- =====================================================
--
-- Control de stock por local.
--
-- Permite:
--
-- - Un producto disponible en un local y agotado en otro.
-- - Control de inventario por tienda.
-- - Mostrar disponibilidad real al cliente.
--
-- =====================================================

create table local_product (
    id bigint generated always as identity primary key,

    local_id bigint not null
        references locals(id)
        on delete cascade,

    product_id bigint not null
        references products(id)
        on delete cascade,

    stock integer not null default 0,

    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),

    constraint local_product_stock_positive
        check (stock >= 0),

    unique (
        local_id,
        product_id
    )
);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_local_product_local
    on local_product(local_id);

create index idx_local_product_product
    on local_product(product_id);

create index idx_local_product_stock
    on local_product(stock);

-- =====================================================
-- SEED DATA
-- =====================================================
--
-- Inicialmente todos los productos tendrán stock 100
-- en el local principal.
--
-- =====================================================

insert into local_product (
    local_id,
    product_id,
    stock
)
select
    l.id,
    p.id,
    100
from locals l
cross join products p;

-- =====================================================
-- END SECTION 008
-- =====================================================

-- =====================================================
-- 009. ORDERS
-- =====================================================
--
-- Pedidos realizados por los clientes.
--
-- Estados permitidos:
--
-- - pending
-- - confirmed
-- - preparing
-- - ready
-- - completed
-- - cancelled
--
-- Tipos permitidos:
--
-- - takeaway
-- - delivery
--
-- =====================================================

create table orders (
    id bigint generated always as identity primary key,

    reference text not null unique,

    user_id uuid not null
        references profiles(id)
        on delete cascade,

    local_id bigint
        references locals(id)
        on delete set null,

    status text not null default 'pending'
        check (
            status in (
                'pending',
                'confirmed',
                'preparing',
                'ready',
                'completed',
                'cancelled'
            )
        ),

    type text not null default 'takeaway'
        check (
            type in (
                'takeaway',
                'delivery'
            )
        ),

    total numeric(10,2) not null default 0,

    notes text,

    estimated_time smallint,

    phone text,

    address text,

    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),

    constraint orders_total_positive
        check (total >= 0),

    constraint orders_estimated_time_positive
        check (
            estimated_time is null
            or estimated_time > 0
        )
);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_orders_reference
    on orders(reference);

create index idx_orders_user
    on orders(user_id);

create index idx_orders_local
    on orders(local_id);

create index idx_orders_status
    on orders(status);

create index idx_orders_type
    on orders(type);

create index idx_orders_created_at
    on orders(created_at desc);

-- =====================================================
-- END SECTION 009
-- =====================================================

-- =====================================================
-- 010. ORDER LINES
-- =====================================================
--
-- Líneas de producto dentro de cada pedido.
--
-- Cada línea representa:
-- - producto comprado
-- - cantidad
-- - precio unitario en el momento del pedido
-- - notas específicas del producto
--
-- El precio se guarda aquí para mantener el histórico,
-- aunque el precio del producto cambie después.
--
-- =====================================================

create table order_lines (
    id bigint generated always as identity primary key,

    order_id bigint not null
        references orders(id)
        on delete cascade,

    product_id bigint not null
        references products(id)
        on delete restrict,

    quantity integer not null,

    unit_price numeric(10,2) not null,

    notes text,

    created_at timestamptz not null default now(),
    updated_at timestamptz not null default now(),

    constraint order_lines_quantity_positive
        check (quantity > 0),

    constraint order_lines_unit_price_positive
        check (unit_price >= 0)
);

-- =====================================================
-- INDEXES
-- =====================================================

create index idx_order_lines_order
    on order_lines(order_id);

create index idx_order_lines_product
    on order_lines(product_id);

-- =====================================================
-- END SECTION 010
-- =====================================================

-- =====================================================
-- 011. FUNCTIONS
-- =====================================================
--
-- Funciones auxiliares utilizadas por triggers,
-- políticas RLS y lógica de negocio.
--
-- =====================================================

-- =====================================================
-- set_updated_at()
-- =====================================================
--
-- Actualiza automáticamente updated_at
-- antes de cada UPDATE.
--
-- =====================================================

create or replace function set_updated_at()
returns trigger
language plpgsql
as $$
begin
    new.updated_at = now();
    return new;
end;
$$;

-- =====================================================
-- has_role()
-- =====================================================
--
-- Comprueba si el usuario autenticado
-- tiene un rol concreto.
--
-- =====================================================

create or replace function has_role(
    required_role text
)
returns boolean
language sql
stable
security definer
set search_path = public
as $$
    select exists (
        select 1
        from profiles p
        where p.id = auth.uid()
        and p.role = required_role
        and p.active = true
    );
$$;

-- =====================================================
-- is_admin()
-- =====================================================
--
-- Comprueba si el usuario actual
-- es administrador.
--
-- =====================================================

create or replace function is_admin()
returns boolean
language sql
stable
security definer
set search_path = public
as $$
    select has_role('ROLE_ADMIN');
$$;

-- =====================================================
-- handle_new_user()
-- =====================================================
--
-- Se ejecuta automáticamente cuando
-- Supabase Auth crea un usuario.
--
-- Crea el perfil asociado.
--
-- =====================================================

create or replace function handle_new_user()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin

    insert into profiles (
        id,
        email,
        name,
        surname,
        role,
        active
    )
    values (
        new.id,
        new.email,
        coalesce(
            new.raw_user_meta_data ->> 'name',
            'Usuario'
        ),
        '',
        'ROLE_USER',
        true
    );

    return new;

end;
$$;

-- =====================================================
-- END SECTION 011
-- =====================================================

-- =====================================================
-- 012. TRIGGERS
-- =====================================================
--
-- Triggers automáticos del sistema.
--
-- Responsabilidades:
--
-- - Mantener updated_at actualizado.
-- - Crear perfiles automáticamente.
--
-- =====================================================

-- =====================================================
-- CATEGORIES
-- =====================================================

create trigger categories_set_updated_at
before update on categories
for each row
execute function set_updated_at();

-- =====================================================
-- ALLERGENS
-- =====================================================

create trigger allergens_set_updated_at
before update on allergens
for each row
execute function set_updated_at();

-- =====================================================
-- PRODUCTS
-- =====================================================

create trigger products_set_updated_at
before update on products
for each row
execute function set_updated_at();

-- =====================================================
-- PROFILES
-- =====================================================

create trigger profiles_set_updated_at
before update on profiles
for each row
execute function set_updated_at();

-- =====================================================
-- LOCALS
-- =====================================================

create trigger locals_set_updated_at
before update on locals
for each row
execute function set_updated_at();

-- =====================================================
-- LOCAL PRODUCT
-- =====================================================

create trigger local_product_set_updated_at
before update on local_product
for each row
execute function set_updated_at();

-- =====================================================
-- ORDERS
-- =====================================================

create trigger orders_set_updated_at
before update on orders
for each row
execute function set_updated_at();

-- =====================================================
-- ORDER LINES
-- =====================================================

create trigger order_lines_set_updated_at
before update on order_lines
for each row
execute function set_updated_at();

-- =====================================================
-- AUTH → PROFILES
-- =====================================================
--
-- Cuando se crea un usuario en auth.users
-- se genera automáticamente un perfil.
--
-- =====================================================

create trigger on_auth_user_created
after insert on auth.users
for each row
execute function handle_new_user();

-- =====================================================
-- END SECTION 012
-- =====================================================

-- =====================================================
-- 013. ROW LEVEL SECURITY
-- =====================================================

alter table categories enable row level security;
alter table allergens enable row level security;
alter table products enable row level security;
alter table product_allergen enable row level security;
alter table profiles enable row level security;
alter table locals enable row level security;
alter table local_product enable row level security;
alter table orders enable row level security;
alter table order_lines enable row level security;

-- =====================================================
-- PUBLIC CATALOG READ
-- =====================================================

create policy categories_public_read
on categories
for select
to anon, authenticated
using (active = true);

create policy allergens_public_read
on allergens
for select
to anon, authenticated
using (true);

create policy products_public_read
on products
for select
to anon, authenticated
using (
    available = true
    and availability <> 'hidden'
);

create policy product_allergen_public_read
on product_allergen
for select
to anon, authenticated
using (true);

create policy locals_public_read
on locals
for select
to anon, authenticated
using (active = true);

create policy local_product_public_read
on local_product
for select
to anon, authenticated
using (true);

-- =====================================================
-- PROFILES
-- =====================================================

-- Column protection for self-service profile updates.
--
-- RLS policies can restrict rows but cannot restrict which columns are changed.
-- This trigger therefore applies a whitelist to authenticated non-admin users.
-- Administrators and privileged backend connections keep their existing update
-- capabilities. The guard trigger name sorts before profiles_set_updated_at, so
-- users cannot submit updated_at while the timestamp trigger can still manage it.
create or replace function protect_profile_user_updates()
returns trigger
language plpgsql
security definer
set search_path = public
as $$
begin
    -- Supabase administrators are authorized by profiles_admin_update_all.
    -- Connections without auth.uid() are privileged backend/database sessions.
    if auth.uid() is null or is_admin() then
        return new;
    end if;

    -- updated_at is exclusively maintained by profiles_set_updated_at.
    if new.updated_at is distinct from old.updated_at then
        raise exception using
            errcode = '42501',
            message = 'updated_at cannot be changed directly';
    end if;

    -- Current user-editable columns: name, surname and phone.
    -- Every other current or future column is protected by default, including
    -- id, email, role, active, status (if added) and created_at. If username,
    -- avatar_url or backdrop_url are added later, they must be explicitly added
    -- to this whitelist before users can update them.
    if (to_jsonb(new) - array['name', 'surname', 'phone', 'updated_at'])
        is distinct from
       (to_jsonb(old) - array['name', 'surname', 'phone', 'updated_at']) then
        raise exception using
            errcode = '42501',
            message = 'protected profile columns cannot be changed';
    end if;

    return new;
end;
$$;

drop trigger if exists profiles_guard_sensitive_fields on profiles;

create trigger profiles_guard_sensitive_fields
before update on profiles
for each row
execute function protect_profile_user_updates();


-- This policy restricts users to their own row. Column-level protection is
-- enforced by profiles_guard_sensitive_fields above.
create policy profiles_read_own
on profiles
for select
to authenticated
using (id = auth.uid());

create policy profiles_update_own
on profiles
for update
to authenticated
using (id = auth.uid())
with check (id = auth.uid());

create policy profiles_admin_read_all
on profiles
for select
to authenticated
using (is_admin());

create policy profiles_admin_update_all
on profiles
for update
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- ADMIN CATALOG MANAGEMENT
-- =====================================================

create policy categories_admin_all
on categories
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy allergens_admin_all
on allergens
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy products_admin_all
on products
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy product_allergen_admin_all
on product_allergen
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy locals_admin_all
on locals
for all
to authenticated
using (is_admin())
with check (is_admin());

create policy local_product_admin_all
on local_product
for all
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- ORDERS
-- =====================================================

create policy orders_insert_own
on orders
for insert
to authenticated
with check (user_id = auth.uid());

create policy orders_read_own
on orders
for select
to authenticated
using (user_id = auth.uid());

create policy orders_admin_read_all
on orders
for select
to authenticated
using (is_admin());

create policy orders_admin_update_all
on orders
for update
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- ORDER LINES
-- =====================================================

create policy order_lines_insert_own_order
on order_lines
for insert
to authenticated
with check (
    exists (
        select 1
        from orders o
        where o.id = order_lines.order_id
        and o.user_id = auth.uid()
    )
);

create policy order_lines_read_own_order
on order_lines
for select
to authenticated
using (
    exists (
        select 1
        from orders o
        where o.id = order_lines.order_id
        and o.user_id = auth.uid()
    )
);

create policy order_lines_admin_read_all
on order_lines
for select
to authenticated
using (is_admin());

create policy order_lines_admin_update_all
on order_lines
for update
to authenticated
using (is_admin())
with check (is_admin());

-- =====================================================
-- END SECTION 013
-- =====================================================

-- =====================================================
-- 014. INITIAL SEED DATA
-- =====================================================

-- categories
-- allergens
-- products
-- locals


-- =====================================================
-- 014. INITIAL SEED DATA
-- =====================================================

-- =====================================================
-- 014.01 CATEGORIES SEED DATA
-- =====================================================
--------------------------------------------------------

## -- Categorías principales del catálogo BAME

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
(
'Pollo y acompañamientos',
'Chicken & Sides',
'Poulet et accompagnements',
'Hähnchen und Beilagen',
'Pollo e contorni',
'chicken-sides',
10
),
(
'Croquetas',
'Croquettes',
'Croquettes',
'Kroketten',
'Crocchette',
'croquettes',
20
),
(
'Platos calientes',
'Hot Dishes',
'Plats chauds',
'Warme Gerichte',
'Piatti caldi',
'hot-dishes',
30
),
(
'Platos fríos',
'Cold Dishes',
'Plats froids',
'Kalte Gerichte',
'Piatti freddi',
'cold-dishes',
40
),
(
'Especialidades murcianas',
'Murcian Specialties',
'Spécialités murciennes',
'Murcianische Spezialitäten',
'Specialità murciane',
'murcian-specialties',
50
),
(
'Salsas',
'Sauces',
'Sauces',
'Saucen',
'Salse',
'sauces',
60
),
(
'Panes',
'Bread',
'Pain',
'Brot',
'Pane',
'bread',
70
),
(
'Postres',
'Desserts',
'Desserts',
'Desserts',
'Dolci',
'desserts',
80
),
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
-- END 014.01
-- =====================================================

-- =====================================================
-- 014.02 ALLERGENS SEED DATA
-- =====================================================
--------------------------------------------------------

-- 14 alérgenos de declaración obligatoria
-- según normativa europea.
---------------------------

-- =====================================================

insert into allergens (
name,
name_en,
name_fr,
name_de,
name_it,
slug,
icon_name,
description,
desc_en,
desc_fr,
desc_de,
desc_it
)
values
(
'Gluten',
'Gluten',
'Gluten',
'Gluten',
'Glutine',
'gluten',
'gluten',
'Cereales que contienen gluten y productos derivados.',
'Cereals containing gluten and derived products.',
'Céréales contenant du gluten et produits dérivés.',
'Getreide mit Gluten und daraus hergestellte Produkte.',
'Cereali contenenti glutine e prodotti derivati.'
),
(
'Huevos',
'Eggs',
'Œufs',
'Eier',
'Uova',
'huevos',
'eggs',
'Huevos y productos elaborados a base de huevo.',
'Eggs and egg-based products.',
'Œufs et produits à base d''œufs.',
'Eier und Produkte auf Eibasis.',
'Uova e prodotti a base di uova.'
),
(
'Lácteos',
'Dairy',
'Produits laitiers',
'Milchprodukte',
'Latticini',
'lacteos',
'dairy',
'Leche y productos lácteos, incluida la lactosa.',
'Milk and dairy products, including lactose.',
'Lait et produits laitiers, y compris le lactose.',
'Milch und Milchprodukte einschließlich Laktose.',
'Latte e prodotti lattiero-caseari, compreso il lattosio.'
),
(
'Pescado',
'Fish',
'Poisson',
'Fisch',
'Pesce',
'pescado',
'fish',
'Pescado y productos derivados del pescado.',
'Fish and fish-derived products.',
'Poisson et produits dérivés du poisson.',
'Fisch und daraus hergestellte Produkte.',
'Pesce e prodotti derivati dal pesce.'
),
(
'Soja',
'Soy',
'Soja',
'Soja',
'Soia',
'soja',
'soy',
'Soja y productos elaborados a base de soja.',
'Soybeans and soy-based products.',
'Soja et produits à base de soja.',
'Soja und daraus hergestellte Produkte.',
'Soia e prodotti a base di soia.'
),
(
'Frutos de cáscara',
'Tree Nuts',
'Fruits à coque',
'Schalenfrüchte',
'Frutta a guscio',
'frutos-cascara',
'tree_nuts',
'Frutos de cáscara y productos derivados.',
'Tree nuts and derived products.',
'Fruits à coque et produits dérivés.',
'Schalenfrüchte und daraus hergestellte Produkte.',
'Frutta a guscio e prodotti derivati.'
),
(
'Crustáceos',
'Crustaceans',
'Crustacés',
'Krebstiere',
'Crostacei',
'crustaceos',
'crustaceans',
'Crustáceos y productos derivados.',
'Crustaceans and derived products.',
'Crustacés et produits dérivés.',
'Krebstiere und daraus hergestellte Produkte.',
'Crostacei e prodotti derivati.'
),
(
'Dióxido de azufre y sulfitos',
'Sulphur Dioxide and Sulphites',
'Dioxyde de soufre et sulfites',
'Schwefeldioxid und Sulfite',
'Anidride solforosa e solfiti',
'sulfitos',
'sulfur_dioxide_sulphites',
'Dióxido de azufre y sulfitos presentes en alimentos y bebidas.',
'Sulphur dioxide and sulphites present in food and beverages.',
'Dioxyde de soufre et sulfites présents dans les aliments et boissons.',
'Schwefeldioxid und Sulfite in Lebensmitteln und Getränken.',
'Anidride solforosa e solfiti presenti negli alimenti e nelle bevande.'
),
(
'Moluscos',
'Molluscs',
'Mollusques',
'Weichtiere',
'Molluschi',
'moluscos',
'mollusks',
'Moluscos y productos derivados.',
'Molluscs and derived products.',
'Mollusques et produits dérivés.',
'Weichtiere und daraus hergestellte Produkte.',
'Molluschi e prodotti derivati.'
),
(
'Granos de sésamo',
'Sesame Seeds',
'Graines de sésame',
'Sesamsamen',
'Semi di sesamo',
'sesamo',
'sesame_grains',
'Semillas de sésamo y productos derivados.',
'Sesame seeds and derived products.',
'Graines de sésame et produits dérivés.',
'Sesamsamen und daraus hergestellte Produkte.',
'Semi di sesamo e prodotti derivati.'
),
(
'Mostaza',
'Mustard',
'Moutarde',
'Senf',
'Senape',
'mostaza',
'mustard',
'Mostaza y productos derivados.',
'Mustard and derived products.',
'Moutarde et produits dérivés.',
'Senf und daraus hergestellte Produkte.',
'Senape e prodotti derivati.'
),
(
'Cacahuetes',
'Peanuts',
'Arachides',
'Erdnüsse',
'Arachidi',
'cacahuetes',
'peanuts',
'Cacahuetes y productos derivados.',
'Peanuts and derived products.',
'Arachides et produits dérivés.',
'Erdnüsse und daraus hergestellte Produkte.',
'Arachidi e prodotti derivati.'
),
(
'Apio',
'Celery',
'Céleri',
'Sellerie',
'Sedano',
'apio',
'celery',
'Apio y productos derivados.',
'Celery and derived products.',
'Céleri et produits dérivés.',
'Sellerie und daraus hergestellte Produkte.',
'Sedano e prodotti derivati.'
),
(
'Altramuces',
'Lupins',
'Lupins',
'Lupinen',
'Lupini',
'altramuces',
'lupins',
'Altramuces y productos derivados.',
'Lupins and derived products.',
'Lupins et produits dérivés.',
'Lupinen und daraus hergestellte Produkte.',
'Lupini e prodotti derivati.'
);

-- =====================================================
-- END 014.02
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
-- 014.12 INITIAL PRODUCTS ALLERGENS
-- =====================================================

-- =====================================================
-- 014.12.01 PRODUCT TRACES
-- CATEGORY: CHICKEN & SIDES
-- =====================================================

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('apio', 'mostaza', 'sulfitos', 'gluten')
where p.slug in (
    'whole-roast-chicken',
    'half-roast-chicken',
    'roast-chicken-with-bakery-potatoes'
);

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('mostaza', 'soja', 'apio', 'gluten')
where p.slug = 'roasted-pork-ribs';

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('sulfitos')
where p.slug = 'roasted-potatoes';

insert into product_traces (
    product_id,
    allergen_id
)
select
    p.id,
    a.id
from products p
join allergens a
    on a.slug in ('gluten', 'huevos', 'lacteos', 'pescado')
where p.slug in (
    'homemade-french-fries',
    'half-portion-of-potatoes'
);

-- Pimientos Asados y Verduras Asadas:
-- Sin alérgenos directos ni trazas relevantes según receta base.

-- =====================================================
-- END OF FILE
-- =====================================================
