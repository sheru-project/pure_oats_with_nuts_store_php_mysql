<?php
include 'config/db.php';

// Product fetch
$result = $conn->query("SELECT * FROM products LIMIT 1");
$product = $result && $result->num_rows > 0 ? $result->fetch_assoc() : null;

if(!$product) {
    die("Product not found. Please add a product in database.");
}

// Add to cart
if(isset($_POST['add_to_cart'])) {
    $id = (int)$product['id'];
    $qty = max(1, (int)$_POST['quantity']);
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;
    header("Location: cart.php");
    exit();
}

// SEO Variables
$page_title = 'Pure Oats with Nuts | Premium Healthy Breakfast in Pakistan';
$page_desc = 'Buy premium rolled oats with roasted almonds, cashews & walnuts. 100% natural, no preservatives. Free delivery above Rs. 2000. Cash on Delivery available across Pakistan.';
$page_keywords = 'pure oats, oats with nuts, buy oats pakistan, healthy breakfast, oatmeal, premium oats lahore karachi';

include 'includes/header.php';
?>

<!-- ==================== HERO ==================== -->
<section id="home" class="relative min-h-[90vh] flex items-center justify-center overflow-hidden py-16 sm:py-20">
    
    <div class="absolute inset-0 z-0">
        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
             class="w-full h-full object-cover" fetchpriority="high" loading="eager">
        <div class="absolute inset-0 bg-gradient-to-r from-choco/95 via-choco/80 to-choco/60"></div>
    </div>

    <div class="relative z-20 text-center max-w-4xl mx-auto px-4 sm:px-6">
        
        <div class="inline-flex items-center gap-2 bg-oat/90 backdrop-blur-sm text-white px-4 sm:px-5 py-2 rounded-full text-xs sm:text-sm font-semibold mb-5 sm:mb-6">
            <i class="fas fa-award"></i>
            Pakistan's #1 Premium Oats Brand
        </div>

        <h1 class="font-display text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black text-cream leading-tight mb-5 sm:mb-6">
            Fuel Your Day <br>
            With <span class="text-oat italic">Pure Oats</span> & Nuts
        </h1>

        <p class="text-cream/80 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-7 sm:mb-8 px-2">
            Handpicked premium rolled oats blended with roasted almonds, cashews, and walnuts. 
            A wholesome breakfast crafted for people who refuse to compromise.
        </p>

        <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-8 sm:mb-10">
            <span class="bg-white/10 backdrop-blur-sm border border-cream/20 text-cream px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm">
                <i class="fas fa-leaf text-oat mr-1.5"></i>100% Natural
            </span>
            <span class="bg-white/10 backdrop-blur-sm border border-cream/20 text-cream px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm">
                <i class="fas fa-check-circle text-oat mr-1.5"></i>No Preservatives
            </span>
            <span class="bg-white/10 backdrop-blur-sm border border-cream/20 text-cream px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm">
                <i class="fas fa-star text-oat mr-1.5"></i>Premium Nuts
            </span>
        </div>

        <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
            <a href="#product" class="bg-oat hover:bg-oat-dark text-white px-6 sm:px-8 py-3.5 sm:py-4 rounded-full font-bold text-sm sm:text-lg transition-all hover:shadow-2xl hover:-translate-y-1 inline-flex items-center gap-2">
                <i class="fas fa-shopping-bag"></i> Shop Now
            </a>
            <a href="#benefits" class="bg-transparent border-2 border-cream/50 hover:border-oat hover:bg-oat/10 text-cream px-6 sm:px-8 py-3.5 sm:py-4 rounded-full font-bold text-sm sm:text-lg transition-all inline-flex items-center gap-2">
                <i class="fas fa-info-circle"></i> Learn More
            </a>
        </div>
    </div>
</section>

<!-- ==================== TRUST BAR ==================== -->
<section class="bg-white py-8 sm:py-10 -mt-12 relative z-30 mx-4 sm:mx-8 lg:mx-12 rounded-3xl shadow-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
            <div class="w-12 h-12 bg-cream rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-truck-fast text-oat text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-choco text-xs sm:text-sm">Fast Delivery</h4>
                <p class="text-[10px] sm:text-xs text-brown">All over Pakistan</p>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
            <div class="w-12 h-12 bg-cream rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-money-bill-wave text-oat text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-choco text-xs sm:text-sm">Cash on Delivery</h4>
                <p class="text-[10px] sm:text-xs text-brown">Pay on receive</p>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
            <div class="w-12 h-12 bg-cream rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-undo-alt text-oat text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-choco text-xs sm:text-sm">Easy Returns</h4>
                <p class="text-[10px] sm:text-xs text-brown">7 days policy</p>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-cream transition-colors">
            <div class="w-12 h-12 bg-cream rounded-full flex items-center justify-center flex-shrink-0">
                <i class="fas fa-headset text-oat text-lg"></i>
            </div>
            <div>
                <h4 class="font-bold text-choco text-xs sm:text-sm">24/7 Support</h4>
                <p class="text-[10px] sm:text-xs text-brown">Always here</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== PRODUCT ==================== -->
