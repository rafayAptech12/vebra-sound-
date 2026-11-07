<?php
require_once __DIR__ . '/includes/store.php';

$productParam = isset($_GET['product']) && is_string($_GET['product'])
    ? trim($_GET['product'])
    : 'nightform-vinyl';
$products = vebra_store_products();
if (!isset($products[$productParam])) {
    $productParam = 'nightform-vinyl';
}
$curProduct = $products[$productParam] ?? null;
if ($curProduct === null) {
    http_response_code(404);
    exit('Product not found.');
}

$cartAdded = isset($_GET['added']) && $_GET['added'] === '1';
$cartError = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: auth/login.php?redirect=' . urlencode('../single-product.php?product=' . $productParam) . '&msg=login_required');
        exit;
    }

    $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
    $size = isset($_POST['size']) && is_string($_POST['size']) ? trim($_POST['size']) : '';

    if (!vebra_store_valid_csrf($_POST['csrf_token'] ?? null)) {
        $cartError = 'Your session expired. Please reload this page and try again.';
    } elseif ($quantity === false || $quantity < 1 || $quantity > 20) {
        $cartError = 'Choose a quantity between 1 and 20.';
    } elseif (($curProduct['sizes'] && !in_array($size, $curProduct['sizes'], true))
        || (!$curProduct['sizes'] && $size !== '')) {
        $cartError = 'Choose a valid product option.';
    } else {
        $cart = vebra_store_cart();
        $key = vebra_store_cart_key($productParam, $size);
        $currentQuantity = (int)($cart[$key]['quantity'] ?? 0);
        if ($currentQuantity + $quantity > 20) {
            $cartError = 'You can order up to 20 of each item.';
        } else {
            vebra_store_cart_add($productParam, $quantity, $size);
            header('Location: single-product.php?product=' . rawurlencode($productParam) . '&added=1');
            exit;
        }
    }
}

$relatedProducts = array_filter($products, static function ($product) use ($productParam) {
    return $product['id'] !== $productParam;
});
$relatedProducts = array_slice($relatedProducts, 0, 4);

$pageTitle = $curProduct['title'] . ' — vebra Store';
$pageDesc = $curProduct['desc'];
$ogImage = $curProduct['image1'];
$productSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $curProduct['title'],
    'description' => $curProduct['desc'],
    'sku' => $curProduct['sku'],
    'category' => $curProduct['category'],
    'offers' => [
        '@type' => 'Offer',
        'priceCurrency' => 'USD',
        'price' => (float)$curProduct['price'],
    ],
];
$extraHead = '<script type="application/ld+json">'
    . json_encode($productSchema, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT)
    . '</script>';
