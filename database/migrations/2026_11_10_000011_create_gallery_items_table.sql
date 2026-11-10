CREATE TABLE gallery_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    type ENUM('photo', 'youtube') NOT NULL DEFAULT 'photo',
    title VARCHAR(160) NULL DEFAULT NULL,
    caption VARCHAR(255) NULL DEFAULT NULL,
    category VARCHAR(80) NULL DEFAULT NULL,
    image_path VARCHAR(255) NULL DEFAULT NULL,
    youtube_id VARCHAR(32) NULL DEFAULT NULL,
    alt_text VARCHAR(180) NULL DEFAULT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_gallery_active_sort (is_active, sort_order),
    KEY idx_gallery_category (category),
    KEY idx_gallery_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
