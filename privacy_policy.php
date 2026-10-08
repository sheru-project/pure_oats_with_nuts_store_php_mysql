<?php
include 'config/db.php';

$page_title = 'Privacy Policy | ' . SITE_NAME;
$page_desc = 'Learn how Pure Oats with Nuts protects your personal information and uses order data.';
$page_keywords = 'privacy policy, customer data, personal information, oats store privacy';

include 'includes/header.php';
?>

<section class="py-16 sm:py-20 bg-cream">
    <div class="max-w-4xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <span class="inline-block bg-oat/20 text-oat-dark px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Privacy Policy
            </span>
            <h1 class="font-display text-4xl sm:text-5xl font-bold text-choco">Your Information Matters</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-lg p-6 sm:p-10 space-y-6 text-brown leading-relaxed">
            <p>We respect your privacy and are committed to protecting the information you share with us.</p>

            <div class="space-y-4">
                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">1. Information We Collect</h2>
                    <p>We may collect your name, phone number, address, order details, and payment-related information required to process your order.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">2. How We Use It</h2>
                    <p>Your information is used to process orders, arrange delivery, contact you about your purchase, and improve our service.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">3. Third Parties</h2>
                    <p>We do not sell your personal information. Information may be shared only with trusted service providers needed to process payments, shipping, or support requests.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">4. Security</h2>
                    <p>We take reasonable care to secure personal information, but no online system is 100% risk-free. Please keep your account and device details secure.</p>
                </div>

                <div>
                    <h2 class="font-display text-2xl text-choco mb-2">5. Contact</h2>
                    <p>If you have questions about this policy, contact us on <a href="tel:+923113138188" class="text-oat hover:text-oat-dark font-semibold">+92 311 3138188</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
