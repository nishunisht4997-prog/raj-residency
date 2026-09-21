<?php
// admin/index.php - Admin Dashboard Overview
$pageTitle = "Dashboard Overview";
require_once __DIR__ . '/includes/admin_header.php';

$allRooms = get_all_rooms();
$totalRooms = count($allRooms);
$availableRooms = count(array_filter($allRooms, function($r) { return !empty($r['is_available']); }));

$bookingStats = get_booking_stats();
$allBookings = get_all_bookings();
$recentBookings = array_slice($allBookings, 0, 6);

$allMessages = get_all_messages();
$recentMessages = array_slice($allMessages, 0, 5);

$allReviews = get_all_reviews();
$pendingReviews = array_filter($allReviews, function($r) { return empty($r['is_approved']); });
$recentReviews = array_slice($allReviews, 0, 4);

$heroBanners = get_section_images('hero_slider', false);
?>

<div class="space-y-8">
    
    <!-- WELCOME BANNER WITH QUICK STATS -->
    <div class="p-5 sm:p-8 rounded-3xl bg-gradient-to-r from-[#2b0e14] via-[#3d141d] to-[#1c080d] text-white border border-[#d4a359]/40 shadow-xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-2">
                <div class="inline-flex items-center space-x-2 bg-[#1c080d]/80 px-3 py-1 rounded-full text-[10px] font-bold tracking-widest text-[#f3cf8a] uppercase border border-[#d4a359]/30">
                    <i class="fa-solid fa-crown text-[#d4a359]"></i>
                    <span>RAJ RESIDENCY CONTROL CENTER</span>
                </div>
                <h2 class="font-serif text-xl sm:text-2xl md:text-3xl font-bold">
                    Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#f3cf8a] via-[#e5b869] to-[#d4a359]"><?php echo htmlspecialchars($adminUser['full_name'] ?? 'Hotel Manager'); ?></span>
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 font-light max-w-xl">
                    Manage hotel bookings, room availability, guest reviews, and direct image uploads from mobile or computer.
                </p>
            </div>

            <!-- Quick Action Hub -->
            <div class="flex flex-wrap gap-2 sm:gap-2.5">
                <a href="media.php" class="btn-gold px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs font-bold uppercase shadow-lg inline-flex items-center space-x-2">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Manage Images</span>
                </a>
                <a href="rooms.php?action=add" class="btn-ghost-gold px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl text-xs font-bold uppercase inline-flex items-center space-x-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Add New Room</span>
                </a>
            </div>
        </div>
        
        <!-- Decorative subtle background glow -->
        <div class="absolute -right-10 -bottom-10 w-60 h-60 rounded-full bg-[#d4a359]/10 blur-2xl pointer-events-none"></div>
    </div>

    <!-- 6 KEY STATISTIC CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-2.5 sm:gap-4">
        
        <!-- Stat 1: Total Bookings -->
        <div class="royal-card p-3.5 sm:p-5 space-y-1.5 sm:space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider truncate">Bookings</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-[#2b0e14]"><?php echo $bookingStats['total']; ?></div>
            <div class="text-[10px] text-slate-500 font-medium truncate"><?php echo $bookingStats['confirmed']; ?> confirmed</div>
        </div>

        <!-- Stat 2: Revenue -->
        <div class="royal-card p-3.5 sm:p-5 space-y-1.5 sm:space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider truncate">Revenue</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fa-solid fa-indian-rupee-sign"></i>
                </div>
            </div>
            <div class="text-lg sm:text-2xl font-black text-emerald-800 truncate">₹<?php echo number_format($bookingStats['revenue'], 0); ?></div>
            <div class="text-[10px] text-emerald-600 font-semibold truncate">Confirmed total</div>
        </div>

        <!-- Stat 3: Pending Bookings -->
        <div class="royal-card p-3.5 sm:p-5 space-y-1.5 sm:space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider truncate">Pending</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-rose-700"><?php echo $bookingStats['pending']; ?></div>
            <div class="text-[10px] text-rose-600 font-medium truncate">Awaiting check-in</div>
        </div>

        <!-- Stat 4: Rooms Available -->
        <div class="royal-card p-3.5 sm:p-5 space-y-1.5 sm:space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider truncate">Rooms</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fa-solid fa-door-open"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-[#2b0e14]"><?php echo $availableRooms; ?> <span class="text-xs text-slate-400 font-normal">/ <?php echo $totalRooms; ?></span></div>
            <div class="text-[10px] text-slate-500 font-medium truncate">Active available</div>
        </div>

        <!-- Stat 5: Inquiries -->
        <div class="royal-card p-3.5 sm:p-5 space-y-1.5 sm:space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider truncate">Inquiries</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-[#2b0e14]"><?php echo count($allMessages); ?></div>
            <div class="text-[10px] text-purple-700 font-semibold truncate"><?php echo $unreadMessagesCount; ?> unread</div>
        </div>

        <!-- Stat 6: Dynamic Images -->
        <div class="royal-card p-3.5 sm:p-5 space-y-1.5 sm:space-y-2">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider truncate">Banners</span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs font-bold shrink-0">
                    <i class="fa-solid fa-images"></i>
                </div>
            </div>
            <div class="text-xl sm:text-2xl font-black text-[#2b0e14]"><?php echo count($heroBanners); ?></div>
            <div class="text-[10px] text-slate-500 font-medium truncate">Rotating slides</div>
        </div>

    </div>

    <!-- MAIN TWO-COLUMN DASHBOARD SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8">
        
        <!-- LEFT COLUMN (8 cols): RECENT BOOKINGS TABLE & MOBILE CARDS -->
        <div class="lg:col-span-8 space-y-6">
            <div class="royal-card overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-[#ebd9c8] flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h3 class="font-serif text-base sm:text-lg font-bold text-[#2b0e14] flex items-center space-x-2">
                            <i class="fa-solid fa-calendar-days text-[#d4a359]"></i>
                            <span>Recent Guest Bookings</span>
                        </h3>
                        <p class="text-xs text-slate-500 font-light mt-0.5">Latest room booking requests from website & hotel counter</p>
                    </div>
                    <a href="bookings.php" class="text-xs font-bold text-[#b88738] hover:text-[#2b0e14] inline-flex items-center space-x-1">
                        <span>View All (<?php echo count($allBookings); ?>)</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <!-- DESKTOP / TABLET TABLE VIEW (Hidden on small mobile < md) -->
                <div class="hidden md:block overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[650px] text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-[#f5efe6] text-slate-600 font-bold uppercase text-[10px] tracking-wider border-b border-[#ebd9c8]">
                                <th class="py-3 px-4">Booking Ref</th>
                                <th class="py-3 px-4">Guest Details</th>
                                <th class="py-3 px-4">Room Type</th>
                                <th class="py-3 px-4">Check-In / Out</th>
                                <th class="py-3 px-4">Amount</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#f5efe6]">
                            <?php if (empty($recentBookings)): ?>
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400">
                                        <i class="fa-solid fa-calendar-xmark text-2xl mb-2 block"></i>
                                        No bookings received yet.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentBookings as $b): ?>
                                    <tr class="hover:bg-[#fbf9f5] transition">
                                        <td class="py-3.5 px-4 font-mono font-bold text-[#2b0e14]">
                                            <?php echo htmlspecialchars($b['booking_number']); ?>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-800"><?php echo htmlspecialchars($b['guest_name']); ?></div>
                                            <div class="text-[11px] text-slate-500 flex items-center space-x-1 mt-0.5">
                                                <i class="fa-solid fa-phone text-[9px] text-[#d4a359]"></i>
                                                <span><?php echo htmlspecialchars($b['guest_phone']); ?></span>
                                            </div>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="font-medium text-slate-700 block"><?php echo htmlspecialchars($b['room_name']); ?></span>
                                            <span class="text-[10px] text-slate-400"><?php echo (int)$b['adults']; ?> Adults &bull; <?php echo (int)$b['total_nights']; ?> Night(s)</span>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <div class="font-medium text-slate-800"><?php echo date('d M Y', strtotime($b['check_in'])); ?></div>
                                            <div class="text-[10px] text-slate-400">to <?php echo date('d M Y', strtotime($b['check_out'])); ?></div>
                                        </td>
                                        <td class="py-3.5 px-4 font-bold text-[#2b0e14]">
                                            ₹<?php echo number_format($b['total_amount'], 0); ?>
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <?php
                                             $status = $b['booking_status'] ?? 'Confirmed';
                                             $stClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                                             if ($status === 'Pending') $stClass = 'bg-amber-100 text-amber-800 border-amber-300';
                                             if ($status === 'Cancelled') $stClass = 'bg-rose-100 text-rose-800 border-rose-300';
                                             if ($status === 'Checked-In') $stClass = 'bg-blue-100 text-blue-800 border-blue-300';
                                            ?>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo $stClass; ?>">
                                                <?php echo htmlspecialchars($status); ?>
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right">
                                            <a href="bookings.php?search=<?php echo urlencode($b['booking_number']); ?>" class="p-1.5 rounded-lg bg-slate-100 hover:bg-[#2b0e14] hover:text-[#f3cf8a] text-slate-600 transition inline-block" title="View details">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE BOOKINGS CARD VIEW (Visible only on mobile < md) -->
                <div class="block md:hidden divide-y divide-[#f5efe6]">
                    <?php if (empty($recentBookings)): ?>
                        <div class="p-6 text-center text-slate-400 text-xs">
                            <i class="fa-solid fa-calendar-xmark text-2xl mb-1 block"></i>
                            No bookings received yet.
                        </div>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $b): ?>
                            <?php
                            $status = $b['booking_status'] ?? 'Confirmed';
                            $stClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                            if ($status === 'Pending') $stClass = 'bg-amber-100 text-amber-800 border-amber-300';
                            if ($status === 'Cancelled') $stClass = 'bg-rose-100 text-rose-800 border-rose-300';
                            if ($status === 'Checked-In') $stClass = 'bg-blue-100 text-blue-800 border-blue-300';
                            ?>
                            <div class="p-4 space-y-3 hover:bg-[#fbf9f5] transition">
                                <div class="flex items-center justify-between">
                                    <div class="font-mono font-bold text-xs text-[#2b0e14] bg-[#f5efe6] px-2 py-0.5 rounded border border-[#ebd9c8]">
                                        <?php echo htmlspecialchars($b['booking_number']); ?>
                                    </div>
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border <?php echo $stClass; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </div>

                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="font-bold text-slate-900 text-xs"><?php echo htmlspecialchars($b['guest_name']); ?></div>
                                        <div class="text-[11px] text-slate-500 font-medium mt-0.5"><?php echo htmlspecialchars($b['room_name']); ?> &bull; <?php echo (int)$b['total_nights']; ?>N</div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            <?php echo date('d M', strtotime($b['check_in'])); ?> &rarr; <?php echo date('d M Y', strtotime($b['check_out'])); ?>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-black text-sm text-[#2b0e14]">₹<?php echo number_format($b['total_amount'], 0); ?></div>
                                        <div class="text-[10px] text-slate-400"><?php echo htmlspecialchars($b['payment_status'] ?? 'Pending'); ?></div>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $b['guest_phone']); ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[11px] font-semibold flex items-center space-x-1">
                                            <i class="fa-solid fa-phone text-[9px] text-[#d4a359]"></i>
                                            <span>Call</span>
                                        </a>
                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $b['guest_phone']); ?>" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[11px] font-semibold flex items-center space-x-1">
                                            <i class="fa-brands fa-whatsapp text-xs text-emerald-600"></i>
                                            <span>WhatsApp</span>
                                        </a>
                                    </div>

                                    <a href="bookings.php?search=<?php echo urlencode($b['booking_number']); ?>" class="px-3 py-1 rounded-lg bg-[#2b0e14] text-[#f3cf8a] text-[11px] font-bold">
                                        View Details &rarr;
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN (4 cols): QUICK MEDIA & INQUIRIES WIDGETS -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Quick Image Upload Direct Card -->
            <div class="royal-card p-5 sm:p-6 space-y-4 bg-gradient-to-b from-white to-[#fbf9f5]">
                <div class="flex items-center justify-between">
                    <h3 class="font-serif text-base font-bold text-[#2b0e14] flex items-center space-x-2">
                        <i class="fa-solid fa-camera text-[#d4a359]"></i>
                        <span>Mobile & PC Upload</span>
                    </h3>
                    <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Active</span>
                </div>
                <p class="text-xs text-slate-600 font-light">
                    Directly select photos from your smartphone camera, photo gallery, or computer without pasting URLs.
                </p>
                <div class="pt-2">
                    <a href="media.php" class="w-full btn-gold py-2.5 rounded-xl text-xs font-bold uppercase flex items-center justify-center space-x-2 shadow-md">
                        <i class="fa-solid fa-images"></i>
                        <span>Open Image Manager</span>
                    </a>
                </div>
            </div>

            <!-- Recent Inquiries Box -->
            <div class="royal-card p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3">
                    <h3 class="font-serif text-base font-bold text-[#2b0e14] flex items-center space-x-2">
                        <i class="fa-solid fa-envelope text-[#d4a359]"></i>
                        <span>Latest Messages</span>
                    </h3>
                    <a href="messages.php" class="text-[11px] font-bold text-[#b88738] hover:underline">View All</a>
                </div>

                <div class="space-y-3">
                    <?php if (empty($recentMessages)): ?>
                        <p class="text-xs text-slate-400 py-3 text-center">No contact inquiries yet.</p>
                    <?php else: ?>
                        <?php foreach ($recentMessages as $m): ?>
                            <div class="p-3 rounded-xl <?php echo empty($m['is_read']) ? 'bg-[#f5efe6] border border-[#d4a359]/40' : 'bg-[#fbf9f5] border border-slate-100'; ?> space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-xs text-slate-800"><?php echo htmlspecialchars($m['name']); ?></span>
                                    <span class="text-[9px] text-slate-400"><?php echo date('d M', strtotime($m['created_at'])); ?></span>
                                </div>
                                <p class="text-[11px] font-medium text-[#2b0e14] truncate"><?php echo htmlspecialchars($m['subject']); ?></p>
                                <p class="text-[10px] text-slate-500 line-clamp-1 font-light"><?php echo htmlspecialchars($m['message']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Reviews Box -->
            <div class="royal-card p-5 sm:p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3">
                    <h3 class="font-serif text-base font-bold text-[#2b0e14] flex items-center space-x-2">
                        <i class="fa-solid fa-star text-[#d4a359]"></i>
                        <span>Pending Reviews</span>
                    </h3>
                    <a href="reviews.php" class="text-[11px] font-bold text-[#b88738] hover:underline">Moderate</a>
                </div>

                <div class="space-y-2.5">
                    <?php if (empty($pendingReviews)): ?>
                        <p class="text-xs text-slate-400 py-2 text-center">No pending reviews to moderate.</p>
                    <?php else: ?>
                        <?php foreach (array_slice($pendingReviews, 0, 3) as $rv): ?>
                            <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200 text-xs space-y-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-800"><?php echo htmlspecialchars($rv['guest_name']); ?></span>
                                    <div class="text-[#d4a359] text-[10px] flex">
                                        <?php for ($i = 0; $i < (int)$rv['rating']; $i++): ?>
                                            <i class="fa-solid fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="text-[11px] text-slate-600 line-clamp-1">"<?php echo htmlspecialchars($rv['title']); ?>"</p>
                                <div class="pt-1 flex items-center space-x-2">
                                    <a href="reviews.php?approve=<?php echo $rv['id']; ?>" class="text-[10px] bg-emerald-700 text-white px-2.5 py-0.5 rounded-full font-bold hover:bg-emerald-800">Approve</a>
                                    <a href="reviews.php?delete=<?php echo $rv['id']; ?>" class="text-[10px] text-rose-600 hover:underline">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
