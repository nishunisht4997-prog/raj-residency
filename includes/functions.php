<?php
// includes/functions.php - Global Utility & Model Functions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Format currency
function format_price($amount, $showSymbol = true) {
    $settings = get_settings();
    $symbol = $showSymbol ? ($settings['currency_symbol'] ?? '₹') : '';
    return $symbol . number_format((float)$amount, 0);
}

// Sanitize user inputs
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8');
}

// Get all settings
function get_settings() {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->query("SELECT `key_name`, `value_text` FROM `settings`");
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return $results ?: SeedData::getDefaultSettings();
    } else {
        $data = Database::getJsonData();
        return $data['settings'] ?? SeedData::getDefaultSettings();
    }
}

// Get single setting
function get_setting($key, $default = '') {
    $settings = get_settings();
    return $settings[$key] ?? $default;
}

// Update settings
function update_settings($newSettings) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("INSERT INTO `settings` (`key_name`, `value_text`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value_text` = ?");
        foreach ($newSettings as $k => $v) {
            $stmt->execute([$k, $v, $v]);
        }
        return true;
    } else {
        $data = Database::getJsonData();
        foreach ($newSettings as $k => $v) {
            $data['settings'][$k] = $v;
        }
        Database::saveJsonData($data);
        return true;
    }
}

// Get all rooms (with optional category filter)
function get_all_rooms($category = null, $onlyAvailable = false) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $sql = "SELECT * FROM `rooms` WHERE 1=1";
        $params = [];
        if ($category && $category !== 'all') {
            $sql .= " AND `category` = ?";
            $params[] = $category;
        }
        if ($onlyAvailable) {
            $sql .= " AND `is_available` = 1";
        }
        $sql .= " ORDER BY `id` ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rooms = $stmt->fetchAll();
        foreach ($rooms as &$r) {
            $r['amenities'] = is_string($r['amenities']) ? json_decode($r['amenities'], true) : $r['amenities'];
            $r['gallery'] = is_string($r['gallery']) ? json_decode($r['gallery'], true) : $r['gallery'];
            $r['inclusions'] = is_string($r['inclusions']) ? json_decode($r['inclusions'], true) : $r['inclusions'];
        }
        return $rooms;
    } else {
        $data = Database::getJsonData();
        $rooms = $data['rooms'] ?? [];
        if ($category && $category !== 'all') {
            $rooms = array_filter($rooms, function($r) use ($category) {
                return ($r['category'] ?? '') === $category;
            });
        }
        if ($onlyAvailable) {
            $rooms = array_filter($rooms, function($r) {
                return !empty($r['is_available']);
            });
        }
        return array_values($rooms);
    }
}

// Get room by ID
function get_room_by_id($id) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM `rooms` WHERE `id` = ? LIMIT 1");
        $stmt->execute([$id]);
        $room = $stmt->fetch();
        if ($room) {
            $room['amenities'] = is_string($room['amenities']) ? json_decode($room['amenities'], true) : $room['amenities'];
            $room['gallery'] = is_string($room['gallery']) ? json_decode($room['gallery'], true) : $room['gallery'];
            $room['inclusions'] = is_string($room['inclusions']) ? json_decode($room['inclusions'], true) : $room['inclusions'];
        }
        return $room;
    } else {
        $data = Database::getJsonData();
        $rooms = $data['rooms'] ?? [];
        foreach ($rooms as $r) {
            if ($r['id'] == $id) return $r;
        }
        return null;
    }
}