<section id="product" class="py-16 sm:py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        
        <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block bg-oat/20 text-oat-dark px-4 sm:px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                The Product
            </span>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-choco mb-4">
                Meet Your New <span class="text-oat italic">Breakfast Hero</span>
            </h2>
            <p class="text-brown max-w-2xl mx-auto text-sm sm:text-base">
                Every pack is crafted with love, care, and premium ingredients.
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-8 sm:gap-16 items-start">
            
            <div>
                <div class="relative rounded-3xl overflow-hidden shadow-2xl group">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="w-full transition-transform duration-700 group-hover:scale-105" loading="lazy">
                    <span class="absolute top-4 sm:top-5 left-4 sm:left-5 bg-oat text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                        <i class="fas fa-award mr-1"></i> Best Seller
                    </span>
                    <span class="absolute top-4 sm:top-5 right-4 sm:right-5 bg-red-500 text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                        -20% OFF
                    </span>
                </div>
            </div>

            <div>
                <div class="flex items-center flex-wrap gap-3 sm:gap-4 mb-5">
                    <div class="flex items-center gap-1 text-oat text-sm">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <span class="text-brown text-xs sm:text-sm">4.9 (1,247 reviews)</span>
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                        <i class="fas fa-check-circle mr-1"></i> In Stock
                    </span>
                </div>

                <h3 class="font-display text-3xl sm:text-4xl font-bold text-choco mb-4">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h3>
                <p class="text-brown mb-6 sm:mb-8 leading-relaxed text-sm sm:text-base">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <div class="bg-white rounded-2xl p-5 sm:p-6 mb-6 sm:mb-8 border-l-4 border-oat shadow-sm">
                    <div class="flex items-center gap-3 sm:gap-4 flex-wrap">
                        <span class="text-3xl sm:text-4xl font-black text-oat">Rs. <?php echo number_format($product['price']); ?></span>
                        <span class="text-lg sm:text-xl text-brown line-through">Rs. 1,500</span>
                        <span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold">Save 20%</span>
                    </div>
                </div>

                <form method="POST" action="" class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-6 sm:mb-8">
                    <div class="flex items-center bg-white border-2 border-cream-dark rounded-full overflow-hidden w-fit">
                        <button type="button" onclick="decreaseQty()" aria-label="Decrease" class="px-4 sm:px-5 py-3 text-oat hover:bg-cream font-bold text-lg">−</button>
                        <input type="number" name="quantity" id="qty" value="1" min="1" 
                               class="w-12 sm:w-14 text-center border-none outline-none font-bold text-choco bg-transparent" 
                               aria-label="Quantity">
                        <button type="button" onclick="increaseQty()" aria-label="Increase" class="px-4 sm:px-5 py-3 text-oat hover:bg-cream font-bold text-lg">+</button>
                    </div>
                    <button type="submit" name="add_to_cart" 
                            class="flex-1 bg-oat hover:bg-oat-dark text-white px-6 sm:px-8 py-4 rounded-full font-bold text-sm sm:text-base transition-all hover:shadow-xl hover:-translate-y-1 inline-flex items-center justify-center gap-2">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                </form>

                <div class="space-y-3 pt-6 border-t border-cream-dark">
                    <p class="flex items-center gap-3 text-xs sm:text-sm text-brown"><i class="fas fa-check text-oat"></i> Free shipping on orders above Rs. 2000</p>
                    <p class="flex items-center gap-3 text-xs sm:text-sm text-brown"><i class="fas fa-check text-oat"></i> Cash on Delivery available</p>
                    <p class="flex items-center gap-3 text-xs sm:text-sm text-brown"><i class="fas fa-check text-oat"></i> Easypaisa / JazzCash accepted</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== BENEFITS ==================== -->
