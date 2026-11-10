CREATE TABLE reservations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(191) NULL DEFAULT NULL,
    customer_phone VARCHAR(30) NOT NULL,
    party_size SMALLINT UNSIGNED NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    special_requests TEXT NULL,
    status ENUM('new', 'contacted', 'confirmed', 'cancelled') NOT NULL DEFAULT 'new',
    admin_notes TEXT NULL,
    handled_by_admin_id BIGINT UNSIGNED NULL DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reservations_status_date (status, reservation_date, reservation_time),
    KEY idx_reservations_date (reservation_date),
    KEY idx_reservations_handled_by (handled_by_admin_id),
    CONSTRAINT fk_reservations_admin_id
        FOREIGN KEY (handled_by_admin_id) REFERENCES admins (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

