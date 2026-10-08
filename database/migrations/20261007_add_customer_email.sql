ALTER TABLE orders
    ADD COLUMN customer_email VARCHAR(254) NULL AFTER customer_name;

CREATE INDEX idx_orders_customer_email ON orders (customer_email);