$bodyClass = 'theme-magenta';
$activePage = 'more';
$activeSub = 'single-product';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">
    <section class="section section--glow product-section">
        <div class="container-vebra">
            <ul class="breadcrumb-vebra product-section__crumbs">
                <li><a href="index.php">Home</a></li>
                <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                <li><a href="shop-page.php">Shop</a></li>
                <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                <li aria-current="page"><?= e($curProduct['title']) ?></li>
            </ul>

            <?php if ($cartAdded): ?>
                <div style="background: rgba(var(--accent-rgb), 0.15); border: 1px solid var(--accent); padding: 16px 20px; border-radius: 6px; margin-bottom: 30px; color: #fff; display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <i class="ri-shopping-bag-3-fill" style="color:var(--accent); margin-right:8px; font-size:1.2rem;"></i>
                        <strong>Item added to your bag!</strong> <?= e($curProduct['title']) ?> ($<?= e(number_format((float)$curProduct['price'], 2)) ?>)
                    </div>
                    <a href="cart.php" class="btn btn--sm" style="margin-left:15px;"><span class="btn__label">View cart (<?= vebra_store_cart_count() ?>)</span></a>
                </div>
            <?php endif; ?>
            <?php if ($cartError !== ''): ?>
                <p role="alert" style="color:#ff8aa8; margin-bottom:20px;"><?= e($cartError) ?></p>
            <?php endif; ?>

            <div class="product-detail">
                <div class="product-detail__gallery">
                    <div class="product-detail__main" data-reveal="mask" data-cursor="Zoom">
                        <img src="<?= e($curProduct['image1']) ?>" alt="<?= e($curProduct['title']) ?>" id="mainProductImg">
                    </div>
                    <div class="product-detail__thumbs">
                        <?php foreach (['image1', 'image2', 'image3'] as $index => $imageKey): ?>
                            <button type="button" class="<?= $index === 0 ? 'is-active' : '' ?>" aria-label="Show product image <?= $index + 1 ?>" aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>" onclick="document.getElementById('mainProductImg').src='<?= e($curProduct[$imageKey]) ?>'; document.getElementById('mainProductImg').alt='<?= e($curProduct['title']) ?> image <?= $index + 1 ?>'; this.parentElement.querySelectorAll('button').forEach(function(button){button.classList.remove('is-active');button.setAttribute('aria-pressed','false')}); this.classList.add('is-active'); this.setAttribute('aria-pressed','true')">
                                <img src="<?= e($curProduct[$imageKey]) ?>" alt="<?= e($curProduct['title']) ?> view <?= $index + 1 ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="product-detail__body">
                    <span class="section-label"><?= e($curProduct['subtitle']) ?></span>
                    <h1 class="product-detail__title" data-split="lines" data-split-hero><?= e($curProduct['title']) ?></h1>
                    <span class="price product-detail__price">
                        $<?= e(number_format((float)$curProduct['price'], 2)) ?>
                        <?php if ($curProduct['old_price'] !== null): ?>
                            <del>$<?= e(number_format((float)$curProduct['old_price'], 2)) ?></del>
                        <?php endif; ?>
                    </span>

                    <div class="product-card__rating" aria-label="Rated <?= (int)$curProduct['rating'] ?> out of 5" role="img">
                        <?php for ($star = 1; $star <= 5; $star++): ?>
                            <i class="<?= $star <= $curProduct['rating'] ? 'ri-star-fill' : 'ri-star-line' ?>"></i>
                        <?php endfor; ?>
                        <span><?= (int)$curProduct['reviews_count'] ?> customer reviews</span>
                    </div>
                    <p class="section-head__text" data-reveal="up"><?= e($curProduct['desc']) ?></p>

                    <form method="post" action="">
                        <input type="hidden" name="add_to_cart" value="1">
                        <input type="hidden" name="csrf_token" value="<?= e(vebra_store_csrf_token()) ?>">
                        <?php if ($curProduct['has_sizes']): ?>
                            <div class="stack stack-2" style="margin-bottom:24px;">
                                <label class="label" for="product-size">Select Size</label>
                                <select id="product-size" name="size" required aria-label="Select size">
                                    <option value="">Choose a size</option>
                                    <?php foreach ($curProduct['sizes'] as $size): ?>
                                        <option value="<?= e($size) ?>"><?= e($size) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                        <div class="product-detail__buy">
                            <div class="qty">
                                <button type="button" data-step="down" aria-label="Decrease quantity"><i class="ri-subtract-line"></i></button>
                                <input type="number" name="quantity" value="1" min="1" max="20" required aria-label="Quantity">
                                <button type="button" data-step="up" aria-label="Increase quantity"><i class="ri-add-line"></i></button>
                            </div>
                            <button type="submit" class="btn btn--solid" data-magnetic="0.3"><span class="btn__label">Add to cart <i class="ri-shopping-bag-line"></i></span></button>
                            <button type="button" class="btn-circle" aria-label="Wishlist"><i class="ri-heart-line"></i></button>
                        </div>
                    </form>

                    <div class="info-list" style="margin-top:32px;">
                        <div class="info-list__row"><span class="info-list__label">SKU</span><span class="info-list__value"><?= e($curProduct['sku']) ?></span></div>
                        <div class="info-list__row"><span class="info-list__label">Category</span><span class="info-list__value"><?= e($curProduct['category']) ?></span></div>
                        <div class="info-list__row"><span class="info-list__label">Shipping</span><span class="info-list__value"><?= e($curProduct['shipping']) ?></span></div>
                    </div>
                </div>
            </div>

            <div class="product-tabs" data-tabs-scope>
                <div class="tabs" data-tabs>
                    <button type="button" class="tabs__btn is-active" data-tab="description">Description</button>
                    <button type="button" class="tabs__btn" data-tab="details">Details</button>
                    <button type="button" class="tabs__btn" data-tab="shipping">Shipping</button>
                    <span class="tabs__indicator" aria-hidden="true"></span>
                </div>
                <div class="tabs__panel is-active" data-tab-panel="description">
                    <div class="rich-text"><p><?= e($curProduct['desc']) ?></p></div>
                </div>
                <div class="tabs__panel" data-tab-panel="details">
                    <div class="rich-text">
                        <p><?= e($curProduct['subtitle']) ?></p>
                        <ul>
                            <li>SKU: <?= e($curProduct['sku']) ?></li>
                            <li>Category: <?= e($curProduct['category']) ?></li>
                        </ul>
                    </div>
                </div>
                <div class="tabs__panel" data-tab-panel="shipping">
                    <div class="rich-text">
                        <p><?= e($curProduct['shipping']) ?></p>
                        <p>Shipping charges and delivery availability are confirmed before dispatch. For order inquiries, contact <a href="contact.php">our support desk</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if ($relatedProducts): ?>
    <section class="section section--alt">
        <div class="container-vebra">
            <div class="section-head">
                <div>
                    <span class="section-label">Related</span>
                    <h2 class="section-head__title" data-split="lines">You may also <em>like</em></h2>
                </div>
                <a href="shop-page.php" class="link-arrow" data-magnetic="0.2">All products <i class="ri-arrow-right-up-line"></i></a>
            </div>
            <div class="products-grid" data-reveal-group data-stagger="0.09">
                <?php foreach ($relatedProducts as $product): ?>
                    <?php $productUrl = 'single-product.php?product=' . rawurlencode($product['id']); ?>
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
                            </div>
                        </div>
                        <div class="product-card__body">
                            <div>
                                <h3 class="product-card__title"><a href="<?= e($productUrl) ?>"><?= e($product['title']) ?></a></h3>
                                <span class="product-card__cat"><?= e($product['category']) ?></span>
                            </div>
                            <span class="price">$<?= e(number_format((float)$product['price'], 2)) ?></span>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
