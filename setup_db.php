<?php
// setup_db.php - Raj Residency 1-Click MySQL Database Setup & Diagnostics
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/seed_data.php';

$pdo = Database::getPDO();
$message = '';
$error = '';

if (isset($_GET['action']) && $_GET['action'] === 'seed') {
    try {
        if ($pdo) {
            SeedData::seedMysql($pdo);
            $message = 'MySQL Database tables & sample records successfully re-seeded in MySQL database!';
        }
    } catch (Exception $e) {
        $error = 'Error seeding MySQL database: ' . $e->getMessage();
    }
}

$settingsCount = 0;
$roomsCount = 0;
$bannersCount = 0;
$bookingsCount = 0;
$reviewsCount = 0;
$messagesCount = 0;

if ($pdo) {
    try {
        $settingsCount = $pdo->query("SELECT COUNT(*) FROM `settings`")->fetchColumn();
        $roomsCount = $pdo->query("SELECT COUNT(*) FROM `rooms`")->fetchColumn();
        $bannersCount = $pdo->query("SELECT COUNT(*) FROM `banners_and_images`")->fetchColumn();
        $bookingsCount = $pdo->query("SELECT COUNT(*) FROM `bookings`")->fetchColumn();
        $reviewsCount = $pdo->query("SELECT COUNT(*) FROM `reviews`")->fetchColumn();
        $messagesCount = $pdo->query("SELECT COUNT(*) FROM `messages`")->fetchColumn();
    } catch (Exception $e) {
        $error = 'Query error: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySQL Database Setup & Diagnostics | Raj Residency</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-[#1c080d] text-white min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full bg-[#2b0e14] border border-[#d4a359]/40 rounded-3xl p-8 shadow-2xl space-y-6">
        
        <div class="text-center space-y-2">
            <div class="w-14 h-14 mx-auto rounded-full border-2 border-[#d4a359] bg-[#1c080d] flex items-center justify-center text-[#f3cf8a] text-xl shadow-lg">
                <i class="fa-solid fa-database"></i>
            </div>
            <h1 class="font-serif text-2xl font-bold text-white">Raj Residency MySQL Database Setup</h1>
            <p class="text-xs text-slate-300 font-light">100% Pure MySQL Engine & Table Diagnostics</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="p-4 rounded-2xl bg-emerald-950 border border-emerald-500/50 text-emerald-200 text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-circle-check text-base text-emerald-400"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="p-4 rounded-2xl bg-rose-950 border border-rose-500/50 text-rose-200 text-xs font-semibold flex items-center space-x-2">
                <i class="fa-solid fa-circle-exclamation text-base text-rose-400"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <div class="p-4 rounded-2xl bg-[#1c080d]/80 border border-[#d4a359]/20 space-y-3 text-xs">
            <div class="flex items-center justify-between border-b border-[#d4a359]/20 pb-2">
                <span class="text-slate-400">Database Engine:</span>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-900 text-emerald-300 border border-emerald-500/40">
                    <i class="fa-solid fa-circle text-[7px] text-emerald-400 animate-pulse mr-1"></i> MYSQL ACTIVE
                </span>
            </div>
            <div class="flex items-center justify-between border-b border-[#d4a359]/20 pb-2">
                <span class="text-slate-400">Database Name:</span>
                <strong class="text-[#f3cf8a] font-mono">raj_residency</strong>
            </div>
            <div class="grid grid-cols-2 gap-2 text-[11px] border-b border-[#d4a359]/20 pb-2">
                <div class="text-slate-300">&bull; Rooms: <strong class="text-white"><?php echo $roomsCount; ?></strong></div>
                <div class="text-slate-300">&bull; Images / Banners: <strong class="text-white"><?php echo $bannersCount; ?></strong></div>
                <div class="text-slate-300">&bull; Bookings: <strong class="text-white"><?php echo $bookingsCount; ?></strong></div>
                <div class="text-slate-300">&bull; Reviews: <strong class="text-white"><?php echo $reviewsCount; ?></strong></div>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-400">Default Admin Credentials:</span>
                <span class="font-mono text-[#f3cf8a] font-bold">admin / admin123</span>
            </div>
        </div>

        <div class="space-y-3 pt-2">
            <a href="setup_db.php?action=seed" class="w-full bg-gradient-to-r from-[#f3cf8a] via-[#d4a359] to-[#b88738] text-[#2b0e14] py-3 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center justify-center space-x-2 shadow-lg hover:brightness-105 transition">
                <i class="fa-solid fa-arrows-rotate"></i>
                <span>Re-Seed MySQL Database Tables</span>
            </a>
            <div class="grid grid-cols-2 gap-3">
                <a href="admin/login.php" class="border border-[#d4a359] text-[#f3cf8a] py-2.5 rounded-xl text-xs font-bold uppercase text-center hover:bg-[#d4a359] hover:text-[#2b0e14] transition">
                    Open Admin Panel
                </a>
                <a href="index.php" class="bg-white/10 text-white py-2.5 rounded-xl text-xs font-bold uppercase text-center hover:bg-white/20 transition">
                    View Website
                </a>
            </div>
        </div>

    </div>

</body>
</html>
