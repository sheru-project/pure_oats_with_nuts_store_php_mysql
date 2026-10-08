<!-- ==================== FOOTER ==================== -->
<footer class="bg-choco text-cream pt-16 sm:pt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 sm:gap-12 mb-12 sm:mb-16">
            
            <!-- About -->
            <div>
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 bg-oat rounded-full flex items-center justify-center">
                        <i class="fas fa-seedling text-white text-xl"></i>
                    </div>
                    <div class="leading-none">
                        <h3 class="font-display font-bold text-xl text-cream">Pure Oats</h3>
                        <p class="text-[10px] tracking-[3px] text-oat uppercase">With Nuts</p>
                    </div>
                </div>
                <p class="text-cream/60 text-sm mb-5 leading-relaxed">
                    Premium oats and nuts blend crafted for people who care about their health.
                </p>
                <div class="flex gap-3">
                    <a href="https://www.facebook.com/share/1DPGqe2qVt/" target="_blank" rel="noopener" aria-label="Facebook" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-oat transition-all hover:-translate-y-1"><i class="fab fa-facebook-f text-sm"></i></a>
                    <a href="https://www.instagram.com/pureoatswithnuts?utm_source=qr&stkn=NDhkdGdhajM5cWow" target="_blank" rel="noopener" aria-label="Instagram" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-oat transition-all hover:-translate-y-1"><i class="fab fa-instagram text-sm"></i></a>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" rel="noopener" aria-label="WhatsApp" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-oat transition-all hover:-translate-y-1"><i class="fab fa-whatsapp text-sm"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-bold text-cream text-lg mb-5">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="index.php" class="text-cream/60 hover:text-oat text-sm transition-colors">Home</a></li>
                    <li><a href="index.php#product" class="text-cream/60 hover:text-oat text-sm transition-colors">Product</a></li>
                    <li><a href="index.php#benefits" class="text-cream/60 hover:text-oat text-sm transition-colors">Benefits</a></li>
                    <li><a href="index.php#ingredients" class="text-cream/60 hover:text-oat text-sm transition-colors">Ingredients</a></li>
                    <li><a href="index.php#reviews" class="text-cream/60 hover:text-oat text-sm transition-colors">Reviews</a></li>
                </ul>
            </div>

            <!-- Support -->
            <div>
                <h4 class="font-bold text-cream text-lg mb-5">Support</h4>
                <ul class="space-y-3">
                    <li><a href="index.php#faq" class="text-cream/60 hover:text-oat text-sm transition-colors">FAQ</a></li>
                    <li><a href="shipping_policy.php" class="text-cream/60 hover:text-oat text-sm transition-colors">Shipping Policy</a></li>
                    <li><a href="return_policy.php" class="text-cream/60 hover:text-oat text-sm transition-colors">Return Policy</a></li>
                    <li><a href="privacy_policy.php" class="text-cream/60 hover:text-oat text-sm transition-colors">Privacy Policy</a></li>
                    <li><a href="terms_conditions.php" class="text-cream/60 hover:text-oat text-sm transition-colors">Terms & Conditions</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-bold text-cream text-lg mb-5">Get In Touch</h4>
                <ul class="space-y-3 mb-5">
                    <li class="flex items-center gap-3 text-cream/60 text-sm">
                        <i class="fas fa-phone text-oat"></i>
                        <a href="tel:+923113138188" class="hover:text-oat">+92 311 3138188</a>
                    </li>
                    <li class="flex items-start gap-3 text-cream/60 text-sm">
                        <i class="fas fa-map-marker-alt text-oat mt-1"></i>
                        <span>Hyderabad, Sindh, Pakistan</span>
                    </li>
                </ul>
                <div class="flex gap-2 flex-wrap items-center">
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-2">
                        <i class="fas fa-wallet text-oat"></i> Easypaisa
                    </span>
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-2">
                        <i class="fas fa-wallet text-oat"></i> JazzCash
                    </span>
                    <span class="bg-white/10 px-3 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center gap-2">
                        <i class="fas fa-truck text-oat"></i> COD
                    </span>
                </div>
            </div>
        </div>

        <div class="border-t border-white/10 py-6 text-center">
            <p class="text-cream/40 text-xs sm:text-sm">
                &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. Made with <i class="fas fa-heart text-red-500"></i> in Pakistan.
            </p>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button onclick="window.scrollTo({top:0, behavior:'smooth'})" id="backTop" aria-label="Back to top"
        class="hidden fixed bottom-6 right-6 w-12 h-12 bg-oat hover:bg-oat-dark text-white rounded-full shadow-xl transition-all z-40 hover:-translate-y-1">
    <i class="fas fa-arrow-up"></i>
</button>

<script>
// Back to top show/hide
window.addEventListener('scroll', () => {
    const btn = document.getElementById('backTop');
    if (window.scrollY > 500) btn.classList.remove('hidden');
    else btn.classList.add('hidden');
});
</script>

</body>
</html>