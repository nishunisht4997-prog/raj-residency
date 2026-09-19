<?php
// about.php - About Us Page for Raj Residency
$pageTitle = "About Us";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Banner -->
<section class="bg-[#2b0e14] text-white py-14 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-3">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <span class="text-white">ABOUT US</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            Welcome to <?php echo htmlspecialchars($settings['hotel_name']); ?>
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto font-light">
            A boutique luxury destination blending traditional royal Indian warmth with contemporary comforts and unmatched affordability in Koraput, Odisha.
        </p>
    </div>
</section>

<!-- About Story Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-6 space-y-6">
                <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                    <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                    <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                    <span>OUR HERITAGE & PROMISE</span>
                </div>

                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14] leading-tight">
                    Serving Royal Hospitality With Sincere Care & Affordability
                </h2>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
                    Established with the vision of providing every traveler with a majestic and relaxing stay, <strong class="text-[#2b0e14] font-semibold"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong> stands as a landmark of comfort, safety, and hygiene on <strong><?php echo htmlspecialchars($settings['hotel_address']); ?></strong>.
                </p>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
                    From corporate executives needing fast fiber WiFi to families exploring the scenic wonders of Koraput—including Deomali peak, Kolab Reservoir, Gupteswar Cave, and Sabara Srikhetra Jagannath Temple—we cater to every distinct travel need.
                </p>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
                    Our hotel features a balanced selection of ultra-luxury AC suites and sparkling clean budget non-AC rooms, ensuring nobody misses out on royal hospitality.
                </p>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div class="p-5 rounded-2xl bg-[#f5efe6] border border-[#d4a359]/30">
                        <span class="text-2xl sm:text-3xl font-black text-[#2b0e14] block">50+</span>
                        <span class="text-xs font-semibold text-slate-700">Clean & Cozy Rooms</span>
                    </div>
                    <div class="p-5 rounded-2xl bg-[#f5efe6] border border-[#d4a359]/30">
                        <span class="text-2xl sm:text-3xl font-black text-[#2b0e14] block">15,000+</span>
                        <span class="text-xs font-semibold text-slate-700">Delighted Guests</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6">
                <div class="royal-arch-frame relative group">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=900&q=80" alt="<?php echo htmlspecialchars($settings['hotel_name']); ?> Building" class="w-full h-[460px] object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#1c080d]/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6 right-6 p-4 rounded-2xl bg-[#2b0e14]/90 backdrop-blur-md border border-[#d4a359]/40 text-white">
                        <div class="flex items-center space-x-2 text-xs font-bold text-[#f3cf8a] uppercase">
                            <i class="fa-solid fa-crown text-[10px]"></i>
                            <span>RAJ RESIDENCY KORAPUT</span>
                        </div>
                        <p class="text-xs text-slate-300 font-light mt-1">Prime landmark on Post Office Road, Koraput, Odisha</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-20 bg-[#f5efe6] border-y border-[#d4a359]/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                <span>WHY CHOOSE US</span>
                <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
            </div>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#2b0e14]">
                Why Guests Choose <?php echo htmlspecialchars($settings['hotel_name']); ?>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/20 shadow-md hover:border-[#d4a359] transition space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl shadow-md border border-[#d4a359]/30">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Pristine Hygiene</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-light">Fresh linens, sanitized bathrooms, regular pest control, and daily housekeeping guarantee utmost cleanliness.</p>
            </div>

            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/20 shadow-md hover:border-[#d4a359] transition space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl shadow-md border border-[#d4a359]/30">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Honest & Transparent Pricing</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-light">No hidden checkout fees or sudden surcharges. Pay the best direct hotel rate always with zero booking fee.</p>
            </div>

            <div class="p-8 rounded-3xl bg-white border border-[#d4a359]/20 shadow-md hover:border-[#d4a359] transition space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl shadow-md border border-[#d4a359]/30">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Atithi Devo Bhava</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-light">Our attentive team treats every guest with unmatched Indian warmth, care, and respectful assistance.</p>
            </div>
        </div>
    </div>
</section>

<!-- Tourism & Location in Koraput Highlight -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-[#2b0e14] rounded-3xl p-8 sm:p-12 text-white border border-[#d4a359]/40 shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div class="space-y-4">
                    <span class="text-xs font-bold text-[#f3cf8a] tracking-widest uppercase">Prime Koraput Location</span>
                    <h3 class="font-serif text-2xl sm:text-3xl font-bold text-white">Your Gateway to Eastern Ghats & Deomali</h3>
                    <p class="text-xs sm:text-sm text-slate-300 font-light leading-relaxed">
                        <?php echo htmlspecialchars($settings['hotel_name']); ?> is located in central Koraput on Post Office Road, offering quick access to railway station, bus terminal, and popular sightseeing routes including Duduma waterfall, Kolab reservoir, and Gupteswar shrine.
                    </p>
                    <div class="pt-2">
                        <a href="contact.php" class="btn-gold px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider inline-flex items-center space-x-2">
                            <i class="fa-solid fa-map-location-dot"></i>
                            <span>View Location & Directions</span>
                        </a>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="bg-[#1c080d] p-4 rounded-2xl border border-[#d4a359]/20 flex items-center space-x-4">
                        <i class="fa-solid fa-mountain text-[#f3cf8a] text-xl"></i>
                        <div>
                            <strong class="text-sm text-white block">Deomali Peak (Highest Peak of Odisha)</strong>
                            <span class="text-xs text-slate-400">Sightseeing cabs arranged easily</span>
                        </div>
                    </div>
                    <div class="bg-[#1c080d] p-4 rounded-2xl border border-[#d4a359]/20 flex items-center space-x-4">
                        <i class="fa-solid fa-water text-[#f3cf8a] text-xl"></i>
                        <div>
                            <strong class="text-sm text-white block">Kolab Botanical Garden & Dam</strong>
                            <span class="text-xs text-slate-400">Just 15 mins drive from hotel</span>
                        </div>
                    </div>
                    <div class="bg-[#1c080d] p-4 rounded-2xl border border-[#d4a359]/20 flex items-center space-x-4">
                        <i class="fa-solid fa-gopuram text-[#f3cf8a] text-xl"></i>
                        <div>
                            <strong class="text-sm text-white block">Sabara Srikhetra Jagannath Temple</strong>
                            <span class="text-xs text-slate-400">5 mins from Post Office Road</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