<section id="benefits" class="relative py-16 sm:py-24 bg-choco overflow-hidden">
    
    <div class="absolute inset-0 opacity-10">
        <img src="https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=1600" class="w-full h-full object-cover" loading="lazy" alt="">
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block bg-oat/20 text-oat px-4 sm:px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Why Choose Us
            </span>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-cream mb-4">
                Power-Packed <span class="text-oat italic">Health Benefits</span>
            </h2>
            <p class="text-cream/60 max-w-2xl mx-auto text-sm sm:text-base">Every spoon delivers nutrition your body craves</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-8">
            <?php 
            $benefits = [
                ['fa-bolt', 'Long-Lasting Energy', 'Slow-release carbs keep you energized for hours.'],
                ['fa-heartbeat', 'Heart Healthy', 'Rich in omega-3 fatty acids from premium nuts.'],
                ['fa-weight', 'Supports Weight Loss', 'High fibre keeps you full and reduces cravings.'],
                ['fa-dumbbell', 'Muscle Recovery', 'Plant-based protein helps rebuild muscles.'],
                ['fa-brain', 'Brain Boost', 'Walnuts and almonds enhance memory and focus.'],
                ['fa-shield-virus', 'Immunity Support', 'Antioxidants strengthen your defense system.'],
            ];
            foreach($benefits as $b): 
            ?>
            <div class="bg-white/5 backdrop-blur-sm border border-cream/10 rounded-2xl p-6 sm:p-8 hover:bg-white/10 hover:-translate-y-2 hover:border-oat/50 transition-all duration-300">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-oat rounded-2xl flex items-center justify-center text-white text-xl sm:text-2xl mb-5 sm:mb-6 shadow-lg">
                    <i class="fas <?php echo $b[0]; ?>"></i>
                </div>
                <h3 class="font-display text-lg sm:text-xl font-bold text-cream mb-3"><?php echo $b[1]; ?></h3>
                <p class="text-cream/60 text-xs sm:text-sm leading-relaxed"><?php echo $b[2]; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== INGREDIENTS ==================== -->
<section id="ingredients" class="py-16 sm:py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="grid lg:grid-cols-2 gap-10 sm:gap-16 items-center">
            
            <div>
                <span class="inline-block bg-oat/20 text-oat-dark px-4 sm:px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                    Premium Ingredients
                </span>
                <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-choco mb-5 sm:mb-6">
                    Crafted With <span class="text-oat italic">Nature's Best</span>
                </h2>
                <p class="text-brown mb-6 sm:mb-8 leading-relaxed text-sm sm:text-base">
                    We source only the finest ingredients from trusted farms. Every batch is quality-checked for freshness and purity.
                </p>

                <div class="space-y-3 sm:space-y-4">
                    <?php 
                    $ingredients = [
                        ['fa-seedling', 'Rolled Oats', '100% whole grain, high in fibre'],
                        ['fa-circle', 'Roasted Almonds', 'Rich in Vitamin E & protein'],
                        ['fa-circle', 'Premium Cashews', 'Creamy texture, healthy fats'],
                        ['fa-circle', 'Walnuts', 'Omega-3 for brain health'],
                    ];
                    foreach($ingredients as $ing): 
                    ?>
                    <div class="flex items-center gap-4 sm:gap-5 bg-white p-4 sm:p-5 rounded-2xl border-l-4 border-oat shadow-sm hover:shadow-lg hover:translate-x-2 transition-all duration-300">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-oat rounded-xl flex items-center justify-center text-white flex-shrink-0">
                            <i class="fas <?php echo $ing[0]; ?> text-sm sm:text-base"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-choco text-sm sm:text-base"><?php echo $ing[1]; ?></h4>
                            <p class="text-xs sm:text-sm text-brown"><?php echo $ing[2]; ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="relative">
                <img src="https://images.unsplash.com/photo-1517093602195-b40af9688b46?w=600" 
                     class="rounded-3xl shadow-2xl w-full" loading="lazy" alt="Fresh Oats Ingredients">
            </div>
        </div>
    </div>
</section>

