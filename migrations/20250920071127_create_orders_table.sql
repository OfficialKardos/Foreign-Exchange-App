CREATE TABLE orders (
    id SERIAL PRIMARY KEY,
    currency_id INTEGER NOT NULL REFERENCES currencies(id),
    exchange_rate DECIMAL(20, 10) NOT NULL,
    surcharge_percentage DECIMAL(4, 3) NOT NULL,
    discount_percentage DECIMAL(4, 3) NOT NULL,
    foreign_amount DECIMAL(20, 2) NOT NULL,
    zar_amount DECIMAL(20, 2) NOT NULL,
    surcharge_amount DECIMAL(20, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_orders_currency_id ON orders(currency_id);
