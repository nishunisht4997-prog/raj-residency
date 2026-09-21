<?php
// admin/messages.php - Contact Form Inquiries & Messages
require_once __DIR__ . '/../includes/functions.php';
check_admin_auth();

// Handle Mark Read / Unread
if (isset($_GET['mark_read'])) {
    $mId = (int)$_GET['mark_read'];
    $val = (int)($_GET['val'] ?? 1);
    mark_message_read($mId, $val);
    set_flash_message('success', 'Message status updated.');
    header('Location: messages.php');
    exit;
}

// Handle Delete Message
if (isset($_GET['delete'])) {
    $mId = (int)$_GET['delete'];
    delete_message($mId);
    set_flash_message('success', 'Message deleted successfully.');
    header('Location: messages.php');
    exit;
}

$allMessages = get_all_messages();
$pageTitle = "Contact Inquiries & Messages";
require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="space-y-6">
    
    <!-- TOP HEADER -->
    <div class="royal-card p-6 bg-gradient-to-r from-white to-[#fbf9f5] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="font-serif text-2xl font-bold text-[#2b0e14]">Contact Inquiries & Guest Messages</h2>
            <p class="text-xs text-slate-500 font-light mt-0.5">View inquiries submitted via the Contact Us form and respond quickly.</p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-xs font-bold text-slate-600">Total Inquiries:</span>
            <span class="px-3 py-1 rounded-full bg-[#2b0e14] text-[#f3cf8a] text-xs font-black"><?php echo count($allMessages); ?></span>
        </div>
    </div>

    <!-- MESSAGES LIST -->
    <div class="space-y-4">
        <?php if (empty($allMessages)): ?>
            <div class="royal-card p-12 text-center text-slate-400 space-y-2">
                <i class="fa-solid fa-inbox text-3xl text-slate-300 block"></i>
                <p>No contact messages received yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($allMessages as $msg): ?>
                <div class="royal-card p-5 sm:p-6 transition <?php echo empty($msg['is_read']) ? 'border-[#d4a359] bg-[#fdfbf7] shadow-md' : 'border-[#ebd9c8] bg-white'; ?>">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-[#ebd9c8] pb-3">
                        <div class="flex items-start sm:items-center space-x-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-[#2b0e14] text-[#f3cf8a] flex items-center justify-center font-bold text-xs shadow-sm shrink-0 mt-0.5 sm:mt-0">
                                <?php echo strtoupper(substr($msg['name'] ?? 'G', 0, 1)); ?>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center space-x-2 flex-wrap gap-1">
                                    <strong class="text-sm font-bold text-[#2b0e14] truncate"><?php echo htmlspecialchars($msg['name']); ?></strong>
                                    <?php if (empty($msg['is_read'])): ?>
                                        <span class="text-[9px] bg-blue-600 text-white font-bold px-2 py-0.5 rounded-full shrink-0">New Unread</span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-[11px] text-slate-500 flex flex-wrap items-center gap-x-3 gap-y-1 mt-0.5">
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $msg['phone']); ?>" class="text-[#b88738] hover:underline flex items-center space-x-1 shrink-0">
                                        <i class="fa-solid fa-phone text-[9px]"></i>
                                        <span><?php echo htmlspecialchars($msg['phone']); ?></span>
                                    </a>
                                    <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="text-slate-600 hover:underline flex items-center space-x-1 break-all">
                                        <i class="fa-solid fa-envelope text-[9px] shrink-0"></i>
                                        <span><?php echo htmlspecialchars($msg['email']); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2 text-xs pt-1 sm:pt-0">
                            <span class="text-[10px] text-slate-400 w-full sm:w-auto"><?php echo format_datetime($msg['created_at']); ?></span>
                            
                            <div class="flex items-center space-x-1.5 ml-auto sm:ml-0">
                                <!-- WhatsApp Action -->
                                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $msg['phone']); ?>?text=Hello%20<?php echo urlencode($msg['name']); ?>,%20thank%20you%20for%20contacting%20Raj%20Residency." target="_blank" class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 text-xs font-semibold flex items-center space-x-1" title="Chat on WhatsApp">
                                    <i class="fa-brands fa-whatsapp text-sm text-emerald-600"></i>
                                    <span>WhatsApp</span>
                                </a>

                                <!-- Mark Read Toggle -->
                                <a href="messages.php?mark_read=<?php echo $msg['id']; ?>&val=<?php echo empty($msg['is_read']) ? '1' : '0'; ?>" class="px-2.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs" title="<?php echo empty($msg['is_read']) ? 'Mark as Read' : 'Mark as Unread'; ?>">
                                    <i class="fa-solid <?php echo empty($msg['is_read']) ? 'fa-envelope-open' : 'fa-envelope'; ?>"></i>
                                </a>

                                <!-- Delete -->
                                <a href="messages.php?delete=<?php echo $msg['id']; ?>" onclick="return confirm('Delete this inquiry?');" class="px-2.5 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs" title="Delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Subject & Body -->
                    <div class="pt-3 space-y-1.5">
                        <h4 class="font-serif text-sm font-bold text-[#2b0e14]">
                            Subject: <?php echo htmlspecialchars($msg['subject']); ?>
                        </h4>
                        <p class="text-xs text-slate-700 leading-relaxed font-light whitespace-pre-line bg-[#fbf9f5] p-3.5 rounded-xl border border-[#ebd9c8]">
                            <?php echo htmlspecialchars($msg['message']); ?>
                        </p>
                    </div>

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
