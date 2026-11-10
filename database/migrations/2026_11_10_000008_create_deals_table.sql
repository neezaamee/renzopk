CREATE TABLE deals (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(160) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    description TEXT NULL,
    type ENUM('permanent', 'time_limited', 'coupon') NOT NULL DEFAULT 'time_limited',
    discount_label VARCHAR(60) NULL DEFAULT NULL,
    coupon_code VARCHAR(50) NULL DEFAULT NULL,
    image_path VARCHAR(255) NULL DEFAULT NULL,
    start_datetime DATETIME NULL DEFAULT NULL,
    end_datetime DATETIME NULL DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_deals_slug (slug),
    UNIQUE KEY uq_deals_coupon_code (coupon_code),
    KEY idx_deals_visibility (is_active, start_datetime, end_datetime, sort_order),
    KEY idx_deals_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

