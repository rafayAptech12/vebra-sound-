<?php
require_once __DIR__ . '/includes/store.php';

$allProducts = vebra_store_products();
$featuredProduct = $allProducts['deluxe-box'] ?? reset($allProducts);
$searchTerm = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$searchTerm = substr($searchTerm, 0, 100);
$sortOrder = isset($_GET['sort']) && is_string($_GET['sort']) ? $_GET['sort'] : 'featured';
if (!in_array($sortOrder, ['featured', 'price-asc', 'price-desc', 'name-asc'], true)) {
    $sortOrder = 'featured';
}

$products = array_filter($allProducts, static function ($product) use ($searchTerm) {
    if ($searchTerm === '') {
        return true;
    }

    return stripos(
        $product['title'] . ' ' . $product['desc'] . ' ' . $product['category'] . ' ' . $product['sku'],
        $searchTerm
    ) !== false;
});

if ($sortOrder !== 'featured') {
    uasort($products, static function ($first, $second) use ($sortOrder) {
        if ($sortOrder === 'price-asc') {
            return (float)$first['price'] <=> (float)$second['price'];
        }
        if ($sortOrder === 'price-desc') {
            return (float)$second['price'] <=> (float)$first['price'];
        }

        return strcasecmp($first['title'], $second['title']);
    });
}

