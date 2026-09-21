<?php
// booking-confirmation.php - Ultra-Luxury Printable Reservation Voucher & Instant WhatsApp Confirmation
require_once __DIR__ . '/includes/functions.php';

$bookingNumber = isset($_GET['booking_number']) ? sanitize($_GET['booking_number']) : '';
$booking = get_booking_by_number($bookingNumber);

if (!$booking) {
    header('Location: index.php');
    exit;
}

$settings = get_settings();
$pageTitle = "Reservation Voucher #" . $booking['booking_number'];
require_once __DIR__ . '/includes/header.php';

// Prepare WhatsApp confirmation text
$cleanPhone = preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']);
$checkInFormatted = date('d M Y (D)', strtotime($booking['check_in']));
$checkOutFormatted = date('d M Y (D)', strtotime($booking['check_out']));

$waText = "🏨 *RAJ RESIDENCY KORAPUT - BOOKING CONFIRMATION*\n";
$waText .= "----------------------------------------\n";
$waText .= "🔖 *Booking ID:* #" . $booking['booking_number'] . "\n";
$waText .= "👤 *Guest Name:* " . $booking['guest_name'] . "\n";
$waText .= "📞 *Contact Phone:* " . $booking['guest_phone'] . "\n";
$waText .= "🛏️ *Room Reserved:* " . $booking['room_name'] . "\n";
$waText .= "📅 *Check-In:* " . $checkInFormatted . " (12:00 PM)\n";
$waText .= "📅 *Check-Out:* " . $checkOutFormatted . " (11:00 AM)\n";
$waText .= "🌙 *Stay Duration:* " . $booking['total_nights'] . " Night(s) (" . $booking['adults'] . " Adult(s))\n";
$waText .= "💰 *Total Amount:* " . format_price($booking['total_amount']) . " (" . $booking['payment_method'] . " - " . $booking['payment_status'] . ")\n";
$waText .= "📍 *Address:* " . $settings['hotel_address'] . "\n";
$waText .= "----------------------------------------\n";
$waText .= "Kindly confirm my room reservation. Thank you!";

$waUrl = "https://wa.me/" . $cleanPhone . "?text=" . urlencode($waText);

// Dynamic QR code for the voucher
$qrData = "RAJ RESIDENCY HOTEL | REF: " . $booking['booking_number'] . " | GUEST: " . $booking['guest_name'] . " | ROOM: " . $booking['room_name'] . " | IN: " . $booking['check_in'] . " | OUT: " . $booking['check_out'] . " | AMOUNT: " . format_price($booking['total_amount']);
$qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=" . urlencode($qrData);
?>