// Save or Update Room
function save_room($roomData, $id = null) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $amenitiesJson = json_encode(is_array($roomData['amenities']) ? $roomData['amenities'] : explode(',', $roomData['amenities']));
        $galleryJson = json_encode(is_array($roomData['gallery']) ? $roomData['gallery'] : [$roomData['featured_image']]);
        $inclusionsJson = json_encode(is_array($roomData['inclusions']) ? $roomData['inclusions'] : []);

        if ($id) {
            $sql = "UPDATE `rooms` SET `name` = ?, `slug` = ?, `category` = ?, `type_label` = ?, `price_per_night` = ?, `discount_price` = ?, `max_guests` = ?, `bed_type` = ?, `room_size` = ?, `featured_image` = ?, `gallery` = ?, `description` = ?, `amenities` = ?, `inclusions` = ?, `is_featured` = ?, `is_available` = ? WHERE `id` = ?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $roomData['name'],
                $roomData['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $roomData['name']))),
                $roomData['category'],
                $roomData['type_label'],
                $roomData['price_per_night'],
                $roomData['discount_price'] ?? null,
                $roomData['max_guests'],
                $roomData['bed_type'],
                $roomData['room_size'] ?? '280 sq.ft',
                $roomData['featured_image'],
                $galleryJson,
                $roomData['description'],
                $amenitiesJson,
                $inclusionsJson,
                isset($roomData['is_featured']) ? (int)$roomData['is_featured'] : 1,
                isset($roomData['is_available']) ? (int)$roomData['is_available'] : 1,
                $id
            ]);
        } else {
            $sql = "INSERT INTO `rooms` (`name`, `slug`, `category`, `type_label`, `price_per_night`, `discount_price`, `max_guests`, `bed_type`, `room_size`, `featured_image`, `gallery`, `description`, `amenities`, `inclusions`, `is_featured`, `is_available`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                $roomData['name'],
                $roomData['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $roomData['name']))),
                $roomData['category'],
                $roomData['type_label'],
                $roomData['price_per_night'],
                $roomData['discount_price'] ?? null,
                $roomData['max_guests'],
                $roomData['bed_type'],
                $roomData['room_size'] ?? '280 sq.ft',
                $roomData['featured_image'],
                $galleryJson,
                $roomData['description'],
                $amenitiesJson,
                $inclusionsJson,
                isset($roomData['is_featured']) ? (int)$roomData['is_featured'] : 1,
                isset($roomData['is_available']) ? (int)$roomData['is_available'] : 1
            ]);
        }
    } else {
        $data = Database::getJsonData();
        if ($id) {
            foreach ($data['rooms'] as $k => $r) {
                if ($r['id'] == $id) {
                    $roomData['id'] = (int)$id;
                    $roomData['amenities'] = is_array($roomData['amenities']) ? $roomData['amenities'] : explode("\n", str_replace("\r", "", $roomData['amenities']));
                    $roomData['gallery'] = is_array($roomData['gallery']) ? $roomData['gallery'] : [$roomData['featured_image']];
                    $data['rooms'][$k] = array_merge($r, $roomData);
                    Database::saveJsonData($data);
                    return true;
                }
            }
        } else {
            $newId = count($data['rooms']) > 0 ? max(array_column($data['rooms'], 'id')) + 1 : 1;
            $roomData['id'] = $newId;
            $roomData['slug'] = $roomData['slug'] ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $roomData['name'])));
            $roomData['amenities'] = is_array($roomData['amenities']) ? $roomData['amenities'] : explode("\n", str_replace("\r", "", $roomData['amenities']));
            $roomData['gallery'] = is_array($roomData['gallery']) ? $roomData['gallery'] : [$roomData['featured_image']];
            $data['rooms'][] = $roomData;
            Database::saveJsonData($data);
            return true;
        }
        return false;
    }
}

// Delete Room
function delete_room($id) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("DELETE FROM `rooms` WHERE `id` = ?");
        return $stmt->execute([$id]);
    } else {
        $data = Database::getJsonData();
        $data['rooms'] = array_filter($data['rooms'], function($r) use ($id) {
            return $r['id'] != $id;
        });
        Database::saveJsonData($data);
        return true;
    }
}

// Create Booking
function create_booking($bookingData) {
    $bookingNumber = 'RR-' . date('Y') . '-' . rand(1000, 9999);
    $bookingData['booking_number'] = $bookingNumber;
    $bookingData['created_at'] = date('Y-m-d H:i:s');
    $bookingData['booking_status'] = $bookingData['booking_status'] ?? 'Confirmed';
    $bookingData['payment_status'] = $bookingData['payment_status'] ?? 'Pending';

    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $sql = "INSERT INTO `bookings` (`booking_number`, `room_id`, `room_name`, `guest_name`, `guest_email`, `guest_phone`, `id_proof_type`, `id_proof_number`, `check_in`, `check_out`, `total_nights`, `adults`, `children`, `price_per_night`, `total_amount`, `payment_method`, `payment_status`, `booking_status`, `special_requests`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $res = $stmt->execute([
            $bookingData['booking_number'],
            $bookingData['room_id'],
            $bookingData['room_name'],
            $bookingData['guest_name'],
            $bookingData['guest_email'],
            $bookingData['guest_phone'],
            $bookingData['id_proof_type'] ?? 'Aadhaar Card',
            $bookingData['id_proof_number'] ?? '',
            $bookingData['check_in'],
            $bookingData['check_out'],
            $bookingData['total_nights'],
            $bookingData['adults'],
            $bookingData['children'] ?? 0,
            $bookingData['price_per_night'],
            $bookingData['total_amount'],
            $bookingData['payment_method'] ?? 'Pay at Hotel',
            $bookingData['payment_status'],
            $bookingData['booking_status'],
            $bookingData['special_requests'] ?? '',
            $bookingData['created_at']
        ]);
        return $res ? $bookingNumber : false;
    } else {
        $data = Database::getJsonData();
        $newId = count($data['bookings'] ?? []) > 0 ? max(array_column($data['bookings'], 'id')) + 1 : 1;
        $bookingData['id'] = $newId;
        $data['bookings'][] = $bookingData;
        Database::saveJsonData($data);
        return $bookingNumber;
    }
}

