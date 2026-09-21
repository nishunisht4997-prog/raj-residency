<?php
// admin/rooms.php - Advanced Rooms & Suites Management with Interactive Multi-Photo Gallery Manager
require_once __DIR__ . '/../includes/functions.php';
check_admin_auth();

$action = $_GET['action'] ?? 'list';
$editId = (int)($_GET['id'] ?? 0);
$editRoom = $editId > 0 ? get_room_by_id($editId) : null;

// Standard Amenities List
$standardAmenities = [
    'Split Air Conditioner',
    'High-Speed Optical Fiber Wi-Fi',
    '43" Full HD Smart TV',
    '24/7 Hot & Cold Geyser Water',
    'Electric Tea/Coffee Maker',
    'Dedicated Work Desk & Chair',
    'Daily Room Sanitization & Housekeeping',
    'Intercom Phone Facility',
    'Scenic Hill / City View Balcony Window',
    'Mini Refrigerator',
    'Complimentary Breakfast Option',
    'Attached Modern Washroom'
];

// Standard Inclusions List
$standardInclusions = [
    'Complimentary High-speed Wi-Fi',
    'Packaged Mineral Water (2 Bottles daily)',
    'Fresh Bath Towels & Luxury Dental/Soap Kits',
    'Free Secure Basement Parking',
    'Welcome Drink on Arrival',
    'Complimentary Royal Buffet Breakfast'
];

// -------------------------------------------------------------
// 1. Handle Room Save (Insert or Update)
// -------------------------------------------------------------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_room'])) {
    $roomId = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? 'ac');
    $typeLabel = sanitize($_POST['type_label'] ?? 'AC Deluxe Room');
    $pricePerNight = (float)($_POST['price_per_night'] ?? 1999);
    $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $maxGuests = (int)($_POST['max_guests'] ?? 2);
    $bedType = sanitize($_POST['bed_type'] ?? 'King Size Bed');
    $roomSize = sanitize($_POST['room_size'] ?? '280 sq.ft');
    $description = sanitize($_POST['description'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isAvailable = isset($_POST['is_available']) ? 1 : 0;

    // Process Amenities
    $selectedAmenities = $_POST['amenities'] ?? [];
    if (!empty($_POST['custom_amenities'])) {
        $customArr = array_map('trim', explode(',', $_POST['custom_amenities']));
        $selectedAmenities = array_merge($selectedAmenities, $customArr);
    }
    $selectedAmenities = array_values(array_filter($selectedAmenities));

    // Process Inclusions
    $selectedInclusions = $_POST['inclusions'] ?? [];
    $selectedInclusions = array_values(array_filter($selectedInclusions));

    // Gallery Photos sent from interactive gallery manager
    $gallery = [];
    if (!empty($_POST['existing_gallery']) && is_array($_POST['existing_gallery'])) {
        $gallery = array_values(array_filter($_POST['existing_gallery']));
    }

    // DIRECT UPLOAD: Multi Gallery / Dropzone Uploads
    if (isset($_FILES['gallery_files'])) {
        $newGalleryUploads = upload_multiple_media_files($_FILES['gallery_files'], 'rooms');
        if (!empty($newGalleryUploads)) {
            $gallery = array_merge($gallery, $newGalleryUploads);
        }
    }

    // DIRECT UPLOAD: Single Cover Photo if uploaded
    if (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] === UPLOAD_ERR_OK) {
        $newFeatured = upload_media_file($_FILES['featured_image_file'], 'rooms');
        if ($newFeatured) {
            array_unshift($gallery, $newFeatured);
            $featuredImage = $newFeatured;
        }
    }

    // Determine Featured Image
    $featuredImage = sanitize($_POST['selected_featured_image'] ?? '');
    if (empty($featuredImage) || !in_array($featuredImage, $gallery)) {
        $featuredImage = $gallery[0] ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80';
    }

    // Ensure featured image is in gallery array
    if (!empty($featuredImage) && !in_array($featuredImage, $gallery)) {
        array_unshift($gallery, $featuredImage);
    }

    $roomData = [
        'name'            => $name,
        'category'        => $category,
        'type_label'      => $typeLabel,
        'price_per_night' => $pricePerNight,
        'discount_price'  => $discountPrice,
        'max_guests'      => $maxGuests,
        'bed_type'        => $bedType,
        'room_size'       => $roomSize,
        'featured_image'  => $featuredImage,
        'gallery'         => array_values(array_unique($gallery)),
        'description'     => $description,
        'amenities'       => $selectedAmenities,
        'inclusions'      => $selectedInclusions,
        'is_featured'     => $isFeatured,
        'is_available'    => $isAvailable
    ];

    if ($roomId) {
        $res = save_room($roomData, $roomId);
        if ($res) {
            set_flash_message('success', 'Room "' . htmlspecialchars($name) . '" updated successfully with ' . count($gallery) . ' gallery photo(s)!');
        } else {
            set_flash_message('error', 'Failed to update room.');
        }
    } else {
        $res = save_room($roomData);
        if ($res) {
            set_flash_message('success', 'New Room "' . htmlspecialchars($name) . '" created successfully!');
        } else {
            set_flash_message('error', 'Failed to create room.');
        }
    }
    header('Location: rooms.php');
    exit;
}

