<?php
require_once __DIR__ . '/includes/store.php';

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!vebra_store_valid_csrf($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please reload the cart and try again.';
    } else {
        $action = $_POST['action'] ?? '';
        $cart = vebra_store_cart();

        if ($action === 'remove') {
            $postedKey = $_POST['item_key'] ?? '';
            $key = is_string($postedKey) ? $postedKey : '';
            if (array_key_exists($key, $cart)) {
                vebra_store_cart_remove($key);
            }
        } elseif ($action === 'clear') {
            vebra_store_cart_clear();
        } elseif ($action === 'update_one') {
            $postedKey = $_POST['item_key'] ?? '';
            $key = is_string($postedKey) ? $postedKey : '';
            $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
            if (!array_key_exists($key, $cart)
                || $quantity === false || $quantity < 1 || $quantity > 20) {
                $error = 'Each item quantity must be between 1 and 20.';
            } else {
                vebra_store_cart_update($key, $quantity);
            }
        } else {
            $error = 'That cart action is not available.';
        }

        if ($error === '') {
            $_SESSION['store_cart_notice'] = 'Your cart has been updated.';
            header('Location: cart.php');
            exit;
        }
    }
}

$notice = $_SESSION['store_cart_notice'] ?? '';
unset($_SESSION['store_cart_notice']);
$items = vebra_store_cart_items();
$subtotalCents = array_sum(array_column($items, 'line_cents'));
$pageTitle = 'Shopping Cart — VEBRA';
$bodyClass = 'theme-magenta';
$activePage = 'more';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">
    <section class="page-caption">
        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page">Shopping Cart</li>
                </ul>
                <h1 class="page-caption__title">Your cart</h1>
                <p class="page-caption__text">Review your items before placing a cash-on-delivery order.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-vebra">
            <?php if ($error !== ''): ?>
                <p role="alert" style="color:#ff8aa8; margin-bottom:20px;"><?= e($error) ?></p>
            <?php endif; ?>
            <?php if ($notice !== ''): ?>
                <p role="status" style="color:#b6f3cc; margin-bottom:20px;"><?= e($notice) ?></p>
            <?php endif; ?>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <div style="padding:20px 24px; border:1px solid rgba(255, 45, 85, 0.3); background:rgba(255, 45, 85, 0.08); border-radius:12px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
                    <div>
                        <h3 style="margin:0 0 4px; font-size:1.05rem; color:#fff;">Logged Out Guest</h3>
                        <p style="margin:0; font-size:0.9rem; color:var(--ink-60);">Please log in to add products to your cart and complete checkout.</p>
                    </div>
                    <a href="auth/login.php?redirect=<?= urlencode('../cart.php') ?>" class="btn btn--solid btn--sm"><span class="btn__label">Log In</span></a>
                </div>
            <?php endif; ?>

            <?php if (!$items): ?>
                <div style="padding:32px; border:1px solid rgba(255,255,255,.12); border-radius:12px;">
                    <h2>Your cart is empty</h2>
                    <p class="section-head__text">Browse the store and add something you love.</p>
                    <a href="shop-page.php" class="btn btn--solid"><span class="btn__label">Visit the store</span></a>
                </div>
            <?php else: ?>
                <div style="display:grid; gap:14px;">
                    <?php foreach ($items as $item): ?>
                        <article style="display:grid; grid-template-columns:90px minmax(0,1fr) auto; gap:18px; align-items:center; padding:16px; border:1px solid rgba(255,255,255,.12); border-radius:12px;">
                            <img src="<?= e($item['image']) ?>" alt="" style="width:90px; height:90px; object-fit:cover; border-radius:8px;">
                            <div>
                                <h2 style="font-size:1.05rem; margin:0 0 6px;"><?= e($item['name']) ?></h2>
                                <?php if ($item['size'] !== ''): ?><p style="margin:0 0 6px;">Size: <?= e($item['size']) ?></p><?php endif; ?>
                                <p style="margin:0;"><?= e(vebra_store_format_money($item['unit_cents'])) ?> each</p>
                            </div>
                            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; justify-content:flex-end;">
                                <form method="post" action="cart.php" style="display:flex; align-items:center; gap:8px;">
                                    <input type="hidden" name="csrf_token" value="<?= e(vebra_store_csrf_token()) ?>">
                                    <input type="hidden" name="item_key" value="<?= e($item['key']) ?>">
                                    <input type="hidden" name="action" value="update_one">
                                    <label>Qty
                                        <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="20" required style="width:76px;">
                                    </label>
                                    <button type="submit" class="btn btn--sm">Update</button>
                                </form>
                                <strong><?= e(vebra_store_format_money($item['line_cents'])) ?></strong>
                                <form method="post" action="cart.php">
                                    <input type="hidden" name="csrf_token" value="<?= e(vebra_store_csrf_token()) ?>">
                                    <input type="hidden" name="item_key" value="<?= e($item['key']) ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <button type="submit" class="btn btn--sm">Remove</button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; gap:20px; flex-wrap:wrap; margin-top:26px;">
                    <form method="post" action="cart.php">
                        <input type="hidden" name="csrf_token" value="<?= e(vebra_store_csrf_token()) ?>">
                        <input type="hidden" name="action" value="clear">
                        <button type="submit" class="btn"><span class="btn__label">Empty cart</span></button>
                    </form>
                    <div style="text-align:right;">
                        <p style="margin:0 0 12px;">Items subtotal: <strong><?= e(vebra_store_format_money($subtotalCents)) ?></strong></p>
                        <a href="checkout.php" class="btn btn--solid"><span class="btn__label">Checkout — cash on delivery</span></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
