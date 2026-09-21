<?php
// admin/bookings.php - Ultra-Modern Luxury Bookings & Reservations Suite for Raj Residency
require_once __DIR__ . '/../includes/functions.php';
check_admin_auth();

$settings = get_settings();
$allRooms = get_all_rooms();
$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

// -------------------------------------------------------------
// 1. Handle CSV Export
// -------------------------------------------------------------
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $exportBookings = get_all_bookings();
    $filename = "raj_residency_bookings_" . date('Y-m-d_His') . ".csv";
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    $output = fopen('php://output', 'w');
    // Output BOM for Excel UTF-8 compatibility
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, [
        'Booking Ref', 'Guest Name', 'Phone', 'Email', 'ID Proof Type', 'ID Proof No',
        'Room Name', 'Check-In Date', 'Check-Out Date', 'Nights', 'Adults', 'Children',
        'Price Per Night (INR)', 'Total Amount (INR)', 'Payment Method', 'Payment Status',
        'Booking Status', 'Special Requests', 'Booked On (IST)'
    ]);
    
    foreach ($exportBookings as $b) {
        fputcsv($output, [
            $b['booking_number'],
            $b['guest_name'],
            $b['guest_phone'],
            $b['guest_email'],
            $b['id_proof_type'],
            $b['id_proof_number'],
            $b['room_name'],
            $b['check_in'],
            $b['check_out'],
            $b['total_nights'],
            $b['adults'],
            $b['children'],
            $b['price_per_night'],
            $b['total_amount'],
            $b['payment_method'],
            $b['payment_status'],
            $b['booking_status'],
            $b['special_requests'],
            format_datetime($b['created_at'])
        ]);
    }
    fclose($output);
    exit;
}

// -------------------------------------------------------------
// 2. Handle Status & Payment Quick Updates
// -------------------------------------------------------------
if (isset($_GET['status']) && isset($_GET['id'])) {
    $bId = (int)$_GET['id'];
    $newStatus = sanitize($_GET['status']);
    $paymentStatus = isset($_GET['pay']) ? sanitize($_GET['pay']) : null;
    
    update_booking_status($bId, $newStatus, $paymentStatus);
    set_flash_message('success', 'Booking #' . $bId . ' status updated to "' . htmlspecialchars($newStatus) . '"');
    header('Location: bookings.php');
    exit;
}

// 1-Click Toggle Payment Status (Paid <-> Pending)
if (isset($_GET['pay_toggle']) && isset($_GET['id'])) {
    $bId = (int)$_GET['id'];
    $current = get_booking_by_id($bId);
    if ($current) {
        $newPay = ($current['payment_status'] === 'Paid') ? 'Pending' : 'Paid';
        toggle_payment_status($bId, $newPay);
        set_flash_message('success', 'Payment status for #' . $current['booking_number'] . ' switched to "' . $newPay . '"');
    }
    header('Location: bookings.php');
    exit;
}

// -------------------------------------------------------------
// 3. Handle Delete Booking Record
// -------------------------------------------------------------
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    delete_booking($delId);
    set_flash_message('success', 'Booking record #' . $delId . ' deleted successfully.');
    header('Location: bookings.php');
    exit;
}

// -------------------------------------------------------------
// 4. Handle Add Manual Walk-In Booking from Front Desk
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'manual_booking') {
    $roomId = (int)($_POST['room_id'] ?? 0);
    $selectedRoom = get_room_by_id($roomId);
    
    if (!$selectedRoom) {
        set_flash_message('error', 'Please select a valid room.');
        header('Location: bookings.php');
        exit;
    }

    $guestName = sanitize($_POST['guest_name'] ?? '');
    $guestPhone = sanitize($_POST['guest_phone'] ?? '');
    $guestEmail = sanitize($_POST['guest_email'] ?? '');
    $idProofType = sanitize($_POST['id_proof_type'] ?? 'Aadhaar Card');
    $idProofNumber = sanitize($_POST['id_proof_number'] ?? '');
    $checkIn = sanitize($_POST['check_in'] ?? $today);
    $checkOut = sanitize($_POST['check_out'] ?? $tomorrow);
    $adults = max(1, (int)($_POST['adults'] ?? 1));
    $children = max(0, (int)($_POST['children'] ?? 0));
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'Cash at Front Desk');
    $paymentStatus = sanitize($_POST['payment_status'] ?? 'Paid');
    $bookingStatus = sanitize($_POST['booking_status'] ?? 'Confirmed');
    $specialRequests = sanitize($_POST['special_requests'] ?? 'Front Desk Walk-In');

    // Calculate nights & amount
    $cInTime = strtotime($checkIn);
    $cOutTime = strtotime($checkOut);
    $totalNights = max(1, ceil(($cOutTime - $cInTime) / 86400));
    $pricePerNight = (float)$selectedRoom['price_per_night'];
    $totalAmount = (float)($_POST['total_amount'] ?? ($pricePerNight * $totalNights));

    $bookingRef = create_booking([
        'room_id'         => $roomId,
        'room_name'       => $selectedRoom['name'],
        'guest_name'      => $guestName,
        'guest_email'     => $guestEmail,
        'guest_phone'     => $guestPhone,
        'id_proof_type'   => $idProofType,
        'id_proof_number' => $idProofNumber,
        'check_in'        => $checkIn,
        'check_out'       => $checkOut,
        'total_nights'    => $totalNights,
        'adults'          => $adults,
        'children'        => $children,
        'price_per_night' => $pricePerNight,
        'total_amount'    => $totalAmount,
        'payment_method'  => $paymentMethod,
        'payment_status'  => $paymentStatus,
        'booking_status'  => $bookingStatus,
        'special_requests'=> $specialRequests
    ]);

    if ($bookingRef) {
        set_flash_message('success', 'Reservation ' . $bookingRef . ' created successfully for ' . htmlspecialchars($guestName) . '!');
    } else {
        set_flash_message('error', 'Failed to save booking.');
    }
    header('Location: bookings.php');
    exit;
}

// -------------------------------------------------------------
// 5. Handle Edit Booking Details
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_booking') {
    $editId = (int)($_POST['booking_id'] ?? 0);
    $roomId = (int)($_POST['room_id'] ?? 0);
    $selectedRoom = get_room_by_id($roomId);

    if ($editId > 0 && $selectedRoom) {
        $guestName = sanitize($_POST['guest_name'] ?? '');
        $guestPhone = sanitize($_POST['guest_phone'] ?? '');
        $guestEmail = sanitize($_POST['guest_email'] ?? '');
        $idProofType = sanitize($_POST['id_proof_type'] ?? 'Aadhaar Card');
        $idProofNumber = sanitize($_POST['id_proof_number'] ?? '');
        $checkIn = sanitize($_POST['check_in'] ?? $today);
        $checkOut = sanitize($_POST['check_out'] ?? $tomorrow);
        $adults = max(1, (int)($_POST['adults'] ?? 1));
        $children = max(0, (int)($_POST['children'] ?? 0));
        $paymentMethod = sanitize($_POST['payment_method'] ?? 'Pay at Hotel');
        $paymentStatus = sanitize($_POST['payment_status'] ?? 'Pending');
        $bookingStatus = sanitize($_POST['booking_status'] ?? 'Confirmed');
        $specialRequests = sanitize($_POST['special_requests'] ?? '');

        $cInTime = strtotime($checkIn);
        $cOutTime = strtotime($checkOut);
        $totalNights = max(1, ceil(($cOutTime - $cInTime) / 86400));
        $pricePerNight = (float)$selectedRoom['price_per_night'];
        $totalAmount = (float)($_POST['total_amount'] ?? ($pricePerNight * $totalNights));

        $updated = update_booking_full($editId, [
            'room_id'         => $roomId,
            'room_name'       => $selectedRoom['name'],
            'guest_name'      => $guestName,
            'guest_email'     => $guestEmail,
            'guest_phone'     => $guestPhone,
            'id_proof_type'   => $idProofType,
            'id_proof_number' => $idProofNumber,
            'check_in'        => $checkIn,
            'check_out'       => $checkOut,
            'total_nights'    => $totalNights,
            'adults'          => $adults,
            'children'        => $children,
            'price_per_night' => $pricePerNight,
            'total_amount'    => $totalAmount,
            'payment_method'  => $paymentMethod,
            'payment_status'  => $paymentStatus,
            'booking_status'  => $bookingStatus,
            'special_requests'=> $specialRequests
        ]);

        if ($updated) {
            set_flash_message('success', 'Booking details updated successfully.');
        } else {
            set_flash_message('error', 'Failed to update booking details.');
        }
    }
    header('Location: bookings.php');
    exit;
}

