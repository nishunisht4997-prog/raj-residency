<?php
// config/database.php - Pure MySQL Database Handler for Raj Residency
require_once __DIR__ . '/seed_data.php';

date_default_timezone_set('Asia/Kolkata');

class Database {
    private static $pdo = null;
    private static $host = 'localhost';
    private static $db_name = 'raj_residency';
    private static $username = 'root';
    private static $password = '';
    private static $port = 3306;
    private static $connectionError = null;

    public static function init() {
        if (self::$pdo !== null) {
            return;
        }

        date_default_timezone_set('Asia/Kolkata');

        // Load .env file if it exists
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || $line[0] === '#') continue;
                if (strpos($line, '=') !== false) {
                    list($k, $v) = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v, " \t\n\r\0\x0B\"'");
                    if (getenv($k) === false) {
                        putenv("$k=$v");
                        $_ENV[$k] = $v;
                    }
                }
            }
        }

        // Support environment variables (Render / Cloud DB / Railway / cPanel / Local)
        self::$host = getenv('DB_HOST') ?: (getenv('MYSQLHOST') ?: self::$host);
        self::$db_name = getenv('DB_NAME') ?: (getenv('MYSQLDATABASE') ?: self::$db_name);
        self::$username = getenv('DB_USER') ?: (getenv('MYSQLUSER') ?: self::$username);
        self::$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : self::$password);
        self::$port = getenv('DB_PORT') ?: (getenv('MYSQLPORT') ?: self::$port);

        // Parse DATABASE_URL if provided
        $dbUrl = getenv('DATABASE_URL') ?: getenv('MYSQL_URL');
        if (!empty($dbUrl)) {
            $parsed = parse_url($dbUrl);
            if (!empty($parsed['host'])) self::$host = $parsed['host'];
            if (!empty($parsed['port'])) self::$port = $parsed['port'];
            if (!empty($parsed['user'])) self::$username = urldecode($parsed['user']);
            if (!empty($parsed['pass'])) self::$password = urldecode($parsed['pass']);
            if (!empty($parsed['path'])) self::$db_name = ltrim($parsed['path'], '/');
        }

        // Ensure upload directories exist
        $uploadDirectories = [
            __DIR__ . '/../uploads',
            __DIR__ . '/../uploads/hero',
            __DIR__ . '/../uploads/archway',
            __DIR__ . '/../uploads/rooms',
            __DIR__ . '/../uploads/about',
            __DIR__ . '/../uploads/settings',
            __DIR__ . '/../uploads/receipts'
        ];
        foreach ($uploadDirectories as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }
        }

        // Configure PDO options with SSL support for remote cloud databases (Aiven, TiDB, etc.)
        $pdoOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ];
        if (self::$host !== 'localhost' && self::$host !== '127.0.0.1') {
            if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
                $pdoOptions[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            }
        }

        // Connect to MySQL (compatible with Cloud DBs, cPanel, and Local XAMPP)
        try {
            // Try connecting directly with dbname first (standard for production/cPanel/cloud hosts)
            try {
                $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$db_name . ";charset=utf8mb4";
                $conn = new PDO($dsn, self::$username, self::$password, $pdoOptions);
            } catch (PDOException $dbEx) {
                // If direct db connection failed, connect without dbname and create database (typical for fresh local XAMPP)
                $dsnNoDb = "mysql:host=" . self::$host . ";port=" . self::$port . ";charset=utf8mb4";
                $conn = new PDO($dsnNoDb, self::$username, self::$password, $pdoOptions);
                $conn->exec("CREATE DATABASE IF NOT EXISTS `" . self::$db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $conn->exec("USE `" . self::$db_name . "`");
            }

            // Set Indian Standard Time (+05:30) for MySQL session
            @$conn->exec("SET time_zone = '+05:30'");
            self::$pdo = $conn;

            // Initialize all MySQL tables & seed default records
            self::initMysqlTables();
        } catch (Exception $e) {
            self::$connectionError = $e->getMessage();
            self::renderMysqlErrorScreen($e->getMessage());
        }
    }

    public static function getPDO() {
        self::init();
        return self::$pdo;
    }

    public static function getConnectionError() {
        return self::$connectionError;
    }

    private static function initMysqlTables() {
        if (!self::$pdo) return;

        // 1. Settings Table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
            `key_name` VARCHAR(100) PRIMARY KEY,
            `value_text` TEXT NOT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // 2. Banners & Dynamic Section Images Table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `banners_and_images` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `section_name` VARCHAR(100) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `image_path` TEXT NOT NULL,
            `badge_text` VARCHAR(255) NULL,
            `display_order` INT DEFAULT 1,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // 3. Rooms Table
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

        // 4. Bookings Table
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

        // 5. Messages / Inquiries Table
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

        // 6. Reviews Table
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

        // 7. Admins Table
        self::$pdo->exec("CREATE TABLE IF NOT EXISTS `admins` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(100) UNIQUE NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Check if database needs initial seeding
        $stmt = self::$pdo->query("SELECT COUNT(*) as count FROM `rooms`");
        $rowCount = $stmt->fetch()['count'];
        if ($rowCount == 0) {
            SeedData::seedMysql(self::$pdo);
        }
    }

    private static function renderMysqlErrorScreen($errorMsg) {
        http_response_code(500);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>MySQL Database Required | Raj Residency</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        </head>
        <body class="bg-[#1c080d] text-white min-h-screen flex items-center justify-center p-4">
            <div class="max-w-xl w-full bg-[#2b0e14] border border-[#d4a359]/40 rounded-3xl p-8 shadow-2xl space-y-6 text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-rose-950 border-2 border-rose-500 flex items-center justify-center text-rose-400 text-2xl shadow-xl">
                    <i class="fa-solid fa-database"></i>
                </div>
                <div>
                    <h1 class="font-serif text-2xl font-bold text-white">MySQL Database Connection Required</h1>
                    <p class="text-xs text-slate-300 mt-1">Raj Residency runs strictly on pure SQL database.</p>
                </div>

                <div class="p-4 rounded-2xl bg-black/40 border border-rose-500/30 text-left space-y-2 text-xs font-mono text-rose-300">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Connection Error:</span>
                    <p class="break-words"><?php echo htmlspecialchars($errorMsg); ?></p>
                </div>

                <div class="p-4 rounded-2xl bg-[#1c080d]/80 border border-[#d4a359]/20 text-left space-y-2 text-xs">
                    <span class="text-[11px] font-bold text-[#f3cf8a] uppercase block">How to Start MySQL:</span>
                    <ol class="list-decimal list-inside space-y-1.5 text-slate-300">
                        <li>Open <strong>XAMPP Control Panel</strong> and click <strong class="text-emerald-400">"Start"</strong> next to <strong>MySQL</strong>.</li>
                        <li>Or ensure MySQL server service is running on <strong>localhost:3306</strong> with user <strong>root</strong> and password <strong>blank</strong>.</li>
                        <li>Once started, refresh this page or visit <a href="setup_db.php" class="text-[#f3cf8a] underline font-bold">setup_db.php</a> to auto-create all tables!</li>
                    </ol>
                </div>

                <a href="index.php" class="w-full inline-block bg-gradient-to-r from-[#f3cf8a] via-[#d4a359] to-[#b88738] text-[#2b0e14] py-3 rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg hover:brightness-105 transition">
                    <i class="fa-solid fa-rotate-right mr-1.5"></i> Retry Connection
                </a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}
