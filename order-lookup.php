<?php
require_once __DIR__ . "/includes/store.php";
require_once __DIR__ . "/includes/db.php";

$order = null;
$items = [];
$lookupError = "";
$orderNumber = "";
$email = "";

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    if (!vebra_store_valid_csrf($_POST["csrf_token"] ?? null)) {
        $lookupError = "Invalid session. Please reload.";
    } else {
        $orderNumber = trim($_POST["order_number"] ?? "");
        $email = trim($_POST["email"] ?? "");

        if ($orderNumber === "" || $email === "") {
            $lookupError = "Please enter both your order number and email address.";
        } else {
            $stmt = mysqli_prepare($conn, "
                SELECT id, order_number, status, subtotal, shipping_amount, total_amount, created_at
                FROM store_orders
                WHERE order_number = ? AND LOWER(customer_email) = LOWER(?)
                LIMIT 1
            ");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $orderNumber, $email);
                mysqli_stmt_execute($stmt);
                $resOrder = mysqli_stmt_get_result($stmt);
                $order = mysqli_fetch_assoc($resOrder);
                mysqli_stmt_close($stmt);

                if ($order) {
                    $itemStmt = mysqli_prepare($conn, "
                        SELECT product_name, product_size, quantity, line_total
                        FROM store_order_items
                        WHERE order_id = ?
                        ORDER BY id
                    ");
                    if ($itemStmt) {
                        mysqli_stmt_bind_param($itemStmt, "i", $order["id"]);
                        mysqli_stmt_execute($itemStmt);
                        $resItems = mysqli_stmt_get_result($itemStmt);
                        while ($item = mysqli_fetch_assoc($resItems)) {
                            $items[] = $item;
                        }
                        mysqli_stmt_close($itemStmt);
                    }
                } else {
                    $lookupError = "We could not find an order matching those details.";
                }
            }
        }
    }
}

$pageTitle = "Order Lookup — VEBRA";
$activePage = "order-lookup";
require_once __DIR__ . "/includes/header.php";
?>

<main id="main">
    <section class="page-caption">
        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li><a href="shop-page.php">Shop</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page">Track an order</li>
                </ul>
                <h1 class="page-caption__title">Track your order</h1>
                <p class="page-caption__text">Enter the order number from your confirmation and the email used at checkout.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-vebra" style="max-width:820px;">
            <?php if ($lookupError !== ''): ?>
                <p role="alert" style="border:1px solid #ff8aa8; padding:16px; border-radius:8px;"><?= e($lookupError) ?></p>
            <?php endif; ?>

            <form method="post" action="order-lookup.php" style="display:grid; gap:16px; padding:24px; border:1px solid rgba(255,255,255,.12); border-radius:12px;">
                <input type="hidden" name="csrf_token" value="<?= e(vebra_store_csrf_token()) ?>">
                <label for="order-number">Order number
                    <input id="order-number" name="order_number" maxlength="24" autocomplete="off" required value="<?= e($orderNumber) ?>" placeholder="VEB-0123456789" style="display:block; width:100%;">
                </label>
                <label for="order-email">Checkout email
                    <input id="order-email" type="email" name="email" maxlength="254" autocomplete="email" required value="<?= e($email) ?>" style="display:block; width:100%;">
                </label>
                <button type="submit" class="btn btn--solid"><span class="btn__label">Find order</span></button>
            </form>

            <?php if ($order): ?>
                <section aria-labelledby="order-result-title" style="margin-top:28px; padding:24px; border:1px solid rgba(255,255,255,.12); border-radius:12px;">
                    <h2 id="order-result-title">Order <?= e($order['order_number']) ?></h2>
                    <p>Placed <?= e(date('M j, Y', strtotime($order['created_at']))) ?> &bull; Status: <strong><?= e(ucfirst($order['status'])) ?></strong></p>
                    <h3>Items</h3>
                    <ul>
                        <?php foreach ($items as $item): ?>
                            <li>
                                <?= e($item['product_name']) ?>
                                <?= $item['product_size'] ? ' (' . e($item['product_size']) . ')' : '' ?>
                                &times; <?= (int)$item['quantity'] ?> &mdash;
                                <?= e(vebra_store_format_money((int)round((float)$item['line_total'] * 100))) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <p>Subtotal: <strong><?= e(vebra_store_format_money((int)round((float)$order['subtotal'] * 100))) ?></strong></p>
                    <p>Shipping: <strong><?= $order['shipping_amount'] === null ? 'To be confirmed' : e(vebra_store_format_money((int)round((float)$order['shipping_amount'] * 100))) ?></strong></p>
                    <p>Total due on delivery: <strong><?= e(vebra_store_format_money((int)round((float)$order['total_amount'] * 100))) ?><?= $order['shipping_amount'] === null ? ' + shipping' : '' ?></strong></p>
                </section>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
