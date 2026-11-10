-- Demo dataset for local Renzo UI & CMS testing (idempotent via fixed IDs / ON DUPLICATE KEY UPDATE).
-- Requires initial_seed.sql (tags 1..6) to be imported first.

INSERT INTO menu_categories (id, name, slug, description, sort_order, is_active) VALUES
(1, 'Starters & Small Plates', 'starters-small-plates', 'Wood-fired breads, cured plates, and seasonal starters.', 1, 1),
(2, 'Wood-Fired Mains', 'wood-fired-mains', 'Prime cuts and line-caught seafood roasted over seasoned oak embers.', 2, 1),
(3, 'Artisan Pastas', 'artisan-pastas', 'Hand-rolled daily with stone-milled semolina and slow reductions.', 3, 1),
(4, 'Desserts & Pours', 'desserts-pours', 'Pastry-crafted finishes and zero-proof botanical pairings.', 4, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), sort_order = VALUES(sort_order), is_active = VALUES(is_active);

INSERT INTO menu_items (id, category_id, name, slug, description, price, is_featured, is_available, is_active, sort_order) VALUES
(1, 1, 'Charred Burrata & Heirloom Tomato', 'charred-burrata-heirloom-tomato', 'Creamy pugliese burrata, blistered vine tomatoes, aged balsamic glaze, basil oil.', 1650.00, 1, 1, 1, 1),
(2, 1, 'Smoked Lamb Meatballs', 'smoked-lamb-meatballs', 'Spiced lamb polpette glazed in roasted red pepper harissa and herb yogurt.', 1950.00, 0, 1, 1, 2),
(3, 1, 'Truffle Wild Mushroom Bruschetta', 'truffle-wild-mushroom-bruschetta', 'Wood-toasted sourdough topped with thyme roasted mushrooms and black truffle drizzle.', 1450.00, 0, 1, 1, 3),

(4, 2, 'Oak-Roasted Ribeye', 'oak-roasted-ribeye', 'Dry-aged ribeye finished with rosemary bone-marrow butter and flaky sea salt.', 4850.00, 1, 1, 1, 1),
(5, 2, 'Herb-Crusted Sea Bass', 'herb-crusted-sea-bass', 'Pan-seared sea bass fillet, saffron fennel purée, charred lemon.', 3650.00, 1, 1, 1, 2),
(6, 2, 'Charred Cauliflower Steak', 'charred-cauliflower-steak', 'Hearth-roasted cauliflower over toasted almond romesco and pomegranate.', 2150.00, 0, 1, 1, 3),

(7, 3, 'Saffron Tagliatelle', 'saffron-tagliatelle', 'Silky ribbon pasta tossed with cultured butter, Parmigiano, and toasted pine nuts.', 2450.00, 1, 1, 1, 1),
(8, 3, 'Spicy Calabrian Rigatoni', 'spicy-calabrian-rigatoni', 'Slow-simmered San Marzano tomato sauce infused with Calabrian chilies and sweet basil.', 2350.00, 0, 1, 1, 2),
(9, 3, 'Braised Beef Ragu Pappardelle', 'braised-beef-ragu-pappardelle', 'Eight-hour red-wine braised short rib ragù folded through wide egg pappardelle.', 2950.00, 1, 1, 1, 3),

(10, 4, 'Hazelnut Gianduja Tart', 'hazelnut-gianduja-tart', 'Dark chocolate ganache tart with roasted Piedmont hazelnuts and gold-leaf dust.', 1350.00, 1, 1, 1, 1),
(11, 4, 'Espresso Tiramisu', 'espresso-tiramisu', 'Savoiardi soaked in single-origin espresso layered with whipped mascarpone.', 1250.00, 0, 1, 1, 2),
(12, 4, 'Blood Orange & Rosemary Spritz', 'blood-orange-rosemary-spritz', 'Fresh blood orange reduction, charred rosemary syrup, and sparkling botanical tonic.', 850.00, 0, 1, 1, 3)
ON DUPLICATE KEY UPDATE name = VALUES(name), description = VALUES(description), price = VALUES(price), is_featured = VALUES(is_featured);

