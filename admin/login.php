<?php
// admin/login.php - Admin Portal Login for Raj Residency
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$settings = get_settings();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if (admin_login($username, $password)) {
            set_flash_message('success', 'Welcome back! You are now logged into Raj Residency Admin Panel.');
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid admin username or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo htmlspecialchars($settings['hotel_name']); ?></title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        theme: {
                            maroon: '#2b0e14',
                            maroonDark: '#1c080d',
                            maroonLight: '#3d141d',
                            gold: '#d4a359',
                            goldLight: '#f3cf8a',
                            goldDark: '#b88738',
                        }
                    },
                    fontFamily: {
                        serif: ['"Playfair Display"', '"Cormorant Garamond"', 'Georgia', 'serif'],
                        royal: ['"Cinzel"', 'serif'],
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .btn-gold {
            background: linear-gradient(135deg, #f3cf8a 0%, #d4a359 50%, #b88738 100%);
            color: #2b0e14;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        .btn-gold:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 25px -5px rgba(212, 163, 89, 0.5);
            filter: brightness(1.05);
        }
    </style>
</head>
<body class="bg-[#1c080d] min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Decorative background elements -->
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_var(--tw-gradient-stops))] from-[#3d141d] via-[#1c080d] to-[#120508] opacity-90 pointer-events-none"></div>
    <div class="absolute w-[500px] h-[500px] rounded-full bg-[#d4a359]/5 blur-3xl top-10 left-10 pointer-events-none"></div>
    <div class="absolute w-[400px] h-[400px] rounded-full bg-[#d4a359]/5 blur-3xl bottom-10 right-10 pointer-events-none"></div>

    <div class="w-full max-w-md bg-[#2b0e14]/90 backdrop-blur-xl border border-[#d4a359]/40 rounded-3xl p-6 sm:p-10 shadow-2xl relative z-10 text-white space-y-5 sm:space-y-6">
        
        <!-- Royal Header Emblem -->
        <div class="text-center space-y-3">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-full border-2 border-[#d4a359] bg-[#1c080d] flex flex-col items-center justify-center text-[#f3cf8a] shadow-xl shadow-[#d4a359]/20">
                <i class="fa-solid fa-crown text-sm sm:text-base text-[#d4a359] mb-0.5"></i>
                <span class="font-royal text-xs sm:text-sm font-black leading-none tracking-tighter">RR</span>
            </div>
            
            <div>
                <h1 class="font-royal text-xl sm:text-2xl font-black tracking-wider uppercase text-white"><?php echo htmlspecialchars($settings['hotel_name']); ?></h1>
                <p class="text-[10px] sm:text-xs text-[#d4a359] tracking-widest uppercase font-bold mt-0.5">ADMINISTRATIVE PORTAL</p>
                <p class="text-[10px] sm:text-[11px] text-slate-400 font-light mt-1">Sign in to manage images, rooms, bookings & settings</p>
            </div>
        </div>

        <!-- Error Alert -->
        <?php if (!empty($error)): ?>
            <div class="p-3.5 rounded-2xl bg-rose-950/80 border border-rose-500/50 text-rose-200 text-xs font-semibold flex items-center space-x-2.5">
                <i class="fa-solid fa-circle-exclamation text-rose-400 text-base"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="login.php" method="POST" class="space-y-4">
            
            <div>
                <label for="username" class="block text-[11px] font-bold uppercase tracking-wider text-[#f3cf8a] mb-1.5">
                    <i class="fa-solid fa-user text-xs mr-1 text-[#d4a359]"></i> Admin Username
                </label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? 'admin'); ?>" required
                       class="w-full bg-[#1c080d] border border-[#d4a359]/40 rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#d4a359] focus:border-transparent transition">
            </div>

            <div>
                <label for="password" class="block text-[11px] font-bold uppercase tracking-wider text-[#f3cf8a] mb-1.5">
                    <i class="fa-solid fa-lock text-xs mr-1 text-[#d4a359]"></i> Password
                </label>
                <input type="password" id="password" name="password" required placeholder="Enter password"
                       class="w-full bg-[#1c080d] border border-[#d4a359]/40 rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#d4a359] focus:border-transparent transition">
            </div>

            <!-- Credentials Hint Pill -->
            <div class="p-3 rounded-xl bg-[#1c080d]/60 border border-[#d4a359]/20 text-[11px] text-slate-300 flex items-center justify-between">
                <span>Default Login:</span>
                <span class="font-mono text-[#f3cf8a] font-bold">admin / admin123</span>
            </div>

            <button type="submit" class="w-full btn-gold py-3.5 rounded-xl text-xs sm:text-sm font-bold tracking-wider uppercase shadow-xl flex items-center justify-center space-x-2 mt-2">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                <span>SIGN IN TO DASHBOARD</span>
            </button>
        </form>

        <div class="text-center pt-2 border-t border-[#d4a359]/20 space-y-2">
            <a href="../index.php" class="text-xs text-[#f3cf8a] hover:underline flex items-center justify-center space-x-1">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Back to Public Website</span>
            </a>
            <div class="text-[10px] text-slate-400">
                Crafted by <span class="text-[#f3cf8a] font-semibold">Geinca</span>
            </div>
        </div>

    </div>

</body>
</html>
