<?php
// ============ ERROR REPORTING (Debug ke liye) ============
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============ DATABASE CONNECTION ============
include 'config/db.php';

// ============ CHECK ORDER ID ============
if(!isset($_SESSION['last_order_id'])) {
    // Agar order ID nahi hai toh home bhej dein
    header("Location: index.php");
    exit();
}

$order_id = (int)$_SESSION['last_order_id'];
$whatsapp_link = isset($_SESSION['whatsapp_link']) ? $_SESSION['whatsapp_link'] : '#';

// ============ FETCH ORDER FROM DATABASE ============
$order_query = "SELECT * FROM orders WHERE id = $order_id";
$order_result = $conn->query($order_query);

if(!$order_result) {
    die("Order Query Error: " . $conn->error);
}

if($order_result->num_rows == 0) {
    // Order nahi mila
    header("Location: index.php");
    exit();
}

$order = $order_result->fetch_assoc();

if($order['payment_method'] === 'COD') {
    $payment_method_label = 'Cash on Delivery';
} elseif($order['payment_method'] === 'Easypaisa') {
    $payment_method_label = 'Easypaisa';
} elseif($order['payment_method'] === 'JazzCash') {
    $payment_method_label = 'JazzCash';
} elseif($order['payment_method'] === 'Easypaisa/JazzCash') {
    $payment_method_label = 'Easypaisa / JazzCash';
} else {
    $payment_method_label = $order['payment_method'];
}

// ============ FETCH ORDER ITEMS ============
$items_query = "SELECT oi.*, p.name, p.image 
                FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_query);

if(!$items_result) {
    die("Items Query Error: " . $conn->error);
}

// ============ GET FIRST NAME ============
$name_parts = explode(' ', trim($order['customer_name']));
$first_name = $name_parts[0];

// ============ INCLUDE HEADER ============
include 'includes/header.php';
?>

