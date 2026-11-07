<?php
require_once __DIR__ . "/includes/store.php";
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/store-mailer.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php?redirect=" . urlencode('../checkout.php') . "&msg=login_required");
    exit;
}

$items = vebra_store_cart_items();
if (!$items) {
    header("Location: cart.php");
    exit;
}

$errors = [];
$values = [
    "customer_name" => "",
    "customer_email" => "",
    "customer_phone" => "",
    "address_line1" => "",
    "address_line2" => "",
    "city" => "",
    "region" => "",
    "postal_code" => "",
    "country" => "",
    "customer_note" => "",
];

if (($_SERVER["REQUEST_METHOD"] ?? "") !== "POST" && isset($_SESSION['user_id'])) {
    $userId = (int)$_SESSION['user_id'];
    $uStmt = mysqli_prepare($conn, "SELECT name, email, phone, address FROM users WHERE id = ? LIMIT 1");
    if ($uStmt) {
        mysqli_stmt_bind_param($uStmt, "i", $userId);
        mysqli_stmt_execute($uStmt);
        $uRes = mysqli_stmt_get_result($uStmt);
        if ($uRow = mysqli_fetch_assoc($uRes)) {
            $values["customer_name"] = $uRow["name"] ?? "";
            $values["customer_email"] = $uRow["email"] ?? "";
            $values["customer_phone"] = $uRow["phone"] ?? "";
            $values["address_line1"] = $uRow["address"] ?? "";
        }
        mysqli_stmt_close($uStmt);
    }
}