<!-- ==================== NUTRITION ==================== -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block bg-oat/20 text-oat-dark px-4 sm:px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Nutrition Facts
            </span>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-choco mb-4">
                What's Inside <span class="text-oat italic">Every Serving</span>
            </h2>
            <p class="text-brown text-sm sm:text-base">Per 40g serving (approx. 1 cup)</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 sm:gap-8">
            <?php 
            $nutrition = [
                ['160', 'kcal', 'Calories', 'Clean energy source'],
                ['6', 'g', 'Protein', 'Muscle building'],
                ['5', 'g', 'Fibre', 'Digestive health'],
                ['9', 'g', 'Healthy Fats', 'Heart friendly'],
            ];
            foreach($nutrition as $n): 
            ?>
            <div class="text-center group">
                <div class="w-32 h-32 sm:w-40 sm:h-40 mx-auto rounded-full bg-cream border-4 border-oat flex flex-col items-center justify-center transition-all duration-300 group-hover:bg-oat group-hover:scale-105 shadow-lg">
                    <div class="text-3xl sm:text-4xl font-black text-oat group-hover:text-white transition-colors counter font-body" data-target="<?php echo $n[0]; ?>">0</div>
                    <div class="text-xs sm:text-sm text-brown group-hover:text-white/90 font-semibold transition-colors"><?php echo $n[1]; ?></div>
                </div>
                <h4 class="font-display text-base sm:text-lg font-bold text-choco mt-4 sm:mt-5 mb-1"><?php echo $n[2]; ?></h4>
                <p class="text-xs sm:text-sm text-brown"><?php echo $n[3]; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== HOW TO USE ==================== -->
<section class="py-16 sm:py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block bg-oat/20 text-oat-dark px-4 sm:px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Simple Steps
            </span>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-choco mb-4">
                Ready in <span class="text-oat italic">3 Minutes</span>
            </h2>
            <p class="text-brown text-sm sm:text-base">No cooking skills needed</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <?php 
            $steps = [
                ['fa-mug-hot', 'Add Oats', 'Take 1/2 cup of Pure Oats in a bowl'],
                ['fa-tint', 'Pour Milk', 'Add 1 cup milk or water as per taste'],
                ['fa-fire', 'Cook 3 Min', 'Cook on low heat until creamy'],
                ['fa-smile', 'Enjoy!', 'Add honey or fruits & enjoy'],
            ];
            foreach($steps as $i => $s): 
            ?>
            <div class="bg-white rounded-2xl p-6 sm:p-8 text-center relative hover:-translate-y-2 hover:shadow-xl transition-all duration-300">
                <span class="absolute -top-5 right-4 sm:right-5 font-display text-5xl sm:text-7xl font-black text-oat/20">0<?php echo $i+1; ?></span>
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-oat rounded-full flex items-center justify-center text-white text-2xl sm:text-3xl mx-auto mb-5 sm:mb-6 shadow-lg relative z-10">
                    <i class="fas <?php echo $s[0]; ?>"></i>
                </div>
                <h3 class="font-display text-lg sm:text-xl font-bold text-choco mb-2 sm:mb-3"><?php echo $s[1]; ?></h3>
                <p class="text-xs sm:text-sm text-brown"><?php echo $s[2]; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== TESTIMONIALS ==================== -->
<section id="reviews" class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block bg-oat/20 text-oat-dark px-4 sm:px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Customer Love
            </span>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-choco mb-4">
                What Our <span class="text-oat italic">Customers Say</span>
            </h2>
            <p class="text-brown text-sm sm:text-base">Real reviews from real people</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <?php 
            $reviews = [
                ['AK', 'Ahmed Khan', 'Lahore', 'Best oats I have ever tried! The nuts are fresh and crunchy. My whole family loves it.'],
                ['SF', 'Sara Fatima', 'Karachi', 'Perfect breakfast for my gym routine. Keeps me full till lunch. Highly recommended!'],
                ['MH', 'Muhammad Hassan', 'Islamabad', 'Packaging is premium, taste is amazing. Will definitely order again. Fast delivery too!'],
            ];
            foreach($reviews as $r): 
            ?>
            <div class="bg-cream rounded-2xl p-6 sm:p-8 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 relative">
                <i class="fas fa-quote-left absolute top-5 right-6 text-oat/20 text-4xl sm:text-5xl"></i>
                <div class="flex gap-1 text-oat mb-4 text-sm">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-brown italic mb-5 sm:mb-6 relative z-10 text-sm sm:text-base">"<?php echo $r[3]; ?>"</p>
                <div class="flex items-center gap-4 pt-4 border-t border-cream-dark">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 bg-oat rounded-full flex items-center justify-center text-white font-bold text-sm">
                        <?php echo $r[0]; ?>
                    </div>
                    <div>
                        <h4 class="font-bold text-choco text-sm"><?php echo $r[1]; ?></h4>
                        <span class="text-xs text-brown"><?php echo $r[2]; ?>, Pakistan</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== FAQ ==================== -->