INSERT INTO menu_item_variants (id, menu_item_id, name, price, is_available, sort_order) VALUES
(1, 4, '300g Cut', 4850.00, 1, 1),
(2, 4, '450g Bone-In Cut', 6400.00, 1, 2),
(3, 7, 'Half Portion', 1650.00, 1, 1),
(4, 7, 'Full Portion', 2450.00, 1, 2)
ON DUPLICATE KEY UPDATE name = VALUES(name), price = VALUES(price);

-- Tags assumed from initial_seed.sql: 1=Veg, 2=Non-Veg, 3=Vegan, 4=Gluten-Free, 5=Spicy, 6=Contains Nuts
INSERT IGNORE INTO menu_item_tags (menu_item_id, tag_id) VALUES
(1, 1), (1, 4),
(2, 2), (2, 5),
(3, 1), (3, 3),
(4, 2), (4, 4),
(5, 2), (5, 4),
(6, 1), (6, 3), (6, 4), (6, 6),
(7, 1), (7, 6),
(8, 1), (8, 5),
(9, 2),
(10, 1), (10, 6),
(11, 1),
(12, 1), (12, 3), (12, 4);

INSERT IGNORE INTO deals (title, slug, description, type, discount_label, coupon_code, start_datetime, end_datetime, is_active, sort_order) VALUES
('Chef''s Midweek Tasting', 'chefs-midweek-tasting', 'Enjoy 15% off our three-course wood-fired tasting table every Tuesday through Thursday.', 'permanent', '15% OFF', NULL, NULL, NULL, 1, 1),
('Weekend Hearth Feast', 'weekend-hearth-feast', 'Special sharing platter and dessert pairing for tables of four or more.', 'time_limited', '20% OFF', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(NOW(), INTERVAL 30 DAY), 1, 2),
('Welcome Table Voucher', 'welcome-table-voucher', 'Present code RENZO500 when dining in to save Rs. 500 on orders above Rs. 4,000.', 'coupon', 'Rs. 500 OFF', 'RENZO500', NULL, DATE_ADD(NOW(), INTERVAL 60 DAY), 1, 3),
('Past Preview Special (Expired)', 'past-preview-special', 'Historical expired promotion kept in admin to verify automatic SQL hiding without cron.', 'time_limited', '50% OFF', NULL, '2025-01-01 00:00:00', '2025-02-01 00:00:00', 1, 4);

INSERT INTO gallery_items (id, type, title, caption, category, image_path, youtube_id, alt_text, sort_order, is_active) VALUES
(4, 'youtube', 'Inside the Oak Hearth', 'Watch our culinary team prepare wood-fired service.', 'Interior', NULL, 'M7lc1UVf-VE', 'Renzo Kitchen Showcase Video', 4, 1),
(5, 'youtube', 'Private Dining & Chef''s Table', 'Behind the scenes of an intimate evening tasting event.', 'Events', NULL, 'aqz-KE-bpKQ', 'Renzo Private Events Video', 5, 1)
ON DUPLICATE KEY UPDATE title = VALUES(title), youtube_id = VALUES(youtube_id), category = VALUES(category);

INSERT INTO reservations (id, customer_name, customer_email, customer_phone, party_size, reservation_date, reservation_time, special_requests, status) VALUES
(1, 'Amina Tariq', 'amina@example.com', '+92 300 1122334', 4, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '20:00:00', 'Corner booth near the hearth if available.', 'new'),
(2, 'Bilal Ahmed', 'bilal@example.com', '+92 321 9988776', 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '19:30:00', 'Anniversary dinner.', 'contacted')
ON DUPLICATE KEY UPDATE customer_name = VALUES(customer_name);

INSERT INTO contact_messages (id, name, email, phone, subject, inquiry_type, message, is_read) VALUES
(1, 'Sana Malik', 'sana@example.com', '+92 333 4455667', 'Private Anniversary Dinner', 'catering', 'Looking to book a private dining table for 18 guests next month. Could you share custom menu options?', 0),
(2, 'Hassan Raza', 'hassan@example.com', NULL, 'Compliments to the Pastry Chef', 'feedback', 'Loved the Hazelnut Gianduja Tart during our preview tasting!', 1)
ON DUPLICATE KEY UPDATE subject = VALUES(subject);
