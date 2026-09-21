<?php
// admin/media.php - Direct Mobile Camera/Gallery & PC Image Manager
require_once __DIR__ . '/../includes/functions.php';
check_admin_auth();

// Handle Actions: Upload, Edit, Toggle, Delete
$activeTab = $_GET['tab'] ?? 'hero_slider';

// 1. Handle New Direct File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_image') {
    $sectionName = sanitize($_POST['section_name'] ?? 'hero_slider');
    $title = sanitize($_POST['title'] ?? 'Banner Image');
    $badgeText = sanitize($_POST['badge_text'] ?? '');
    $displayOrder = (int)($_POST['display_order'] ?? 1);
    
    // Choose subfolder based on section
    $subfolder = 'hero';
    if (strpos($sectionName, 'about') !== false) {
        $subfolder = 'about';
    } elseif (strpos($sectionName, 'archway') !== false || strpos($sectionName, 'preview') !== false) {
        $subfolder = 'archway';
    }

    $imagePath = '';
    
    // Direct file upload from Mobile Camera / Gallery or PC
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploaded = upload_media_file($_FILES['image_file'], $subfolder);
        if ($uploaded) {
            $imagePath = $uploaded;
        } else {
            set_flash_message('error', 'Failed to process uploaded image. Please ensure it is a valid JPG, PNG, WEBP, or GIF file.');
            header('Location: media.php?tab=' . urlencode($sectionName));
            exit;
        }
    } else {
        set_flash_message('error', 'Please select a photo from your phone gallery/camera or computer to upload.');
        header('Location: media.php?tab=' . urlencode($sectionName));
        exit;
    }

    if (!empty($imagePath)) {
        $saved = save_section_image([
            'section_name'  => $sectionName,
            'title'         => $title,
            'image_path'    => $imagePath,
            'badge_text'    => $badgeText,
            'display_order' => $displayOrder,
            'is_active'     => 1
        ]);

        if ($saved) {
            set_flash_message('success', 'Photo successfully uploaded from device and published!');
        } else {
            set_flash_message('error', 'Failed to save image metadata to database.');
        }
    }
    header('Location: media.php?tab=' . urlencode($sectionName));
    exit;
}

// 2. Handle Replace Image File directly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'replace_image') {
    $imageId = (int)($_POST['image_id'] ?? 0);
    $existing = get_section_image_by_id($imageId);
    
    if ($existing) {
        $subfolder = 'hero';
        if (strpos($existing['section_name'], 'about') !== false) {
            $subfolder = 'about';
        } elseif (strpos($existing['section_name'], 'archway') !== false || strpos($existing['section_name'], 'preview') !== false) {
            $subfolder = 'archway';
        }

        if (isset($_FILES['replace_file']) && $_FILES['replace_file']['error'] === UPLOAD_ERR_OK) {
            $uploaded = upload_media_file($_FILES['replace_file'], $subfolder);
            if ($uploaded) {
                $existing['image_path'] = $uploaded;
                if (!empty($_POST['title'])) $existing['title'] = sanitize($_POST['title']);
                if (isset($_POST['badge_text'])) $existing['badge_text'] = sanitize($_POST['badge_text']);
                save_section_image($existing, $imageId);
                set_flash_message('success', 'Image successfully replaced with new upload from device!');
            } else {
                set_flash_message('error', 'Failed to upload replacement image.');
            }
        } else {
            // Just update title / badge if no new file
            if (!empty($_POST['title'])) $existing['title'] = sanitize($_POST['title']);
            if (isset($_POST['badge_text'])) $existing['badge_text'] = sanitize($_POST['badge_text']);
            if (isset($_POST['display_order'])) $existing['display_order'] = (int)$_POST['display_order'];
            save_section_image($existing, $imageId);
            set_flash_message('success', 'Image details updated successfully.');
        }
        header('Location: media.php?tab=' . urlencode($existing['section_name']));
        exit;
    }
}

// 3. Handle Toggle Active / Inactive
if (isset($_GET['toggle'])) {
    $toggleId = (int)$_GET['toggle'];
    $img = get_section_image_by_id($toggleId);
    if ($img) {
        toggle_section_image_status($toggleId);
        set_flash_message('success', 'Image status updated.');
        header('Location: media.php?tab=' . urlencode($img['section_name']));
        exit;
    }
}