// -------------------------------------------------------------
// 2. Handle Delete Room
// -------------------------------------------------------------
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    delete_room($delId);
    set_flash_message('success', 'Room deleted successfully.');
    header('Location: rooms.php');
    exit;
}

// -------------------------------------------------------------
// 3. Handle Toggle Availability
// -------------------------------------------------------------
if (isset($_GET['toggle'])) {
    $tId = (int)$_GET['toggle'];
    toggle_room_status($tId);
    set_flash_message('success', 'Room availability status updated.');
    header('Location: rooms.php');
    exit;
}

$allRooms = get_all_rooms();
$pageTitle = "Rooms & Suites Management";
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="space-y-6">

    <?php if ($action === 'add' || $action === 'edit'): ?>
        <!-- ============================================================= -->
        <!-- ADD / EDIT ROOM FORM WITH INTERACTIVE GALLERY MANAGER        -->
        <!-- ============================================================= -->
        <?php 
        $isEdit = ($action === 'edit' && $editRoom);
        $currentAmenities = $isEdit ? (is_array($editRoom['amenities']) ? $editRoom['amenities'] : []) : ['Split Air Conditioner', 'High-Speed Optical Fiber Wi-Fi', '24/7 Hot & Cold Geyser Water', 'Daily Room Sanitization & Housekeeping'];
        $currentInclusions = $isEdit ? (is_array($editRoom['inclusions']) ? $editRoom['inclusions'] : []) : ['Complimentary High-speed Wi-Fi', 'Packaged Mineral Water (2 Bottles daily)', 'Free Secure Basement Parking'];
        
        $currentGallery = [];
        if ($isEdit) {
            if (is_array($editRoom['gallery']) && !empty($editRoom['gallery'])) {
                $currentGallery = $editRoom['gallery'];
            } elseif (!empty($editRoom['featured_image'])) {
                $currentGallery = [$editRoom['featured_image']];
            }
        }
        $currentFeatured = $editRoom['featured_image'] ?? ($currentGallery[0] ?? '');
        ?>

        <div class="royal-card p-4 sm:p-6 md:p-8 space-y-6 border border-[#ebd9c8]">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#ebd9c8] pb-4">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-sm shadow-md shrink-0">
                        <i class="fa-solid fa-door-open"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-serif text-lg sm:text-2xl font-bold text-[#2b0e14] truncate">
                            <?php echo $isEdit ? 'Edit Room: ' . htmlspecialchars($editRoom['name']) : 'Add New Room / Suite'; ?>
                        </h2>
                        <p class="text-[11px] sm:text-xs text-slate-500 font-light truncate">Set room pricing, specifications, live drag-and-drop photos, and amenities.</p>
                    </div>
                </div>
                <a href="rooms.php" class="self-start sm:self-auto px-4 py-2 rounded-full border border-slate-300 text-xs font-bold text-slate-600 hover:bg-slate-100 transition flex items-center space-x-1.5 shrink-0">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i>
                    <span>Back to List</span>
                </a>
            </div>

            <form action="rooms.php" method="POST" enctype="multipart/form-data" class="space-y-6" id="room-manage-form">
                <input type="hidden" name="save_room" value="1">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="room_id" value="<?php echo $editRoom['id']; ?>">
                <?php endif; ?>
                <!-- Hidden input for designated Main / Cover Photo -->
                <input type="hidden" name="selected_featured_image" id="selected_featured_image" value="<?php echo htmlspecialchars($currentFeatured); ?>">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    
                    <!-- LEFT COLUMN: FORM FIELDS (8 Cols) -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- Row 1: Basic Information -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Room Title / Name *</label>
                                <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($editRoom['name'] ?? ''); ?>" placeholder="e.g. Executive AC Deluxe Room" oninput="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-4 py-2.5 text-xs text-slate-800 font-bold focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="category" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Category *</label>
                                <select id="category" name="category" required onchange="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                                    <option value="ac" <?php echo ($editRoom['category'] ?? '') === 'ac' ? 'selected' : ''; ?>>AC Rooms (Deluxe / Super)</option>
                                    <option value="non_ac" <?php echo ($editRoom['category'] ?? '') === 'non_ac' ? 'selected' : ''; ?>>Budget Non-AC</option>
                                    <option value="suite" <?php echo ($editRoom['category'] ?? '') === 'suite' ? 'selected' : ''; ?>>Royal Luxury Suite</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Type Label & Pricing -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="type_label" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Badge / Type Label</label>
                                <input type="text" id="type_label" name="type_label" value="<?php echo htmlspecialchars($editRoom['type_label'] ?? 'AC Deluxe Room'); ?>" placeholder="e.g. AC Deluxe Room" oninput="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="price_per_night" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Price / Night (₹) *</label>
                                <input type="number" id="price_per_night" name="price_per_night" step="1" required value="<?php echo (float)($editRoom['price_per_night'] ?? 2499); ?>" oninput="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-black text-[#2b0e14] focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="discount_price" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Cut Price (₹) (Optional)</label>
                                <input type="number" id="discount_price" name="discount_price" step="1" value="<?php echo !empty($editRoom['discount_price']) ? (float)$editRoom['discount_price'] : ''; ?>" placeholder="e.g. 2999" oninput="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>

                        <!-- Row 3: Specifications -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="max_guests" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Max Guests</label>
                                <input type="number" id="max_guests" name="max_guests" min="1" max="10" value="<?php echo (int)($editRoom['max_guests'] ?? 2); ?>" oninput="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="bed_type" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Bed Configuration</label>
                                <input type="text" id="bed_type" name="bed_type" value="<?php echo htmlspecialchars($editRoom['bed_type'] ?? 'King Size Bed'); ?>" placeholder="e.g. King Size Bed" oninput="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>

                            <div>
                                <label for="room_size" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Room Area / Size</label>
                                <input type="text" id="room_size" name="room_size" value="<?php echo htmlspecialchars($editRoom['room_size'] ?? '280 sq.ft'); ?>" placeholder="e.g. 280 sq.ft" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>

                        <!-- ========================================================= -->
                        <!-- INTERACTIVE MULTI-PHOTO GALLERY & DRAG-AND-DROP MANAGER   -->
                        <!-- ========================================================= -->
                        <div class="p-6 rounded-3xl bg-gradient-to-br from-[#fcfaf7] to-[#f5efe6] border-2 border-[#d4a359]/40 space-y-4 shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#ebd9c8] pb-3">
                                <div>
                                    <div class="flex items-center space-x-2 text-xs font-black text-[#2b0e14] uppercase">
                                        <i class="fa-solid fa-images text-[#d4a359]"></i>
                                        <span>Interactive Multi-Photo Gallery Manager</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 font-light">Drag & drop photos or click to upload. Set any photo as Main Cover or delete with 1 click.</p>
                                </div>
                                <span class="px-3 py-1 rounded-full text-[10px] font-black bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/30" id="gallery-count-badge">
                                    <?php echo count($currentGallery); ?> Photos in Gallery
                                </span>
                            </div>

                            <!-- 1. DRAG & DROP UPLOAD DROPZONE -->
                            <div id="dropzone-area" class="border-2 border-dashed border-[#d4a359] rounded-2xl p-6 text-center bg-white/90 hover:bg-[#fffdfa] hover:border-[#b88738] transition cursor-pointer relative group">
                                <input type="file" id="multi-gallery-input" name="gallery_files[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                
                                <div class="space-y-2 pointer-events-none">
                                    <div class="w-12 h-12 mx-auto rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-xl shadow-md group-hover:scale-110 transition duration-300">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-[#2b0e14]">
                                            <span class="text-[#b88738] underline">Click to choose photos</span> or Drag & Drop multiple images here
                                        </p>
                                        <p class="text-[10px] text-slate-500 font-medium mt-0.5">Supports JPG, PNG, WEBP from Phone Camera, Mobile Gallery, or PC</p>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. INTERACTIVE PHOTO GALLERY GRID -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-[11px] font-bold uppercase text-slate-600">
                                    <span>Active Room Gallery (<span id="photo-cards-count"><?php echo count($currentGallery); ?></span>)</span>
                                    <span class="text-[10px] text-slate-400 font-normal">Click ⭐ to set as Main Cover Photo</span>
                                </div>

                                <div id="interactive-gallery-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <?php foreach ($currentGallery as $idx => $photo): ?>
                                        <?php
                                        $photoSrc = $photo;
                                        if (!str_starts_with($photoSrc, 'http') && !str_starts_with($photoSrc, '/')) {
                                            $photoSrc = '../' . $photoSrc;
                                        }
                                        $isMain = ($photo === $currentFeatured || ($idx === 0 && empty($currentFeatured)));
                                        ?>
                                        <div class="gallery-photo-card relative rounded-2xl overflow-hidden border-2 <?php echo $isMain ? 'border-[#d4a359] shadow-lg ring-2 ring-[#d4a359]/40' : 'border-slate-200'; ?> bg-white group shadow-sm transition" data-photo-path="<?php echo htmlspecialchars($photo); ?>">
                                            <input type="hidden" name="existing_gallery[]" value="<?php echo htmlspecialchars($photo); ?>">
                                            
                                            <!-- Image Thumbnail -->
                                            <div class="h-28 w-full bg-slate-100 overflow-hidden">
                                                <img src="<?php echo htmlspecialchars($photoSrc); ?>" alt="Room photo" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                            </div>

                                            <!-- Main Cover Badge / Button -->
                                            <div class="absolute top-2 left-2 z-10">
                                                <?php if ($isMain): ?>
                                                    <span class="main-badge px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359] shadow-md flex items-center space-x-1">
                                                        <i class="fa-solid fa-crown text-[8px] text-[#d4a359]"></i>
                                                        <span>Cover</span>
                                                    </span>
                                                <?php else: ?>
                                                    <button type="button" onclick="setMainPhoto('<?php echo htmlspecialchars($photo); ?>', this)" class="set-main-btn px-2 py-0.5 rounded-full text-[9px] font-bold bg-white/90 hover:bg-[#2b0e14] hover:text-[#f3cf8a] text-slate-700 shadow-md border border-slate-300 transition flex items-center space-x-1" title="Set as Main Cover Photo">
                                                        <i class="fa-regular fa-star text-[8px]"></i>
                                                        <span>Set Main</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <!-- 1-Click Delete / Remove Button -->
                                            <div class="absolute top-2 right-2 z-10">
                                                <button type="button" onclick="removeGalleryPhoto(this, '<?php echo htmlspecialchars($photo); ?>')" class="w-6 h-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center text-[10px] shadow-md transition hover:scale-110" title="Remove Photo from Gallery">
                                                    <i class="fa-solid fa-trash-can text-[9px]"></i>
                                                </button>
                                            </div>

                                            <!-- Bottom Info Pill -->
                                            <div class="p-1.5 bg-slate-50 border-t border-slate-100 text-center">
                                                <span class="text-[9px] text-slate-500 font-mono truncate block"><?php echo htmlspecialchars(basename($photo)); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Newly Dropped / Selected Files Preview Grid -->
                                <div id="new-uploads-preview-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 pt-2"></div>
                            </div>

                        </div>

                        <!-- Room Description -->
                        <div>
                            <label for="description" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Room Description *</label>
                            <textarea id="description" name="description" rows="3" required placeholder="Describe the room comfort, views, and key attractions..." oninput="updateLiveCard()" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl p-3.5 text-xs text-slate-800 leading-relaxed focus:outline-none focus:ring-2 focus:ring-[#d4a359]"><?php echo htmlspecialchars($editRoom['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Amenities Checklist -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                Room Amenities & Features
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8]">
                                <?php foreach ($standardAmenities as $amenity): ?>
                                    <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer p-1.5 rounded-lg hover:bg-white transition">
                                        <input type="checkbox" name="amenities[]" value="<?php echo htmlspecialchars($amenity); ?>" <?php echo in_array($amenity, $currentAmenities) ? 'checked' : ''; ?> class="w-4 h-4 text-[#2b0e14] rounded border-slate-300 focus:ring-[#d4a359]">
                                        <span><?php echo htmlspecialchars($amenity); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <div>
                                <input type="text" name="custom_amenities" placeholder="Additional custom amenities (comma-separated, e.g. Jacuzzi, Hill View Balcony)" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                            </div>
                        </div>

                        <!-- Inclusions Checklist -->
                        <div class="space-y-2">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">
                                Complimentary Inclusions
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5 p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8]">
                                <?php foreach ($standardInclusions as $incl): ?>
                                    <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer p-1.5 rounded-lg hover:bg-white transition">
                                        <input type="checkbox" name="inclusions[]" value="<?php echo htmlspecialchars($incl); ?>" <?php echo in_array($incl, $currentInclusions) ? 'checked' : ''; ?> class="w-4 h-4 text-[#2b0e14] rounded border-slate-300 focus:ring-[#d4a359]">
                                        <span><?php echo htmlspecialchars($incl); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Toggles: Featured & Available -->
                        <div class="flex flex-wrap items-center gap-6 pt-2">
                            <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" <?php echo !empty($editRoom['is_featured']) || !$isEdit ? 'checked' : ''; ?> class="w-4 h-4 text-[#2b0e14] rounded border-slate-300 focus:ring-[#d4a359]">
                                <span>Show on Homepage Featured Showcase</span>
                            </label>

                            <label class="flex items-center space-x-2 text-xs font-bold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="is_available" value="1" <?php echo !empty($editRoom['is_available']) || !$isEdit ? 'checked' : ''; ?> class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                <span>Room is Active & Available for Booking</span>
                            </label>
                        </div>

                        <!-- Form Submit Actions -->
                        <div class="pt-4 border-t border-[#ebd9c8] flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3">
                            <a href="rooms.php" class="text-center px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 transition">Cancel</a>
                            <button type="submit" class="btn-gold px-8 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                                <i class="fa-solid fa-floppy-disk text-xs"></i>
                                <span><?php echo $isEdit ? 'Update Room & Gallery' : 'Save & Publish Room'; ?></span>
                            </button>
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: LIVE ROOM CARD PREVIEW (4 Cols) -->
                    <div class="lg:col-span-4 space-y-4">
                        <div class="sticky top-24 space-y-4">
                            <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-[#ebd9c8] pb-2">
                                <span class="flex items-center space-x-1 text-[#2b0e14]">
                                    <i class="fa-solid fa-eye text-[#d4a359]"></i>
                                    <span>Live Public Card Preview</span>
                                </span>
                                <span class="text-[10px] text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">Real-time</span>
                            </div>

                            <!-- PREVIEW CARD WIDGET -->
                            <div class="royal-card overflow-hidden shadow-xl border-2 border-[#d4a359]/50 bg-white">
                                <!-- Cover Photo -->
                                <div class="relative h-48 bg-slate-900 overflow-hidden">
                                    <img id="live-card-img" src="<?php echo htmlspecialchars($currentFeatured ? (str_starts_with($currentFeatured, 'http') || str_starts_with($currentFeatured, '/') ? $currentFeatured : '../' . $currentFeatured) : 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1000&q=80'); ?>" alt="Preview" class="w-full h-full object-cover">
                                    
                                    <!-- Badge -->
                                    <div class="absolute top-3 left-3">
                                        <span id="live-card-badge" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359]/40 shadow-md">
                                            <?php echo htmlspecialchars($editRoom['type_label'] ?? 'AC Deluxe Room'); ?>
                                        </span>
                                    </div>

                                    <!-- Price -->
                                    <div class="absolute bottom-3 right-3 bg-[#2b0e14]/95 text-[#f3cf8a] px-3 py-1.5 rounded-xl text-xs font-black border border-[#d4a359]/40 shadow-lg">
                                        <span id="live-card-price">₹<?php echo number_format($editRoom['price_per_night'] ?? 2499, 0); ?></span>
                                        <span class="text-[10px] text-slate-300 font-normal">/ night</span>
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-5 space-y-3">
                                    <div>
                                        <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                            <span class="font-bold text-[#b88738]" id="live-card-category">AC DELUXE</span>
                                            <span id="live-card-guests">Max <?php echo (int)($editRoom['max_guests'] ?? 2); ?> Guests</span>
                                        </div>
                                        <h3 class="font-serif text-lg font-bold text-[#2b0e14] line-clamp-1" id="live-card-title">
                                            <?php echo htmlspecialchars($editRoom['name'] ?? 'Executive AC Deluxe Room'); ?>
                                        </h3>
                                        <p class="text-xs text-slate-600 line-clamp-2 mt-1 font-light leading-relaxed" id="live-card-desc">
                                            <?php echo htmlspecialchars($editRoom['description'] ?? 'Luxurious air-conditioned room with king-size bed, high-speed Wi-Fi, and 24/7 hot water.'); ?>
                                        </p>
                                    </div>

                                    <div class="pt-3 border-t border-[#f5efe6] flex items-center justify-between">
                                        <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                                            <i class="fa-solid fa-check mr-1"></i> Instant Booking Available
                                        </span>
                                        <span class="btn-gold px-4 py-1.5 rounded-full text-[10px] font-bold uppercase shadow-sm">Reserve</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Helpful Tips -->
                            <div class="p-4 rounded-2xl bg-[#fbf9f5] border border-[#ebd9c8] space-y-2 text-xs text-slate-600">
                                <div class="font-bold text-[#2b0e14] flex items-center space-x-1.5">
                                    <i class="fa-solid fa-lightbulb text-[#d4a359]"></i>
                                    <span>Gallery Tips:</span>
                                </div>
                                <ul class="space-y-1 text-[11px] text-slate-500 list-disc pl-4 font-light">
                                    <li>Add at least 3-4 photos (Bed, Washroom, Balcony, Amenities) for higher booking conversions.</li>
                                    <li>Click ⭐ on any thumbnail to set it as the primary cover photo.</li>
                                    <li>Photos are automatically optimized and saved to MySQL database.</li>
                                </ul>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>

    <?php else: ?>
        <!-- ============================================================= -->
        <!-- ROOMS CATALOG LIST VIEW                                       -->
        <!-- ============================================================= -->
        
        <div class="royal-card p-6 bg-gradient-to-r from-white via-[#fdfbf7] to-[#fbf9f5] flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-[#ebd9c8]">
            <div>
                <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                    <i class="fa-solid fa-bed text-[#d4a359]"></i>
                    <span>INVENTORY & PHOTO MANAGEMENT</span>
                </div>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-[#2b0e14] mt-0.5">Rooms & Accommodations</h2>
                <p class="text-xs text-slate-500 font-light mt-0.5">Manage room rates, categories, multi-photo galleries, and booking availability.</p>
            </div>
            
            <a href="rooms.php?action=add" class="btn-gold px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Add New Room</span>
            </a>
        </div>

        <!-- ROOMS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($allRooms as $room): ?>
                <?php
                $imgSrc = $room['featured_image'];
                if (!str_starts_with($imgSrc, 'http') && !str_starts_with($imgSrc, '/')) {
                    $imgSrc = '../' . $imgSrc;
                }
                $galleryPhotos = is_array($room['gallery']) ? $room['gallery'] : [$room['featured_image']];
                $galleryCount = count($galleryPhotos);
                ?>
                <div class="royal-card overflow-hidden flex flex-col justify-between group hover:shadow-2xl transition border border-[#ebd9c8]">
                    
                    <!-- Cover Image & Badges -->
                    <div class="relative h-52 bg-slate-900 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($room['name']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        
                        <!-- Availability Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold shadow-md <?php echo !empty($room['is_available']) ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'; ?>">
                                <i class="fa-solid <?php echo !empty($room['is_available']) ? 'fa-check' : 'fa-ban'; ?> text-[9px]"></i>
                                <span><?php echo !empty($room['is_available']) ? 'Available' : 'Unavailable'; ?></span>
                            </span>
                        </div>

                        <!-- Gallery Photo Counter Button -->
                        <button type="button" onclick="openRoomGalleryModal(<?php echo htmlspecialchars(json_encode($room)); ?>)" class="absolute top-3 right-3 bg-[#2b0e14]/90 backdrop-blur-md text-[#f3cf8a] hover:bg-[#3d141d] px-2.5 py-1 rounded-full text-[10px] font-bold border border-[#d4a359]/40 shadow-lg flex items-center space-x-1.5 transition" title="View Full Photo Gallery">
                            <i class="fa-solid fa-images text-[9px] text-[#d4a359]"></i>
                            <span><?php echo $galleryCount; ?> Photos</span>
                        </button>

                        <!-- Price Tag -->
                        <div class="absolute bottom-3 right-3 bg-[#2b0e14]/95 backdrop-blur-md text-[#f3cf8a] px-3.5 py-1.5 rounded-xl text-xs font-black border border-[#d4a359]/40 shadow-lg">
                            ₹<?php echo number_format($room['price_per_night'], 0); ?> <span class="text-[10px] text-slate-300 font-normal">/ night</span>
                        </div>
                    </div>

                    <!-- Room Details Content -->
                    <div class="p-5 space-y-3 flex-grow flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                <span class="font-bold uppercase tracking-wider text-[#b88738]"><?php echo htmlspecialchars($room['type_label']); ?></span>
                                <span>Max <?php echo (int)$room['max_guests']; ?> Guests</span>
                            </div>
                            <h3 class="font-serif text-lg font-bold text-[#2b0e14] line-clamp-1">
                                <?php echo htmlspecialchars($room['name']); ?>
                            </h3>
                            <p class="text-xs text-slate-600 line-clamp-2 mt-1 font-light leading-relaxed">
                                <?php echo htmlspecialchars($room['description']); ?>
                            </p>
                        </div>

                        <!-- Quick Gallery Strip Preview -->
                        <?php if ($galleryCount > 1): ?>
                            <div class="flex items-center space-x-1.5 pt-2 overflow-hidden">
                                <?php foreach (array_slice($galleryPhotos, 0, 5) as $gThumb): ?>
                                    <?php
                                    $tSrc = $gThumb;
                                    if (!str_starts_with($tSrc, 'http') && !str_starts_with($tSrc, '/')) {
                                        $tSrc = '../' . $tSrc;
                                    }
                                    ?>
                                    <img src="<?php echo htmlspecialchars($tSrc); ?>" class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                                <?php endforeach; ?>
                                <?php if ($galleryCount > 5): ?>
                                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold flex items-center justify-center border border-slate-200">+<?php echo $galleryCount - 5; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="pt-3 border-t border-[#f5efe6] flex flex-wrap items-center justify-between gap-2">
                            <a href="rooms.php?toggle=<?php echo $room['id']; ?>" class="text-[11px] sm:text-xs font-bold px-3 py-1.5 rounded-xl border <?php echo !empty($room['is_available']) ? 'border-amber-300 text-amber-800 bg-amber-50/50 hover:bg-amber-100' : 'border-emerald-300 text-emerald-800 bg-emerald-50/50 hover:bg-emerald-100'; ?> transition flex items-center space-x-1">
                                <i class="fa-solid <?php echo !empty($room['is_available']) ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-slate-400'; ?>"></i>
                                <span><?php echo !empty($room['is_available']) ? 'Set Unavailable' : 'Set Available'; ?></span>
                            </a>

                            <div class="flex items-center space-x-1.5 ml-auto">
                                <a href="../room-details.php?id=<?php echo $room['id']; ?>" target="_blank" class="p-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 text-xs border border-blue-200 shadow-sm transition" title="View Live Room Details Page">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                </a>
                                <a href="rooms.php?action=edit&id=<?php echo $room['id']; ?>" class="p-2 rounded-xl bg-[#2b0e14] text-[#f3cf8a] hover:bg-[#3d141d] text-xs shadow-sm transition" title="Edit Room & Manage Photos">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <a href="rooms.php?delete=<?php echo $room['id']; ?>" onclick="return confirm('Are you sure you want to delete this room?');" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs border border-rose-200 shadow-sm transition" title="Delete Room">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<!-- ============================================================= -->
<!-- MODAL: QUICK ROOM GALLERY VIEWER                              -->
<!-- ============================================================= -->
<div id="room-gallery-modal" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-3 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-3xl w-full p-4 sm:p-6 shadow-2xl border border-[#ebd9c8] space-y-4 max-h-[90vh] overflow-y-auto custom-scrollbar">
        <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3">
            <div class="min-w-0 pr-2">
                <h3 class="font-serif text-base sm:text-lg font-bold text-[#2b0e14] truncate" id="modal-gallery-title">Room Photo Gallery</h3>
                <p class="text-[11px] sm:text-xs text-slate-500 font-light truncate" id="modal-gallery-subtitle"></p>
            </div>
            <button type="button" onclick="document.getElementById('room-gallery-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 shrink-0">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div id="modal-gallery-photos-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3"></div>

        <div class="pt-3 border-t border-[#ebd9c8] flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-2">
            <button type="button" onclick="document.getElementById('room-gallery-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 text-center">Close</button>
            <a href="#" id="modal-gallery-edit-link" class="btn-gold px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider flex items-center justify-center space-x-1.5">
                <i class="fa-solid fa-pencil text-xs"></i>
                <span>Edit Photos & Details</span>
            </a>
        </div>
    </div>
</div>

<script>
    // 1. Interactive Set Main Photo
    function setMainPhoto(photoPath, btnElement) {
        document.getElementById('selected_featured_image').value = photoPath;
        
        // Update all cards in grid
        const allCards = document.querySelectorAll('.gallery-photo-card');
        allCards.forEach(card => {
            const cardPath = card.getAttribute('data-photo-path');
            const badgeContainer = card.querySelector('.absolute.top-2.left-2');
            
            if (cardPath === photoPath) {
                card.className = 'gallery-photo-card relative rounded-2xl overflow-hidden border-2 border-[#d4a359] shadow-lg ring-2 ring-[#d4a359]/40 bg-white group shadow-sm transition';
                badgeContainer.innerHTML = '<span class="main-badge px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-[#2b0e14] text-[#f3cf8a] border border-[#d4a359] shadow-md flex items-center space-x-1"><i class="fa-solid fa-crown text-[8px] text-[#d4a359]"></i><span>Cover</span></span>';
                
                // Update live card preview
                let src = photoPath;
                if (!src.startsWith('http') && !src.startsWith('/')) {
                    src = '../' + src;
                }
                const liveImg = document.getElementById('live-card-img');
                if (liveImg) liveImg.src = src;
            } else {
                card.className = 'gallery-photo-card relative rounded-2xl overflow-hidden border-2 border-slate-200 bg-white group shadow-sm transition';
                badgeContainer.innerHTML = `<button type="button" onclick="setMainPhoto('${cardPath}', this)" class="set-main-btn px-2 py-0.5 rounded-full text-[9px] font-bold bg-white/90 hover:bg-[#2b0e14] hover:text-[#f3cf8a] text-slate-700 shadow-md border border-slate-300 transition flex items-center space-x-1" title="Set as Main Cover Photo"><i class="fa-regular fa-star text-[8px]"></i><span>Set Main</span></button>`;
            }
        });
    }

    // 2. 1-Click Delete Photo from Active Gallery
    function removeGalleryPhoto(btnElement, photoPath) {
        if (!confirm('Remove this photo from room gallery?')) return;
        
        const card = btnElement.closest('.gallery-photo-card');
        if (card) {
            card.classList.add('opacity-0', 'scale-90');
            setTimeout(() => {
                card.remove();
                
                // If this was the main photo, promote first available photo
                const currentMain = document.getElementById('selected_featured_image').value;
                if (currentMain === photoPath) {
                    const firstCard = document.querySelector('.gallery-photo-card');
                    if (firstCard) {
                        const newMainPath = firstCard.getAttribute('data-photo-path');
                        setMainPhoto(newMainPath, firstCard);
                    } else {
                        document.getElementById('selected_featured_image').value = '';
                    }
                }
                
                // Update counter
                updateGalleryCount();
            }, 250);
        }
    }

    function updateGalleryCount() {
        const count = document.querySelectorAll('.gallery-photo-card').length;
        const countEl = document.getElementById('photo-cards-count');
        const badgeEl = document.getElementById('gallery-count-badge');
        if (countEl) countEl.innerText = count;
        if (badgeEl) badgeEl.innerText = count + ' Photos in Gallery';
    }

    // 3. Multi-Photo Drag & Drop Listener
    const dropzone = document.getElementById('dropzone-area');
    const multiInput = document.getElementById('multi-gallery-input');
    const newPreviewGrid = document.getElementById('new-uploads-preview-grid');

    if (dropzone && multiInput) {
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('border-[#2b0e14]', 'bg-[#faf4ec]', 'scale-[1.01]');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('border-[#2b0e14]', 'bg-[#faf4ec]', 'scale-[1.01]');
            }, false);
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            multiInput.files = files;
            handleSelectedFiles(files);
        });

        multiInput.addEventListener('change', function() {
            handleSelectedFiles(this.files);
        });
    }

    function handleSelectedFiles(files) {
        if (!newPreviewGrid) return;
        newPreviewGrid.innerHTML = '';
        
        if (files.length > 0) {
            Array.from(files).forEach((file, i) => {
                if (!file.type.startsWith('image/')) return;
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const card = document.createElement('div');
                    card.className = 'relative rounded-2xl overflow-hidden border-2 border-emerald-400 bg-white shadow-md animate-fade-in';
                    card.innerHTML = `
                        <div class="h-28 w-full bg-slate-100 overflow-hidden">
                            <img src="${e.target.result}" class="w-full h-full object-cover">
                        </div>
                        <div class="absolute top-2 left-2">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-600 text-white shadow-sm flex items-center space-x-1">
                                <i class="fa-solid fa-plus text-[8px]"></i>
                                <span>New</span>
                            </span>
                        </div>
                        <div class="p-1.5 bg-emerald-50 text-center">
                            <span class="text-[9px] text-emerald-800 font-bold truncate block">${file.name}</span>
                        </div>
                    `;
                    newPreviewGrid.appendChild(card);
                };
                reader.readAsDataURL(file);
            });
        }
    }

    // 4. Live Room Preview Card updater
    function updateLiveCard() {
        const nameVal = document.getElementById('name')?.value || 'Room Title';
        const priceVal = document.getElementById('price_per_night')?.value || '2499';
        const badgeVal = document.getElementById('type_label')?.value || 'AC Deluxe Room';
        const descVal = document.getElementById('description')?.value || 'Room description';
        const guestsVal = document.getElementById('max_guests')?.value || '2';
        const catVal = document.getElementById('category')?.value || 'ac';

        if (document.getElementById('live-card-title')) document.getElementById('live-card-title').innerText = nameVal;
        if (document.getElementById('live-card-price')) document.getElementById('live-card-price').innerText = '₹' + Number(priceVal).toLocaleString();
        if (document.getElementById('live-card-badge')) document.getElementById('live-card-badge').innerText = badgeVal;
        if (document.getElementById('live-card-desc')) document.getElementById('live-card-desc').innerText = descVal;
        if (document.getElementById('live-card-guests')) document.getElementById('live-card-guests').innerText = 'Max ' + guestsVal + ' Guests';
        if (document.getElementById('live-card-category')) document.getElementById('live-card-category').innerText = catVal.toUpperCase();
    }

    // 5. Open Quick Room Gallery Modal
    function openRoomGalleryModal(room) {
        document.getElementById('modal-gallery-title').innerText = room.name;
        document.getElementById('modal-gallery-subtitle').innerText = room.type_label + ' | ' + (room.gallery ? room.gallery.length : 1) + ' Photos';
        document.getElementById('modal-gallery-edit-link').href = 'rooms.php?action=edit&id=' + room.id;

        const grid = document.getElementById('modal-gallery-photos-grid');
        grid.innerHTML = '';

        const photos = Array.isArray(room.gallery) ? room.gallery : [room.featured_image];
        photos.forEach((p, idx) => {
            let src = p;
            if (!src.startsWith('http') && !src.startsWith('/')) {
                src = '../' + src;
            }
            const isCover = (p === room.featured_image || idx === 0);
            const item = document.createElement('div');
            item.className = 'relative rounded-2xl overflow-hidden border border-slate-200 shadow-sm group h-40 bg-slate-900';
            item.innerHTML = `
                <img src="${src}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                ${isCover ? '<div class="absolute top-2 left-2 bg-[#2b0e14] text-[#f3cf8a] text-[9px] font-black px-2 py-0.5 rounded-full border border-[#d4a359]">COVER</div>' : ''}
            `;
            grid.appendChild(item);
        });

        document.getElementById('room-gallery-modal').classList.remove('hidden');
    }
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
