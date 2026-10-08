<?php
include 'config/db.php';

// Remove item
if(isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

// Update quantity
if(isset($_POST['update_qty'])) {
    $id = (int)$_POST['product_id'];
    $qty = max(1, (int)$_POST['quantity']);
    $_SESSION['cart'][$id] = $qty;
    header("Location: cart.php");
    exit();
}

// SEO
$page_title = 'Shopping Cart | ' . SITE_NAME;
$page_desc = 'View and manage your shopping cart. Secure checkout with Cash on Delivery, Easypaisa and JazzCash.';
$page_keywords = 'shopping cart, buy oats online';

include 'includes/header.php';
?>

<section class="py-12 sm:py-16 bg-cream min-h-[70vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div class="text-center mb-10 sm:mb-12">
            <h1 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-choco mb-3">
                Your <span class="text-oat italic">Shopping Cart</span>
            </h1>
            <p class="text-brown text-sm sm:text-base">Review your items before checkout</p>
        </div>

        <?php if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
            
            <?php
            $total = 0;
            $cart_items = [];
            foreach($_SESSION['cart'] as $id => $qty) {
                $id = (int)$id;
                $res = $conn->query("SELECT * FROM products WHERE id = $id");
                if($res && $res->num_rows > 0) {
                    $p = $res->fetch_assoc();
                    $subtotal = $p['price'] * $qty;
                    $total += $subtotal;
                    $cart_items[] = [
                        'id' => $id, 'name' => $p['name'], 'price' => $p['price'],
                        'qty' => $qty, 'subtotal' => $subtotal, 'image' => $p['image']
                    ];
                }
            }
            ?>
            
            <!-- Desktop Table -->
            <div class="hidden md:block bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                <table class="w-full">
                    <thead class="bg-cream-dark/50">
                        <tr>
                            <th class="text-left p-5 font-bold text-choco text-xs uppercase tracking-wider">Product</th>
                            <th class="text-left p-5 font-bold text-choco text-xs uppercase tracking-wider">Price</th>
                            <th class="text-left p-5 font-bold text-choco text-xs uppercase tracking-wider">Qty</th>
                            <th class="text-left p-5 font-bold text-choco text-xs uppercase tracking-wider">Subtotal</th>
                            <th class="text-left p-5 font-bold text-choco text-xs uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart_items as $item): ?>
                        <tr class="border-t border-cream hover:bg-cream/50 transition-colors">
                            <td class="p-5">
                                <div class="flex items-center gap-4">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         class="w-16 h-16 object-cover rounded-xl" loading="lazy">
                                    <span class="font-semibold text-choco"><?php echo htmlspecialchars($item['name']); ?></span>
                                </div>
                            </td>
                            <td class="p-5 text-brown">Rs. <?php echo number_format($item['price']); ?></td>
                            <td class="p-5">
                                <form method="POST" class="flex items-center gap-2">
                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['qty']; ?>" min="1" 
                                           class="w-16 px-3 py-2 border-2 border-cream-dark rounded-lg text-center font-bold text-choco outline-none focus:border-oat">
                                    <button type="submit" name="update_qty" aria-label="Update" class="bg-oat hover:bg-oat-dark text-white px-3 py-2 rounded-lg text-sm transition-all">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="p-5 font-bold text-oat">Rs. <?php echo number_format($item['subtotal']); ?></td>
                            <td class="p-5">
                                <a href="cart.php?remove=<?php echo $item['id']; ?>" aria-label="Remove" class="text-red-500 hover:text-red-700 font-semibold text-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden space-y-4 mb-6">
                <?php foreach($cart_items as $item): ?>
                <div class="bg-white rounded-2xl shadow-lg p-4">
                    <div class="flex gap-4 mb-4">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="w-20 h-20 object-cover rounded-xl flex-shrink-0" loading="lazy">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-choco mb-1 truncate"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-sm text-brown">Rs. <?php echo number_format($item['price']); ?> each</p>
                            <p class="font-bold text-oat mt-1">Rs. <?php echo number_format($item['subtotal']); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-cream">
                        <form method="POST" class="flex items-center gap-2">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="number" name="quantity" value="<?php echo $item['qty']; ?>" min="1" 
                                   class="w-14 px-2 py-1.5 border-2 border-cream-dark rounded-lg text-center font-bold text-choco text-sm outline-none focus:border-oat">
                            <button type="submit" name="update_qty" class="bg-oat text-white px-3 py-2 rounded-lg text-xs">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </form>
                        <a href="cart.php?remove=<?php echo $item['id']; ?>" class="text-red-500 font-semibold text-sm">
                            <i class="fas fa-trash-alt"></i> Remove
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Cart Total -->
            <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <p class="text-sm text-brown mb-1">Subtotal</p>
                        <p class="font-bold text-choco text-lg">Rs. <?php echo number_format($total); ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-brown mb-1">Shipping</p>
                        <p class="font-bold text-green-600 text-lg">
                            <?php echo $total >= FREE_SHIPPING_MIN ? 'FREE' : 'Rs. ' . number_format(SHIPPING_FEE); ?>
                        </p>
                    </div>
                    <div class="sm:text-right">
                        <p class="text-sm text-brown mb-1">Total</p>
                        <p class="font-black text-oat text-2xl sm:text-3xl">Rs. <?php echo number_format($total); ?></p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <a href="index.php" class="bg-white border-2 border-oat text-oat hover:bg-oat hover:text-white px-6 py-3 rounded-full font-bold text-sm sm:text-base transition-all text-center">
                        Continue Shopping
                    </a>
                    <a href="checkout.php" class="bg-oat hover:bg-oat-dark text-white px-8 py-3 rounded-full font-bold text-sm sm:text-base transition-all hover:shadow-xl hover:-translate-y-1 text-center inline-flex items-center justify-center gap-2">
                        <i class="fas fa-arrow-right"></i> Checkout
                    </a>
                </div>
            </div>
            
        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-lg p-10 sm:p-16 text-center">
                <i class="fas fa-shopping-bag text-oat text-5xl sm:text-6xl mb-6"></i>
                <h3 class="font-display text-xl sm:text-2xl font-bold text-choco mb-3">Your cart is empty</h3>
                <p class="text-brown text-sm sm:text-base mb-8">Add some delicious oats to your cart!</p>
                <a href="index.php" class="bg-oat hover:bg-oat-dark text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold transition-all inline-flex items-center gap-2">
                    <i class="fas fa-shopping-basket"></i> Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>