// -------------------------------------------------------------
// 6. Data Retrieval & Filtering
// -------------------------------------------------------------
$pageTitle = "Bookings & Reservations";
require_once __DIR__ . '/includes/admin_header.php';

$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status_filter'] ?? 'all');
$dateFilter = trim($_GET['date_filter'] ?? 'all');
$allBookings = get_all_bookings();

// Statistics counts
$stats = [
    'total'       => count($allBookings),
    'confirmed'   => 0,
    'pending'     => 0,
    'checked_in'  => 0,
    'checked_out' => 0,
    'cancelled'   => 0,
    'revenue'     => 0.0,
    'today_in'    => 0,
    'today_out'   => 0
];

foreach ($allBookings as $b) {
    $st = $b['booking_status'] ?? 'Confirmed';
    if ($st === 'Confirmed') {
        $stats['confirmed']++;
        $stats['revenue'] += (float)($b['total_amount'] ?? 0);
    } elseif ($st === 'Pending') {
        $stats['pending']++;
    } elseif ($st === 'Checked-In') {
        $stats['checked_in']++;
        $stats['revenue'] += (float)($b['total_amount'] ?? 0);
    } elseif ($st === 'Checked-Out') {
        $stats['checked_out']++;
        $stats['revenue'] += (float)($b['total_amount'] ?? 0);
    } elseif ($st === 'Cancelled') {
        $stats['cancelled']++;
    }

    if (($b['check_in'] ?? '') === $today && $st !== 'Cancelled') {
        $stats['today_in']++;
    }
    if (($b['check_out'] ?? '') === $today && $st === 'Checked-In') {
        $stats['today_out']++;
    }
}

// Filter bookings array
$filteredBookings = array_filter($allBookings, function($b) use ($search, $statusFilter, $dateFilter, $today) {
    // Status Filter
    if ($statusFilter !== 'all' && ($b['booking_status'] ?? '') !== $statusFilter) {
        return false;
    }
    
    // Date Quick Filter
    if ($dateFilter === 'today_in' && ($b['check_in'] ?? '') !== $today) {
        return false;
    }
    if ($dateFilter === 'today_out' && ($b['check_out'] ?? '') !== $today) {
        return false;
    }
    if ($dateFilter === 'in_house' && ($b['booking_status'] ?? '') !== 'Checked-In') {
        return false;
    }
    if ($dateFilter === 'upcoming' && (($b['check_in'] ?? '') <= $today || ($b['booking_status'] ?? '') === 'Cancelled')) {
        return false;
    }

    // Search Query
    if (!empty($search)) {
        $s = strtolower($search);
        $match = (
            strpos(strtolower($b['booking_number'] ?? ''), $s) !== false ||
            strpos(strtolower($b['guest_name'] ?? ''), $s) !== false ||
            strpos(strtolower($b['guest_phone'] ?? ''), $s) !== false ||
            strpos(strtolower($b['guest_email'] ?? ''), $s) !== false ||
            strpos(strtolower($b['room_name'] ?? ''), $s) !== false
        );
        return $match;
    }
    return true;
});
?>

