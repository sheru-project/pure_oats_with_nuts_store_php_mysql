<?php
include 'config/db.php';

$page_title = 'Terms & Conditions | ' . SITE_NAME;
$page_desc = 'The terms and conditions for shopping with Pure Oats with Nuts in Pakistan.';
$page_keywords = 'terms and conditions, oats shop terms, online store conditions';

include 'includes/header.php';
?>

<section class="py-16 sm:py-20 bg-cream">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <span class="inline-block bg-oat/20 text-oat-dark px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Terms & Conditions
            </span>
            <h1 class="font-display text-4xl sm:text-5xl font-bold text-choco">Store Terms</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-lg p-6 sm:p-10 space-y-6 text-brown leading-relaxed">
            <p>By placing an order with <?php echo SITE_NAME; ?>, you agree to the following terms and conditions.</p>

            <div class="space-y-4">
                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">1. Product Availability</h2>
                    <p>Products are subject to availability. If an item is unavailable, we may inform you and offer an alternative or refund.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">2. Pricing</h2>
                    <p>All prices are shown in Pakistani Rupees and may change without prior notice. Taxes or shipping charges may apply where applicable.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">3. Orders</h2>
                    <p>Once an order is placed, it becomes binding. We reserve the right to decline or cancel any order for security, verification, or legal reasons.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">4. Payments</h2>
                    <p>We accept Cash on Delivery, Easypaisa, and JazzCash payment methods as listed on the site.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">5. Limitation of Liability</h2>
                    <p>We are not liable for any indirect loss or damage resulting from the use of our products or services beyond their direct purchase value.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">6. Contact</h2>
                    <p>For any concerns or clarifications, contact us at <a href="tel:+923113138188" class="text-oat hover:text-oat-dark font-semibold">+92 311 3138188</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
