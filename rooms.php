<?php
// rooms.php - Rooms & Accommodations Catalog for Raj Residency
$pageTitle = "Rooms & Accommodations";
require_once __DIR__ . '/includes/header.php';

$selectedCategory = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$rooms = get_all_rooms($selectedCategory, true);
?>

<!-- Breadcrumb Banner -->
<section class="bg-[#2b0e14] text-white py-14 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-3">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <span class="text-white">ROOMS</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            Rooms & Accommodations
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto font-light">
            Choose from our premium air-conditioned luxury rooms or budget-friendly comfortable non-AC rooms in Koraput, Odisha.
        </p>
    </div>
</section>

<!-- Filter Navigation Bar -->
<section class="bg-[#fbf9f5] border-b border-[#d4a359]/20 sticky top-20 sm:top-24 z-40 shadow-sm backdrop-blur-md bg-opacity-95">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5">
        <div class="flex items-center justify-between gap-3 overflow-x-auto pb-1 sm:pb-0 scrollbar-thin">
            
            <div class="flex items-center gap-2 shrink-0">
                <a href="rooms.php" class="px-4 sm:px-5 py-2 rounded-full text-xs font-bold transition whitespace-nowrap <?php echo ($selectedCategory === 'all') ? 'bg-[#2b0e14] text-[#f3cf8a] shadow-md ring-1 ring-[#d4a359]/40' : 'bg-white text-slate-700 hover:bg-[#f5efe6] border border-slate-200'; ?>">
                    All Rooms
                </a>
                <a href="rooms.php?category=ac" class="px-4 sm:px-5 py-2 rounded-full text-xs font-bold transition whitespace-nowrap <?php echo ($selectedCategory === 'ac') ? 'bg-[#2b0e14] text-[#f3cf8a] shadow-md ring-1 ring-[#d4a359]/40' : 'bg-white text-slate-700 hover:bg-[#f5efe6] border border-slate-200'; ?>">
                    <i class="fa-solid fa-snowflake text-[10px] mr-1 text-[#d4a359]"></i> AC Deluxe
                </a>
                <a href="rooms.php?category=non_ac" class="px-4 sm:px-5 py-2 rounded-full text-xs font-bold transition whitespace-nowrap <?php echo ($selectedCategory === 'non_ac') ? 'bg-[#2b0e14] text-[#f3cf8a] shadow-md ring-1 ring-[#d4a359]/40' : 'bg-white text-slate-700 hover:bg-[#f5efe6] border border-slate-200'; ?>">
                    <i class="fa-solid fa-wind text-[10px] mr-1 text-slate-500"></i> Budget Non-AC
                </a>
                <a href="rooms.php?category=suite" class="px-4 sm:px-5 py-2 rounded-full text-xs font-bold transition whitespace-nowrap <?php echo ($selectedCategory === 'suite') ? 'bg-[#2b0e14] text-[#f3cf8a] shadow-md ring-1 ring-[#d4a359]/40' : 'bg-white text-slate-700 hover:bg-[#f5efe6] border border-slate-200'; ?>">
                    <i class="fa-solid fa-crown text-[10px] mr-1 text-[#d4a359]"></i> Royal Suites
                </a>
            </div>

            <?php if ($selectedCategory !== 'all'): ?>
                <a href="rooms.php" class="text-xs text-rose-600 hover:underline font-semibold flex items-center space-x-1 shrink-0 whitespace-nowrap">
                    <i class="fa-solid fa-rotate-left"></i>
                    <span>Reset</span>
                </a>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Rooms Grid Section -->
<section class="py-16 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (empty($rooms)): ?>
            <div class="text-center py-20 bg-white rounded-3xl border border-[#d4a359]/30 p-8 space-y-3">
                <i class="fa-solid fa-hotel text-4xl text-slate-300 mb-1"></i>
                <h3 class="font-serif text-2xl font-bold text-[#2b0e14]">No rooms found in this category</h3>
                <p class="text-xs text-slate-500">Please view all available rooms or contact our reservation desk.</p>
                <div class="pt-2">
                    <a href="rooms.php" class="btn-gold px-6 py-2.5 rounded-full text-xs font-bold uppercase">View All Rooms</a>
                </div>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php 
                foreach ($rooms as $room) {
                    include __DIR__ . '/includes/room_card.php';
                }
                ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<!-- Bottom Booking Assistance Callout -->
<section class="py-14 bg-[#f5efe6] border-t border-[#d4a359]/30">
    <div class="max-w-4xl mx-auto px-4 text-center space-y-4">
        <h3 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">Need Help Deciding the Perfect Room for Your Family?</h3>
        <p class="text-xs sm:text-sm text-slate-600 font-light max-w-2xl mx-auto">
            Our front desk at Post Office Road, Koraput is available 24 hours to help you choose the best AC/Non-AC room for your dates.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="btn-gold px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider flex items-center space-x-2">
                <i class="fa-solid fa-phone"></i>
                <span>Call <?php echo htmlspecialchars($settings['hotel_phone']); ?></span>
            </a>
            <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20please%20help%20me%20choose%20a%20room" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider flex items-center space-x-2 shadow-md transition">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>WhatsApp Desk</span>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

