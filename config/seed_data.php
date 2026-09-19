<?php
// config/seed_data.php - Initial Default Data for Raj Residency

class SeedData {
    public static function getDefaultSettings() {
        return [
            'hotel_name' => 'RAJ RESIDENCY',
            'hotel_tagline' => 'ROYAL HERITAGE & LUXURY',
            'hotel_address' => 'POST OFFICE ROAD, DIST - KORAPUT, ODISHA, PIN - 764020',
            'hotel_phone' => '083289 10274',
            'hotel_phone_alt' => '+91 83289 10274',
            'hotel_email' => 'info@rajresidency.com',
            'hotel_booking_email' => 'booking@rajresidency.com',
            'hotel_whatsapp' => '918328910274',
            'check_in_time' => '12:00 PM',
            'check_out_time' => '11:00 AM',
            'currency_symbol' => '₹',
            'tax_percentage' => '12',
            'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d60462.43577317769!2d82.6800726486328!3d18.81352490000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a3a41b52bb6b825%3A0x6b772c366e792e35!2sKoraput%2C%20Odisha%20764020!5e0!3m2!1sen!2sin!4v1710000000000!5m2!1sen!2sin'
        ];
    }

    public static function getDefaultRooms() {
        return [
            [
                'id' => 1,
                'name' => 'Executive AC Deluxe Room',
                'slug' => 'executive-ac-deluxe-room',
                'category' => 'ac',
                'type_label' => 'AC Deluxe Room',
                'price_per_night' => 2499.00,
                'discount_price' => 2999.00,
                'max_guests' => 2,
                'bed_type' => 'King Size Bed',
                'room_size' => '280 sq.ft',
                'featured_image' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80'
                ],
                'description' => 'Designed for absolute comfort and relaxation, our Executive AC Deluxe room features a plush king-size bed, elegant ambient lighting, split air conditioning, 43-inch Smart LED TV, and a modern attached bathroom with premium luxury toiletries in Koraput.',
                'amenities' => [
                    'Split Air Conditioner',
                    'High-Speed Optical Fiber Wi-Fi',
                    '43" Full HD Smart TV',
                    '24/7 Hot & Cold Geyser Water',
                    'Electric Tea/Coffee Maker',
                    'Dedicated Work Desk & Chair',
                    'Daily Room Sanitization & Housekeeping',
                    'Intercom Phone Facility'
                ],
                'inclusions' => [
                    'Complimentary High-speed Wi-Fi',
                    'Packaged Mineral Water (2 Bottles daily)',
                    'Fresh Bath Towels & Luxury Dental/Soap Kits',
                    'Free Secure Basement Parking'
                ],
                'is_featured' => 1,
                'is_available' => 1
            ],
            [
                'id' => 2,
                'name' => 'Royal Super Deluxe AC Room',
                'slug' => 'royal-super-deluxe-ac-room',
                'category' => 'ac',
                'type_label' => 'Royal Super Deluxe',
                'price_per_night' => 3499.00,
                'discount_price' => 4199.00,
                'max_guests' => 3,
                'bed_type' => 'Super King Bed + Extra Sofa Bed',
                'room_size' => '360 sq.ft',
                'featured_image' => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80'
                ],
                'description' => 'Experience true royal hospitality in our Super Deluxe AC room. Equipped with scenic Koraput valley and city views, high-end interior aesthetics, executive workspace table, seating lounge, and generous living area for family and corporate travelers.',
                'amenities' => [
                    'Heavy-Duty Split AC',
                    'Scenic Hill / City View Balcony Window',
                    'Complimentary Breakfast Option',
                    'High-Speed Optical Fiber Wi-Fi',
                    '50" 4K Smart Android TV',
                    'Modern Glass Shower Bathroom',
                    'Mini Refrigerator',
                    '24/7 Priority Room Service'
                ],
                'inclusions' => [
                    'Welcome Drink on Arrival',
                    'Complimentary High-speed Fiber Wi-Fi',
                    'Morning Hot Tea / Coffee Set',
                    'Free Valet Parking'
                ],
                'is_featured' => 1,
                'is_available' => 1
            ],
            [
                'id' => 3,
                'name' => 'Comfort Standard Non-AC Room',
                'slug' => 'comfort-standard-non-ac-room',
                'category' => 'non_ac',
                'type_label' => 'Budget Non-AC Room',
                'price_per_night' => 1299.00,
                'discount_price' => 1699.00,
                'max_guests' => 2,
                'bed_type' => 'Comfort Queen Bed',
                'room_size' => '220 sq.ft',
                'featured_image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80'
                ],
                'description' => 'A budget-friendly yet spotless and well-ventilated Non-AC room in Koraput with Queen size bed, fresh crisp linen, clean attached bathroom with 24/7 hot water geyser, and high-speed fiber internet for solo travellers, backpackers, and couples.',
                'amenities' => [
                    'High-Speed Fiber Wi-Fi',
                    'Clean Attached Washroom',
                    '24/7 Geyser Hot Water',
                    '32" HD LED TV with Cable Channels',
                    'High-Speed Silent Ceiling Fan',
                    'Large Natural Ventilation Windows',
                    'Daily Linen Change & Cleaning'
                ],
                'inclusions' => [
                    'Free High-Speed Wi-Fi',
                    'Packaged Drinking Water Bottle',
                    'Sanitized Towels & Soaps',
                    'Free Parking'
                ],
                'is_featured' => 1,
                'is_available' => 1
            ],
            [
                'id' => 4,
                'name' => 'Spacious Family Non-AC Room',
                'slug' => 'spacious-family-non-ac-room',
                'category' => 'non_ac',
                'type_label' => 'Family Non-AC Room',
                'price_per_night' => 1899.00,
                'discount_price' => 2299.00,
                'max_guests' => 4,
                'bed_type' => 'Twin Double Beds',
                'room_size' => '320 sq.ft',
                'featured_image' => 'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1595576508898-0ad5c879a061?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80'
                ],
                'description' => 'Ideal for friends and families traveling on a budget who desire a spacious room with twin double beds, natural hill breeze, large windows, and prompt room service right in the center of Koraput town.',
                'amenities' => [
                    'Twin Double Beds (Sleeps 4 Comfortably)',
                    'High-Speed Fiber Wi-Fi',
                    'Attached Spacious Bathroom',
                    '24/7 Hot Water Geyser',
                    '40" LED TV with Multi-Language Channels',
                    'Seating Chairs & Center Table',
                    '24/7 Room Service & Dining Support'
                ],
                'inclusions' => [
                    'Free High-Speed Wi-Fi',
                    'Complimentary Drinking Water',
                    'Clean Fresh Linens & Blankets',
                    'Free On-site Vehicle Parking'
                ],
                'is_featured' => 1,
                'is_available' => 1
            ],
            [
                'id' => 5,
                'name' => 'Raj Presidential Royal Suite',
                'slug' => 'raj-presidential-royal-suite',
                'category' => 'suite',
                'type_label' => 'Royal Luxury Suite',
                'price_per_night' => 5999.00,
                'discount_price' => 7499.00,
                'max_guests' => 4,
                'bed_type' => 'King Master Bed + Living Lounge',
                'room_size' => '520 sq.ft',
                'featured_image' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
                'gallery' => [
                    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80'
                ],
                'description' => 'The crown jewel of Raj Residency. A majestic presidential suite featuring an extravagant living lounge, separate master bedroom, private panoramic balcony, Jacuzzi bathtub, and regal decor fit for VIPs, executives, and honeymoon couples visiting Koraput.',
                'amenities' => [
                    'Separate Master Bedroom & Living Lounge',
                    'Luxury Jacuzzi / Bathtub Setup',
                    'Dual Inverter ACs',
                    '55" 4K Smart TV in Bedroom & Lounge',
                    'Private Balcony with Koraput Hill Panorama',
                    'Mini Bar & Refrigerator',
                    'Express Check-in & VIP Butler Care',
                    'Free Gourmet Breakfast Included'
                ],
                'inclusions' => [
                    'Complimentary Royal Buffet Breakfast',
                    'Welcome Fresh Fruit Basket & Drinks',
                    'High-speed Dedicated Fiber Wi-Fi',
                    '24/7 Dedicated Concierge & Valet'
                ],
                'is_featured' => 1,
                'is_available' => 1
            ]
        ];
    }

    public static function getDefaultAdmins() {
        return [
            [
                'id' => 1,
                'username' => 'admin',
                'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
                'full_name' => 'Raj Residency Manager',
                'email' => 'admin@rajresidency.com'
            ]
        ];
    }

    public static function getDefaultReviews() {
        return [
            [
                'id' => 1,
                'guest_name' => 'Satyabrata Mohapatra',
                'guest_city' => 'Bhubaneswar, Odisha',
                'room_stayed' => 'Royal Super Deluxe AC Room',
                'rating' => 5,
                'title' => 'Best Hotel Experience in Koraput!',
                'review_text' => 'We stayed at Raj Residency during our Deomali and Kolab Dam trip. The rooms were spotless, AC was great, and the staff helped us arrange a very good sightseeing cab. Food at the restaurant was delicious and authentic. Highly recommended!',
                'is_approved' => 1,
                'is_featured' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'id' => 2,
                'guest_name' => 'Dr. Arvind Rao',
                'guest_city' => 'Visakhapatnam, AP',
                'room_stayed' => 'Executive AC Deluxe Room',
                'rating' => 5,
                'title' => 'Prime location on Post Office Road & fast Wi-Fi',
                'review_text' => 'Very convenient location right on Post Office Road. Checking in was super fast. The optical fiber Wi-Fi is blazing fast which made my corporate remote work seamless. Will definitely book again.',
                'is_approved' => 1,
                'is_featured' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-12 days'))
            ],
            [
                'id' => 3,
                'guest_name' => 'Pooja & Rakesh Sharma',
                'guest_city' => 'Raipur, Chhattisgarh',
                'room_stayed' => 'Comfort Standard Non-AC Room',
                'rating' => 5,
                'title' => 'Super clean budget room & warm hospitality',
                'review_text' => 'Traveling on a budget, we booked the Non-AC room and were pleasantly surprised by how pristine clean the bathroom and linens were. Geyser hot water was 24/7. True Atithi Devo Bhava treatment!',
                'is_approved' => 1,
                'is_featured' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-18 days'))
            ]
        ];
    }

    public static function getDefaultData() {
        return [
            'settings' => self::getDefaultSettings(),
            'rooms' => self::getDefaultRooms(),
            'reviews' => self::getDefaultReviews(),
            'bookings' => [
                [
                    'id' => 1,
                    'booking_number' => 'RR-2026-1001',
                    'room_id' => 1,
                    'room_name' => 'Executive AC Deluxe Room',
                    'guest_name' => 'Prakash Mohanty',
                    'guest_email' => 'prakash.mohanty@example.com',
                    'guest_phone' => '083289 10274',
                    'id_proof_type' => 'Aadhaar Card',
                    'id_proof_number' => 'XXXX-XXXX-4589',
                    'check_in' => date('Y-m-d', strtotime('+1 day')),
                    'check_out' => date('Y-m-d', strtotime('+3 days')),
                    'total_nights' => 2,
                    'adults' => 2,
                    'children' => 0,
                    'price_per_night' => 2499.00,
                    'total_amount' => 4998.00,
                    'payment_method' => 'Pay at Hotel',
                    'payment_status' => 'Pending',
                    'booking_status' => 'Confirmed',
                    'special_requests' => 'Early check-in around 11:30 AM requested.',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ],
            'messages' => [
                [
                    'id' => 1,
                    'name' => 'Debasish Patnaik',
                    'email' => 'debasish@example.com',
                    'phone' => '083289 10274',
                    'subject' => 'Group Booking Inquiry for Deomali Tour',
                    'message' => 'Hello Raj Residency, we are visiting Koraput for Deomali hills sightseeing next weekend and need 4 AC Deluxe rooms for 8 people. Please let us know best group rates.',
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ],
            'admins' => self::getDefaultAdmins()
        ];
    }

    public static function seedMysql($pdo) {
        $settings = self::getDefaultSettings();
        $stmt = $pdo->prepare("INSERT INTO `settings` (`key_name`, `value_text`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value_text` = ?");
        foreach ($settings as $key => $val) {
            $stmt->execute([$key, $val, $val]);
        }

        $rooms = self::getDefaultRooms();
        $roomStmt = $pdo->prepare("INSERT INTO `rooms` (`id`, `name`, `slug`, `category`, `type_label`, `price_per_night`, `discount_price`, `max_guests`, `bed_type`, `room_size`, `featured_image`, `gallery`, `description`, `amenities`, `inclusions`, `is_featured`, `is_available`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($rooms as $r) {
            $roomStmt->execute([
                $r['id'],
                $r['name'],
                $r['slug'],
                $r['category'],
                $r['type_label'],
                $r['price_per_night'],
                $r['discount_price'],
                $r['max_guests'],
                $r['bed_type'],
                $r['room_size'],
                $r['featured_image'],
                json_encode($r['gallery']),
                $r['description'],
                json_encode($r['amenities']),
                json_encode($r['inclusions']),
                $r['is_featured'],
                $r['is_available']
            ]);
        }

        $reviews = self::getDefaultReviews();
        $revStmt = $pdo->prepare("INSERT INTO `reviews` (`id`, `guest_name`, `guest_city`, `room_stayed`, `rating`, `title`, `review_text`, `is_approved`, `is_featured`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($reviews as $rv) {
            $revStmt->execute([
                $rv['id'],
                $rv['guest_name'],
                $rv['guest_city'],
                $rv['room_stayed'],
                $rv['rating'],
                $rv['title'],
                $rv['review_text'],
                $rv['is_approved'],
                $rv['is_featured'],
                $rv['created_at']
            ]);
        }

        $admins = self::getDefaultAdmins();
        $admStmt = $pdo->prepare("INSERT INTO `admins` (`id`, `username`, `password_hash`, `full_name`, `email`) VALUES (?, ?, ?, ?, ?)");
        foreach ($admins as $a) {
            $admStmt->execute([
                $a['id'],
                $a['username'],
                $a['password_hash'],
                $a['full_name'],
                $a['email']
            ]);
        }
    }
}
