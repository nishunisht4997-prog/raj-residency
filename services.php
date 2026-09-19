<?php
// services.php - Services & Facilities Page for Raj Residency
$pageTitle = "Services & Facilities";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Banner -->
<section class="bg-[#2b0e14] text-white py-14 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-3">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <span class="text-white">SERVICES</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            World-Class Hotel Services
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto font-light">
            Everything you need for a relaxing, productive, and memorable stay at <?php echo htmlspecialchars($settings['hotel_name']); ?>, Koraput.
        </p>
    </div>
</section>

<!-- Services Grid Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Service 1 -->
            <div class="p-8 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 hover:border-[#d4a359] shadow-sm hover:shadow-xl transition duration-300 space-y-4 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl mb-5 shadow-md border border-[#d4a359]/30">
                        <i class="fa-solid fa-bell-concierge"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14]">24/7 Room Service</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light mt-2">
                        Round-the-clock room service delivering delicious food, refreshing beverages, extra beddings, and toiletries with just one phone call from your room intercom.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]">Available: 24 Hours</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 2 -->
            <div class="p-8 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 hover:border-[#d4a359] shadow-sm hover:shadow-xl transition duration-300 space-y-4 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl mb-5 shadow-md border border-[#d4a359]/30">
                        <i class="fa-solid fa-utensils"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14]">In-House Multi-Cuisine Restaurant</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light mt-2">
                        Relish authentic Odia delicacies, North Indian curries, South Indian dosas, and fresh morning buffet breakfasts prepared with fresh local ingredients.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]">Timings: 7:00 AM - 11:00 PM</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 3 -->
            <div class="p-8 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 hover:border-[#d4a359] shadow-sm hover:shadow-xl transition duration-300 space-y-4 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl mb-5 shadow-md border border-[#d4a359]/30">
                        <i class="fa-solid fa-wifi"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14]">High-Speed Fiber Wi-Fi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light mt-2">
                        Complimentary unlimited high-speed fiber internet available in every guest room, banquet area, and lobby for uninterrupted video calls and 4K streaming.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]">Speed: 100+ Mbps Fiber</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 4 -->
            <div class="p-8 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 hover:border-[#d4a359] shadow-sm hover:shadow-xl transition duration-300 space-y-4 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl mb-5 shadow-md border border-[#d4a359]/30">
                        <i class="fa-solid fa-square-parking"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Secure Valet & Open Parking</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light mt-2">
                        Safe, 24-hour CCTV-monitored parking facilities for your cars, bikes, and tourist buses at zero extra charge with full night security guard on duty.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]">24/7 CCTV Monitored</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 5 -->
            <div class="p-8 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 hover:border-[#d4a359] shadow-sm hover:shadow-xl transition duration-300 space-y-4 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl mb-5 shadow-md border border-[#d4a359]/30">
                        <i class="fa-solid fa-taxi"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Travel Desk & Sightseeing Cabs</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light mt-2">
                        Railway station & airport pickup/drop, local Deomali peak trips, Kolab Dam, Duduma, Gupteswar Cave tour packages, and reliable cab booking assistance.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]">Custom Tour Packages</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

            <!-- Service 6 -->
            <div class="p-8 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 hover:border-[#d4a359] shadow-sm hover:shadow-xl transition duration-300 space-y-4 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-2xl mb-5 shadow-md border border-[#d4a359]/30">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14]">24/7 Power & Hot Water Backup</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light mt-2">
                        Heavy-duty soundproof generators and continuous hot/cold water geyser supply ensuring zero interruptions throughout your stay even in monsoon or hill weather.
                    </p>
                </div>
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-[#2b0e14]">100% Uninterrupted Power</span>
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                </div>
            </div>

        </div>

        <!-- Group & Banquet Banner -->
        <div class="mt-16 bg-[#f5efe6] rounded-3xl p-8 sm:p-12 text-center space-y-4 border border-[#d4a359]/30">
            <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">Looking for Special Group Booking or Banquet Services?</h3>
            <p class="text-xs sm:text-sm text-slate-600 max-w-2xl mx-auto font-light">
                Whether you are hosting a family gathering, business seminar, or tourist group in Koraput, we offer custom dining, bulk room discounts, and event management.
            </p>
            <div class="pt-2">
                <a href="contact.php" class="btn-gold px-8 py-3 rounded-full text-xs font-bold uppercase tracking-wider inline-flex items-center space-x-2">
                    <i class="fa-solid fa-phone"></i>
                    <span>Contact Banquet Team</span>
                </a>
            </div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

