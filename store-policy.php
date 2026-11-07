<?php
$policies = [
    'shipping' => [
        'title' => 'Shipping policy',
        'description' => 'How delivery availability, shipping charges, and cash-on-delivery orders are handled.',
        'sections' => [
            ['Delivery availability', 'We review each order and confirm whether delivery is available for the address provided. An order remains pending until the store confirms it.'],
            ['Shipping charges and timing', 'Shipping charges and an estimated dispatch or delivery timeframe are confirmed with you before dispatch. Timing can vary by destination and carrier; no delivery estimate is guaranteed until confirmed.'],
            ['Payment', 'Payment is collected in cash at delivery. The amount due is the confirmed order subtotal plus the shipping charge agreed with you.'],
            ['Address changes', 'Contact us as soon as possible if your delivery details need to change. We cannot guarantee changes after an order has been dispatched.'],
        ],
    ],
    'returns' => [
        'title' => 'Returns and refunds',
        'description' => 'How to contact VEBRA about an order issue, return, or refund request.',
        'sections' => [
            ['Contact us first', 'If an item is damaged, incorrect, or you want to request a return, contact us through the Contact page and include your order number and a description of the issue. Please wait for return instructions before sending an item back.'],
            ['Review and resolution', 'We will review requests in light of the item condition and consumer-protection rules that apply to your location. Any approved return, replacement, or refund arrangement will be confirmed with you directly.'],
            ['Legal rights', 'This draft policy does not limit rights that cannot be excluded under applicable law. Specific eligibility periods, exclusions, and refund timing must be confirmed for the markets in which the store operates.'],
        ],
    ],
    'privacy' => [
        'title' => 'Privacy policy',
        'description' => 'What information is used to process and support VEBRA store orders.',
        'sections' => [
            ['Information collected', 'When you place an order, we collect the name, email, phone number, delivery address, order contents, and any note you choose to provide.'],
            ['How we use it', 'We use order information to review and fulfill orders, confirm delivery and shipping charges, provide order updates, and respond to customer-support requests.'],
            ['Storage and sharing', 'Order records are stored in the store database. We share only the information needed with service providers involved in payment collection or delivery, and when required by law.'],
            ['Your choices', 'Contact us through the Contact page to ask about access, correction, or deletion of your information. Some order records may need to be retained for legal, accounting, or dispute-resolution purposes. Applicable rights depend on your location.'],
        ],
    ],
    'terms' => [
        'title' => 'Terms of sale',
        'description' => 'Draft terms for placing and paying for VEBRA store orders.',
        'sections' => [
            ['Orders', 'Submitting an order is a request to purchase. The store reviews each order and confirms delivery availability and shipping charges before dispatch. An order is not confirmed until the store contacts you.'],
            ['Prices and payment', 'Prices are displayed in US dollars. Orders use cash on delivery; the final amount due is the item subtotal plus the shipping amount confirmed before dispatch.'],
            ['Product information', 'We make reasonable efforts to describe and display products accurately. Product images and colors may vary from the physical item; contact us with questions before ordering.'],
            ['Applicable rights', 'These draft terms are subject to mandatory consumer rights and other rules that apply in your location. Store-specific business details and governing-law terms must be reviewed before launch.'],
        ],
    ],
];

$policyKey = isset($_GET['policy']) && is_string($_GET['policy']) ? $_GET['policy'] : 'shipping';
if (!isset($policies[$policyKey])) {
    http_response_code(404);
    $policyKey = 'shipping';
}
$policy = $policies[$policyKey];
$pageTitle = $policy['title'] . ' — VEBRA Store';
$pageDesc = $policy['description'];
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
                    <li><a href="shop-page.php">Shop</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page"><?= e($policy['title']) ?></li>
                </ul>
                <h1 class="page-caption__title"><?= e($policy['title']) ?></h1>
                <p class="page-caption__text"><?= e($policy['description']) ?></p>
            </div>
        </div>
    </section>
    <section class="section">
        <div class="container-vebra" style="max-width:900px;">
            <p role="note" style="border:1px solid var(--accent); padding:16px; border-radius:8px;">
                Draft for store-owner review. Confirm these terms against your actual business practices and the laws in each sales region before launch.
            </p>
            <?php foreach ($policy['sections'] as [$heading, $copy]): ?>
                <section aria-labelledby="<?= e($policyKey . '-' . strtolower(str_replace(' ', '-', $heading))) ?>" style="margin:28px 0;">
                    <h2 id="<?= e($policyKey . '-' . strtolower(str_replace(' ', '-', $heading))) ?>"><?= e($heading) ?></h2>
                    <p><?= e($copy) ?></p>
                </section>
            <?php endforeach; ?>
            <p>Questions about an order? <a href="contact.php">Contact VEBRA</a> or <a href="order-lookup.php">look up your order</a>.</p>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
