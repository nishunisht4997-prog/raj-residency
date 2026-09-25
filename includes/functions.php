<?php
// includes/functions.php - 100% Pure MySQL Utility & Model Functions for Raj Residency

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Kolkata');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/seed_data.php';

// Format Indian Standard Time
function format_datetime($datetimeStr, $format = 'd M Y, h:i A') {
    if (empty($datetimeStr)) return '';
    try {
        $dt = new DateTime($datetimeStr);
        $dt->setTimezone(new DateTimeZone('Asia/Kolkata'));
        return $dt->format($format);
    } catch (Exception $e) {
        return date($format, strtotime($datetimeStr));
    }
}

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

// Set flash message
function set_flash_message($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

// Get flash message and clear it
function get_flash_message($type) {
    if (isset($_SESSION['flash_' . $type])) {
        $msg = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $msg;
    }
    return null;
}

// -------------------------------------------------------------
// Direct File Upload Helpers (Mobile Phone Camera/Gallery & PC)
// -------------------------------------------------------------

/**
 * Handles direct single image upload from Mobile Camera/Gallery or PC
 * @param array $fileArray $_FILES['input_name']
 * @param string $subfolder 'hero' | 'archway' | 'rooms' | 'about' | 'settings' | 'general'
 * @return string|false Relative path 'uploads/<subfolder>/<filename>' or false on failure
 */
function upload_media_file($fileArray, $subfolder = 'rooms') {
    if (!isset($fileArray) || !is_array($fileArray)) {
        return false;
    }
    
    if ($fileArray['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png' => 'png',
        'image/x-png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/avif' => 'avif'
    ];
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileArray['tmp_name']);
    finfo_close($finfo);
    
    $ext = '';
    if (isset($allowedMimes[$mimeType])) {
        $ext = $allowedMimes[$mimeType];
    } else {
        $origExt = strtolower(pathinfo($fileArray['name'], PATHINFO_EXTENSION));
        if (in_array($origExt, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'])) {
            $ext = ($origExt === 'jpeg') ? 'jpg' : $origExt;
        } else {
            return false;
        }
    }
    
    $targetDir = __DIR__ . '/../uploads/' . trim($subfolder, '/');
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0777, true);
    }
    
    $uniqueName = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $targetPath = $targetDir . '/' . $uniqueName;
    
    if (move_uploaded_file($fileArray['tmp_name'], $targetPath)) {
        return 'uploads/' . trim($subfolder, '/') . '/' . $uniqueName;
    }
    
    return false;
}

/**
 * Handles direct multi-image upload for Room Galleries
 * @param array $filesArray $_FILES['gallery_files']
 * @param string $subfolder
 * @return array Array of relative upload paths
 */
function upload_multiple_media_files($filesArray, $subfolder = 'rooms') {
    $uploadedPaths = [];
    if (!isset($filesArray['name']) || !is_array($filesArray['name'])) {
        return $uploadedPaths;
    }
    
    $fileCount = count($filesArray['name']);
    for ($i = 0; $i < $fileCount; $i++) {
        if ($filesArray['error'][$i] === UPLOAD_ERR_OK) {
            $singleFile = [
                'name'     => $filesArray['name'][$i],
                'type'     => $filesArray['type'][$i],
                'tmp_name' => $filesArray['tmp_name'][$i],
                'error'    => $filesArray['error'][$i],
                'size'     => $filesArray['size'][$i],
            ];
            $savedPath = upload_media_file($singleFile, $subfolder);
            if ($savedPath) {
                $uploadedPaths[] = $savedPath;
            }
        }
    }
    return $uploadedPaths;
}

// -------------------------------------------------------------
// Pure MySQL Settings Model Functions
// -------------------------------------------------------------

function get_settings() {
    $pdo = Database::getPDO();
    $stmt = $pdo->query("SELECT `key_name`, `value_text` FROM `settings`");
    $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    return $results ?: SeedData::getDefaultSettings();
}

function get_setting($key, $default = '') {
    $settings = get_settings();
    return $settings[$key] ?? $default;
}

