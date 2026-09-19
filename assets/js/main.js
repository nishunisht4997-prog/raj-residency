// assets/js/main.js - Raj Residency Frontend Scripts

document.addEventListener('DOMContentLoaded', function() {
    // 1. Setup Date Picker min dates
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');

    if (checkInInput && checkOutInput) {
        const today = new Date().toISOString().split('T')[0];
        if (!checkInInput.min) checkInInput.min = today;
        
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        if (!checkOutInput.min) checkOutInput.min = tomorrowStr;

        if (!checkInInput.value) {
            checkInInput.value = today;
        }
        if (!checkOutInput.value) {
            checkOutInput.value = tomorrowStr;
        }

        checkInInput.addEventListener('change', function() {
            const selectedCheckIn = new Date(this.value);
            const nextDay = new Date(selectedCheckIn);
            nextDay.setDate(nextDay.getDate() + 1);
            
            const nextDayStr = nextDay.toISOString().split('T')[0];
            checkOutInput.min = nextDayStr;
            
            if (checkOutInput.value <= this.value) {
                checkOutInput.value = nextDayStr;
            }
            if (typeof updateBookingSummary === 'function') {
                updateBookingSummary();
            }
        });

        checkOutInput.addEventListener('change', function() {
            if (typeof updateBookingSummary === 'function') {
                updateBookingSummary();
            }
        });
    }

    // 2. Room Category Filter (Home & Rooms page)
    const filterButtons = document.querySelectorAll('.room-filter-btn');
    const roomCards = document.querySelectorAll('.room-card-item');

    if (filterButtons.length > 0 && roomCards.length > 0) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active button classes
                filterButtons.forEach(b => {
                    b.classList.remove('bg-[#2b0e14]', 'text-[#f3cf8a]', 'shadow-md', 'ring-1', 'ring-[#d4a359]/40');
                    b.classList.add('bg-white', 'text-slate-700', 'hover:bg-[#f5efe6]', 'border', 'border-slate-200');
                });
                this.classList.remove('bg-white', 'text-slate-700', 'hover:bg-[#f5efe6]', 'border', 'border-slate-200');
                this.classList.add('bg-[#2b0e14]', 'text-[#f3cf8a]', 'shadow-md', 'ring-1', 'ring-[#d4a359]/40');

                const filter = this.getAttribute('data-filter');

                roomCards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    if (filter === 'all' || category === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // 3. Mobile Navigation Menu Toggle with outside click listener
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    }

    // 4. Hero Background 5-Image Crossfade Auto-Slider
    const heroSlides = document.querySelectorAll('.hero-slide');
    if (heroSlides.length > 0) {
        let currentSlideIndex = 0;
        setInterval(() => {
            heroSlides[currentSlideIndex].classList.remove('active');
            currentSlideIndex = (currentSlideIndex + 1) % heroSlides.length;
            heroSlides[currentSlideIndex].classList.add('active');
        }, 4500);
    }

    // 5. About Us Luxury Room 4-Image Crossfade Auto-Slider
    const roomSlides = document.querySelectorAll('.about-room-slide');
    const roomDots = document.querySelectorAll('.about-room-dot');
    if (roomSlides.length > 0) {
        let currentRoomIndex = 0;
        setInterval(() => {
            roomSlides[currentRoomIndex].classList.remove('active');
            if (roomDots.length > 0 && roomDots[currentRoomIndex]) {
                roomDots[currentRoomIndex].classList.remove('w-2', 'h-2', 'bg-[#f3cf8a]');
                roomDots[currentRoomIndex].classList.add('w-1.5', 'h-1.5', 'bg-white/50');
            }
            
            currentRoomIndex = (currentRoomIndex + 1) % roomSlides.length;
            
            roomSlides[currentRoomIndex].classList.add('active');
            if (roomDots.length > 0 && roomDots[currentRoomIndex]) {
                roomDots[currentRoomIndex].classList.remove('w-1.5', 'h-1.5', 'bg-white/50');
                roomDots[currentRoomIndex].classList.add('w-2', 'h-2', 'bg-[#f3cf8a]');
            }
        }, 3800);
    }

    // 6. About Us Luxury Dining 4-Image Crossfade Auto-Slider
    const diningSlides = document.querySelectorAll('.about-dining-slide');
    const diningDots = document.querySelectorAll('.about-dining-dot');
    if (diningSlides.length > 0) {
        let currentDiningIndex = 0;
        setInterval(() => {
            diningSlides[currentDiningIndex].classList.remove('active');
            if (diningDots.length > 0 && diningDots[currentDiningIndex]) {
                diningDots[currentDiningIndex].classList.remove('w-2', 'h-2', 'bg-[#f3cf8a]');
                diningDots[currentDiningIndex].classList.add('w-1.5', 'h-1.5', 'bg-white/50');
            }
            
            currentDiningIndex = (currentDiningIndex + 1) % diningSlides.length;
            
            diningSlides[currentDiningIndex].classList.add('active');
            if (diningDots.length > 0 && diningDots[currentDiningIndex]) {
                diningDots[currentDiningIndex].classList.remove('w-1.5', 'h-1.5', 'bg-white/50');
                diningDots[currentDiningIndex].classList.add('w-2', 'h-2', 'bg-[#f3cf8a]');
            }
        }, 4400);
    }
});

// Helper for interactive image switches on room-details.php
function switchDetailImage(src) {
    const mainImg = document.getElementById('main-room-img');
    if (mainImg) {
        mainImg.src = src;
    }
}
