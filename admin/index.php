<?php
include '../config/db.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// ============ REDIRECT IF LOGGED IN ============
if(isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";
$username = "";
if(!isset($_SESSION['admin_login_csrf'])
    || !is_string($_SESSION['admin_login_csrf'])
    || !preg_match('/\A[a-f0-9]{64}\z/', $_SESSION['admin_login_csrf'])) {
    $_SESSION['admin_login_csrf'] = bin2hex(random_bytes(32));
}

// ============ HANDLE LOGIN ============
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if(!is_string($username) || !is_string($password) || !is_string($csrf_token)) {
        $username = '';
        $error = "Invalid username or password.";
    } elseif(!hash_equals($_SESSION['admin_login_csrf'], $csrf_token)) {
        $error = "Your login session expired. Please try again.";
    } else {
        $username = trim($username);
        if($username === '' || $password === '') {
            $error = "Please enter username and password.";
        } elseif(strlen($username) > 50 || strlen($password) > 4096) {
            $error = "Invalid username or password.";
        } else {
            $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ? LIMIT 1");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->bind_result($admin_id, $admin_username, $admin_password);

            if($stmt->fetch() && password_verify($password, $admin_password)) {
                session_regenerate_id(true);
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['admin_username'] = $admin_username;
                unset($_SESSION['admin_login_csrf']);
                $stmt->close();
                header("Location: dashboard.php");
                exit();
            }
            $stmt->close();

            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Pure Oats with Nuts</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: {
                    cream: '#F5F0E6', 'cream-dark': '#E8DFD0',
                    oat: '#D4A574', 'oat-dark': '#B8895A',
                    brown: '#8B6B4A', choco: '#3E2C1F',
                },
                fontFamily: {
                    display: ['"Playfair Display"', 'serif'],
                    body: ['"Poppins"', 'sans-serif'],
                }
            }}
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-cream min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <!-- Login Card -->
    <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-oat rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-seedling text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-choco" style="font-family: 'Playfair Display', serif;">Admin Login</h1>
            <p class="text-brown text-sm mt-1">Pure Oats with Nuts</p>
        </div>
        
        <!-- Error -->
        <?php if($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-5" role="alert">
                <p class="text-red-700 text-sm font-semibold">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form method="POST" action="index.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_login_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-4">
                <label for="username" class="block text-sm font-bold text-choco mb-2">Username</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-oat"></i>
                    <input type="text" id="username" name="username" required maxlength="50" autocomplete="username"
                           value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>"
                           class="w-full pl-12 pr-4 py-3.5 border-2 border-cream-dark rounded-xl outline-none focus:border-oat transition-colors text-choco font-medium text-sm">
                </div>
            </div>
            
            <div class="mb-5">
                <label for="passwordField" class="block text-sm font-bold text-choco mb-2">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-oat"></i>
                    <input type="password" name="password" required maxlength="4096" autocomplete="current-password"
                           id="passwordField"
                           class="w-full pl-12 pr-12 py-3.5 border-2 border-cream-dark rounded-xl outline-none focus:border-oat transition-colors text-choco font-medium text-sm">
                    <button type="button" onclick="togglePassword(this)" aria-label="Show password" aria-controls="passwordField" aria-pressed="false"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-oat hover:text-oat-dark">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit"
                    class="w-full bg-oat hover:bg-oat-dark text-white py-4 rounded-full font-bold transition-all hover:shadow-xl inline-flex items-center justify-center gap-2 text-sm">
                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
            </button>
        </form>
        
        <!-- Back to Site -->
        <div class="text-center mt-5">
            <a href="../index.php" class="text-xs text-brown hover:text-oat font-medium">
                <i class="fas fa-arrow-left mr-1"></i> Back to Website
            </a>
        </div>
    </div>
</div>

<script>
function togglePassword(button) {
    const field = document.getElementById('passwordField');
    const icon = document.getElementById('eyeIcon');
    if(field.type === 'password') {
        field.type = 'text';
        button.setAttribute('aria-label', 'Hide password');
        button.setAttribute('aria-pressed', 'true');
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        button.setAttribute('aria-label', 'Show password');
        button.setAttribute('aria-pressed', 'false');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>