function update_settings($newSettings) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("INSERT INTO `settings` (`key_name`, `value_text`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value_text` = ?");
    foreach ($newSettings as $k => $v) {
        $stmt->execute([$k, $v, $v]);
    }
    return true;
}

// -------------------------------------------------------------
// Bulletproof Cloud Image Resolution & Fallback Helpers
// -------------------------------------------------------------

function resolve_media_url($imagePath, $fallbackKey = null, $type = 'banner') {
    if (empty($imagePath)) {
        return get_default_media_fallback($fallbackKey, $type);
    }
    
    // If already an absolute web URL (http:// or https://), return it directly
    if (strpos($imagePath, 'http://') === 0 || strpos($imagePath, 'https://') === 0) {
        return $imagePath;
    }
    
    // If it's a relative uploads path, verify if file exists on this server disk
    $cleanPath = ltrim($imagePath, '/');
    $fullDiskPath = __DIR__ . '/../' . $cleanPath;
    
    if (file_exists($fullDiskPath) && !is_dir($fullDiskPath)) {
        return $imagePath;
    }
    
    // File missing on disk (e.g. Render container wiped /uploads) -> return fallback image!
    return get_default_media_fallback($fallbackKey, $type);
}

function get_default_media_fallback($key = null, $type = 'banner') {
    static $defaultRoomsMap = null;
    static $defaultBannersMap = null;
    
    if ($defaultRoomsMap === null) {
        $defaultRoomsMap = [
            1 => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80',
            2 => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=1000&q=80',
            3 => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1000&q=80',
            4 => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
            5 => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1000&q=80'
        ];
        $defaultBannersMap = [
            'hero_slider_1' => 'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1920&q=80',
            'hero_slider_2' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1920&q=80',
            'hero_slider_3' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1920&q=80',
            'hero_slider_4' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1920&q=80',
            'hero_slider_5' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=1920&q=80',
            'hero_archway'   => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80',
            'hero_preview_1' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=400&q=80',
            'hero_preview_2' => 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
            'about_rooms_1'  => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800&q=80',
            'about_rooms_2'  => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=800&q=80',
            'about_rooms_3'  => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=800&q=80',
            'about_rooms_4'  => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80',
            'about_dining_1' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=800&q=80',
            'about_dining_2' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=800&q=80',
            'about_dining_3' => 'https://images.unsplash.com/photo-1533777857889-4be7c70e33f7?auto=format&fit=crop&w=800&q=80',
            'about_dining_4' => 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=800&q=80',
            'about_building' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80'
        ];
    }
    
    if ($type === 'room') {
        return $defaultRoomsMap[$key] ?? $defaultRoomsMap[1];
    }
    
    if ($type === 'hero_preview_1') return $defaultBannersMap['hero_preview_1'];
    if ($type === 'hero_preview_2') return $defaultBannersMap['hero_preview_2'];
    if ($type === 'hero_archway') return $defaultBannersMap['hero_archway'];
    
    if (isset($defaultBannersMap[$type])) {
        return $defaultBannersMap[$type];
    }
    
    $combinedKey = $type . '_' . $key;
    if (isset($defaultBannersMap[$combinedKey])) {
        return $defaultBannersMap[$combinedKey];
    }
    
    return 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80';
}

// -------------------------------------------------------------
// Pure MySQL Banners & Dynamic Section Images CRUD (Hero, Archway, About)
// -------------------------------------------------------------

function get_section_images($sectionName = null, $onlyActive = true) {
    $pdo = Database::getPDO();
    $sql = "SELECT * FROM `banners_and_images` WHERE 1=1";
    $params = [];
    if ($sectionName) {
        $sql .= " AND `section_name` = ?";
        $params[] = $sectionName;
    }
    if ($onlyActive) {
        $sql .= " AND `is_active` = 1";
    }
    $sql .= " ORDER BY `display_order` ASC, `id` ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    if ($results) {
        foreach ($results as &$b) {
            $b['image_path'] = resolve_media_url($b['image_path'], $b['display_order'] ?? $b['id'], $b['section_name']);
        }
    }
    return $results ?: [];
}