// 4. Handle Delete Image
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $img = get_section_image_by_id($deleteId);
    if ($img) {
        delete_section_image($deleteId);
        set_flash_message('success', 'Image deleted successfully.');
        header('Location: media.php?tab=' . urlencode($img['section_name']));
        exit;
    }
}

// Fetch all banners for current tab
$allSectionImages = get_all_section_images();
$pageTitle = "Hero & Image Manager";
require_once __DIR__ . '/includes/admin_header.php';

$tabSections = [
    'hero_slider' => [
        'name' => 'Hero Background Slider',
        'icon' => 'fa-solid fa-panorama',
        'desc' => 'Fullscreen rotating background photos displayed in the main hero section of the home page.'
    ],
    'hero_archway' => [
        'name' => 'Archway Box & Previews',
        'icon' => 'fa-solid fa-archway',
        'desc' => 'The majestic royal archway frame photo and the 2 floating badge preview cards on the home page hero.'
    ],
    'about_rooms' => [
        'name' => 'About Us: Luxury Rooms',
        'icon' => 'fa-solid fa-bed',
        'desc' => 'Crossfading room slideshow images on the home page and about page.'
    ],
    'about_dining' => [
        'name' => 'About Us: Dining & Cuisine',
        'icon' => 'fa-solid fa-utensils',
        'desc' => 'Restaurant, dining, and breakfast slider photos.'
    ],
    'about_building' => [
        'name' => 'Story & Building Photo',
        'icon' => 'fa-solid fa-hotel',
        'desc' => 'Main architectural photo of Raj Residency shown in the About Us heritage story.'
    ]
];

// Filter images based on tab
if ($activeTab === 'hero_archway') {
    $filteredImages = array_filter($allSectionImages, function($b) {
        return in_array($b['section_name'] ?? '', ['hero_archway', 'hero_preview_1', 'hero_preview_2']);
    });
} else {
    $filteredImages = array_filter($allSectionImages, function($b) use ($activeTab) {
        return ($b['section_name'] ?? '') === $activeTab;
    });
}
?>