// Get Booking by Reference Number
function get_booking_by_number($bookingNumber) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM `bookings` WHERE `booking_number` = ? LIMIT 1");
        $stmt->execute([$bookingNumber]);
        return $stmt->fetch();
    } else {
        $data = Database::getJsonData();
        foreach ($data['bookings'] ?? [] as $b) {
            if ($b['booking_number'] === $bookingNumber) return $b;
        }
        return null;
    }
}

// Get All Bookings
function get_all_bookings() {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->query("SELECT * FROM `bookings` ORDER BY `id` DESC");
        return $stmt->fetchAll();
    } else {
        $data = Database::getJsonData();
        $bookings = $data['bookings'] ?? [];
        usort($bookings, function($a, $b) {
            return $b['id'] - $a['id'];
        });
        return $bookings;
    }
}

// Update Booking Status
function update_booking_status($id, $status, $paymentStatus = null) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        if ($paymentStatus) {
            $stmt = $pdo->prepare("UPDATE `bookings` SET `booking_status` = ?, `payment_status` = ? WHERE `id` = ?");
            return $stmt->execute([$status, $paymentStatus, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE `bookings` SET `booking_status` = ? WHERE `id` = ?");
            return $stmt->execute([$status, $id]);
        }
    } else {
        $data = Database::getJsonData();
        foreach ($data['bookings'] as &$b) {
            if ($b['id'] == $id) {
                $b['booking_status'] = $status;
                if ($paymentStatus) $b['payment_status'] = $paymentStatus;
                Database::saveJsonData($data);
                return true;
            }
        }
        return false;
    }
}

// Delete Booking
function delete_booking($id) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("DELETE FROM `bookings` WHERE `id` = ?");
        return $stmt->execute([$id]);
    } else {
        $data = Database::getJsonData();
        $data['bookings'] = array_filter($data['bookings'], function($b) use ($id) {
            return $b['id'] != $id;
        });
        Database::saveJsonData($data);
        return true;
    }
}

// Save Contact Message
function save_contact_message($messageData) {
    $messageData['created_at'] = date('Y-m-d H:i:s');
    $messageData['is_read'] = 0;

    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $sql = "INSERT INTO `messages` (`name`, `email`, `phone`, `subject`, `message`, `is_read`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $messageData['name'],
            $messageData['email'],
            $messageData['phone'],
            $messageData['subject'],
            $messageData['message'],
            $messageData['is_read'],
            $messageData['created_at']
        ]);
    } else {
        $data = Database::getJsonData();
        $newId = count($data['messages'] ?? []) > 0 ? max(array_column($data['messages'], 'id')) + 1 : 1;
        $messageData['id'] = $newId;
        $data['messages'][] = $messageData;
        Database::saveJsonData($data);
        return true;
    }
}

// Get All Messages
function get_all_messages() {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->query("SELECT * FROM `messages` ORDER BY `id` DESC");
        return $stmt->fetchAll();
    } else {
        $data = Database::getJsonData();
        $messages = $data['messages'] ?? [];
        usort($messages, function($a, $b) {
            return $b['id'] - $a['id'];
        });
        return $messages;
    }
}

// Delete Message
function delete_message($id) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("DELETE FROM `messages` WHERE `id` = ?");
        return $stmt->execute([$id]);
    } else {
        $data = Database::getJsonData();
        $data['messages'] = array_filter($data['messages'], function($m) use ($id) {
            return $m['id'] != $id;
        });
        Database::saveJsonData($data);
        return true;
    }
}

