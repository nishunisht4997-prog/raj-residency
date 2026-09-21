<?php
// about.php - Ultra-Luxury About Us & Heritage Experience for Raj Residency
$pageTitle = "About Us & Heritage";
require_once __DIR__ . '/includes/header.php';

// Fetch dynamic Room Slides and Dining Slides from database
$roomSlides = get_section_images('about_rooms', true);
$diningSlides = get_section_images('about_dining', true);
$buildingList = get_section_images('about_building', true);

if (empty($roomSlides)) {
    $defaultBanners = SeedData::getDefaultBanners();
    $roomSlides = array_filter($defaultBanners, function($b) { return ($b['section_name'] ?? '') === 'about_rooms'; });
}
if (empty($diningSlides)) {
    $defaultBanners = SeedData::getDefaultBanners();
    $diningSlides = array_filter($defaultBanners, function($b) { return ($b['section_name'] ?? '') === 'about_dining'; });
}

$roomSlides = array_values($roomSlides);
$diningSlides = array_values($diningSlides);
$buildingPhoto = !empty($buildingList) ? $buildingList[0]['image_path'] : 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80';
?>

<!-- 1. BREADCRUMB HERO BANNER -->
<section class="bg-[#2b0e14] text-white py-14 lg:py-20 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-3">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <span class="text-white">ABOUT US</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            Welcome to <?php echo htmlspecialchars($settings['hotel_name']); ?>
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto font-light leading-relaxed">
            A boutique luxury destination blending traditional royal Indian warmth with contemporary comforts and unmatched affordability in Koraput, Odisha.
        </p>
    </div>

    <!-- Decorative background glow -->
    <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
</section>

<!-- 2. ROYAL HERITAGE STORY & BUILDING LANDMARK -->
<section class="py-16 sm:py-24 bg-white border-b border-[#ebd9c8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Side: Story Text -->
            <div class="lg:col-span-6 space-y-6">
                <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                    <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                    <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                    <span>OUR HERITAGE & PROMISE</span>
                </div>

                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14] leading-tight">
                    Serving Royal Hospitality With Sincere Care & Affordability
                </h2>

                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-light">
                    Established with the vision of providing every traveler with a majestic and relaxing stay, <strong class="text-[#2b0e14] font-semibold"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong> stands as a landmark of comfort, safety, and hygiene on <strong class="text-slate-800"><?php echo htmlspecialchars($settings['hotel_address']); ?></strong>.
                </p>

                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-light">
                    From corporate executives needing high-speed fiber Wi-Fi to families exploring the scenic wonders of Koraput—including Deomali peak, Kolab Reservoir, Gupteswar Cave, and Sabara Srikhetra Jagannath Temple—we cater to every distinct travel need.
                </p>

                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-light">
                    Our hotel features a balanced selection of ultra-luxury AC suites and sparkling clean budget non-AC rooms, ensuring nobody misses out on royal hospitality.
                </p>

                <!-- Core Values Chips -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-base shrink-0 shadow-sm">
                            <i class="fa-solid fa-shield-heart"></i>
                        </div>
                        <div>
                            <strong class="text-xs text-[#2b0e14] block font-bold">100% Sanitized</strong>
                            <span class="text-[10px] text-slate-500">Daily housekeeping</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-base shrink-0 shadow-sm">
                            <i class="fa-solid fa-hand-holding-dollar"></i>
                        </div>
                        <div>
                            <strong class="text-xs text-[#2b0e14] block font-bold">Best Price Promise</strong>
                            <span class="text-[10px] text-slate-500">Zero booking fees</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Side: Building Archway Photo -->
            <div class="lg:col-span-6">
                <div class="royal-arch-frame relative group overflow-hidden rounded-t-[60px] rounded-b-3xl border-4 border-white shadow-2xl bg-slate-900 h-[480px]">
                    <img src="<?php echo htmlspecialchars($buildingPhoto); ?>" alt="<?php echo htmlspecialchars($settings['hotel_name']); ?> Building Landmark" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1c080d]/85 via-transparent to-transparent"></div>
                    
                    <div class="absolute bottom-6 left-6 right-6 p-5 rounded-2xl bg-[#2b0e14]/90 backdrop-blur-md border border-[#d4a359]/40 text-white shadow-xl">
                        <div class="flex items-center space-x-2 text-xs font-bold text-[#f3cf8a] uppercase">
                            <i class="fa-solid fa-crown text-[10px]"></i>
                            <span>RAJ RESIDENCY KORAPUT</span>
                        </div>
                        <p class="text-xs text-slate-300 font-light mt-1">Prime landmark on Post Office Road, Dist - Koraput, Odisha - 764020</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 3. 🏆 15,000+ HAPPY GUESTS & TRUST METRICS COUNTER -->
