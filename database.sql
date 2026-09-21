-- database.sql - Complete MySQL Database Schema and Seed Data for Raj Residency
-- Target Database: raj_residency

CREATE DATABASE IF NOT EXISTS `raj_residency` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `raj_residency`;

-- 1. Table structure for `settings`
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `key_name` VARCHAR(100) PRIMARY KEY,
  `value_text` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`key_name`, `value_text`) VALUES
('hotel_name', 'RAJ RESIDENCY'),
('hotel_tagline', 'ROYAL HERITAGE & LUXURY'),
('hotel_address', 'POST OFFICE ROAD, DIST - KORAPUT, ODISHA, PIN - 764020'),
('hotel_phone', '083289 10274'),
('hotel_phone_alt', '+91 83289 10274'),
('hotel_email', 'info@rajresidency.com'),
('hotel_booking_email', 'booking@rajresidency.com'),
('hotel_whatsapp', '918328910274'),
('check_in_time', '12:00 PM'),
('check_out_time', '11:00 AM'),
('currency_symbol', '₹'),
('tax_percentage', '12'),
('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d60462.43577317769!2d82.6800726486328!3d18.81352490000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a3a41b52bb6b825%3A0x6b772c366e792e35!2sKoraput%2C%20Odisha%20764020!5e0!3m2!1sen!2sin!4v1710000000000!5m2!1sen!2sin');

-- 2. Table structure for `banners_and_images` (Dynamic Hero Slides, Archway, About Us photos)
DROP TABLE IF EXISTS `banners_and_images`;
CREATE TABLE `banners_and_images` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `section_name` VARCHAR(100) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `image_path` TEXT NOT NULL,
  `badge_text` VARCHAR(255) NULL,
  `display_order` INT DEFAULT 1,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `banners_and_images` (`id`, `section_name`, `title`, `image_path`, `badge_text`, `display_order`, `is_active`) VALUES
