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