<section class="py-16 bg-[#2b0e14] text-white border-y border-[#d4a359]/40 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-12 space-y-2">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#f3cf8a] block">PROVEN HOSPITALITY RECORD</span>
            <h2 class="font-serif text-2xl sm:text-3xl lg:text-4xl font-bold text-white">
                Trusted by Families & Executives Across India
            </h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
            
            <div class="p-6 rounded-3xl bg-[#1c080d]/90 border border-[#d4a359]/30 text-center space-y-2 shadow-xl hover:border-[#d4a359] transition group">
                <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl mx-auto shadow-md border border-[#d4a359]/40 group-hover:scale-110 transition">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#f3cf8a] via-[#e5b869] to-[#d4a359]">15,000+</div>
                <span class="text-xs font-bold text-slate-300 block uppercase tracking-wider">Delighted Guests Hosted</span>
                <p class="text-[10px] text-slate-400 font-light">From tourists to corporate executives</p>
            </div>

            <div class="p-6 rounded-3xl bg-[#1c080d]/90 border border-[#d4a359]/30 text-center space-y-2 shadow-xl hover:border-[#d4a359] transition group">
                <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl mx-auto shadow-md border border-[#d4a359]/40 group-hover:scale-110 transition">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#f3cf8a] via-[#e5b869] to-[#d4a359]">50+</div>
                <span class="text-xs font-bold text-slate-300 block uppercase tracking-wider">Spotless Cozy Rooms</span>
                <p class="text-[10px] text-slate-400 font-light">Luxury AC & Budget Non-AC</p>
            </div>

            <div class="p-6 rounded-3xl bg-[#1c080d]/90 border border-[#d4a359]/30 text-center space-y-2 shadow-xl hover:border-[#d4a359] transition group">
                <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl mx-auto shadow-md border border-[#d4a359]/40 group-hover:scale-110 transition">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#f3cf8a] via-[#e5b869] to-[#d4a359]">100%</div>
                <span class="text-xs font-bold text-slate-300 block uppercase tracking-wider">Power & Geyser Backup</span>
                <p class="text-[10px] text-slate-400 font-light">Heavy-duty generator uninterrupted</p>
            </div>

            <div class="p-6 rounded-3xl bg-[#1c080d]/90 border border-[#d4a359]/30 text-center space-y-2 shadow-xl hover:border-[#d4a359] transition group">
                <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl mx-auto shadow-md border border-[#d4a359]/40 group-hover:scale-110 transition">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#f3cf8a] via-[#e5b869] to-[#d4a359]">4.8 / 5</div>
                <span class="text-xs font-bold text-slate-300 block uppercase tracking-wider">Guest Satisfaction</span>
                <p class="text-[10px] text-slate-400 font-light">Rated for cleanliness & service</p>
            </div>

        </div>

    </div>
</section>

<!-- 4. DUAL HERITAGE SHOWCASE (ROOMS & DINING INTERACTIVE TABS) -->
<section class="py-20 bg-[#fbf9f5] border-b border-[#ebd9c8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                <span>EXPLORE THE PROPERTY</span>
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14]">
                Dual Heritage Experience: Rooms & Dining
            </h2>
            <p class="text-xs sm:text-sm text-slate-600 font-light">
                Switch between our luxurious accommodations and royal dining flavors below.
            </p>

            <!-- Interactive Tab Switcher -->
            <div class="inline-flex items-center p-1.5 rounded-full bg-[#f5efe6] border border-[#ebd9c8] shadow-sm mt-4">
                <button type="button" id="tab-btn-rooms" onclick="switchShowcaseTab('rooms')" class="px-6 py-2.5 rounded-full text-xs font-bold transition flex items-center space-x-2 bg-[#2b0e14] text-[#f3cf8a] shadow-md">
                    <i class="fa-solid fa-bed text-xs"></i>
                    <span>Luxury Accommodations (<?php echo count($roomSlides); ?>)</span>
                </button>
                <button type="button" id="tab-btn-dining" onclick="switchShowcaseTab('dining')" class="px-6 py-2.5 rounded-full text-xs font-bold text-slate-700 hover:text-[#2b0e14] transition flex items-center space-x-2">
                    <i class="fa-solid fa-utensils text-xs"></i>
                    <span>Dining & Cuisine (<?php echo count($diningSlides); ?>)</span>
                </button>
            </div>
        </div>

        <!-- SHOWCASE 1: LUXURY ROOMS SLIDER -->
        <div id="showcase-rooms" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-7">
                <div class="royal-arch-frame relative rounded-t-[50px] rounded-b-3xl border-4 border-white shadow-2xl bg-slate-900 h-[380px] sm:h-[460px] overflow-hidden">
                    <?php foreach ($roomSlides as $rIdx => $rSlide): ?>
                        <div class="room-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?php echo $rIdx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>">
                            <img src="<?php echo htmlspecialchars($rSlide['image_path']); ?>" alt="<?php echo htmlspecialchars($rSlide['title']); ?>" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#1c080d]/80 via-transparent to-transparent"></div>
                            
                            <div class="absolute bottom-6 left-6 right-6 flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-[#2b0e14]/90 text-[#f3cf8a] border border-[#d4a359]/40 backdrop-blur-md shadow-md">
                                    <?php echo htmlspecialchars($rSlide['badge_text'] ?? 'Luxury Stay'); ?>
                                </span>
                                <span class="text-xs font-bold text-white drop-shadow truncate max-w-[200px]">
                                    <?php echo htmlspecialchars($rSlide['title']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Dots -->
                <div class="flex items-center justify-center space-x-2 mt-4" id="room-dots">
                    <?php foreach ($roomSlides as $rIdx => $rSlide): ?>
                        <button type="button" onclick="goToRoomSlide(<?php echo $rIdx; ?>)" class="room-dot w-3 h-3 rounded-full transition-all <?php echo $rIdx === 0 ? 'bg-[#2b0e14] w-6' : 'bg-slate-300 hover:bg-[#d4a359]'; ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-5">
                <div class="space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#b88738] block">PREMIUM ACCOMMODATION</span>
                    <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">Air-Conditioned Deluxe & Cozy Non-AC Comfort</h3>
                    <p class="text-xs sm:text-sm text-slate-600 font-light leading-relaxed">
                        Every room at Raj Residency is meticulously sanitized, thoughtfully furnished with comfortable king-size spring beds, high-speed fiber internet, and crystal clean private attached bathrooms.
                    </p>
                </div>

                <ul class="space-y-2 text-xs text-slate-700 font-medium">
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>Executive AC Deluxe & Royal Family Suites</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>Sparkling Clean Budget Non-AC Rooms</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>24/7 Hot & Cold Geyser Water Supply</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>Intercom & Daily Housekeeping</span></li>
                </ul>

                <div class="pt-2">
                    <a href="rooms.php" class="btn-gold px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider inline-flex items-center space-x-2 shadow-lg">
                        <i class="fa-solid fa-door-open"></i>
                        <span>Explore All Rooms</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- SHOWCASE 2: DINING & CUISINE SLIDER (HIDDEN INITIALLY) -->
        <div id="showcase-dining" class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center hidden">
            <div class="lg:col-span-7">
                <div class="royal-arch-frame relative rounded-t-[50px] rounded-b-3xl border-4 border-white shadow-2xl bg-slate-900 h-[380px] sm:h-[460px] overflow-hidden">
                    <?php foreach ($diningSlides as $dIdx => $dSlide): ?>
                        <div class="about-dining-slide absolute inset-0 transition-opacity duration-700 ease-in-out <?php echo $dIdx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>">
                            <img src="<?php echo htmlspecialchars($dSlide['image_path']); ?>" alt="<?php echo htmlspecialchars($dSlide['title']); ?>" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-[#1c080d]/80 via-transparent to-transparent"></div>
                            
                            <div class="absolute bottom-6 left-6 right-6 flex items-center justify-between">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-[#2b0e14]/90 text-[#f3cf8a] border border-[#d4a359]/40 backdrop-blur-md shadow-md">
                                    <?php echo htmlspecialchars($dSlide['badge_text'] ?? 'Royal Dining'); ?>
                                </span>
                                <span class="text-xs font-bold text-white drop-shadow truncate max-w-[200px]">
                                    <?php echo htmlspecialchars($dSlide['title']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Dots -->
                <div class="flex items-center justify-center space-x-2 mt-4" id="about-dining-dots">
                    <?php foreach ($diningSlides as $dIdx => $dSlide): ?>
                        <button type="button" onclick="goToAboutDiningSlide(<?php echo $dIdx; ?>)" class="about-dining-dot w-3 h-3 rounded-full transition-all <?php echo $dIdx === 0 ? 'bg-[#2b0e14] w-6' : 'bg-slate-300 hover:bg-[#d4a359]'; ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="lg:col-span-5 space-y-5">
                <div class="space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#b88738] block">CULINARY EXCELLENCE</span>
                    <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">Multi-Cuisine Flavours & Buffet Delicacies</h3>
                    <p class="text-xs sm:text-sm text-slate-600 font-light leading-relaxed">
                        Relish authentic Odia culinary preparations, aromatic North Indian curries, and South Indian morning buffet breakfasts prepared with fresh local ingredients.
                    </p>
                </div>

                <ul class="space-y-2 text-xs text-slate-700 font-medium">
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>Morning Buffet Breakfast (7:00 AM - 10:30 AM)</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>Authentic Odia & North Indian Thalis</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>24/7 In-Room Dining Service</span></li>
                    <li class="flex items-center space-x-2"><i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i><span>Special Group & Banquet Catering</span></li>
                </ul>

                <div class="pt-2">
                    <a href="services.php" class="btn-gold px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider inline-flex items-center space-x-2 shadow-lg">
                        <i class="fa-solid fa-utensils"></i>
                        <span>View Dining & Services</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- JAVASCRIPT FOR DUAL SHOWCASE -->
<script>
    // Tab switcher
    function switchShowcaseTab(tab) {
        const roomsBox = document.getElementById('showcase-rooms');
        const diningBox = document.getElementById('showcase-dining');
        const btnRooms = document.getElementById('tab-btn-rooms');
        const btnDining = document.getElementById('tab-btn-dining');

        if (tab === 'rooms') {
            roomsBox.classList.remove('hidden');
            diningBox.classList.add('hidden');
            btnRooms.className = 'px-6 py-2.5 rounded-full text-xs font-bold transition flex items-center space-x-2 bg-[#2b0e14] text-[#f3cf8a] shadow-md';
            btnDining.className = 'px-6 py-2.5 rounded-full text-xs font-bold text-slate-700 hover:text-[#2b0e14] transition flex items-center space-x-2';
        } else {
            diningBox.classList.remove('hidden');
            roomsBox.classList.add('hidden');
            btnDining.className = 'px-6 py-2.5 rounded-full text-xs font-bold transition flex items-center space-x-2 bg-[#2b0e14] text-[#f3cf8a] shadow-md';
            btnRooms.className = 'px-6 py-2.5 rounded-full text-xs font-bold text-slate-700 hover:text-[#2b0e14] transition flex items-center space-x-2';
        }
    }

    // Room Slider
    let curRoomIdx = 0;
    const roomSlides = document.querySelectorAll('.room-slide');
    const roomDots = document.querySelectorAll('.room-dot');
    const totalRoomSlides = roomSlides.length;

    function showRoomSlide(idx) {
        if (totalRoomSlides === 0) return;
        curRoomIdx = (idx + totalRoomSlides) % totalRoomSlides;
        roomSlides.forEach((s, i) => {
            if (i === curRoomIdx) {
                s.classList.remove('opacity-0', 'z-0');
                s.classList.add('opacity-100', 'z-10');
            } else {
                s.classList.remove('opacity-100', 'z-10');
                s.classList.add('opacity-0', 'z-0');
            }
        });
        roomDots.forEach((d, i) => {
            d.className = (i === curRoomIdx) ? 'room-dot w-6 h-3 rounded-full bg-[#2b0e14] transition-all' : 'room-dot w-3 h-3 rounded-full bg-slate-300 hover:bg-[#d4a359] transition-all';
        });
    }

    function goToRoomSlide(idx) {
        showRoomSlide(idx);
    }

    // Dining Slider
    let curAboutDiningIdx = 0;
    const aboutDiningSlides = document.querySelectorAll('.about-dining-slide');
    const aboutDiningDots = document.querySelectorAll('.about-dining-dot');
    const totalAboutDiningSlides = aboutDiningSlides.length;

    function showAboutDiningSlide(idx) {
        if (totalAboutDiningSlides === 0) return;
        curAboutDiningIdx = (idx + totalAboutDiningSlides) % totalAboutDiningSlides;
        aboutDiningSlides.forEach((s, i) => {
            if (i === curAboutDiningIdx) {
                s.classList.remove('opacity-0', 'z-0');
                s.classList.add('opacity-100', 'z-10');
            } else {
                s.classList.remove('opacity-100', 'z-10');
                s.classList.add('opacity-0', 'z-0');
            }
        });
        aboutDiningDots.forEach((d, i) => {
            d.className = (i === curAboutDiningIdx) ? 'about-dining-dot w-6 h-3 rounded-full bg-[#2b0e14] transition-all' : 'about-dining-dot w-3 h-3 rounded-full bg-slate-300 hover:bg-[#d4a359] transition-all';
        });
    }

    function goToAboutDiningSlide(idx) {
        showAboutDiningSlide(idx);
    }

    // Auto rotate
    setInterval(() => {
        showRoomSlide(curRoomIdx + 1);
        showAboutDiningSlide(curAboutDiningIdx + 1);
    }, 4500);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
