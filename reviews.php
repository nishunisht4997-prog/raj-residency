<?php
// reviews.php - Guest Reviews & Ratings Page for Raj Residency
require_once __DIR__ . '/includes/functions.php';

$settings = get_settings();
$successMsg = '';
$errorMsg = '';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    $guestName = sanitize($_POST['guest_name'] ?? '');
    $guestCity = sanitize($_POST['guest_city'] ?? '');
    $roomStayed = sanitize($_POST['room_stayed'] ?? 'Executive AC Deluxe Room');
    $rating = (int)($_POST['rating'] ?? 5);
    $title = sanitize($_POST['title'] ?? '');
    $reviewText = sanitize($_POST['review_text'] ?? '');

    if (empty($guestName) || empty($title) || empty($reviewText)) {
        $errorMsg = "Please fill in all required fields (Your Name, Title, and Review text).";
    } else {
        $saved = save_review([
            'guest_name' => $guestName,
            'guest_city' => $guestCity ?: 'Valued Guest',
            'room_stayed' => $roomStayed,
            'rating' => max(1, min(5, $rating)),
            'title' => $title,
            'review_text' => $reviewText,
            'is_approved' => 1, // Directly visible on website
            'is_featured' => 0
        ]);

        if ($saved) {
            $successMsg = "Thank you! Your review and star rating have been published successfully.";
        } else {
            $errorMsg = "Could not submit your review. Please try again or contact our front desk.";
        }
    }
}

$reviews = get_approved_reviews();
$stats = get_review_stats();
$allRooms = get_all_rooms();

$pageTitle = "Guest Reviews & Ratings";
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Banner -->
<section class="bg-[#2b0e14] text-white py-14 border-b border-[#d4a359]/30 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center space-y-3">
        <div class="flex items-center justify-center space-x-2 text-[11px] font-bold tracking-widest text-[#f3cf8a] uppercase">
            <a href="index.php" class="hover:underline">HOME</a>
            <span>/</span>
            <span class="text-white">GUEST REVIEWS</span>
        </div>
        <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
            Guest Reviews & Ratings
        </h1>
        <p class="text-xs sm:text-sm text-slate-300 max-w-2xl mx-auto font-light">
            Real feedback and experiences from guests who stayed at <?php echo htmlspecialchars($settings['hotel_name']); ?>, Koraput.
        </p>
    </div>
</section>

