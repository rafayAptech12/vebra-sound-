<?php
require_once __DIR__ . '/includes/store.php';
require_once __DIR__ . '/includes/db.php';

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : (int)($_SESSION['store_last_order_id'] ?? 0);
if ($orderId <= 0) {
    header('Location: shop-page.php');
    exit;
}

$order = null;
$orderStmt = mysqli_prepare($conn, 'SELECT * FROM store_orders WHERE id = ? LIMIT 1');
if ($orderStmt) {
    mysqli_stmt_bind_param($orderStmt, "i", $orderId);
    mysqli_stmt_execute($orderStmt);
    $resOrder = mysqli_stmt_get_result($orderStmt);
    $order = mysqli_fetch_assoc($resOrder);
    mysqli_stmt_close($orderStmt);
}

if (!$order) {
    header('Location: shop-page.php');
    exit;
}

$items = [];
$itemStmt = mysqli_prepare($conn, 'SELECT * FROM store_order_items WHERE order_id = ? ORDER BY id');
if ($itemStmt) {
    mysqli_stmt_bind_param($itemStmt, "i", $orderId);
    mysqli_stmt_execute($itemStmt);
    $resItems = mysqli_stmt_get_result($itemStmt);
    while ($item = mysqli_fetch_assoc($resItems)) {
        $items[] = $item;
    }
    mysqli_stmt_close($itemStmt);
}

$pageTitle = 'Order Confirmation — VEBRA';
$activePage = 'cart';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">
    <section class="section section--glow">
        <div class="container-vebra" style="max-width:820px;">
            <span class="section-label">Order received</span>
            <h1 class="page-caption__title">Thank you</h1>
            <p class="section-head__text">
                Your order <strong><?= e($order['order_number']) ?></strong> has been saved.
                Payment is due in cash when delivered. We will confirm delivery availability and
                any shipping charge before dispatch.
            </p>
            <?php if (!empty($_SESSION['store_confirmation_email_sent'])): ?>
                <p role="status">A confirmation email has been sent to the email address provided at checkout.</p>
            <?php else: ?>
                <p role="status">We could not send a confirmation email, but your order is saved. You can check its status using your order number and checkout email.</p>
            <?php endif; ?>

            <div style="padding:24px; border:1px solid rgba(255,255,255,.12); border-radius:12px; margin:24px 0;">
                <h2>Order summary</h2>
                <?php foreach ($items as $item): ?>
                    <p>
                        <?= e($item['product_name']) ?>
                        <?= $item['product_size'] ? ' (' . e($item['product_size']) . ')' : '' ?>
                        &times; <?= (int)$item['quantity'] ?>
                        <strong style="float:right;"><?= e(vebra_store_format_money((int)round((float)$item['line_total'] * 100))) ?></strong>
                    </p>
                <?php endforeach; ?>
                <hr>
                <p>Items subtotal <strong style="float:right;"><?= e(vebra_store_format_money((int)round((float)$order['subtotal'] * 100))) ?></strong></p>
                <p>Shipping <strong style="float:right;">To be confirmed</strong></p>
                <p>Payment <strong style="float:right;">Cash on delivery</strong></p>
                <p>Order status <strong style="float:right;"><?= e(ucfirst($order['status'])) ?></strong></p>
            </div>

            <a href="shop-page.php" class="btn btn--solid"><span class="btn__label">Continue shopping</span></a>
            <a href="order-lookup.php" class="btn"><span class="btn__label">Track this order</span></a>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
