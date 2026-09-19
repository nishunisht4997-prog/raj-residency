<?php
// room-details.php - Room Details Page for Raj Residency
require_once __DIR__ . '/includes/functions.php';

$roomId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$room = get_room_by_id($roomId);

if (!$room) {
    header('Location: rooms.php');
    exit;
}

$pageTitle = $room['name'];
require_once __DIR__ . '/includes/header.php';

$gallery = !empty($room['gallery']) && is_array($room['gallery']) ? $room['gallery'] : [$room['featured_image']];
?>

<!-- Breadcrumb Banner -->
<section class="bg-[#2b0e14] text-white py-10 border-b border-[#d4a359]/30 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase mb-2">
            <a href="index.php" class="hover:underline">Home</a>
            <span>/</span>
            <a href="rooms.php" class="hover:underline">Rooms</a>
            <span>/</span>
            <span class="text-white"><?php echo htmlspecialchars($room['name']); ?></span>
        </div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#d4a359] block mb-1"><?php echo htmlspecialchars($room['type_label']); ?></span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-white">
                    <?php echo htmlspecialchars($room['name']); ?>
                </h1>
            </div>
            <div class="flex items-center space-x-3 bg-[#1c080d]/80 px-5 py-3 rounded-2xl border border-[#d4a359]/30">
                <span class="text-2xl sm:text-3xl font-black text-[#f3cf8a]"><?php echo format_price($room['price_per_night']); ?></span>
                <span class="text-xs text-slate-300">/ night + taxes</span>
            </div>
        </div>
    </div>
</section>

