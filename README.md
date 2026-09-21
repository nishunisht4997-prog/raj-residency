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

3. **100% Pure MySQL Database Architecture**:
   - Strictly powered by MySQL with prepared PDO queries and SQL transactions.
   - Auto-table initialization & seeding if database is fresh.
   - Built-in `setup_db.php` for 1-click diagnostics and table count verification.
   - Full MySQL dump available in `database.sql` for easy 1-click phpMyAdmin import.

4. **Complete Admin Suite (`/admin`)**:
   - Real-time Analytics Dashboard (`admin/index.php`)
   - Reservation Manager with Status Updates & Check-In tracking (`admin/bookings.php`)
   - Room Catalog & Pricing Manager (`admin/rooms.php`)
   - Hero Slider & Banner Media Manager (`admin/media.php`)
   - Contact Messages & Inquiries Inbox (`admin/messages.php`)
   - Guest Reviews Moderation (`admin/reviews.php`)
   - Hotel Profile, Contact & Timings Settings (`admin/settings.php`)

---

## 🔐 Admin Panel Credentials
- **URL**: `http://your-domain.com/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

---

## 🌐 How to Deploy Live (cPanel / Hostinger / GoDaddy / VPS)

1. **Upload Files**:
   - Upload all files from this folder into your hosting `public_html` directory via cPanel File Manager or FTP.

2. **Database Setup**:
   - In cPanel, go to **MySQL Databases** and create a new database (e.g. `raj_residency`) and user.
   - Go to **phpMyAdmin**, select the newly created database, click **Import**, and choose `database.sql` from the project.
   - Open `config/database.php` and enter your database credentials:
     - `self::$db_name = 'your_database_name';`
     - `self::$username = 'your_database_user';`
     - `self::$password = 'your_database_password';`
     *(Or set environment variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` in your host's environment settings).*

3. **Verify Deployment**:
   - Visit `http://your-domain.com/setup_db.php` to verify all 6 rooms, banners, settings, and tables are running smoothly.
   - Log in at `http://your-domain.com/admin/login.php` using `admin` / `admin123` and update your password from the top-right profile modal!
   - Website is 100% live and ready for bookings!