<div class="space-y-6">
    
    <!-- 1. TOP HEADER & ACTION CONTROLS -->
    <div class="royal-card p-4 sm:p-6 bg-gradient-to-r from-white via-[#fdfbf7] to-[#fbf9f5] flex flex-col md:flex-row md:items-center justify-between gap-4 border border-[#ebd9c8] shadow-sm">
        <div>
            <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                <i class="fa-solid fa-crown text-[10px] text-[#d4a359]"></i>
                <span>FRONT DESK & ONLINE RESERVATIONS ENGINE</span>
            </div>
            <h2 class="font-serif text-xl sm:text-2xl md:text-3xl font-bold text-[#2b0e14] mt-1">Bookings & Reservations</h2>
            <p class="text-xs text-slate-500 font-light mt-0.5">Manage live check-ins, guest dossiers, 1-click WhatsApp confirmations, instant bills, and room allocations.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
            <!-- Export CSV -->
            <a href="bookings.php?export=csv" class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-full border border-[#2b0e14]/20 text-[#2b0e14] hover:bg-[#2b0e14] hover:text-[#f3cf8a] text-xs font-bold transition flex items-center space-x-2 shadow-sm bg-white" title="Export All Bookings to Excel/CSV">
                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                <span>Export CSV</span>
            </a>

            <!-- + Walk-In Booking Button -->
            <button type="button" onclick="document.getElementById('manual-booking-modal').classList.remove('hidden')" class="btn-gold px-4 sm:px-6 py-2 sm:py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>+ Walk-In Booking</span>
            </button>
        </div>
    </div>

    <!-- 2. 5 STATISTIC KPI METRIC CARDS -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-4">
        
        <!-- Total Bookings -->
        <a href="bookings.php?status_filter=all" class="royal-card p-3 sm:p-4 hover:shadow-md transition <?php echo $statusFilter === 'all' ? 'border-[#d4a359] bg-[#fdfbf7] ring-2 ring-[#d4a359]/20' : ''; ?>">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider truncate">All Bookings</span>
                <i class="fa-solid fa-calendar-days text-xs text-slate-400"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-[#2b0e14]"><?php echo $stats['total']; ?></div>
            <span class="text-[10px] text-slate-400">Total recorded</span>
        </a>

        <!-- Confirmed -->
        <a href="bookings.php?status_filter=Confirmed" class="royal-card p-3 sm:p-4 hover:shadow-md transition <?php echo $statusFilter === 'Confirmed' ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20' : ''; ?>">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 truncate">Confirmed</span>
                <i class="fa-solid fa-circle-check text-xs text-emerald-500"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-emerald-800"><?php echo $stats['confirmed']; ?></div>
            <span class="text-[10px] text-emerald-600 font-medium truncate">Ready to check-in</span>
        </a>

        <!-- Pending -->
        <a href="bookings.php?status_filter=Pending" class="royal-card p-3 sm:p-4 hover:shadow-md transition <?php echo $statusFilter === 'Pending' ? 'border-amber-500 bg-amber-50/40 ring-2 ring-amber-500/20' : ''; ?>">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 truncate">Pending</span>
                <i class="fa-solid fa-clock text-xs text-amber-500"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-amber-800"><?php echo $stats['pending']; ?></div>
            <span class="text-[10px] text-amber-600 font-medium truncate">Action required</span>
        </a>

        <!-- Checked In -->
        <a href="bookings.php?status_filter=Checked-In" class="royal-card p-3 sm:p-4 hover:shadow-md transition <?php echo $statusFilter === 'Checked-In' ? 'border-blue-500 bg-blue-50/40 ring-2 ring-blue-500/20' : ''; ?>">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700 truncate">Checked-In</span>
                <i class="fa-solid fa-key text-xs text-blue-500"></i>
            </div>
            <div class="text-xl sm:text-2xl font-black text-blue-800"><?php echo $stats['checked_in']; ?></div>
            <span class="text-[10px] text-blue-600 font-medium truncate">In-house guests</span>
        </a>

        <!-- Total Revenue -->
        <div class="royal-card p-3 sm:p-4 bg-gradient-to-br from-white via-[#fcfaf7] to-[#fbf9f5] border border-[#d4a359]/30 col-span-2 sm:col-span-1 shadow-sm">
            <div class="flex items-center justify-between text-slate-500 mb-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#b88738] truncate">Revenue</span>
                <i class="fa-solid fa-indian-rupee-sign text-xs text-[#d4a359]"></i>
            </div>
            <div class="text-lg sm:text-2xl font-black text-[#2b0e14] truncate">₹<?php echo number_format($stats['revenue'], 0); ?></div>
            <span class="text-[10px] text-slate-500 truncate">Confirmed total</span>
        </div>

    </div>

    <!-- 3. SEARCH & DYNAMIC FILTER BAR -->
    <div class="royal-card p-4 sm:p-5 space-y-3.5">
        
        <!-- Status Filter Tabs (Swipeable on Mobile) -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-[#f5efe6]">
            
            <div class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 sm:pb-0 no-scrollbar whitespace-nowrap">
                <?php
                $tabOptions = [
                    'all'         => ['label' => 'All Bookings', 'count' => $stats['total']],
                    'Confirmed'   => ['label' => 'Confirmed', 'count' => $stats['confirmed']],
                    'Pending'     => ['label' => 'Pending', 'count' => $stats['pending']],
                    'Checked-In'  => ['label' => 'Checked-In', 'count' => $stats['checked_in']],
                    'Checked-Out' => ['label' => 'Checked-Out', 'count' => $stats['checked_out']],
                    'Cancelled'   => ['label' => 'Cancelled', 'count' => $stats['cancelled']]
                ];
                foreach ($tabOptions as $tabKey => $tabData):
                    $isCurrentTab = ($statusFilter === $tabKey);
                ?>
                    <a href="bookings.php?status_filter=<?php echo $tabKey; ?>&date_filter=<?php echo urlencode($dateFilter); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="shrink-0 px-3 py-1.5 rounded-full text-xs font-bold transition flex items-center space-x-1.5 <?php echo $isCurrentTab ? 'bg-[#2b0e14] text-[#f3cf8a] shadow-sm ring-1 ring-[#d4a359]/40' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200'; ?>">
                        <span><?php echo $tabData['label']; ?></span>
                        <span class="text-[10px] px-1.5 py-0.2 rounded-full <?php echo $isCurrentTab ? 'bg-[#f3cf8a] text-[#2b0e14]' : 'bg-slate-200 text-slate-700'; ?> font-black"><?php echo $tabData['count']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Date Quick Filters -->
            <div class="flex items-center flex-wrap gap-1.5 text-xs shrink-0">
                <span class="text-[11px] font-bold text-slate-400 hidden xl:inline">Quick Dates:</span>
                <a href="bookings.php?status_filter=<?php echo urlencode($statusFilter); ?>&date_filter=all" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold <?php echo ($dateFilter === 'all') ? 'bg-[#d4a359] text-[#2b0e14] font-bold' : 'text-slate-500 hover:bg-slate-100'; ?>">All</a>
                <a href="bookings.php?status_filter=<?php echo urlencode($statusFilter); ?>&date_filter=today_in" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold <?php echo ($dateFilter === 'today_in') ? 'bg-emerald-700 text-white font-bold' : 'text-emerald-700 hover:bg-emerald-50'; ?>">Arriving Today (<?php echo $stats['today_in']; ?>)</a>
                <a href="bookings.php?status_filter=<?php echo urlencode($statusFilter); ?>&date_filter=in_house" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold <?php echo ($dateFilter === 'in_house') ? 'bg-blue-700 text-white font-bold' : 'text-blue-700 hover:bg-blue-50'; ?>">In-House</a>
                <a href="bookings.php?status_filter=<?php echo urlencode($statusFilter); ?>&date_filter=upcoming" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold <?php echo ($dateFilter === 'upcoming') ? 'bg-purple-700 text-white font-bold' : 'text-purple-700 hover:bg-purple-50'; ?>">Upcoming</a>
            </div>
        </div>

        <!-- Search Input Form -->
        <form action="bookings.php" method="GET" class="flex flex-col sm:flex-row gap-2 sm:gap-2.5">
            <input type="hidden" name="status_filter" value="<?php echo htmlspecialchars($statusFilter); ?>">
            <input type="hidden" name="date_filter" value="<?php echo htmlspecialchars($dateFilter); ?>">
            
            <div class="flex-grow relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Booking # (e.g. RR-2026-3630), Guest Name, Phone, Room..." class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl pl-9 pr-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full sm:w-auto btn-gold px-5 sm:px-6 py-2.5 rounded-xl text-xs font-bold uppercase shadow-sm flex items-center justify-center space-x-1.5">
                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                    <span>Search</span>
                </button>
                <?php if (!empty($search) || $statusFilter !== 'all' || $dateFilter !== 'all'): ?>
                    <a href="bookings.php" class="px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-500 hover:bg-slate-100 text-xs font-bold transition flex items-center space-x-1 shrink-0" title="Reset All Filters">
                        <i class="fa-solid fa-rotate-left text-xs"></i>
                        <span>Reset</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- 4. UPGRADED LUXURY BOOKINGS CONTAINER -->
    <div class="royal-card overflow-hidden shadow-sm border border-[#ebd9c8]">
        
        <!-- DESKTOP / TABLET VIEW (Visible >= lg screen) -->
        <div class="hidden lg:block overflow-x-auto custom-scrollbar">
            <table class="w-full min-w-[980px] text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#2b0e14] text-[#f3cf8a] font-bold uppercase text-[10px] tracking-wider border-b border-[#d4a359]/30">
                        <th class="py-4 px-4 w-[160px]">BOOKING REF & SOURCE</th>
                        <th class="py-4 px-4 w-[230px]">GUEST INFORMATION</th>
                        <th class="py-4 px-4 w-[200px]">ROOM & GUESTS</th>
                        <th class="py-4 px-4 w-[180px]">STAY DURATION</th>
                        <th class="py-4 px-4 w-[120px]">AMOUNT</th>
                        <th class="py-4 px-4 w-[120px]">PAYMENT</th>
                        <th class="py-4 px-4 w-[130px]">BOOKING STATUS</th>
                        <th class="py-4 px-4 text-right w-[150px]">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#f2e7db]">
                    <?php if (empty($filteredBookings)): ?>
                        <tr>
                            <td colspan="8" class="py-16 text-center text-slate-400 space-y-3">
                                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center text-slate-300 text-2xl">
                                    <i class="fa-solid fa-calendar-xmark"></i>
                                </div>
                                <h4 class="font-serif text-base font-bold text-slate-700">No booking records found</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto">Try clearing your search filters or click the "+ Walk-In Booking" button to create a new reservation.</p>
                                <div class="pt-2">
                                    <a href="bookings.php" class="px-5 py-2 rounded-full border border-[#2b0e14] text-[#2b0e14] text-xs font-bold hover:bg-[#2b0e14] hover:text-[#f3cf8a] transition inline-flex items-center space-x-1">
                                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                                        <span>Reset Filters</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($filteredBookings as $b): ?>
                            <?php
                            $st = $b['booking_status'] ?? 'Confirmed';
                            
                            // Status badge colors
                            $stBadge = 'bg-emerald-50 text-emerald-800 border-emerald-300 ring-1 ring-emerald-500/20';
                            $stIcon = 'fa-solid fa-circle-check text-emerald-600';
                            if ($st === 'Pending') {
                                $stBadge = 'bg-amber-50 text-amber-800 border-amber-300 ring-1 ring-amber-500/20';
                                $stIcon = 'fa-solid fa-clock text-amber-600';
                            } elseif ($st === 'Cancelled') {
                                $stBadge = 'bg-rose-50 text-rose-800 border-rose-300 ring-1 ring-rose-500/20';
                                $stIcon = 'fa-solid fa-ban text-rose-600';
                            } elseif ($st === 'Checked-In') {
                                $stBadge = 'bg-blue-50 text-blue-800 border-blue-300 ring-1 ring-blue-500/20';
                                $stIcon = 'fa-solid fa-key text-blue-600';
                            } elseif ($st === 'Checked-Out') {
                                $stBadge = 'bg-slate-100 text-slate-700 border-slate-300 ring-1 ring-slate-400/20';
                                $stIcon = 'fa-solid fa-door-closed text-slate-500';
                            }

                            // Payment badge
                            $paySt = $b['payment_status'] ?? 'Pending';
                            $isPaid = ($paySt === 'Paid');
                            
                            // Dynamic Stay Status Badge
                            $cIn = $b['check_in'] ?? '';
                            $cOut = $b['check_out'] ?? '';
                            $stayBadge = '';
                            if ($st === 'Cancelled') {
                                $stayBadge = '<span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200"><i class="fa-solid fa-circle-xmark text-[8px]"></i><span>Cancelled</span></span>';
                            } elseif ($st === 'Checked-Out') {
                                $stayBadge = '<span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200"><i class="fa-solid fa-check text-[8px]"></i><span>Completed</span></span>';
                            } elseif ($st === 'Checked-In') {
                                if ($cOut === $today) {
                                    $stayBadge = '<span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse"><i class="fa-solid fa-person-walking-arrow-right text-[8px]"></i><span>Departing Today</span></span>';
                                } else {
                                    $stayBadge = '<span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-blue-100 text-blue-800 border border-blue-300"><i class="fa-solid fa-bed text-[8px]"></i><span>In-House</span></span>';
                                }
                            } elseif ($cIn === $today) {
                                $stayBadge = '<span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 animate-pulse"><i class="fa-solid fa-bell text-[8px]"></i><span>Arriving Today</span></span>';
                            } elseif ($cIn > $today) {
                                $stayBadge = '<span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-purple-50 text-purple-700 border border-purple-200"><i class="fa-regular fa-clock text-[8px]"></i><span>Upcoming</span></span>';
                            } else {
                                $stayBadge = '<span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200"><i class="fa-solid fa-calendar text-[8px]"></i><span>Past Stay</span></span>';
                            }

                            // Booking Source detection
                            $isWalkIn = (stripos($b['special_requests'] ?? '', 'Walk-In') !== false || stripos($b['special_requests'] ?? '', 'Desk') !== false);
                            
                            // WhatsApp message text
                            $waMessage = urlencode("Namaskar " . $b['guest_name'] . " Ji, Greetings from RAJ RESIDENCY, Koraput! Your room reservation #" . $b['booking_number'] . " for " . $b['room_name'] . " (Check-in: " . date('d M Y', strtotime($b['check_in'])) . " to " . date('d M Y', strtotime($b['check_out'])) . ") is confirmed. Total: ₹" . number_format($b['total_amount'], 0) . ". Hotel Contact: " . $settings['hotel_phone'] . ", Post Office Road, Koraput. We look forward to hosting you!");
                            ?>
                            <tr class="hover:bg-[#fcfaf7] transition duration-150">
                                
                                <!-- 1. BOOKING REF & SOURCE -->
                                <td class="py-4 px-4 align-top">
                                    <div class="flex items-center space-x-1.5">
                                        <div class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-[#2b0e14] text-[#f3cf8a] font-mono text-xs font-black shadow-sm border border-[#d4a359]/30">
                                            <i class="fa-solid fa-receipt text-[10px] text-[#d4a359]"></i>
                                            <span><?php echo htmlspecialchars($b['booking_number']); ?></span>
                                        </div>
                                        <button type="button" onclick="copyBookingRef('<?php echo htmlspecialchars($b['booking_number']); ?>', this)" class="text-slate-400 hover:text-[#b88738] p-1 rounded transition" title="Copy Reference #">
                                            <i class="fa-regular fa-copy text-xs"></i>
                                        </button>
                                    </div>

                                    <!-- Source Badge & Timestamp -->
                                    <div class="mt-1.5 space-y-1">
                                        <?php if ($isWalkIn): ?>
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <i class="fa-solid fa-bell-concierge text-[8px]"></i>
                                                <span>Front Desk Walk-In</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[9px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                <i class="fa-solid fa-globe text-[8px]"></i>
                                                <span>Online Website</span>
                                            </span>
                                        <?php endif; ?>

                                        <div class="text-[10px] text-slate-500 font-medium flex items-center space-x-1 pt-0.5">
                                            <i class="fa-regular fa-clock text-[9px] text-[#d4a359]"></i>
                                            <span><?php echo format_datetime($b['created_at']); ?></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 2. GUEST INFORMATION -->
                                <td class="py-4 px-4 align-top">
                                    <div class="flex items-start space-x-2.5">
                                        <!-- Guest Avatar Monogram -->
                                        <div class="w-9 h-9 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center font-black text-xs shrink-0 shadow-sm border border-[#d4a359]/40 mt-0.5">
                                            <?php echo strtoupper(substr($b['guest_name'] ?? 'G', 0, 1)); ?>
                                        </div>
                                        
                                        <div class="space-y-0.5">
                                            <strong class="text-slate-900 text-xs font-bold block leading-snug"><?php echo htmlspecialchars($b['guest_name']); ?></strong>
                                            
                                            <!-- Phone & WhatsApp -->
                                            <div class="flex items-center space-x-2 text-[11px] pt-0.5">
                                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $b['guest_phone']); ?>" class="text-[#b88738] hover:underline flex items-center space-x-1 font-bold" title="Click to Call Guest">
                                                    <i class="fa-solid fa-phone text-[9px]"></i>
                                                    <span><?php echo htmlspecialchars($b['guest_phone']); ?></span>
                                                </a>
                                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $b['guest_phone']); ?>?text=<?php echo $waMessage; ?>" target="_blank" class="text-emerald-600 hover:text-emerald-700 hover:scale-110 transition inline-flex items-center" title="Send WhatsApp Message">
                                                    <i class="fa-brands fa-whatsapp text-sm font-bold"></i>
                                                </a>
                                            </div>

                                            <?php if (!empty($b['guest_email'])): ?>
                                                <span class="text-[10px] text-slate-400 block truncate max-w-[180px]"><?php echo htmlspecialchars($b['guest_email']); ?></span>
                                            <?php endif; ?>

                                            <?php if (!empty($b['id_proof_number'])): ?>
                                                <div class="text-[9px] text-slate-500 font-medium pt-0.5 flex items-center space-x-1">
                                                    <i class="fa-solid fa-id-card text-[9px] text-slate-400"></i>
                                                    <span><?php echo htmlspecialchars($b['id_proof_type'] ?? 'ID'); ?>: <?php echo htmlspecialchars($b['id_proof_number']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- 3. ROOM & GUESTS -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <strong class="text-slate-900 text-xs block font-bold leading-snug"><?php echo htmlspecialchars($b['room_name']); ?></strong>
                                        
                                        <div class="flex flex-wrap items-center gap-1.5 text-[10px] text-slate-600">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md bg-[#f5efe6] text-slate-700 font-medium border border-[#ebd9c8]">
                                                <i class="fa-solid fa-users text-[9px] text-[#d4a359]"></i>
                                                <span><?php echo (int)$b['adults']; ?> Adults<?php echo !empty($b['children']) ? ', ' . (int)$b['children'] . ' Child' : ''; ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <!-- 4. STAY DURATION & STATUS -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <div class="font-bold text-slate-800 text-[11px] leading-tight">
                                            <?php echo date('D, d M Y', strtotime($b['check_in'])); ?>
                                        </div>
                                        <div class="text-[10px] text-slate-400 leading-tight">
                                            to <?php echo date('D, d M Y', strtotime($b['check_out'])); ?>
                                        </div>
                                        <div class="flex items-center space-x-1.5 pt-0.5">
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-[#2b0e14] text-[#f3cf8a] text-[9px] font-black">
                                                <?php echo (int)$b['total_nights']; ?> Night(s)
                                            </span>
                                            <?php echo $stayBadge; ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- 5. AMOUNT -->
                                <td class="py-4 px-4 align-top">
                                    <div class="text-sm font-black text-[#2b0e14]">
                                        ₹<?php echo number_format($b['total_amount'], 0); ?>
                                    </div>
                                    <span class="text-[9px] text-slate-400 block mt-0.5">Includes GST</span>
                                    <span class="text-[9px] text-slate-500 block font-mono">₹<?php echo number_format($b['price_per_night'], 0); ?>/night</span>
                                </td>

                                <!-- 6. PAYMENT (1-CLICK TOGGLE) -->
                                <td class="py-4 px-4 align-top">
                                    <div class="space-y-1">
                                        <!-- 1-Click Payment Status Switcher -->
                                        <a href="bookings.php?id=<?php echo $b['id']; ?>&pay_toggle=1" onclick="return confirm('Switch payment status to <?php echo $isPaid ? 'Pending' : 'Paid'; ?>?');" class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border transition shadow-sm <?php echo $isPaid ? 'bg-emerald-50 text-emerald-800 border-emerald-300 hover:bg-emerald-100 ring-1 ring-emerald-400/20' : 'bg-amber-50 text-amber-800 border-amber-300 hover:bg-amber-100 ring-1 ring-amber-400/20'; ?>" title="Click to toggle Paid/Pending">
                                            <i class="fa-solid <?php echo $isPaid ? 'fa-circle-check text-emerald-600' : 'fa-clock text-amber-600'; ?> text-[9px]"></i>
                                            <span><?php echo htmlspecialchars($paySt); ?></span>
                                            <i class="fa-solid fa-rotate text-[8px] opacity-40 ml-0.5"></i>
                                        </a>

                                        <span class="block text-[10px] text-slate-500 font-medium truncate max-w-[110px]" title="<?php echo htmlspecialchars($b['payment_method'] ?? 'Pay at Hotel'); ?>">
                                            <?php echo htmlspecialchars($b['payment_method'] ?? 'Pay at Hotel'); ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- 7. BOOKING STATUS & QUICK SWITCHER -->
                                <td class="py-4 px-4 align-top">
                                    <div class="relative inline-block text-left group">
                                        <!-- Status Badge Button -->
                                        <button type="button" class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-[10px] font-black border shadow-sm transition hover:shadow <?php echo $stBadge; ?>">
                                            <i class="<?php echo $stIcon; ?> text-[9px]"></i>
                                            <span><?php echo htmlspecialchars($st); ?></span>
                                            <i class="fa-solid fa-chevron-down text-[8px] opacity-50 ml-0.5"></i>
                                        </button>

                                        <!-- Status Quick Change Dropdown on Hover/Click -->
                                        <div class="hidden group-hover:block absolute left-0 mt-1 w-44 bg-white rounded-2xl shadow-2xl border border-[#ebd9c8] p-1.5 z-30 space-y-0.5 text-left">
                                            <div class="px-2.5 py-1 text-[9px] font-bold uppercase text-slate-400 border-b border-slate-100 mb-1">Quick Change Status</div>
                                            
                                            <a href="bookings.php?id=<?php echo $b['id']; ?>&status=Confirmed" class="block px-2.5 py-1.5 text-[11px] rounded-xl hover:bg-emerald-50 text-emerald-800 font-bold flex items-center space-x-2">
                                                <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                                <span>Confirmed</span>
                                            </a>
                                            <a href="bookings.php?id=<?php echo $b['id']; ?>&status=Checked-In&pay=Paid" class="block px-2.5 py-1.5 text-[11px] rounded-xl hover:bg-blue-50 text-blue-800 font-bold flex items-center space-x-2">
                                                <i class="fa-solid fa-key text-blue-600 text-xs"></i>
                                                <span>Checked-In & Paid</span>
                                            </a>
                                            <a href="bookings.php?id=<?php echo $b['id']; ?>&status=Checked-Out" class="block px-2.5 py-1.5 text-[11px] rounded-xl hover:bg-slate-50 text-slate-800 font-bold flex items-center space-x-2">
                                                <i class="fa-solid fa-door-closed text-slate-600 text-xs"></i>
                                                <span>Checked-Out</span>
                                            </a>
                                            <a href="bookings.php?id=<?php echo $b['id']; ?>&status=Pending" class="block px-2.5 py-1.5 text-[11px] rounded-xl hover:bg-amber-50 text-amber-800 font-bold flex items-center space-x-2">
                                                <i class="fa-solid fa-clock text-amber-600 text-xs"></i>
                                                <span>Mark Pending</span>
                                            </a>
                                            <a href="bookings.php?id=<?php echo $b['id']; ?>&status=Cancelled" class="block px-2.5 py-1.5 text-[11px] rounded-xl hover:bg-rose-50 text-rose-800 font-bold flex items-center space-x-2">
                                                <i class="fa-solid fa-ban text-rose-600 text-xs"></i>
                                                <span>Cancel Booking</span>
                                            </a>
                                        </div>
                                    </div>
                                </td>

                                <!-- 8. ACTION BUTTONS -->
                                <td class="py-4 px-4 align-top text-right">
                                    <div class="inline-flex items-center space-x-1.5">
                                        
                                        <!-- View Dossier & Print Bill -->
                                        <button type="button" onclick="viewBookingDetails(<?php echo htmlspecialchars(json_encode($b)); ?>)" class="p-2 rounded-xl bg-[#2b0e14] text-[#f3cf8a] hover:bg-[#3d141d] hover:scale-105 transition text-xs shadow-sm" title="View Dossier & Print Invoice">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </button>

                                        <!-- WhatsApp Direct Message -->
                                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $b['guest_phone']); ?>?text=<?php echo $waMessage; ?>" target="_blank" class="p-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 hover:scale-105 transition text-xs border border-emerald-200 shadow-sm" title="Send WhatsApp Confirmation">
                                            <i class="fa-brands fa-whatsapp text-xs font-bold"></i>
                                        </a>

                                        <!-- Edit Booking Modal Button -->
                                        <button type="button" onclick="openEditBookingModal(<?php echo htmlspecialchars(json_encode($b)); ?>)" class="p-2 rounded-xl bg-[#f5efe6] text-[#2b0e14] hover:bg-[#ebd9c8] hover:scale-105 transition text-xs border border-[#ebd9c8] shadow-sm" title="Edit Reservation Details">
                                            <i class="fa-solid fa-pencil text-xs"></i>
                                        </button>

                                        <!-- 3-Dots Dropdown Menu -->
                                        <div class="relative inline-block text-left group">
                                            <button type="button" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs border border-slate-200">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </button>
                                            <div class="hidden group-hover:block absolute right-0 mt-1 w-48 bg-white rounded-2xl shadow-2xl border border-[#ebd9c8] p-1.5 z-30 space-y-1 text-left">
                                                <button type="button" onclick="viewBookingDetails(<?php echo htmlspecialchars(json_encode($b)); ?>)" class="w-full text-left px-3 py-1.5 text-[11px] rounded-xl hover:bg-slate-50 text-slate-800 font-bold flex items-center space-x-2">
                                                    <i class="fa-solid fa-print text-slate-600"></i>
                                                    <span>Print Guest Invoice</span>
                                                </button>
                                                <button type="button" onclick="openEditBookingModal(<?php echo htmlspecialchars(json_encode($b)); ?>)" class="w-full text-left px-3 py-1.5 text-[11px] rounded-xl hover:bg-slate-50 text-slate-800 font-bold flex items-center space-x-2">
                                                    <i class="fa-solid fa-pen-to-square text-slate-600"></i>
                                                    <span>Edit Guest Data</span>
                                                </button>
                                                <div class="border-t border-slate-100 my-1"></div>
                                                <a href="bookings.php?delete=<?php echo $b['id']; ?>" onclick="return confirm('Are you sure you want to permanently delete booking #<?php echo $b['booking_number']; ?>?');" class="block px-3 py-1.5 text-[11px] rounded-xl hover:bg-rose-50 text-rose-600 flex items-center space-x-2">
                                                    <i class="fa-solid fa-trash-can text-rose-500"></i>
                                                    <span>Delete Record</span>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- MOBILE RESERVATIONS CARD VIEW (Visible < lg screen) -->
        <div class="block lg:hidden divide-y divide-[#ebd9c8]">
            <?php if (empty($filteredBookings)): ?>
                <div class="p-8 text-center text-slate-400 space-y-2">
                    <i class="fa-solid fa-calendar-xmark text-3xl mb-1 block"></i>
                    <p class="font-bold text-slate-600 text-sm">No bookings found</p>
                    <a href="bookings.php" class="text-xs text-[#d4a359] font-bold underline inline-block pt-1">Reset Filters</a>
                </div>
            <?php else: ?>
                <?php foreach ($filteredBookings as $b): ?>
                    <?php
                    $st = $b['booking_status'] ?? 'Confirmed';
                    $stBadge = 'bg-emerald-50 text-emerald-800 border-emerald-300';
                    if ($st === 'Pending') $stBadge = 'bg-amber-50 text-amber-800 border-amber-300';
                    if ($st === 'Cancelled') $stBadge = 'bg-rose-50 text-rose-800 border-rose-300';
                    if ($st === 'Checked-In') $stBadge = 'bg-blue-50 text-blue-800 border-blue-300';
                    if ($st === 'Checked-Out') $stBadge = 'bg-slate-100 text-slate-700 border-slate-300';

                    $paySt = $b['payment_status'] ?? 'Pending';
                    $isPaid = ($paySt === 'Paid');
                    $isWalkIn = (stripos($b['special_requests'] ?? '', 'Walk-In') !== false || stripos($b['special_requests'] ?? '', 'Desk') !== false);
                    $waMessage = urlencode("Namaskar " . $b['guest_name'] . " Ji, Greetings from RAJ RESIDENCY! Your room booking #" . $b['booking_number'] . " for " . $b['room_name'] . " is confirmed.");
                    ?>
                    <div class="p-4 sm:p-5 space-y-3.5 hover:bg-[#fcfaf7] transition">
                        
                        <!-- Top Ref & Status Row -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-black text-xs text-[#2b0e14] bg-[#f5efe6] px-2.5 py-1 rounded-lg border border-[#ebd9c8]">
                                    <?php echo htmlspecialchars($b['booking_number']); ?>
                                </span>
                                <?php if ($isWalkIn): ?>
                                    <span class="text-[9px] font-bold bg-amber-50 text-amber-800 px-2 py-0.5 rounded border border-amber-200">Walk-In</span>
                                <?php else: ?>
                                    <span class="text-[9px] font-bold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-200">Website</span>
                                <?php endif; ?>
                            </div>

                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black border <?php echo $stBadge; ?>">
                                <?php echo htmlspecialchars($st); ?>
                            </span>
                        </div>

                        <!-- Guest & Stay Schedule -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="space-y-1">
                                <strong class="text-sm font-bold text-slate-900 block"><?php echo htmlspecialchars($b['guest_name']); ?></strong>
                                <div class="text-xs text-[#b88738] font-bold"><?php echo htmlspecialchars($b['room_name']); ?></div>
                                <div class="text-[11px] text-slate-500 font-medium">
                                    <?php echo date('d M Y', strtotime($b['check_in'])); ?> &rarr; <?php echo date('d M Y', strtotime($b['check_out'])); ?>
                                    <span class="font-bold text-[#2b0e14] ml-1">(<?php echo (int)$b['total_nights']; ?>N)</span>
                                </div>
                            </div>

                            <!-- Amount & Payment Toggle -->
                            <div class="text-right space-y-1 shrink-0">
                                <div class="text-base font-black text-[#2b0e14]">₹<?php echo number_format($b['total_amount'], 0); ?></div>
                                <a href="bookings.php?id=<?php echo $b['id']; ?>&pay_toggle=1" onclick="return confirm('Toggle payment status?');" class="inline-flex items-center space-x-1 px-2 py-0.5 rounded text-[10px] font-bold border <?php echo $isPaid ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-300'; ?>">
                                    <i class="fa-solid <?php echo $isPaid ? 'fa-check' : 'fa-clock'; ?> text-[8px]"></i>
                                    <span><?php echo htmlspecialchars($paySt); ?></span>
                                </a>
                            </div>
                        </div>

                        <!-- Action Toolbar (Call, WhatsApp, View Invoice, Edit, Delete) -->
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap">
                            <div class="flex items-center space-x-2">
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $b['guest_phone']); ?>" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-semibold flex items-center space-x-1.5">
                                    <i class="fa-solid fa-phone text-[10px] text-[#d4a359]"></i>
                                    <span>Call</span>
                                </a>
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $b['guest_phone']); ?>?text=<?php echo $waMessage; ?>" target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold flex items-center space-x-1.5 border border-emerald-200">
                                    <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                                    <span>WhatsApp</span>
                                </a>
                            </div>

                            <div class="flex items-center space-x-1.5">
                                <button type="button" onclick="viewBookingDetails(<?php echo htmlspecialchars(json_encode($b)); ?>)" class="p-2 rounded-xl bg-[#2b0e14] text-[#f3cf8a] text-xs" title="View Dossier & Print Invoice">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button type="button" onclick="openEditBookingModal(<?php echo htmlspecialchars(json_encode($b)); ?>)" class="p-2 rounded-xl bg-slate-100 text-slate-700 text-xs border border-slate-200" title="Edit Reservation">
                                    <i class="fa-solid fa-pencil"></i>
                                </button>
                                <a href="bookings.php?delete=<?php echo $b['id']; ?>" onclick="return confirm('Permanently delete booking #<?php echo $b['booking_number']; ?>?');" class="p-2 rounded-xl bg-rose-50 text-rose-600 text-xs border border-rose-200" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

</div>

<!-- ============================================================= -->
<!-- MODAL 1: NEW WALK-IN BOOKING (FRONT DESK)                     -->
<!-- ============================================================= -->
<div id="manual-booking-modal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-3 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-xl w-full p-4 sm:p-7 shadow-2xl border border-[#ebd9c8] space-y-4 max-h-[92vh] overflow-y-auto custom-scrollbar">
        
        <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3.5">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xs sm:text-sm shadow-md shrink-0">
                    <i class="fa-solid fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="font-serif text-base sm:text-lg font-bold text-[#2b0e14]">New Walk-In / Front Desk Reservation</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-light">Create instant reservation for front desk walk-in guests</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('manual-booking-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="bookings.php" method="POST" class="space-y-3.5" id="manual-booking-form">
            <input type="hidden" name="action" value="manual_booking">

            <!-- Room Selector -->
            <div>
                <label for="room_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Select Room Type *</label>
                <select id="room_id" name="room_id" required onchange="calculateWalkinTotal()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    <option value="">-- Choose Room --</option>
                    <?php foreach ($allRooms as $r): ?>
                        <option value="<?php echo $r['id']; ?>" data-price="<?php echo $r['price_per_night']; ?>">
                            <?php echo htmlspecialchars($r['name']); ?> (₹<?php echo number_format($r['price_per_night'], 0); ?>/night)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Guest Name & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="guest_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Guest Full Name *</label>
                    <input type="text" id="guest_name" name="guest_name" required placeholder="e.g. Ramesh Chandra" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="guest_phone" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Phone / WhatsApp Number *</label>
                    <input type="tel" id="guest_phone" name="guest_phone" required placeholder="e.g. 08328910274" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Guest Email & ID Proof -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="guest_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
                    <input type="email" id="guest_email" name="guest_email" placeholder="guest@example.com" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="id_proof_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">ID Proof Type</label>
                    <select id="id_proof_type" name="id_proof_type" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="Aadhaar Card">Aadhaar Card</option>
                        <option value="Driving License">Driving License</option>
                        <option value="Voter ID">Voter ID</option>
                        <option value="Passport">Passport</option>
                    </select>
                </div>
                <div>
                    <label for="id_proof_number" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">ID Number</label>
                    <input type="text" id="id_proof_number" name="id_proof_number" placeholder="XXXX-XXXX-XXXX" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Dates & Guests -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                <div>
                    <label for="w_check_in" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Check-In *</label>
                    <input type="date" id="w_check_in" name="check_in" required value="<?php echo $today; ?>" onchange="calculateWalkinTotal()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="w_check_out" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Check-Out *</label>
                    <input type="date" id="w_check_out" name="check_out" required value="<?php echo $tomorrow; ?>" onchange="calculateWalkinTotal()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="w_adults" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Adults</label>
                    <input type="number" id="w_adults" name="adults" min="1" max="10" value="2" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="w_children" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Children</label>
                    <input type="number" id="w_children" name="children" min="0" max="10" value="0" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Payment & Total -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3">
                <div>
                    <label for="w_total_amount" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Total Price (₹) *</label>
                    <input type="number" id="w_total_amount" name="total_amount" required value="2499" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-black text-[#2b0e14] focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="w_payment_method" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Payment Mode</label>
                    <select id="w_payment_method" name="payment_method" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="Cash at Front Desk">Cash at Front Desk</option>
                        <option value="UPI / QR Code">UPI / QR Code</option>
                        <option value="Debit/Credit Card">Debit/Credit Card</option>
                        <option value="Pay at Hotel">Pay at Hotel</option>
                    </select>
                </div>
                <div>
                    <label for="w_payment_status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Payment Status</label>
                    <select id="w_payment_status" name="payment_status" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="Paid" selected>Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-3 border-t border-[#ebd9c8] flex items-center justify-end space-x-3">
                <button type="button" onclick="document.getElementById('manual-booking-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="btn-gold px-7 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Confirm & Book Room</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL 2: EDIT RESERVATION DETAILS                             -->
<!-- ============================================================= -->
<div id="edit-booking-modal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-3 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-xl w-full p-4 sm:p-7 shadow-2xl border border-[#ebd9c8] space-y-4 max-h-[92vh] overflow-y-auto custom-scrollbar">
        
        <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3.5">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xs sm:text-sm shadow-md shrink-0">
                    <i class="fa-solid fa-pencil"></i>
                </div>
                <div>
                    <h3 class="font-serif text-base sm:text-lg font-bold text-[#2b0e14]">Edit Reservation Details</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-light" id="edit-booking-header-sub">Update booking details</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('edit-booking-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="bookings.php" method="POST" class="space-y-3.5" id="edit-booking-form">
            <input type="hidden" name="action" value="edit_booking">
            <input type="hidden" name="booking_id" id="edit_booking_id" value="">

            <!-- Room Selector -->
            <div>
                <label for="edit_room_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Room Type *</label>
                <select id="edit_room_id" name="room_id" required onchange="calculateEditTotal()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    <?php foreach ($allRooms as $r): ?>
                        <option value="<?php echo $r['id']; ?>" data-price="<?php echo $r['price_per_night']; ?>">
                            <?php echo htmlspecialchars($r['name']); ?> (₹<?php echo number_format($r['price_per_night'], 0); ?>/night)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Guest Name & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit_guest_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Guest Full Name *</label>
                    <input type="text" id="edit_guest_name" name="guest_name" required class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="edit_guest_phone" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Phone / WhatsApp *</label>
                    <input type="tel" id="edit_guest_phone" name="guest_phone" required class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Guest Email & ID Proof -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label for="edit_guest_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
                    <input type="email" id="edit_guest_email" name="guest_email" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="edit_id_proof_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">ID Proof</label>
                    <select id="edit_id_proof_type" name="id_proof_type" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="Aadhaar Card">Aadhaar Card</option>
                        <option value="Driving License">Driving License</option>
                        <option value="Voter ID">Voter ID</option>
                        <option value="Passport">Passport</option>
                    </select>
                </div>
                <div>
                    <label for="edit_id_proof_number" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">ID Number</label>
                    <input type="text" id="edit_id_proof_number" name="id_proof_number" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Dates & Guests -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3">
                <div>
                    <label for="edit_check_in" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Check-In *</label>
                    <input type="date" id="edit_check_in" name="check_in" required onchange="calculateEditTotal()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="edit_check_out" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Check-Out *</label>
                    <input type="date" id="edit_check_out" name="check_out" required onchange="calculateEditTotal()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="edit_adults" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Adults</label>
                    <input type="number" id="edit_adults" name="adults" min="1" max="10" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="edit_children" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Children</label>
                    <input type="number" id="edit_children" name="children" min="0" max="10" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-2.5 sm:px-3 py-2 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Price & Statuses -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                <div>
                    <label for="edit_total_amount" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Total Amount (₹) *</label>
                    <input type="number" id="edit_total_amount" name="total_amount" required class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-black text-[#2b0e14] focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="edit_payment_method" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Payment Mode</label>
                    <select id="edit_payment_method" name="payment_method" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="Cash at Front Desk">Cash at Front Desk</option>
                        <option value="UPI / QR Code">UPI / QR Code</option>
                        <option value="Debit/Credit Card">Debit/Credit Card</option>
                        <option value="Pay at Hotel">Pay at Hotel</option>
                    </select>
                </div>
                <div>
                    <label for="edit_payment_status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Payment Status</label>
                    <select id="edit_payment_status" name="payment_status" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label for="edit_booking_status" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Booking Status</label>
                    <select id="edit_booking_status" name="booking_status" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="Confirmed">Confirmed</option>
                        <option value="Checked-In">Checked-In</option>
                        <option value="Checked-Out">Checked-Out</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Special Requests -->
            <div>
                <label for="edit_special_requests" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Special Requests / Notes</label>
                <textarea id="edit_special_requests" name="special_requests" rows="2" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]"></textarea>
            </div>

            <!-- Submit -->
            <div class="pt-3 border-t border-[#ebd9c8] flex items-center justify-end space-x-3">
                <button type="button" onclick="document.getElementById('edit-booking-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="btn-gold px-7 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ============================================================= -->
<!-- MODAL 3: ROYAL PRINTABLE INVOICE & GUEST DOSSIER              -->
<!-- ============================================================= -->
<div id="booking-modal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-3 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-xl w-full p-4 sm:p-7 shadow-2xl border border-[#ebd9c8] space-y-5 max-h-[92vh] overflow-y-auto custom-scrollbar" id="printable-invoice-area">
        
        <!-- Voucher Header (Branded Royal Emblem) -->
        <div class="flex items-center justify-between border-b-2 border-[#d4a359]/40 pb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-[#2b0e14] border-2 border-[#d4a359] text-[#f3cf8a] flex flex-col items-center justify-center shadow-md">
                    <i class="fa-solid fa-crown text-[10px] text-[#d4a359]"></i>
                    <span class="font-royal text-xs font-black leading-none">RR</span>
                </div>
                <div>
                    <h3 class="font-royal text-base sm:text-lg font-black tracking-wider uppercase text-[#2b0e14]"><?php echo htmlspecialchars($settings['hotel_name']); ?></h3>
                    <p class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($settings['hotel_address']); ?></p>
                    <p class="text-[10px] text-[#b88738] font-bold">Desk Phone: <?php echo htmlspecialchars($settings['hotel_phone']); ?></p>
                </div>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] font-black uppercase bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/40 shadow-sm" id="modal-status-pill">CONFIRMED</span>
                <span class="block text-[9px] text-slate-400 mt-1 font-mono font-bold" id="modal-booking-ref-badge">#REF</span>
            </div>
        </div>

        <div class="space-y-4 text-xs">
            
            <!-- Guest & Reservation Breakdown -->
            <div class="grid grid-cols-2 gap-4 p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8]">
                <div class="space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Guest Information</span>
                    <strong class="text-sm text-[#2b0e14] font-bold block" id="modal-guest-name"></strong>
                    <div class="text-slate-600 text-[11px]" id="modal-guest-phone"></div>
                    <div class="text-slate-400 text-[10px]" id="modal-guest-email"></div>
                </div>

                <div class="space-y-1 text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">ID Proof / Verification</span>
                    <strong class="text-slate-800 text-[11px] block" id="modal-guest-idproof"></strong>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mt-2">Booked On (IST)</span>
                    <div class="text-slate-600 text-[10px] font-mono" id="modal-booking-date"></div>
                </div>
            </div>

            <!-- Stay Schedule & Room Details -->
            <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] space-y-3">
                <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-2">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Reserved Room</span>
                        <strong class="text-[#2b0e14] text-sm font-bold" id="modal-room-name"></strong>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-500 block">Guests & Nights</span>
                        <span class="text-slate-800 font-bold" id="modal-stay-duration"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-1">
                    <div class="p-2.5 rounded-xl bg-white border border-[#ebd9c8]">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Check-In Date</span>
                        <strong class="text-[#2b0e14] text-xs font-bold" id="modal-check-in"></strong>
                        <span class="text-[9px] text-slate-500 block">From 12:00 PM (IST)</span>
                    </div>
                    <div class="p-2.5 rounded-xl bg-white border border-[#ebd9c8] text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Check-Out Date</span>
                        <strong class="text-[#2b0e14] text-xs font-bold" id="modal-check-out"></strong>
                        <span class="text-[9px] text-slate-500 block">Until 11:00 AM (IST)</span>
                    </div>
                </div>
            </div>

            <!-- Pricing & Payment Invoice -->
            <div class="p-4 rounded-2xl bg-[#2b0e14] text-white border border-[#d4a359]/40 space-y-2">
                <div class="flex items-center justify-between text-slate-300 text-xs">
                    <span>Room Rate per Night:</span>
                    <span id="modal-price-night" class="font-mono"></span>
                </div>
                <div class="flex items-center justify-between text-slate-300 text-xs">
                    <span>Payment Mode & Status:</span>
                    <span id="modal-payment-method" class="font-bold text-[#f3cf8a]"></span>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-[#d4a359]/30 text-sm font-bold">
                    <span class="text-[#f3cf8a]">Total Amount (Inc. GST):</span>
                    <span class="text-xl text-white font-black" id="modal-total-amount"></span>
                </div>
            </div>

            <!-- Special Requests -->
            <div id="modal-special-container" class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] uppercase font-bold text-slate-500 block mb-0.5">Special Guest Instructions:</span>
                <p class="text-slate-700 italic" id="modal-special-requests"></p>
            </div>

        </div>

        <!-- Voucher Footer & Print Controls -->
        <div class="pt-4 border-t border-[#ebd9c8] flex items-center justify-between gap-3 no-print">
            <button type="button" onclick="printInvoice()" class="px-5 py-2.5 rounded-full border border-[#2b0e14] text-[#2b0e14] text-xs font-bold hover:bg-[#2b0e14] hover:text-[#f3cf8a] transition inline-flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Official Invoice</span>
            </button>
            <button type="button" onclick="document.getElementById('booking-modal').classList.add('hidden')" class="btn-gold px-7 py-2.5 rounded-full text-xs font-bold uppercase">Close</button>
        </div>

    </div>
</div>

<!-- FLOATING TOAST NOTIFICATION -->
<div id="copy-toast" class="fixed bottom-6 right-6 bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359] px-4 py-2.5 rounded-xl shadow-2xl text-xs font-bold transition duration-300 transform translate-y-20 opacity-0 z-50 flex items-center space-x-2">
    <i class="fa-solid fa-circle-check text-emerald-400"></i>
    <span id="copy-toast-text">Reference copied to clipboard!</span>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printable-invoice-area, #printable-invoice-area * {
        visibility: visible;
    }
    #printable-invoice-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100%;
        box-shadow: none;
        border: none;
    }
    .no-print {
        display: none !important;
    }
}
</style>

<script>
    // 1-Click Copy Reference to Clipboard with Toast Notification
    function copyBookingRef(ref, btnElement) {
        navigator.clipboard.writeText(ref).then(function() {
            const icon = btnElement.querySelector('i');
            if (icon) {
                icon.className = 'fa-solid fa-check text-emerald-600 text-xs';
                setTimeout(() => {
                    icon.className = 'fa-regular fa-copy text-xs';
                }, 2000);
            }
            
            const toast = document.getElementById('copy-toast');
            document.getElementById('copy-toast-text').innerText = 'Copied ' + ref + ' to clipboard!';
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 2500);
        });
    }

    // Auto-calculate walk-in price
    function calculateWalkinTotal() {
        const select = document.getElementById('room_id');
        const opt = select.options[select.selectedIndex];
        const pricePerNight = parseFloat(opt.getAttribute('data-price') || 0);
        
        const inDate = new Date(document.getElementById('w_check_in').value);
        const outDate = new Date(document.getElementById('w_check_out').value);
        
        let nights = 1;
        if (!isNaN(inDate.getTime()) && !isNaN(outDate.getTime())) {
            const diffTime = Math.abs(outDate - inDate);
            nights = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
        }
        
        if (pricePerNight > 0) {
            document.getElementById('w_total_amount').value = pricePerNight * nights;
        }
    }

    // Auto-calculate edit modal price
    function calculateEditTotal() {
        const select = document.getElementById('edit_room_id');
        const opt = select.options[select.selectedIndex];
        const pricePerNight = parseFloat(opt.getAttribute('data-price') || 0);
        
        const inDate = new Date(document.getElementById('edit_check_in').value);
        const outDate = new Date(document.getElementById('edit_check_out').value);
        
        let nights = 1;
        if (!isNaN(inDate.getTime()) && !isNaN(outDate.getTime())) {
            const diffTime = Math.abs(outDate - inDate);
            nights = Math.max(1, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
        }
        
        if (pricePerNight > 0) {
            document.getElementById('edit_total_amount').value = pricePerNight * nights;
        }
    }

    // Open Edit Booking Modal and populate values
    function openEditBookingModal(b) {
        document.getElementById('edit_booking_id').value = b.id;
        document.getElementById('edit-booking-header-sub').innerText = 'Editing reservation #' + b.booking_number;
        document.getElementById('edit_guest_name').value = b.guest_name || '';
        document.getElementById('edit_guest_phone').value = b.guest_phone || '';
        document.getElementById('edit_guest_email').value = b.guest_email || '';
        document.getElementById('edit_id_proof_type').value = b.id_proof_type || 'Aadhaar Card';
        document.getElementById('edit_id_proof_number').value = b.id_proof_number || '';
        document.getElementById('edit_check_in').value = b.check_in || '';
        document.getElementById('edit_check_out').value = b.check_out || '';
        document.getElementById('edit_adults').value = b.adults || 2;
        document.getElementById('edit_children').value = b.children || 0;
        document.getElementById('edit_total_amount').value = b.total_amount || 0;
        document.getElementById('edit_payment_method').value = b.payment_method || 'Pay at Hotel';
        document.getElementById('edit_payment_status').value = b.payment_status || 'Pending';
        document.getElementById('edit_booking_status').value = b.booking_status || 'Confirmed';
        document.getElementById('edit_special_requests').value = b.special_requests || '';

        // Select Room
        const roomSelect = document.getElementById('edit_room_id');
        for (let i = 0; i < roomSelect.options.length; i++) {
            if (parseInt(roomSelect.options[i].value) === parseInt(b.room_id)) {
                roomSelect.selectedIndex = i;
                break;
            }
        }

        document.getElementById('edit-booking-modal').classList.remove('hidden');
    }

    // View Dossier & Invoice
    function viewBookingDetails(b) {
        document.getElementById('modal-booking-ref-badge').innerText = '#' + b.booking_number;
        document.getElementById('modal-status-pill').innerText = (b.booking_status || 'CONFIRMED').toUpperCase();
        document.getElementById('modal-booking-date').innerText = b.created_at;
        document.getElementById('modal-guest-name').innerText = b.guest_name;
        document.getElementById('modal-guest-phone').innerText = b.guest_phone;
        document.getElementById('modal-guest-email').innerText = b.guest_email || 'No email provided';
        document.getElementById('modal-guest-idproof').innerText = (b.id_proof_type || 'Aadhaar') + (b.id_proof_number ? ' - ' + b.id_proof_number : '');
        document.getElementById('modal-room-name').innerText = b.room_name;
        document.getElementById('modal-check-in').innerText = b.check_in;
        document.getElementById('modal-check-out').innerText = b.check_out;
        document.getElementById('modal-stay-duration').innerText = b.total_nights + ' Night(s) | ' + b.adults + ' Adults' + (b.children > 0 ? ', ' + b.children + ' Child' : '');
        document.getElementById('modal-price-night').innerText = '₹' + Number(b.price_per_night).toLocaleString();
        document.getElementById('modal-payment-method').innerText = (b.payment_method || 'Pay at Hotel') + ' (' + (b.payment_status || 'Pending') + ')';
        document.getElementById('modal-total-amount').innerText = '₹' + Number(b.total_amount).toLocaleString();
        document.getElementById('modal-special-requests').innerText = b.special_requests || 'No special requests submitted.';

        document.getElementById('booking-modal').classList.remove('hidden');
    }

    function printInvoice() {
        window.print();
    }
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
