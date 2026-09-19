<?php
// includes/footer.php - Footer for Raj Residency
$footerRooms = get_all_rooms(null, true);
$footerRooms = array_slice($footerRooms, 0, 4);
?>
    </main>

    <!-- Imperial Royal Maroon Luxury Footer -->
    <footer class="bg-[#1c080d] text-slate-300 pt-16 pb-12 border-t border-[#d4a359]/30 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
                
                <!-- Column 1: Brand & About -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-11 h-11 rounded-full border border-[#d4a359] bg-[#2b0e14] flex flex-col items-center justify-center text-[#f3cf8a] text-xs shadow-md">
                            <i class="fa-solid fa-crown text-[9px] text-[#d4a359]"></i>
                            <span class="font-royal text-[10px] font-black text-[#f3cf8a] leading-none">RR</span>
                        </div>
                        <div>
                            <span class="font-royal tracking-widest text-lg font-bold uppercase text-white block leading-none"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                            <span class="text-[8px] tracking-[0.25em] text-[#d4a359] uppercase font-semibold"><?php echo htmlspecialchars($settings['hotel_tagline']); ?></span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed font-light">
                        <?php echo htmlspecialchars($settings['hotel_name']); ?> offers the perfect harmony of authentic Indian hospitality, luxury AC suites, clean budget Non-AC rooms, and world-class dining at affordable rates in Koraput, Odisha.
                    </p>
                    <div class="pt-2 text-xs text-[#f3cf8a] flex items-start space-x-2.5">
                        <i class="fa-solid fa-location-dot mt-0.5 text-[#d4a359]"></i>
                        <span><?php echo htmlspecialchars($settings['hotel_address']); ?></span>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div>
                    <h4 class="font-royal text-sm font-bold uppercase text-white tracking-wider mb-5 pb-2 border-b border-[#d4a359]/30 inline-block">
                        Quick Links
                    </h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><a href="index.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>Home</span></a></li>
                        <li><a href="rooms.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>All Rooms & Suites</span></a></li>
                        <li><a href="rooms.php?category=ac" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>AC Deluxe Rooms</span></a></li>
                        <li><a href="rooms.php?category=non_ac" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>Budget Non-AC Rooms</span></a></li>
                        <li><a href="about.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>About Our Hotel</span></a></li>
                        <li><a href="services.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>Hotel Services</span></a></li>
                        <li><a href="reviews.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>Guest Reviews & Ratings</span></a></li>
                        <li><a href="contact.php" class="hover:text-[#f3cf8a] transition flex items-center space-x-2"><i class="fa-solid fa-angle-right text-[10px] text-[#d4a359]"></i><span>Contact Us</span></a></li>
                    </ul>
                </div>

                <!-- Column 3: Accommodations -->
                <div>
                    <h4 class="font-royal text-sm font-bold uppercase text-white tracking-wider mb-5 pb-2 border-b border-[#d4a359]/30 inline-block">
                        Accommodations
                    </h4>
                    <ul class="space-y-3 text-xs">
                        <?php foreach ($footerRooms as $fr): ?>
                            <li>
                                <a href="room-details.php?id=<?php echo $fr['id']; ?>" class="group block hover:text-[#f3cf8a] transition">
                                    <span class="font-semibold text-white group-hover:text-[#f3cf8a] block"><?php echo htmlspecialchars($fr['name']); ?></span>
                                    <span class="text-[11px] text-[#d4a359]"><?php echo format_price($fr['price_per_night']); ?>/night</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Column 4: Contact & Support -->
                <div class="space-y-4">
                    <h4 class="font-royal text-sm font-bold uppercase text-white tracking-wider mb-5 pb-2 border-b border-[#d4a359]/30 inline-block">
                        Contact & Support
                    </h4>
                    <div class="space-y-3 text-xs">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-[#2b0e14] border border-[#d4a359]/40 flex items-center justify-center text-[#d4a359]">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <div>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="hover:text-[#f3cf8a] transition font-bold block text-white"><?php echo htmlspecialchars($settings['hotel_phone']); ?></a>
                                <?php if (!empty($settings['hotel_phone_alt'])): ?>
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone_alt']); ?>" class="hover:text-[#f3cf8a] transition text-[11px] text-slate-400 block"><?php echo htmlspecialchars($settings['hotel_phone_alt']); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-[#2b0e14] border border-[#d4a359]/40 flex items-center justify-center text-[#d4a359]">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </div>
                            <div>
                                <a href="mailto:<?php echo htmlspecialchars($settings['hotel_email']); ?>" class="hover:text-[#f3cf8a] transition block text-white"><?php echo htmlspecialchars($settings['hotel_email']); ?></a>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-[#2b0e14] border border-[#d4a359]/40 flex items-center justify-center text-[#d4a359]">
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
            <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4 text-center sm:text-left">
                <p>&copy; <?php echo date('Y'); ?> <strong class="text-white font-semibold"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong>, Koraput, Odisha. All Rights Reserved.</p>
                <div class="flex flex-wrap items-center justify-center space-x-4 sm:space-x-6 text-[11px]">
                    <a href="contact.php" class="hover:text-[#f3cf8a] transition">Privacy Policy</a>
                    <a href="contact.php" class="hover:text-[#f3cf8a] transition">Terms & Conditions</a>
                    <a href="contact.php" class="hover:text-[#f3cf8a] transition">Cancellation Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile Floating Quick-Action Bottom Bar (Visible on phones & tablets < 1024px) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#1c080d]/95 mobile-bottom-bar border-t border-[#d4a359]/40 px-3 py-2">
        <div class="grid grid-cols-3 gap-2 max-w-md mx-auto">
            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl bg-[#2b0e14] text-white hover:text-[#f3cf8a] border border-[#d4a359]/30 text-center transition">
                <i class="fa-solid fa-phone text-sm text-[#d4a359] mb-0.5"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight">Call Now</span>
            </a>
            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20I%20want%20to%20inquire%20about%20room%20booking" target="_blank" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl bg-emerald-700/90 text-white text-center transition shadow-sm border border-emerald-500/40">
                <i class="fa-brands fa-whatsapp text-sm mb-0.5"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight">WhatsApp</span>
            </a>
            <a href="rooms.php" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl btn-gold text-center transition shadow-sm">
                <i class="fa-solid fa-calendar-check text-sm text-[#1c080d] mb-0.5"></i>
                <span class="text-[10px] font-bold uppercase tracking-tight text-[#1c080d]">Book Room</span>
            </a>
        </div>
    </div>

    <!-- Main JavaScript -->
    <script src="assets/js/main.js?v=3.0"></script>
</body>
</html>