// Admin Authentication Check
function check_admin_auth() {
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

// Admin Login Authenticate
function admin_login($username, $password) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `username` = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        if ($admin && password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            return true;
        }
    } else {
        $data = Database::getJsonData();
        $admins = $data['admins'] ?? [];
        foreach ($admins as $a) {
            if ($a['username'] === $username && password_verify($password, $a['password_hash'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $a['id'];
                $_SESSION['admin_username'] = $a['username'];
                $_SESSION['admin_name'] = $a['full_name'];
                return true;
            }
        }
    }
    return false;
}

// -------------------------------------------------------------
// Guest Reviews & Ratings Functions
// -------------------------------------------------------------

// Get approved reviews for homepage & public display
function get_approved_reviews($limit = null) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $sql = "SELECT * FROM `reviews` WHERE `is_approved` = 1 ORDER BY `is_featured` DESC, `id` DESC";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    } else {
        $data = Database::getJsonData();
        $reviews = $data['reviews'] ?? [];
        $approved = array_filter($reviews, function($r) {
            return !empty($r['is_approved']);
        });
        usort($approved, function($a, $b) {
            if (($b['is_featured'] ?? 0) !== ($a['is_featured'] ?? 0)) {
                return ($b['is_featured'] ?? 0) - ($a['is_featured'] ?? 0);
            }
            return $b['id'] - $a['id'];
        });
        if ($limit) {
            return array_slice(array_values($approved), 0, (int)$limit);
        }
        return array_values($approved);
    }
}

// Get all reviews (for Admin moderation)
function get_all_reviews() {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->query("SELECT * FROM `reviews` ORDER BY `id` DESC");
        return $stmt->fetchAll();
    } else {
        $data = Database::getJsonData();
        $reviews = $data['reviews'] ?? [];
        usort($reviews, function($a, $b) {
            return $b['id'] - $a['id'];
        });
        return $reviews;
    }
}

// Save a new guest review (submitted from website, pending approval by default)
function save_review($reviewData) {
    $reviewData['rating'] = max(1, min(5, (int)($reviewData['rating'] ?? 5)));
    $reviewData['is_approved'] = isset($reviewData['is_approved']) ? (int)$reviewData['is_approved'] : 0;
    $reviewData['is_featured'] = isset($reviewData['is_featured']) ? (int)$reviewData['is_featured'] : 0;
    $reviewData['created_at'] = date('Y-m-d H:i:s');

    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $sql = "INSERT INTO `reviews` (`guest_name`, `guest_city`, `room_stayed`, `rating`, `title`, `review_text`, `is_approved`, `is_featured`, `created_at`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $reviewData['guest_name'],
            $reviewData['guest_city'] ?? 'Guest',
            $reviewData['room_stayed'] ?? 'Executive AC Deluxe',
            $reviewData['rating'],
            $reviewData['title'],
            $reviewData['review_text'],
            $reviewData['is_approved'],
            $reviewData['is_featured'],
            $reviewData['created_at']
        ]);
    } else {
        $data = Database::getJsonData();
        $newId = count($data['reviews'] ?? []) > 0 ? max(array_column($data['reviews'], 'id')) + 1 : 1;
        $reviewData['id'] = $newId;
        $data['reviews'][] = $reviewData;
        Database::saveJsonData($data);
        return true;
    }
}

// Update review approval status (Admin)
function update_review_status($id, $isApproved, $isFeatured = null) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        if ($isFeatured !== null) {
            $stmt = $pdo->prepare("UPDATE `reviews` SET `is_approved` = ?, `is_featured` = ? WHERE `id` = ?");
            return $stmt->execute([(int)$isApproved, (int)$isFeatured, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE `reviews` SET `is_approved` = ? WHERE `id` = ?");
            return $stmt->execute([(int)$isApproved, $id]);
        }
    } else {
        $data = Database::getJsonData();
        foreach ($data['reviews'] as &$r) {
            if ($r['id'] == $id) {
                $r['is_approved'] = (int)$isApproved;
                if ($isFeatured !== null) $r['is_featured'] = (int)$isFeatured;
                Database::saveJsonData($data);
                return true;
            }
        }
        return false;
    }
}

// Delete review (Admin)
function delete_review($id) {
    $mode = Database::getStorageMode();
    if ($mode === 'mysql') {
        $pdo = Database::getPDO();
        $stmt = $pdo->prepare("DELETE FROM `reviews` WHERE `id` = ?");
        return $stmt->execute([$id]);
    } else {
        $data = Database::getJsonData();
        $data['reviews'] = array_filter($data['reviews'], function($r) use ($id) {
            return $r['id'] != $id;
        });
        Database::saveJsonData($data);
        return true;
    }
}

// Calculate review statistics (Average Rating & Total Count)
function get_review_stats() {
    $approved = get_approved_reviews();
    $total = count($approved);
    if ($total === 0) {
        return ['average' => 4.8, 'total' => 0, 'five_star' => 0];
    }
    $sum = array_sum(array_column($approved, 'rating'));
    $fiveStar = count(array_filter($approved, function($r) { return $r['rating'] == 5; }));
    return [
        'average' => round($sum / $total, 1),
        'total' => $total,
        'five_star' => $fiveStar
    ];
}