function get_all_section_images() {
    return get_section_images(null, false);
}

function get_section_image_by_id($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("SELECT * FROM `banners_and_images` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$id]);
    $img = $stmt->fetch();
    if ($img) {
        $img['image_path'] = resolve_media_url($img['image_path'], $img['display_order'] ?? $img['id'], $img['section_name']);
    }
    return $img;
}

function save_section_image($imageData, $id = null) {
    $pdo = Database::getPDO();
    if ($id) {
        $sql = "UPDATE `banners_and_images` SET `section_name` = ?, `title` = ?, `image_path` = ?, `badge_text` = ?, `display_order` = ?, `is_active` = ? WHERE `id` = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $imageData['section_name'],
            $imageData['title'],
            $imageData['image_path'],
            $imageData['badge_text'] ?? null,
            $imageData['display_order'] ?? 1,
            isset($imageData['is_active']) ? (int)$imageData['is_active'] : 1,
            $id
        ]);
    } else {
        $sql = "INSERT INTO `banners_and_images` (`section_name`, `title`, `image_path`, `badge_text`, `display_order`, `is_active`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            $imageData['section_name'],
            $imageData['title'],
            $imageData['image_path'],
            $imageData['badge_text'] ?? null,
            $imageData['display_order'] ?? 1,
            isset($imageData['is_active']) ? (int)$imageData['is_active'] : 1
        ]);
    }
}

function delete_section_image($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("DELETE FROM `banners_and_images` WHERE `id` = ?");
    return $stmt->execute([$id]);
}

function toggle_section_image_status($id) {
    $img = get_section_image_by_id($id);
    if (!$img) return false;
    $newStatus = !empty($img['is_active']) ? 0 : 1;
    $img['is_active'] = $newStatus;
    return save_section_image($img, $id);
}

// -------------------------------------------------------------
// Pure MySQL Rooms CRUD & Filter Functions
// -------------------------------------------------------------

function get_all_rooms($category = null, $onlyAvailable = false) {
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
        $r['featured_image'] = resolve_media_url($r['featured_image'], $r['id'], 'room');
        if (is_array($r['gallery'])) {
            $r['gallery'] = array_map(function($g) use ($r) {
                return resolve_media_url($g, $r['id'], 'room');
            }, $r['gallery']);
        }
    }
    return $rooms;
}

function get_room_by_id($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("SELECT * FROM `rooms` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$id]);
    $room = $stmt->fetch();
    if ($room) {
        $room['amenities'] = is_string($room['amenities']) ? json_decode($room['amenities'], true) : $room['amenities'];
        $room['gallery'] = is_string($room['gallery']) ? json_decode($room['gallery'], true) : $room['gallery'];
        $room['inclusions'] = is_string($room['inclusions']) ? json_decode($room['inclusions'], true) : $room['inclusions'];
        $room['featured_image'] = resolve_media_url($room['featured_image'], $room['id'], 'room');
        if (is_array($room['gallery'])) {
            $room['gallery'] = array_map(function($g) use ($room) {
                return resolve_media_url($g, $room['id'], 'room');
            }, $room['gallery']);
        }
    }
    return $room;
}

function save_room($roomData, $id = null) {
    $pdo = Database::getPDO();
    $amenitiesJson = json_encode(is_array($roomData['amenities']) ? array_values(array_filter(array_map('trim', $roomData['amenities']))) : array_values(array_filter(array_map('trim', explode(',', $roomData['amenities'])))));
    $galleryJson = json_encode(is_array($roomData['gallery']) ? array_values(array_filter($roomData['gallery'])) : [$roomData['featured_image']]);
    $inclusionsJson = json_encode(is_array($roomData['inclusions']) ? array_values(array_filter(array_map('trim', $roomData['inclusions']))) : array_values(array_filter(array_map('trim', explode(',', $roomData['inclusions'] ?? '')))));

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
}

function delete_room($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("DELETE FROM `rooms` WHERE `id` = ?");
    return $stmt->execute([$id]);
}

function toggle_room_status($id) {
    $room = get_room_by_id($id);
    if (!$room) return false;
    $room['is_available'] = !empty($room['is_available']) ? 0 : 1;
    return save_room($room, $id);
}

// -------------------------------------------------------------
// Pure MySQL Bookings Functions
// -------------------------------------------------------------

function create_booking($bookingData) {
    $bookingNumber = 'RR-' . date('Y') . '-' . rand(1000, 9999);
    $bookingData['booking_number'] = $bookingNumber;
    $bookingData['created_at'] = date('Y-m-d H:i:s');
    $bookingData['booking_status'] = $bookingData['booking_status'] ?? 'Confirmed';
    $bookingData['payment_status'] = $bookingData['payment_status'] ?? 'Pending';

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
}

function get_booking_by_number($bookingNumber) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("SELECT * FROM `bookings` WHERE `booking_number` = ? LIMIT 1");
    $stmt->execute([$bookingNumber]);
    return $stmt->fetch();
}

