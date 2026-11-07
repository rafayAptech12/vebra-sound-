<?php
require_once __DIR__ . '/includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cityParam = isset($_GET['city']) ? strtolower(trim($_GET['city'])) : 'vienna';

// Real database artist record for VEBRA featured artist
$artistRes = mysqli_query($conn, "SELECT * FROM artists WHERE image IS NOT NULL AND image != '' ORDER BY id ASC LIMIT 1");
$artist = ($artistRes && $row = mysqli_fetch_assoc($artistRes)) ? $row : null;
$artistName = $artist['name'] ?? 'VEBRA';
$artistId = (int)($artist['id'] ?? 2);

$eventsData = [
    'vienna' => [
        'city' => 'Vienna',
        'country' => 'Austria',
        'venue' => 'Wiener Stadthalle',
        'hall' => 'Wiener Stadthalle, Halle D',
        'date' => '02 June 2026',
        'date_full' => 'Tuesday, 02 June 2026',
        'doors' => '20:00',
        'stage' => '21:00',
        'curfew' => '23:00',
        'price' => '€48',
        'capacity' => '2,800 standing',
        'age' => '16+ (under 16 with an adult)',
        'address' => 'Roland-Rainer-Platz 1, Vienna',
        'transport' => 'U6 Burggasse-Stadthalle',
        'image' => 'assets/images/slider-3.jpg',
        'badge' => 'Final tickets',
        'days' => '21', 'hours' => '08', 'mins' => '44', 'secs' => '17'
    ],
    'london' => [
        'city' => 'London',
        'country' => 'United Kingdom',
        'venue' => 'Eventim Apollo',
        'hall' => 'Eventim Apollo Hammersmith',
        'date' => '14 May 2026',
        'date_full' => 'Thursday, 14 May 2026',
        'doors' => '20:00',
        'stage' => '21:00',
        'curfew' => '23:00',
        'price' => '£42',
        'capacity' => '3,500 standing & seated',
        'age' => '14+ (under 16 with adult)',
        'address' => '45 Queen Caroline St, London W6 9QH',
        'transport' => 'Hammersmith Station (Piccadilly / District)',
        'image' => 'assets/images/gallery-1.jpg',
        'badge' => 'Selling fast',
        'days' => '02', 'hours' => '14', 'mins' => '22', 'secs' => '05'
    ],
    'manchester' => [
        'city' => 'Manchester',
        'country' => 'United Kingdom',
        'venue' => 'Albert Hall',
        'hall' => 'Albert Hall Manchester',
        'date' => '19 May 2026',
        'date_full' => 'Tuesday, 19 May 2026',
        'doors' => '19:30',
        'stage' => '20:00',
        'curfew' => '23:00',
        'price' => '£38',
        'capacity' => '2,000 standing',
        'age' => '16+',
        'address' => '27 Peter St, Manchester M2 5QR',
        'transport' => 'St Peter’s Square Tram',
        'image' => 'assets/images/gallery-2.jpg',
        'badge' => 'Limited',
        'days' => '07', 'hours' => '05', 'mins' => '10', 'secs' => '40'
    ],
    'amsterdam' => [
        'city' => 'Amsterdam',
        'country' => 'Netherlands',
        'venue' => 'AFAS Live',
        'hall' => 'AFAS Live Arena',
        'date' => '25 May 2026',
        'date_full' => 'Monday, 25 May 2026',
        'doors' => '18:30',
        'stage' => '19:30',
        'curfew' => '23:00',
        'price' => '€45',
        'capacity' => '6,000 standing',
        'age' => 'All ages (under 14 with adult)',
        'address' => 'ArenA Boulevard 590, Amsterdam',
        'transport' => 'Station Bijlmer ArenA',
        'image' => 'assets/images/gallery-3.jpg',
        'badge' => 'Available',
        'days' => '13', 'hours' => '19', 'mins' => '02', 'secs' => '18'
    ],
    'brooklyn' => [
        'city' => 'Brooklyn',
        'country' => 'United States',
        'venue' => 'Kings Theatre',
        'hall' => 'Kings Theatre Main Auditorium',
        'date' => '26 July 2026',
        'date_full' => 'Sunday, 26 July 2026',
        'doors' => '19:00',
        'stage' => '20:00',
        'curfew' => '23:30',
        'price' => '$55',
        'capacity' => '3,200 seated',
        'age' => 'All ages',
        'address' => '1027 Flatbush Ave, Brooklyn, NY 11226',
        'transport' => 'Flatbush Ave B/Q Train',
        'image' => 'assets/images/gallery-5.jpg',
        'badge' => 'Homecoming Special',
        'days' => '75', 'hours' => '12', 'mins' => '30', 'secs' => '00'
    ]
];

