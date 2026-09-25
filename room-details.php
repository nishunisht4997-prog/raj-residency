<?php
// room-details.php - Ultra-Luxury Room Details Showcase for Raj Residency
require_once __DIR__ . '/includes/functions.php';

$roomId = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$room = get_room_by_id($roomId);

if (!$room) {
    header('Location: rooms.php');
    exit;
}

$pageTitle = $room['name'] . " - Luxury Accommodation";
require_once __DIR__ . '/includes/header.php';

// Build gallery array ensuring featured image is first
$gallery = !empty($room['gallery']) && is_array($room['gallery']) ? $room['gallery'] : [];
if (!in_array($room['featured_image'], $gallery) && !empty($room['featured_image'])) {
    array_unshift($gallery, $room['featured_image']);
}
if (empty($gallery)) {
    $gallery = ['https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80'];
}
$gallery = array_values($gallery);

// Category Badges & Metadata
$badgeClass = 'badge-ac';
$badgeIcon = 'fa-solid fa-snowflake';
$badgeText = 'Air Conditioned Deluxe';
if ($room['category'] === 'non_ac') {
    $badgeClass = 'badge-non-ac';
    $badgeIcon = 'fa-solid fa-wind';
    $badgeText = 'Sparkling Clean Non-AC';
} elseif ($room['category'] === 'suite') {
    $badgeClass = 'badge-suite';
    $badgeIcon = 'fa-solid fa-crown';
    $badgeText = 'Royal Executive Suite';
}

// Fetch 3 Similar / Recommended Rooms
$allOtherRooms = get_all_rooms(null, true);
$similarRooms = array_filter($allOtherRooms, function($r) use ($roomId) {
    return (int)$r['id'] !== $roomId;
});
$similarRooms = array_slice(array_values($similarRooms), 0, 3);
?>

