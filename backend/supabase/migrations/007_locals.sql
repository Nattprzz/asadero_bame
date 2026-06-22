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