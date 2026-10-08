<?php
include '../config/db.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

if(!isset($_SESSION['admin_status_csrf'])
    || !is_string($_SESSION['admin_status_csrf'])
    || !preg_match('/\A[a-f0-9]{64}\z/', $_SESSION['admin_status_csrf'])) {
    $_SESSION['admin_status_csrf'] = bin2hex(random_bytes(32));
}

$admin_toast = $_SESSION['admin_toast'] ?? null;
unset($_SESSION['admin_toast']);

if(isset($_POST['update_status'])) {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $status = $_POST['status'] ?? '';
    $allowed_statuses = ['Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled'];
    $csrf_token = $_POST['csrf_token'] ?? '';

    if(is_string($csrf_token) && hash_equals($_SESSION['admin_status_csrf'], $csrf_token)
        && $order_id && is_string($status) && in_array($status, $allowed_statuses, true)) {
        $order_stmt = $conn->prepare('SELECT customer_name, customer_email, total_amount, status FROM orders WHERE id = ? LIMIT 1');
        $order_stmt->bind_param('i', $order_id);
        $order_stmt->execute();
        $order_stmt->bind_result($customer_name, $customer_email, $order_total, $old_status);
        $order_found = $order_stmt->fetch();
        $order_stmt->close();

        if(!$order_found) {
            $_SESSION['admin_toast'] = ['type' => 'error', 'message' => 'Order not found.'];
        } elseif($old_status === $status) {
            $_SESSION['admin_toast'] = ['type' => 'info', 'message' => 'Order status is already ' . $status . '; no email was sent.'];
        } else {
            $stmt = $conn->prepare('UPDATE orders SET status = ? WHERE id = ?');
            $stmt->bind_param('si', $status, $order_id);
            $stmt->execute();
            $stmt->close();

            if(!$customer_email || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['admin_toast'] = [
                    'type' => 'warning',
                    'message' => 'Order status updated, but this order has no valid customer email address.',
                ];
            } else {
                require_once '../config/email.php';
                $safe_name = htmlspecialchars($customer_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $safe_status = htmlspecialchars($status, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $order_number = '#' . str_pad((string)$order_id, 6, '0', STR_PAD_LEFT);
                $subject = SITE_NAME . ' — Order ' . $order_number . ' ' . $status;
                $html_body = '<h2>Order status update</h2><p>Hello ' . $safe_name
                    . ',</p><p>Your order <strong>' . $order_number . '</strong> is now <strong>'
                    . $safe_status . '</strong>.</p><p>Order total: Rs. ' . number_format((float)$order_total, 2)
                    . '</p><p>Thank you for ordering from ' . htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') . '.</p>';
                $text_body = "Hello {$customer_name},\n\nYour order {$order_number} is now {$status}.\n"
                    . 'Order total: Rs. ' . number_format((float)$order_total, 2) . "\n\nThank you for ordering from " . SITE_NAME . '.';

                try {
                    sendStoreEmail($customer_email, $subject, $html_body, $text_body);
                    $_SESSION['admin_toast'] = [
                        'type' => 'success',
                        'message' => 'Order status updated and customer email sent.',
                    ];
                } catch(Throwable $email_exception) {
                    error_log('Order status email failed for order ' . $order_id . ': ' . $email_exception->getMessage());
                    $_SESSION['admin_toast'] = [
                        'type' => 'warning',
                        'message' => 'Order status updated, but the customer email could not be sent. Check SMTP settings and the PHP error log.',
                    ];
                }
            }
        }
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['admin_toast'] = [
            'type' => 'error',
            'message' => 'The order status request was invalid or expired. Please try again.',
        ];
        header('Location: dashboard.php');
        exit();
    }
}

// Stats
$total_orders = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'];
$pending_orders = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='Pending'")->fetch_assoc()['c'];
$delivered = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='Delivered'")->fetch_assoc()['c'];
$revenue = $conn->query("SELECT COALESCE(SUM(total_amount),0) as s FROM orders WHERE status != 'Cancelled'")->fetch_assoc()['s'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | <?php echo SITE_NAME; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                colors: { cream: '#F5F0E6', 'cream-dark': '#E8DFD0', oat: '#D4A574', 'oat-dark': '#B8895A', brown: '#8B6B4A', choco: '#3E2C1F' },
                fontFamily: { display: ['"Playfair Display"', 'serif'], body: ['"Poppins"', 'sans-serif'] }
            }}
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-body bg-cream min-h-screen">

<!-- Top Bar -->
<header class="bg-choco text-cream sticky top-0 z-50 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-oat rounded-full flex items-center justify-center">
                <i class="fas fa-seedling text-white"></i>
            </div>
            <div>
                <h1 class="font-display font-bold text-lg sm:text-xl leading-none">Admin Panel</h1>
                <p class="text-[10px] text-oat uppercase tracking-wider"><?php echo SITE_NAME; ?></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs text-cream/60 hidden sm:block">Hi, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
            <a href="logout.php" class="bg-oat hover:bg-oat-dark text-white px-4 py-2 rounded-full text-sm font-semibold transition-all">
                <i class="fas fa-sign-out-alt"></i> <span class="hidden sm:inline">Logout</span>
            </a>
        </div>
    </div>
</header>

<main class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
    
    <h2 class="font-display text-2xl sm:text-3xl font-bold text-choco mb-6">Dashboard Overview</h2>
    <?php if(is_array($admin_toast) && isset($admin_toast['type'], $admin_toast['message'])): ?>
        <?php
        $toast_classes = [
            'success' => 'bg-green-50 border-green-500 text-green-800',
            'warning' => 'bg-yellow-50 border-yellow-500 text-yellow-800',
            'error' => 'bg-red-50 border-red-500 text-red-800',
            'info' => 'bg-blue-50 border-blue-500 text-blue-800',
        ];
        $toast_class = $toast_classes[$admin_toast['type']] ?? $toast_classes['info'];
        ?>
        <div id="adminToast" class="fixed top-5 right-5 z-[100] max-w-md border-l-4 rounded-xl shadow-xl p-4 <?php echo $toast_class; ?>" role="status" aria-live="polite">
            <div class="flex items-start justify-between gap-4">
                <p class="text-sm font-semibold"><?php echo htmlspecialchars($admin_toast['message'], ENT_QUOTES, 'UTF-8'); ?></p>
                <button type="button" onclick="document.getElementById('adminToast').remove()" class="font-bold" aria-label="Dismiss notification">&times;</button>
            </div>
        </div>
        <script>
        window.setTimeout(() => document.getElementById('adminToast')?.remove(), 7000);
        </script>
    <?php endif; ?>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-oat/20 rounded-xl flex items-center justify-center text-oat text-xl">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
            <p class="text-xs text-brown uppercase tracking-wider mb-1">Total Orders</p>
            <h3 class="font-display text-3xl font-black text-choco"><?php echo $total_orders; ?></h3>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600 text-xl">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-xs text-brown uppercase tracking-wider mb-1">Pending</p>
            <h3 class="font-display text-3xl font-black text-choco"><?php echo $pending_orders; ?></h3>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="text-xs text-brown uppercase tracking-wider mb-1">Delivered</p>
            <h3 class="font-display text-3xl font-black text-choco"><?php echo $delivered; ?></h3>
        </div>
        
        <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
            <div class="flex items-center justify-between mb-3">
                <div class="w-12 h-12 bg-oat/20 rounded-xl flex items-center justify-center text-oat text-xl">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <p class="text-xs text-brown uppercase tracking-wider mb-1">Revenue</p>
            <h3 class="font-display text-2xl sm:text-3xl font-black text-choco">Rs. <?php echo number_format($revenue); ?></h3>
        </div>
    </div>
    
    <!-- Orders Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-cream">
            <h3 class="font-display text-xl font-bold text-choco">Recent Orders</h3>
        </div>
        
        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-cream/50">
                    <tr>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">#</th>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">Customer</th>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">Phone</th>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">Total</th>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">Payment</th>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">Status</th>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">Date</th>
                        <th class="text-left p-4 font-bold text-choco text-xs uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 50");
                    if($res && $res->num_rows > 0):
                        while($o = $res->fetch_assoc()):
                            $statusColors = [
                                'Pending' => 'bg-yellow-100 text-yellow-700',
                                'Confirmed' => 'bg-blue-100 text-blue-700',
                                'Shipped' => 'bg-purple-100 text-purple-700',
                                'Delivered' => 'bg-green-100 text-green-700',
                                'Cancelled' => 'bg-red-100 text-red-700',
                            ];
                            $sc = $statusColors[$o['status']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <tr class="border-t border-cream hover:bg-cream/30">
                        <td class="p-4 font-bold text-choco text-sm">#<?php echo $o['id']; ?></td>
                        <td class="p-4 text-sm text-choco"><?php echo htmlspecialchars($o['customer_name']); ?></td>
                        <td class="p-4 text-sm text-brown"><?php echo htmlspecialchars($o['phone']); ?></td>
                        <td class="p-4 font-bold text-oat text-sm">Rs. <?php echo number_format($o['total_amount']); ?></td>
                        <td class="p-4 text-xs text-brown">
                            <?php echo htmlspecialchars($o['payment_method'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php if($o['transaction_id']): ?>
                                <br><span class="font-mono text-[10px]"><?php echo htmlspecialchars($o['transaction_id']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <span class="<?php echo $sc; ?> px-3 py-1 rounded-full text-[10px] font-bold uppercase"><?php echo htmlspecialchars($o['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <td class="p-4 text-xs text-brown"><?php echo date('d M, Y', strtotime($o['created_at'])); ?></td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <form method="POST" class="flex items-center gap-1">
                                    <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_status_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <select name="status" class="px-2 py-1.5 border-2 border-cream-dark rounded-lg text-xs outline-none focus:border-oat">
                                        <?php foreach(['Pending','Confirmed','Shipped','Delivered','Cancelled'] as $s): ?>
                                            <option <?php if($o['status']==$s) echo 'selected'; ?>><?php echo $s; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="bg-oat hover:bg-oat-dark text-white px-3 py-1.5 rounded-lg text-xs transition-all">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                                <a href="order_detail.php?id=<?php echo $o['id']; ?>" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs transition-all">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="8" class="p-8 text-center text-brown">No orders yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="md:hidden p-4 space-y-4">
            <?php
            $res2 = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 20");
            if($res2 && $res2->num_rows > 0):
                while($o = $res2->fetch_assoc()):
                    $statusColors = [
                        'Pending' => 'bg-yellow-100 text-yellow-700',
                        'Confirmed' => 'bg-blue-100 text-blue-700',
                        'Shipped' => 'bg-purple-100 text-purple-700',
                        'Delivered' => 'bg-green-100 text-green-700',
                        'Cancelled' => 'bg-red-100 text-red-700',
                    ];
                    $sc = $statusColors[$o['status']] ?? 'bg-gray-100';
            ?>
            <div class="bg-cream/40 rounded-xl p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-bold text-choco">#<?php echo $o['id']; ?></p>
                        <p class="text-xs text-brown"><?php echo date('d M Y', strtotime($o['created_at'])); ?></p>
                    </div>
                    <span class="<?php echo $sc; ?> px-2.5 py-1 rounded-full text-[10px] font-bold uppercase"><?php echo htmlspecialchars($o['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <p class="text-sm text-choco font-semibold mb-1"><?php echo htmlspecialchars($o['customer_name']); ?></p>
                <p class="text-xs text-brown mb-2"><?php echo htmlspecialchars($o['phone']); ?></p>
                <p class="font-bold text-oat mb-3">Rs. <?php echo number_format($o['total_amount']); ?></p>
                <div class="flex gap-2">
                    <form method="POST" class="flex-1 flex gap-1">
                        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['admin_status_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                        <select name="status" class="flex-1 px-2 py-1.5 border-2 border-cream-dark rounded-lg text-xs outline-none">
                            <?php foreach(['Pending','Confirmed','Shipped','Delivered','Cancelled'] as $s): ?>
                                <option <?php if($o['status']==$s) echo 'selected'; ?>><?php echo $s; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="update_status" class="bg-oat text-white px-3 py-1.5 rounded-lg text-xs">
                            <i class="fas fa-save"></i>
                        </button>
                    </form>
                    <a href="order_detail.php?id=<?php echo $o['id']; ?>" class="bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
            <?php endwhile; endif; ?>
        </div>
    </div>
</main>

</body>
</html>