<section id="faq" class="py-16 sm:py-24 bg-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block bg-oat/20 text-oat-dark px-4 sm:px-5 py-2 rounded-full text-xs font-bold uppercase tracking-[2px] mb-4">
                Questions
            </span>
            <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-choco mb-4">
                Frequently Asked <span class="text-oat italic">Questions</span>
            </h2>
        </div>

        <div class="max-w-3xl mx-auto space-y-3 sm:space-y-4">
            <?php 
            $faqs = [
                ['How long does delivery take?', 'Delivery usually takes 2-4 working days within Pakistan. For major cities it is 1-2 days.'],
                ['Is Cash on Delivery available?', 'Yes! We offer COD all over Pakistan. You can also pay via Easypaisa or JazzCash.'],
                ['How should I store the oats?', 'Store in a cool, dry place. Once opened, keep in an airtight container for maximum freshness.'],
                ['Do you offer bulk orders?', 'Yes, for bulk orders (10+ packs) please contact us on WhatsApp for special pricing.'],
                ['Are there any preservatives?', 'Absolutely not! Our oats are 100% natural with zero preservatives, artificial colors, or flavors.'],
            ];
            foreach($faqs as $f): 
            ?>
            <div class="faq-item bg-white rounded-2xl overflow-hidden shadow-sm transition-all">
                <div class="faq-question flex justify-between items-center p-5 sm:p-6 cursor-pointer hover:bg-oat/10 transition-colors" onclick="toggleFaq(this)">
                    <h4 class="font-bold text-choco text-sm sm:text-base pr-4"><?php echo $f[0]; ?></h4>
                    <i class="fas fa-plus text-oat transition-transform flex-shrink-0"></i>
                </div>
                <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                    <p class="px-5 sm:px-6 pb-5 sm:pb-6 text-brown text-sm"><?php echo $f[1]; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== CTA ==================== -->
<section class="py-16 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">
        <div class="relative rounded-3xl overflow-hidden p-10 sm:p-16 text-center">
            <div class="absolute inset-0 bg-choco"></div>
            <img src="https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=1200" 
                 class="absolute inset-0 w-full h-full object-cover opacity-10" loading="lazy" alt="">
            
            <div class="relative z-10 max-w-2xl mx-auto">
                <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold text-cream mb-4">
                    Start Your Healthy Journey <span class="text-oat italic">Today</span>
                </h2>
                <p class="text-cream/70 mb-8 text-sm sm:text-base">Join 15,000+ happy customers who made the switch</p>
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                    <a href="cart.php" class="bg-oat hover:bg-oat-dark text-white px-8 sm:px-10 py-4 sm:py-5 rounded-full font-bold text-base sm:text-lg transition-all hover:shadow-2xl hover:-translate-y-1 inline-flex items-center justify-center gap-3">
                        <i class="fas fa-shopping-bag"></i> Order Now
                    </a>
                    <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" target="_blank" rel="noopener"
                       class="bg-[#25D366] hover:bg-[#1da851] text-white px-8 sm:px-10 py-4 sm:py-5 rounded-full font-bold text-base sm:text-lg transition-all hover:shadow-2xl hover:-translate-y-1 inline-flex items-center justify-center gap-3">
                        <i class="fab fa-whatsapp"></i> Chat on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== SCRIPTS ==================== -->
<script>
    // Quantity
    function increaseQty() {
        const qty = document.getElementById('qty');
        qty.value = parseInt(qty.value) + 1;
    }
    function decreaseQty() {
        const qty = document.getElementById('qty');
        if(parseInt(qty.value) > 1) qty.value = parseInt(qty.value) - 1;
    }

    // FAQ
    function toggleFaq(el) {
        const item = el.parentElement;
        const answer = item.querySelector('.faq-answer');
        const icon = el.querySelector('i');
        
        document.querySelectorAll('.faq-item').forEach(fi => {
            if(fi !== item) {
                fi.querySelector('.faq-answer').style.maxHeight = null;
                fi.querySelector('i').style.transform = 'rotate(0deg)';
            }
        });

        if(answer.style.maxHeight) {
            answer.style.maxHeight = null;
            icon.style.transform = 'rotate(0deg)';
        } else {
            answer.style.maxHeight = answer.scrollHeight + 'px';
            icon.style.transform = 'rotate(45deg)';
        }
    }

    // Counter animation
    const counters = document.querySelectorAll('.counter');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                const counter = entry.target;
                const target = +counter.getAttribute('data-target');
                let count = 0;
                const inc = target / 100;
                const updateCount = () => {
                    if(count < target) {
                        count += inc;
                        counter.innerText = Math.ceil(count);
                        requestAnimationFrame(updateCount);
                    } else {
                        counter.innerText = target + "+";
                    }
                };
                updateCount();
                observer.unobserve(counter);
            }
        });
    }, { threshold: 0.5 });
    counters.forEach(c => observer.observe(c));
</script>

<?php include 'includes/footer.php'; ?>