<div class="py-12 sm:py-16 bg-[#fbf9f5] min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        
        <!-- 1. TOP SUCCESS BANNER -->
        <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-emerald-800 text-white rounded-3xl p-6 sm:p-8 shadow-2xl text-center mb-8 border border-emerald-500/40 relative overflow-hidden no-print">
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto text-2xl mb-3 shadow-inner border border-white/30">
                <i class="fa-solid fa-check text-white"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-200 block">RESERVATION CONFIRMED</span>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold mt-1">Thank You, <?php echo htmlspecialchars($booking['guest_name']); ?>!</h1>
            <p class="text-xs sm:text-sm text-emerald-100 font-light max-w-lg mx-auto mt-1 leading-relaxed">
                Your booking reference <strong class="text-white font-bold">#<?php echo htmlspecialchars($booking['booking_number']); ?></strong> is confirmed at <strong><?php echo htmlspecialchars($settings['hotel_name']); ?></strong>.
            </p>

            <!-- Quick Action Hub on Banner -->
            <div class="flex flex-wrap items-center justify-center gap-3 pt-5">
                <a href="<?php echo $waUrl; ?>" target="_blank" class="bg-white text-emerald-900 hover:bg-emerald-50 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2 transition">
                    <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i>
                    <span>Send to Hotel WhatsApp</span>
                </a>
                <button type="button" onclick="window.print()" class="bg-[#1c080d]/80 hover:bg-[#1c080d] text-[#f3cf8a] border border-[#d4a359]/50 px-6 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2 transition">
                    <i class="fa-solid fa-print"></i>
                    <span>Print PDF Voucher</span>
                </button>
            </div>
        </div>


        <!-- 2. OFFICIAL PRINTABLE ROYAL HOTEL VOUCHER -->
        <div id="voucher-card" class="bg-white rounded-3xl p-6 sm:p-10 border-2 border-[#d4a359]/40 shadow-2xl space-y-6 sm:space-y-8 relative overflow-hidden">
            
            <!-- Top Watermark Background Crest -->
            <div class="absolute right-8 top-1/3 text-slate-100 pointer-events-none -z-0 opacity-40 select-none hidden sm:block">
                <i class="fa-solid fa-crown text-[280px]"></i>
            </div>

            <!-- Voucher Brand Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b-2 border-slate-100 gap-4 relative z-10">
                <div class="flex items-center space-x-3.5">
                    <div class="w-14 h-14 rounded-full border-2 border-[#d4a359] bg-[#2b0e14] flex flex-col items-center justify-center text-[#f3cf8a] text-xs shadow-md shrink-0">
                        <i class="fa-solid fa-crown text-xs text-[#d4a359] mb-0.5"></i>
                        <span class="font-royal text-sm font-black tracking-tighter leading-none text-[#f3cf8a]">RR</span>
                    </div>
                    <div>
                        <span class="font-serif text-2xl font-bold uppercase text-[#2b0e14] block leading-tight"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                        <span class="text-[9px] tracking-widest text-[#b88738] uppercase font-bold block"><?php echo htmlspecialchars($settings['hotel_tagline']); ?></span>
                        <span class="text-[11px] text-slate-500 font-light block"><?php echo htmlspecialchars($settings['hotel_address']); ?></span>
                        <span class="text-[10px] text-slate-600 font-bold block mt-0.5">
                            <i class="fa-solid fa-phone text-[#d4a359] text-[9px] mr-1"></i> <?php echo htmlspecialchars($settings['hotel_phone']); ?> | 
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-[9px] mr-1"></i> <?php echo htmlspecialchars($settings['hotel_whatsapp']); ?>
                        </span>
                    </div>
                </div>

                <!-- Right Reference & QR Box -->
                <div class="flex items-center sm:flex-col sm:items-end justify-between sm:justify-start gap-2 bg-[#fbf9f5] sm:bg-transparent p-3 sm:p-0 rounded-2xl border sm:border-0 border-[#ebd9c8]">
                    <div class="sm:text-right">
                        <span class="text-[9px] uppercase font-bold text-slate-400 block tracking-wider">BOOKING VOUCHER REF</span>
                        <strong class="text-base sm:text-lg font-black text-[#2b0e14] bg-[#f5efe6] border border-[#d4a359]/40 px-3 py-1 rounded-xl inline-block shadow-sm">
                            #<?php echo htmlspecialchars($booking['booking_number']); ?>
                        </strong>
                        <span class="text-[10px] text-slate-500 block mt-0.5 font-medium">Issued: <?php echo format_datetime($booking['created_at']); ?></span>
                    </div>
                    
                    <!-- Dynamic Verification QR Code -->
                    <div class="p-1 bg-white border border-[#d4a359]/30 rounded-xl shadow-sm shrink-0 sm:mt-2">
                        <img src="<?php echo $qrCodeUrl; ?>" alt="Verification QR Code" class="w-16 h-16 sm:w-20 sm:h-20 object-contain">
                    </div>
                </div>
            </div>

            <!-- Guest & Stay Dossier Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-[#fffdfa] p-6 rounded-3xl border border-[#ebd9c8] shadow-sm relative z-10">
                
                <!-- Guest Info -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 text-[11px] font-bold uppercase tracking-wider text-[#b88738]">
                        <i class="fa-solid fa-user text-[#d4a359]"></i>
                        <span>Primary Guest Information</span>
                    </div>
                    <div class="text-xs space-y-1.5 text-slate-700">
                        <p><strong class="text-slate-900 font-semibold">Guest Name:</strong> <?php echo htmlspecialchars($booking['guest_name']); ?></p>
                        <p><strong class="text-slate-900 font-semibold">Contact Mobile:</strong> <?php echo htmlspecialchars($booking['guest_phone']); ?></p>
                        <?php if (!empty($booking['guest_email'])): ?>
                            <p><strong class="text-slate-900 font-semibold">Email:</strong> <?php echo htmlspecialchars($booking['guest_email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($booking['id_proof_type'])): ?>
                            <p><strong class="text-slate-900 font-semibold">ID Proof:</strong> <?php echo htmlspecialchars($booking['id_proof_type']); ?> <?php echo !empty($booking['id_proof_number']) ? '(' . htmlspecialchars($booking['id_proof_number']) . ')' : ''; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($booking['special_requests'])): ?>
                            <p class="pt-1 text-[11px] text-slate-500 italic"><strong class="text-slate-700 not-italic">Notes / Requests:</strong> <?php echo htmlspecialchars($booking['special_requests']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Stay Schedule -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-2 text-[11px] font-bold uppercase tracking-wider text-[#b88738]">
                        <i class="fa-solid fa-calendar-check text-[#d4a359]"></i>
                        <span>Reservation Schedule</span>
                    </div>
                    <div class="text-xs space-y-1.5 text-slate-700">
                        <p><strong class="text-slate-900 font-semibold">Check-In:</strong> <?php echo $checkInFormatted; ?> <span class="text-emerald-700 font-bold">(<?php echo htmlspecialchars($settings['check_in_time'] ?? '12:00 PM'); ?>)</span></p>
                        <p><strong class="text-slate-900 font-semibold">Check-Out:</strong> <?php echo $checkOutFormatted; ?> <span class="text-amber-800 font-bold">(<?php echo htmlspecialchars($settings['check_out_time'] ?? '11:00 AM'); ?>)</span></p>
                        <p><strong class="text-slate-900 font-semibold">Total Duration:</strong> <?php echo (int)$booking['total_nights']; ?> Night(s)</p>
                        <p><strong class="text-slate-900 font-semibold">Guests:</strong> <?php echo (int)$booking['adults']; ?> Adult(s) <?php echo !empty($booking['children']) ? ', ' . (int)$booking['children'] . ' Child(ren)' : ''; ?></p>
                        <p><strong class="text-slate-900 font-semibold">Booking Status:</strong> <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[10px]">🟢 Confirmed Reservation</span></p>
                    </div>
                </div>

            </div>

            <!-- Itemized Room & Billing Table -->
            <div class="overflow-x-auto relative z-10">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#2b0e14] text-white">
                        <tr>
                            <th class="p-3.5 rounded-l-2xl">Room Description & Inclusions</th>
                            <th class="p-3.5 text-center">Nights</th>
                            <th class="p-3.5 text-right">Rate / Night</th>
                            <th class="p-3.5 rounded-r-2xl text-right">Item Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-4">
                                <strong class="text-slate-900 text-sm block"><?php echo htmlspecialchars($booking['room_name']); ?></strong>
                                <span class="text-[11px] text-slate-500">Includes High-Speed Wi-Fi, 24/7 Hot Water Geyser, Daily Sanitized Housekeeping</span>
                            </td>
                            <td class="p-4 text-center font-bold text-slate-700"><?php echo (int)$booking['total_nights']; ?></td>
                            <td class="p-4 text-right text-slate-700"><?php echo format_price($booking['price_per_night']); ?></td>
                            <td class="p-4 text-right font-bold text-slate-900"><?php echo format_price($booking['price_per_night'] * $booking['total_nights']); ?></td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t-2 border-slate-200">
                        <tr>
                            <td colspan="3" class="p-3 text-right font-semibold text-slate-600">Total Stay Amount (Taxes & Fees Included):</td>
                            <td class="p-3 text-right text-lg font-black text-[#2b0e14]"><?php echo format_price($booking['total_amount']); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="p-2 text-right text-[11px] text-slate-500">Payment Option & Status:</td>
                            <td class="p-2 text-right text-xs font-bold <?php echo (strpos($booking['payment_status'], 'Paid') !== false) ? 'text-emerald-700' : 'text-amber-800'; ?>">
                                <?php echo htmlspecialchars($booking['payment_method']); ?> (<?php echo htmlspecialchars($booking['payment_status']); ?>)
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Front Desk Stamp & Verification Signatory (Visible on print & screen) -->
            <div class="grid grid-cols-2 gap-8 pt-6 border-t-2 border-slate-100 text-xs text-slate-600 relative z-10">
                <div class="space-y-1">
                    <p class="font-bold text-[#2b0e14]">Front Desk Verification:</p>
                    <div class="h-16 w-36 border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center text-[10px] text-slate-400">
                        Official Hotel Stamp
                    </div>
                </div>
                <div class="text-right space-y-1 flex flex-col justify-end">
                    <div class="w-48 ml-auto border-b border-slate-400 pb-1"></div>
                    <span class="text-[11px] font-bold text-slate-800 block">Authorized Receptionist</span>
                    <span class="text-[10px] text-slate-400 block"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                </div>
            </div>

            <!-- Important Instructions -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1 relative z-10">
                <p class="font-bold flex items-center space-x-1.5 text-amber-950">
                    <i class="fa-solid fa-circle-info text-amber-600"></i>
                    <span>Important Check-In Guidelines:</span>
                </p>
                <p>&bull; Please present this printed voucher or digital QR code along with original Government photo ID (Aadhaar / Driving License / Passport) at the hotel reception during check-in.</p>
                <p>&bull; For early check-in, late arrival, or cab pick-up from Koraput Railway Station, please call our 24/7 reception desk at <strong><?php echo htmlspecialchars($settings['hotel_phone']); ?></strong>.</p>
            </div>

        </div>


        <!-- 3. ACTION BUTTONS STRIP -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4 no-print">
            
            <button type="button" onclick="window.print()" class="btn-gold px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-xl flex items-center space-x-2">
                <i class="fa-solid fa-print text-sm"></i>
                <span>Print Official Voucher / PDF</span>
            </button>

            <a href="<?php echo $waUrl; ?>" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-xl flex items-center space-x-2 transition">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Send to WhatsApp</span>
            </a>

            <button type="button" onclick="copyBookingDetails()" class="bg-white border border-[#d4a359]/40 hover:bg-[#f5efe6] text-[#2b0e14] px-6 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm transition flex items-center space-x-1.5" id="copy-details-btn">
                <i class="fa-regular fa-copy"></i>
                <span>Copy Summary</span>
            </button>

            <a href="index.php" class="bg-slate-100 hover:bg-slate-200 text-slate-700 px-6 py-3.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm transition">
                Back to Home
            </a>

        </div>

    </div>
</div>

<!-- COPY TO CLIPBOARD JAVASCRIPT -->
<script>
function copyBookingDetails() {
    const textToCopy = <?php echo json_encode($waText); ?>;
    navigator.clipboard.writeText(textToCopy).then(() => {
        const btn = document.getElementById('copy-details-btn');
        btn.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i> <span>Copied!</span>';
        setTimeout(() => {
            btn.innerHTML = '<i class="fa-regular fa-copy"></i> <span>Copy Summary</span>';
        }, 2000);
    });
}
</script>

<style>
@media print {
    header, footer, .no-print, .animated-waves {
        display: none !important;
    }
    body {
        background: #ffffff !important;
        color: #000000 !important;
    }
    #voucher-card {
        box-shadow: none !important;
        border: 1px solid #999 !important;
        padding: 20px !important;
        margin: 0 !important;
        width: 100% !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
