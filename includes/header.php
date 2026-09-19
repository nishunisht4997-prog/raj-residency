<?php
// includes/header.php - Header for Raj Residency
require_once __DIR__ . '/functions.php';
$settings = get_settings();
$currentPage = basename($_SERVER['PHP_SELF'], ".php");
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . htmlspecialchars($settings['hotel_name']) : htmlspecialchars($settings['hotel_name']) . ' - Royal Heritage & Luxury Hotel in Koraput'; ?></title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Welcome to Raj Residency, premier luxury hotel on Post Office Road, Koraput, Odisha. Best AC deluxe suites and clean budget Non-AC rooms with world-class hospitality.">
    <meta name="keywords" content="Raj Residency, Koraput Hotel, Hotel in Koraput, Post Office Road Koraput, AC Rooms Koraput, Budget Rooms Koraput, Odisha Tourism Stay">

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
    
    <!-- Custom Style Sheet -->
    <link rel="stylesheet" href="assets/css/style.css?v=3.0">
</head>
<body class="bg-[#fbf9f5] text-[#2e2624] antialiased flex flex-col min-h-screen pb-16 lg:pb-0">

    <!-- Imperial Royal Maroon & Bronze Gold Header -->
    <header class="bg-[#2b0e14] text-white border-b border-[#d4a359]/30 sticky top-0 z-50 shadow-xl backdrop-blur-md bg-opacity-95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 sm:h-24">
                
                <!-- Brand Logo (RAJ RESIDENCY with Royal "RR" Crest Emblem) -->
                <a href="index.php" class="flex items-center space-x-3 group">
                    <div class="w-12 h-12 rounded-full border-2 border-[#d4a359] bg-[#1c080d] flex flex-col items-center justify-center text-[#f3cf8a] shadow-lg shadow-[#d4a359]/20 group-hover:scale-105 transition">
                        <i class="fa-solid fa-crown text-[10px] text-[#d4a359] mb-0.5"></i>
                        <span class="font-royal text-xs font-black tracking-tighter leading-none text-[#f3cf8a]">RR</span>
                    </div>
                    <div>
                        <span class="font-royal tracking-widest text-xl sm:text-2xl font-black uppercase text-white block leading-none"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                        <span class="text-[9px] tracking-[0.28em] text-[#d4a359] uppercase font-bold"><?php echo htmlspecialchars($settings['hotel_tagline']); ?></span>
                    </div>
                </a>

                <!-- Desktop Navigation Menu (HOME, ABOUT US, SERVICES, ROOMS, CONTACT) -->
                <nav class="hidden lg:flex items-center space-x-8 text-xs font-bold tracking-wider uppercase">
                    <a href="index.php" class="<?php echo ($currentPage == 'index' || $currentPage == '') ? 'text-[#f3cf8a] border-b-2 border-[#d4a359] pb-1' : 'text-slate-200 hover:text-[#f3cf8a]'; ?> transition">HOME</a>
                    <a href="about.php" class="<?php echo ($currentPage == 'about') ? 'text-[#f3cf8a] border-b-2 border-[#d4a359] pb-1' : 'text-slate-200 hover:text-[#f3cf8a]'; ?> transition">ABOUT US</a>
                    <a href="services.php" class="<?php echo ($currentPage == 'services') ? 'text-[#f3cf8a] border-b-2 border-[#d4a359] pb-1' : 'text-slate-200 hover:text-[#f3cf8a]'; ?> transition">SERVICES</a>
                    <a href="rooms.php" class="<?php echo ($currentPage == 'rooms' || $currentPage == 'room-details') ? 'text-[#f3cf8a] border-b-2 border-[#d4a359] pb-1' : 'text-slate-200 hover:text-[#f3cf8a]'; ?> transition flex items-center">
                        <span>ROOMS</span>
                        <i class="fa-solid fa-angle-down text-[10px] ml-1 text-slate-400"></i>
                    </a>
                    <a href="contact.php" class="<?php echo ($currentPage == 'contact') ? 'text-[#f3cf8a] border-b-2 border-[#d4a359] pb-1' : 'text-slate-200 hover:text-[#f3cf8a]'; ?> transition">CONTACT</a>
                </nav>

                <!-- Search Icon & Book Now Button -->
                <div class="hidden sm:flex items-center space-x-5">
                    <a href="rooms.php" title="Search Rooms" class="text-slate-200 hover:text-[#f3cf8a] transition text-sm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                    <a href="rooms.php" class="btn-gold px-7 py-2.5 rounded-full text-xs font-bold tracking-wider uppercase shadow-md flex items-center space-x-2">
                        <i class="fa-solid fa-calendar-check text-[11px]"></i>
                        <span>BOOK NOW</span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-[#f3cf8a] hover:text-white p-2" aria-label="Toggle Navigation">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobile-menu" class="hidden lg:hidden bg-[#1c080d] border-t border-[#d4a359]/30 px-6 py-4 space-y-3 text-xs font-bold tracking-wider uppercase">
            <a href="index.php" class="block py-2 <?php echo ($currentPage == 'index' || $currentPage == '') ? 'text-[#f3cf8a]' : 'text-white'; ?>">HOME</a>
            <a href="about.php" class="block py-2 <?php echo ($currentPage == 'about') ? 'text-[#f3cf8a]' : 'text-slate-300 hover:text-[#f3cf8a]'; ?>">ABOUT US</a>
            <a href="services.php" class="block py-2 <?php echo ($currentPage == 'services') ? 'text-[#f3cf8a]' : 'text-slate-300 hover:text-[#f3cf8a]'; ?>">SERVICES</a>
            <a href="rooms.php" class="block py-2 <?php echo ($currentPage == 'rooms') ? 'text-[#f3cf8a]' : 'text-slate-300 hover:text-[#f3cf8a]'; ?>">ROOMS (AC & Non-AC)</a>
            <a href="contact.php" class="block py-2 <?php echo ($currentPage == 'contact') ? 'text-[#f3cf8a]' : 'text-slate-300 hover:text-[#f3cf8a]'; ?>">CONTACT</a>
            <div class="pt-2">
                <a href="rooms.php" class="btn-gold block text-center py-3 rounded-full text-xs font-bold uppercase">BOOK NOW</a>
            </div>
        </div>
    </header>

    <main class="flex-grow">
