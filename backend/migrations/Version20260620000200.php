<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260620000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Align orders user_id with application users table when legacy Supabase profile FK is empty';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
DO $$
DECLARE
    order_count bigint;
    user_id_type text;
BEGIN
    SELECT count(*) INTO order_count FROM public.orders;
    SELECT udt_name INTO user_id_type
    FROM information_schema.columns
    WHERE table_schema = 'public'
      AND table_name = 'orders'
      AND column_name = 'user_id';

    IF user_id_type = 'uuid' THEN
        IF order_count > 0 THEN
            RAISE EXCEPTION 'Cannot convert orders.user_id from uuid to bigint because orders table is not empty';
        END IF;

        DROP POLICY IF EXISTS orders_insert_own ON public.orders;
        DROP POLICY IF EXISTS orders_read_own ON public.orders;
        DROP POLICY IF EXISTS order_lines_insert_own_order ON public.order_lines;
        DROP POLICY IF EXISTS order_lines_read_own_order ON public.order_lines;

        ALTER TABLE public.orders DROP CONSTRAINT IF EXISTS orders_user_id_fkey;
        ALTER TABLE public.orders DROP COLUMN user_id;
        ALTER TABLE public.orders ADD COLUMN user_id BIGINT NOT NULL;
        ALTER TABLE public.orders
            ADD CONSTRAINT FK_ORDERS_USER
            FOREIGN KEY (user_id) REFERENCES public.users (id) ON DELETE CASCADE;
        CREATE INDEX IF NOT EXISTS IDX_ORDERS_USER ON public.orders (user_id);
    END IF;
END $$;
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
DO $$
DECLARE
    order_count bigint;
    user_id_type text;
BEGIN
    SELECT count(*) INTO order_count FROM public.orders;
    SELECT udt_name INTO user_id_type
    FROM information_schema.columns
    WHERE table_schema = 'public'
      AND table_name = 'orders'
      AND column_name = 'user_id';

    IF user_id_type = 'int8' THEN
        IF order_count > 0 THEN
            RAISE EXCEPTION 'Cannot revert orders.user_id from bigint to uuid because orders table is not empty';
        END IF;

        DROP POLICY IF EXISTS orders_insert_own ON public.orders;
        DROP POLICY IF EXISTS orders_read_own ON public.orders;
        DROP POLICY IF EXISTS order_lines_insert_own_order ON public.order_lines;
        DROP POLICY IF EXISTS order_lines_read_own_order ON public.order_lines;

        ALTER TABLE public.orders DROP CONSTRAINT IF EXISTS FK_ORDERS_USER;
        DROP INDEX IF EXISTS IDX_ORDERS_USER;
        ALTER TABLE public.orders DROP COLUMN user_id;
        ALTER TABLE public.orders ADD COLUMN user_id UUID NOT NULL;
        ALTER TABLE public.orders
            ADD CONSTRAINT orders_user_id_fkey
            FOREIGN KEY (user_id) REFERENCES public.profiles (id) ON DELETE CASCADE;

        CREATE POLICY orders_insert_own ON public.orders
            FOR INSERT TO authenticated
            WITH CHECK (user_id = auth.uid());
        CREATE POLICY orders_read_own ON public.orders
            FOR SELECT TO authenticated
            USING (user_id = auth.uid());
        CREATE POLICY order_lines_insert_own_order ON public.order_lines
            FOR INSERT TO authenticated
            WITH CHECK (EXISTS (
                SELECT 1 FROM public.orders o
                WHERE o.id = order_lines.order_id AND o.user_id = auth.uid()
            ));
        CREATE POLICY order_lines_read_own_order ON public.order_lines
            FOR SELECT TO authenticated
            USING (EXISTS (
                SELECT 1 FROM public.orders o
                WHERE o.id = order_lines.order_id AND o.user_id = auth.uid()
            ));
    END IF;
END $$;
SQL);
    }
}
