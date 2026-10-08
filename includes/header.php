<?php
// SEO Variables - har page set kar sakta hai
$page_title = $page_title ?? 'Pure Oats with Nuts | Premium Healthy Breakfast in Pakistan';
$page_desc  = $page_desc  ?? 'Premium quality rolled oats blended with roasted almonds, cashews and walnuts. 100% natural, no preservatives. Free delivery above Rs. 2000 across Pakistan.';
$page_keywords = $page_keywords ?? 'pure oats, oats with nuts, healthy breakfast, premium oats pakistan, oatmeal, rolled oats, almonds, cashews, walnuts';
$page_url = $page_url ?? (SITE_URL . $_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary SEO -->
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_desc); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($page_url); ?>">
    
    <!-- Open Graph (Facebook) -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_desc); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($page_url); ?>">
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:image" content="<?php echo SITE_URL; ?>/oats-product.jpg">
    
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_desc); ?>">
    <meta name="twitter:image" content="<?php echo SITE_URL; ?>/oats-product.jpg">
    
    <!-- Favicon (Emoji Fallback) -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🌾</text></svg>">
    
    <!-- Preconnect for Speed -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        cream: '#F5F0E6',
                        'cream-dark': '#E8DFD0',
                        oat: '#D4A574',
                        'oat-dark': '#B8895A',
                        brown: '#8B6B4A',
                        'brown-dark': '#6B4F35',
                        choco: '#3E2C1F',
                        'choco-light': '#5A4433',
                    },
                    fontFamily: {
                        display: ['"Playfair Display"', 'serif'],
                        body: ['"Poppins"', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 4s ease-in-out infinite',
                        'bounce-slow': 'bounce 2s infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Critical CSS - fast initial render */
        body { font-family: 'Poppins', sans-serif; }
        h1,h2,h3,h4 { font-family: 'Playfair Display', serif; }
        html { scroll-behavior: smooth; }
        * { -webkit-tap-highlight-color: transparent; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #F5F0E6; }
        ::-webkit-scrollbar-thumb { background: #D4A574; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #B8895A; }
    </style>
    
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": "Pure Oats with Nuts",
        "description": "Premium quality rolled oats blended with roasted almonds, cashews and walnuts",
        "brand": {
            "@type": "Brand",
            "name": "<?php echo SITE_NAME; ?>"
        },
        "offers": {
            "@type": "Offer",
            "priceCurrency": "PKR",
            "availability": "https://schema.org/InStock",
            "url": "<?php echo SITE_URL; ?>"
        },
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "reviewCount": "1247"
        }
    }
    </script>
</head>
<body class="font-body bg-cream text-choco antialiased overflow-x-hidden">

<!-- Announcement Bar -->
<div class="bg-choco text-cream text-center py-2 text-xs sm:text-sm">
    <i class="fas fa-truck text-oat mr-1.5"></i>
    Free Delivery Above Rs. <?php echo FREE_SHIPPING_MIN; ?> — 
    <span class="text-oat font-semibold">Cash on Delivery Available</span>
</div>

<!-- Header -->
<header class="bg-white/95 backdrop-blur-sm shadow-sm sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-3 sm:py-4 flex justify-between items-center">
        
        <!-- Logo -->
        <a href="index.php" class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
            <div class="w-10 h-10 sm:w-11 sm:h-11 bg-oat rounded-full flex items-center justify-center">
                <i class="fas fa-seedling text-white text-lg sm:text-xl"></i>
            </div>
            <div class="leading-none">
                <h1 class="font-display font-bold text-lg sm:text-2xl text-choco">Pure Oats</h1>
                <p class="text-[9px] sm:text-[10px] tracking-[2px] sm:tracking-[3px] text-oat uppercase">With Nuts</p>
            </div>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden lg:flex items-center gap-7">
            <a href="index.php#home" class="text-choco hover:text-oat font-medium text-sm transition-colors">Home</a>
            <a href="index.php#product" class="text-choco hover:text-oat font-medium text-sm transition-colors">Product</a>
            <a href="index.php#benefits" class="text-choco hover:text-oat font-medium text-sm transition-colors">Benefits</a>
            <a href="index.php#ingredients" class="text-choco hover:text-oat font-medium text-sm transition-colors">Ingredients</a>
            <a href="index.php#reviews" class="text-choco hover:text-oat font-medium text-sm transition-colors">Reviews</a>
            <a href="index.php#faq" class="text-choco hover:text-oat font-medium text-sm transition-colors">FAQ</a>
        </nav>

        <!-- Right Actions -->
        <div class="flex items-center gap-2 sm:gap-3">
            <a href="cart.php" class="relative bg-oat hover:bg-oat-dark text-white px-3 sm:px-5 py-2 sm:py-2.5 rounded-full flex items-center gap-1.5 sm:gap-2 font-semibold text-xs sm:text-sm transition-all hover:shadow-lg">
                <i class="fas fa-shopping-bag"></i>
                <span class="hidden sm:inline">Cart</span>
                <span class="bg-white text-oat rounded-full w-5 h-5 sm:w-6 sm:h-6 flex items-center justify-center text-[10px] sm:text-xs font-bold">
                    <?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?>
                </span>
            </a>
            <button onclick="toggleMenu()" aria-label="Menu" class="lg:hidden text-choco text-xl sm:text-2xl p-1">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-cream-dark px-6 py-4">
        <a href="index.php#home" class="block py-3 text-choco hover:text-oat font-medium border-b border-cream-dark">Home</a>
        <a href="index.php#product" class="block py-3 text-choco hover:text-oat font-medium border-b border-cream-dark">Product</a>
        <a href="index.php#benefits" class="block py-3 text-choco hover:text-oat font-medium border-b border-cream-dark">Benefits</a>
        <a href="index.php#ingredients" class="block py-3 text-choco hover:text-oat font-medium border-b border-cream-dark">Ingredients</a>
        <a href="index.php#reviews" class="block py-3 text-choco hover:text-oat font-medium border-b border-cream-dark">Reviews</a>
        <a href="index.php#faq" class="block py-3 text-choco hover:text-oat font-medium">FAQ</a>
    </div>
</header>

<script>
function toggleMenu() {
    document.getElementById('mobileMenu').classList.toggle('hidden');
}
</script>