function get_booking_by_id($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("SELECT * FROM `bookings` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function get_all_bookings($statusFilter = null) {
    $pdo = Database::getPDO();
    $sql = "SELECT * FROM `bookings` WHERE 1=1";
    $params = [];
    if ($statusFilter && $statusFilter !== 'all') {
        $sql .= " AND `booking_status` = ?";
        $params[] = $statusFilter;
    }
    $sql .= " ORDER BY `id` DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function update_booking_status($id, $status, $paymentStatus = null) {
    $pdo = Database::getPDO();
    if ($paymentStatus) {
        $stmt = $pdo->prepare("UPDATE `bookings` SET `booking_status` = ?, `payment_status` = ? WHERE `id` = ?");
        return $stmt->execute([$status, $paymentStatus, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE `bookings` SET `booking_status` = ? WHERE `id` = ?");
        return $stmt->execute([$status, $id]);
    }
}

function update_booking_full($id, $data) {
    $pdo = Database::getPDO();
    $sql = "UPDATE `bookings` SET 
            `room_id` = ?, 
            `room_name` = ?, 
            `guest_name` = ?, 
            `guest_email` = ?, 
            `guest_phone` = ?, 
            `id_proof_type` = ?, 
            `id_proof_number` = ?, 
            `check_in` = ?, 
            `check_out` = ?, 
            `total_nights` = ?, 
            `adults` = ?, 
            `children` = ?, 
            `price_per_night` = ?, 
            `total_amount` = ?, 
            `payment_method` = ?, 
            `payment_status` = ?, 
            `booking_status` = ?, 
            `special_requests` = ? 
            WHERE `id` = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        $data['room_id'],
        $data['room_name'],
        $data['guest_name'],
        $data['guest_email'],
        $data['guest_phone'],
        $data['id_proof_type'],
        $data['id_proof_number'],
        $data['check_in'],
        $data['check_out'],
        $data['total_nights'],
        $data['adults'],
        $data['children'],
        $data['price_per_night'],
        $data['total_amount'],
        $data['payment_method'],
        $data['payment_status'],
        $data['booking_status'],
        $data['special_requests'],
        $id
    ]);
}

function toggle_payment_status($id, $newPaymentStatus) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("UPDATE `bookings` SET `payment_status` = ? WHERE `id` = ?");
    return $stmt->execute([$newPaymentStatus, $id]);
}

function delete_booking($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("DELETE FROM `bookings` WHERE `id` = ?");
    return $stmt->execute([$id]);
}

function get_booking_stats() {
    $bookings = get_all_bookings();
    $totalBookings = count($bookings);
    $confirmed = 0;
    $pending = 0;
    $totalRevenue = 0.0;
    
    foreach ($bookings as $b) {
        if (($b['booking_status'] ?? '') === 'Confirmed' || ($b['booking_status'] ?? '') === 'Checked-In') {
            $confirmed++;
            $totalRevenue += (float)($b['total_amount'] ?? 0);
        } elseif (($b['booking_status'] ?? '') === 'Pending') {
            $pending++;
        }
    }
    
    return [
        'total' => $totalBookings,
        'confirmed' => $confirmed,
        'pending' => $pending,
        'revenue' => $totalRevenue
    ];
}

