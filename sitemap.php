<?php
$pageTitle = 'Sitemap — VEBRA';
$pageDesc = 'Explore a structured overview of every page on the VEBRA website.';
$bodyClass = 'theme-magenta';
$activePage = 'sitemap';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="assets/images/contact-bg.jpg" alt="" data-parallax-speed="0.12">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page">Sitemap</li>
                </ul>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Site map
                    <span class="page-caption__ghost" aria-hidden="true">Sitemap</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.2">
                    Browse the complete VEBRA site structure, from music and artist pages to events,
                    news, shop and contact points.
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-vebra">
            <div class="row g-5">
                <div class="col-lg-6">
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 28px 24px;">
                        <h2 style="font-size: 1.7rem; margin-bottom: 18px; color: #fff; font-family: 'Unbounded',sans-serif;">Main pages</h2>
                        <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px;">
                            <li><a href="index.php" class="link-arrow">Home <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="all-music.php" class="link-arrow">All Music <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="discography.php" class="link-arrow">Discography <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="albums.php" class="link-arrow">Albums <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="singles.php" class="link-arrow">Singles <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="artist.php" class="link-arrow">Artist Overview <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="artists.php" class="link-arrow">Artists <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="single-artist.php" class="link-arrow">Featured Artist <i class="ri-arrow-right-up-line"></i></a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 28px 24px;">
                        <h2 style="font-size: 1.7rem; margin-bottom: 18px; color: #fff; font-family: 'Unbounded',sans-serif;">Media & stories</h2>
                        <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px;">
                            <li><a href="video-gallery.php" class="link-arrow">Video Gallery <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="single-video.php" class="link-arrow">Featured Video <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="events.php" class="link-arrow">Events <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="single-event.php" class="link-arrow">Single Event <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="news-page.php" class="link-arrow">News <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="single-news.php" class="link-arrow">Single News <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="gallery.php" class="link-arrow">Photo Gallery <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="typography.php" class="link-arrow">Typography <i class="ri-arrow-right-up-line"></i></a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 28px 24px;">
                        <h2 style="font-size: 1.7rem; margin-bottom: 18px; color: #fff; font-family: 'Unbounded',sans-serif;">Shop & account</h2>
                        <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px;">
                            <li><a href="shop-page.php" class="link-arrow">Shop / Vinyl <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="single-product.php" class="link-arrow">Single Product <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="cart.php" class="link-arrow">Shopping Cart <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="checkout.php" class="link-arrow">Checkout <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="order-lookup.php" class="link-arrow">Track an Order <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="store-policy.php?policy=shipping" class="link-arrow">Shipping Policy <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="store-policy.php?policy=returns" class="link-arrow">Returns Policy <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="store-policy.php?policy=privacy" class="link-arrow">Privacy Policy <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="store-policy.php?policy=terms" class="link-arrow">Terms of Sale <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="search.php" class="link-arrow">Search <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="contact.php" class="link-arrow">Contact <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="auth/login.php" class="link-arrow">Login <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="auth/register.php" class="link-arrow">Sign Up <i class="ri-arrow-right-up-line"></i></a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 28px 24px;">
                        <h2 style="font-size: 1.7rem; margin-bottom: 18px; color: #fff; font-family: 'Unbounded',sans-serif;">Quick jump</h2>
                        <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 12px;">
                            <li><a href="single-album.php" class="link-arrow">Featured Album <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="single-product.php" class="link-arrow">Featured Product <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="song-detail.php" class="link-arrow">Song Detail <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="track.php" class="link-arrow">Track Page <i class="ri-arrow-right-up-line"></i></a></li>
                            <li><a href="video.php" class="link-arrow">Video Page <i class="ri-arrow-right-up-line"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