$curEvent = isset($eventsData[$cityParam]) ? $eventsData[$cityParam] : $eventsData['vienna'];

$bookingSuccess = null;
$bookingError = '';

// Handle Actual MySQL Ticket Booking
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['book_tickets'])) {
    $custName = trim($_POST['customer_name'] ?? '');
    $custEmail = trim($_POST['customer_email'] ?? '');
    $custPhone = trim($_POST['customer_phone'] ?? '');
    $qty = (int)($_POST['quantity'] ?? 1);
    $selectedCity = strtolower(trim($_POST['city_key'] ?? $cityParam));

    if (!isset($eventsData[$selectedCity])) {
        $bookingError = 'Invalid event city selected.';
    } elseif ($custName === '') {
        $bookingError = 'Please enter your full customer name.';
    } elseif ($custEmail === '' || !filter_var($custEmail, FILTER_VALIDATE_EMAIL)) {
        $bookingError = 'Please provide a valid email address for confirmation.';
    } elseif ($qty < 1 || $qty > 6) {
        $bookingError = 'Ticket quantity must be between 1 and 6.';
    } else {
        $eventInfo = $eventsData[$selectedCity];
        preg_match('/([^0-9]+)?([0-9]+)/', $eventInfo['price'], $matches);
        $curr = !empty($matches[1]) ? trim($matches[1]) : '€';
        $unitPrice = !empty($matches[2]) ? (float)$matches[2] : 45.0;
        $totalPrice = $unitPrice * $qty;

        // Anti-duplicate check
        $dupCheck = mysqli_prepare($conn, "SELECT id FROM ticket_bookings WHERE customer_email = ? AND event_city = ? AND quantity = ? AND created_at > (NOW() - INTERVAL 60 SECOND) LIMIT 1");
        $isDuplicate = false;
        if ($dupCheck) {
            mysqli_stmt_bind_param($dupCheck, "ssi", $custEmail, $eventInfo['city'], $qty);
            mysqli_stmt_execute($dupCheck);
            mysqli_stmt_store_result($dupCheck);
            if (mysqli_stmt_num_rows($dupCheck) > 0) {
                $isDuplicate = true;
            }
            mysqli_stmt_close($dupCheck);
        }

        if ($isDuplicate) {
            $bookingError = 'A booking with these details was already placed a moment ago. Please check your email.';
        } else {
            $bookingRef = 'TKT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

            $insertStmt = mysqli_prepare($conn, "
                INSERT INTO ticket_bookings 
                (booking_reference, user_id, event_city, event_venue, event_date, customer_name, customer_email, customer_phone, quantity, total_price, currency, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");

            if ($insertStmt) {
                mysqli_stmt_bind_param($insertStmt, "sissssssids",
                    $bookingRef, $userId, $eventInfo['city'], $eventInfo['venue'], $eventInfo['date_full'],
                    $custName, $custEmail, $custPhone, $qty, $totalPrice, $curr
                );
                if (mysqli_stmt_execute($insertStmt)) {
                    $bookingSuccess = [
                        'reference' => $bookingRef,
                        'name' => $custName,
                        'email' => $custEmail,
                        'phone' => $custPhone,
                        'city' => $eventInfo['city'],
                        'venue' => $eventInfo['venue'],
                        'date' => $eventInfo['date_full'],
                        'doors' => $eventInfo['doors'],
                        'quantity' => $qty,
                        'total' => $curr . number_format($totalPrice, 2),
                        'unit_price' => $curr . number_format($unitPrice, 2)
                    ];
                } else {
                    $bookingError = 'Database booking execution failed. Please try again.';
                }
                mysqli_stmt_close($insertStmt);
            } else {
                $bookingError = 'Could not prepare ticket reservation statement.';
            }
        }
    }
}

$pageTitle = $curEvent['venue'] . ', ' . $curEvent['city'] . ' — vebra';
$pageDesc = 'vebra play Nightform in full at ' . $curEvent['venue'] . ', ' . $curEvent['city'] . ' on ' . $curEvent['date'] . '. Tickets, line-up and venue details.';
$bodyClass = 'theme-magenta';
$activePage = 'events';
$activeSub = 'single-event';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- SINGLE EVENT
    ============================================= -->
    <section class="section section--below-header section--glow">
        <div class="container-vebra">
            <div class="stack stack-7">
                <div class="stack stack-6">
                    <ul class="breadcrumb-vebra" data-reveal="up">
                        <li><a href="index.php">Home</a></li>
                        <li class="breadcrumb-vebra__sep">/</li>
                        <li><a href="events.php">Events</a></li>
                        <li class="breadcrumb-vebra__sep">/</li>
                        <li aria-current="page"><?= htmlspecialchars($curEvent['venue']) ?></li>
                    </ul>

                    <div class="event-hero" data-reveal="mask">
                        <img src="<?= htmlspecialchars($curEvent['image']) ?>" alt="vebra on stage at <?= htmlspecialchars($curEvent['venue']) ?>">
                        <div class="event-hero__body">
                            <div class="stack stack-3">
                                <span class="section-label"><?= htmlspecialchars($curEvent['date']) ?> &middot; <?= htmlspecialchars($curEvent['city']) ?>, <?= htmlspecialchars($curEvent['country']) ?></span>
                                <h1 class="event-hero__title" data-split="lines" data-split-hero><?= htmlspecialchars($curEvent['venue']) ?></h1>
                            </div>
                            <div class="album-hero__platforms">
                                <span class="pill pill--accent"><i class="ri-time-line"></i> Doors <?= htmlspecialchars($curEvent['doors']) ?></span>
                                <span class="pill"><i class="ri-user-3-line"></i> <?= htmlspecialchars($curEvent['age']) ?></span>
                                <span class="pill"><i class="ri-fire-line"></i> <?= htmlspecialchars($curEvent['badge']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="event-layout">
                    <div class="stack stack-6">
                        <div class="stack stack-4" data-reveal="up">
                            <h2 class="section-head__title" data-split="lines">One night, <em>full volume</em></h2>
                            <p class="section-head__text">
                                <?= htmlspecialchars($curEvent['city']) ?> is one of the standout dates on the Nightform run where we
                                play the record in its fullest dimension. The room is tuned specifically for sub-bass dynamics,
                                bringing our signature modular rig, analogue synthesizers, and lighting installation that captivated audiences last winter.
                            </p>
                            <p class="section-head__text">
                                Doors open at <?= htmlspecialchars($curEvent['doors']) ?> with a short ambient DJ warm-up in the foyer. VEBRA take the stage at <?= htmlspecialchars($curEvent['stage']) ?>
                                and play for roughly one hundred minutes with no interval &mdash; the whole of side A
                                in sequence, highlights from <em>Halogen</em>, and exclusive unreleased live arrangements.
                            </p>
                            <p class="section-head__text">
                                The venue is fully cashless and bag-checked. Anything larger than A4 goes in the
                                cloakroom, which is free with a ticket. Under-16s must be accompanied by an adult, and
                                the entire floor offers unobstructed views and high-fidelity sound.
                            </p>
                        </div>

                        <div class="stack stack-3" data-reveal="up">
                            <h3 class="sidebar__title">Line-up &amp; running order</h3>
                            <div class="info-list">
                                <div class="info-list__row"><span class="info-list__label"><?= htmlspecialchars($curEvent['doors']) ?></span><span class="info-list__value">Doors &amp; foyer DJ set</span></div>
                                <div class="info-list__row"><span class="info-list__label">20:15</span><span class="info-list__value">Marta Voss &mdash; support</span></div>
                                <div class="info-list__row"><span class="info-list__label"><?= htmlspecialchars($curEvent['stage']) ?></span><span class="info-list__value"><a href="single-artist.php?id=<?= $artistId ?>" style="color:var(--accent); font-weight:600; text-decoration:underline; text-underline-offset:3px;"><?= htmlspecialchars($artistName) ?></a> &mdash; Nightform live</span></div>
                                <div class="info-list__row"><span class="info-list__label"><?= htmlspecialchars($curEvent['curfew']) ?></span><span class="info-list__value">Encore &amp; curfew</span></div>
                            </div>
                        </div>

                        <div class="stack stack-3" data-reveal="up">
                            <h3 class="sidebar__title">Event details</h3>
                            <div class="info-list">
                                <div class="info-list__row"><span class="info-list__label">Date</span><span class="info-list__value"><?= htmlspecialchars($curEvent['date_full']) ?></span></div>
                                <div class="info-list__row"><span class="info-list__label">Venue</span><span class="info-list__value"><?= htmlspecialchars($curEvent['hall']) ?></span></div>
                                <div class="info-list__row"><span class="info-list__label">City</span><span class="info-list__value"><?= htmlspecialchars($curEvent['city']) ?>, <?= htmlspecialchars($curEvent['country']) ?></span></div>
                                <div class="info-list__row"><span class="info-list__label">Capacity</span><span class="info-list__value"><?= htmlspecialchars($curEvent['capacity']) ?></span></div>
                                <div class="info-list__row"><span class="info-list__label">Age</span><span class="info-list__value"><?= htmlspecialchars($curEvent['age']) ?></span></div>
                            </div>
                        </div>

                        <div class="contact-map" data-reveal="up">
                            <img src="assets/images/event-map.jpg" alt="Map showing <?= htmlspecialchars($curEvent['venue']) ?>, <?= htmlspecialchars($curEvent['city']) ?>">
                            <span class="contact-map__pin"><i class="ri-map-pin-fill"></i> <?= htmlspecialchars($curEvent['address']) ?></span>
                        </div>
                    </div>

                    <aside class="event-sidebar" data-reveal="up" aria-label="Event details">
                        <div class="stack stack-2">
                            <span class="section-label">Tickets from</span>
                            <span class="event-sidebar__price"><?= htmlspecialchars($curEvent['price']) ?></span>
                        </div>

                        <div class="event-stats">
                            <div><strong><?= htmlspecialchars($curEvent['days']) ?></strong><span>Days</span></div>
                            <div><strong><?= htmlspecialchars($curEvent['hours']) ?></strong><span>Hours</span></div>
                            <div><strong><?= htmlspecialchars($curEvent['mins']) ?></strong><span>Mins</span></div>
                            <div><strong><?= htmlspecialchars($curEvent['secs']) ?></strong><span>Secs</span></div>
                        </div>

                        <?php if ($bookingSuccess): ?>
                            <div class="ticket-booking-box" style="margin: 1.25rem 0; padding: 1.5rem; background: rgba(var(--accent-rgb, 255, 46, 147), 0.08); border: 1px solid var(--accent); border-radius: var(--radius-md);" role="status">
                                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; color:var(--accent); font-weight:700; font-size:1.05rem;">
                                    <i class="ri-checkbox-circle-fill" style="font-size:1.4rem;"></i>
                                    <span>TICKETS RESERVED (PENDING)</span>
                                </div>
                                <div style="padding:12px; background:rgba(0,0,0,0.4); border-radius:var(--radius-xs); margin-bottom:14px; border:1px dashed rgba(255,255,255,0.2);">
                                    <div style="font-size:0.75rem; color:var(--ink-70); text-transform:uppercase; letter-spacing:0.05em;">Pass Reference</div>
                                    <div style="font-family:'Unbounded', monospace; font-size:1.15rem; font-weight:800; color:#fff; letter-spacing:1px;"><?= htmlspecialchars($bookingSuccess['reference']) ?></div>
                                    <div style="margin-top:8px; font-size:0.85rem; color:var(--ink-100);">
                                        <strong><?= (int)$bookingSuccess['quantity'] ?>x Ticket(s)</strong> &bull; <?= htmlspecialchars($bookingSuccess['total']) ?>
                                    </div>
                                    <div style="font-size:0.8rem; color:var(--ink-70); margin-top:4px;">
                                        Guest: <?= htmlspecialchars($bookingSuccess['name']) ?> &bull; Status: <span style="color:#f59e0b;font-weight:600;">Pending Payment</span>
                                    </div>
                                </div>
                                <p style="font-size:0.8rem; color:var(--ink-70); margin:0 0 12px; line-height:1.4;">
                                    Your booking has been registered as pending. Settle at the box office or show your pass reference at the venue doors on <?= htmlspecialchars($bookingSuccess['date']) ?>.
                                </p>
                                <a href="events.php" class="btn btn--glass btn--sm btn--block"><span class="btn__label"><i class="ri-arrow-left-line"></i> View All Dates</span></a>
                            </div>
                        <?php else: ?>
                            <div class="ticket-booking-box" style="margin: 1.25rem 0; padding: 1.25rem; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--radius-md);">
                                <h3 style="font-family:'Unbounded', sans-serif; font-size:0.95rem; text-transform:uppercase; margin:0 0 12px; color:#fff;">Reserve Tickets</h3>
                                
                                <?php if ($bookingError !== ''): ?>
                                    <div style="background:rgba(239,68,68,0.15); border:1px solid #ef4444; color:#fff; padding:8px 12px; border-radius:var(--radius-xs); font-size:0.82rem; margin-bottom:12px;" role="alert">
                                        <i class="ri-error-warning-line"></i> <?= htmlspecialchars($bookingError) ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="single-event.php?city=<?= urlencode($cityParam) ?>" class="stack stack-3">
                                    <input type="hidden" name="city_key" value="<?= htmlspecialchars($cityParam) ?>">
                                    
                                    <div>
                                        <label for="ticket-quantity" style="display:block; font-size: 0.8rem; color: var(--ink-70); margin-bottom: 4px;">Number of Tickets</label>
                                        <div style="display:flex; align-items:center; justify-content:space-between; gap: 10px;">
                                            <select id="ticket-quantity" name="quantity" style="width: 100%; padding: 8px 12px; background: rgba(10,10,15,0.85); border: 1px solid rgba(255,255,255,0.18); border-radius: var(--radius-xs); color: #fff; font-size: 0.95rem;">
                                                <option value="1">1 Ticket</option>
                                                <option value="2" selected>2 Tickets</option>
                                                <option value="3">3 Tickets</option>
                                                <option value="4">4 Tickets</option>
                                                <option value="5">5 Tickets</option>
                                                <option value="6">6 Tickets (Max)</option>
                                            </select>
                                            <span id="ticket-calc-total" style="white-space:nowrap; font-size: 1.1rem; font-weight: 700; color: var(--accent);"></span>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="customer_name" style="display:block; font-size:0.8rem; color:var(--ink-70); margin-bottom:4px;">Full Name *</label>
                                        <input type="text" id="customer_name" name="customer_name" required value="<?= htmlspecialchars($_SESSION['name'] ?? $_SESSION['username'] ?? '') ?>" placeholder="Your name" style="width:100%; padding:8px 12px; background:rgba(10,10,15,0.85); border:1px solid rgba(255,255,255,0.18); border-radius:var(--radius-xs); color:#fff; font-size:0.88rem;">
                                    </div>

                                    <div>
                                        <label for="customer_email" style="display:block; font-size:0.8rem; color:var(--ink-70); margin-bottom:4px;">Email Address *</label>
                                        <input type="email" id="customer_email" name="customer_email" required value="<?= htmlspecialchars($_SESSION['email'] ?? '') ?>" placeholder="name@example.com" style="width:100%; padding:8px 12px; background:rgba(10,10,15,0.85); border:1px solid rgba(255,255,255,0.18); border-radius:var(--radius-xs); color:#fff; font-size:0.88rem;">
                                    </div>

                                    <div>
                                        <label for="customer_phone" style="display:block; font-size:0.8rem; color:var(--ink-70); margin-bottom:4px;">Mobile Phone (Optional)</label>
                                        <input type="tel" id="customer_phone" name="customer_phone" placeholder="+1..." style="width:100%; padding:8px 12px; background:rgba(10,10,15,0.85); border:1px solid rgba(255,255,255,0.18); border-radius:var(--radius-xs); color:#fff; font-size:0.88rem;">
                                    </div>

                                    <button type="submit" name="book_tickets" value="1" class="btn btn--solid btn--block" data-magnetic="0.25" style="margin-top:6px;">
                                        <span class="btn__label"><i class="ri-ticket-fill"></i> Confirm &amp; Reserve</span>
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <div class="info-list">
                            <div class="info-list__row"><span class="info-list__label">Doors</span><span class="info-list__value"><?= htmlspecialchars($curEvent['doors']) ?></span></div>
                            <div class="info-list__row"><span class="info-list__label">On stage</span><span class="info-list__value"><?= htmlspecialchars($curEvent['stage']) ?></span></div>
                            <div class="info-list__row"><span class="info-list__label">Curfew</span><span class="info-list__value"><?= htmlspecialchars($curEvent['curfew']) ?></span></div>
                            <div class="info-list__row"><span class="info-list__label">Address</span><span class="info-list__value"><?= htmlspecialchars($curEvent['address']) ?></span></div>
                            <div class="info-list__row"><span class="info-list__label">Transport</span><span class="info-list__value"><?= htmlspecialchars($curEvent['transport']) ?></span></div>
                        </div>

                        <a href="events.php" class="link-arrow" data-magnetic="0.2">All tour dates <i class="ri-arrow-right-up-line"></i></a>
                    </aside>
                </div>
            </div>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const qtySelect = document.getElementById('ticket-quantity');
    const calcTotal = document.getElementById('ticket-calc-total');
    const basePriceStr = <?= json_encode($curEvent['price']) ?>;
    if (qtySelect && calcTotal) {
        const symbol = basePriceStr.replace(/[0-9]/g, '') || '€';
        const numPrice = parseInt(basePriceStr.replace(/[^0-9]/g, ''), 10) || 45;
        function updateTicketTotal() {
            const qty = parseInt(qtySelect.value, 10) || 1;
            calcTotal.textContent = "Total: " + symbol + (numPrice * qty);
        }
        qtySelect.addEventListener('change', updateTicketTotal);
        updateTicketTotal();
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
