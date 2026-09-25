<?php
// booking.php - Complete Ultra-Luxury Reservation Flow for Raj Residency
require_once __DIR__ . '/includes/functions.php';

$allAvailableRooms = get_all_rooms(null, true);
$roomId = isset($_REQUEST['room_id']) ? (int)$_REQUEST['room_id'] : 1;
$room = get_room_by_id($roomId);

if (!$room && !empty($allAvailableRooms)) {
    $room = $allAvailableRooms[0];
    $roomId = $room['id'];
}

$settings = get_settings();
$taxRate = (float)($settings['tax_percentage'] ?? 12);
$hotelUpiId = !empty($settings['hotel_upi_id']) ? $settings['hotel_upi_id'] : '8328910274@paytm';

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$checkIn = isset($_REQUEST['check_in']) && !empty($_REQUEST['check_in']) ? sanitize($_REQUEST['check_in']) : $today;
$checkOut = isset($_REQUEST['check_out']) && !empty($_REQUEST['check_out']) ? sanitize($_REQUEST['check_out']) : $tomorrow;
$guests = isset($_REQUEST['guests']) ? (int)$_REQUEST['guests'] : 2;

// Calculate initial nights
$d1 = new DateTime($checkIn);
$d2 = new DateTime($checkOut);
$interval = $d1->diff($d2);
$nights = max(1, (int)$interval->days);

// Handle POST Booking submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_booking') {
    $postRoomId = (int)($_POST['room_id'] ?? $roomId);
    $selectedRoom = get_room_by_id($postRoomId) ?: $room;
    
    $guestName = sanitize($_POST['guest_name'] ?? '');
    $guestEmail = sanitize($_POST['guest_email'] ?? '');
    $guestPhone = sanitize($_POST['guest_phone'] ?? '');
    $idProofType = sanitize($_POST['id_proof_type'] ?? 'Aadhaar Card');
    $idProofNumber = sanitize($_POST['id_proof_number'] ?? '');
    $checkInPost = sanitize($_POST['check_in'] ?? $today);
    $checkOutPost = sanitize($_POST['check_out'] ?? $tomorrow);
    $adults = (int)($_POST['adults'] ?? 2);
    $children = (int)($_POST['children'] ?? 0);
    $paymentMethod = sanitize($_POST['payment_method'] ?? 'Pay at Hotel');
    $utrNumber = sanitize($_POST['utr_number'] ?? '');
    $specialRequests = sanitize($_POST['special_requests'] ?? '');

    if (empty($guestName)) $errors[] = "Please enter primary guest name.";
    if (empty($guestPhone)) $errors[] = "Please enter mobile / WhatsApp phone number.";
    if (empty($checkInPost) || empty($checkOutPost)) $errors[] = "Please select valid check-in and check-out dates.";

    // Handle Payment Receipt Upload if UPI selected
    $receiptPath = '';
    if ($paymentMethod === 'UPI / QR Code') {
        if (isset($_FILES['payment_receipt_file']) && $_FILES['payment_receipt_file']['error'] === UPLOAD_ERR_OK) {
            $uploadedReceipt = upload_media_file($_FILES['payment_receipt_file'], 'receipts');
            if ($uploadedReceipt) {
                $receiptPath = $uploadedReceipt;
            }
        }
    }

    if (empty($errors)) {
        $p1 = new DateTime($checkInPost);
        $p2 = new DateTime($checkOutPost);
        $postNights = max(1, (int)$p1->diff($p2)->days);
        
        $pricePerNight = (float)$selectedRoom['price_per_night'];
        $baseTotal = $pricePerNight * $postNights;
        $taxAmount = ($baseTotal * $taxRate) / 100;
        $grandTotal = $baseTotal + $taxAmount;

        // Combine UTR and Receipt in special requests for audit
        $auditNotes = $specialRequests;
        if (!empty($utrNumber)) {
            $auditNotes .= ($auditNotes ? " | " : "") . "UPI UTR / Ref: " . $utrNumber;
        }
        if (!empty($receiptPath)) {
            $auditNotes .= ($auditNotes ? " | " : "") . "Receipt: " . $receiptPath;
        }

        $paymentStatus = 'Pending';
        if ($paymentMethod === 'UPI / QR Code') {
            $paymentStatus = 'Paid (Pending Verification)';
        } elseif ($paymentMethod === 'Online Card / NetBanking') {
            $paymentStatus = 'Paid (Online)';
        }

        $bookingData = [
            'room_id' => $selectedRoom['id'],
            'room_name' => $selectedRoom['name'],
            'guest_name' => $guestName,
            'guest_email' => $guestEmail,
            'guest_phone' => $guestPhone,
            'id_proof_type' => $idProofType,
            'id_proof_number' => $idProofNumber,
            'check_in' => $checkInPost,
            'check_out' => $checkOutPost,
            'total_nights' => $postNights,
            'adults' => $adults,
            'children' => $children,
            'price_per_night' => $pricePerNight,
            'total_amount' => $grandTotal,
            'payment_method' => $paymentMethod,
            'payment_status' => $paymentStatus,
            'booking_status' => 'Confirmed',
            'special_requests' => $auditNotes
        ];

        $bookingNum = create_booking($bookingData);
        if ($bookingNum) {
            header("Location: booking-confirmation.php?booking_number=" . urlencode($bookingNum));
            exit;
        } else {
            $errors[] = "Could not process your booking. Please try again or call our reception directly.";
        }
    }
}

