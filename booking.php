<?php
// booking.php - Complete Reservation Flow for Raj Residency
require_once __DIR__ . '/includes/functions.php';

$roomId = isset($_REQUEST['room_id']) ? (int)$_REQUEST['room_id'] : 1;
$room = get_room_by_id($roomId);

if (!$room) {
    $all = get_all_rooms(null, true);
    $room = $all[0] ?? null;
    $roomId = $room ? $room['id'] : 1;
}

$settings = get_settings();
$taxRate = (float)($settings['tax_percentage'] ?? 12);

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));

$checkIn = isset($_REQUEST['check_in']) && !empty($_REQUEST['check_in']) ? sanitize($_REQUEST['check_in']) : $today;
$checkOut = isset($_REQUEST['check_out']) && !empty($_REQUEST['check_out']) ? sanitize($_REQUEST['check_out']) : $tomorrow;
$guests = isset($_REQUEST['guests']) ? (int)$_REQUEST['guests'] : 2;

// Calculate nights
$d1 = new DateTime($checkIn);
$d2 = new DateTime($checkOut);
$interval = $d1->diff($d2);
$nights = max(1, (int)$interval->days);

// Handle POST Booking submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_booking') {
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
    $specialRequests = sanitize($_POST['special_requests'] ?? '');

    if (empty($guestName)) $errors[] = "Please enter guest name.";
    if (empty($guestPhone)) $errors[] = "Please enter contact phone number.";
    if (empty($checkInPost) || empty($checkOutPost)) $errors[] = "Please select valid check-in and check-out dates.";

    if (empty($errors)) {
        $p1 = new DateTime($checkInPost);
        $p2 = new DateTime($checkOutPost);
        $postNights = max(1, (int)$p1->diff($p2)->days);
        
        $pricePerNight = (float)$room['price_per_night'];
        $baseTotal = $pricePerNight * $postNights;
        $taxAmount = ($baseTotal * $taxRate) / 100;
        $grandTotal = $baseTotal + $taxAmount;

        $bookingData = [
            'room_id' => $room['id'],
            'room_name' => $room['name'],
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
            'payment_status' => ($paymentMethod === 'UPI / QR Code') ? 'Paid (Pending Verification)' : 'Pending',
            'booking_status' => 'Confirmed',
            'special_requests' => $specialRequests
        ];

        $bookingNum = create_booking($bookingData);
        if ($bookingNum) {
            header("Location: booking-confirmation.php?booking_number=" . urlencode($bookingNum));
            exit;
        } else {
            $errors[] = "Could not process your booking. Please try again or call our reception.";
        }
    }
}

$pageTitle = "Complete Reservation - " . ($room['name'] ?? 'Booking');
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Banner -->
<section class="bg-[#2b0e14] text-white py-10 border-b border-[#d4a359]/30 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2">
        <span class="text-[10px] font-bold uppercase tracking-widest text-[#f3cf8a] block">INSTANT CONFIRMATION & ZERO BOOKING FEE</span>
        <h1 class="font-serif text-3xl sm:text-4xl font-bold text-white">
            Confirm Your Stay at <?php echo htmlspecialchars($settings['hotel_name']); ?>
        </h1>
        <p class="text-xs text-slate-300"><?php echo htmlspecialchars($settings['hotel_address']); ?></p>
    </div>
</section>

