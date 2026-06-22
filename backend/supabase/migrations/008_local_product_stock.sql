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