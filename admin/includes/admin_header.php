<?php
// admin/includes/admin_header.php - Responsive Royal Maroon & Bronze Gold Admin Header
require_once __DIR__ . '/../../includes/functions.php';
check_admin_auth();

$settings = get_settings();
$adminUser = get_admin_user();
$currentPage = basename($_SERVER['PHP_SELF'], ".php");

// Get badges for sidebar counters
$allBookings = get_all_bookings();
$pendingBookingsCount = count(array_filter($allBookings, function($b) {
    return ($b['booking_status'] ?? '') === 'Pending';
}));

$allMessages = get_all_messages();
$unreadMessagesCount = count(array_filter($allMessages, function($m) {
    return empty($m['is_read']);
}));

$allReviews = get_all_reviews();
$pendingReviewsCount = count(array_filter($allReviews, function($r) {
    return empty($r['is_approved']);
}));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | Admin Panel - ' . htmlspecialchars($settings['hotel_name']) : 'Admin Dashboard | ' . htmlspecialchars($settings['hotel_name']); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: {
                            maroon: '#2b0e14',
                            maroonDark: '#1c080d',
                            maroonLight: '#3d141d',
                            wine: '#4a1923',
                            gold: '#d4a359',
                            goldLight: '#f3cf8a',
                            goldDark: '#b88738',
                            ivory: '#fcfaf7',
                            linen: '#f5efe6',
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', '"Cormorant Garamond"', 'Georgia', 'serif'],
                        royal: ['"Cinzel"', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-gold {
            background: linear-gradient(135deg, #f3cf8a 0%, #d4a359 50%, #b88738 100%);
            color: #2b0e14;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 20px -5px rgba(212, 163, 89, 0.4);
            filter: brightness(1.05);
        }
        .btn-ghost-gold {
            border: 1.5px solid #d4a359;
            color: #f3cf8a;
            transition: all 0.3s ease;
        }
        .btn-ghost-gold:hover {
            background-color: #d4a359;
            color: #2b0e14;
        }
        .royal-card {
            background-color: #ffffff;
            border: 1px solid #ebd9c8;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px -2px rgba(43, 14, 20, 0.05);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d4a359;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #2b0e14;
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-[#fbf9f5] text-slate-800 antialiased min-h-screen flex flex-col overflow-x-hidden">

    <div class="flex-grow flex flex-col lg:flex-row min-h-screen">
        
        <!-- SIDEBAR NAVIGATION (Desktop Sticky & Mobile Drawer) -->
        <aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] h-full bg-[#2b0e14] text-white flex flex-col border-r border-[#d4a359]/30 transform -translate-x-full lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen lg:shrink-0 transition-transform duration-300 ease-in-out shadow-2xl lg:shadow-none">
            
            <!-- Brand Logo & Header -->
            <div class="p-5 sm:p-6 border-b border-[#d4a359]/30 flex items-center justify-between shrink-0">
                <a href="index.php" class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-full border-2 border-[#d4a359] bg-[#1c080d] flex flex-col items-center justify-center text-[#f3cf8a] shadow-md shrink-0">
                        <i class="fa-solid fa-crown text-[9px] text-[#d4a359]"></i>
                        <span class="font-royal text-[10px] font-black leading-none">RR</span>
                    </div>
                    <div class="min-w-0">
                        <span class="font-royal text-sm sm:text-base font-black tracking-wider text-white block uppercase leading-tight truncate"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                        <span class="text-[9px] tracking-widest text-[#d4a359] uppercase font-bold block">ADMIN MANAGEMENT</span>
                    </div>
                </a>
                
                <!-- Close Button on Mobile -->
                <button id="close-sidebar-btn" aria-label="Close Sidebar" class="lg:hidden text-slate-400 hover:text-white p-2 rounded-lg hover:bg-white/10 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Database / Storage Badge -->
            <div class="px-5 sm:px-6 py-2 bg-[#1c080d]/60 border-b border-[#d4a359]/20 flex items-center justify-between text-[11px] shrink-0">
                <span class="text-slate-400">Database Engine:</span>
                <span class="inline-flex items-center space-x-1.5 px-2 py-0.5 rounded-full font-bold uppercase text-[9px] bg-emerald-950 text-emerald-300 border border-emerald-500/40">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>MYSQL PURE SQL</span>
                </span>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1.5 flex-grow overflow-y-auto custom-scrollbar">
                
                <a href="index.php" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($currentPage === 'index' || $currentPage === '') ? 'bg-gradient-to-r from-[#d4a359] to-[#b88738] text-[#2b0e14] shadow-lg shadow-[#d4a359]/20' : 'text-slate-300 hover:bg-[#3d141d] hover:text-white'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-gauge-high text-sm"></i>
                        <span>Dashboard Overview</span>
                    </div>
                </a>

                <!-- Media & Image Manager (Direct Mobile/PC Uploads) -->
                <a href="media.php" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($currentPage === 'media') ? 'bg-gradient-to-r from-[#d4a359] to-[#b88738] text-[#2b0e14] shadow-lg shadow-[#d4a359]/20' : 'text-slate-300 hover:bg-[#3d141d] hover:text-white'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-images text-sm text-[#f3cf8a]"></i>
                        <span>Hero & Image Manager</span>
                    </div>
                    <span class="text-[9px] bg-[#d4a359]/20 text-[#f3cf8a] px-2 py-0.5 rounded-full font-bold border border-[#d4a359]/40">Mobile/PC</span>
                </a>

                <!-- Rooms & Suites -->
                <a href="rooms.php" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($currentPage === 'rooms') ? 'bg-gradient-to-r from-[#d4a359] to-[#b88738] text-[#2b0e14] shadow-lg shadow-[#d4a359]/20' : 'text-slate-300 hover:bg-[#3d141d] hover:text-white'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-door-open text-sm"></i>
                        <span>Rooms & Suites</span>
                    </div>
                </a>

                <!-- Bookings & Reservations -->
                <a href="bookings.php" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($currentPage === 'bookings') ? 'bg-gradient-to-r from-[#d4a359] to-[#b88738] text-[#2b0e14] shadow-lg shadow-[#d4a359]/20' : 'text-slate-300 hover:bg-[#3d141d] hover:text-white'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-calendar-check text-sm"></i>
                        <span>Bookings & Guests</span>
                    </div>
                    <?php if ($pendingBookingsCount > 0): ?>
                        <span class="text-[10px] bg-rose-500 text-white font-black px-2 py-0.5 rounded-full"><?php echo $pendingBookingsCount; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Guest Reviews Moderation -->
                <a href="reviews.php" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($currentPage === 'reviews') ? 'bg-gradient-to-r from-[#d4a359] to-[#b88738] text-[#2b0e14] shadow-lg shadow-[#d4a359]/20' : 'text-slate-300 hover:bg-[#3d141d] hover:text-white'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-star text-sm"></i>
                        <span>Guest Reviews</span>
                    </div>
                    <?php if ($pendingReviewsCount > 0): ?>
                        <span class="text-[10px] bg-amber-500 text-slate-900 font-black px-2 py-0.5 rounded-full"><?php echo $pendingReviewsCount; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Inquiries / Messages -->
                <a href="messages.php" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($currentPage === 'messages') ? 'bg-gradient-to-r from-[#d4a359] to-[#b88738] text-[#2b0e14] shadow-lg shadow-[#d4a359]/20' : 'text-slate-300 hover:bg-[#3d141d] hover:text-white'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-envelope text-sm"></i>
                        <span>Contact Messages</span>
                    </div>
                    <?php if ($unreadMessagesCount > 0): ?>
                        <span class="text-[10px] bg-blue-500 text-white font-black px-2 py-0.5 rounded-full"><?php echo $unreadMessagesCount; ?></span>
                    <?php endif; ?>
                </a>

                <!-- Hotel Settings -->
                <a href="settings.php" class="flex items-center justify-between px-4 py-3 rounded-xl text-xs font-bold transition <?php echo ($currentPage === 'settings') ? 'bg-gradient-to-r from-[#d4a359] to-[#b88738] text-[#2b0e14] shadow-lg shadow-[#d4a359]/20' : 'text-slate-300 hover:bg-[#3d141d] hover:text-white'; ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fa-solid fa-sliders text-sm"></i>
                        <span>Hotel & Security</span>
                    </div>
                </a>

            </nav>

            <!-- Sidebar Bottom Profile / Quick Links -->
            <div class="p-4 border-t border-[#d4a359]/30 space-y-2 bg-[#1c080d]/80 shrink-0">
                <a href="../index.php" target="_blank" class="flex items-center justify-center space-x-2 w-full py-2.5 rounded-xl border border-[#d4a359]/40 text-xs font-bold text-[#f3cf8a] hover:bg-[#d4a359] hover:text-[#2b0e14] transition">
                    <i class="fa-solid fa-globe text-xs"></i>
                    <span>Public Website</span>
                </a>
                <a href="logout.php" class="flex items-center justify-center space-x-2 w-full py-2 rounded-xl text-xs font-bold text-rose-300 hover:bg-rose-900/40 transition">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    <span>Log Out</span>
                </a>
            </div>

        </aside>

        <!-- Sidebar Overlay on Mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/70 z-40 hidden lg:hidden backdrop-blur-sm transition-opacity"></div>

        <!-- MAIN CONTENT CONTAINER -->
        <div class="flex-1 flex flex-col min-w-0 w-full">
            
            <!-- TOPBAR -->
            <header class="bg-white border-b border-[#ebd9c8] sticky top-0 z-30 shadow-sm">
                <div class="px-3 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-2">
                    
                    <!-- Left: Mobile Menu Toggle & Title -->
                    <div class="flex items-center space-x-2 sm:space-x-3 min-w-0">
                        <button id="mobile-sidebar-btn" aria-label="Open Sidebar Menu" class="lg:hidden text-[#2b0e14] hover:text-[#b88738] p-2 rounded-xl bg-[#f5efe6] active:scale-95 transition shrink-0">
                            <i class="fa-solid fa-bars text-base sm:text-lg"></i>
                        </button>
                        <h1 class="font-serif text-base sm:text-lg lg:text-xl font-bold text-[#2b0e14] truncate">
                            <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Admin Dashboard'; ?>
                        </h1>
                    </div>

                    <!-- Right: Quick Status & Profile Info -->
                    <div class="flex items-center space-x-2 sm:space-x-4 shrink-0">
                        
                        <!-- Contextual Quick Actions -->
                        <?php if ($currentPage === 'bookings'): ?>
                            <button type="button" onclick="document.getElementById('manual-booking-modal') ? document.getElementById('manual-booking-modal').classList.remove('hidden') : null" class="inline-flex items-center space-x-1.5 btn-gold px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-bold uppercase shadow-sm">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span class="hidden sm:inline">Walk-In Booking</span>
                                <span class="sm:hidden">Walk-In</span>
                            </button>
                        <?php elseif ($currentPage === 'rooms'): ?>
                            <a href="rooms.php?action=add" class="inline-flex items-center space-x-1.5 btn-gold px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-bold uppercase shadow-sm">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                                <span class="hidden sm:inline">Add Room</span>
                                <span class="sm:hidden">Add</span>
                            </a>
                        <?php else: ?>
                            <a href="media.php#upload-modal" class="inline-flex items-center space-x-1.5 bg-[#2b0e14] text-[#f3cf8a] hover:bg-[#3d141d] px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs font-bold border border-[#d4a359]/40 transition shadow-sm">
                                <i class="fa-solid fa-camera text-xs"></i>
                                <span class="hidden sm:inline">Upload Photo</span>
                                <span class="sm:hidden">Upload</span>
                            </a>
                        <?php endif; ?>

                        <!-- Admin Profile Pill -->
                        <div class="flex items-center space-x-2 sm:space-x-3 pl-2 sm:pl-3 border-l border-[#ebd9c8]">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359] flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                                <i class="fa-solid fa-user-shield text-xs"></i>
                            </div>
                            <div class="hidden md:block text-left">
                                <span class="text-xs font-bold text-[#2b0e14] block leading-none truncate max-w-[120px]"><?php echo htmlspecialchars($adminUser['full_name'] ?? 'Raj Admin'); ?></span>
                                <span class="text-[10px] text-slate-500 font-medium">Hotel Manager</span>
                            </div>
                        </div>

                    </div>

                </div>
            </header>

            <!-- FLASH ALERTS CONTAINER -->
            <div class="px-4 sm:px-6 lg:px-8 pt-4">
                <?php if ($flashSuccess = get_flash_message('success')): ?>
                    <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                            <span class="text-xs sm:text-sm font-semibold"><?php echo htmlspecialchars($flashSuccess); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900 p-1"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                <?php endif; ?>

                <?php if ($flashError = get_flash_message('error')): ?>
                    <div class="mb-4 p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-900 flex items-center justify-between shadow-sm animate-fade-in">
                        <div class="flex items-center space-x-3">
                            <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                            <span class="text-xs sm:text-sm font-semibold"><?php echo htmlspecialchars($flashError); ?></span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900 p-1"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- MAIN PAGE BODY WRAPPER (Closed in admin_footer.php) -->
            <main class="flex-grow p-4 sm:p-6 lg:p-8">
