-- PortWise PostgreSQL schema

CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    needs_weight BOOLEAN NOT NULL DEFAULT FALSE,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE hs_codes (
    id SERIAL PRIMARY KEY,
    category_id INT NOT NULL REFERENCES categories (id),
    code VARCHAR(20) NOT NULL UNIQUE, -- e.g. '8517.13'
    description VARCHAR(255) NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_hs_codes_category
    ON hs_codes (category_id);

CREATE INDEX idx_hs_codes_search
    ON hs_codes
    USING gin (to_tsvector('english', description));

CREATE TYPE rate_type AS ENUM ('ad_valorem', 'per_kg');

CREATE TABLE duty_rates (
    id SERIAL PRIMARY KEY,
    hs_code_id INT NOT NULL REFERENCES hs_codes (id),
    rate_type rate_type NOT NULL DEFAULT 'ad_valorem',
    duty_pct NUMERIC(6, 3) CHECK (duty_pct BETWEEN 0 AND 100),
    rate_per_kg NUMERIC(12, 2), -- NGN per kg, used when per_kg
    effective_from DATE NOT NULL DEFAULT CURRENT_DATE,
    effective_to DATE, -- NULL = current
    source_ref VARCHAR(255), -- e.g. 'NCS Tariff, p.114'
    created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now(),
    CHECK (
        effective_to IS NULL
        OR effective_to >= effective_from
    ),
    CHECK (
        (rate_type = 'ad_valorem' AND duty_pct IS NOT NULL)
        OR (rate_type = 'per_kg' AND rate_per_kg IS NOT NULL)
    )
);

CREATE INDEX idx_duty_rates_lookup
    ON duty_rates (hs_code_id, effective_from DESC);

CREATE TYPE levy_base AS ENUM ('fob', 'cif', 'duty');

CREATE TABLE levies (
    id SERIAL PRIMARY KEY,
    code VARCHAR(30) NOT NULL UNIQUE, -- SURCHARGE, ETLS, CISS
    name VARCHAR(100) NOT NULL,
    pct NUMERIC(6, 3) NOT NULL CHECK (pct >= 0),
    base levy_base NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    sort_order INT NOT NULL DEFAULT 0,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE settings (
    key VARCHAR(50) PRIMARY KEY, -- e.g. 'vat_pct'
    value VARCHAR(100) NOT NULL,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE fx_rates (
    id BIGSERIAL PRIMARY KEY,
    base_ccy CHAR(3) NOT NULL, -- USD
    quote_ccy CHAR(3) NOT NULL DEFAULT 'NGN',
    rate NUMERIC(18, 6) NOT NULL CHECK (rate > 0),
    source VARCHAR(50) NOT NULL, -- 'exchangerate-api' | 'manual'
    fetched_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_fx_latest
    ON fx_rates (base_ccy, quote_ccy, fetched_at DESC);

CREATE TABLE admin_users (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE TABLE audit_log (
    id BIGSERIAL PRIMARY KEY,
    admin_id INT REFERENCES admin_users (id),
    entity VARCHAR(50) NOT NULL, -- 'duty_rates', 'hs_codes', ...
    entity_id INT,
    action VARCHAR(20) NOT NULL, -- create | update | delete | import
    before_data JSONB,
    after_data JSONB,
    created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX idx_audit_created
    ON audit_log (created_at DESC);
