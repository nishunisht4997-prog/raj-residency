<?php
// contact.php - Contact Us & Location Page for Raj Residency
require_once __DIR__ . '/includes/functions.php';

$settings = get_settings();
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || empty($phone) || empty($message)) {
        $errorMsg = "Please fill in all required fields (Name, Phone, and Message).";
    } else {
        $saved = save_contact_message([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject ?: 'Website Inquiry',
            'message' => $message
        ]);

        if ($saved) {
            $successMsg = "Thank you! Your inquiry has been sent to Raj Residency front desk. We will call/WhatsApp you shortly.";
        } else {
            $errorMsg = "Could not send your message. Please call or WhatsApp our reception directly.";
        }
    }
}

$pageTitle = "Contact Us";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Banner -->
<section class="bg-[#2b0e14] text-white py-14 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-3">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <span class="text-white">CONTACT US</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            Contact <?php echo htmlspecialchars($settings['hotel_name']); ?>
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto font-light">
            Have questions about room availability, group bookings, or Koraput sightseeing? We are here 24 hours a day to assist you.
        </p>
    </div>
</section>

<!-- Contact Main Section -->
<section class="py-16 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Left Column: Contact Cards & Info -->
            <div class="lg:col-span-5 space-y-6">
                
                <div class="space-y-2">
                    <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                        <span class="w-6 h-[1.5px] bg-[#d4a359]"></span>
                        <i class="fa-solid fa-crown text-[#d4a359] text-[10px]"></i>
                        <span>GET IN TOUCH</span>
                    </div>
                    <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14]">
                        We'd Love to Hear From You
                    </h2>
                </div>

                <!-- Location Card -->
                <div class="p-6 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 shadow-sm flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/30 flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="font-royal text-sm font-bold text-[#2b0e14] uppercase">Hotel Location</h4>
                        <p class="text-xs text-slate-600 font-semibold mt-1 leading-relaxed">
                            <?php echo htmlspecialchars($settings['hotel_address']); ?>
                        </p>
                        <span class="text-[11px] text-slate-400 block mt-1">Prime central landmark in Koraput town</span>
                    </div>
                </div>

                <!-- Phone Card -->
                <div class="p-6 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 shadow-sm flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/30 flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                        <i class="fa-solid fa-phone-volume"></i>
                    </div>
                    <div>
                        <h4 class="font-royal text-sm font-bold text-[#2b0e14] uppercase">Front Desk & Reservations</h4>
                        <div class="mt-1 space-y-0.5">
                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="text-xs font-bold text-[#2b0e14] hover:text-[#b88738] block transition">
                                Direct: <?php echo htmlspecialchars($settings['hotel_phone']); ?>
                            </a>
                            <?php if (!empty($settings['hotel_phone_alt'])): ?>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone_alt']); ?>" class="text-xs text-slate-600 hover:text-[#b88738] block transition">
                                    Alt: <?php echo htmlspecialchars($settings['hotel_phone_alt']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Email Card -->
                <div class="p-6 rounded-3xl bg-[#fffdfa] border border-[#d4a359]/20 shadow-sm flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-2xl bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/30 flex items-center justify-center text-xl flex-shrink-0 shadow-md">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <h4 class="font-royal text-sm font-bold text-[#2b0e14] uppercase">Email Address</h4>
                        <div class="mt-1 space-y-0.5 text-xs">
                            <a href="mailto:<?php echo htmlspecialchars($settings['hotel_email']); ?>" class="text-slate-700 hover:text-[#b88738] block transition">
                                Inquiries: <?php echo htmlspecialchars($settings['hotel_email']); ?>
                            </a>
                            <?php if (!empty($settings['hotel_booking_email'])): ?>
                                <a href="mailto:<?php echo htmlspecialchars($settings['hotel_booking_email']); ?>" class="text-slate-700 hover:text-[#b88738] block transition">
                                    Bookings: <?php echo htmlspecialchars($settings['hotel_booking_email']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- WhatsApp Quick Pill Card -->
                <div class="p-6 rounded-3xl bg-emerald-50 border border-emerald-200 text-emerald-950 flex flex-col justify-between space-y-3">
                    <div class="flex items-center space-x-3">
                        <i class="fa-brands fa-whatsapp text-2xl text-emerald-600"></i>
                        <span class="text-xs font-bold">Instant WhatsApp Desk</span>
                    </div>
                    <p class="text-xs text-emerald-800 font-light">Get instant room availability photos and custom discount quotes directly on WhatsApp.</p>
                    <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $settings['hotel_whatsapp']); ?>?text=Hello%20Raj%20Residency,%20I%20want%20to%20inquire%20about%20room%20booking" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 px-5 rounded-xl text-center text-xs font-bold uppercase tracking-wider transition shadow-sm">
                        Chat With Reception
                    </a>
                </div>

            </div>

            <!-- Right Column: Contact Inquiry Form -->
            <div class="lg:col-span-7">
                <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-5 sm:p-10 border border-[#d4a359]/20 shadow-xl space-y-5 sm:space-y-6">
                    <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#2b0e14]">Send Us a Message</h3>
                    
                    <?php if (!empty($successMsg)): ?>
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center space-x-2">
                            <i class="fa-solid fa-circle-check text-base text-emerald-600"></i>
                            <span><?php echo htmlspecialchars($successMsg); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errorMsg)): ?>
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center space-x-2">
                            <i class="fa-solid fa-triangle-exclamation text-base text-rose-600"></i>
                            <span><?php echo htmlspecialchars($errorMsg); ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="contact.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="send_message">

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Your Name *</label>
                                <input type="text" name="name" required placeholder="e.g. Ananya Mishra" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phone Number *</label>
                                <input type="tel" name="phone" required placeholder="+91 83289 10274" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email Address</label>
                                <input type="email" name="email" placeholder="ananya@example.com" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Subject</label>
                                <input type="text" name="subject" placeholder="Room Booking / Sightseeing / Banquet" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Your Message / Query *</label>
                            <textarea name="message" rows="4" required placeholder="Tell us how we can help you with your stay in Koraput..." class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none"></textarea>
                        </div>

                        <button type="submit" class="btn-gold w-full py-3.5 px-6 rounded-xl text-xs font-bold uppercase tracking-wider shadow-md flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Send Message</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>
</section>

<!-- Google Maps Location Section -->
<section class="bg-[#f5efe6] py-14 border-t border-[#d4a359]/20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl p-4 sm:p-6 border border-[#d4a359]/20 shadow-md">
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="font-serif text-xl font-bold text-[#2b0e14]"><?php echo htmlspecialchars($settings['hotel_name']); ?> on Google Maps</h3>
                    <p class="text-xs text-slate-500 font-light"><?php echo htmlspecialchars($settings['hotel_address']); ?></p>
                </div>
                <a href="https://maps.google.com/?q=<?php echo urlencode($settings['hotel_name'] . ' ' . $settings['hotel_address']); ?>" target="_blank" class="btn-gold px-4 py-2 rounded-xl text-xs font-bold uppercase inline-flex items-center space-x-1 self-start sm:self-auto">
                    <i class="fa-solid fa-diamond-turn-right"></i>
                    <span>Get Directions</span>
                </a>
            </div>
            
            <div class="h-96 w-full rounded-2xl overflow-hidden border border-slate-200">
                <iframe src="<?php echo htmlspecialchars($settings['map_embed_url']); ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

