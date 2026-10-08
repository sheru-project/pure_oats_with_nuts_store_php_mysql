<?php
include 'config/db.php';

if(empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

$page_title = 'Checkout | ' . SITE_NAME;
$page_desc = 'Enter your delivery details and place your order.';
$page_keywords = 'checkout, place order';
$error = '';
$customer_name = '';
$customer_email = '';
$phone = '';
$address = '';
$delivery_city = 'Hyderabad';
$cod_available = isCodAvailable($delivery_city, $address);
$payment_method = '';
$transaction_id = '';
$cart_items = [];
$subtotal = 0;
$shipping = 0;
$total = 0;

if(!isset($_SESSION['checkout_csrf'])
    || !is_string($_SESSION['checkout_csrf'])
    || !preg_match('/\A[a-f0-9]{64}\z/', $_SESSION['checkout_csrf'])) {
    $_SESSION['checkout_csrf'] = bin2hex(random_bytes(32));
}

foreach($_SESSION['cart'] as $product_id => $quantity) {
    $product_id = filter_var($product_id, FILTER_VALIDATE_INT);
    $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

    if(!$product_id || $product_id < 1 || !$quantity || $quantity < 1 || $quantity > 99) {
        $error = 'Your cart has an invalid item. Please return to your cart and update it.';
        break;
    }

    $product_stmt = $conn->prepare('SELECT id, name, price, image FROM products WHERE id = ? LIMIT 1');
    $product_stmt->bind_param('i', $product_id);
    $product_stmt->execute();
    $product_result = $product_stmt->get_result();
    $product = $product_result->fetch_assoc();
    $product_stmt->close();

    if(!$product) {
        $error = 'A product in your cart is no longer available. Please update your cart.';
        break;
    }

    $line_total = (float)$product['price'] * $quantity;
    $subtotal += $line_total;
    $cart_items[] = [
        'id' => (int)$product['id'],
        'name' => $product['name'],
        'price' => (float)$product['price'],
        'image' => $product['image'],
        'quantity' => $quantity,
        'line_total' => $line_total,
    ];
}

if(!$error && !$cart_items) {
    $error = 'Your cart is empty. Add an item before checkout.';
}

if(!$error) {
    $shipping = $subtotal >= FREE_SHIPPING_MIN ? 0 : getShippingCharge($delivery_city);
    $total = $subtotal + $shipping;
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_email = $_POST['customer_email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $delivery_city = $_POST['delivery_city'] ?? 'Hyderabad';
    $payment_method = $_POST['payment_method'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if(!is_string($customer_name) || !is_string($customer_email) || !is_string($phone) || !is_string($address)
        || !is_string($delivery_city) || !is_string($payment_method) || !is_string($transaction_id) || !is_string($csrf_token)) {
        $error = 'Please check your details and try again.';
    } elseif(!hash_equals($_SESSION['checkout_csrf'], $csrf_token)) {
        $error = 'Your checkout session expired. Please refresh the page and try again.';
    } else {
        $customer_name = trim($customer_name);
        $customer_email = trim($customer_email);
        $phone = trim($phone);
        $address = trim($address);
        $delivery_city = trim($delivery_city);
        $cod_available = isCodAvailable($delivery_city, $address);
        $transaction_id = trim($transaction_id);

        if($customer_name === '' || strlen($customer_name) > 255) {
            $error = 'Please enter your name (up to 255 characters).';
        } elseif(strlen($customer_email) > 254 || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif(!preg_match('/\A[0-9+().\-\s]{7,20}\z/', $phone)) {
            $error = 'Please enter a valid phone number.';
        } elseif($delivery_city === '') {
            $error = 'Please enter your delivery city or location.';
        } elseif($address === '' || strlen($address) > 5000) {
            $error = 'Please enter your complete delivery address.';
        } elseif(!in_array($payment_method, ['COD', 'Easypaisa', 'JazzCash'], true)) {
            $error = 'Please select a payment method.';
        } elseif($payment_method === 'COD' && !$cod_available) {
            $error = 'Cash on Delivery is only available in Hyderabad. Please pay in advance using Easypaisa or JazzCash.';
        } elseif($payment_method !== 'COD' && $transaction_id === '') {
            $error = 'Please enter the transaction ID for your online payment.';
        } elseif(strlen($transaction_id) > 100) {
            $error = 'The payment reference must be 100 characters or fewer.';
        } else {
            if($payment_method === 'COD') {
                $transaction_id = '';
            }

            $shipping = $subtotal >= FREE_SHIPPING_MIN ? 0 : getShippingCharge($delivery_city);
            $total = $subtotal + $shipping;

            if($shipping === 0 && $subtotal < FREE_SHIPPING_MIN) {
                $shipping = 0;
            }

            try {
                $conn->begin_transaction();

                $status = 'Pending';
                $order_stmt = $conn->prepare(
                    'INSERT INTO orders (customer_name, customer_email, phone, address, total_amount, payment_method, transaction_id, status)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $order_stmt->bind_param(
                    'ssssdsss',
                    $customer_name,
                    $customer_email,
                    $phone,
                    $address,
                    $total,
                    $payment_method,
                    $transaction_id,
                    $status
                );
                $order_stmt->execute();
                $order_id = (int)$conn->insert_id;
                $order_stmt->close();

                $item_stmt = $conn->prepare(
                    'INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)'
                );
                foreach($cart_items as $item) {
                    $product_id = $item['id'];
                    $quantity = $item['quantity'];
                    $price = $item['price'];
                    $item_stmt->bind_param('iiid', $order_id, $product_id, $quantity, $price);
                    $item_stmt->execute();
                }
                $item_stmt->close();
                $conn->commit();

                require_once 'config/email.php';
                $email_escape = static function(string $value): string {
                    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                };
                $item_rows = '';
                $text_items = [];
                foreach($cart_items as $item) {
                    $item_rows .= '<tr><td>' . $email_escape($item['name']) . '</td><td>'
                        . $item['quantity'] . '</td><td>Rs. ' . number_format($item['line_total'], 2) . '</td></tr>';
                    $text_items[] = $item['name'] . ' x ' . $item['quantity'] . ' — Rs. ' . number_format($item['line_total'], 2);
                }
                $order_number = '#' . str_pad((string)$order_id, 6, '0', STR_PAD_LEFT);
                $order_summary_html = '<h2>New order ' . $order_number . '</h2><p><strong>Customer:</strong> '
                    . $email_escape($customer_name) . '<br><strong>Email:</strong> ' . $email_escape($customer_email)
                    . '<br><strong>Phone:</strong> ' . $email_escape($phone) . '<br><strong>Address:</strong><br>'
                    . nl2br($email_escape($address)) . '<br><strong>Payment:</strong> ' . $email_escape($payment_method)
                    . '<br><strong>Transaction ID:</strong> ' . $email_escape($transaction_id ?: 'N/A')
                    . '</p><table border="1" cellpadding="6" cellspacing="0"><thead><tr><th>Item</th><th>Qty</th>'
                    . '<th>Amount</th></tr></thead><tbody>' . $item_rows . '</tbody></table><p><strong>Total: Rs. '
                    . number_format($total, 2) . '</strong></p>';
                $order_summary_text = "New order {$order_number}\nCustomer: {$customer_name}\nEmail: {$customer_email}\n"
                    . "Phone: {$phone}\nAddress: {$address}\nPayment: {$payment_method}\nTransaction ID: "
                    . ($transaction_id ?: 'N/A') . "\n" . implode("\n", $text_items) . "\nTotal: Rs. " . number_format($total, 2);

                try {
                    sendStoreEmail(
                        ORDER_NOTIFICATION_EMAIL,
                        SITE_NAME . ' — New order ' . $order_number,
                        $order_summary_html,
                        $order_summary_text
                    );
                } catch(Throwable $email_exception) {
                    error_log('New order email failed for order ' . $order_id . ': ' . $email_exception->getMessage());
                }

                $message_lines = [
                    'New order #' . str_pad((string)$order_id, 6, '0', STR_PAD_LEFT),
                    'Customer: ' . $customer_name,
                    'Email: ' . $customer_email,
                    'Phone: ' . $phone,
                    'Address: ' . $address,
                    'Payment: ' . $payment_method,
                    'Transaction ID: ' . ($transaction_id ?: 'N/A'),
                    'Items:',
                ];

                foreach($cart_items as $item) {
                    $message_lines[] = '- ' . $item['name'] . ' x ' . $item['quantity'] . ' = Rs. ' . number_format($item['line_total'], 2);
                }

                $message_lines[] = 'Total: Rs. ' . number_format($total, 2);
                $_SESSION['whatsapp_link'] = 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . rawurlencode(implode("\n", $message_lines));

                $_SESSION['last_order_id'] = $order_id;
                unset($_SESSION['cart'], $_SESSION['checkout_csrf']);
                header('Location: order_success.php');
                exit();
            } catch(Throwable $exception) {
                $conn->rollback();
                error_log('Checkout order creation failed: ' . $exception->getMessage());
                $error = 'We could not place your order right now. Your cart is saved; please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<section class="py-10 sm:py-16 bg-cream min-h-[70vh]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-8 sm:mb-10">
            <h1 class="font-display text-3xl sm:text-4xl font-bold text-choco mb-2">Checkout</h1>
            <p class="text-brown text-sm sm:text-base">Enter your delivery details and place your order.</p>
        </div>

        <?php if($error): ?>
            <div class="max-w-4xl mx-auto bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6 text-red-700 text-sm" role="alert">
                <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if($cart_items): ?>
            <form method="POST" action="checkout.php" class="grid lg:grid-cols-5 gap-6 lg:gap-8">
                <div class="lg:col-span-3 space-y-6">
                    <section class="bg-white rounded-2xl shadow-sm p-5 sm:p-7">
                        <h2 class="font-display text-xl font-bold text-choco mb-5">Customer information</h2>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['checkout_csrf'], ENT_QUOTES, 'UTF-8'); ?>">

                        <div class="space-y-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-semibold text-choco mb-2">Full name</label>
                                <input id="customer_name" name="customer_name" type="text" maxlength="255" autocomplete="name" required
                                       value="<?php echo htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8'); ?>"
                                       class="w-full px-4 py-3 border-2 border-cream-dark rounded-xl outline-none focus:border-oat text-choco">
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-choco mb-2">Phone number</label>
                                <input id="phone" name="phone" type="tel" maxlength="20" autocomplete="tel" required
                                       value="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="03XX XXXXXXX"
                                       class="w-full px-4 py-3 border-2 border-cream-dark rounded-xl outline-none focus:border-oat text-choco">
                            </div>
                            <div>
                                <label for="customer_email" class="block text-sm font-semibold text-choco mb-2">Email address</label>
                                <input id="customer_email" name="customer_email" type="email" maxlength="254" autocomplete="email" required
                                       value="<?php echo htmlspecialchars($customer_email, ENT_QUOTES, 'UTF-8'); ?>"
                                       class="w-full px-4 py-3 border-2 border-cream-dark rounded-xl outline-none focus:border-oat text-choco">
                            </div>
                            <div>
                                <label for="delivery_city" class="block text-sm font-semibold text-choco mb-2">Delivery city / location</label>
                                <input id="delivery_city" name="delivery_city" type="text" list="shippingCities" maxlength="100" autocomplete="address-level2" required
                                       value="<?php echo htmlspecialchars($delivery_city, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="Enter your city or area"
                                       class="w-full px-4 py-3 border-2 border-cream-dark rounded-xl outline-none focus:border-oat text-choco">
                                <datalist id="shippingCities">
                                    <?php foreach(getShippingOptions() as $city => $fee): ?>
                                        <option value="<?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?></option>
                                    <?php endforeach; ?>
                                </datalist>
                            </div>
                            <div>
                                <label for="address" class="block text-sm font-semibold text-choco mb-2">Complete delivery address</label>
                                <textarea id="address" name="address" rows="4" maxlength="5000" autocomplete="street-address" required
                                          class="w-full px-4 py-3 border-2 border-cream-dark rounded-xl outline-none focus:border-oat text-choco"><?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>
                        </div>
                    </section>

                    <section class="bg-white rounded-2xl shadow-sm p-5 sm:p-7">
                        <h2 class="font-display text-xl font-bold text-choco mb-5">Payment method</h2>
                        <p id="paymentLocationNotice" class="text-sm text-brown mb-4" aria-live="polite"></p>
                        <div class="grid sm:grid-cols-2 gap-3">
                            <label id="codPaymentOption" class="flex items-center gap-3 border-2 border-cream-dark rounded-xl p-4 cursor-pointer has-[:checked]:border-oat">
                                <input id="codPaymentRadio" type="radio" name="payment_method" value="COD" required
                                       <?php echo $payment_method === 'COD' && $cod_available ? 'checked' : ''; ?>
                                       class="accent-[#D4A574]">
                                <span class="text-sm font-semibold text-choco">Cash on Delivery</span>
                            </label>
                            <label class="flex items-center gap-3 border-2 border-cream-dark rounded-xl p-4 cursor-pointer has-[:checked]:border-oat">
                                <input type="radio" name="payment_method" value="Easypaisa" required
                                       <?php echo $payment_method === 'Easypaisa' ? 'checked' : ''; ?>
                                       class="accent-[#D4A574]">
                                <span class="text-sm font-semibold text-choco">Easypaisa</span>
                            </label>
                            <label class="flex items-center gap-3 border-2 border-cream-dark rounded-xl p-4 cursor-pointer has-[:checked]:border-oat">
                                <input type="radio" name="payment_method" value="JazzCash" required
                                       <?php echo $payment_method === 'JazzCash' ? 'checked' : ''; ?>
                                       class="accent-[#D4A574]">
                                <span class="text-sm font-semibold text-choco">JazzCash</span>
                            </label>
                        </div>

                        <div id="onlinePaymentDetails" class="hidden mt-4 rounded-xl border-2 border-oat/40 bg-cream/60 p-4 sm:p-5" aria-live="polite">
                            <h3 id="paymentDetailsTitle" class="font-bold text-choco mb-3"></h3>
                            <p class="text-sm text-brown">Send the order total to this account:</p>
                            <dl class="mt-3 space-y-2 text-sm">
                                <div id="easypaisaAccountName" class="hidden flex justify-between gap-4">
                                    <dt class="text-brown">Account name</dt>
                                    <dd class="font-semibold text-choco"><?php echo htmlspecialchars(EASYPAISA_NAME, ENT_QUOTES, 'UTF-8'); ?></dd>
                                </div>
                                <div id="easypaisaAccountNumber" class="hidden flex justify-between gap-4">
                                    <dt class="text-brown">Easypaisa number</dt>
                                    <dd class="font-semibold text-choco"><?php echo htmlspecialchars(EASYPAISA_NUMBER, ENT_QUOTES, 'UTF-8'); ?></dd>
                                </div>
                                <div id="jazzcashAccountName" class="hidden flex justify-between gap-4">
                                    <dt class="text-brown">Account name</dt>
                                    <dd class="font-semibold text-choco"><?php echo htmlspecialchars(JAZZCASH_NAME, ENT_QUOTES, 'UTF-8'); ?></dd>
                                </div>
                                <div id="jazzcashAccountNumber" class="hidden flex justify-between gap-4">
                                    <dt class="text-brown">JazzCash number</dt>
                                    <dd class="font-semibold text-choco"><?php echo htmlspecialchars(JAZZCASH_NUMBER, ENT_QUOTES, 'UTF-8'); ?></dd>
                                </div>
                                <div class="flex justify-between gap-4 border-t border-cream-dark pt-2">
                                    <dt class="text-brown">Amount to send</dt>
                                    <dd id="onlinePaymentAmount" class="font-bold text-choco">Rs. <?php echo number_format($total, 2); ?></dd>
                                </div>
                            </dl>
                            <div class="mt-4">
                                <label for="transaction_id" class="block text-sm font-semibold text-choco mb-2">Transaction ID <span class="text-red-600">*</span></label>
                                <input id="transaction_id" name="transaction_id" type="text" maxlength="100"
                                       value="<?php echo htmlspecialchars($transaction_id, ENT_QUOTES, 'UTF-8'); ?>"
                                       placeholder="Enter transaction ID after payment"
                                       class="w-full px-4 py-3 border-2 border-cream-dark rounded-xl outline-none focus:border-oat text-choco">
                                <p class="text-xs text-brown mt-2">Complete your payment, then enter the transaction ID to place the order.</p>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-7 lg:sticky lg:top-24">
                        <h2 class="font-display text-xl font-bold text-choco mb-5">Your order</h2>
                        <div class="space-y-4 mb-5">
                            <?php foreach($cart_items as $item): ?>
                                <div class="flex items-center gap-3">
                                    <img src="<?php echo htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8'); ?>"
                                         alt="" class="w-14 h-14 object-cover rounded-lg">
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-choco text-sm truncate"><?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                        <p class="text-xs text-brown"><?php echo $item['quantity']; ?> × Rs. <?php echo number_format($item['price'], 2); ?></p>
                                    </div>
                                    <span class="font-semibold text-choco text-sm">Rs. <?php echo number_format($item['line_total'], 2); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="space-y-3 border-t border-cream-dark pt-4 text-sm">
                            <div class="flex justify-between text-brown">
                                <span>Subtotal</span>
                                <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-brown">
                                <span>Shipping</span>
                                <span id="shippingAmountDisplay"><?php echo $shipping === 0 ? 'Free' : 'Rs. ' . number_format($shipping, 2); ?></span>
                            </div>
                            <div class="flex justify-between border-t border-cream-dark pt-3 font-bold text-choco text-lg">
                                <span>Total</span>
                                <span id="totalAmountDisplay">Rs. <?php echo number_format($total, 2); ?></span>
                            </div>
                        </div>

                        <button type="submit" class="w-full mt-6 bg-oat hover:bg-oat-dark text-white py-4 rounded-full font-bold transition-all inline-flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i> Place Order
                        </button>
                        <a href="cart.php" class="block text-center text-sm text-brown hover:text-oat mt-4">Back to cart</a>
                    </div>
                </aside>
            </form>
        <?php endif; ?>
    </div>
</section>

<script>
const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
const paymentDetails = document.getElementById('onlinePaymentDetails');
const paymentDetailsTitle = document.getElementById('paymentDetailsTitle');
const transactionIdInput = document.getElementById('transaction_id');
const easypaisaAccountName = document.getElementById('easypaisaAccountName');
const easypaisaAccountNumber = document.getElementById('easypaisaAccountNumber');
const jazzcashAccountName = document.getElementById('jazzcashAccountName');
const jazzcashAccountNumber = document.getElementById('jazzcashAccountNumber');
const onlinePaymentAmount = document.getElementById('onlinePaymentAmount');
const deliveryCityInput = document.getElementById('delivery_city');
const codPaymentOption = document.getElementById('codPaymentOption');
const codPaymentRadio = document.getElementById('codPaymentRadio');
const paymentLocationNotice = document.getElementById('paymentLocationNotice');
const deliveryAddressInput = document.getElementById('address');
const shippingAmountDisplay = document.getElementById('shippingAmountDisplay');
const totalAmountDisplay = document.getElementById('totalAmountDisplay');
const shippingRates = <?php echo json_encode(getShippingOptions(), JSON_UNESCAPED_UNICODE); ?>;
const hyderabadLocationAliases = <?php echo json_encode(getHyderabadLocationAliases(), JSON_UNESCAPED_UNICODE); ?>;
const otherCities = <?php echo json_encode(array_values(array_diff(array_keys(getShippingOptions()), ['Hyderabad', 'Other city'])), JSON_UNESCAPED_UNICODE); ?>;
const subtotalValue = <?php echo (float)$subtotal; ?>;
const freeShippingMinimum = <?php echo (float)FREE_SHIPPING_MIN; ?>;

function formatCurrency(amount) {
    return 'Rs. ' + Number(amount).toLocaleString('en-PK', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function getShippingFeeForCity(cityName) {
    const normalized = (cityName || '').trim();
    if(!normalized) {
        return 0;
    }

    const directMatch = shippingRates[normalized];
    if(typeof directMatch !== 'undefined') {
        return Number(directMatch);
    }

    const lowerCity = normalized.toLowerCase();
    for(const [city, fee] of Object.entries(shippingRates)) {
        if(city.toLowerCase() === lowerCity || city.toLowerCase().includes(lowerCity) || lowerCity.includes(city.toLowerCase())) {
            return Number(fee);
        }
    }

    return Number(shippingRates['Other city'] || 0);
}

function updateShippingSummary() {
    const shippingFee = subtotalValue >= freeShippingMinimum ? 0 : getShippingFeeForCity(deliveryCityInput.value);
    const totalValue = subtotalValue + shippingFee;

    shippingAmountDisplay.textContent = shippingFee === 0 ? 'Free' : formatCurrency(shippingFee);
    totalAmountDisplay.textContent = formatCurrency(totalValue);

    onlinePaymentAmount.textContent = formatCurrency(totalValue);
}

function updatePaymentDetails() {
    const location = deliveryCityInput.value.trim().toLowerCase();
    const fullAddress = deliveryAddressInput.value.trim().toLowerCase();
    const explicitOtherCity = otherCities.some((city) => {
        const normalizedCity = city.toLowerCase();
        return (` ${location.replace(/[^a-z0-9]+/g, ' ').trim()} `).includes(` ${normalizedCity} `);
    });
    const combinedLocation = ` ${`${location} ${fullAddress}`.replace(/[^a-z0-9]+/g, ' ').trim()} `;
    const isHyderabadLocation = hyderabadLocationAliases.some((alias) => {
        const normalizedAlias = alias.toLowerCase().replace(/[^a-z0-9]+/g, ' ').trim();
        return combinedLocation.includes(` ${normalizedAlias} `);
    });
    const codAvailable = !explicitOtherCity && isHyderabadLocation;
    codPaymentOption.classList.toggle('hidden', !codAvailable);
    codPaymentRadio.disabled = !codAvailable;
    if(!codAvailable && codPaymentRadio.checked) {
        codPaymentRadio.checked = false;
    }
    paymentLocationNotice.textContent = codAvailable
        ? 'Cash on Delivery is available for Hyderabad. Easypaisa and JazzCash advance payment are also available.'
        : 'Outside Hyderabad, advance payment via Easypaisa or JazzCash is required.';

    const selectedRadio = document.querySelector('input[name="payment_method"]:checked');
    const selectedPayment = selectedRadio ? selectedRadio.value : '';
    const isEasypaisa = selectedPayment === 'Easypaisa';
    const isJazzCash = selectedPayment === 'JazzCash';
    const requiresOnlinePayment = isEasypaisa || isJazzCash;

    paymentDetails.classList.toggle('hidden', !requiresOnlinePayment);
    transactionIdInput.required = requiresOnlinePayment;
    transactionIdInput.disabled = !requiresOnlinePayment;
    easypaisaAccountName.classList.toggle('hidden', !isEasypaisa);
    easypaisaAccountNumber.classList.toggle('hidden', !isEasypaisa);
    jazzcashAccountName.classList.toggle('hidden', !isJazzCash);
    jazzcashAccountNumber.classList.toggle('hidden', !isJazzCash);

    if(requiresOnlinePayment) {
        paymentDetailsTitle.textContent = isEasypaisa ? 'Easypaisa account details' : 'JazzCash account details';
    }
}

paymentRadios.forEach((radio) => radio.addEventListener('change', updatePaymentDetails));
deliveryCityInput.addEventListener('input', () => {
    updateShippingSummary();
    updatePaymentDetails();
});
deliveryAddressInput.addEventListener('input', updatePaymentDetails);
updatePaymentDetails();
updateShippingSummary();
</script>
<?php include 'includes/footer.php'; ?>
