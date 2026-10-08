<?php
include '../config/db.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if(!$id || $id < 1) {
    header("Location: dashboard.php");
    exit();
}

$order_stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
$order_stmt->bind_param("i", $id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order = $order_result->fetch_assoc();
$order_stmt->close();

if(!$order) {
    header("Location: dashboard.php");
    exit();
}

$items_stmt = $conn->prepare("SELECT oi.*, p.name FROM order_items oi 
                              LEFT JOIN products p ON oi.product_id = p.id 
                              WHERE oi.order_id = ?");
$items_stmt->bind_param("i", $id);
$items_stmt->execute();
$items = $items_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?php echo $id; ?> | Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: {
            colors: { cream: '#F5F0E6', 'cream-dark': '#E8DFD0', oat: '#D4A574', 'oat-dark': '#B8895A', brown: '#8B6B4A', choco: '#3E2C1F' },
            fontFamily: { display: ['"Playfair Display"', 'serif'], body: ['"Poppins"', 'sans-serif'] }
        }}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-body bg-cream min-h-screen">

<header class="bg-choco text-cream shadow-lg">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">
        <h1 class="font-display font-bold text-lg sm:text-xl">Order #<?php echo str_pad($id, 6, '0', STR_PAD_LEFT); ?></h1>
        <a href="dashboard.php" class="bg-oat hover:bg-oat-dark text-white px-4 py-2 rounded-full text-sm font-semibold">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</header>

<main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
    
    <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8 mb-6">
        <h2 class="font-display text-xl font-bold text-choco mb-5 pb-3 border-b-2 border-cream">Customer Details</h2>
        <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <div><strong class="text-choco">Name:</strong> <span class="text-brown"><?php echo htmlspecialchars($order['customer_name']); ?></span></div>
            <div><strong class="text-choco">Email:</strong> <span class="text-brown"><?php echo htmlspecialchars($order['customer_email'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div><strong class="text-choco">Phone:</strong> <span class="text-brown"><?php echo htmlspecialchars($order['phone']); ?></span></div>
            <div class="sm:col-span-2"><strong class="text-choco">Address:</strong> <span class="text-brown"><?php echo htmlspecialchars($order['address']); ?></span></div>
            <div><strong class="text-choco">Payment:</strong> <span class="text-brown"><?php echo htmlspecialchars($order['payment_method'], ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div><strong class="text-choco">TID:</strong> <span class="text-brown font-mono"><?php echo htmlspecialchars($order['transaction_id'] ?: 'N/A', ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div><strong class="text-choco">Status:</strong> <span class="text-brown"><?php echo htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></span></div>
            <div><strong class="text-choco">Date:</strong> <span class="text-brown"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></span></div>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm p-6 sm:p-8">
        <h2 class="font-display text-xl font-bold text-choco mb-5 pb-3 border-b-2 border-cream">Order Items</h2>
        <div class="space-y-3">
            <?php while($i = $items->fetch_assoc()): ?>
                <div class="flex justify-between items-center py-3 border-b border-cream">
                    <div>
                        <p class="font-semibold text-choco"><?php echo htmlspecialchars($i['name'] ?? 'Product'); ?></p>
                        <p class="text-xs text-brown">Qty: <?php echo $i['quantity']; ?> × Rs. <?php echo number_format($i['price']); ?></p>
                    </div>
                    <p class="font-bold text-oat">Rs. <?php echo number_format($i['price'] * $i['quantity']); ?></p>
                </div>
            <?php endwhile; ?>
            <div class="flex justify-between items-center pt-4">
                <span class="font-bold text-choco text-lg">Total</span>
                <span class="font-black text-oat text-2xl">Rs. <?php echo number_format($order['total_amount']); ?></span>
            </div>
        </div>
    </div>
</main>

</body>
</html>
<?php $items_stmt->close(); ?>