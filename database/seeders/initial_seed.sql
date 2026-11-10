-- Optional baseline configuration & starter tags for Renzo.
-- Insert your first Owner admin manually with your real email before logging in:
-- INSERT INTO admins (name, email, role, is_active) VALUES ('Owner Name', 'owner@example.com', 'owner', 1);

INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('restaurant_name', 'Renzo', 'general'),
('restaurant_tagline', 'Wood-Fired Kitchen & Contemporary Table', 'general'),
('currency_symbol', 'Rs. ', 'general'),
('whatsapp_number', '923111200925', 'contact'),
('contact_phone', '+92 311 1200925', 'contact'),
('contact_email', 'hello@renzo.example', 'contact'),
('notification_email', 'neezaamee@gmail.com', 'contact'),
('contact_address', '124 Harbor Lane, Culinary District', 'contact'),
('map_embed_url', '', 'contact'),
('opening_hours', "Tue - Thu: 12:00 PM - 10:00 PM\nFri - Sun: 12:00 PM - 11:30 PM\nMon: Closed", 'hours'),
('careers_email', 'neezaamee@gmail.com', 'careers'),
('careers_intro', 'We are always looking for passionate chefs, servers, and hospitality professionals to join the Renzo kitchen and dining room.', 'careers'),
('careers_positions', "Line Cook (Full-Time)\nPastry Sous Chef (Full-Time)\nFront of House Host / Server (Part-Time)", 'careers'),
('social_instagram', '', 'social'),
('social_facebook', '', 'social'),
('social_tiktok', '', 'social')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

INSERT INTO tags (name, slug, color) VALUES
('Veg', 'veg', '#15803d'),
('Non-Veg', 'non-veg', '#b91c1c'),
('Vegan', 'vegan', '#166534'),
('Gluten-Free', 'gluten-free', '#0369a1'),
('Spicy', 'spicy', '#c2410c'),
('Contains Nuts', 'contains-nuts', '#a16207')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO slider_items (id, title, subtitle, cta_text, cta_url, image_path, alt_text, sort_order, is_active) VALUES
(1, 'Opening soon', 'Wood-fired seasonal dishes, warm hospitality, and curated evening pours in the heart of town.', 'Explore Our Menu', '/menu', NULL, 'Renzo Opening Soon', 1, 1),
(2, 'Opening soon — Come sit with us', 'Intimate booths, hearth-side dining, and an atmosphere built for unhurried conversation.', 'Reserve a Table', '#contact', NULL, 'Renzo Dining Room', 2, 1),
(3, 'Opening soon — Crafted with care', 'Every sauce, dough, and reduction prepared daily from scratch by our culinary team.', 'View Current Deals', '/deals', NULL, 'Renzo Kitchen Craft', 3, 1)
ON DUPLICATE KEY UPDATE title = VALUES(title), subtitle = VALUES(subtitle);