<!-- Overview & Review Form Section -->
<section class="py-16 bg-[#fbf9f5]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            <!-- Left Column: Rating Score & Submit Form -->
            <div class="lg:col-span-5 space-y-8">
                
                <!-- Rating Score Box -->
                <div class="bg-[#2b0e14] rounded-2xl sm:rounded-3xl p-6 sm:p-8 text-white border border-[#d4a359]/40 shadow-2xl space-y-4 sm:space-y-5 text-center">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#f3cf8a] block">OVERALL GUEST SATISFACTION</span>
                    <div class="flex items-center justify-center space-x-3">
                        <span class="text-4xl sm:text-5xl font-black text-white"><?php echo $stats['average']; ?></span>
                        <div class="text-left">
                            <div class="text-[#d4a359] text-sm sm:text-base flex space-x-0.5">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa-solid fa-star <?php echo ($i <= round($stats['average'])) ? 'text-[#d4a359]' : 'text-slate-600'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="text-[11px] sm:text-xs text-slate-300 font-semibold block mt-0.5">Based on <?php echo count($reviews); ?>+ Verified Stays</span>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-700/60 text-xs text-slate-300 flex items-center justify-around">
                        <div><strong class="text-white font-bold block text-sm"><?php echo $stats['five_star']; ?></strong> 5-Star Reviews</div>
                        <div class="h-6 w-[1px] bg-slate-700"></div>
                        <div><strong class="text-white font-bold block text-sm">100%</strong> Verified Guests</div>
                    </div>
                </div>

                <!-- Review Submission Card -->
                <div class="bg-[#fffdfa] rounded-2xl sm:rounded-3xl p-5 sm:p-8 border border-[#d4a359]/20 shadow-md space-y-4 sm:space-y-5">
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2 text-[10px] font-bold tracking-widest text-[#d4a359] uppercase">
                            <i class="fa-solid fa-pen-nib text-[#d4a359]"></i>
                            <span>SHARE YOUR STAY EXPERIENCE</span>
                        </div>
                        <h3 class="font-serif text-xl sm:text-2xl font-bold text-[#2b0e14]">Write a Review</h3>
                    </div>

                    <?php if (!empty($successMsg)): ?>
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-start space-x-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 mt-0.5 text-base flex-shrink-0"></i>
                            <span><?php echo htmlspecialchars($successMsg); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($errorMsg)): ?>
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-start space-x-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600 mt-0.5 text-base flex-shrink-0"></i>
                            <span><?php echo htmlspecialchars($errorMsg); ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="reviews.php" method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="submit_review">

                        <!-- Interactive Star Rating Selector -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Your Overall Rating *</label>
                            <div class="flex items-center space-x-2" id="star-rating-picker">
                                <input type="hidden" name="rating" id="selected-rating-input" value="5">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                    <button type="button" onclick="setRating(<?php echo $s; ?>)" class="star-btn text-2xl text-[#d4a359] focus:outline-none transition hover:scale-110" data-val="<?php echo $s; ?>">
                                        <i class="fa-solid fa-star"></i>
                                    </button>
                                <?php endfor; ?>
                                <span class="text-xs font-bold text-[#2b0e14] ml-2" id="rating-label">5 Stars (Excellent)</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Your Full Name *</label>
                                <input type="text" name="guest_name" required placeholder="e.g. Ramesh Mohanty" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">City / Location</label>
                                <input type="text" name="guest_city" placeholder="e.g. Bhubaneswar / Vizag" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Room Stayed In</label>
                            <select name="room_stayed" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                                <?php foreach ($allRooms as $rm): ?>
                                    <option value="<?php echo htmlspecialchars($rm['name']); ?>"><?php echo htmlspecialchars($rm['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Review Headline / Title *</label>
                            <input type="text" name="title" required placeholder="e.g. Clean AC room and wonderful staff service" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Detailed Feedback *</label>
                            <textarea name="review_text" rows="3" required placeholder="How was your stay in Koraput? How was the room cleanliness, AC, Wi-Fi, and hospitality?" class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:ring-2 focus:ring-[#d4a359] focus:outline-none"></textarea>
                        </div>

                        <button type="submit" class="w-full btn-gold py-3 px-5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-md flex items-center justify-center space-x-2">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>Submit Review & Rating</span>
                        </button>
                    </form>
                </div>

            </div>

            <!-- Right Column: Approved Reviews Grid -->
            <div class="lg:col-span-7 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="font-serif text-2xl font-bold text-[#2b0e14]">Verified Guest Feedback</h3>
                    <span class="text-xs text-slate-500 font-medium"><?php echo count($reviews); ?> Reviews</span>
                </div>

                <?php if (empty($reviews)): ?>
                    <div class="text-center py-16 bg-[#fffdfa] rounded-3xl border border-[#d4a359]/20 p-8 space-y-2">
                        <i class="fa-regular fa-star text-4xl text-slate-300"></i>
                        <p class="text-xs font-semibold text-slate-600">Be the first to leave a review for Raj Residency!</p>
                    </div>
                <?php else: ?>
                    <div class="space-y-5">
                        <?php foreach ($reviews as $rv): ?>
                            <div class="bg-[#fffdfa] rounded-3xl p-6 sm:p-8 border border-[#d4a359]/20 shadow-sm hover:border-[#d4a359] transition duration-300 space-y-4">
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-11 h-11 rounded-full bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/30 flex items-center justify-center font-bold text-sm shadow-md">
                                            <?php echo strtoupper(substr($rv['guest_name'] ?? 'G', 0, 1)); ?>
                                        </div>
                                        <div>
                                            <strong class="text-sm text-[#2b0e14] block"><?php echo htmlspecialchars($rv['guest_name']); ?></strong>
                                            <span class="text-[11px] text-slate-500">
                                                <i class="fa-solid fa-location-dot text-[9px] text-[#d4a359] mr-0.5"></i>
                                                <?php echo htmlspecialchars($rv['guest_city']); ?> &bull; Stayed in <strong><?php echo htmlspecialchars($rv['room_stayed']); ?></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col sm:items-end">
                                        <div class="text-[#d4a359] text-xs flex space-x-0.5">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fa-solid fa-star <?php echo ($i <= (int)$rv['rating']) ? 'text-[#d4a359]' : 'text-slate-300'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="text-[10px] text-slate-400 mt-1"><?php echo date('d M Y', strtotime($rv['created_at'])); ?></span>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-serif font-bold text-base text-[#2b0e14] mb-1.5"><?php echo htmlspecialchars($rv['title']); ?></h4>
                                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                                        &ldquo;<?php echo nl2br(htmlspecialchars($rv['review_text'])); ?>&rdquo;
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-slate-100 flex items-center space-x-2 text-[11px] text-emerald-700 font-bold">
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span>Verified Guest Stay at Raj Residency</span>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</section>

<script>
function setRating(val) {
    document.getElementById('selected-rating-input').value = val;
    const buttons = document.querySelectorAll('.star-btn');
    const label = document.getElementById('rating-label');

    const labels = {
        1: '1 Star (Poor)',
        2: '2 Stars (Fair)',
        3: '3 Stars (Good)',
        4: '4 Stars (Very Good)',
        5: '5 Stars (Excellent)'
    };
    if (label) label.innerText = labels[val] || (val + ' Stars');

    buttons.forEach((btn, index) => {
        if (index < val) {
            btn.classList.add('text-[#d4a359]');
            btn.classList.remove('text-slate-300');
        } else {
            btn.classList.remove('text-[#d4a359]');
            btn.classList.add('text-slate-300');
        }
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

