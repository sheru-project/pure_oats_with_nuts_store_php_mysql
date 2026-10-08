<?php
include 'config/db.php';

$page_title = 'Shipping Policy | ' . SITE_NAME;
$page_desc = 'Our shipping policy for orders across Pakistan, delivery timelines, and packaging standards.';
$page_keywords = 'shipping policy, oats delivery pakistan, order shipping, local delivery';

include 'includes/header.php';
?>

<section class="py-16 sm:py-20 bg-cream">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <span class="inline-block bg-oat/20 text-oat-dark px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Shipping Policy
            </span>
            <h1 class="font-display text-4xl sm:text-5xl font-bold text-choco">Delivery Information</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-lg p-6 sm:p-10 space-y-6 text-brown leading-relaxed">
            <p>At <?php echo SITE_NAME; ?>, we work hard to ensure your order reaches you safely, fresh, and on time.</p>

            <div class="space-y-4">
                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">1. Order Processing</h2>
                    <p>Orders are processed within 24 hours of confirmation, excluding public holidays and weekends when necessary.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">2. Delivery Areas</h2>
                    <p>We deliver across Pakistan. Delivery timings may vary by city, courier availability, and local conditions.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">3. Charges</h2>
                    <p>Orders above Rs. <?php echo number_format(FREE_SHIPPING_MIN); ?> qualify for free shipping. Orders below this amount incur a shipping fee of Rs. <?php echo number_format(SHIPPING_FEE); ?>.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">4. Delivery Time</h2>
                    <p>Most orders are delivered within 2 to 4 working days after dispatch. During peak seasons or weather disruptions, delivery may take a little longer.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">5. Customer Support</h2>
                    <p>If there is a delay, missing parcel, or address issue, please contact us immediately at <a href="tel:+923113138188" class="text-oat hover:text-oat-dark font-semibold">+92 311 3138188</a> or on WhatsApp.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