<!-- 1. LUXURY BREADCRUMB & ROOM BANNER -->
<section class="bg-[#2b0e14] text-white py-12 lg:py-16 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- Breadcrumb -->
        <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase mb-4">
            <a href="index.php" class="hover:underline">Home</a>
            <span>/</span>
            <a href="rooms.php" class="hover:underline">Rooms & Suites</a>
            <span>/</span>
            <span class="text-white"><?php echo htmlspecialchars($room['name']); ?></span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="<?php echo $badgeClass; ?> text-xs py-1 px-3">
                        <i class="<?php echo $badgeIcon; ?> text-[10px]"></i>
                        <span><?php echo $badgeText; ?></span>
                    </span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-[#3d141d] text-[#f3cf8a] border border-[#d4a359]/30 inline-flex items-center space-x-1">
                        <i class="fa-solid fa-location-dot text-[9px] text-[#d4a359]"></i>
                        <span>Post Office Road, Koraput</span>
                    </span>
                </div>

                <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">
                    <?php echo htmlspecialchars($room['name']); ?>
                </h1>

                <div class="flex items-center space-x-3 text-xs text-slate-300 pt-1">
                    <div class="flex items-center text-[#d4a359] text-xs">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <span class="font-bold text-white">4.9 / 5</span>
                    <span class="text-slate-400">• 120+ Verified Guest Ratings</span>
                </div>
            </div>

            <!-- Price & Guarantee Pill -->
            <div class="bg-[#1c080d]/90 p-5 rounded-3xl border border-[#d4a359]/40 shadow-2xl flex items-center space-x-5 shrink-0">
                <div>
                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">DIRECT RATE</span>
                    <div class="flex items-baseline space-x-1.5">
                        <span class="text-3xl sm:text-4xl font-black text-[#f3cf8a]"><?php echo format_price($room['price_per_night']); ?></span>
                        <span class="text-xs text-slate-400 font-medium">/ night</span>
                    </div>
                    <?php if (!empty($room['discount_price'])): ?>
                        <span class="text-[11px] text-slate-400 line-through">Standard: <?php echo format_price($room['discount_price']); ?></span>
                    <?php endif; ?>
                </div>
                <div class="h-12 w-[1px] bg-[#d4a359]/30"></div>
                <div class="space-y-1 text-[11px]">
                    <span class="text-emerald-400 font-bold flex items-center space-x-1"><i class="fa-solid fa-circle-check text-[10px]"></i><span>Free Cancellation</span></span>
                    <span class="text-[#f3cf8a] font-bold flex items-center space-x-1"><i class="fa-solid fa-shield-halved text-[10px]"></i><span>Zero Booking Fee</span></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Background glow decorative circles -->
    <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
</section>


<!-- 2. MAIN CONTENT GRID: GALLERY + DETAILS + STICKY PRICE CALCULATOR -->
<section class="py-12 sm:py-16 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- LEFT COLUMN (8 COLS): GALLERY, SPECS, OVERVIEW, AMENITIES, POLICIES -->
            <div class="lg:col-span-8 space-y-10">
                
                <!-- A. INTERACTIVE MULTI-PHOTO GALLERY & LIGHTBOX -->
                <div class="space-y-4">
                    
                    <!-- Main High-Res Image Frame -->
                    <div class="royal-arch-frame relative h-72 sm:h-96 md:h-[480px] rounded-t-[40px] rounded-b-2xl sm:rounded-b-3xl overflow-hidden shadow-2xl border-4 border-white bg-[#1c080d] group">
                        <img id="main-room-img" 
                             src="<?php echo htmlspecialchars($gallery[0]); ?>" 
                             alt="<?php echo htmlspecialchars($room['name']); ?>" 
                             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80';"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-700 cursor-pointer"
                             onclick="openRoomLightbox(currentGalleryIndex)">
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#1c080d]/80 via-transparent to-transparent pointer-events-none"></div>

                        <!-- Top Right Fullscreen Button -->
                        <button type="button" 
                                onclick="openRoomLightbox(currentGalleryIndex)" 
                                class="absolute top-4 right-4 bg-[#2b0e14]/90 hover:bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/50 px-3.5 py-2 rounded-2xl text-xs font-bold shadow-xl flex items-center space-x-2 backdrop-blur-md transition group-hover:scale-105">
                            <i class="fa-solid fa-expand text-xs"></i>
                            <span>View Fullscreen (<?php echo count($gallery); ?> Photos)</span>
                        </button>

                        <!-- Bottom Left Caption -->
                        <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-white pointer-events-none">
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-[#2b0e14]/90 text-[#f3cf8a] border border-[#d4a359]/40 backdrop-blur-md">
                                    <i class="fa-solid fa-crown text-[9px] mr-1"></i> Raj Residency Koraput
                                </span>
                                <span id="gallery-counter-badge" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-black/60 text-white backdrop-blur-md">
                                    Photo 1 of <?php echo count($gallery); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnails Strip Carousel -->
                    <?php if (count($gallery) > 1): ?>
                        <div class="flex items-center gap-2 sm:gap-3 overflow-x-auto pb-2 scrollbar-thin" id="thumbnails-container">
                            <?php foreach ($gallery as $gIdx => $gImg): ?>
                                <button type="button" 
                                        onclick="switchDetailImage('<?php echo htmlspecialchars($gImg); ?>', <?php echo $gIdx; ?>)" 
                                        id="thumb-btn-<?php echo $gIdx; ?>"
                                        class="thumb-btn relative h-16 sm:h-20 w-24 sm:w-28 shrink-0 rounded-2xl overflow-hidden border-2 transition duration-300 focus:outline-none <?php echo $gIdx === 0 ? 'border-[#b88738] shadow-md ring-2 ring-[#d4a359]/50 scale-95' : 'border-slate-200 hover:border-[#d4a359] opacity-80 hover:opacity-100'; ?>">
                                    <img src="<?php echo htmlspecialchars($gImg); ?>" alt="Thumbnail <?php echo $gIdx + 1; ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=400&q=80';" class="w-full h-full object-cover">
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>


                <!-- B. KEY SPECIFICATIONS GRID -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ebd9c8] shadow-md grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
                    
                    <div class="space-y-1.5 p-3 rounded-2xl bg-[#fbf9f5] border border-[#f0e4d6]">
                        <div class="w-10 h-10 rounded-xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-base mx-auto shadow-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-wider block">Occupancy</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14] block">Max <?php echo (int)$room['max_guests']; ?> Adults</strong>
                    </div>

                    <div class="space-y-1.5 p-3 rounded-2xl bg-[#fbf9f5] border border-[#f0e4d6]">
                        <div class="w-10 h-10 rounded-xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-base mx-auto shadow-sm">
                            <i class="fa-solid fa-bed"></i>
                        </div>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-wider block">Bedding</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14] block"><?php echo htmlspecialchars($room['bed_type']); ?></strong>
                    </div>

                    <div class="space-y-1.5 p-3 rounded-2xl bg-[#fbf9f5] border border-[#f0e4d6]">
                        <div class="w-10 h-10 rounded-xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-base mx-auto shadow-sm">
                            <i class="fa-solid fa-vector-square"></i>
                        </div>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-wider block">Room Area</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14] block"><?php echo htmlspecialchars($room['room_size']); ?></strong>
                    </div>

                    <div class="space-y-1.5 p-3 rounded-2xl bg-[#fbf9f5] border border-[#f0e4d6]">
                        <div class="w-10 h-10 rounded-xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-base mx-auto shadow-sm">
                            <i class="fa-solid fa-temperature-arrow-down"></i>
                        </div>
                        <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-wider block">Climate</span>
                        <strong class="text-xs sm:text-sm text-[#2b0e14] block"><?php echo ($room['category'] === 'non_ac') ? 'Air Circulated Fan' : 'Super Silent AC'; ?></strong>
                    </div>

                </div>


                <!-- C. ROOM OVERVIEW & DESCRIPTION -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ebd9c8] shadow-md space-y-4">
                    <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                        <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                        <i class="fa-solid fa-crown text-[10px]"></i>
                        <span>ROOM OVERVIEW & EXPERIENCE</span>
                    </div>

                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">
                        About this Accommodation
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-light">
                        <?php echo nl2br(htmlspecialchars($room['description'])); ?>
                    </p>

                    <!-- Trust Highlights Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <div class="flex items-center space-x-2.5 p-3 rounded-xl bg-[#f5efe6] border border-[#ebd9c8]">
                            <i class="fa-solid fa-shield-heart text-emerald-700 text-sm"></i>
                            <span class="text-xs font-semibold text-[#2b0e14]">100% Sanitized Linen</span>
                        </div>
                        <div class="flex items-center space-x-2.5 p-3 rounded-xl bg-[#f5efe6] border border-[#ebd9c8]">
                            <i class="fa-solid fa-bolt text-amber-600 text-sm"></i>
                            <span class="text-xs font-semibold text-[#2b0e14]">24/7 Power & Geyser Backup</span>
                        </div>
                        <div class="flex items-center space-x-2.5 p-3 rounded-xl bg-[#f5efe6] border border-[#ebd9c8]">
                            <i class="fa-solid fa-wifi text-blue-600 text-sm"></i>
                            <span class="text-xs font-semibold text-[#2b0e14]">High-Speed Optical Wi-Fi</span>
                        </div>
                    </div>
                </div>


                <!-- D. LUXURY AMENITIES & INCLUSIONS -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ebd9c8] shadow-md space-y-6">
                    <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                        <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                        <i class="fa-solid fa-sparkles text-[10px]"></i>
                        <span>COMFORTS & CONVENIENCES</span>
                    </div>

                    <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">
                        Amenities Included in this Room
                    </h3>

                    <!-- Amenities Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <?php 
                        $amenities = is_array($room['amenities']) ? $room['amenities'] : [];
                        
                        // Smart Icon Assigner for Amenities
                        function getAmenityIcon($name) {
                            $name = strtolower($name);
                            if (strpos($name, 'wi-fi') !== false || strpos($name, 'wifi') !== false) return 'fa-solid fa-wifi text-blue-600';
                            if (strpos($name, 'tv') !== false || strpos($name, 'led') !== false) return 'fa-solid fa-tv text-indigo-600';
                            if (strpos($name, 'ac') !== false || strpos($name, 'condition') !== false) return 'fa-solid fa-snowflake text-sky-600';
                            if (strpos($name, 'hot water') !== false || strpos($name, 'geyser') !== false) return 'fa-solid fa-shower text-amber-600';
                            if (strpos($name, 'service') !== false || strpos($name, 'dining') !== false) return 'fa-solid fa-bell-concierge text-rose-600';
                            if (strpos($name, 'power') !== false || strpos($name, 'generator') !== false) return 'fa-solid fa-bolt text-amber-500';
                            if (strpos($name, 'wardrobe') !== false || strpos($name, 'closet') !== false) return 'fa-solid fa-door-closed text-slate-600';
                            if (strpos($name, 'kettle') !== false || strpos($name, 'tea') !== false) return 'fa-solid fa-mug-hot text-emerald-600';
                            if (strpos($name, 'desk') !== false || strpos($name, 'chair') !== false) return 'fa-solid fa-chair text-amber-800';
                            return 'fa-solid fa-circle-check text-emerald-600';
                        }

                        foreach ($amenities as $am): 
                            $iconClass = getAmenityIcon($am);
                        ?>
                            <div class="flex items-center space-x-3 p-3.5 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8] hover:border-[#d4a359] transition">
                                <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center shadow-sm shrink-0">
                                    <i class="<?php echo $iconClass; ?> text-sm"></i>
                                </div>
                                <span class="text-xs font-bold text-slate-800"><?php echo htmlspecialchars($am); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Free Inclusions -->
                    <?php if (!empty($room['inclusions'])): ?>
                        <div class="pt-6 border-t border-slate-100">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-[#b88738] block mb-3">
                                <i class="fa-solid fa-gift mr-1 text-[#d4a359]"></i> Special Complimentary Inclusions:
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <?php 
                                $inclusions = is_array($room['inclusions']) ? $room['inclusions'] : [];
                                foreach ($inclusions as $inc): 
                                ?>
                                    <div class="flex items-center space-x-2 text-xs text-slate-700 bg-[#f5efe6]/70 p-2.5 rounded-xl border border-[#ebd9c8]">
                                        <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                                        <span class="font-medium"><?php echo htmlspecialchars($inc); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>


                <!-- E. HOUSE RULES & POLICIES -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ebd9c8] shadow-md space-y-5">
                    <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                        <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                        <i class="fa-solid fa-shield-halved text-[10px]"></i>
                        <span>HOTEL RULES & POLICIES</span>
                    </div>

                    <h3 class="font-serif text-2xl font-bold text-[#2b0e14]">
                        Stay Policies & Important Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8] space-y-1">
                            <div class="flex items-center space-x-2 text-xs font-bold text-[#2b0e14]">
                                <i class="fa-regular fa-clock text-[#d4a359]"></i>
                                <span>Check-In & Check-Out</span>
                            </div>
                            <p class="text-[11px] text-slate-600">Check-In: <strong><?php echo htmlspecialchars($settings['check_in_time'] ?? '12:00 PM'); ?></strong></p>
                            <p class="text-[11px] text-slate-600">Check-Out: <strong><?php echo htmlspecialchars($settings['check_out_time'] ?? '11:00 AM'); ?></strong></p>
                            <span class="text-[10px] text-slate-400">Early check-in subject to room availability</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8] space-y-1">
                            <div class="flex items-center space-x-2 text-xs font-bold text-[#2b0e14]">
                                <i class="fa-solid fa-id-card text-[#d4a359]"></i>
                                <span>Government Photo ID</span>
                            </div>
                            <p class="text-[11px] text-slate-600">Aadhaar, Driving License, or Passport is mandatory for each adult guest at reception.</p>
                            <span class="text-[10px] text-emerald-700 font-semibold">Strict safety & hotel compliance</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8] space-y-1">
                            <div class="flex items-center space-x-2 text-xs font-bold text-[#2b0e14]">
                                <i class="fa-solid fa-rotate-left text-[#d4a359]"></i>
                                <span>Free Cancellation Guarantee</span>
                            </div>
                            <p class="text-[11px] text-slate-600">Cancel or reschedule up to 24 hours before check-in date without penalty.</p>
                            <span class="text-[10px] text-emerald-700 font-semibold">100% stress-free reservations</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8] space-y-1">
                            <div class="flex items-center space-x-2 text-xs font-bold text-[#2b0e14]">
                                <i class="fa-solid fa-ban-smoking text-[#d4a359]"></i>
                                <span>Clean Air & Family Atmosphere</span>
                            </div>
                            <p class="text-[11px] text-slate-600">Smoking is strictly prohibited inside bedrooms. Dedicated outdoor smoking zones available.</p>
                            <span class="text-[10px] text-slate-500">24/7 CCTV & Security</span>
                        </div>
                    </div>
                </div>

            </div>


            <!-- RIGHT COLUMN (4 COLS): STICKY INSTANT PRICE CALCULATOR & BOOKING HUB -->
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    
                    <!-- CALCULATOR CARD -->
                    <div class="royal-card p-6 sm:p-7 border-2 border-[#d4a359]/60 shadow-2xl bg-white relative overflow-hidden">
                        
                        <!-- Top Accent Banner -->
                        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-[#d4a359] via-[#f3cf8a] to-[#b88738]"></div>

                        <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Direct Website Rate</span>
                                <div class="flex items-baseline space-x-1.5 mt-0.5">
                                    <span class="text-3xl font-black text-[#2b0e14]" id="rate-display"><?php echo format_price($room['price_per_night']); ?></span>
                                    <span class="text-xs text-slate-500 font-medium">/ night</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                🟢 Best Rate
                            </span>
                        </div>

                        <!-- LIVE INTERACTIVE FORM -->
                        <form action="booking.php" method="GET" id="calc-form" class="space-y-4 pt-3">
                            <input type="hidden" name="room_id" value="<?php echo $room['id']; ?>">
                            
                            <!-- Check-In Date -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1 flex items-center justify-between">
                                    <span>Check-In Date</span>
                                    <i class="fa-regular fa-calendar text-[#d4a359]"></i>
                                </label>
                                <input type="date" 
                                       id="calc_check_in" 
                                       name="check_in" 
                                       required 
                                       onchange="calculateStayBill()"
                                       class="w-full bg-[#fbf9f5] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359] transition">
                            </div>

                            <!-- Check-Out Date -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1 flex items-center justify-between">
                                    <span>Check-Out Date</span>
                                    <i class="fa-regular fa-calendar-check text-[#d4a359]"></i>
                                </label>
                                <input type="date" 
                                       id="calc_check_out" 
                                       name="check_out" 
                                       required 
                                       onchange="calculateStayBill()"
                                       class="w-full bg-[#fbf9f5] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359] transition">
                            </div>

                            <!-- Guests Selector -->
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1 flex items-center justify-between">
                                    <span>Adult Guests</span>
                                    <i class="fa-solid fa-user-group text-[#d4a359]"></i>
                                </label>
                                <select name="guests" 
                                        id="calc_guests" 
                                        onchange="calculateStayBill()"
                                        class="w-full bg-[#fbf9f5] border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                                    <option value="1">1 Guest</option>
                                    <option value="2" selected>2 Guests (Standard)</option>
                                    <?php if ($room['max_guests'] > 2): ?>
                                        <option value="3">3 Guests</option>
                                        <option value="4">4 Guests (Family)</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- DYNAMIC LIVE BILL BREAKDOWN BOX -->
                            <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] space-y-2.5" id="bill-breakdown-box">
                                <div class="flex items-center justify-between text-xs text-slate-600">
                                    <span id="nights-label">Duration of Stay:</span>
                                    <strong class="text-[#2b0e14]" id="nights-value">1 Night</strong>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-600">
                                    <span id="calculation-formula">Base Rate (₹<?php echo number_format($room['price_per_night'], 0); ?> × 1):</span>
                                    <strong class="text-[#2b0e14]" id="subtotal-value">₹<?php echo number_format($room['price_per_night'], 0); ?></strong>
                                </div>

                                <div class="flex items-center justify-between text-xs text-slate-600">
                                    <span>Hotel Taxes & Service:</span>
                                    <span class="text-emerald-700 font-semibold text-[11px]">Included / Zero Fees</span>
                                </div>

                                <div class="pt-2 border-t border-[#ebd9c8] flex items-center justify-between">
                                    <div>
                                        <span class="text-[11px] font-black uppercase text-[#2b0e14] block">Estimated Total</span>
                                        <span class="text-[9px] text-slate-500 font-medium">Pay Online or At Hotel</span>
                                    </div>
                                    <div class="text-xl sm:text-2xl font-black text-[#2b0e14]" id="grand-total-value">
                                        ₹<?php echo number_format($room['price_per_night'], 0); ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Primary Booking CTA Button -->
                            <button type="submit" class="w-full btn-gold py-3.5 px-4 rounded-xl text-xs font-bold uppercase tracking-wider shadow-xl flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-calendar-check text-sm"></i>
                                <span>Reserve This Room Now</span>
                            </button>
                        </form>

                        <!-- Direct WhatsApp and Call Hub -->
                        <div class="pt-5 border-t border-slate-100 space-y-2.5">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block text-center">Instant Desk Support</span>
                            
                            <a id="whatsapp-inquiry-btn" 
                               href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20I%20want%20to%20inquire%20about%20booking%20<?php echo urlencode($room['name']); ?>" 
                               target="_blank" 
                               class="w-full py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-800 hover:text-white border border-emerald-200 text-xs font-bold transition flex items-center justify-center space-x-2">
                                <i class="fa-brands fa-whatsapp text-sm text-emerald-600 hover:text-white"></i>
                                <span>Inquire on WhatsApp</span>
                            </a>

                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" 
                               class="w-full py-2.5 rounded-xl bg-[#f5efe6] hover:bg-[#2b0e14] text-[#2b0e14] hover:text-[#f3cf8a] border border-[#ebd9c8] text-xs font-bold transition flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-phone text-xs text-[#d4a359]"></i>
                                <span>Call: <?php echo htmlspecialchars($settings['hotel_phone']); ?></span>
                            </a>
                        </div>

                    </div>

                    <!-- Direct Booking Perks Pill -->
                    <div class="p-4 rounded-2xl bg-white border border-[#ebd9c8] shadow-sm space-y-2 text-[11px] text-slate-600">
                        <div class="flex items-center space-x-2 text-[#2b0e14] font-bold">
                            <i class="fa-solid fa-medal text-[#d4a359]"></i>
                            <span>Why Book Directly With Us?</span>
                        </div>
                        <ul class="space-y-1 text-[10px] text-slate-500 pl-4 list-disc">
                            <li>Guaranteed lowest rate with zero commission</li>
                            <li>Free room category upgrade (subject to availability)</li>
                            <li>Instant WhatsApp confirmation receipt</li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>