<div class="space-y-6">
    
    <!-- TOP HEADER WITH DIRECT UPLOAD CTA -->
    <div class="royal-card p-6 bg-gradient-to-r from-white to-[#fbf9f5] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 text-[11px] font-bold tracking-widest text-[#d4a359] uppercase">
                <i class="fa-solid fa-camera text-[#d4a359]"></i>
                <span>DIRECT MOBILE & PC PHOTO MANAGER</span>
            </div>
            <h2 class="font-serif text-2xl font-bold text-[#2b0e14] mt-1">Manage Website Images & Banners</h2>
            <p class="text-xs text-slate-500 font-light mt-0.5">
                Upload new photos directly from your smartphone camera, photo library, or PC. No URLs required.
            </p>
        </div>

        <button type="button" onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="btn-gold px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
            <i class="fa-solid fa-circle-plus text-sm"></i>
            <span>Upload New Photo</span>
        </button>
    </div>

    <!-- SECTION TABS (Swipeable on Mobile) -->
    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-3 border-b border-[#ebd9c8] whitespace-nowrap flex-nowrap md:flex-wrap">
        <?php foreach ($tabSections as $key => $info): ?>
            <?php 
            $isActiveTab = ($activeTab === $key);
            $count = count(array_filter($allSectionImages, function($b) use ($key) {
                if ($key === 'hero_archway') return in_array($b['section_name'] ?? '', ['hero_archway', 'hero_preview_1', 'hero_preview_2']);
                return ($b['section_name'] ?? '') === $key;
            }));
            ?>
            <a href="media.php?tab=<?php echo $key; ?>" class="shrink-0 px-4 py-2.5 rounded-2xl text-xs font-bold transition flex items-center space-x-2 <?php echo $isActiveTab ? 'bg-[#2b0e14] text-[#f3cf8a] shadow-md border border-[#d4a359]/40' : 'bg-white text-slate-700 hover:bg-[#f5efe6] border border-[#ebd9c8]'; ?>">
                <i class="<?php echo $info['icon']; ?> text-xs"></i>
                <span><?php echo $info['name']; ?></span>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold <?php echo $isActiveTab ? 'bg-[#d4a359] text-[#2b0e14]' : 'bg-slate-100 text-slate-600'; ?>"><?php echo $count; ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- TAB DESCRIPTION NOTICE -->
    <div class="p-4 rounded-2xl bg-[#f5efe6] border border-[#ebd9c8] text-xs text-slate-700 flex items-center space-x-3">
        <i class="fa-solid fa-circle-info text-[#d4a359] text-base"></i>
        <span><?php echo $tabSections[$activeTab]['desc'] ?? 'Manage section photos'; ?></span>
    </div>

    <!-- IMAGE GRID CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($filteredImages)): ?>
            <div class="col-span-full royal-card p-12 text-center text-slate-400 space-y-3">
                <i class="fa-solid fa-images text-4xl text-slate-300 block"></i>
                <h4 class="font-serif text-lg font-bold text-slate-600">No images uploaded for this section yet</h4>
                <p class="text-xs text-slate-500">Tap the "Upload New Photo" button above to add photos from your phone or PC.</p>
                <button type="button" onclick="document.getElementById('upload-modal').classList.remove('hidden')" class="btn-gold px-6 py-2.5 rounded-full text-xs font-bold uppercase shadow-sm inline-flex items-center space-x-2">
                    <i class="fa-solid fa-camera"></i>
                    <span>Upload Now</span>
                </button>
            </div>
        <?php else: ?>
            <?php foreach ($filteredImages as $img): ?>
                <?php
                $imgSrc = $img['image_path'];
                if (!str_starts_with($imgSrc, 'http') && !str_starts_with($imgSrc, '/')) {
                    $imgSrc = '../' . $imgSrc;
                }
                ?>
                <div class="royal-card overflow-hidden flex flex-col justify-between group hover:shadow-xl transition border border-[#ebd9c8]">
                    
                    <!-- Image Display Area -->
                    <div class="relative h-48 bg-slate-900 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($img['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        
                        <!-- Status Badge -->
                        <div class="absolute top-3 left-3">
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-bold shadow-md <?php echo !empty($img['is_active']) ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'; ?>">
                                <i class="fa-solid <?php echo !empty($img['is_active']) ? 'fa-check' : 'fa-eye-slash'; ?> text-[9px]"></i>
                                <span><?php echo !empty($img['is_active']) ? 'Active on Site' : 'Hidden / Inactive'; ?></span>
                            </span>
                        </div>

                        <!-- Section Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="bg-[#2b0e14]/90 backdrop-blur-sm text-[#f3cf8a] border border-[#d4a359]/40 px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase">
                                <?php echo htmlspecialchars($img['section_name']); ?>
                            </span>
                        </div>

                        <?php if (!empty($img['badge_text'])): ?>
                            <div class="absolute bottom-3 left-3 bg-black/70 backdrop-blur-sm text-[#f3cf8a] text-[10px] font-bold px-2.5 py-1 rounded-lg border border-[#d4a359]/30">
                                <?php echo htmlspecialchars($img['badge_text']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Details Area -->
                    <div class="p-4 space-y-3 flex-grow flex flex-col justify-between">
                        <div>
                            <h4 class="font-serif text-base font-bold text-[#2b0e14] line-clamp-1">
                                <?php echo htmlspecialchars($img['title']); ?>
                            </h4>
                            <div class="text-[11px] text-slate-400 mt-1 flex items-center justify-between">
                                <span>Order: #<?php echo (int)($img['display_order'] ?? 1); ?></span>
                                <span class="font-mono text-[9px] truncate max-w-[150px]"><?php echo basename($img['image_path']); ?></span>
                            </div>
                        </div>

                        <!-- Action Toolbar -->
                        <div class="pt-3 border-t border-[#f5efe6] flex items-center justify-between gap-2">
                            
                            <!-- Toggle Active Button -->
                            <a href="media.php?toggle=<?php echo $img['id']; ?>&tab=<?php echo urlencode($activeTab); ?>" class="px-3 py-1.5 rounded-xl text-[11px] font-bold border transition <?php echo !empty($img['is_active']) ? 'bg-slate-50 text-slate-700 hover:bg-slate-100 border-slate-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-300'; ?>">
                                <i class="fa-solid <?php echo !empty($img['is_active']) ? 'fa-eye-slash' : 'fa-eye'; ?> mr-1"></i>
                                <span><?php echo !empty($img['is_active']) ? 'Hide' : 'Activate'; ?></span>
                            </a>

                            <div class="flex items-center space-x-1.5">
                                <!-- Replace / Edit Button -->
                                <button type="button" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($img)); ?>)" class="p-2 rounded-xl bg-[#2b0e14] text-[#f3cf8a] hover:bg-[#3d141d] transition text-xs" title="Replace Photo / Edit Details">
                                    <i class="fa-solid fa-camera-rotate"></i>
                                </button>

                                <!-- Delete Button -->
                                <a href="media.php?delete=<?php echo $img['id']; ?>&tab=<?php echo urlencode($activeTab); ?>" onclick="return confirm('Are you sure you want to delete this image?');" class="p-2 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition text-xs" title="Delete Image">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>

                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<!-- 1. DIRECT MOBILE/PC FILE UPLOAD MODAL -->
<div id="upload-modal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-3 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-lg w-full p-4 sm:p-6 md:p-8 shadow-2xl border border-[#ebd9c8] space-y-4 sm:space-y-5 max-h-[90vh] overflow-y-auto custom-scrollbar">
        
        <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3 sm:pb-4">
            <div class="flex items-center space-x-3 min-w-0 pr-2">
                <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-sm shadow-md shrink-0">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-serif text-base sm:text-lg font-bold text-[#2b0e14] truncate">Upload New Photo</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-light truncate">From Mobile Camera, Gallery, or PC</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 shrink-0">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="media.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="upload_image">

            <!-- Section Choice -->
            <div>
                <label for="modal_section_name" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Select Section / Placement
                </label>
                <select id="modal_section_name" name="section_name" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                    <option value="hero_slider" <?php echo $activeTab === 'hero_slider' ? 'selected' : ''; ?>>Hero Background Rotating Slider</option>
                    <option value="hero_archway" <?php echo $activeTab === 'hero_archway' ? 'selected' : ''; ?>>Hero Archway Palace Frame (Main Box)</option>
                    <option value="hero_preview_1">Hero Floating Card 1 (AC Suites Preview)</option>
                    <option value="hero_preview_2">Hero Floating Card 2 (Deomali Tours Preview)</option>
                    <option value="about_rooms" <?php echo $activeTab === 'about_rooms' ? 'selected' : ''; ?>>About Us: Luxury Rooms Crossfade Slide</option>
                    <option value="about_dining" <?php echo $activeTab === 'about_dining' ? 'selected' : ''; ?>>About Us: Dining & Cuisine Slide</option>
                    <option value="about_building" <?php echo $activeTab === 'about_building' ? 'selected' : ''; ?>>About Page: Hotel Heritage Building Photo</option>
                </select>
            </div>

            <!-- Title & Badge -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="modal_title" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Photo Title / Label</label>
                    <input type="text" id="modal_title" name="title" required placeholder="e.g. Imperial Deluxe Lounge" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="modal_badge_text" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Badge Text (Optional)</label>
                    <input type="text" id="modal_badge_text" name="badge_text" placeholder="e.g. ROYAL HERITAGE" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Display Order -->
            <div>
                <label for="modal_display_order" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Display Sort Order</label>
                <input type="number" id="modal_display_order" name="display_order" value="1" min="1" class="w-28 bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
            </div>

            <!-- DIRECT FILE UPLOAD INPUT WITH INSTANT LIVE PREVIEW -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">
                    <i class="fa-solid fa-camera mr-1 text-[#d4a359]"></i> Select Photo (Mobile Camera, Gallery or PC)
                </label>
                
                <div class="border-2 border-dashed border-[#d4a359] rounded-2xl p-4 text-center bg-[#fdfaf6] hover:bg-[#faf4ec] transition relative cursor-pointer">
                    <input type="file" name="image_file" accept="image/*" required data-preview-target="#upload-live-preview" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                    <div class="space-y-2 py-2">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-[#d4a359]"></i>
                        <p class="text-xs font-bold text-[#2b0e14]">Tap to Take Photo or Choose from Gallery</p>
                        <p class="text-[10px] text-slate-500">Supports JPG, PNG, WEBP, GIF (Max 15MB)</p>
                    </div>
                </div>

                <!-- Instant Live Preview Image Container -->
                <div class="text-center pt-2">
                    <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Selected Photo Live Preview:</p>
                    <img id="upload-live-preview" src="" alt="Live Preview" class="w-full h-44 object-cover rounded-xl border border-[#d4a359]/60 shadow-md hidden mx-auto">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-t border-[#ebd9c8] flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:space-x-3">
                <button type="button" onclick="document.getElementById('upload-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 text-center">Cancel</button>
                <button type="submit" class="btn-gold px-7 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Save & Publish Photo</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- 2. REPLACE PHOTO / EDIT DETAILS MODAL -->
<div id="edit-modal" class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-3 sm:p-4 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl max-w-lg w-full p-4 sm:p-6 md:p-8 shadow-2xl border border-[#ebd9c8] space-y-4 sm:space-y-5 max-h-[90vh] overflow-y-auto custom-scrollbar">
        
        <div class="flex items-center justify-between border-b border-[#ebd9c8] pb-3 sm:pb-4">
            <div class="flex items-center space-x-3 min-w-0 pr-2">
                <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center text-sm shadow-md shrink-0">
                    <i class="fa-solid fa-camera-rotate"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-serif text-base sm:text-lg font-bold text-[#2b0e14] truncate">Replace Photo / Edit</h3>
                    <p class="text-[10px] sm:text-[11px] text-slate-500 font-light truncate">Update photo from device or edit title</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 p-1 shrink-0">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="media.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="replace_image">
            <input type="hidden" id="edit_image_id" name="image_id" value="">

            <div>
                <label for="edit_title" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Photo Title</label>
                <input type="text" id="edit_title" name="title" required class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="edit_badge_text" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Badge Text</label>
                    <input type="text" id="edit_badge_text" name="badge_text" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
                <div>
                    <label for="edit_display_order" class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Display Sort Order</label>
                    <input type="number" id="edit_display_order" name="display_order" value="1" min="1" class="w-full bg-[#fbf9f5] border border-[#ebd9c8] rounded-xl px-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#d4a359]">
                </div>
            </div>

            <!-- Current Image Preview -->
            <div>
                <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Current Active Photo:</span>
                <img id="edit-current-preview" src="" alt="Current Photo" class="w-full h-36 object-cover rounded-xl border border-[#ebd9c8]">
            </div>

            <!-- Replace File Input -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">
                    <i class="fa-solid fa-camera mr-1 text-[#d4a359]"></i> Upload Replacement Photo (Optional)
                </label>
                <div class="border-2 border-dashed border-[#d4a359] rounded-2xl p-3 text-center bg-[#fdfaf6] hover:bg-[#faf4ec] transition relative cursor-pointer">
                    <input type="file" name="replace_file" accept="image/*" data-preview-target="#edit-new-preview" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                    <p class="text-xs font-bold text-[#2b0e14]">Tap here to select new photo from camera/gallery</p>
                </div>

                <div class="text-center pt-2">
                    <img id="edit-new-preview" src="" alt="New Replacement Preview" class="w-full h-36 object-cover rounded-xl border border-[#d4a359]/60 shadow-md hidden mx-auto">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-t border-[#ebd9c8] flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 sm:space-x-3">
                <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="px-5 py-2.5 rounded-full text-xs font-bold text-slate-600 hover:bg-slate-100 text-center">Cancel</button>
                <button type="submit" class="btn-gold px-7 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider shadow-lg flex items-center justify-center space-x-2">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Update Photo</span>
                </button>
            </div>
        </form>

    </div>
</div>

<script>
    function openEditModal(imgObj) {
        document.getElementById('edit_image_id').value = imgObj.id;
        document.getElementById('edit_title').value = imgObj.title || '';
        document.getElementById('edit_badge_text').value = imgObj.badge_text || '';
        document.getElementById('edit_display_order').value = imgObj.display_order || 1;

        let previewSrc = imgObj.image_path;
        if (!previewSrc.startsWith('http') && !previewSrc.startsWith('/')) {
            previewSrc = '../' + previewSrc;
        }
        document.getElementById('edit-current-preview').src = previewSrc;
        document.getElementById('edit-new-preview').classList.add('hidden');
        document.getElementById('edit-modal').classList.remove('hidden');
    }
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
