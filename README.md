# Raj Residency - Royal Heritage & Luxury Hotel Website

A complete, luxury, and responsive hotel web application built in **PHP**, **Tailwind CSS**, and **Font Awesome 6**, crafted specifically for **RAJ RESIDENCY**, Koraput, Odisha.

---

## 📍 Hotel Details
- **Hotel Name**: RAJ RESIDENCY
- **Tagline**: ROYAL HERITAGE & LUXURY
- **Address**: POST OFFICE ROAD, DIST - KORAPUT, ODISHA, PIN - 764020
- **Phone**: 083289 10274 / +91 83289 10274
- **Email**: info@rajresidency.com / booking@rajresidency.com
- **WhatsApp Desk**: 083289 10274 (+91 83289 10274)

---

## 🌟 Key Features

1. **Luxury Imperial Aesthetic**:
   - Imperial Midnight Teal (`#0e2a33`) & Royal Champagne Gold (`#d4a359`) theme.
   - Flowing multi-layer SVG wave dividers.
   - Auto crossfading background hero slider and about photo gallery.
   - Dynamic floating rating badge and price starting indicators.

2. **Public Pages**:
   - `index.php`: Hero banner, quick availability search widget, about story, convenience service cards, featured AC & Non-AC rooms catalog, testimonials, and WhatsApp CTA.
   - `about.php`: Heritage story in Koraput, Odisha, Koraput tourism gateway highlights (Deomali Peak, Kolab Dam, Sabara Srikhetra), and statistics.
   - `services.php`: 24/7 Room Service, In-house Multi-Cuisine Restaurant, Optical Fiber Wi-Fi, Valet Parking, Travel Desk & Sightseeing Cabs, 24/7 Power Backup.
   - `rooms.php`: Category filterable catalog (AC Deluxe, Royal Super Deluxe, Budget Non-AC, Family Non-AC, Raj Presidential Suite).
   - `room-details.php`: Dynamic room pages with photo gallery, room specs, detailed amenities checklist, hotel policies, and live pricing sidebar.
   - `booking.php`: Real-time booking calculation, guest information form, ID proof selection, and multiple payment modes (Pay at Hotel, UPI QR, Online Cards).
   - `booking-confirmation.php`: Printable reservation voucher with unique booking ID (e.g. `RR-2026-XXXX`), summary breakdown, and WhatsApp share button.
   - `reviews.php`: Interactive 5-star rating submission form, guest feedback showcase, and overall satisfaction score breakdown.
   - `contact.php`: Interactive inquiry form, Koraput address, direct dial buttons, WhatsApp chat link, and embedded Google Maps.

3. **Zero-Config Dual Database Architecture**:
   - **Out-of-the-Box Mode**: Automatic JSON storage fallback in `data/store.json` so the website works immediately with standard PHP without requiring MySQL setup.
   - **Production MySQL Mode**: Auto-connects to MySQL if available, or import `database.sql` via phpMyAdmin.

---

## 🚀 How to Run Locally

### Option 1: PHP Built-in Server (Zero Setup)
1. Open PowerShell / Command Prompt in this folder:
   ```bash
   cd c:\Users\HP\OneDrive\Desktop\raj-residency
   ```
2. Start the local server:
   ```bash
   php -S localhost:8000
   ```
3. Open your browser:
   - **Main Website**: [http://localhost:8000](http://localhost:8000)

### Option 2: XAMPP / WAMP / cPanel
1. Copy the `raj-residency` folder into `C:\xampp\htdocs\`
2. Start Apache in XAMPP Control Panel.
3. Open `http://localhost/raj-residency` in your browser.