<!-- 3. SIMILAR & RECOMMENDED ROOMS SECTION -->
<?php if (!empty($similarRooms)): ?>
<section class="py-16 bg-white border-t border-[#ebd9c8]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase mb-1">
                    <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                    <i class="fa-solid fa-bed text-[10px]"></i>
                    <span>EXPLORE MORE ROOMS</span>
                </div>
                <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">
                    Similar Accommodations at Raj Residency
                </h3>
            </div>
            <a href="rooms.php" class="text-xs font-bold text-[#b88738] hover:text-[#2b0e14] flex items-center space-x-1 uppercase tracking-wider">
                <span>View All Categories</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php 
            foreach ($similarRooms as $simRoom): 
                $room = $simRoom; // for include
            ?>
                <div class="h-full">
                    <?php include __DIR__ . '/includes/room_card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>
<?php endif; ?>


<!-- 4. FULLSCREEN ROYAL PHOTO LIGHTBOX MODAL -->
<div id="room-lightbox-modal" class="fixed inset-0 z-50 bg-black/95 backdrop-blur-md hidden flex flex-col justify-between p-4 sm:p-6 transition duration-300">
    
    <!-- Lightbox Header -->
    <div class="flex items-center justify-between text-white z-20">
        <div class="flex items-center space-x-3">
            <span class="text-xs font-bold text-[#f3cf8a] uppercase tracking-wider">
                <i class="fa-solid fa-crown text-[#d4a359] mr-1.5"></i> <?php echo htmlspecialchars($room['name'] ?? 'Room Photo'); ?>
            </span>
            <span id="lb-image-counter" class="px-3 py-1 rounded-full text-[10px] font-bold bg-white/10 text-slate-300">
                1 / <?php echo count($gallery); ?>
            </span>
        </div>
        <button type="button" onclick="closeRoomLightbox()" class="w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg transition focus:outline-none">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Lightbox Main Image & Arrows Container -->
    <div class="relative flex-grow flex items-center justify-center my-4 overflow-hidden">
        
        <!-- Prev Arrow -->
        <button type="button" 
                onclick="prevLightboxImage()" 
                class="absolute left-2 sm:left-6 z-20 w-12 h-12 rounded-full bg-black/60 hover:bg-[#d4a359] text-white hover:text-[#2b0e14] border border-white/20 flex items-center justify-center text-lg transition focus:outline-none">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <!-- Center Active Image -->
        <div class="max-w-4xl max-h-[75vh] p-2 flex items-center justify-center">
            <img id="lb-active-image" 
                 src="<?php echo htmlspecialchars($gallery[0]); ?>" 
                 alt="Full Room Image" 
                 class="max-w-full max-h-[72vh] object-contain rounded-2xl shadow-2xl transition duration-300">
        </div>

        <!-- Next Arrow -->
        <button type="button" 
                onclick="nextLightboxImage()" 
                class="absolute right-2 sm:right-6 z-20 w-12 h-12 rounded-full bg-black/60 hover:bg-[#d4a359] text-white hover:text-[#2b0e14] border border-white/20 flex items-center justify-center text-lg transition focus:outline-none">
            <i class="fa-solid fa-chevron-right"></i>
        </button>
    </div>

    <!-- Lightbox Bottom Thumbnails Strip -->
    <div class="flex items-center justify-center gap-2 overflow-x-auto pb-2 z-20">
        <?php foreach ($gallery as $lIdx => $lImg): ?>
            <button type="button" 
                    onclick="setLightboxImage(<?php echo $lIdx; ?>)" 
                    id="lb-thumb-<?php echo $lIdx; ?>" 
                    class="h-12 w-16 sm:h-14 sm:w-20 rounded-lg overflow-hidden border-2 transition focus:outline-none shrink-0 <?php echo $lIdx === 0 ? 'border-[#f3cf8a] scale-105' : 'border-white/20 opacity-50 hover:opacity-100'; ?>">
                <img src="<?php echo htmlspecialchars($lImg); ?>" alt="Thumb <?php echo $lIdx + 1; ?>" class="w-full h-full object-cover">
            </button>
        <?php endforeach; ?>
    </div>

