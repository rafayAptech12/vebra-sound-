<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/store-mailer.php';

$msgSent = false;
$contactEmailSent = false;
$subjectPrefill = isset($_GET['subject']) ? trim($_GET['subject']) : '';
$qtyParam = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 0;
if ($qtyParam > 0 && !empty($subjectPrefill)) {
    $subjectPrefill .= " ({$qtyParam} tickets requested)";
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['contact_submit'])) {
    $cName = trim($_POST['name'] ?? '');
    $cEmail = trim($_POST['email'] ?? '');
    $cSubject = trim($_POST['subject'] ?? '');
    $cMessage = trim($_POST['message'] ?? '');
    $consentGiven = !empty($_POST['consent']);

    if ($cName !== '' && $cEmail !== '' && $cSubject !== '' && $cMessage !== '' && $consentGiven && filter_var($cEmail, FILTER_VALIDATE_EMAIL)) {
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ssss", $cName, $cEmail, $cSubject, $cMessage);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $msgSent = true;
                $contactEmailSent = vebra_send_email(
                    vebra_mail_setting('MAIL_CONTACT_TO'),
                    'Contact form: ' . $cSubject,
                    "Name: {$cName}\nEmail: {$cEmail}\n\n{$cMessage}",
                    $cEmail,
                    $cName
                );
            }
        } catch (Exception $e) {
            error_log('VEBRA contact form insert failed: ' . $e->getMessage());
        }
    }
}

$pageTitle = 'Contact — vebra';
$pageDesc = 'Bookings, management and press contacts for vebra, plus the studio address in Brooklyn.';
$bodyClass = 'theme-magenta';
$activePage = 'contact';
require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- CONTACT
    ============================================= -->
    <section class="page-caption" data-parallax-scope>
        <div class="page-caption__bg" aria-hidden="true">
            <img src="assets/images/contact-bg.jpg" alt="" data-parallax-speed="0.12">
        </div>

        <div class="container-vebra">
            <div class="page-caption__inner">
                <ul class="breadcrumb-vebra">
                    <li><a href="index.php">Home</a></li>
                    <li class="breadcrumb-vebra__sep" aria-hidden="true">/</li>
                    <li aria-current="page">Contact</li>
                </ul>

                <h1 class="page-caption__title" data-split="chars" data-split-hero>
                    Get in touch
                    <span class="page-caption__ghost" aria-hidden="true">Contact</span>
                </h1>

                <p class="page-caption__text" data-reveal="up" data-delay="0.2">
                    Bookings, licensing, press or just a note about a show — write to us and someone
                    in the band or the office will come back to you within two working days.
                </p>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container-vebra">
            <div class="contact-layout">
                <div class="contact-info">
                    <h2 class="contact-info__title" data-split="lines">Say <em>hello</em></h2>

                    <div class="contact-info__list">
                        <div class="contact-info__item" data-reveal="up">
                            <span class="label">Bookings</span>
                            <a href="mailto:bookings@vebra.com">bookings@vebra.com</a>
                        </div>
                        <div class="contact-info__item" data-reveal="up" data-delay="0.08">
                            <span class="label">Management</span>
                            <a href="tel:+15550142200">+1 (555) 014 2200</a>
                        </div>
                        <div class="contact-info__item" data-reveal="up" data-delay="0.16">
                            <span class="label">Press &amp; licensing</span>
                            <a href="mailto:press@vebra.com">press@vebra.com</a>
                        </div>
                        <div class="contact-info__item" data-reveal="up" data-delay="0.24">
                            <span class="label">Studio</span>
                            <p>218 Rivington Street<br>Brooklyn, NY 11206</p>
                        </div>
                    </div>

                    <div class="socials">
                        <a href="https://facebook.com/" target="_blank" rel="noopener" aria-label="Facebook"><i class="ri-facebook-fill"></i></a>
                        <a href="https://x.com/" target="_blank" rel="noopener" aria-label="X / Twitter"><i class="ri-twitter-fill"></i></a>
                        <a href="https://instagram.com/" target="_blank" rel="noopener" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                        <a href="https://youtube.com/" target="_blank" rel="noopener" aria-label="YouTube"><i class="ri-youtube-fill"></i></a>
                        <a href="https://spotify.com/" target="_blank" rel="noopener" aria-label="Spotify"><i class="ri-spotify-fill"></i></a>
                    </div>
                </div>

                <div class="contact-form" data-reveal="up" data-delay="0.1">
                    <?php if ($msgSent): ?>
                        <div style="background: rgba(var(--accent-rgb), 0.15); border: 1px solid var(--accent); padding: 18px 22px; border-radius: 6px; margin-bottom: 28px; color: #fff;">
                            <h3 style="color:var(--accent); margin-bottom:6px; font-family:'Unbounded',sans-serif; font-size:1.1rem;"><i class="ri-checkbox-circle-fill"></i> Message Received</h3>
                            <p style="margin:0; opacity:0.9;">Thanks <?= htmlspecialchars($cName) ?> &mdash; your message is with us. We answer everything within two working days.</p>
                            <?php if (!$contactEmailSent): ?>
                                <p role="status" style="margin:10px 0 0; color:#ffb4c4;">Your message was saved, but we could not send an email notification. Please contact us directly if you need a prompt reply.</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <input type="hidden" name="contact_submit" value="1">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="field">
                                    <input type="text" name="name" id="contact-name" class="field__control" placeholder=" " required>
                                    <label for="contact-name" class="field__label">Your name</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="field">
                                    <input type="email" name="email" id="contact-email" class="field__control" placeholder=" " required>
                                    <label for="contact-email" class="field__label">Your email</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="field">
                                    <input type="text" name="subject" id="contact-subject" class="field__control" placeholder=" " value="<?= htmlspecialchars($subjectPrefill) ?>" required>
                                    <label for="contact-subject" class="field__label">Subject</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="field">
                                    <textarea name="message" id="contact-message" class="field__control" placeholder=" " rows="5" required></textarea>
                                    <label for="contact-message" class="field__label">Message</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                        </div>

                        <label class="check">
                            <input type="checkbox" name="consent" required checked>
                            <span class="check__box" aria-hidden="true"></span>
                            <span>I agree that vebra may store this message in order to reply to it.</span>
                        </label>

                        <div class="contact-form__actions">
                            <button type="submit" class="btn btn--solid" data-magnetic="0.3"><span class="btn__label">Send message <i class="ri-arrow-right-up-line"></i></span></button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="contact-map" data-reveal="mask" data-cursor="Map">
                <img src="assets/images/contact-bg.jpg" alt="Map of the vebra studio in Brooklyn">
                <span class="contact-map__pin"><i class="ri-map-pin-2-fill"></i> 218 Rivington St, Brooklyn</span>
            </div>
        </div>
    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
