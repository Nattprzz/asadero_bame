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