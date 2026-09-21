<?php
// admin/reviews.php - Guest Reviews Moderation & Management
require_once __DIR__ . '/../includes/functions.php';
check_admin_auth();

// 1. Handle Approve / Unapprove
if (isset($_GET['approve'])) {
    $revId = (int)$_GET['approve'];
    update_review_status($revId, 1);
    set_flash_message('success', 'Review approved and published to public website!');
    header('Location: reviews.php');
    exit;
}
if (isset($_GET['unapprove'])) {
    $revId = (int)$_GET['unapprove'];
    update_review_status($revId, 0);
    set_flash_message('success', 'Review unapproved and hidden from public website.');
    header('Location: reviews.php');
    exit;
}

// 2. Handle Toggle Featured
if (isset($_GET['toggle_featured']) && isset($_GET['id'])) {
    $revId = (int)$_GET['id'];
    $isFeatured = (int)$_GET['toggle_featured'];
    update_review_status($revId, 1, $isFeatured);
    set_flash_message('success', 'Review featured status updated.');
    header('Location: reviews.php');
    exit;
}

// 3. Handle Delete Review
if (isset($_GET['delete'])) {
    $revId = (int)$_GET['delete'];
    delete_review($revId);
    set_flash_message('success', 'Review deleted successfully.');
    header('Location: reviews.php');
    exit;
}

// 4. Handle Add Review Manually
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $guestName = sanitize($_POST['guest_name'] ?? '');
    $guestCity = sanitize($_POST['guest_city'] ?? 'Guest');
    $roomStayed = sanitize($_POST['room_stayed'] ?? 'Executive AC Deluxe');
    $rating = (int)($_POST['rating'] ?? 5);
    $title = sanitize($_POST['title'] ?? '');
    $reviewText = sanitize($_POST['review_text'] ?? '');
    $isApproved = isset($_POST['is_approved']) ? 1 : 0;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;

    save_review([
        'guest_name' => $guestName,
        'guest_city' => $guestCity,
        'room_stayed' => $roomStayed,
        'rating' => $rating,
        'title' => $title,
        'review_text' => $reviewText,
        'is_approved' => $isApproved,
        'is_featured' => $isFeatured
    ]);

    set_flash_message('success', 'New review successfully added!');
    header('Location: reviews.php');
    exit;
}

$allReviews = get_all_reviews();
$reviewStats = get_review_stats();
$pageTitle = "Guest Reviews Moderation";
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="space-y-6">
    
    <!-- TOP HEADER -->
    <div class="royal-card p-6 bg-gradient-to-r from-white to-[#fbf9f5] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-[#2b0e14]">Guest Reviews & Ratings Moderation</h2>
            <p class="text-xs text-slate-500 font-light mt-0.5">Approve, feature, and manage testimonials submitted by guests.</p>
        </div>
        <button type="button" onclick="document.getElementById('add-review-modal').classList.remove('hidden')" class="btn-gold px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Add Review Manually</span>
        </button>
    </div>

    <!-- STATS OVERVIEW -->
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4">
        <div class="royal-card p-4 sm:p-5 text-center space-y-1 col-span-2 sm:col-span-1">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Average Rating</span>
            <div class="text-2xl sm:text-3xl font-black text-[#2b0e14] flex items-center justify-center space-x-1">
                <span><?php echo $reviewStats['average']; ?></span>
                <i class="fa-solid fa-star text-[#d4a359] text-lg sm:text-xl"></i>
            </div>
            <span class="text-[10px] sm:text-[11px] text-slate-400">Based on approved reviews</span>
        </div>

        <div class="royal-card p-4 sm:p-5 text-center space-y-1">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Approved</span>
            <div class="text-2xl sm:text-3xl font-black text-emerald-700"><?php echo $reviewStats['total']; ?></div>
            <span class="text-[10px] sm:text-[11px] text-slate-400">Visible on website</span>
        </div>

        <div class="royal-card p-4 sm:p-5 text-center space-y-1">
            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pending Moderation</span>
            <?php 
            $pendingCount = count(array_filter($allReviews, function($r) { return empty($r['is_approved']); }));
            ?>
            <div class="text-2xl sm:text-3xl font-black text-amber-700"><?php echo $pendingCount; ?></div>
            <span class="text-[10px] sm:text-[11px] text-slate-400">Awaiting approval</span>
        </div>
    </div>

    <!-- REVIEWS GRID -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($allReviews)): ?>
            <div class="col-span-full royal-card p-12 text-center text-slate-400 space-y-2">
                <i class="fa-solid fa-star-half-stroke text-3xl text-slate-300 block"></i>
                <p>No guest reviews in database yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($allReviews as $rv): ?>
                <div class="royal-card p-5 space-y-4 flex flex-col justify-between border <?php echo empty($rv['is_approved']) ? 'border-amber-300 bg-amber-50/20' : 'border-[#ebd9c8]'; ?>">
                    
                    <div>
                        <!-- Header: Name, City & Status -->
                        <div class="flex items-start justify-between gap-2 border-b border-[#ebd9c8] pb-3">
                            <div>
                                <strong class="text-sm text-[#2b0e14] block"><?php echo htmlspecialchars($rv['guest_name']); ?></strong>
                                <span class="text-[10px] text-slate-500 font-medium"><?php echo htmlspecialchars($rv['guest_city']); ?> &bull; <?php echo htmlspecialchars($rv['room_stayed']); ?></span>
                            </div>

                            <div class="flex flex-col items-end space-y-1">
                                <?php if (!empty($rv['is_approved'])): ?>
                                    <span class="text-[9px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded-full">Approved</span>
                                <?php else: ?>
                                    <span class="text-[9px] bg-amber-100 text-amber-800 font-bold px-2 py-0.5 rounded-full">Pending</span>
                                <?php endif; ?>

                                <?php if (!empty($rv['is_featured'])): ?>
                                    <span class="text-[9px] bg-[#2b0e14] text-[#f3cf8a] font-bold px-2 py-0.5 rounded-full border border-[#d4a359]/40">Featured</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Stars & Title -->
                        <div class="pt-3 space-y-1">
                            <div class="text-[#d4a359] text-xs flex space-x-1">
                                <?php for ($i = 0; $i < (int)$rv['rating']; $i++): ?>
                                    <i class="fa-solid fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <h4 class="font-serif text-sm font-bold text-slate-800">
                                "<?php echo htmlspecialchars($rv['title']); ?>"
                            </h4>
                            <p class="text-xs text-slate-600 font-light leading-relaxed">
                                <?php echo htmlspecialchars($rv['review_text']); ?>
                            </p>
                        </div>
                    </div>

                    <!-- Footer: Date & Actions -->
                    <div class="pt-3 border-t border-[#f5efe6] flex items-center justify-between gap-2 text-xs">
                        <span class="text-[10px] text-slate-400"><?php echo format_datetime($rv['created_at'], 'd M Y'); ?></span>
                        
                        <div class="flex items-center space-x-1.5">
                            <?php if (empty($rv['is_approved'])): ?>
                                <a href="reviews.php?approve=<?php echo $rv['id']; ?>" class="px-2.5 py-1 rounded-lg bg-emerald-700 text-white text-[10px] font-bold hover:bg-emerald-800">Approve</a>
                            <?php else: ?>
                                <a href="reviews.php?unapprove=<?php echo $rv['id']; ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-[10px] font-bold hover:bg-slate-200">Hide</a>
                                <a href="reviews.php?toggle_featured=<?php echo !empty($rv['is_featured']) ? '0' : '1'; ?>&id=<?php echo $rv['id']; ?>" class="px-2 py-1 rounded-lg border border-[#d4a359] text-[#b88738] text-[10px] font-bold hover:bg-[#d4a359] hover:text-[#2b0e14]">
                                    <?php echo !empty($rv['is_featured']) ? 'Unfeature' : 'Feature'; ?>
                                </a>
                            <?php endif; ?>

                            <a href="reviews.php?delete=<?php echo $rv['id']; ?>" onclick="return confirm('Delete this review?');" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 text-[10px]" title="Delete">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- ADD REVIEW MANUALLY MODAL -->
