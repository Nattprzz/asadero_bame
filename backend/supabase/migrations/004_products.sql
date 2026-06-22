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