</div>


<!-- 5. JAVASCRIPT LOGIC: GALLERY SWITCHER, LIGHTBOX & PRICE CALCULATOR -->
<script>
    // Gallery Data
    const roomGallery = <?php echo json_encode($gallery); ?>;
    const roomBasePrice = <?php echo (float)$room['price_per_night']; ?>;
    const roomName = <?php echo json_encode($room['name']); ?>;
    const hotelWhatsApp = <?php echo json_encode(preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp'])); ?>;
    
    let currentGalleryIndex = 0;

    // 1. Thumbnail Switcher
    function switchDetailImage(imageSrc, index) {
        currentGalleryIndex = index;
        const mainImg = document.getElementById('main-room-img');
        const counterBadge = document.getElementById('gallery-counter-badge');
        
        if (mainImg) {
            mainImg.style.opacity = '0.3';
            setTimeout(() => {
                mainImg.src = imageSrc;
                mainImg.style.opacity = '1';
            }, 150);
        }

        if (counterBadge) {
            counterBadge.textContent = `Photo ${index + 1} of ${roomGallery.length}`;
        }

        // Update thumbnail borders
        document.querySelectorAll('.thumb-btn').forEach((btn, idx) => {
            if (idx === index) {
                btn.className = 'thumb-btn relative h-16 sm:h-20 w-24 sm:w-28 shrink-0 rounded-2xl overflow-hidden border-2 border-[#b88738] shadow-md ring-2 ring-[#d4a359]/50 scale-95 transition duration-300';
            } else {
                btn.className = 'thumb-btn relative h-16 sm:h-20 w-24 sm:w-28 shrink-0 rounded-2xl overflow-hidden border-2 border-slate-200 hover:border-[#d4a359] opacity-80 hover:opacity-100 transition duration-300';
            }
        });
    }

    // 2. Fullscreen Lightbox Controls
    function openRoomLightbox(index) {
        currentGalleryIndex = index || 0;
        setLightboxImage(currentGalleryIndex);
        const modal = document.getElementById('room-lightbox-modal');
        if (modal) modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeRoomLightbox() {
        const modal = document.getElementById('room-lightbox-modal');
        if (modal) modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    function setLightboxImage(index) {
        if (index < 0) index = roomGallery.length - 1;
        if (index >= roomGallery.length) index = 0;
        currentGalleryIndex = index;

        const lbImg = document.getElementById('lb-active-image');
        const counter = document.getElementById('lb-image-counter');
        
        if (lbImg) {
            lbImg.src = roomGallery[index];
        }
        if (counter) {
            counter.textContent = `${index + 1} / ${roomGallery.length}`;
        }

        // Highlight active lightbox thumbnail
        document.querySelectorAll('[id^="lb-thumb-"]').forEach((btn, idx) => {
            if (idx === index) {
                btn.className = 'h-12 w-16 sm:h-14 sm:w-20 rounded-lg overflow-hidden border-2 border-[#f3cf8a] scale-105 transition shrink-0';
            } else {
                btn.className = 'h-12 w-16 sm:h-14 sm:w-20 rounded-lg overflow-hidden border-2 border-white/20 opacity-50 hover:opacity-100 transition shrink-0';
            }
        });
    }

    function prevLightboxImage() {
        setLightboxImage(currentGalleryIndex - 1);
    }

    function nextLightboxImage() {
        setLightboxImage(currentGalleryIndex + 1);
    }

    // Keyboard support for Lightbox
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('room-lightbox-modal');
        if (modal && !modal.classList.contains('hidden')) {
            if (e.key === 'Escape') closeRoomLightbox();
            if (e.key === 'ArrowLeft') prevLightboxImage();
            if (e.key === 'ArrowRight') nextLightboxImage();
        }
    });

    // 3. Live Price & Bill Calculation
    function calculateStayBill() {
        const checkInInput = document.getElementById('calc_check_in');
        const checkOutInput = document.getElementById('calc_check_out');
        const guestsInput = document.getElementById('calc_guests');
        
        const nightsLabel = document.getElementById('nights-value');
        const formulaLabel = document.getElementById('calculation-formula');
        const subtotalLabel = document.getElementById('subtotal-value');
        const grandTotalLabel = document.getElementById('grand-total-value');
        const whatsappBtn = document.getElementById('whatsapp-inquiry-btn');

        if (!checkInInput || !checkOutInput) return;

        const checkInVal = checkInInput.value;
        const checkOutVal = checkOutInput.value;

        if (checkInVal && checkOutVal) {
            const checkInDate = new Date(checkInVal);
            const checkOutDate = new Date(checkOutVal);
            const diffTime = checkOutDate - checkInDate;
            let nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (nights <= 0) {
                // Auto-adjust checkout date to next day
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.value = nextDay.toISOString().split('T')[0];
                nights = 1;
            }

            const totalAmount = nights * roomBasePrice;
            const formattedTotal = '₹' + totalAmount.toLocaleString('en-IN');
            const guestsCount = guestsInput ? guestsInput.value : 2;

            if (nightsLabel) nightsLabel.textContent = `${nights} Night${nights > 1 ? 's' : ''}`;
            if (formulaLabel) formulaLabel.textContent = `Base Rate (₹${roomBasePrice.toLocaleString('en-IN')} × ${nights}):`;
            if (subtotalLabel) subtotalLabel.textContent = formattedTotal;
            if (grandTotalLabel) grandTotalLabel.textContent = formattedTotal;

            // Update WhatsApp inquiry link with prefilled dynamic details
            if (whatsappBtn) {
                const waText = `Hello Raj Residency, I want to book ${roomName} from ${checkInVal} to ${checkOutVal} (${nights} Night(s), ${guestsCount} Guests). Total estimated rate: ${formattedTotal}. Please confirm availability.`;
                whatsappBtn.href = `https://wa.me/${hotelWhatsApp}?text=${encodeURIComponent(waText)}`;
            }
        }
    }

    // Initialize Default Dates (Today & Tomorrow)
    document.addEventListener('DOMContentLoaded', function() {
        const checkInInput = document.getElementById('calc_check_in');
        const checkOutInput = document.getElementById('calc_check_out');

        const today = new Date();
        const tomorrow = new Date();
        tomorrow.setDate(today.getDate() + 1);

        const todayStr = today.toISOString().split('T')[0];
        const tomorrowStr = tomorrow.toISOString().split('T')[0];

        if (checkInInput) {
            checkInInput.min = todayStr;
            checkInInput.value = todayStr;
        }
        if (checkOutInput) {
            checkOutInput.min = tomorrowStr;
            checkOutInput.value = tomorrowStr;
        }

        calculateStayBill();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