$pageTitle = 'Shop — vebra';
$pageDesc = 'Explore VEBRA vinyl, apparel and accessories. Search the store and place a cash-on-delivery order.';
$bodyClass = 'theme-magenta';
$activePage = 'more';
$activeSub = 'shop';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="assets/images/hero-8.jpg" alt="" data-parallax-speed="0.12">
        </div>
        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page">Shop</li>
                </ul>
                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Store
                    <span class="page-caption__ghost" aria-hidden="true">Store</span>
                </h1>
                <p class="page-caption__text" data-reveal="up" data-delay="0.2">
                    Records, shirts and small objects made in short runs. Pressed, printed and packed
                    by the band — shipped worldwide from Brooklyn.
                </p>
                <div class="page-caption__meta" data-reveal="up" data-delay="0.3">
                    <div class="page-caption__stat"><strong data-count-to="<?= count($allProducts) ?>">0</strong><span>Products</span></div>
                    <div class="page-caption__stat"><strong data-count-to="180" data-count-suffix="g">0</strong><span>Vinyl weight</span></div>
                    <div class="page-caption__stat"><strong data-count-to="38" data-count-suffix="">0</strong><span>Countries shipped</span></div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($featuredProduct): ?>
    <section class="section section--bottom-0">
        <div class="container-vebra">
            <div class="store-banner" data-reveal="up">
                <div class="store-banner__body">
                    <span class="section-label"><?= e($featuredProduct['badge'] ?: 'Featured') ?></span>
                    <h2 class="store-banner__title" data-split="lines"><?= e($featuredProduct['title']) ?></h2>
                    <p class="section-head__text"><?= e($featuredProduct['desc']) ?></p>
                    <div class="hero__actions">
                        <a href="single-product.php?product=<?= rawurlencode($featuredProduct['id']) ?>" class="btn btn--solid" data-magnetic="0.3">
                            <span class="btn__label">Shop this product <i class="ri-arrow-right-up-line"></i></span>
                        </a>
                        <span class="pill pill--accent">$<?= e(number_format((float)$featuredProduct['price'], 2)) ?></span>
                    </div>
                </div>
                <div class="store-banner__media" data-reveal="mask" data-delay="0.15" data-cursor="View">
                    <img src="<?= e($featuredProduct['image']) ?>" alt="<?= e($featuredProduct['title']) ?>">
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="section" data-filter-scope>
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">All products</span>
                    <h2 class="section-head__title" data-split="lines">Merch &amp; <em>records</em></h2>
                </div>
                <div class="filter-list">
                    <button type="button" class="filter-list__btn is-active" data-filter="*" aria-pressed="true">All</button>
                    <button type="button" class="filter-list__btn" data-filter="vinyl" aria-pressed="false">Vinyl</button>
                    <button type="button" class="filter-list__btn" data-filter="apparel" aria-pressed="false">Apparel</button>
                    <button type="button" class="filter-list__btn" data-filter="accessories" aria-pressed="false">Accessories</button>
                </div>
            </div>

            <form method="get" action="shop-page.php" class="store-controls" role="search">
                <label>
                    Search products
                    <input type="search" name="q" maxlength="100" value="<?= e($searchTerm) ?>" placeholder="Name, category or SKU">
                </label>
                <label>
                    Sort by
                    <select name="sort">
                        <option value="featured" <?= $sortOrder === 'featured' ? 'selected' : '' ?>>Featured</option>
                        <option value="price-asc" <?= $sortOrder === 'price-asc' ? 'selected' : '' ?>>Price: low to high</option>
                        <option value="price-desc" <?= $sortOrder === 'price-desc' ? 'selected' : '' ?>>Price: high to low</option>
                        <option value="name-asc" <?= $sortOrder === 'name-asc' ? 'selected' : '' ?>>Name: A to Z</option>
                    </select>
                </label>
                <button type="submit" class="btn btn--solid btn--sm"><span class="btn__label">Apply</span></button>
                <?php if ($searchTerm !== '' || $sortOrder !== 'featured'): ?>
                    <a href="shop-page.php" class="link-arrow">Clear search and sort</a>
                <?php endif; ?>
            </form>

            <?php if ($products): ?>
                <p data-filter-count role="status" aria-live="polite"><?= count($products) ?> product<?= count($products) === 1 ? '' : 's' ?> found</p>
                <div class="filter-grid products-grid" data-filter-grid data-reveal-group data-stagger="0.08" aria-label="Store products">
                    <?php foreach ($products as $product): ?>
                        <?php $productUrl = 'single-product.php?product=' . rawurlencode($product['id']); ?>
                        <div class="filter-grid__item" data-category="<?= e($product['category_key']) ?>">
                            <article class="product-card">
                                <div class="product-card__media" data-hover-scope data-cursor="View">
                                    <a href="<?= e($productUrl) ?>" aria-label="<?= e($product['title']) ?>">
                                        <img src="<?= e($product['image1']) ?>" data-hover-src="<?= e($product['image2']) ?>" alt="<?= e($product['title']) ?>">
                                    </a>
                                    <?php if ($product['badge']): ?>
                                        <span class="pill <?= e($product['badge_style']) ?> product-card__badge"><?= e($product['badge']) ?></span>
                                    <?php endif; ?>
                                    <div class="product-card__action">
                                        <a href="<?= e($productUrl) ?>" class="btn btn--solid btn--sm"><span class="btn__label">View Product</span></a>
                                        <button type="button" class="btn-circle" aria-label="Add <?= e($product['title']) ?> to wishlist"><i class="ri-heart-line"></i></button>
                                    </div>
                                </div>
                                <div class="product-card__body">
                                    <div>
                                        <h3 class="product-card__title"><a href="<?= e($productUrl) ?>"><?= e($product['title']) ?></a></h3>
                                        <span class="product-card__cat"><?= e($product['category']) ?></span>
                                        <div class="product-card__rating" aria-label="Rated <?= (int)$product['rating'] ?> out of 5" role="img">
                                            <?php for ($star = 1; $star <= 5; $star++): ?>
                                                <i class="<?= $star <= $product['rating'] ? 'ri-star-fill' : 'ri-star-line' ?>"></i>
                                            <?php endfor; ?>
                                            <span><?= (int)$product['reviews_count'] ?> reviews</span>
                                        </div>
                                    </div>
                                    <span class="price">
                                        $<?= e(number_format((float)$product['price'], 2)) ?>
                                        <?php if ($product['old_price'] !== null): ?>
                                            <del>$<?= e(number_format((float)$product['old_price'], 2)) ?></del>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                    <p class="filter-empty" role="status">No products match your search or category. Try another search.</p>
                </div>
            <?php else: ?>
                <p role="status">No products match your search. Try another term.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
