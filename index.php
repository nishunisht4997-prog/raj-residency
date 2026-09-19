<?php
// index.php - Home Page for Raj Residency (Royal Maroon & Bronze Gold Edition)
$pageTitle = "Home";
require_once __DIR__ . '/includes/header.php';

$allRooms = get_all_rooms(null, true);
$featuredRooms = array_filter($allRooms, function($r) {
    return !empty($r['is_featured']);
});
if (empty($featuredRooms)) {
    $featuredRooms = $allRooms;
}
?>

<!-- 1. MAGAZINE-STYLE ASYMMETRIC ROYAL HERO SECTION -->
<section class="relative bg-[#2b0e14] text-white pt-10 pb-24 lg:pt-14 lg:pb-32 overflow-hidden border-b border-[#d4a359]/30">
    
    <!-- Hero Background Crossfade Slider -->
    <div class="hero-bg-slider absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="hero-slide active" style="background-image: url('https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1920&q=80');"></div>
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1920&q=80');"></div>
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1920&q=80');"></div>
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1920&q=80');"></div>
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=1920&q=80');"></div>
        
        <!-- Royal Wine & Dark Maroon Gradient Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-[#1c080d]/95 via-[#2b0e14]/90 to-[#1c080d]/80 backdrop-blur-[0.5px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Hero Content -->
            <div class="lg:col-span-6 space-y-6 text-left">
                
                <!-- Subtitle Pill with Crown & Monogram -->
                <div class="inline-flex items-center space-x-2 bg-[#3d141d]/80 border border-[#d4a359]/40 px-3.5 py-1.5 rounded-full text-[10px] font-bold tracking-widest text-[#f3cf8a] uppercase shadow-md">
                    <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                    <span>ROYAL HERITAGE & LUXURY STAY IN KORAPUT</span>
                </div>

                <!-- Main Heading -->
                <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl lg:text-[54px] font-bold text-white leading-[1.12]">
                    Where Royal <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f3cf8a] via-[#e5b869] to-[#d4a359]">Heritage</span> Meets Pure <span class="text-white">Comfort</span>
                </h1>

                <!-- Subtitle text -->
                <p class="text-slate-300 text-xs sm:text-sm max-w-lg leading-relaxed font-light opacity-95">
                    Welcome to <strong class="text-white font-bold"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong> on Post Office Road, Koraput, Odisha. Experience genuine Odia warmth, premium AC deluxe suites, and sparkling clean budget Non-AC rooms in the heart of the city.
                </p>

                <!-- Action Buttons (Call / Book / Explore) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4 pt-1">
                    <a href="rooms.php" class="btn-gold px-8 py-3.5 rounded-full text-xs font-bold tracking-wider uppercase shadow-xl flex items-center justify-center space-x-2 text-center">
                        <i class="fa-solid fa-calendar-check text-xs"></i>
                        <span>BOOK YOUR STAY</span>
                    </a>
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="btn-ghost-gold px-7 py-3.5 rounded-full text-xs font-bold tracking-wider uppercase flex items-center justify-center space-x-2 text-center">
                        <i class="fa-solid fa-phone text-xs"></i>
                        <span>Call: <?php echo htmlspecialchars($settings['hotel_phone']); ?></span>
                    </a>
                </div>

                <!-- Floating Price & Google Rating Pill -->
                <div class="pt-2 sm:pt-4">
                    <div class="hero-badge-pill">
                        <div>
                            <span class="text-[9px] text-slate-500 font-bold uppercase tracking-wider block">PRICE STARTS FROM</span>
                            <div class="flex items-baseline">
                                <span class="text-xl sm:text-2xl font-black text-[#2b0e14]">₹1,299</span>
                                <span class="text-[10px] sm:text-[11px] text-slate-500 font-medium ml-1">/ NIGHT</span>
                            </div>
                        </div>
                        <div class="h-8 sm:h-9 w-[1px] bg-slate-200"></div>
                        <div class="flex items-center space-x-2.5 sm:space-x-3">
                            <span class="text-xl sm:text-2xl font-black text-[#2b0e14]">4.8</span>
                            <div>
                                <div class="text-[#d4a359] text-xs flex space-x-0.5">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <span class="text-[9px] sm:text-[10px] text-slate-600 font-semibold block">Google Verified Reviews</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: Magazine-Style Asymmetrical Archway with Floating Previews -->
            <div class="lg:col-span-6 relative mt-6 lg:mt-0 flex items-center justify-center">
                
                <!-- Main Archway Frame (Jharokha Style) -->
                <div class="royal-arch-frame w-full max-w-[420px] h-[380px] sm:h-[460px] bg-slate-900 relative">
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1000&q=80" 
                         alt="<?php echo htmlspecialchars($settings['hotel_name']); ?> Luxury Suite Lounge" 
                         class="w-full h-full object-cover">
                    
                    <!-- Center Overlay Crest on Archway -->
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1c080d]/80 via-transparent to-transparent flex items-end p-6">
                        <div class="text-white">
                            <span class="text-[10px] font-bold text-[#f3cf8a] uppercase tracking-widest block">POST OFFICE ROAD, KORAPUT</span>
                            <span class="font-serif text-lg font-bold text-white">Authentic Royal Comfort</span>
                        </div>
                    </div>
                </div>

                <!-- Floating Mini Preview 1 (Top Left) -->
                <div class="floating-hero-preview-1 absolute -top-4 -left-3 sm:-left-6 bg-white/95 backdrop-blur-md rounded-2xl p-2.5 sm:p-3 border border-[#d4a359]/50 shadow-2xl flex items-center space-x-3 max-w-[200px] sm:max-w-[220px]">
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=200&q=80" alt="AC Suite" class="w-11 h-11 rounded-xl object-cover border border-[#d4a359]/40">
                    <div>
                        <strong class="text-xs text-[#2b0e14] block leading-tight">Executive AC Suites</strong>
                        <span class="text-[10px] text-[#b88738] font-bold">From ₹2,499/night</span>
                    </div>
                </div>

                <!-- Floating Mini Preview 2 (Bottom Right) -->
                <div class="floating-hero-preview-2 absolute -bottom-4 -right-3 sm:-right-6 bg-white/95 backdrop-blur-md rounded-2xl p-2.5 sm:p-3 border border-[#d4a359]/50 shadow-2xl flex items-center space-x-3 max-w-[200px] sm:max-w-[230px]">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=200&q=80" alt="Deomali Gateway" class="w-11 h-11 rounded-xl object-cover border border-[#d4a359]/40">
                    <div>
                        <strong class="text-xs text-[#2b0e14] block leading-tight">Deomali Tour Cabs</strong>
                        <span class="text-[10px] text-emerald-700 font-bold">24/7 Travel Desk</span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

<!-- 2. QUICK ROOM AVAILABILITY SEARCH BAR -->
<section class="relative -mt-10 z-30 max-w-6xl mx-auto px-4 sm:px-6">
    <div class="bg-white rounded-3xl shadow-2xl p-5 sm:p-6 border border-[#ebd9c8]">
        <form action="rooms.php" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            
            <div>
                <label for="check_in" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fa-regular fa-calendar text-[#d4a359] mr-1"></i> Check-In Date
                </label>
                <input type="date" id="check_in" name="check_in" required
                       class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
            </div>

            <div>
                <label for="check_out" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fa-regular fa-calendar-check text-[#d4a359] mr-1"></i> Check-Out Date
                </label>
                <input type="date" id="check_out" name="check_out" required
                       class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
            </div>

            <div>
                <label for="category" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fa-solid fa-snowflake text-[#d4a359] mr-1"></i> Room Type
                </label>
                <select id="category" name="category" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    <option value="all">All Room Types</option>
                    <option value="ac">AC Rooms (Deluxe & Super)</option>
                    <option value="non_ac">Non-AC Rooms (Budget)</option>
                    <option value="suite">Raj Presidential Suites</option>
                </select>
            </div>

            <div>
                <label for="guests" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    <i class="fa-solid fa-user-group text-[#d4a359] mr-1"></i> Guests
                </label>
                <select id="guests" name="guests" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    <option value="1">1 Guest</option>
                    <option value="2" selected>2 Guests</option>
                    <option value="3">3 Guests (Family)</option>
                    <option value="4">4+ Guests</option>
                </select>
            </div>

            <div>
                <button type="submit" class="w-full btn-gold font-bold text-xs py-3 px-4 rounded-xl shadow-md uppercase tracking-wider flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <span>Check Rooms</span>
                </button>
            </div>

        </form>
    </div>
</section>

<!-- 3. ABOUT US SECTION (Warm Linen Heritage Background with Archway Image) -->
<section class="py-20 lg:py-28 bg-[#f5efe6] mt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-14 items-center">
            
            <!-- Left Side: Archway Layered Luxury Photo Frame -->
            <div class="lg:col-span-6 relative">
                <div class="relative max-w-md mx-auto">
                    <!-- Main Archway Frame with 4-image auto slider -->
                    <div class="royal-arch-frame w-full h-96 sm:h-[440px] bg-slate-900 relative border-4 border-white shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=900&q=80" alt="Raj Residency Room 1" class="about-room-slide active">
                        <img src="https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=900&q=80" alt="Raj Residency Room 2" class="about-room-slide">
                        <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=900&q=80" alt="Raj Residency Room 3" class="about-room-slide">
                        <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=900&q=80" alt="Raj Residency Room 4" class="about-room-slide">
                    </div>

                    <!-- Floating Pill on top of image -->
                    <div class="absolute -bottom-5 left-6 bg-[#2b0e14] text-white px-5 py-2.5 rounded-2xl border border-[#d4a359]/50 shadow-xl flex items-center space-x-3 z-20">
                        <i class="fa-solid fa-shield-halved text-[#f3cf8a] text-lg"></i>
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider block text-[#f3cf8a]">Sanitized & Safe</span>
                            <span class="text-[10px] text-slate-300">100% Hygiene Assured</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content: About Raj Residency -->
            <div class="lg:col-span-6 space-y-6">
                <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                    <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                    <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                    <span>ABOUT <?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                </div>

                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14] leading-tight">
                    Experience Royal Warmth & Modern Elegance in Koraput
                </h2>

                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-light">
                    At <strong class="text-[#2b0e14] font-semibold"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong>, we take immense pride in delivering authentic Odia and Indian hospitality. Located strategically on Post Office Road in Koraput, Odisha, our hotel is designed to fulfill every travel desire with warmth, cleanliness, and royal care.
                </p>

                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-light">
                    Whether you are an executive on a business trip, a family exploring the picturesque hills of Deomali and Kolab Dam, or a budget backpacker, we offer both ultra-luxurious AC suites and sparkling clean Non-AC rooms.
                </p>

                <!-- Contact & Booking Bubble -->
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-[#ebd9c8] shadow-md flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-lg shadow-md border border-[#d4a359]/30">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">RESERVATION HOTLINE</span>
                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="text-sm font-black text-[#2b0e14] hover:text-[#b88738] transition"><?php echo htmlspecialchars($settings['hotel_phone']); ?></a>
                        </div>
                    </div>
                    <a href="contact.php" class="btn-gold px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm">
                        CONTACT US
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 4. BEST CONVENIENCE SERVICES SECTION -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Title -->
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                <span>WHAT WE OFFER</span>
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14]">
                Best Convenience Services
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 font-light">
                Experience royal hospitality with round-the-clock assistance and premier modern facilities in Koraput.
            </p>
        </div>

        <!-- Service Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
            <!-- Service 1: Room Services -->
            <div class="convenience-card flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Room Services</h3>
                        <a href="services.php" class="convenience-arrow-btn"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 font-light">24/7 dedicated room service, fresh linens, prompt housekeeping, and in-room meal delivery.</p>
                </div>
                <div class="convenience-img-wrapper">
                    <div class="convenience-icon-box"><i class="fa-solid fa-bell-concierge"></i></div>
                    <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80" alt="Room Services" class="w-full h-full object-cover">
                    <div class="convenience-bottom-bubble flex items-center justify-between">
                        <span class="text-xs font-bold text-[#2b0e14]">24 Hours Available</span>
                        <span class="text-[10px] text-emerald-700 font-bold uppercase">Always On</span>
                    </div>
                </div>
            </div>

            <!-- Service 2: Tea & Dining -->
            <div class="convenience-card flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Tea & Breakfast</h3>
                        <a href="services.php" class="convenience-arrow-btn"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 font-light">Start your morning with fresh tea, aromatic filter coffee, and healthy royal Odia breakfast delicacies.</p>
                </div>
                <div class="convenience-img-wrapper">
                    <div class="convenience-icon-box"><i class="fa-solid fa-mug-hot"></i></div>
                    <img src="https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=600&q=80" alt="Dining & Breakfast" class="w-full h-full object-cover">
                    <div class="convenience-bottom-bubble flex items-center justify-between">
                        <span class="text-xs font-bold text-[#2b0e14]">Fresh Multi-Cuisine</span>
                        <span class="text-[10px] text-amber-700 font-bold uppercase">Daily Morning</span>
                    </div>
                </div>
            </div>

            <!-- Service 3: Fiber Internet -->
            <div class="convenience-card flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Fiber Internet</h3>
                        <a href="services.php" class="convenience-arrow-btn"><i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                    <p class="text-xs text-slate-500 mt-2 font-light">High-speed uninterrupted optical fiber Wi-Fi in all rooms, suites, and reception lounge.</p>
                </div>
                <div class="convenience-img-wrapper">
                    <div class="convenience-icon-box"><i class="fa-solid fa-wifi"></i></div>
                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=600&q=80" alt="Fiber Wi-Fi" class="w-full h-full object-cover">
                    <div class="convenience-bottom-bubble flex items-center justify-between">
                        <span class="text-xs font-bold text-[#2b0e14]">Ultra-Fast Fiber</span>
                        <span class="text-[10px] text-blue-700 font-bold uppercase">Free Access</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center mt-12">
            <a href="services.php" class="btn-ghost-gold bg-[#2b0e14] text-white px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider inline-flex items-center space-x-2">
                <span>View All 8+ Hotel Services</span>
                <i class="fa-solid fa-arrow-right text-[11px]"></i>
            </a>
        </div>

    </div>
</section>

<!-- 5. COMFORT & ELEGANCE BANNER (Royal Maroon) -->
<section class="py-16 bg-[#2b0e14] text-white relative overflow-hidden border-y border-[#d4a359]/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-5">
        <h2 class="font-serif text-3xl sm:text-4xl font-bold">
            Comfort & Elegance Are Perfectly Combined Here !
        </h2>
        <p class="text-slate-300 text-xs sm:text-sm max-w-2xl mx-auto font-light leading-relaxed">
            Enjoy seamless check-in, pristine sanitized rooms, delicious multi-cuisine food, and dedicated staff ready to serve you 24 hours a day at <?php echo htmlspecialchars($settings['hotel_name']); ?>, Koraput.
        </p>
        <div class="pt-2">
            <a href="rooms.php" class="btn-gold px-9 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-xl inline-flex items-center space-x-2">
                <i class="fa-solid fa-door-open"></i>
                <span>EXPLORE ALL ROOMS</span>
            </a>
        </div>
    </div>
</section>

<!-- 6. FEATURED ROOMS & SUITES CATALOG (Arch-Top Cards) -->
<section id="rooms-catalog" class="py-20 lg:py-28 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Title & Filter Tabs -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-6">
            <div>
                <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase mb-2">
                    <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                    <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                    <span>ROOMS & ACCOMMODATIONS</span>
                </div>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14]">
                    Featured Rooms & Suites
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 font-light mt-1">
                    Choose from our luxury AC rooms and budget-friendly Non-AC options in Koraput.
                </p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex flex-wrap gap-2">
                <button type="button" class="room-filter-btn px-5 py-2.5 rounded-full text-xs font-bold transition bg-[#2b0e14] text-white shadow-lg border border-[#d4a359]/30" data-filter="all">All Rooms</button>
                <button type="button" class="room-filter-btn px-5 py-2.5 rounded-full text-xs font-bold transition bg-white text-slate-700 hover:bg-[#f5efe6] border border-[#ebd9c8]" data-filter="ac">AC Deluxe</button>
                <button type="button" class="room-filter-btn px-5 py-2.5 rounded-full text-xs font-bold transition bg-white text-slate-700 hover:bg-[#f5efe6] border border-[#ebd9c8]" data-filter="non_ac">Budget Non-AC</button>
                <button type="button" class="room-filter-btn px-5 py-2.5 rounded-full text-xs font-bold transition bg-white text-slate-700 hover:bg-[#f5efe6] border border-[#ebd9c8]" data-filter="suite">Royal Suites</button>
            </div>
        </div>

        <!-- Room Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php 
            foreach ($featuredRooms as $room) {
                include __DIR__ . '/includes/room_card.php';
            }
            ?>
        </div>

    </div>
</section>

<!-- 7. WHY GUESTS CHOOSE RAJ RESIDENCY -->
<section class="py-20 bg-white border-t border-[#ebd9c8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                <span>OUR CORE VALUES</span>
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14]">
                Why Guests Choose <?php echo htmlspecialchars($settings['hotel_name']); ?>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 rounded-3xl bg-[#fbf9f5] border border-[#ebd9c8] shadow-sm text-center space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl shadow-md border border-[#d4a359]/30">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Pristine Hygiene</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-light">Fresh linens, sanitized bathrooms, regular pest control, and daily housekeeping guarantee utmost cleanliness.</p>
            </div>

            <div class="p-8 rounded-3xl bg-[#fbf9f5] border border-[#ebd9c8] shadow-sm text-center space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl shadow-md border border-[#d4a359]/30">
                    <i class="fa-solid fa-tags"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Honest & Transparent Pricing</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-light">No hidden checkout fees or sudden surcharges. Pay the best direct hotel rate always with zero booking fee.</p>
            </div>

            <div class="p-8 rounded-3xl bg-[#fbf9f5] border border-[#ebd9c8] shadow-sm text-center space-y-4">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl shadow-md border border-[#d4a359]/30">
                    <i class="fa-solid fa-hands-praying"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Atithi Devo Bhava</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-light">Our attentive team treats every guest with unmatched Indian warmth, care, and respectful 24-hour assistance.</p>
            </div>
        </div>
    </div>
</section>

<!-- 8. GUEST REVIEWS & TESTIMONIALS SECTION -->
<?php 
$approvedHomeReviews = get_approved_reviews(3);
$reviewStats = get_review_stats();
?>
<section class="py-20 bg-[#f5efe6] border-t border-[#d4a359]/30 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-14 gap-6">
            <div class="space-y-2">
                <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                    <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                    <i class="fa-solid fa-star text-[#d4a359] text-[10px]"></i>
                    <span>VERIFIED GUEST EXPERIENCES</span>
                    <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                </div>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14]">
                    What Our Guests Say
                </h2>
                <p class="text-xs sm:text-sm text-slate-700 font-light">
                    Rated <strong><?php echo $reviewStats['average']; ?> / 5.0</strong> by happy travelers visiting Koraput, Odisha.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="reviews.php" class="btn-gold px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm flex items-center space-x-2">
                    <i class="fa-solid fa-pen-to-square text-[11px]"></i>
                    <span>Write a Review</span>
                </a>
                <a href="reviews.php" class="bg-white border border-[#ebd9c8] hover:bg-slate-50 text-slate-800 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm transition">
                    View All Reviews
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($approvedHomeReviews as $hrv): ?>
                <div class="bg-white rounded-3xl p-8 border border-[#ebd9c8] shadow-md hover:shadow-xl transition space-y-4 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="text-[#d4a359] text-xs flex space-x-0.5">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <i class="fa-solid fa-star <?php echo ($s <= (int)$hrv['rating']) ? 'text-[#d4a359]' : 'text-slate-300'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-[10px] text-emerald-800 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">
                                Verified Stay
                            </span>
                        </div>
                        <h4 class="font-serif font-bold text-base text-[#2b0e14]"><?php echo htmlspecialchars($hrv['title']); ?></h4>
                        <p class="text-xs text-slate-600 leading-relaxed font-light italic">
                            &ldquo;<?php echo htmlspecialchars(mb_strimwidth($hrv['review_text'], 0, 150, '...')); ?>&rdquo;
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center font-bold text-xs shadow-sm">
                            <?php echo strtoupper(substr($hrv['guest_name'] ?? 'G', 0, 1)); ?>
                        </div>
                        <div>
                            <strong class="text-xs text-[#2b0e14] block"><?php echo htmlspecialchars($hrv['guest_name']); ?></strong>
                            <span class="text-[10px] text-slate-400"><?php echo htmlspecialchars($hrv['guest_city']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<!-- 8. CTA SECTION (WhatsApp & Direct Hotline) -->
<section class="py-16 bg-gradient-to-r from-[#1c080d] via-[#2b0e14] to-[#1c080d] text-white border-t border-[#d4a359]/30">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white">
            Visiting Koraput, Odisha? Book Your Royal Stay Today!
        </h2>
        <p class="text-slate-300 text-xs sm:text-sm max-w-2xl mx-auto font-light">
            Conveniently located on Post Office Road, Koraput. Get instant discount quotes and room photos on WhatsApp.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
            <a href="rooms.php" class="btn-gold px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2">
                <i class="fa-solid fa-calendar-check"></i>
                <span>Instant Online Booking</span>
            </a>
            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20I%20want%20to%20inquire%20about%20room%20booking" target="_blank" class="bg-emerald-700 hover:bg-emerald-800 text-white px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2 transition border border-emerald-500/30">
                <i class="fa-brands fa-whatsapp text-base"></i>
                <span>Chat On WhatsApp</span>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
