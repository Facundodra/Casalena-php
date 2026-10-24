-- Esquema de Casaleña.
-- Corre solo al crearse el volumen. Si lo cambiás:
--     docker compose down -v && docker compose up -d

-- ============================================================================
-- USERS: jerarquía User -> Admin / Customer en una sola tabla.
-- `type` es el discriminador; las columnas del otro subtipo quedan NULL.
-- ============================================================================
CREATE TABLE users (
    email      VARCHAR(120) NOT NULL,
    name       VARCHAR(100) NOT NULL,
    password   VARCHAR(255) NOT NULL,   -- hash de password_hash(), nunca la contraseña
    type       VARCHAR(10)  NOT NULL,
    job_title  VARCHAR(100) NULL,       -- solo ADMIN
    address    VARCHAR(200) NULL,       -- solo CUSTOMER
    PRIMARY KEY (email),
    CONSTRAINT chk_user_type CHECK (type IN ('ADMIN', 'CUSTOMER')),
    CONSTRAINT chk_user_subtype CHECK (
        (type = 'ADMIN'    AND job_title IS NOT NULL AND address IS NULL) OR
        (type = 'CUSTOMER' AND address IS NOT NULL AND job_title IS NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- MENU_ITEMS: jerarquía MenuItem -> Dish / Drink en una sola tabla.
-- availability y category son enumerados guardados como texto.
-- ============================================================================
CREATE TABLE menu_items (
    id            INT           NOT NULL AUTO_INCREMENT,
    name          VARCHAR(100)  NOT NULL,
    price         DECIMAL(10,2) NOT NULL,   -- DECIMAL, nunca FLOAT: el dinero es exacto
    availability  VARCHAR(10)   NOT NULL,
    type          VARCHAR(10)   NOT NULL,
    category      VARCHAR(10)   NULL,       -- solo DISH
    calories      INT           NULL,       -- solo DISH
    alcoholic     TINYINT(1)    NULL,       -- solo DRINK
    milliliters   INT           NULL,       -- solo DRINK
    PRIMARY KEY (id),
    UNIQUE KEY uq_menu_item_name (name),
    CONSTRAINT chk_menu_item_price        CHECK (price > 0),
    CONSTRAINT chk_menu_item_type         CHECK (type IN ('DISH', 'DRINK')),
    CONSTRAINT chk_menu_item_availability CHECK (availability IN ('LUNCH', 'DINNER', 'ALL_DAY')),
    CONSTRAINT chk_menu_item_category     CHECK (category IN ('STARTER', 'MAIN', 'DESSERT')),
    -- IS NOT NULL explícito: en un CHECK, "NULL > 0" da desconocido y MySQL lo deja pasar.
    CONSTRAINT chk_menu_item_subtype CHECK (
        (type = 'DISH'  AND category IS NOT NULL
                        AND calories IS NOT NULL AND calories > 0
                        AND alcoholic IS NULL AND milliliters IS NULL) OR
        (type = 'DRINK' AND alcoholic IS NOT NULL AND alcoholic IN (0, 1)
                        AND milliliters IS NOT NULL AND milliliters > 0
                        AND category IS NULL AND calories IS NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- FAVORITES: clase asociativa Customer–MenuItem con su fecha.
-- El UNIQUE impide marcar dos veces el mismo; CASCADE: si se borra el ítem, caen.
-- ============================================================================
CREATE TABLE favorites (
    id              INT          NOT NULL AUTO_INCREMENT,
    customer_email  VARCHAR(120) NOT NULL,
    menu_item_id    INT          NOT NULL,
    marked_on       DATE         NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_favorite (customer_email, menu_item_id),
    CONSTRAINT fk_favorite_customer  FOREIGN KEY (customer_email) REFERENCES users (email),
    CONSTRAINT fk_favorite_menu_item FOREIGN KEY (menu_item_id)   REFERENCES menu_items (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================================
-- ORDERS y ORDER_LINES: la composición.
-- Una línea no vive sin su pedido (CASCADE). Un ítem que aparece en un pedido
-- no se puede borrar (RESTRICT): el historial no se reescribe.
-- item_name y unit_price se congelan al confirmar: el historial no cambia
-- aunque después cambie el nombre o el precio del ítem.
-- ============================================================================
CREATE TABLE orders (
    id              INT          NOT NULL AUTO_INCREMENT,
    customer_email  VARCHAR(120) NOT NULL,
    created_at      DATETIME     NOT NULL,
    delivery_mode   VARCHAR(10)  NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_order_delivery_mode CHECK (delivery_mode IN ('DELIVERY', 'PICKUP')),
    CONSTRAINT fk_order_customer FOREIGN KEY (customer_email) REFERENCES users (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_lines (
    id            INT           NOT NULL AUTO_INCREMENT,
    order_id      INT           NOT NULL,
    menu_item_id  INT           NOT NULL,
    item_name     VARCHAR(100)  NOT NULL,
    quantity      INT           NOT NULL,
    unit_price    DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT chk_order_line_quantity CHECK (quantity > 0),
    CONSTRAINT fk_order_line_order     FOREIGN KEY (order_id)     REFERENCES orders (id)
        ON DELETE CASCADE,
    CONSTRAINT fk_order_line_menu_item FOREIGN KEY (menu_item_id) REFERENCES menu_items (id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