$pageTitle = "Reserve Room - " . ($room['name'] ?? 'Booking');
require_once __DIR__ . '/includes/header.php';
?>

<!-- 1. BREADCRUMB HERO BANNER -->
<section class="bg-[#2b0e14] text-white py-12 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-2">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase mb-1">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <a href="rooms.php" class="hover:underline">ROOMS</a>
            <span>/</span>
            <span class="text-white">ONLINE RESERVATION</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            Complete Your Stay Reservation
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-xl mx-auto font-light">
            Instant booking confirmation with 100% direct hotel price guarantee at <strong class="text-white"><?php echo htmlspecialchars($settings['hotel_name']); ?></strong>, Koraput.
        </p>
    </div>

    <!-- Decorative background glow -->
    <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-80 h-80 rounded-full bg-[#d4a359]/10 blur-3xl pointer-events-none"></div>
</section>


<!-- 2. MAIN RESERVATION CONTENT & INTERACTIVE BILL CALCULATOR -->
<section class="py-12 sm:py-16 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (!empty($errors)): ?>
            <div class="mb-8 p-5 rounded-3xl bg-rose-50 border-2 border-rose-300 text-rose-800 text-xs font-semibold shadow-sm">
                <div class="flex items-center space-x-2 font-bold mb-1 text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                    <span>Please correct the following errors:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 pl-2 font-normal">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- LEFT COLUMN: INTERACTIVE BOOKING FORM (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">
                <form id="main-booking-form" action="booking.php" method="POST" enctype="multipart/form-data" class="space-y-8">
                    <input type="hidden" name="action" value="confirm_booking">

                    <!-- STEP 1: SELECT ROOM & DATES -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ebd9c8] shadow-md space-y-6">
                        
                        <div class="flex items-center space-x-3 pb-4 border-b border-[#ebd9c8]">
                            <span class="w-8 h-8 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-black flex items-center justify-center border border-[#d4a359]/40 shadow-sm">1</span>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Select Accommodation & Dates</h3>
                                <p class="text-xs text-slate-500 font-light">Choose your preferred room type and stay schedule</p>
                            </div>
                        </div>

                        <!-- Room Selector Dropdown -->
                        <div>
                            <label for="room_id" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1 flex items-center justify-between">
                                <span>Selected Room Type *</span>
                                <a href="rooms.php" target="_blank" class="text-[10px] text-[#b88738] hover:underline font-bold">Compare All Rooms ↗</a>
                            </label>
                            <select name="room_id" id="room_id" onchange="onRoomChanged()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                                <?php foreach ($allAvailableRooms as $rOption): ?>
                                    <option value="<?php echo $rOption['id']; ?>" 
                                            data-price="<?php echo (float)$rOption['price_per_night']; ?>"
                                            data-name="<?php echo htmlspecialchars($rOption['name']); ?>"
                                            data-img="<?php echo htmlspecialchars($rOption['featured_image']); ?>"
                                            data-category="<?php echo htmlspecialchars($rOption['type_label']); ?>"
                                            data-bed="<?php echo htmlspecialchars($rOption['bed_type']); ?>"
                                            data-max="<?php echo (int)$rOption['max_guests']; ?>"
                                            <?php echo ($rOption['id'] == $room['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($rOption['name']); ?> — ₹<?php echo number_format($rOption['price_per_night'], 0); ?>/night (<?php echo htmlspecialchars($rOption['type_label']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Date Pickers Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="check_in" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1 flex items-center justify-between">
                                    <span>Check-In Date *</span>
                                    <i class="fa-regular fa-calendar text-[#d4a359]"></i>
                                </label>
                                <input type="date" 
                                       name="check_in" 
                                       id="check_in" 
                                       value="<?php echo htmlspecialchars($checkIn); ?>" 
                                       required 
                                       onchange="recalculateBill()" 
                                       class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="check_out" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1 flex items-center justify-between">
                                    <span>Check-Out Date *</span>
                                    <i class="fa-regular fa-calendar-check text-[#d4a359]"></i>
                                </label>
                                <input type="date" 
                                       name="check_out" 
                                       id="check_out" 
                                       value="<?php echo htmlspecialchars($checkOut); ?>" 
                                       required 
                                       onchange="recalculateBill()" 
                                       class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>

                        <!-- Guests Counts -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="adults" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    <i class="fa-solid fa-users text-[#d4a359] mr-1"></i> Adults (12+ yrs) *
                                </label>
                                <select name="adults" id="adults" onchange="recalculateBill()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                                    <option value="1" <?php echo $guests == 1 ? 'selected' : ''; ?>>1 Adult</option>
                                    <option value="2" <?php echo ($guests == 2 || $guests > 4) ? 'selected' : ''; ?>>2 Adults (Standard)</option>
                                    <option value="3" <?php echo $guests == 3 ? 'selected' : ''; ?>>3 Adults (+ Extra Bed)</option>
                                    <option value="4" <?php echo $guests == 4 ? 'selected' : ''; ?>>4 Adults (Family)</option>
                                </select>
                            </div>

                            <div>
                                <label for="children" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                                    <i class="fa-solid fa-child text-[#d4a359] mr-1"></i> Children (Below 10 yrs)
                                </label>
                                <select name="children" id="children" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                                    <option value="0" selected>0 Children (Stay Free)</option>
                                    <option value="1">1 Child (Stay Free)</option>
                                    <option value="2">2 Children (Stay Free)</option>
                                </select>
                            </div>
                        </div>

                    </div>


                    <!-- STEP 2: PRIMARY GUEST CONTACT & GOVT ID -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ebd9c8] shadow-md space-y-6">
                        
                        <div class="flex items-center space-x-3 pb-4 border-b border-[#ebd9c8]">
                            <span class="w-8 h-8 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-black flex items-center justify-center border border-[#d4a359]/40 shadow-sm">2</span>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Primary Guest Information</h3>
                                <p class="text-xs text-slate-500 font-light">Details for booking confirmation voucher and reception check-in</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="guest_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Full Name (As per Govt ID) *</label>
                                <input type="text" id="guest_name" name="guest_name" required placeholder="e.g. Ramesh Chandra Das" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="guest_phone" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Mobile / WhatsApp Number *</label>
                                <input type="tel" id="guest_phone" name="guest_phone" required placeholder="e.g. 083289 10274" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="guest_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
                                <input type="email" id="guest_email" name="guest_email" placeholder="ramesh@example.com" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="id_proof_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Govt ID Proof</label>
                                <select id="id_proof_type" name="id_proof_type" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                                    <option value="Aadhaar Card" selected>Aadhaar Card</option>
                                    <option value="Driving License">Driving License</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Voter ID">Voter ID</option>
                                </select>
                            </div>

                            <div>
                                <label for="id_proof_number" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">ID Number (Optional)</label>
                                <input type="text" id="id_proof_number" name="id_proof_number" placeholder="e.g. XXXX-XXXX-4589" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>

                        <div>
                            <label for="special_requests" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Special Requests or Tour Cab Pickup (Optional)</label>
                            <textarea id="special_requests" name="special_requests" rows="2" placeholder="e.g. Early check-in, ground floor room, Deomali cab guidance, extra pillows..." class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-2xl px-4 py-3 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]"></textarea>
                        </div>

                    </div>


                    <!-- STEP 3: PAYMENT METHOD & INTERACTIVE UPI QR MODAL -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-[#ebd9c8] shadow-md space-y-6">
                        
                        <div class="flex items-center space-x-3 pb-4 border-b border-[#ebd9c8]">
                            <span class="w-8 h-8 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-black flex items-center justify-center border border-[#d4a359]/40 shadow-sm">3</span>
                            <div>
                                <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Payment Preference</h3>
                                <p class="text-xs text-slate-500 font-light">Choose how you wish to settle your room charges</p>
                            </div>
                        </div>

                        <!-- 3 Payment Options -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            
                            <!-- Option 1: Pay at Hotel -->
                            <label class="payment-option-card border-2 border-[#d4a359] bg-[#f5efe6] rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition hover:shadow-md" id="opt-hotel-card">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-8 h-8 rounded-xl bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xs shadow-sm">
                                        <i class="fa-solid fa-hotel"></i>
                                    </div>
                                    <input type="radio" name="payment_method" value="Pay at Hotel" checked onchange="onPaymentMethodChanged()" class="text-[#2b0e14] focus:ring-[#d4a359]">
                                </div>
                                <div>
                                    <strong class="text-xs text-[#2b0e14] block font-bold">Pay at Hotel</strong>
                                    <span class="text-[10px] text-slate-600 block mt-0.5">Pay cash/card at check-in desk</span>
                                </div>
                            </label>

                            <!-- Option 2: Instant UPI / QR Code -->
                            <label class="payment-option-card border border-slate-200 bg-white hover:border-[#d4a359] rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition hover:shadow-md" id="opt-upi-card">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center text-xs shadow-sm">
                                        <i class="fa-solid fa-qrcode"></i>
                                    </div>
                                    <input type="radio" name="payment_method" value="UPI / QR Code" onchange="onPaymentMethodChanged()" class="text-[#2b0e14] focus:ring-[#d4a359]">
                                </div>
                                <div>
                                    <strong class="text-xs text-[#2b0e14] block font-bold">Scan & Pay via UPI</strong>
                                    <span class="text-[10px] text-emerald-700 font-semibold block mt-0.5">GPay / PhonePe / Paytm</span>
                                </div>
                            </label>

                            <!-- Option 3: Online Payment -->
                            <label class="payment-option-card border border-slate-200 bg-white hover:border-[#d4a359] rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition hover:shadow-md" id="opt-online-card">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-8 h-8 rounded-xl bg-blue-700 text-white flex items-center justify-center text-xs shadow-sm">
                                        <i class="fa-solid fa-credit-card"></i>
                                    </div>
                                    <input type="radio" name="payment_method" value="Online Card / NetBanking" onchange="onPaymentMethodChanged()" class="text-[#2b0e14] focus:ring-[#d4a359]">
                                </div>
                                <div>
                                    <strong class="text-xs text-[#2b0e14] block font-bold">Credit / Debit Card</strong>
                                    <span class="text-[10px] text-slate-500 block mt-0.5">NetBanking & All Cards</span>
                                </div>
                            </label>

                        </div>

                        <!-- DYNAMIC UPI SCAN & PAY MODAL / CONTAINER (Reveals on UPI Radio) -->
                        <div id="upi-scan-box" class="hidden p-6 rounded-3xl bg-gradient-to-br from-[#1c080d] to-[#2b0e14] text-white border-2 border-[#d4a359]/60 shadow-2xl space-y-6">
                            
                            <div class="flex items-center justify-between border-b border-[#d4a359]/30 pb-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-qrcode text-[#f3cf8a] text-lg"></i>
                                    <span class="text-xs font-black uppercase tracking-wider text-[#f3cf8a]">Raj Residency Direct UPI Payment</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-600 text-white">Instant QR</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 items-center">
                                
                                <!-- QR Code Visual Frame -->
                                <div class="sm:col-span-5 text-center space-y-2">
                                    <div class="bg-white p-3 rounded-2xl shadow-xl inline-block border-4 border-[#d4a359]/50">
                                        <!-- Dynamic QR Image generated for UPI payment -->
                                        <img id="upi-qr-image" 
                                             src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=upi://pay?pa=<?php echo urlencode($hotelUpiId); ?>%26pn=Raj%20Residency%26cu=INR" 
                                             alt="Raj Residency UPI QR Code" 
                                             class="w-36 h-36 sm:w-40 sm:h-40 object-contain mx-auto">
                                    </div>
                                    <div class="flex items-center justify-center space-x-2 text-[10px] text-[#f3cf8a]">
                                        <i class="fa-solid fa-camera"></i>
                                        <span>Scan with any UPI App</span>
                                    </div>
                                </div>

                                <!-- UPI ID & Details -->
                                <div class="sm:col-span-7 space-y-4">
                                    
                                    <!-- UPI ID Pill with 1-Click Copy -->
                                    <div class="space-y-1">
                                        <span class="text-[10px] text-slate-300 font-bold uppercase tracking-wider block">Official Hotel UPI ID:</span>
                                        <div class="flex items-center space-x-2">
                                            <div class="bg-[#100407] border border-[#d4a359]/40 px-3.5 py-2 rounded-xl text-xs font-mono font-bold text-[#f3cf8a] flex-grow select-all" id="upi-id-text">
                                                <?php echo htmlspecialchars($hotelUpiId); ?>
                                            </div>
                                            <button type="button" onclick="copyUpiId()" class="px-3.5 py-2 rounded-xl bg-[#d4a359] hover:bg-[#f3cf8a] text-[#2b0e14] text-xs font-bold transition shrink-0 flex items-center space-x-1" id="copy-upi-btn">
                                                <i class="fa-regular fa-copy"></i>
                                                <span>Copy</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Amount to Pay Reminder -->
                                    <div class="p-3 rounded-xl bg-[#100407]/80 border border-[#d4a359]/30 flex items-center justify-between">
                                        <span class="text-xs text-slate-300">Amount to Transfer:</span>
                                        <strong class="text-base font-black text-[#f3cf8a]" id="upi-payable-amount">₹<?php echo number_format(($room['price_per_night'] * $nights * (1 + $taxRate/100)), 0); ?></strong>
                                    </div>

                                    <!-- UTR Reference input & Receipt Upload -->
                                    <div class="space-y-3 pt-1">
                                        <div>
                                            <label for="utr_number" class="block text-[10px] text-slate-300 font-bold uppercase tracking-wider mb-1">
                                                UPI Reference / UTR Number (12 Digits)
                                            </label>
                                            <input type="text" 
                                                   id="utr_number" 
                                                   name="utr_number" 
                                                   placeholder="e.g. 426189034512" 
                                                   class="w-full bg-[#100407] border border-[#d4a359]/40 rounded-xl px-3.5 py-2 text-xs font-mono font-bold text-white focus:outline-none focus:ring-1 focus:ring-[#f3cf8a]">
                                        </div>

                                        <div>
                                            <label for="payment_receipt_file" class="block text-[10px] text-slate-300 font-bold uppercase tracking-wider mb-1">
                                                Upload Payment Screenshot / Receipt (Optional)
                                            </label>
                                            <input type="file" 
                                                   id="payment_receipt_file" 
                                                   name="payment_receipt_file" 
                                                   accept="image/*" 
                                                   class="w-full bg-[#100407] border border-[#d4a359]/40 rounded-xl px-3 py-1.5 text-xs text-slate-300 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#d4a359] file:text-[#2b0e14] cursor-pointer">
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- CONFIRM BOOKING BUTTON -->
                    <button type="submit" class="w-full btn-gold py-4 px-6 rounded-2xl text-xs sm:text-sm font-bold uppercase tracking-wider shadow-2xl flex items-center justify-center space-x-2 group">
                        <i class="fa-solid fa-lock text-sm"></i>
                        <span>Confirm & Complete Stay Reservation</span>
                        <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition"></i>
                    </button>

                </form>
            </div>


            <!-- RIGHT COLUMN: STICKY RESERVATION BILL BREAKDOWN (4 Cols) -->
            <div class="lg:col-span-4">
                <div class="sticky top-24 space-y-6">
                    
                    <!-- BILL SUMMARY CARD -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border-2 border-[#d4a359]/40 shadow-xl space-y-6">
                        
                        <div class="pb-3 border-b border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Stay Summary</span>
                            <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Billing Breakdown</h3>
                        </div>

                        <!-- Selected Room Visual -->
                        <div class="space-y-3">
                            <div class="h-36 rounded-2xl overflow-hidden shadow-sm relative bg-slate-900 border border-[#ebd9c8]">
                                <img id="summary-room-img" src="<?php echo htmlspecialchars($room['featured_image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80';" class="w-full h-full object-cover">
                                <span id="summary-category-badge" class="absolute top-2 left-2 px-2.5 py-0.5 rounded-full text-[9px] font-bold bg-[#2b0e14]/90 text-[#f3cf8a] border border-[#d4a359]/40">
                                    <?php echo htmlspecialchars($room['type_label']); ?>
                                </span>
                            </div>
                            <div>
                                <h4 class="font-serif text-base font-bold text-[#2b0e14] leading-snug" id="summary-room-name">
                                    <?php echo htmlspecialchars($room['name']); ?>
                                </h4>
                                <span class="text-xs text-slate-500 block mt-0.5" id="summary-room-specs">
                                    <?php echo htmlspecialchars($room['bed_type']); ?> &bull; Max <?php echo (int)$room['max_guests']; ?> Adults
                                </span>
                            </div>
                        </div>

                        <!-- Calculation Rows -->
                        <div class="space-y-3 text-xs text-slate-600 pt-3 border-t border-slate-100">
                            
                            <div class="flex items-center justify-between">
                                <span>Room Rate / Night:</span>
                                <strong class="text-[#2b0e14]" id="summary-rate-display">₹<?php echo number_format($room['price_per_night'], 0); ?></strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Duration:</span>
                                <strong class="text-[#2b0e14]" id="summary-nights-display"><?php echo $nights; ?> Night<?php echo $nights > 1 ? 's' : ''; ?></strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Base Amount:</span>
                                <strong class="text-[#2b0e14]" id="summary-base-display">₹<?php echo number_format($room['price_per_night'] * $nights, 0); ?></strong>
                            </div>

                            <div class="flex items-center justify-between">
                                <span>Hotel Taxes & GST (<?php echo $taxRate; ?>%):</span>
                                <strong class="text-[#2b0e14]" id="summary-tax-display">₹<?php echo number_format(($room['price_per_night'] * $nights * $taxRate) / 100, 0); ?></strong>
                            </div>

                            <!-- Grand Total -->
                            <div class="pt-3 border-t border-[#ebd9c8] flex items-baseline justify-between">
                                <div>
                                    <strong class="text-sm text-[#2b0e14] block">Total Payable</strong>
                                    <span class="text-[9px] text-slate-400 font-medium">All taxes & fees included</span>
                                </div>
                                <strong class="text-2xl font-black text-[#2b0e14]" id="summary-grand-total">
                                    ₹<?php echo number_format(($room['price_per_night'] * $nights * (1 + $taxRate/100)), 0); ?>
                                </strong>
                            </div>

                        </div>

                    </div>

                    <!-- Direct Booking Perks Pill -->
                    <div class="p-5 rounded-3xl bg-[#f5efe6] border border-[#ebd9c8] space-y-2 text-[11px] text-slate-700">
                        <div class="flex items-center space-x-2 text-[#2b0e14] font-bold">
                            <i class="fa-solid fa-crown text-[#d4a359]"></i>
                            <span>Direct Booking Guarantee:</span>
                        </div>
                        <ul class="space-y-1.5 text-[10px] text-slate-600 pl-4 list-disc">
                            <li>Instant printable booking voucher with QR code</li>
                            <li>1-Click confirmation sent to your WhatsApp</li>
                            <li>Free date rescheduling or cancellation up to 24h prior</li>
                        </ul>
                    </div>

                    <!-- Reception Call Assistance -->
                    <div class="text-center space-y-1">
                        <span class="text-[11px] text-slate-400 block">Need help with your booking?</span>
                        <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="text-xs font-bold text-[#2b0e14] hover:text-[#b88738] transition inline-flex items-center space-x-1.5">
                            <i class="fa-solid fa-phone text-[#d4a359]"></i>
                            <span>Call Reception: <?php echo htmlspecialchars($settings['hotel_phone']); ?></span>
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>


<!-- 3. JAVASCRIPT: DYNAMIC BILL CALCULATOR, ROOM SWITCHER & UPI QR LOGIC -->
<script>
    const taxRate = <?php echo $taxRate; ?>;
    const hotelUpi = <?php echo json_encode($hotelUpiId); ?>;

    function recalculateBill() {
        const roomSelect = document.getElementById('room_id');
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');

        if (!roomSelect || !checkInInput || !checkOutInput) return;

        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const pricePerNight = parseFloat(selectedOption.getAttribute('data-price')) || 2499;

        const checkInVal = checkInInput.value;
        const checkOutVal = checkOutInput.value;

        if (checkInVal && checkOutVal) {
            const d1 = new Date(checkInVal);
            const d2 = new Date(checkOutVal);
            const diffTime = d2 - d1;
            let nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (nights <= 0) {
                const nextDay = new Date(d1);
                nextDay.setDate(nextDay.getDate() + 1);
                checkOutInput.value = nextDay.toISOString().split('T')[0];
                nights = 1;
            }

            const baseAmount = pricePerNight * nights;
            const taxAmount = (baseAmount * taxRate) / 100;
            const grandTotal = Math.round(baseAmount + taxAmount);

            // Update Right Summary Card
            document.getElementById('summary-rate-display').textContent = '₹' + pricePerNight.toLocaleString('en-IN');
            document.getElementById('summary-nights-display').textContent = `${nights} Night${nights > 1 ? 's' : ''}`;
            document.getElementById('summary-base-display').textContent = '₹' + baseAmount.toLocaleString('en-IN');
            document.getElementById('summary-tax-display').textContent = '₹' + Math.round(taxAmount).toLocaleString('en-IN');
            document.getElementById('summary-grand-total').textContent = '₹' + grandTotal.toLocaleString('en-IN');
            
            // Update UPI Section
            const upiPayable = document.getElementById('upi-payable-amount');
            if (upiPayable) upiPayable.textContent = '₹' + grandTotal.toLocaleString('en-IN');

            const upiQrImg = document.getElementById('upi-qr-image');
            if (upiQrImg) {
                const qrData = `upi://pay?pa=${encodeURIComponent(hotelUpi)}&pn=Raj%20Residency&am=${grandTotal}&cu=INR`;
                upiQrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(qrData)}`;
            }
        }
    }

    function onRoomChanged() {
        const roomSelect = document.getElementById('room_id');
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        
        const roomName = selectedOption.getAttribute('data-name');
        const roomImg = selectedOption.getAttribute('data-img');
        const roomCategory = selectedOption.getAttribute('data-category');
        const roomBed = selectedOption.getAttribute('data-bed');
        const roomMax = selectedOption.getAttribute('data-max');

        // Update Summary Card details
        const sumName = document.getElementById('summary-room-name');
        const sumImg = document.getElementById('summary-room-img');
        const sumCat = document.getElementById('summary-category-badge');
        const sumSpecs = document.getElementById('summary-room-specs');

        if (sumName) sumName.textContent = roomName;
        if (sumImg && roomImg) sumImg.src = roomImg;
        if (sumCat) sumCat.textContent = roomCategory;
        if (sumSpecs) sumSpecs.textContent = `${roomBed} • Max ${roomMax} Adults`;

        recalculateBill();
    }

    function onPaymentMethodChanged() {
        const upiRadio = document.querySelector('input[name="payment_method"][value="UPI / QR Code"]');
        const upiBox = document.getElementById('upi-scan-box');
        
        const cardHotel = document.getElementById('opt-hotel-card');
        const cardUpi = document.getElementById('opt-upi-card');
        const cardOnline = document.getElementById('opt-online-card');

        // Reset borders
        if (cardHotel) cardHotel.className = 'payment-option-card border border-slate-200 bg-white rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition hover:shadow-md';
        if (cardUpi) cardUpi.className = 'payment-option-card border border-slate-200 bg-white rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition hover:shadow-md';
        if (cardOnline) cardOnline.className = 'payment-option-card border border-slate-200 bg-white rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition hover:shadow-md';

        const checkedRadio = document.querySelector('input[name="payment_method"]:checked');
        if (checkedRadio) {
            const parentLabel = checkedRadio.closest('label');
            if (parentLabel) {
                parentLabel.className = 'payment-option-card border-2 border-[#d4a359] bg-[#f5efe6] rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition shadow-sm';
            }
        }

        if (upiRadio && upiRadio.checked) {
            if (upiBox) upiBox.classList.remove('hidden');
        } else {
            if (upiBox) upiBox.classList.add('hidden');
        }
    }

    function copyUpiId() {
        const upiText = document.getElementById('upi-id-text').innerText.trim();
        navigator.clipboard.writeText(upiText).then(() => {
            const copyBtn = document.getElementById('copy-upi-btn');
            copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> <span>Copied!</span>';
            setTimeout(() => {
                copyBtn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copy</span>';
            }, 2000);
        });
    }

    // Set Default Min Dates
    document.addEventListener('DOMContentLoaded', function() {
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');

        const today = new Date();
        const todayStr = today.toISOString().split('T')[0];

        if (checkInInput) {
            checkInInput.min = todayStr;
            if (!checkInInput.value) checkInInput.value = todayStr;
        }

        recalculateBill();
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
