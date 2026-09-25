<?php
// includes/room_card.php - Reusable Room Card Component
// Expects $room variable
if (!isset($room)) return;

$badgeClass = 'badge-ac';
$badgeIcon = 'fa-solid fa-snowflake';
$badgeText = 'Air Conditioned';

if ($room['category'] === 'non_ac') {
    $badgeClass = 'badge-non-ac';
    $badgeIcon = 'fa-solid fa-wind';
    $badgeText = 'Budget Non-AC';
} elseif ($room['category'] === 'suite') {
    $badgeClass = 'badge-suite';
    $badgeIcon = 'fa-solid fa-crown';
    $badgeText = 'Royal Suite';
}
?>
<div class="room-card-item arch-top-card bg-white border border-[#ebd9c8] shadow-md hover:shadow-2xl gold-glow-hover flex flex-col justify-between" data-category="<?php echo htmlspecialchars($room['category']); ?>">
    
    <!-- Arch-Top Card Image & Floating Badge -->
    <div class="card-img-holder relative h-64 group m-3 mb-0">
        <img src="<?php echo htmlspecialchars($room['featured_image']); ?>" 
             alt="<?php echo htmlspecialchars($room['name']); ?>" 
             onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80';"
             class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
        
        <!-- Category Badge -->
        <div class="absolute top-3 left-3">
            <span class="<?php echo $badgeClass; ?>">
                <i class="<?php echo $badgeIcon; ?> text-[10px]"></i>
                <span><?php echo $badgeText; ?></span>
            </span>
        </div>

        <!-- Glassmorphic Price Tag Overlay -->
        <div class="absolute bottom-3 right-3 glass-price-tag flex items-baseline space-x-1.5">
            <span class="text-sm font-black text-[#f3cf8a]"><?php echo format_price($room['price_per_night']); ?></span>
            <span class="text-[10px] text-slate-300 font-normal">/ night</span>
        </div>
    </div>

    <!-- Card Content -->
    <div class="p-5 sm:p-6 flex-grow flex flex-col justify-between">
        <div>
            <!-- Room Title -->
            <h3 class="font-serif text-xl font-bold text-[#2b0e14] mb-2 hover:text-[#b88738] transition">
                <a href="room-details.php?id=<?php echo $room['id']; ?>"><?php echo htmlspecialchars($room['name']); ?></a>
            </h3>

            <!-- Room Specs -->
            <div class="flex items-center space-x-4 text-xs text-slate-500 mb-3.5 border-b border-[#f5efe6] pb-3">
                <span class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-users text-[#d4a359]"></i>
                    <span>Max <?php echo (int)$room['max_guests']; ?> Guests</span>
                </span>
                <span class="flex items-center space-x-1.5">
                    <i class="fa-solid fa-bed text-[#d4a359]"></i>
                    <span><?php echo htmlspecialchars($room['bed_type']); ?></span>
                </span>
            </div>

            <!-- Description -->
            <p class="text-xs text-slate-600 line-clamp-2 mb-4 leading-relaxed font-light">
                <?php echo htmlspecialchars($room['description']); ?>
            </p>

            <!-- Amenities Icons preview -->
            <div class="flex flex-wrap gap-1.5 mb-5">
                <?php 
                $previewAmenities = is_array($room['amenities']) ? array_slice($room['amenities'], 0, 3) : [];
                foreach ($previewAmenities as $am): 
                ?>
                    <span class="text-[11px] bg-[#fbf9f5] text-slate-700 px-2.5 py-1 rounded-lg border border-[#ebd9c8] flex items-center space-x-1 font-medium">
                        <i class="fa-solid fa-circle-check text-[9px] text-[#d4a359]"></i>
                        <span><?php echo htmlspecialchars($am); ?></span>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Buttons (Details & Book Now) -->
        <div class="grid grid-cols-2 gap-2.5 pt-3 border-t border-[#f5efe6]">
            <a href="room-details.php?id=<?php echo $room['id']; ?>" 
               class="px-4 py-2.5 rounded-full text-center text-xs font-bold text-[#2b0e14] border border-[#ebd9c8] hover:border-[#2b0e14] hover:bg-[#fbf9f5] transition">
                Details
            </a>
            <a href="booking.php?room_id=<?php echo $room['id']; ?>" 
               class="btn-gold px-4 py-2.5 rounded-full text-center text-xs font-bold uppercase shadow-sm flex items-center justify-center space-x-1">
                <span>Book Now</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

</div>