<!-- Booking Form Section -->
<section class="py-12 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <?php if (!empty($errors)): ?>
            <div class="mb-8 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold">
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?>
                        <li><?php echo htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- Left Column: Form Fields -->
            <div class="lg:col-span-8">
                <form id="booking-form" action="booking.php" method="POST" class="space-y-8">
                    <input type="hidden" name="action" value="confirm_booking">
                    <input type="hidden" name="room_id" id="form_room_id" value="<?php echo $room['id']; ?>">

                    <!-- Step 1: Stay & Dates Details -->
                    <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-[#d4a359]/20 shadow-sm space-y-4 sm:space-y-5">
                        <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                            <span class="w-7 h-7 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-bold flex items-center justify-center border border-[#d4a359]/30">1</span>
                            <h3 class="font-serif text-lg font-bold text-[#2b0e14]">Dates & Guest Details</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Check-In Date *</label>
                                <input type="date" name="check_in" id="check_in" value="<?php echo htmlspecialchars($checkIn); ?>" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Check-Out Date *</label>
                                <input type="date" name="check_out" id="check_out" value="<?php echo htmlspecialchars($checkOut); ?>" required class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Adults *</label>
                                <select name="adults" id="adults_count" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                                    <option value="1" <?php echo $guests == 1 ? 'selected' : ''; ?>>1 Adult</option>
                                    <option value="2" <?php echo ($guests == 2 || $guests > 4) ? 'selected' : ''; ?>>2 Adults</option>
                                    <option value="3" <?php echo $guests == 3 ? 'selected' : ''; ?>>3 Adults</option>
                                    <option value="4" <?php echo $guests == 4 ? 'selected' : ''; ?>>4 Adults</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Children (Below 10 yrs)</label>
                                <select name="children" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-bold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                                    <option value="0" selected>0 Children</option>
                                    <option value="1">1 Child</option>
                                    <option value="2">2 Children</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Primary Guest Information -->
                    <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-[#d4a359]/20 shadow-sm space-y-4 sm:space-y-5">
                        <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                            <span class="w-7 h-7 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-bold flex items-center justify-center border border-[#d4a359]/30">2</span>
                            <h3 class="font-serif text-lg font-bold text-[#2b0e14]">Primary Guest Contact</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Full Name *</label>
                                <input type="text" name="guest_name" required placeholder="e.g. Ramesh Chandra Das" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Mobile / WhatsApp Phone *</label>
                                <input type="tel" name="guest_phone" required placeholder="+91 83289 10274" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
                                <input type="email" name="guest_email" placeholder="ramesh@example.com" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">ID Proof Type</label>
                                <select name="id_proof_type" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                                    <option value="Aadhaar Card" selected>Aadhaar Card</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Driving License">Driving License</option>
                                    <option value="Voter ID Card">Voter ID Card</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Special Requests (Optional)</label>
                            <textarea name="special_requests" rows="2" placeholder="e.g. Early check-in, ground floor room, extra towels, cab pickup from Koraput Station..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- Step 3: Payment Option -->
                    <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-[#d4a359]/20 shadow-sm space-y-4 sm:space-y-5">
                        <div class="flex items-center space-x-3 pb-3 border-b border-slate-100">
                            <span class="w-7 h-7 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-bold flex items-center justify-center border border-[#d4a359]/30">3</span>
                            <h3 class="font-serif text-lg font-bold text-[#2b0e14]">Payment Option</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                            <label class="border-2 border-[#d4a359] bg-[#f5efe6] rounded-2xl p-4 cursor-pointer flex flex-col justify-between">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fa-solid fa-hotel text-[#2b0e14] text-lg"></i>
                                    <input type="radio" name="payment_method" value="Pay at Hotel" checked class="text-[#2b0e14] focus:ring-[#d4a359]">
                                </div>
                                <div>
                                    <strong class="text-xs text-[#2b0e14] block">Pay at Hotel</strong>
                                    <span class="text-[10px] text-slate-500">Pay cash/card at check-in</span>
                                </div>
                            </label>

                            <label class="border border-slate-200 hover:border-[#d4a359] rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fa-solid fa-qrcode text-[#2b0e14] text-lg"></i>
                                    <input type="radio" name="payment_method" value="UPI / QR Code" class="text-[#2b0e14] focus:ring-[#d4a359]">
                                </div>
                                <div>
                                    <strong class="text-xs text-[#2b0e14] block">Instant UPI QR</strong>
                                    <span class="text-[10px] text-slate-500">GPay / PhonePe / Paytm</span>
                                </div>
                            </label>

                            <label class="border border-slate-200 hover:border-[#d4a359] rounded-2xl p-4 cursor-pointer flex flex-col justify-between transition">
                                <div class="flex items-center justify-between mb-2">
                                    <i class="fa-solid fa-credit-card text-[#2b0e14] text-lg"></i>
                                    <input type="radio" name="payment_method" value="Online Card / NetBanking" class="text-[#2b0e14] focus:ring-[#d4a359]">
                                </div>
                                <div>
                                    <strong class="text-xs text-[#2b0e14] block">Online Payment</strong>
                                    <span class="text-[10px] text-slate-500">Debit / Credit Cards</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full btn-gold py-4 px-6 rounded-2xl text-xs sm:text-sm font-bold uppercase tracking-wider shadow-xl flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-lock"></i>
                        <span>Confirm & Complete Booking</span>
                    </button>

                </form>
            </div>

            <!-- Right Column: Order Summary Card -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-[#d4a359]/30 shadow-xl sticky top-28 space-y-5 sm:space-y-6">
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14] pb-3 border-b border-slate-100">
                        Reservation Summary
                    </h3>

                    <!-- Selected Room Card -->
                    <div class="space-y-3">
                        <div class="h-36 rounded-2xl overflow-hidden shadow-sm">
                            <img src="<?php echo htmlspecialchars($room['featured_image']); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <span class="text-[10px] font-bold uppercase text-[#d4a359] block tracking-wider"><?php echo htmlspecialchars($room['type_label']); ?></span>
                            <h4 class="font-serif text-base font-bold text-[#2b0e14]"><?php echo htmlspecialchars($room['name']); ?></h4>
                            <span class="text-xs text-slate-500"><?php echo htmlspecialchars($room['bed_type']); ?> &bull; Max <?php echo (int)$room['max_guests']; ?> Guests</span>
                        </div>
                    </div>

                    <!-- Calculation breakdown -->
                    <div class="space-y-3 text-xs text-slate-600 pt-3 border-t border-slate-100">
                        <div class="flex justify-between">
                            <span>Room Rate</span>
                            <span class="font-bold text-slate-800" id="rate_per_night_display"><?php echo format_price($room['price_per_night']); ?> / night</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Duration</span>
                            <span class="font-bold text-slate-800" id="nights_count_display"><?php echo $nights; ?> Night<?php echo $nights > 1 ? 's' : ''; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Base Amount</span>
                            <span class="font-bold text-slate-800" id="base_total_display"><?php echo format_price($room['price_per_night'] * $nights); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Taxes & GST (<?php echo $taxRate; ?>%)</span>
                            <span class="font-bold text-slate-800" id="tax_amount_display"><?php echo format_price(($room['price_per_night'] * $nights * $taxRate) / 100); ?></span>
                        </div>

                        <div class="pt-3 border-t border-slate-200 flex justify-between items-baseline">
                            <strong class="text-sm text-[#2b0e14]">Total Payable</strong>
                            <strong class="text-xl font-black text-[#2b0e14]" id="grand_total_display">
                                <?php 
                                $baseVal = $room['price_per_night'] * $nights;
                                $taxVal = ($baseVal * $taxRate) / 100;
                                echo format_price($baseVal + $taxVal); 
                                ?>
                            </strong>
                        </div>
                    </div>

                    <!-- Trust indicators -->
                    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#d4a359]/30 text-[11px] text-slate-600 space-y-2">
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>Free Cancellation up to 24h prior</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-shield-halved text-[#d4a359]"></i>
                            <span>100% Direct Hotel Rate Guaranteed</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <i class="fa-solid fa-bolt text-amber-500"></i>
                            <span>Instant Booking Confirmation SMS/WhatsApp</span>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<script>
// Dynamic client-side price calculation
function updateBookingSummary() {
    const checkInVal = document.getElementById('check_in').value;
    const checkOutVal = document.getElementById('check_out').value;
    const pricePerNight = <?php echo (float)$room['price_per_night']; ?>;
    const taxRate = <?php echo $taxRate; ?>;
    const currency = '<?php echo htmlspecialchars($settings['currency_symbol'] ?? '₹'); ?>';

    if (checkInVal && checkOutVal) {
        const d1 = new Date(checkInVal);
        const d2 = new Date(checkOutVal);
        const diffTime = d2 - d1;
        let nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (nights < 1) nights = 1;

        const baseTotal = pricePerNight * nights;
        const taxAmount = (baseTotal * taxRate) / 100;
        const grandTotal = baseTotal + taxAmount;

        document.getElementById('nights_count_display').innerText = nights + (nights > 1 ? ' Nights' : ' Night');
        document.getElementById('base_total_display').innerText = currency + baseTotal.toLocaleString('en-IN');
        document.getElementById('tax_amount_display').innerText = currency + Math.round(taxAmount).toLocaleString('en-IN');
        document.getElementById('grand_total_display').innerText = currency + Math.round(grandTotal).toLocaleString('en-IN');
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

