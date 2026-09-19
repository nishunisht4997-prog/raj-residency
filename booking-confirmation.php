<?php
// booking-confirmation.php - Printable Reservation Invoice / Slip
require_once __DIR__ . '/includes/functions.php';

$bookingNumber = isset($_GET['booking_number']) ? sanitize($_GET['booking_number']) : '';
$booking = get_booking_by_number($bookingNumber);

if (!$booking) {
    header('Location: index.php');
    exit;
}

$settings = get_settings();
$pageTitle = "Booking Voucher #" . $booking['booking_number'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="py-12 bg-[#fbf9f5] min-h-[80vh]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        
        <!-- Confirmation Alert Card -->
        <div class="bg-emerald-700 text-white rounded-2xl sm:rounded-3xl p-5 sm:p-8 shadow-xl text-center mb-6 sm:mb-8 space-y-2 border border-emerald-500/40">
            <div class="w-12 sm:w-14 h-12 sm:h-14 bg-white/20 rounded-full flex items-center justify-center mx-auto text-xl sm:text-2xl mb-2">
                <i class="fa-solid fa-check text-white"></i>
            </div>
            <h1 class="font-serif text-2xl sm:text-3xl font-bold">Reservation Confirmed!</h1>
            <p class="text-xs sm:text-sm text-emerald-100 font-light max-w-lg mx-auto">
                Thank you for choosing <strong><?php echo htmlspecialchars($settings['hotel_name']); ?></strong>. We look forward to welcoming you in Koraput, Odisha.
            </p>
        </div>

        <!-- Printable Official Voucher -->
        <div id="voucher-card" class="bg-white rounded-2xl sm:rounded-3xl p-5 sm:p-10 border border-[#d4a359]/30 shadow-2xl space-y-6 sm:space-y-8">
            
            <!-- Voucher Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b-2 border-slate-100 gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full border-2 border-[#d4a359] bg-[#2b0e14] flex flex-col items-center justify-center text-[#f3cf8a] text-xs shadow-md">
                        <i class="fa-solid fa-crown text-[10px] text-[#d4a359] mb-0.5"></i>
                        <span class="font-royal text-xs font-black tracking-tighter leading-none text-[#f3cf8a]">RR</span>
                    </div>
                    <div>
                        <span class="font-royal text-xl font-bold uppercase text-[#2b0e14] block"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                        <span class="text-[9px] tracking-widest text-[#d4a359] uppercase font-bold block"><?php echo htmlspecialchars($settings['hotel_tagline']); ?></span>
                        <span class="text-[10px] text-slate-500 block"><?php echo htmlspecialchars($settings['hotel_address']); ?></span>
                    </div>
                </div>

                <div class="sm:text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">BOOKING REFERENCE</span>
                    <strong class="text-lg font-black text-[#2b0e14] bg-[#f5efe6] border border-[#d4a359]/30 px-3 py-1 rounded-lg inline-block"><?php echo htmlspecialchars($booking['booking_number']); ?></strong>
                    <span class="text-[10px] text-slate-400 block mt-1">Booked on: <?php echo date('d M Y, h:i A', strtotime($booking['created_at'])); ?></span>
                </div>
            </div>

            <!-- Guest & Stay Details Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-[#fffdfa] p-6 rounded-2xl border border-[#d4a359]/20">
                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#d4a359]">Guest Information</h4>
                    <div class="text-xs space-y-1.5 text-slate-700">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['guest_name']); ?></p>
                        <p><strong>Mobile:</strong> <?php echo htmlspecialchars($booking['guest_phone']); ?></p>
                        <?php if (!empty($booking['guest_email'])): ?>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['guest_email']); ?></p>
                        <?php endif; ?>
                        <?php if (!empty($booking['id_proof_type'])): ?>
                            <p><strong>ID Proof:</strong> <?php echo htmlspecialchars($booking['id_proof_type']); ?> <?php echo htmlspecialchars($booking['id_proof_number']); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#d4a359]">Stay Schedule</h4>
                    <div class="text-xs space-y-1.5 text-slate-700">
                        <p><strong>Check-In:</strong> <?php echo date('d M Y (D)', strtotime($booking['check_in'])); ?> <span class="text-slate-400 font-normal">at <?php echo htmlspecialchars($settings['check_in_time']); ?></span></p>
                        <p><strong>Check-Out:</strong> <?php echo date('d M Y (D)', strtotime($booking['check_out'])); ?> <span class="text-slate-400 font-normal">at <?php echo htmlspecialchars($settings['check_out_time']); ?></span></p>
                        <p><strong>Total Stay:</strong> <?php echo (int)$booking['total_nights']; ?> Night(s)</p>
                        <p><strong>Guests:</strong> <?php echo (int)$booking['adults']; ?> Adult(s) <?php echo !empty($booking['children']) ? ', ' . (int)$booking['children'] . ' Child(ren)' : ''; ?></p>
                    </div>
                </div>
            </div>

            <!-- Room & Billing Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-[#2b0e14] text-white">
                        <tr>
                            <th class="p-3.5 rounded-l-xl">Room Description</th>
                            <th class="p-3.5 text-center">Nights</th>
                            <th class="p-3.5 text-right">Rate/Night</th>
                            <th class="p-3.5 rounded-r-xl text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-4">
                                <strong class="text-slate-900 block"><?php echo htmlspecialchars($booking['room_name']); ?></strong>
                                <span class="text-[11px] text-slate-500">Includes Complimentary Wi-Fi & Sanitized Room</span>
                            </td>
                            <td class="p-4 text-center font-semibold text-slate-700"><?php echo (int)$booking['total_nights']; ?></td>
                            <td class="p-4 text-right text-slate-700"><?php echo format_price($booking['price_per_night']); ?></td>
                            <td class="p-4 text-right font-bold text-slate-900"><?php echo format_price($booking['price_per_night'] * $booking['total_nights']); ?></td>
                        </tr>
                    </tbody>
                    <tfoot class="border-t-2 border-slate-200">
                        <tr>
                            <td colspan="3" class="p-3 text-right font-semibold text-slate-600">Total Bill (Taxes Included):</td>
                            <td class="p-3 text-right text-base font-black text-[#2b0e14]"><?php echo format_price($booking['total_amount']); ?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="p-2 text-right text-[11px] text-slate-500">Payment Mode & Status:</td>
                            <td class="p-2 text-right text-xs font-bold text-emerald-700">
                                <?php echo htmlspecialchars($booking['payment_method']); ?> (<?php echo htmlspecialchars($booking['payment_status']); ?>)
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Important Instructions -->
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs text-amber-900 space-y-1">
                <p><strong><i class="fa-solid fa-circle-info mr-1"></i> Important Instructions:</strong></p>
                <p>&bull; Please present this voucher and original Government photo ID (Aadhaar / Driving License / Passport) at the hotel front desk during check-in.</p>
                <p>&bull; Need assistance or late check-in? Call our 24-hour reception desk at <strong><?php echo htmlspecialchars($settings['hotel_phone']); ?></strong>.</p>
            </div>

        </div>

        <!-- Action Buttons (Print & WhatsApp) -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-4 no-print">
            <button type="button" onclick="window.print()" class="btn-gold px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2">
                <i class="fa-solid fa-print"></i>
                <span>Print Voucher / Save PDF</span>
            </button>

            <?php 
            $waMessage = "Hello Raj Residency! My booking reference is " . $booking['booking_number'] . " for " . $booking['guest_name'] . " (" . $booking['room_name'] . " on " . $booking['check_in'] . "). Please confirm my reservation.";
            $waLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']) . "?text=" . urlencode($waMessage);
            ?>
            <a href="<?php echo $waLink; ?>" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center space-x-2 transition">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Send to WhatsApp</span>
            </a>

            <a href="reviews.php" class="bg-amber-500 hover:bg-amber-600 text-slate-950 px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-md transition flex items-center space-x-1.5">
                <i class="fa-solid fa-star text-xs"></i>
                <span>Leave a Review</span>
            </a>

            <a href="index.php" class="bg-white border border-[#d4a359]/30 hover:bg-[#f5efe6] text-[#2b0e14] px-7 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm transition">
                Back to Home
            </a>
        </div>

    </div>
</div>

<style>
@media print {
    header, footer, .no-print, .animated-waves {
        display: none !important;
    }
    body {
        background: #ffffff !important;
    }
    #voucher-card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

