<?php
// includes/footer.php - Footer for Raj Residency
$footerRooms = get_all_rooms(null, true);
$footerRooms = array_slice($footerRooms, 0, 4);
?>
    </main>

    <!-- Imperial Royal Maroon Luxury Footer -->
    <footer class="bg-[#1c080d] text-slate-300 pt-12 sm:pt-16 pb-28 lg:pb-12 border-t border-[#d4a359]/30 relative overflow-hidden">
        <!-- Subtle background glow -->
        <div class="absolute w-96 h-96 rounded-full bg-[#d4a359]/5 blur-3xl -top-20 -left-20 pointer-events-none"></div>
        <div class="absolute w-96 h-96 rounded-full bg-[#d4a359]/5 blur-3xl -bottom-20 -right-20 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-x-5 sm:gap-x-8 gap-y-8 lg:gap-10 mb-10 sm:mb-12">
                
                <!-- Column 1: Brand & About (Full width on mobile) -->
                <div class="col-span-2 md:col-span-1 lg:col-span-1 space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-11 h-11 rounded-full border-2 border-[#d4a359] bg-[#2b0e14] flex flex-col items-center justify-center text-[#f3cf8a] text-xs shadow-md shrink-0">
                            <i class="fa-solid fa-crown text-[9px] text-[#d4a359]"></i>
                            <span class="font-royal text-[10px] font-black text-[#f3cf8a] leading-none">RR</span>
                        </div>
                        <div class="min-w-0">
                            <span class="font-royal tracking-wider text-base sm:text-lg font-bold uppercase text-white block leading-tight truncate"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                            <span class="text-[8px] sm:text-[9px] tracking-[0.2em] text-[#d4a359] uppercase font-semibold block truncate"><?php echo htmlspecialchars($settings['hotel_tagline']); ?></span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed font-light">
                        <?php echo htmlspecialchars($settings['hotel_name']); ?> offers the perfect harmony of authentic Indian hospitality, luxury AC suites, clean budget Non-AC rooms, and world-class dining at affordable rates in Koraput, Odisha.
                    </p>
                    <div class="pt-1 text-xs text-[#f3cf8a] flex items-start space-x-2.5">
                        <i class="fa-solid fa-location-dot mt-0.5 text-[#d4a359] shrink-0"></i>
                        <span class="leading-relaxed"><?php echo htmlspecialchars($settings['hotel_address']); ?></span>
                    </div>
                </div>

                <!-- Column 2: Quick Links (Left Column on mobile) -->
                <div class="col-span-1">
                    <h4 class="font-royal text-xs sm:text-sm font-bold uppercase text-white tracking-wider mb-3.5 sm:mb-5 pb-1.5 sm:pb-2 border-b border-[#d4a359]/30 inline-block">
                        Quick Links
                    </h4>
                    <ul class="space-y-2 sm:space-y-2.5 text-xs">
                        <li><a href="index.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>Home</span></a></li>
                        <li><a href="rooms.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>All Rooms</span></a></li>
                        <li><a href="rooms.php?category=ac" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>AC Deluxe</span></a></li>
                        <li><a href="rooms.php?category=non_ac" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>Budget Non-AC</span></a></li>
                        <li><a href="about.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>About Us</span></a></li>
                        <li><a href="services.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>Services</span></a></li>
                        <li><a href="reviews.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>Reviews</span></a></li>
                        <li><a href="contact.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-1.5"><i class="fa-solid fa-angle-right text-[9px] text-[#d4a359] shrink-0"></i><span>Contact</span></a></li>
                    </ul>
                </div>

                <!-- Column 3: Accommodations (Right Column on mobile next to Quick Links) -->
                <div class="col-span-1">
                    <h4 class="font-royal text-xs sm:text-sm font-bold uppercase text-white tracking-wider mb-3.5 sm:mb-5 pb-1.5 sm:pb-2 border-b border-[#d4a359]/30 inline-block">
                        Accommodations
                    </h4>
                    <ul class="space-y-2.5 sm:space-y-3 text-xs">
                        <?php foreach ($footerRooms as $fr): ?>
                            <li>
                                <a href="room-details.php?id=<?php echo $fr['id']; ?>" class="group block hover:text-[#f3cf8a] transition">
                                    <span class="font-semibold text-white group-hover:text-[#f3cf8a] block truncate text-[11px] sm:text-xs"><?php echo htmlspecialchars($fr['name']); ?></span>
                                    <span class="text-[10px] sm:text-[11px] text-[#d4a359] font-medium block"><?php echo format_price($fr['price_per_night']); ?>/night</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Column 4: Contact & Support (Full width on mobile) -->
                <div class="col-span-2 md:col-span-1 lg:col-span-1 space-y-4">
                    <h4 class="font-royal text-xs sm:text-sm font-bold uppercase text-white tracking-wider mb-3.5 sm:mb-5 pb-1.5 sm:pb-2 border-b border-[#d4a359]/30 inline-block">
                        Contact & Support
                    </h4>
                    <div class="space-y-3 sm:space-y-3.5 text-xs">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-[#2b0e14] border border-[#d4a359]/40 flex items-center justify-center text-[#d4a359] shrink-0 mt-0.5">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <div>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="hover:text-[#f3cf8a] transition font-bold block text-white"><?php echo htmlspecialchars($settings['hotel_phone']); ?></a>
                                <?php if (!empty($settings['hotel_phone_alt'])): ?>
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone_alt']); ?>" class="hover:text-[#f3cf8a] transition text-[11px] text-slate-400 block"><?php echo htmlspecialchars($settings['hotel_phone_alt']); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-[#2b0e14] border border-[#d4a359]/40 flex items-center justify-center text-[#d4a359] shrink-0 mt-0.5">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <a href="mailto:<?php echo htmlspecialchars($settings['hotel_email']); ?>" class="hover:text-[#f3cf8a] transition block text-white break-all"><?php echo htmlspecialchars($settings['hotel_email']); ?></a>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-[#2b0e14] border border-[#d4a359]/40 flex items-center justify-center text-[#d4a359] shrink-0 mt-0.5">
                                <i class="fa-brands fa-whatsapp text-xs"></i>
                            </div>
                            <div>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20I%20want%20to%20inquire%20about%20room%20booking" target="_blank" class="text-[#f3cf8a] hover:underline font-semibold block">WhatsApp Reservation</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Bottom Copyright & Policies -->
            <div class="pt-8 border-t border-slate-800/80 flex flex-col md:flex-row items-center justify-between text-xs text-slate-400 gap-4 text-center md:text-left">
                <div class="space-y-1">
                    <p>&copy; <?php echo date('Y'); ?> <strong class="text-white font-semibold"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong>, Koraput, Odisha. All Rights Reserved.</p>
                    <p class="text-[11px] text-slate-400 font-light">Crafted by <span class="text-[#f3cf8a] font-bold tracking-wide">Geinca</span></p>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-x-4 sm:gap-x-6 gap-y-2 text-[11px]">
                    <a href="contact.php" class="hover:text-[#f3cf8a] transition">Privacy Policy</a>
                    <a href="contact.php" class="hover:text-[#f3cf8a] transition">Terms & Conditions</a>
                    <a href="contact.php" class="hover:text-[#f3cf8a] transition">Cancellation Policy</a>
                    <a href="admin/login.php" class="hover:text-white transition font-bold text-[#f3cf8a] flex items-center space-x-1">
                        <i class="fa-solid fa-lock text-[9px] text-[#d4a359]"></i>
                        <span>Admin Login</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Floating Quick-Action Bottom Bar (Visible on phones & tablets < 1024px) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#1c080d]/95 backdrop-blur-md mobile-bottom-bar border-t border-[#d4a359]/40 px-3 py-2 shadow-2xl">
        <div class="grid grid-cols-3 gap-2 max-w-md mx-auto">
            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl bg-[#2b0e14] text-white hover:text-[#f3cf8a] active:scale-95 border border-[#d4a359]/30 text-center transition shadow-sm">
                <i class="fa-solid fa-phone text-sm text-[#d4a359] mb-0.5"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight">Call Now</span>
            </a>
            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20I%20want%20to%20inquire%20about%20room%20booking" target="_blank" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white active:scale-95 text-center transition shadow-sm border border-emerald-500/40">
                <i class="fa-brands fa-whatsapp text-sm mb-0.5"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight">WhatsApp</span>
            </a>
            <a href="rooms.php" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl btn-gold text-center active:scale-95 transition shadow-sm">
                <i class="fa-solid fa-calendar-check text-sm text-[#1c080d] mb-0.5"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight text-[#1c080d]">Book Room</span>
            </a>
        </div>
    </div>

    <!-- Main JavaScript -->
    <script src="assets/js/main.js?v=3.0"></script>
</body>
</html>