<!-- Details Main Section -->
<section class="py-12 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left Column: Gallery & Details -->
            <div class="lg:col-span-8 space-y-10">
                
                <!-- Gallery Box -->
                <div class="space-y-4">
                    <div class="h-64 sm:h-80 md:h-[460px] rounded-t-[36px] sm:rounded-t-[48px] rounded-b-2xl sm:rounded-b-3xl overflow-hidden shadow-2xl border-2 border-[#d4a359]/30 bg-[#1c080d]">
                        <img id="main-room-img" src="<?php echo htmlspecialchars($room['featured_image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="w-full h-full object-cover transition duration-300">
                    </div>

                    <!-- Thumbnails -->
                    <?php if (count($gallery) > 1): ?>
                        <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 sm:gap-3">
                            <?php foreach ($gallery as $img): ?>
                                <button type="button" onclick="switchDetailImage('<?php echo htmlspecialchars($img); ?>')" class="h-16 sm:h-20 rounded-xl overflow-hidden border-2 border-slate-200 hover:border-[#d4a359] transition focus:outline-none">
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail" class="w-full h-full object-cover">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Key Specifications Bar -->
                <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-4 sm:p-6 border border-[#d4a359]/20 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-4 sm:gap-6 text-center">
                    <div>
                        <i class="fa-solid fa-users text-lg sm:text-xl text-[#d4a359] mb-1.5 sm:mb-2"></i>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase block">Max Occupancy</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14]"><?php echo (int)$room['max_guests']; ?> Adults</strong>
                    </div>
                    <div>
                        <i class="fa-solid fa-bed text-lg sm:text-xl text-[#d4a359] mb-1.5 sm:mb-2"></i>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase block">Bed Setup</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14]"><?php echo htmlspecialchars($room['bed_type']); ?></strong>
                    </div>
                    <div>
                        <i class="fa-solid fa-vector-square text-lg sm:text-xl text-[#d4a359] mb-1.5 sm:mb-2"></i>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase block">Room Size</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14]"><?php echo htmlspecialchars($room['room_size']); ?></strong>
                    </div>
                    <div>
                        <i class="fa-solid fa-snowflake text-lg sm:text-xl text-[#d4a359] mb-1.5 sm:mb-2"></i>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase block">Cooling</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14]"><?php echo ($room['category'] === 'non_ac') ? 'Ceiling Fan' : 'Air Conditioned'; ?></strong>
                    </div>
                </div>

                <!-- Room Overview -->
                <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-[#d4a359]/20 shadow-sm space-y-3 sm:space-y-4">
                    <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#2b0e14]">Room Overview</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
                        <?php echo htmlspecialchars($room['description']); ?>
                    </p>
                </div>

                <!-- Amenities & Inclusions -->
                <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-[#d4a359]/20 shadow-sm space-y-5 sm:space-y-6">
                    <h3 class="font-serif text-2xl font-bold text-[#2b0e14]">Amenities & Inclusions</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php 
                        $amenities = is_array($room['amenities']) ? $room['amenities'] : [];
                        foreach ($amenities as $am): 
                        ?>
                            <div class="flex items-center space-x-3 p-3 rounded-xl bg-white border border-[#d4a359]/20 shadow-sm">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                <span class="text-xs font-semibold text-slate-700"><?php echo htmlspecialchars($am); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (!empty($room['inclusions'])): ?>
                        <div class="pt-4 border-t border-slate-200/60">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-[#d4a359] mb-3">Free Inclusions with this Room:</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <?php 
                                $inclusions = is_array($room['inclusions']) ? $room['inclusions'] : [];
                                foreach ($inclusions as $inc): 
                                ?>
                                    <div class="flex items-center space-x-2 text-xs text-slate-600">
                                        <i class="fa-solid fa-gift text-[#d4a359] text-xs"></i>
                                        <span><?php echo htmlspecialchars($inc); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Hotel Policies -->
                <div class="bg-[#fffdfa] rounded-3xl p-8 border border-[#d4a359]/20 shadow-sm space-y-4">
                    <h3 class="font-serif text-2xl font-bold text-[#2b0e14]">Hotel Policies</h3>
                    <ul class="space-y-3 text-xs text-slate-600">
                        <li class="flex items-start space-x-2.5">
                            <i class="fa-regular fa-clock text-[#d4a359] mt-0.5"></i>
                            <span><strong>Check-In:</strong> <?php echo htmlspecialchars($settings['check_in_time']); ?> | <strong>Check-Out:</strong> <?php echo htmlspecialchars($settings['check_out_time']); ?></span>
                        </li>
                        <li class="flex items-start space-x-2.5">
                            <i class="fa-solid fa-id-card text-[#d4a359] mt-0.5"></i>
                            <span>Valid government photo ID (Aadhaar, Passport, Driving License) is mandatory for all guests upon check-in.</span>
                        </li>
                        <li class="flex items-start space-x-2.5">
                            <i class="fa-solid fa-shield-halved text-[#d4a359] mt-0.5"></i>
                            <span>Free cancellation up to 24 hours before check-in date. Instant refund or date rescheduling.</span>
                        </li>
                    </ul>
                </div>

            </div>

            <!-- Right Column: Sticky Reservation Sidebar -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-[#d4a359]/40 shadow-2xl sticky top-28 space-y-6">
                    
                    <div class="pb-5 border-b border-slate-100">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Direct Booking Guarantee</span>
                        <div class="flex items-baseline space-x-2 mt-1">
                            <span class="text-3xl font-black text-[#2b0e14]"><?php echo format_price($room['price_per_night']); ?></span>
                            <?php if (!empty($room['discount_price'])): ?>
                                <span class="text-sm line-through text-slate-400"><?php echo format_price($room['discount_price']); ?></span>
                            <?php endif; ?>
                            <span class="text-xs text-slate-500 font-medium">/ night</span>
                        </div>
                    </div>

                    <!-- Direct Booking Form -->
                    <form action="booking.php" method="GET" class="space-y-4">
                        <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                        
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Check-In Date</label>
                            <input type="date" id="check_in" name="check_in" required class="w-full bg-[#fbf9f5] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Check-Out Date</label>
                            <input type="date" id="check_out" name="check_out" required class="w-full bg-[#fbf9f5] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Guests</label>
                            <select name="guests" class="w-full bg-[#fbf9f5] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                                <option value="1">1 Guest</option>
                                <option value="2" selected>2 Guests</option>
                                <?php if ($room['max_guests'] > 2): ?>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full btn-gold py-3.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>Reserve This Room</span>
                            </button>
                        </div>
                    </form>

                    <!-- Contact Front Desk directly -->
                    <div class="pt-4 border-t border-slate-100 text-center space-y-3">
                        <span class="text-xs text-slate-500 block">Or call directly for quick inquiry:</span>
                        <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="text-sm font-bold text-[#2b0e14] hover:text-[#b88738] transition block">
                            <i class="fa-solid fa-phone-volume text-[#d4a359] mr-1.5"></i>
                            <span><?php echo htmlspecialchars($settings['hotel_phone']); ?></span>
                        </a>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20I%20am%20interested%20in%20booking%20<?php echo urlencode($room['name']); ?>" target="_blank" class="text-xs font-bold text-emerald-600 hover:underline flex items-center justify-center space-x-1.5">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Chat on WhatsApp</span>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

