<?php
// admin/settings.php - Hotel Configuration & Security Settings
require_once __DIR__ . '/../includes/functions.php';
check_admin_auth();

$settings = get_settings();
$adminUser = get_admin_user();

// 1. Handle Hotel Settings Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $newSettings = [
        'hotel_name'         => sanitize($_POST['hotel_name'] ?? 'RAJ RESIDENCY'),
        'hotel_tagline'      => sanitize($_POST['hotel_tagline'] ?? 'ROYAL HERITAGE & LUXURY'),
        'hotel_phone'        => sanitize($_POST['hotel_phone'] ?? '083289 10274'),
        'hotel_phone_alt'    => sanitize($_POST['hotel_phone_alt'] ?? '+91 83289 10274'),
        'hotel_email'        => sanitize($_POST['hotel_email'] ?? 'info@rajresidency.com'),
        'hotel_booking_email'=> sanitize($_POST['hotel_booking_email'] ?? 'booking@rajresidency.com'),
        'hotel_whatsapp'     => sanitize($_POST['hotel_whatsapp'] ?? '918328910274'),
        'hotel_address'      => sanitize($_POST['hotel_address'] ?? 'POST OFFICE ROAD, DIST - KORAPUT, ODISHA, PIN - 764020'),
        'check_in_time'      => sanitize($_POST['check_in_time'] ?? '10:00 AM'),
        'check_out_time'     => sanitize($_POST['check_out_time'] ?? '08:00 AM'),
        'intercom_number'    => sanitize($_POST['intercom_number'] ?? '#222'),
        'breakfast_timing'   => sanitize($_POST['breakfast_timing'] ?? '08:00 AM TO 11:00 AM (A La Carte, Not Buffet)'),
        'lunch_timing'       => sanitize($_POST['lunch_timing'] ?? '11:30 AM TO 03:30 PM (Lunch Thalis Not Available)'),
        'snacks_timing'      => sanitize($_POST['snacks_timing'] ?? '04:00 PM TO 07:00 PM (A La Carte)'),
        'dinner_timing'      => sanitize($_POST['dinner_timing'] ?? '07:00 PM TO 10:00 PM (Thalis Not Available)'),
        'dining_notice'      => sanitize($_POST['dining_notice'] ?? 'No 24/7 In-Room Dining (Strictly within meal times)'),
        'parking_policy'     => sanitize($_POST['parking_policy'] ?? 'Free Secure Hotel Front Side Self Parking (No Basement Parking)'),
        'id_policy'          => sanitize($_POST['id_policy'] ?? 'Government Photo ID Mandatory (Local ID Not Allowed)'),
        'couples_policy'     => sanitize($_POST['couples_policy'] ?? '18+ Years Couples Allowed (Unmarried Couples Allowed)'),
        'extra_person_charge'=> sanitize($_POST['extra_person_charge'] ?? '350'),
        'currency_symbol'    => sanitize($_POST['currency_symbol'] ?? '₹'),
        'tax_percentage'     => sanitize($_POST['tax_percentage'] ?? '12'),
        'map_embed_url'      => trim($_POST['map_embed_url'] ?? '')
    ];

    // Handle Direct Logo File Upload from Mobile / PC
    if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
        $uploadedLogo = upload_media_file($_FILES['logo_file'], 'settings');
        if ($uploadedLogo) {
            $newSettings['hotel_logo'] = $uploadedLogo;
        }
    }

    update_settings($newSettings);
    set_flash_message('success', 'Hotel configuration updated successfully!');
    header('Location: settings.php');
    exit;
}

// 2. Handle Admin Profile & Password Change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_admin_profile'])) {
    $fullName = sanitize($_POST['admin_fullname'] ?? 'Raj Residency Manager');
    $email = sanitize($_POST['admin_email'] ?? 'admin@rajresidency.com');
    $newPass = trim($_POST['new_password'] ?? '');
    $confirmPass = trim($_POST['confirm_password'] ?? '');

    if (!empty($newPass)) {
        if ($newPass !== $confirmPass) {
            set_flash_message('error', 'New password and confirm password do not match.');
            header('Location: settings.php');
            exit;
        }
        if (strlen($newPass) < 6) {
            set_flash_message('error', 'New password must be at least 6 characters long.');
            header('Location: settings.php');
            exit;
        }
    }

    $passToUpdate = !empty($newPass) ? $newPass : null;
    update_admin_profile($adminUser['id'], $fullName, $email, $passToUpdate);
    set_flash_message('success', 'Admin profile and security settings updated successfully!');
    header('Location: settings.php');
    exit;
}

