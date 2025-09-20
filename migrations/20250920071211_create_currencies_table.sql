CREATE TABLE currencies (
    id SERIAL PRIMARY KEY,
    code VARCHAR(3) UNIQUE NOT NULL,
    name VARCHAR(50) NOT NULL,
    exchange_rate DECIMAL(20, 10) NOT NULL,
    surcharge_percentage DECIMAL(5, 2) NOT NULL,
    discount_percentage DECIMAL(5, 2) DEFAULT 0,
    last_updated TIMESTAMP
);

INSERT INTO currencies (code, name, exchange_rate, surcharge_percentage, discount_percentage) VALUES
('USD', 'US Dollar', 0.0808279, 7.5, 0),
('GBP', 'British Pound', 0.0527032, 5, 0),
('EUR', 'Euro', 0.0718710, 5, 2),
('KES', 'Kenyan Shilling', 7.81498, 2.5, 0);