if (($_SERVER["REQUEST_METHOD"] ?? "") === "POST") {
    foreach ($values as $field => $default) {
        $postedValue = $_POST[$field] ?? $default;
        if (!is_string($postedValue)) {
            $errors[] = "Invalid form data. Please review your details and try again.";
            continue;
        }
        $values[$field] = trim($postedValue);
    }

    if (!vebra_store_valid_csrf($_POST["csrf_token"] ?? null)) {
        $errors[] = "Your session expired. Please reload checkout and try again.";
    }

    $requiredFields = [
        "customer_name" => "Name",
        "customer_email" => "Email",
        "customer_phone" => "Phone",
        "address_line1" => "Address",
        "city" => "City",
        "country" => "Country",
    ];
    foreach ($requiredFields as $field => $label) {
        if ($values[$field] === "") {
            $errors[] = $label . " is required.";
        }
    }

    if ($values["customer_email"] !== "" && !filter_var($values["customer_email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }
    if ($values["customer_phone"] !== "" && !preg_match("/^[0-9+().\-\s]{7,40}$/", $values["customer_phone"])) {
        $errors[] = "Enter a valid phone number.";
    }
    $maxLengths = [
        "customer_name" => 150,
        "customer_email" => 254,
        "customer_phone" => 40,
        "address_line1" => 255,
        "address_line2" => 255,
        "city" => 120,
        "region" => 120,
        "postal_code" => 32,
        "country" => 120,
        "customer_note" => 2000,
    ];
    foreach ($maxLengths as $field => $maxLength) {
        if (strlen($values[$field]) > $maxLength) {
            $errors[] = "One or more fields exceed the allowed length.";
            break;
        }
    }

    if (!$errors) {
        $subtotalCents = array_sum(array_column($items, "line_cents"));
        $orderNumber = "VEB-" . strtoupper(bin2hex(random_bytes(5)));

        mysqli_begin_transaction($conn);
        try {
            $orderStmt = mysqli_prepare($conn, "
                INSERT INTO store_orders (
                    order_number, customer_name, customer_email, customer_phone,
                    address_line1, address_line2, city, region, postal_code, country,
                    customer_note, payment_method, status, subtotal, shipping_amount, total_amount
                ) VALUES (
                    ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?,
                    ?, 'cash_on_delivery', 'pending', ?, NULL, ?
                )
            ");
            if ($orderStmt) {
                $subtotal = $subtotalCents / 100;
                $total = $subtotal;
                mysqli_stmt_bind_param($orderStmt, "sssssssssssdd", 
                    $orderNumber, $values["customer_name"], $values["customer_email"], $values["customer_phone"],
                    $values["address_line1"], $values["address_line2"], $values["city"], $values["region"], $values["postal_code"], $values["country"],
                    $values["customer_note"], $subtotal, $total);
                mysqli_stmt_execute($orderStmt);
                $orderId = mysqli_insert_id($conn);
                mysqli_stmt_close($orderStmt);

                $itemStmt = mysqli_prepare($conn, "
                    INSERT INTO store_order_items (
                        order_id, product_key, sku, product_name, product_size,
                        unit_price, quantity, line_total
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                if ($itemStmt) {
                    foreach ($items as $item) {
                        $unitP = $item["unit_cents"] / 100;
                        $lineP = $item["line_cents"] / 100;
                        mysqli_stmt_bind_param($itemStmt, "issssdid", 
                            $orderId, $item["id"], $item["sku"], $item["name"], $item["size"],
                            $unitP, $item["quantity"], $lineP);
                        mysqli_stmt_execute($itemStmt);
                    }
                    mysqli_stmt_close($itemStmt);
                }

                mysqli_commit($conn);
                vebra_store_cart_clear();
                $_SESSION["store_last_order_id"] = $orderId;
                $_SESSION["store_csrf_token"] = bin2hex(random_bytes(32));

                // Send confirmation email
                $emailLines = [
                    "Hello " . $values["customer_name"] . ",",
                    "Your order #" . $orderNumber . " has been received.",
                    "Items: " . count($items),
                    "Subtotal: " . vebra_store_format_money($subtotalCents),
                    "Payment: Cash on delivery",
                    "",
                    "We will contact you shortly to confirm shipping costs and delivery.",
                ];
                $appUrl = rtrim((string)getenv("APP_URL"), "/");
                $emailLines[] = filter_var($appUrl, FILTER_VALIDATE_URL) && preg_match("#^https?://#i", $appUrl)
                    ? "Track your order at: " . $appUrl . "/order-lookup.php"
                    : "To check your order, use the Track an Order page on the VEBRA website.";
                
                vebra_send_email(
                    $values["customer_email"],
                    "Order Confirmation: " . $orderNumber,
                    implode("\n", $emailLines)
                );

                header("Location: order-confirmation.php?id=" . $orderId);
                exit;
            }
        } catch (Exception $exception) {
            mysqli_rollback($conn);
            error_log("VEBRA checkout error: " . $exception->getMessage());
            $errors[] = "An unexpected error occurred while saving your order. Please try again.";
        }
    }
}

$subtotalCents = array_sum(array_column($items, "line_cents"));
$pageTitle = "Checkout — VEBRA";
$activePage = "cart";
require_once __DIR__ . "/includes/header.php";
?>

<main id="main">
    <section class="page-caption">
        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li><a href="cart.php">Cart</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page">Checkout</li>
                </ul>
                <h1 class="page-caption__title">Checkout</h1>
                <p class="page-caption__text">Place your order and pay in cash when it is delivered.</p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-vebra">
            <?php if ($errors): ?>
                <div role="alert" style="border:1px solid #ff8aa8; padding:16px; border-radius:8px; margin-bottom:24px;">
                    <ul style="margin:0;"><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="row g-5">
                <div class="col-lg-7">
                    <form method="post" action="checkout.php" style="display:grid; gap:16px;">
                        <input type="hidden" name="csrf_token" value="<?= e(vebra_store_csrf_token()) ?>">
                        <h2>Delivery details</h2>
                        <label>Full name
                            <input name="customer_name" maxlength="150" autocomplete="name" required value="<?= e($values['customer_name']) ?>" style="display:block; width:100%;">
                        </label>
                        <div class="row g-3">
                            <div class="col-md-6"><label>Email
                                <input type="email" name="customer_email" maxlength="254" autocomplete="email" required value="<?= e($values['customer_email']) ?>" style="display:block; width:100%;">
                            </label></div>
                            <div class="col-md-6"><label>Phone
                                <input type="tel" name="customer_phone" maxlength="40" autocomplete="tel" required value="<?= e($values['customer_phone']) ?>" style="display:block; width:100%;">
                            </label></div>
                        </div>
                        <label>Address line 1
                            <input name="address_line1" maxlength="255" autocomplete="address-line1" required value="<?= e($values['address_line1']) ?>" style="display:block; width:100%;">
                        </label>
                        <label>Address line 2 (optional)
                            <input name="address_line2" maxlength="255" autocomplete="address-line2" value="<?= e($values['address_line2']) ?>" style="display:block; width:100%;">
                        </label>
                        <div class="row g-3">
                            <div class="col-md-6"><label>City
                                <input name="city" maxlength="120" autocomplete="address-level2" required value="<?= e($values['city']) ?>" style="display:block; width:100%;">
                            </label></div>
                            <div class="col-md-6"><label>State / region
                                <input name="region" maxlength="120" autocomplete="address-level1" value="<?= e($values['region']) ?>" style="display:block; width:100%;">
                            </label></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6"><label>Postal code
                                <input name="postal_code" maxlength="32" autocomplete="postal-code" value="<?= e($values['postal_code']) ?>" style="display:block; width:100%;">
                            </label></div>
                            <div class="col-md-6"><label>Country
                                <input name="country" maxlength="120" autocomplete="country-name" required value="<?= e($values['country']) ?>" style="display:block; width:100%;">
                            </label></div>
                        </div>
                        <label>Order note (optional)
                            <textarea name="customer_note" maxlength="2000" rows="3" style="display:block; width:100%;"><?= e($values['customer_note']) ?></textarea>
                        </label>
                        <p>Payment method: <strong>Cash on delivery</strong>. Shipping cost and delivery availability will be confirmed before dispatch.</p>
                        <button type="submit" class="btn btn--solid"><span class="btn__label">Place cash-on-delivery order</span></button>
                    </form>
                </div>

                <aside class="col-lg-5">
                    <div style="padding:24px; border:1px solid rgba(255,255,255,.12); border-radius:12px;">
                        <h2>Your order</h2>
                        <?php foreach ($items as $item): ?>
                            <p><?= e($item['name']) ?><?= $item['size'] !== '' ? ' (' . e($item['size']) . ')' : '' ?> &times; <?= (int)$item['quantity'] ?>
                                <strong style="float:right;"><?= e(vebra_store_format_money($item['line_cents'])) ?></strong>
                            </p>
                        <?php endforeach; ?>
                        <hr>
                        <p>Items subtotal <strong style="float:right;"><?= e(vebra_store_format_money($subtotalCents)) ?></strong></p>
                        <p>Shipping <strong style="float:right;">To be confirmed</strong></p>
                        <p>Pay at delivery <strong style="float:right;"><?= e(vebra_store_format_money($subtotalCents)) ?> + shipping</strong></p>
                        <a href="cart.php">Edit cart</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
