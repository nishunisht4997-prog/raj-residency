            </main>

            <!-- ADMIN FOOTER -->
            <footer class="bg-white border-t border-[#ebd9c8] py-3.5 px-3 sm:px-6 lg:px-8 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2 mt-auto text-center sm:text-left">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-2 gap-y-1">
                    <span class="font-royal font-bold text-[#2b0e14]"><?php echo htmlspecialchars($settings['hotel_name']); ?></span>
                    <span>&bull;</span>
                    <span>Admin Suite &copy; <?php echo date('Y'); ?></span>
                    <span>&bull;</span>
                    <span class="text-slate-500 font-normal">Crafted by <strong class="text-[#b88738] font-bold">Geinca</strong></span>
                </div>
                <div class="flex items-center justify-center sm:justify-end space-x-3 sm:space-x-4 text-[11px]">
                    <span class="text-slate-400 hidden sm:inline">Post Office Road, Koraput</span>
                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $settings['hotel_phone']); ?>" class="inline-flex items-center space-x-1 font-semibold text-slate-700 hover:text-[#b88738]">
                        <i class="fa-solid fa-phone text-[#d4a359]"></i>
                        <span><?php echo htmlspecialchars($settings['hotel_phone']); ?></span>
                    </a>
                </div>
            </footer>

        </div>
    </div>

    <!-- GLOBAL JAVASCRIPT FOR MOBILE DRAWER & INSTANT LIVE IMAGE PREVIEWS -->
    <script>
        // 1. Mobile Sidebar Toggle & Touch Handlers
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const openBtn = document.getElementById('mobile-sidebar-btn');
        const closeBtn = document.getElementById('close-sidebar-btn');

        function openSidebar() {
            if (sidebar && overlay) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            }
        }

        function closeSidebar() {
            if (sidebar && overlay) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        }

        if (openBtn) openBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        // Close on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // 2. Direct Mobile / PC Image File Upload Live Preview Handler
        document.querySelectorAll('input[type="file"]').forEach(function(input) {
            input.addEventListener('change', function(e) {
                const targetPreviewId = input.getAttribute('data-preview-target');
                if (targetPreviewId && input.files && input.files[0]) {
                    const previewEl = document.querySelector(targetPreviewId);
                    if (previewEl) {
                        const reader = new FileReader();
                        reader.onload = function(re) {
                            if (previewEl.tagName === 'IMG') {
                                previewEl.src = re.target.result;
                                previewEl.classList.remove('hidden');
                            } else {
                                previewEl.style.backgroundImage = `url('${re.target.result}')`;
                                previewEl.classList.remove('hidden');
                            }
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            });
        });

        // 3. Multi-file upload thumbnail previewer
        document.querySelectorAll('input[type="file"][multiple]').forEach(function(input) {
            input.addEventListener('change', function(e) {
                const targetContainerId = input.getAttribute('data-multi-preview-target');
                if (targetContainerId && input.files) {
                    const container = document.querySelector(targetContainerId);
                    if (container) {
                        container.innerHTML = '';
                        Array.from(input.files).forEach(function(file) {
                            if (file.type.startsWith('image/')) {
                                const reader = new FileReader();
                                reader.onload = function(re) {
                                    const thumbDiv = document.createElement('div');
                                    thumbDiv.className = 'w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden border border-[#d4a359]/60 shadow-sm relative shrink-0';
                                    thumbDiv.innerHTML = `<img src="${re.target.result}" class="w-full h-full object-cover" alt="New Gallery Image">`;
                                    container.appendChild(thumbDiv);
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    }
                }
            });
        });
    </script>
</body>
</html>