$pageTitle = "Hotel Configuration & Settings";
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="space-y-8">
    
    <!-- TOP HEADER -->
    <div class="royal-card p-6 bg-gradient-to-r from-white to-[#fbf9f5] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-[#2b0e14]">Hotel Information & System Settings</h2>
            <p class="text-xs text-slate-500 font-light mt-0.5">Manage hotel contact numbers, address, booking timings, taxes, and admin credentials.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs font-bold text-slate-600">Hotel Contact:</span>
            <span class="px-3 py-1 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-black"><?php echo htmlspecialchars($settings['hotel_phone']); ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- LEFT: HOTEL INFORMATION SETTINGS (8 cols) -->
        <div class="lg:col-span-8 space-y-6">
            <div class="royal-card p-6 sm:p-8 space-y-6">
                <div class="flex items-center space-x-3 border-b border-[#ebd9c8] pb-4">
                    <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-sm shadow-md">
                        <i class="fa-solid fa-hotel"></i>
                    </div>
                    <div>
                        <h3 class="font-serif text-xl font-bold text-[#2b0e14]">Hotel Brand & Contact Details</h3>
                        <p class="text-xs text-slate-500 font-light">Shown across website header, footer, hero section, and booking receipts</p>
                    </div>
                </div>

                <form action="settings.php" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="save_settings" value="1">

                    <!-- Name & Tagline -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="hotel_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Hotel Name *</label>
                            <input type="text" id="hotel_name" name="hotel_name" required value="<?php echo htmlspecialchars($settings['hotel_name']); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-4 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                        <div>
                            <label for="hotel_tagline" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Tagline</label>
                            <input type="text" id="hotel_tagline" name="hotel_tagline" value="<?php echo htmlspecialchars($settings['hotel_tagline']); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                    </div>

                    <!-- Phone Numbers & WhatsApp -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="hotel_phone" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Primary Phone *</label>
                            <input type="text" id="hotel_phone" name="hotel_phone" required value="<?php echo htmlspecialchars($settings['hotel_phone']); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-[#2b0e14] focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                        <div>
                            <label for="hotel_phone_alt" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Secondary / Mobile Phone</label>
                            <input type="text" id="hotel_phone_alt" name="hotel_phone_alt" value="<?php echo htmlspecialchars($settings['hotel_phone_alt'] ?? ''); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                        <div>
                            <label for="hotel_whatsapp" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">WhatsApp (Digits Only)</label>
                            <input type="text" id="hotel_whatsapp" name="hotel_whatsapp" value="<?php echo htmlspecialchars($settings['hotel_whatsapp']); ?>" placeholder="918328910274" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                    </div>

                    <!-- Emails -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="hotel_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Contact Email</label>
                            <input type="email" id="hotel_email" name="hotel_email" value="<?php echo htmlspecialchars($settings['hotel_email']); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                        <div>
                            <label for="hotel_booking_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Booking Notification Email</label>
                            <input type="email" id="hotel_booking_email" name="hotel_booking_email" value="<?php echo htmlspecialchars($settings['hotel_booking_email'] ?? 'booking@rajresidency.com'); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="hotel_address" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Hotel Physical Address *</label>
                        <input type="text" id="hotel_address" name="hotel_address" required value="<?php echo htmlspecialchars($settings['hotel_address']); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-4 py-2.5 text-xs font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    </div>

                    <!-- Timings & Rates -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                        <div>
                            <label for="check_in_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Check-In</label>
                            <input type="text" id="check_in_time" name="check_in_time" value="<?php echo htmlspecialchars($settings['check_in_time'] ?? '10:00 AM'); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                        <div>
                            <label for="check_out_time" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Check-Out</label>
                            <input type="text" id="check_out_time" name="check_out_time" value="<?php echo htmlspecialchars($settings['check_out_time'] ?? '08:00 AM'); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                        <div>
                            <label for="currency_symbol" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Currency</label>
                            <input type="text" id="currency_symbol" name="currency_symbol" value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? '₹'); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                        <div>
                            <label for="tax_percentage" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">GST Tax (%)</label>
                            <input type="number" id="tax_percentage" name="tax_percentage" value="<?php echo (float)($settings['tax_percentage'] ?? 12); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        </div>
                    </div>

                    <!-- 🍽️ RESTAURANT, MEAL TIMINGS & INTERCOM -->
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-[#fcfaf7] to-[#f5efe6] border border-[#d4a359]/40 space-y-4">
                        <div class="flex items-center space-x-2 text-xs font-black text-[#2b0e14] uppercase border-b border-[#ebd9c8] pb-2">
                            <i class="fa-solid fa-utensils text-[#d4a359]"></i>
                            <span>In-House Restaurant, Meal Timings & Intercom</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="intercom_number" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Intercom Extension</label>
                                <input type="text" id="intercom_number" name="intercom_number" value="<?php echo htmlspecialchars($settings['intercom_number'] ?? '#222'); ?>" placeholder="#222" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-[#2b0e14] focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="dining_notice" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">In-Room Dining Notice</label>
                                <input type="text" id="dining_notice" name="dining_notice" value="<?php echo htmlspecialchars($settings['dining_notice'] ?? 'No 24/7 In-Room Dining (Strictly within meal times)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label for="breakfast_timing" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Breakfast Timing & Menu</label>
                                <input type="text" id="breakfast_timing" name="breakfast_timing" value="<?php echo htmlspecialchars($settings['breakfast_timing'] ?? '08:00 AM TO 11:00 AM (A La Carte, Not Buffet)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="lunch_timing" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Lunch Timing & Policy</label>
                                <input type="text" id="lunch_timing" name="lunch_timing" value="<?php echo htmlspecialchars($settings['lunch_timing'] ?? '11:30 AM TO 03:30 PM (Lunch Thalis Not Available)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="snacks_timing" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Evening Snacks Timing</label>
                                <input type="text" id="snacks_timing" name="snacks_timing" value="<?php echo htmlspecialchars($settings['snacks_timing'] ?? '04:00 PM TO 07:00 PM (A La Carte)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="dinner_timing" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Dinner Timing & Policy</label>
                                <input type="text" id="dinner_timing" name="dinner_timing" value="<?php echo htmlspecialchars($settings['dinner_timing'] ?? '07:00 PM TO 10:00 PM (Thalis Not Available)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>
                    </div>

                    <!-- 📜 HOTEL POLICIES & PARKING -->
                    <div class="p-5 rounded-2xl bg-gradient-to-br from-[#fcfaf7] to-[#f5efe6] border border-[#d4a359]/40 space-y-4">
                        <div class="flex items-center space-x-2 text-xs font-black text-[#2b0e14] uppercase border-b border-[#ebd9c8] pb-2">
                            <i class="fa-solid fa-shield-halved text-[#d4a359]"></i>
                            <span>Hotel Stay Policies, ID Rules & Parking</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="parking_policy" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Parking Policy</label>
                                <input type="text" id="parking_policy" name="parking_policy" value="<?php echo htmlspecialchars($settings['parking_policy'] ?? 'Free Secure Hotel Front Side Self Parking (No Basement Parking)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="id_policy" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Guest ID Policy</label>
                                <input type="text" id="id_policy" name="id_policy" value="<?php echo htmlspecialchars($settings['id_policy'] ?? 'Government Photo ID Mandatory (Local ID Not Allowed)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="couples_policy" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Couples Policy</label>
                                <input type="text" id="couples_policy" name="couples_policy" value="<?php echo htmlspecialchars($settings['couples_policy'] ?? '18+ Years Couples Allowed (Unmarried Couples Allowed)'); ?>" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="extra_person_charge" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Extra Person Charge (₹)</label>
                                <input type="number" id="extra_person_charge" name="extra_person_charge" value="<?php echo (float)($settings['extra_person_charge'] ?? 350); ?>" placeholder="350" class="w-full bg-white border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>
                    </div>

                    <!-- Map URL -->
                    <div>
                        <label for="map_embed_url" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Google Maps Embed URL</label>
                        <textarea id="map_embed_url" name="map_embed_url" rows="2" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl p-3 text-xs text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-[#d4a359]"><?php echo htmlspecialchars($settings['map_embed_url'] ?? ''); ?></textarea>
                    </div>

                    <!-- Submit -->
                    <div class="pt-3 border-t border-[#ebd9c8] flex flex-col sm:flex-row justify-end">
                        <button type="submit" class="w-full sm:w-auto btn-gold px-8 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Save Hotel Configuration</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: ADMIN PROFILE & SECURITY (4 cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="royal-card p-6 sm:p-8 space-y-6">
                <div class="flex items-center space-x-3 border-b border-[#ebd9c8] pb-4">
                    <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-sm shadow-md">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="font-serif text-lg font-bold text-[#2b0e14]">Admin Security</h3>
                        <p class="text-xs text-slate-500 font-light">Profile & password management</p>
                    </div>
                </div>

                <form action="settings.php" method="POST" class="space-y-4">
                    <input type="hidden" name="save_admin_profile" value="1">

                    <div>
                        <label for="admin_username" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Admin Username</label>
                        <input type="text" id="admin_username" disabled value="<?php echo htmlspecialchars($adminUser['username'] ?? 'admin'); ?>" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-500 font-mono cursor-not-allowed">
                    </div>

                    <div>
                        <label for="admin_fullname" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Full Name</label>
                        <input type="text" id="admin_fullname" name="admin_fullname" required value="<?php echo htmlspecialchars($adminUser['full_name'] ?? 'Raj Residency Manager'); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    </div>

                    <div>
                        <label for="admin_email" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Admin Email</label>
                        <input type="email" id="admin_email" name="admin_email" required value="<?php echo htmlspecialchars($adminUser['email'] ?? 'admin@rajresidency.com'); ?>" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    </div>

                    <div class="pt-2 border-t border-[#f5efe6]">
                        <span class="text-[11px] font-bold text-[#2b0e14] block mb-2 uppercase">Change Password</span>
                        
                        <div class="space-y-3">
                            <div>
                                <label for="new_password" class="block text-[10px] font-semibold text-slate-600 mb-1">New Password (Leave blank to keep current)</label>
                                <input type="password" id="new_password" name="new_password" placeholder="Enter new password" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                            <div>
                                <label for="confirm_password" class="block text-[10px] font-semibold text-slate-600 mb-1">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat new password" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-[#ebd9c8]">
                        <button type="submit" class="w-full btn-gold py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-md flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                            <span>Update Profile</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
