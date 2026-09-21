<?php
// services.php - Ultra-Luxury Services & Facilities Page for Raj Residency
$pageTitle = "Services & Facilities";
require_once __DIR__ . '/includes/header.php';

// Fetch dynamic Dining & Cuisine photos from database
$diningSlides = get_section_images('about_dining', true);
if (empty($diningSlides)) {
    $defaultBanners = SeedData::getDefaultBanners();
    $diningSlides = array_filter($defaultBanners, function($b) {
        return ($b['section_name'] ?? '') === 'about_dining';
    });
}
$diningSlides = array_values($diningSlides);
?>

<!-- 1. BREADCRUMB HERO BANNER -->
<section class="bg-[#2b0e14] text-white py-14 lg:py-20 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-3">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <span class="text-white">SERVICES & FACILITIES</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            World-Class Hotel Services & Dining
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto font-light leading-relaxed">
            Experience uncompromised luxury at <?php echo htmlspecialchars($settings['hotel_name']); ?> — from authentic multi-cuisine dining to 24/7 room service, high-speed fiber Wi-Fi, and custom Koraput sightseeing cab packages.
        </p>
    </div>

    <!-- Decorative background glow -->
    <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
</section>

<!-- 2. GRAND DINING & CUISINE SHOWCASE (CONNECTED TO ADMIN 'ABOUT_DINING') -->
<section class="py-16 sm:py-24 bg-[#fffdfa] border-b border-[#ebd9c8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Side: Dynamic Auto Photo Slider -->
            <div class="lg:col-span-6 relative">
                <div class="relative max-w-lg mx-auto">
                    
                    <!-- Royal Archway Frame Slider -->
                    <div class="royal-arch-frame w-full h-[380px] sm:h-[460px] bg-slate-900 relative border-4 border-white shadow-2xl overflow-hidden rounded-t-[50px] rounded-b-3xl">
                        <?php if (!empty($diningSlides)): ?>
                            <?php foreach ($diningSlides as $dIdx => $dSlide): ?>
                                <div class="dining-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?php echo $dIdx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>" data-index="<?php echo $dIdx; ?>">
                                    <img src="<?php echo htmlspecialchars($dSlide['image_path']); ?>" alt="<?php echo htmlspecialchars($dSlide['title']); ?>" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#1c080d]/80 via-transparent to-transparent"></div>
                                    
                                    <!-- Slide Caption Badge -->
                                    <div class="absolute bottom-6 left-6 right-6 flex items-center justify-between">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-[#2b0e14]/90 text-[#f3cf8a] border border-[#d4a359]/40 backdrop-blur-md shadow-md">
                                            <?php echo htmlspecialchars($dSlide['badge_text'] ?? 'Raj Dining'); ?>
                                        </span>
                                        <span class="text-xs font-bold text-white drop-shadow truncate max-w-[200px]">
                                            <?php echo htmlspecialchars($dSlide['title']); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Slider Dot Indicators -->
                    <div class="flex items-center justify-center space-x-2 mt-4" id="dining-dots">
                        <?php foreach ($diningSlides as $dIdx => $dSlide): ?>
                            <button type="button" onclick="goToDiningSlide(<?php echo $dIdx; ?>)" class="dining-dot w-3 h-3 rounded-full transition-all <?php echo $dIdx === 0 ? 'bg-[#2b0e14] w-6' : 'bg-slate-300 hover:bg-[#d4a359]'; ?>"></button>
                        <?php endforeach; ?>
                    </div>

                    <!-- Floating Quality Pill -->
                    <div class="absolute -top-4 -right-4 bg-[#2b0e14] text-[#f3cf8a] px-4 py-2 rounded-2xl border border-[#d4a359]/50 shadow-xl flex items-center space-x-2 z-20">
                        <i class="fa-solid fa-utensils text-[#d4a359] text-xs"></i>
                        <span class="text-[11px] font-black uppercase tracking-wider">Multi-Cuisine & Breakfast</span>
                    </div>

                </div>
            </div>

            <!-- Right Side: Dining Description & Timings -->
            <div class="lg:col-span-6 space-y-6">
                
                <div class="space-y-2">
                    <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                        <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                        <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                        <span>ROYAL DINING EXPERIENCE</span>
                    </div>
                    <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14] leading-tight">
                        In-House Multi-Cuisine Restaurant & Buffet Delicacies
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-light">
                        At <strong class="text-[#2b0e14] font-semibold"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong>, food is a celebratory affair. Our master chefs prepare fresh traditional Odia specialties, mouth-watering North Indian curries, South Indian breakfast tiffins, and Continental dishes using pure local spices and farm-fresh ingredients.
                    </p>
                </div>

                <!-- Timings & Highlights Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    
                    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] space-y-1">
                        <div class="flex items-center space-x-2 text-[#2b0e14] font-bold text-xs">
                            <i class="fa-solid fa-mug-saucer text-[#d4a359]"></i>
                            <span>Morning Buffet Breakfast</span>
                        </div>
                        <p class="text-[11px] text-slate-600">7:00 AM – 10:30 AM Daily</p>
                        <span class="text-[10px] text-emerald-700 font-semibold block">Fresh dosas, poori bhaji, tea & coffee</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] space-y-1">
                        <div class="flex items-center space-x-2 text-[#2b0e14] font-bold text-xs">
                            <i class="fa-solid fa-bell-concierge text-[#d4a359]"></i>
                            <span>24/7 In-Room Dining</span>
                        </div>
                        <p class="text-[11px] text-slate-600">Round-the-clock room service</p>
                        <span class="text-[10px] text-emerald-700 font-semibold block">Dial intercom for instant delivery</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] space-y-1">
                        <div class="flex items-center space-x-2 text-[#2b0e14] font-bold text-xs">
                            <i class="fa-solid fa-bowl-rice text-[#d4a359]"></i>
                            <span>Lunch Thalis & Specialties</span>
                        </div>
                        <p class="text-[11px] text-slate-600">12:30 PM – 3:30 PM</p>
                        <span class="text-[10px] text-slate-500 block">Authentic Odia & North Indian Thalis</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] space-y-1">
                        <div class="flex items-center space-x-2 text-[#2b0e14] font-bold text-xs">
                            <i class="fa-solid fa-champagne-glasses text-[#d4a359]"></i>
                            <span>Dinner & Family Gathering</span>
                        </div>
                        <p class="text-[11px] text-slate-600">7:30 PM – 11:00 PM</p>
                        <span class="text-[10px] text-slate-500 block">Cozy royal ambience for families</span>
                    </div>

                </div>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="btn-gold px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider inline-flex items-center space-x-2 shadow-lg">
                        <i class="fa-solid fa-phone"></i>
                        <span>Order Food / Call Desk</span>
                    </a>
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20please%20share%20the%20dining%20menu" target="_blank" class="px-6 py-3 rounded-full border border-[#2b0e14] text-[#2b0e14] hover:bg-[#2b0e14] hover:text-[#f3cf8a] text-xs font-bold transition inline-flex items-center space-x-2">
                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                        <span>WhatsApp Menu</span>
                    </a>
                </div>

            </div>

        </div>

    </div>
</section>

<!-- 3. UPGRADED LUXURY 6 HOTEL SERVICE CARDS -->
<section class="py-20 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                <span>PREMIUM HOTEL AMENITIES</span>
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14]">
                Complete Comfort & Royal Conveniences
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 font-light">
                Designed to make your stay effortless, relaxing, and enjoyable in Koraput, Odisha.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Service 1: 24/7 Room Service -->
            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/30 hover:border-[#d4a359] shadow-sm hover:shadow-2xl transition duration-300 space-y-5 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] group-hover:scale-110 transition duration-300 flex items-center justify-center text-2xl shadow-lg border border-[#d4a359]/40">
                            <i class="fa-solid fa-bell-concierge"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                            24 Hours Active
                        </span>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14] group-hover:text-[#b88738] transition">24/7 In-Room Dining & Service</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Round-the-clock room service delivering delicious hot meals, refreshing tea/coffee, extra linens, and luxury dental kits with just one phone call from your room intercom.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]"><i class="fa-solid fa-phone-volume text-[#d4a359] mr-1.5"></i> Dial Intercom #9</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 2: Multi-Cuisine Restaurant -->
            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/30 hover:border-[#d4a359] shadow-sm hover:shadow-2xl transition duration-300 space-y-5 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] group-hover:scale-110 transition duration-300 flex items-center justify-center text-2xl shadow-lg border border-[#d4a359]/40">
                            <i class="fa-solid fa-utensils"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-[#f5efe6] text-[#2b0e14] border border-[#d4a359]/40 font-mono">
                            7:00 AM - 11:00 PM
                        </span>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14] group-hover:text-[#b88738] transition">In-House Multi-Cuisine Restaurant</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Relish authentic Odia culinary recipes, sizzling tandoori items, rich North Indian curries, and South Indian morning buffet breakfasts made fresh every morning.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]"><i class="fa-solid fa-bowl-food text-[#d4a359] mr-1.5"></i> Pure & Fresh Spices</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 3: High-Speed Wi-Fi -->
            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/30 hover:border-[#d4a359] shadow-sm hover:shadow-2xl transition duration-300 space-y-5 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] group-hover:scale-110 transition duration-300 flex items-center justify-center text-2xl shadow-lg border border-[#d4a359]/40">
                            <i class="fa-solid fa-wifi"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-300">
                            100+ Mbps Fiber
                        </span>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14] group-hover:text-[#b88738] transition">High-Speed Fiber Wi-Fi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Complimentary unlimited optical fiber broadband available in all AC/Non-AC guest rooms, conference halls, and lobby for lag-free video calls and entertainment.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]"><i class="fa-solid fa-lock text-[#d4a359] mr-1.5"></i> 100% Free for Guests</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 4: Secure Parking -->
            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/30 hover:border-[#d4a359] shadow-sm hover:shadow-2xl transition duration-300 space-y-5 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] group-hover:scale-110 transition duration-300 flex items-center justify-center text-2xl shadow-lg border border-[#d4a359]/40">
                            <i class="fa-solid fa-square-parking"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-purple-50 text-purple-800 border border-purple-300">
                            24/7 CCTV Monitored
                        </span>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14] group-hover:text-[#b88738] transition">Secure Valet & Bus Parking</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Spacious, safe on-premise parking for personal cars, two-wheelers, and large tourist buses under 24-hour HD CCTV surveillance and dedicated security guard watch.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]"><i class="fa-solid fa-shield-halved text-[#d4a359] mr-1.5"></i> Zero Parking Fee</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 5: Travel Desk & Cabs -->
            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/30 hover:border-[#d4a359] shadow-sm hover:shadow-2xl transition duration-300 space-y-5 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] group-hover:scale-110 transition duration-300 flex items-center justify-center text-2xl shadow-lg border border-[#d4a359]/40">
                            <i class="fa-solid fa-taxi"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                            Deomali & Kolab Tours
                        </span>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14] group-hover:text-[#b88738] transition">Travel Desk & Sightseeing Cabs</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Airport & railway station pickup/drop, reliable local taxi cabs, and curated day tour itineraries to Deomali peak, Kolab Dam, Duduma waterfall, and Gupteswar cave.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]"><i class="fa-solid fa-map-location-dot text-[#d4a359] mr-1.5"></i> Custom Cab Packages</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 6: 24/7 Power & Hot Water Backup -->
            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/30 hover:border-[#d4a359] shadow-sm hover:shadow-2xl transition duration-300 space-y-5 flex flex-col justify-between group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] group-hover:scale-110 transition duration-300 flex items-center justify-center text-2xl shadow-lg border border-[#d4a359]/40">
                            <i class="fa-solid fa-bolt"></i>
                        </div>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                            100% Guaranteed
                        </span>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14] group-hover:text-[#b88738] transition">24/7 Power & Hot Water Backup</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Equipped with heavy-duty silent generators and automatic switchgear so you never experience power cuts or hot water delays even in hill weather or storms.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]"><i class="fa-solid fa-shower text-[#d4a359] mr-1.5"></i> Instant Geysers in all rooms</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- 4. 🎉 GRAND BANQUET, CONFERENCE & GROUP DINING BANNER -->
<section class="py-20 bg-[#f5efe6] border-t border-[#ebd9c8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-gradient-to-r from-[#2b0e14] via-[#3d141d] to-[#1c080d] rounded-3xl p-8 sm:p-14 text-white border border-[#d4a359]/50 shadow-2xl relative overflow-hidden">
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <div class="lg:col-span-8 space-y-4">
                    <div class="inline-flex items-center space-x-2 bg-[#1c080d]/80 px-3.5 py-1.5 rounded-full text-[10px] font-bold tracking-widest text-[#f3cf8a] uppercase border border-[#d4a359]/40">
                        <i class="fa-solid fa-crown text-[#d4a359]"></i>
                        <span>BANQUET & CORPORATE GATHERINGS</span>
                    </div>
                    <h3 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight">
                        Host Your Special Events, Weddings & Conferences in Koraput
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-300 font-light leading-relaxed max-w-2xl">
                        With capacity for up to 150+ guests, customized royal buffet catering, crystal sound audio-visual systems, and seamless air conditioning, <?php echo htmlspecialchars($settings['hotel_name']); ?> is the premier venue for business meets, marriage parties, and family anniversaries.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 pt-2 text-xs font-semibold text-[#f3cf8a]">
                        <span class="flex items-center space-x-1.5"><i class="fa-solid fa-check text-emerald-400"></i><span>Capacity 150+ Guests</span></span>
                        <span class="flex items-center space-x-1.5"><i class="fa-solid fa-check text-emerald-400"></i><span>Custom Buffet Menus</span></span>
                        <span class="flex items-center space-x-1.5"><i class="fa-solid fa-check text-emerald-400"></i><span>Bulk Room Discounts</span></span>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col sm:flex-row lg:flex-col gap-3 justify-center">
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="btn-gold px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider text-center shadow-lg flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-phone"></i>
                        <span>Call Event Manager</span>
                    </a>
                    <a href="contact.php" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider text-center transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span>Book Banquet Hall</span>
                    </a>
                </div>

            </div>

            <!-- Background decorative elements -->
            <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
        </div>

    </div>
</section>

<!-- DINING SLIDER JAVASCRIPT -->
<script>
    let currentDiningIdx = 0;
    const diningSlides = document.querySelectorAll('.dining-slide');
    const diningDots = document.querySelectorAll('.dining-dot');
    const totalDiningSlides = diningSlides.length;
    let diningInterval = null;

    function showDiningSlide(index) {
        if (totalDiningSlides === 0) return;
        currentDiningIdx = (index + totalDiningSlides) % totalDiningSlides;

        diningSlides.forEach((slide, idx) => {
            if (idx === currentDiningIdx) {
                slide.classList.remove('opacity-0', 'z-0');
                slide.classList.add('opacity-100', 'z-10');
            } else {
                slide.classList.remove('opacity-100', 'z-10');
                slide.classList.add('opacity-0', 'z-0');
            }
        });

        diningDots.forEach((dot, idx) => {
            if (idx === currentDiningIdx) {
                dot.className = 'dining-dot w-6 h-3 rounded-full bg-[#2b0e14] transition-all';
            } else {
                dot.className = 'dining-dot w-3 h-3 rounded-full bg-slate-300 hover:bg-[#d4a359] transition-all';
            }
        });
    }

    function goToDiningSlide(index) {
        showDiningSlide(index);
        resetDiningTimer();
    }

    function nextDiningSlide() {
        showDiningSlide(currentDiningIdx + 1);
    }

    function startDiningTimer() {
        if (totalDiningSlides > 1) {
            diningInterval = setInterval(nextDiningSlide, 4500);
        }
    }

    function resetDiningTimer() {
        if (diningInterval) clearInterval(diningInterval);
        startDiningTimer();
    }

    document.addEventListener('DOMContentLoaded', () => {
        startDiningTimer();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