<div id="add-review-modal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-3 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-lg w-full p-4 sm:p-6 md:p-8 shadow-2xl border border-[#ebd9c8] space-y-4 sm:space-y-5 max-h-[90vh] overflow-y-auto custom-scrollbar">
        
        <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3 sm:pb-4">
            <div class="flex items-center space-x-3 min-w-0 pr-2">
                <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-sm shadow-md shrink-0">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-serif text-base sm:text-lg font-bold text-[#2b0e14] truncate">Add Guest Review</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-light truncate">From offline guest registers or feedback forms</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('add-review-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 shrink-0">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="reviews.php" method="POST" class="space-y-4">
            <input type="hidden" name="add_review" value="1">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="guest_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Guest Name *</label>
                    <input type="text" id="guest_name" name="guest_name" required placeholder="e.g. Ramesh Chandra" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="guest_city" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Guest City / State</label>
                    <input type="text" id="guest_city" name="guest_city" placeholder="e.g. Bhubaneswar, Odisha" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="room_stayed" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Room Category</label>
                    <input type="text" id="room_stayed" name="room_stayed" value="Executive AC Deluxe Room" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="rating" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Rating (Stars)</label>
                    <select id="rating" name="rating" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                        <option value="5">5 Stars (Excellent)</option>
                        <option value="4">4 Stars (Very Good)</option>
                        <option value="3">3 Stars (Average)</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="title" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Review Headline / Title *</label>
                <input type="text" id="title" name="title" required placeholder="e.g. Best hospitality & clean rooms in Koraput!" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
            </div>

            <div>
                <label for="review_text" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Review Content *</label>
                <textarea id="review_text" name="review_text" rows="3" required placeholder="Write guest experience feedback..." class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl p-3.5 text-xs text-slate-800 leading-relaxed focus:outline-none focus:ring-2 focus:ring-[#d4a359]"></textarea>
            </div>

            <div class="flex flex-wrap items-center gap-3 pt-1">
                <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_approved" value="1" checked class="w-4 h-4 text-[#2b0e14] rounded">
                    <span>Approve Immediately</span>
                </label>
                <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_featured" value="1" checked class="w-4 h-4 text-[#2b0e14] rounded">
                    <span>Feature on Homepage</span>
                </label>
            </div>

            <div class="pt-3 border-t border-[#ebd9c8] flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:space-x-3">
                <button type="button" onclick="document.getElementById('add-review-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 text-center">Cancel</button>
                <button type="submit" class="btn-gold px-7 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Save Review</span>
                </button>
            </div>
        </form>

    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
