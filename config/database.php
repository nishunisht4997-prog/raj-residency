<?php
// config/database.php - Raj Residency Database & Storage Handler

class Database {
    private static $pdo = null;
    private static $storageMode = null; // 'mysql' or 'json'
    private static $jsonFile = __DIR__ . '/../data/store.json';

    // MySQL default credentials (can be overridden via environment variables or settings)
    private static $host = 'localhost';
    private static $db_name = 'raj_residency';
    private static $username = 'root';
    private static $password = '';

    public static function init() {
        if (self::$storageMode !== null) {
            return;
        }

        // Ensure data directory exists for json storage / uploads
        $dataDir = __DIR__ . '/../data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0777, true);
        }

        // Try connecting to MySQL first
        try {
            $dsn = "mysql:host=" . self::$host . ";charset=utf8mb4";
            $conn = new PDO($dsn, self::$username, self::$password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 2
            ]);

            // Check or create database
            $conn->exec("CREATE DATABASE IF NOT EXISTS `" . self::$db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $conn->exec("USE `" . self::$db_name . "`");
            self::$pdo = $conn;
            self::$storageMode = 'mysql';

            // Create tables if they don't exist
            self::initMysqlTables();
        } catch (Exception $e) {
            // Fallback to robust file-based JSON storage
            self::$storageMode = 'json';
            self::initJsonStore();
        }
    }

    public static function getStorageMode() {
        self::init();
        return self::$storageMode;
    }

    public static function getPDO() {
        self::init();
        return self::$pdo;
    }

    private static function initMysqlTables() {
        if (!self::$pdo) return;

        // Settings table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
            `key_name` VARCHAR(100) PRIMARY KEY,
            `value_text` TEXT NOT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Rooms table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `rooms` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `slug` VARCHAR(255) NOT NULL,
            `category` VARCHAR(50) NOT NULL,
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Bookings table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `bookings` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Messages / Inquiries table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `phone` VARCHAR(50) NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `is_read` TINYINT(1) DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Reviews table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `reviews` (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Admins table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(100) UNIQUE NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Check if seeded
        $stmt = self::$pdo->query("SELECT COUNT(*) as count FROM `rooms`");
        $rowCount = $stmt->fetch()['count'];
        if ($rowCount == 0) {
            require_once __DIR__ . '/seed_data.php';
            SeedData::seedMysql(self::$pdo);
        }
    }

    private static function initJsonStore() {
        if (!file_exists(self::$jsonFile)) {
            require_once __DIR__ . '/seed_data.php';
            $defaultData = SeedData::getDefaultData();
            file_put_contents(self::$jsonFile, json_encode($defaultData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }

    public static function getJsonData() {
        self::init();
        if (file_exists(self::$jsonFile)) {
            $content = file_get_contents(self::$jsonFile);
            $data = json_decode($content, true);
            if (is_array($data)) return $data;
        }
        require_once __DIR__ . '/seed_data.php';
        return SeedData::getDefaultData();
    }

    public static function saveJsonData($data) {
        self::init();
        file_put_contents(self::$jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
