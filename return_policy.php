<?php
include 'config/db.php';

$page_title = 'Return Policy | ' . SITE_NAME;
$page_desc = 'Return and exchange policy for Pure Oats with Nuts orders in Pakistan.';
$page_keywords = 'return policy, exchange policy, oats return, refund pakistan';

include 'includes/header.php';
?>

<section class="py-16 sm:py-20 bg-cream">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <span class="inline-block bg-oat/20 text-oat-dark px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Return Policy
            </span>
            <h1 class="font-display text-4xl sm:text-5xl font-bold text-choco">Returns & Exchanges</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-lg p-6 sm:p-10 space-y-6 text-brown leading-relaxed">
            <p>We want you to be fully satisfied with every order from <?php echo SITE_NAME; ?>.</p>

            <div class="space-y-4">
                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">1. Eligibility</h2>
                    <p>Returns are accepted only if the product is damaged, incorrect, or not as described at the time of delivery.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">2. Time Window</h2>
                    <p>Please notify us within 7 days of receiving the product to request a return or exchange.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">3. Product Condition</h2>
                    <p>The original packaging and product condition should be intact for any return or exchange request to be reviewed.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">4. Damaged or Wrong Order</h2>
                    <p>If the order arrives damaged or incorrect, send us a clear photo and your order details, and we will arrange a replacement or refund according to the situation.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">5. Contact Us</h2>
                    <p>For any return inquiry, contact us on <a href="tel:+923113138188" class="text-oat hover:text-oat-dark font-semibold">+92 311 3138188</a> or WhatsApp and we will guide you through the process.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