// -------------------------------------------------------------
// Pure MySQL Messages / Inquiries Functions
// -------------------------------------------------------------

function save_contact_message($messageData) {
    $messageData['created_at'] = date('Y-m-d H:i:s');
    $messageData['is_read'] = 0;

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
}

function get_all_messages() {
    $pdo = Database::getPDO();
    $stmt = $pdo->query("SELECT * FROM `messages` ORDER BY `id` DESC");
    return $stmt->fetchAll();
}

function mark_message_read($id, $isRead = 1) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("UPDATE `messages` SET `is_read` = ? WHERE `id` = ?");
    return $stmt->execute([(int)$isRead, $id]);
}

function delete_message($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("DELETE FROM `messages` WHERE `id` = ?");
    return $stmt->execute([$id]);
}

// -------------------------------------------------------------
// Pure MySQL Guest Reviews & Ratings Functions
// -------------------------------------------------------------

function get_approved_reviews($limit = null) {
    $pdo = Database::getPDO();
    $sql = "SELECT * FROM `reviews` WHERE `is_approved` = 1 ORDER BY `is_featured` DESC, `id` DESC";
    if ($limit) $sql .= " LIMIT " . (int)$limit;
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll() ?: [];
}

function get_all_reviews() {
    $pdo = Database::getPDO();
    $stmt = $pdo->query("SELECT * FROM `reviews` ORDER BY `id` DESC");
    return $stmt->fetchAll() ?: [];
}

function save_review($reviewData) {
    $reviewData['rating'] = max(1, min(5, (int)($reviewData['rating'] ?? 5)));
    $reviewData['is_approved'] = isset($reviewData['is_approved']) ? (int)$reviewData['is_approved'] : 0;
    $reviewData['is_featured'] = isset($reviewData['is_featured']) ? (int)$reviewData['is_featured'] : 0;
    $reviewData['created_at'] = date('Y-m-d H:i:s');

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
}

function update_review_status($id, $isApproved, $isFeatured = null) {
    $pdo = Database::getPDO();
    if ($isFeatured !== null) {
        $stmt = $pdo->prepare("UPDATE `reviews` SET `is_approved` = ?, `is_featured` = ? WHERE `id` = ?");
        return $stmt->execute([(int)$isApproved, (int)$isFeatured, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE `reviews` SET `is_approved` = ? WHERE `id` = ?");
        return $stmt->execute([(int)$isApproved, $id]);
    }
}

function delete_review($id) {
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("DELETE FROM `reviews` WHERE `id` = ?");
    return $stmt->execute([$id]);
}

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

// -------------------------------------------------------------
// Pure MySQL Admin Authentication & Profile Management
// -------------------------------------------------------------

function check_admin_auth() {
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: login.php');
        exit;
    }
}

function admin_login($username, $password) {
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
    return false;
}

function admin_logout() {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_name']);
    session_destroy();
}

function get_admin_user() {
    if (empty($_SESSION['admin_id'])) return null;
    $pdo = Database::getPDO();
    $stmt = $pdo->prepare("SELECT `id`, `username`, `full_name`, `email` FROM `admins` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

function update_admin_profile($id, $fullName, $email, $newPassword = null) {
    $pdo = Database::getPDO();
    if (!empty($newPassword)) {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE `admins` SET `full_name` = ?, `email` = ?, `password_hash` = ? WHERE `id` = ?");
        $res = $stmt->execute([$fullName, $email, $hash, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE `admins` SET `full_name` = ?, `email` = ? WHERE `id` = ?");
        $res = $stmt->execute([$fullName, $email, $id]);
    }
    if ($res) {
        $_SESSION['admin_name'] = $fullName;
    }
    return $res;
}