(1, 'hero_slider', 'Imperial Luxury Hotel Lounge', 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1920&q=80', 'ROYAL HERITAGE & LUXURY', 1, 1),
(2, 'hero_slider', 'Executive Deluxe AC Suite', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1920&q=80', 'PREMIUM AC LIVING', 2, 1),
(3, 'hero_slider', 'Raj Residency Palace Landmark', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1920&q=80', 'POST OFFICE ROAD KORAPUT', 3, 1),
(4, 'hero_slider', 'Royal Presidential Suite', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1920&q=80', 'MAJESTIC HOSPITALITY', 4, 1),
(5, 'hero_slider', 'Fine Dining & Banquet Hall', 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=1920&q=80', 'AUTHENTIC ODIA CUISINE', 5, 1),
(6, 'hero_archway', 'Authentic Royal Comfort', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80', 'POST OFFICE ROAD, KORAPUT', 1, 1),
(7, 'hero_preview_1', 'Executive AC Suites', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=400&q=80', 'From ₹2,499/night', 1, 1),
(8, 'hero_preview_2', 'Deomali Tour Cabs', 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80', '24/7 Travel Desk', 1, 1),
(9, 'about_rooms', 'Presidential Bedroom & Lounge', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80', 'Presidential Suite', 1, 1),
(10, 'about_rooms', 'Executive AC Deluxe Room', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80', 'Executive Deluxe', 2, 1),
(11, 'about_rooms', 'Royal Super Deluxe Suite', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80', 'Super Deluxe', 3, 1),
(12, 'about_rooms', 'Spotless Budget Non-AC Room', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80', 'Clean Budget Non-AC', 4, 1),
(13, 'about_dining', 'Multi-Cuisine Royal Dining', 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80', 'Multi-Cuisine Hall', 1, 1),
(14, 'about_dining', 'Fresh Traditional Delicacies', 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80', 'Fresh Local Spices', 2, 1),
(15, 'about_dining', 'Morning Buffet Breakfast', 'https://images.unsplash.com/photo-1533777857889-4be7c70e33f7?auto=format&fit=crop&w=800&q=80', 'Morning Breakfast', 3, 1),
(16, 'about_dining', '24-Hour In-Room Dining Intercom', 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=800&q=80', '24/7 Room Service', 4, 1),
(17, 'about_building', 'Raj Residency Building Architecture', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80', 'POST OFFICE ROAD, KORAPUT', 1, 1);

-- 3. Table structure for `rooms`
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `category` VARCHAR(50) NOT NULL, -- 'ac', 'non_ac', 'suite'
  `type_label` VARCHAR(100) NOT NULL,
  `price_per_night` DECIMAL(10,2) NOT NULL,
  `discount_price` DECIMAL(10,2) NULL,
  `max_guests` INT DEFAULT 2,
  `bed_type` VARCHAR(100) NOT NULL,
  `room_size` VARCHAR(100) DEFAULT '280 sq.ft',
  `featured_image` TEXT NOT NULL,
  `gallery` TEXT NULL,
  `description` TEXT NOT NULL,
  `amenities` TEXT NOT NULL,
  `inclusions` TEXT NULL,
  `is_featured` TINYINT(1) DEFAULT 1,
  `is_available` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `rooms` (`id`, `name`, `slug`, `category`, `type_label`, `price_per_night`, `discount_price`, `max_guests`, `bed_type`, `room_size`, `featured_image`, `gallery`, `description`, `amenities`, `inclusions`, `is_featured`, `is_available`) VALUES
(1, 'Executive AC Deluxe Room', 'executive-ac-deluxe-room', 'ac', 'AC Deluxe Room', 2499.00, 2999.00, 2, 'King Size Bed', '280 sq.ft', 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80', '[\"https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80\",\"https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80\"]', 'Designed for absolute comfort and relaxation, our Executive AC Deluxe room features a plush king-size bed, elegant ambient lighting, split air conditioning, 43-inch Smart LED TV, and a modern attached bathroom with luxury toiletries in Koraput.', '[\"Split Air Conditioner\",\"High-Speed Optical Fiber Wi-Fi\",\"43\\\" Full HD Smart TV\",\"24/7 Hot & Cold Geyser Water\",\"Electric Tea/Coffee Maker\",\"Dedicated Work Desk & Chair\",\"Daily Room Sanitization & Housekeeping\",\"Intercom Phone Facility\"]', '[\"Complimentary High-speed Wi-Fi\",\"Packaged Mineral Water (2 Bottles daily)\",\"Fresh Bath Towels & Luxury Dental/Soap Kits\",\"Free Secure Basement Parking\"]', 1, 1),
(2, 'Royal Super Deluxe AC Room', 'royal-super-deluxe-ac-room', 'ac', 'Royal Super Deluxe', 3499.00, 4199.00, 3, 'Super King Bed + Extra Sofa Bed', '360 sq.ft', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80', '[\"https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80\",\"https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1000&q=80\"]', 'Experience true royal hospitality in our Super Deluxe AC room. Equipped with scenic Koraput valley and city views, high-end interior aesthetics, executive workspace table, seating lounge, and generous living area for family and corporate travelers.', '[\"Heavy-Duty Split AC\",\"Scenic Hill / City View Balcony Window\",\"Complimentary Breakfast Option\",\"High-Speed Optical Fiber Wi-Fi\",\"50\\\" 4K Smart Android TV\",\"Modern Glass Shower Bathroom\",\"Mini Refrigerator\",\"24/7 Priority Room Service\"]', '[\"Welcome Drink on Arrival\",\"Complimentary High-speed Fiber Wi-Fi\",\"Morning Hot Tea / Coffee Set\",\"Free Valet Parking\"]', 1, 1),
(3, 'Comfort Standard Non-AC Room', 'comfort-standard-non-ac-room', 'non_ac', 'Budget Non-AC Room', 1299.00, 1699.00, 2, 'Comfort Queen Bed', '220 sq.ft', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80', '[\"https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80\"]', 'A budget-friendly yet spotless and well-ventilated Non-AC room in Koraput with Queen size bed, fresh crisp linen, clean attached bathroom with 24/7 hot water geyser, and high-speed fiber internet for solo travellers, backpackers, and couples.', '[\"High-Speed Fiber Wi-Fi\",\"Clean Attached Washroom\",\"24/7 Geyser Hot Water\",\"32\\\" HD LED TV with Cable Channels\",\"High-Speed Silent Ceiling Fan\",\"Large Natural Ventilation Windows\",\"Daily Linen Change & Cleaning\"]', '[\"Free High-Speed Wi-Fi\",\"Packaged Drinking Water Bottle\",\"Sanitized Towels & Soaps\",\"Free Parking\"]', 1, 1),
(4, 'Spacious Family Non-AC Room', 'spacious-family-non-ac-room', 'non_ac', 'Family Non-AC Room', 1899.00, 2299.00, 4, 'Twin Double Beds', '320 sq.ft', 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?auto=format&fit=crop&w=1000&q=80', '[\"https://images.unsplash.com/photo-1595576508898-0ad5c879a061?auto=format&fit=crop&w=1000&q=80\"]', 'Ideal for friends and families traveling on a budget who desire a spacious room with twin double beds, natural hill breeze, large windows, and prompt room service right in the center of Koraput town.', '[\"Twin Double Beds (Sleeps 4 Comfortably)\",\"High-Speed Fiber Wi-Fi\",\"Attached Spacious Bathroom\",\"24/7 Hot Water Geyser\",\"40\\\" LED TV with Multi-Language Channels\",\"Seating Chairs & Center Table\",\"24/7 Room Service & Dining Support\"]', '[\"Free High-Speed Wi-Fi\",\"Complimentary Drinking Water\",\"Clean Fresh Linens & Blankets\",\"Free On-site Vehicle Parking\"]', 1, 1),
(5, 'Raj Presidential Royal Suite', 'raj-presidential-royal-suite', 'suite', 'Royal Luxury Suite', 5999.00, 7499.00, 4, 'King Master Bed + Living Lounge', '520 sq.ft', 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80', '[\"https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80\"]', 'The crown jewel of Raj Residency. A majestic presidential suite featuring an extravagant living lounge, separate master bedroom, private panoramic balcony, Jacuzzi bathtub, and regal decor fit for VIPs, executives, and honeymoon couples visiting Koraput.', '[\"Separate Master Bedroom & Living Lounge\",\"Luxury Jacuzzi / Bathtub Setup\",\"Dual Inverter ACs\",\"55\\\" 4K Smart TV in Bedroom & Lounge\",\"Private Balcony with Koraput Hill Panorama\",\"Mini Bar & Refrigerator\",\"Express Check-in & VIP Butler Care\",\"Free Gourmet Breakfast Included\"]', '[\"Complimentary Royal Buffet Breakfast\",\"Welcome Fresh Fruit Basket & Drinks\",\"High-speed Dedicated Fiber Wi-Fi\",\"24/7 Dedicated Concierge & Valet\"]', 1, 1);

-- 3. Table structure for `bookings`
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `booking_number` VARCHAR(50) UNIQUE NOT NULL,
  `room_id` INT NOT NULL,
  `room_name` VARCHAR(255) NOT NULL,
  `guest_name` VARCHAR(255) NOT NULL,
  `guest_email` VARCHAR(255) NOT NULL,
  `guest_phone` VARCHAR(50) NOT NULL,
  `id_proof_type` VARCHAR(50) DEFAULT 'Aadhaar Card',
  `id_proof_number` VARCHAR(100) NULL,
  `check_in` DATE NOT NULL,
  `check_out` DATE NOT NULL,
  `total_nights` INT NOT NULL DEFAULT 1,
  `adults` INT NOT NULL DEFAULT 1,
  `children` INT NOT NULL DEFAULT 0,
  `price_per_night` DECIMAL(10,2) NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'Pay at Hotel',
  `payment_status` VARCHAR(50) DEFAULT 'Pending',
  `booking_status` VARCHAR(50) DEFAULT 'Confirmed',
  `special_requests` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bookings` (`id`, `booking_number`, `room_id`, `room_name`, `guest_name`, `guest_email`, `guest_phone`, `id_proof_type`, `id_proof_number`, `check_in`, `check_out`, `total_nights`, `adults`, `children`, `price_per_night`, `total_amount`, `payment_method`, `payment_status`, `booking_status`, `special_requests`, `created_at`) VALUES
(1, 'RR-2026-1001', 1, 'Executive AC Deluxe Room', 'Prakash Mohanty', 'prakash.mohanty@example.com', '+91 98610 98765', 'Aadhaar Card', 'XXXX-XXXX-4589', '2026-09-20', '2026-09-22', 2, 2, 0, 2499.00, 5597.76, 'Pay at Hotel', 'Pending', 'Confirmed', 'Early check-in around 11:30 AM requested.', NOW());

-- 4. Table structure for `messages`
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 'Debasish Patnaik', 'debasish@example.com', '+91 94371 88990', 'Group Booking Inquiry for Deomali Tour', 'Hello Raj Residency, we are visiting Koraput for Deomali hills sightseeing next weekend and need 4 AC Deluxe rooms for 8 people. Please let us know best group rates.', 0, NOW());

-- 5. Table structure for `reviews`
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `guest_name` VARCHAR(255) NOT NULL,
  `guest_city` VARCHAR(100) DEFAULT 'Guest',
  `room_stayed` VARCHAR(255) DEFAULT 'Executive AC Deluxe',
  `rating` INT NOT NULL DEFAULT 5,
  `title` VARCHAR(255) NOT NULL,
  `review_text` TEXT NOT NULL,
  `is_approved` TINYINT(1) DEFAULT 0,
  `is_featured` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `reviews` (`id`, `guest_name`, `guest_city`, `room_stayed`, `rating`, `title`, `review_text`, `is_approved`, `is_featured`, `created_at`) VALUES
(1, 'Satyabrata Mohapatra', 'Bhubaneswar, Odisha', 'Royal Super Deluxe AC Room', 5, 'Best Hotel Experience in Koraput!', 'We stayed at Raj Residency during our Deomali and Kolab Dam trip. The rooms were spotless, AC was great, and the staff helped us arrange a very good sightseeing cab. Food at the restaurant was delicious and authentic. Highly recommended!', 1, 1, NOW()),
(2, 'Dr. Arvind Rao', 'Visakhapatnam, AP', 'Executive AC Deluxe Room', 5, 'Prime location on Post Office Road & fast Wi-Fi', 'Very convenient location right on Post Office Road. Checking in was super fast. The optical fiber Wi-Fi is blazing fast which made my corporate remote work seamless. Will definitely book again.', 1, 1, NOW()),
(3, 'Pooja & Rakesh Sharma', 'Raipur, Chhattisgarh', 'Comfort Standard Non-AC Room', 5, 'Super clean budget room & warm hospitality', 'Traveling on a budget, we booked the Non-AC room and were pleasantly surprised by how pristine clean the bathroom and linens were. Geyser hot water was 24/7. True Atithi Devo Bhava treatment!', 1, 1, NOW());

-- 6. Table structure for `admins`
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default password is 'admin123'
INSERT INTO `admins` (`id`, `username`, `password_hash`, `full_name`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$7R.49FqX7Y5m4pM6rS.B8u7z8jE1Nqj6yFjR5LgPqZp6N3K9W2v1C', 'Raj Residency Manager', 'admin@rajresidency.com', NOW());