<!-- ==================== SUCCESS HERO ==================== -->
<section class="relative py-20 bg-cream overflow-hidden">
    
    <div class="absolute inset-0 opacity-5">
        <img src="https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=1600" class="w-full h-full object-cover" alt="bg">
    </div>

    <div class="relative max-w-7xl mx-auto px-6">
        
        <!-- Success Animation -->
        <div class="text-center mb-16" data-aos="zoom-in">
            
            <div class="inline-flex items-center justify-center w-32 h-32 bg-green-100 rounded-full mb-8 relative">
                <div class="absolute inset-0 bg-green-500/30 rounded-full animate-ping"></div>
                <i class="fas fa-check-circle text-green-500 text-7xl relative z-10"></i>
            </div>
            
            <div class="inline-block bg-oat/20 text-oat-dark px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                <i class="fas fa-check mr-1"></i> Order Confirmed
            </div>
            
            <h1 class="font-display text-5xl md:text-6xl font-bold text-choco mb-6">
                Thank You, <span class="text-oat italic"><?php echo htmlspecialchars($first_name); ?>!</span>
            </h1>
            
            <p class="text-brown text-lg max-w-2xl mx-auto leading-relaxed">
                Your order has been successfully placed. We've received it and will contact you shortly to confirm the delivery details.
            </p>
        </div>

        <!-- ==================== ORDER NUMBER CARD ==================== -->
        <div class="max-w-4xl mx-auto mb-12" data-aos="fade-up">
            <div class="bg-gradient-to-r from-oat to-oat-dark rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                
                <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/10 rounded-full -ml-16 -mb-16"></div>
                
                <div class="relative grid md:grid-cols-3 gap-6 items-center text-center md:text-left">
                    
                    <div>
                        <p class="text-xs uppercase tracking-[2px] opacity-70 mb-2">Order Number</p>
                        <h2 class="font-display text-3xl md:text-4xl font-black">
                            #<?php echo str_pad($order_id, 6, '0', STR_PAD_LEFT); ?>
                        </h2>
                    </div>
                    
                    <div class="md:border-l md:border-r border-white/20 md:px-6">
                        <p class="text-xs uppercase tracking-[2px] opacity-70 mb-2">Order Date</p>
                        <h3 class="font-bold text-xl">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <?php echo date('d M, Y', strtotime($order['created_at'])); ?>
                        </h3>
                    </div>
                    
                    <div>
                        <p class="text-xs uppercase tracking-[2px] opacity-70 mb-2">Status</p>
                        <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full text-sm font-bold uppercase tracking-wider">
                            <i class="fas fa-clock"></i>
                            <?php echo htmlspecialchars($order['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== MAIN CONTENT GRID ==================== -->
        <div class="max-w-6xl mx-auto grid lg:grid-cols-3 gap-8 mb-12">
            
            <!-- LEFT: ORDER ITEMS (2/3) -->
            <div class="lg:col-span-2" data-aos="fade-right">
                
                <div class="bg-white rounded-3xl shadow-xl p-8">
                    
                    <h2 class="font-display text-2xl font-bold text-choco mb-6 pb-4 border-b-2 border-cream flex items-center gap-3">
                        <i class="fas fa-box-open text-oat"></i>
                        Your Order Items
                    </h2>
                    
                    <div class="space-y-4 mb-6">
                        <?php 
                        $subtotal = 0;
                        if($items_result->num_rows > 0):
                            while($item = $items_result->fetch_assoc()): 
                                $item_total = $item['price'] * $item['quantity'];
                                $subtotal += $item_total;
                                $img = !empty($item['image']) ? $item['image'] : 'https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?w=300';
                        ?>
                            <div class="flex items-center gap-4 p-4 bg-cream/50 rounded-2xl hover:bg-cream transition-colors">
                                <img src="<?php echo htmlspecialchars($img); ?>" 
                                     alt="<?php echo htmlspecialchars($item['name'] ?? 'Product'); ?>" 
                                     class="w-20 h-20 object-cover rounded-xl flex-shrink-0 shadow-sm">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-choco mb-1">
                                        <?php echo htmlspecialchars($item['name'] ?? 'Product'); ?>
                                    </h4>
                                    <p class="text-sm text-brown">
                                        Qty: <span class="font-semibold"><?php echo $item['quantity']; ?></span> 
                                        × Rs. <?php echo number_format($item['price']); ?>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-oat text-lg">
                                        Rs. <?php echo number_format($item_total); ?>
                                    </p>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <p class="text-brown text-center py-4">No items found.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="space-y-3 pt-6 border-t-2 border-cream">
                        <div class="flex justify-between text-sm">
                            <span class="text-brown">Subtotal</span>
                            <span class="font-semibold text-choco">Rs. <?php echo number_format($subtotal); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-brown">Shipping</span>
                            <span class="font-semibold text-green-600">
                                <?php echo $subtotal >= FREE_SHIPPING_MIN ? 'FREE' : 'Rs. ' . number_format(SHIPPING_FEE); ?>
                            </span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-brown">Tax</span>
                            <span class="font-semibold text-choco">Rs. 0</span>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-r from-oat to-oat-dark rounded-2xl p-6 text-white mt-6 flex justify-between items-center">
                        <div>
                            <p class="text-xs uppercase tracking-[2px] opacity-70 mb-1">Total</p>
                            <p class="text-sm opacity-80">
                                <?php echo htmlspecialchars($payment_method_label, ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                        <span class="font-black text-4xl font-body">
                            Rs. <?php echo number_format($order['total_amount']); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- RIGHT: INFO CARDS (1/3) -->
            <div class="space-y-6" data-aos="fade-left">
                
                <!-- Customer -->
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b-2 border-cream">
                        <div class="w-12 h-12 bg-oat rounded-full flex items-center justify-center text-white text-lg">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="font-bold text-choco uppercase tracking-wider text-sm">Customer</h3>
                    </div>
                    <p class="font-bold text-choco mb-1">
                        <?php echo htmlspecialchars($order['customer_name']); ?>
                    </p>
                    <p class="text-sm text-brown">
                        <i class="fas fa-phone text-oat mr-2"></i>
                        <?php echo htmlspecialchars($order['phone']); ?>
                    </p>
                    <?php if(!empty($order['customer_email'])): ?>
                        <p class="text-sm text-brown mt-1">
                            <i class="fas fa-envelope text-oat mr-2"></i>
                            <?php echo htmlspecialchars($order['customer_email'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <!-- Shipping -->
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b-2 border-cream">
                        <div class="w-12 h-12 bg-oat rounded-full flex items-center justify-center text-white text-lg">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h3 class="font-bold text-choco uppercase tracking-wider text-sm">Shipping To</h3>
                    </div>
                    <p class="text-sm text-brown leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                    </p>
                </div>
                
                <!-- Payment -->
                <div class="bg-white rounded-3xl shadow-xl p-6">
                    <div class="flex items-center gap-3 mb-4 pb-4 border-b-2 border-cream">
                        <div class="w-12 h-12 bg-oat rounded-full flex items-center justify-center text-white text-lg">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h3 class="font-bold text-choco uppercase tracking-wider text-sm">Payment</h3>
                    </div>
                    <p class="font-bold text-choco mb-2">
                        <?php echo htmlspecialchars($payment_method_label, ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <?php if(!empty($order['transaction_id'])): ?>
                        <div class="bg-cream rounded-lg px-3 py-2">
                            <p class="text-xs text-brown uppercase tracking-wider mb-1">Transaction ID</p>
                            <p class="text-sm font-mono font-bold text-choco">
                                <?php echo htmlspecialchars($order['transaction_id']); ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-brown">
                            <i class="fas fa-info-circle text-oat mr-1"></i>
                            <?php echo $order['payment_method'] === 'COD' ? 'Payment on delivery' : 'No transaction ID recorded'; ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ==================== WHATSAPP CTA ==================== -->
        <?php if($whatsapp_link != '#'): ?>
        <div class="max-w-4xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="100">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-3xl p-8 md:p-10 relative overflow-hidden">
                
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-200/30 rounded-full -mr-16 -mt-16"></div>
                
                <div class="relative flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                    
                    <div class="w-24 h-24 bg-[#25D366] rounded-full flex items-center justify-center flex-shrink-0 shadow-xl relative">
                        <div class="absolute inset-0 bg-[#25D366] rounded-full animate-ping opacity-30"></div>
                        <i class="fab fa-whatsapp text-white text-5xl relative z-10"></i>
                    </div>
                    
                    <div class="flex-1">
                        <span class="inline-block bg-green-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                            <i class="fas fa-exclamation-circle mr-1"></i> Important Step
                        </span>
                        <h3 class="font-display text-3xl font-bold text-choco mb-3">
                            Confirm Your Order on WhatsApp!
                        </h3>
                        <p class="text-brown mb-6">
                            Please send us your order details on WhatsApp so we can process it immediately.
                        </p>
                        <a href="<?php echo htmlspecialchars($whatsapp_link); ?>" target="_blank" 
                           class="inline-flex items-center gap-3 bg-[#25D366] hover:bg-[#1da851] text-white px-8 py-4 rounded-full font-bold transition-all hover:shadow-2xl hover:-translate-y-1">
                            <i class="fab fa-whatsapp text-xl"></i>
                            Open WhatsApp & Confirm
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ==================== WHAT'S NEXT ==================== -->
        <div class="max-w-6xl mx-auto mb-12" data-aos="fade-up" data-aos-delay="200">
            <div class="text-center mb-10">
                <span class="inline-block bg-oat/20 text-oat-dark px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                    What's Next
                </span>
                <h2 class="font-display text-3xl md:text-4xl font-bold text-choco">
                    Here's What <span class="text-oat italic">Happens Now</span>
                </h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                
                <!-- Step 1 -->
                <div class="bg-white rounded-3xl shadow-lg p-8 text-center relative hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-oat text-white w-10 h-10 rounded-full flex items-center justify-center font-bold shadow-lg">
                        1
                    </div>
                    <div class="w-20 h-20 bg-oat/20 rounded-full flex items-center justify-center text-oat text-3xl mx-auto mb-5 mt-4">
                        <i class="fas fa-phone-volume"></i>
                    </div>
                    <h4 class="font-display text-xl font-bold text-choco mb-3">Confirmation Call</h4>
                    <p class="text-sm text-brown leading-relaxed">
                        Our team will call you within <strong class="text-oat">2 hours</strong> to verify your order.
                    </p>
                </div>
                
                <!-- Step 2 -->
                <div class="bg-white rounded-3xl shadow-lg p-8 text-center relative hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-oat text-white w-10 h-10 rounded-full flex items-center justify-center font-bold shadow-lg">
                        2
                    </div>
                    <div class="w-20 h-20 bg-oat/20 rounded-full flex items-center justify-center text-oat text-3xl mx-auto mb-5 mt-4">
                        <i class="fas fa-box"></i>
                    </div>
                    <h4 class="font-display text-xl font-bold text-choco mb-3">Careful Packing</h4>
                    <p class="text-sm text-brown leading-relaxed">
                        Your order is <strong class="text-oat">freshly packed</strong> with premium materials.
                    </p>
                </div>
                
                <!-- Step 3 -->
                <div class="bg-white rounded-3xl shadow-lg p-8 text-center relative hover:-translate-y-2 hover:shadow-2xl transition-all duration-300">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-oat text-white w-10 h-10 rounded-full flex items-center justify-center font-bold shadow-lg">
                        3
                    </div>
                    <div class="w-20 h-20 bg-oat/20 rounded-full flex items-center justify-center text-oat text-3xl mx-auto mb-5 mt-4">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h4 class="font-display text-xl font-bold text-choco mb-3">Fast Delivery</h4>
                    <p class="text-sm text-brown leading-relaxed">
                        Delivered in <strong class="text-oat">2-4 working days</strong> all across Pakistan.
                    </p>
                </div>
            </div>
        </div>

        <!-- ==================== ACTION BUTTONS ==================== -->
        <div class="max-w-4xl mx-auto text-center" data-aos="fade-up" data-aos-delay="300">
            <div class="flex flex-wrap justify-center gap-4">
                <a href="index.php" class="bg-oat hover:bg-oat-dark text-white px-8 py-4 rounded-full font-bold transition-all hover:shadow-xl hover:-translate-y-1 inline-flex items-center gap-3">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="index.php#product" class="bg-white border-2 border-oat text-oat hover:bg-oat hover:text-white px-8 py-4 rounded-full font-bold transition-all inline-flex items-center gap-3">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ==================== SUPPORT SECTION ==================== -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-6 text-center" data-aos="fade-up">
        
        <div class="bg-cream rounded-3xl p-8 md:p-10">
            <div class="w-16 h-16 bg-oat rounded-full flex items-center justify-center text-white text-2xl mx-auto mb-5">
                <i class="fas fa-headset"></i>
            </div>
            
            <h3 class="font-display text-2xl font-bold text-choco mb-3">
                Need Help With Your Order?
            </h3>
            <p class="text-brown mb-6 max-w-xl mx-auto">
                Our customer support team is available 24/7 to assist you.
            </p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="tel:+923113138188" class="inline-flex items-center gap-2 bg-white text-choco px-6 py-3 rounded-full font-semibold shadow-sm hover:shadow-md transition-all border border-cream-dark">
                    <i class="fas fa-phone text-oat"></i>
                    +92 311 3138188
                </a>
                <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-2 bg-[#25D366] text-white px-6 py-3 rounded-full font-semibold shadow-sm hover:shadow-md transition-all">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<?php 
// Clear WhatsApp link but keep order_id for refresh
unset($_SESSION['whatsapp_link']);
include 'includes/footer.php'; 
?>