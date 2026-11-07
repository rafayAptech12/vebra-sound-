<?php
require_once __DIR__ . '/includes/db.php';

$submissionNotice = '';
$submissionError = '';

/* =========================================================
   ARTIST APPLICATION
   Actual database: artists
   Fields: id, name, bio, image, created_at
   ========================================================= */

if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
    && isset($_POST['submit_artist_request'])
) {

    $artName = trim($_POST['artist_name'] ?? '');
    $artBio = trim($_POST['artist_bio'] ?? '');

    $imagePath = '';
    $uploadValid = true;

    /* ARTIST NAME VALIDATION */

    if ($artName === '') {

        $submissionError = 'Artist or band name is required.';
        $uploadValid = false;

    } elseif (mb_strlen($artName) > 150) {

        $submissionError = 'Artist name must not exceed 150 characters.';
        $uploadValid = false;

    }

    if (
    !isset($_FILES['artist_image']) ||
    $_FILES['artist_image']['error'] === UPLOAD_ERR_NO_FILE
) {
    $submissionError = 'Please upload your artist photo.';
    $uploadValid = false;
}

    /* IMAGE UPLOAD VALIDATION */

    if (
        $uploadValid &&
        isset($_FILES['artist_image']) &&
        $_FILES['artist_image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        $file = $_FILES['artist_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {

            $submissionError = 'Image upload failed. Please try again.';
            $uploadValid = false;

        } elseif ($file['size'] > 5 * 1024 * 1024) {

            $submissionError = 'Image must be smaller than 5MB.';
            $uploadValid = false;

        } else {

            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            $mime = $finfo
                ? finfo_file($finfo, $file['tmp_name'])
                : '';

            if ($finfo) {
                finfo_close($finfo);
            }

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($allowed[$mime])) {

                $submissionError = 'Only JPG, PNG and WEBP images are allowed.';
                $uploadValid = false;

            } else {

                $uploadDir = __DIR__ . '/assets/media/artists/';

                if (
                    !is_dir($uploadDir) &&
                    !mkdir($uploadDir, 0755, true)
                ) {

                    $submissionError = 'Could not create the image upload directory.';
                    $uploadValid = false;

                }

                if ($uploadValid) {

                    $newFilename = 'img_req_'
                        . bin2hex(random_bytes(8))
                        . '.'
                        . $allowed[$mime];

                    if (
                        move_uploaded_file(
                            $file['tmp_name'],
                            $uploadDir . $newFilename
                        )
                    ) {

                        $imagePath = 'assets/media/artists/' . $newFilename;

                    } else {

                        $submissionError = 'Could not save the uploaded image.';
                        $uploadValid = false;

                    }

                }

            }

        }

    }

 /* DATABASE INSERT — PENDING APPLICATION */

if ($uploadValid && $submissionError === '') {

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO artist_applications
         (name, bio, image, status)
         VALUES (?, ?, ?, 'pending')"
    );

    if ($stmt) {

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $artName,
            $artBio,
            $imagePath
        );

        if (mysqli_stmt_execute($stmt)) {

            $submissionNotice =
                'Your application has been submitted successfully. Our team will review it shortly.';

        } else {

            $submissionError =
                'Could not submit your application. Please try again.';

        }

        mysqli_stmt_close($stmt);

    } else {

        $submissionError =
            'Database error. Please try again.';

    }
}
}
/* =========================================================
   PAGE SETTINGS
   ========================================================= */

$pageTitle = 'Add Artist — VEBRA';
$activePage = 'artists';
$activeSub = 'artists';
$bodyClass = 'theme-magenta';

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <!-- PAGE CAPTION -->

    <section class="page-caption" data-parallax-scope>

        <div class="page-caption__bg" aria-hidden="true">

            <img
                src="assets/images/artists-page-cover.jpg"
                alt=""
                data-parallax-speed="0.12"
            >

        </div>

        <div class="container-vebra">

            <div class="page-caption__inner">

                <nav class="breadcrumb-vebra" aria-label="Breadcrumb">

                    <a href="index.php">Home</a>

                    <span class="breadcrumb-vebra__sep">/</span>

                    <a href="artists.php">Artists</a>

                    <span class="breadcrumb-vebra__sep">/</span>

                    <span aria-current="page">Add Artist</span>

                </nav>

                <h1 class="page-caption__title"
                    data-split="chars"
                    data-split-hero>

                    Join the roster

                    <span class="page-caption__ghost" aria-hidden="true">
                        Join the roster
                    </span>

                </h1>

                <p class="page-caption__text"
                   data-reveal="up"
                   data-delay="0.2">

                    Your music deserves a place here.
                    Introduce yourself and become part of the VEBRA artist directory.

                </p>

            </div>

        </div>

    </section>


    <!-- ARTIST APPLICATION -->

    <section class="section section--below-header section--glow"
             style="padding-top:2rem;">

        <div class="container-vebra">

            <div style="
                max-width:760px;
                margin:0 auto;
                background:var(--bg-2);
                border:1px solid rgba(255,255,255,0.08);
                border-radius:var(--radius-md);
                padding:var(--space-6);
                backdrop-filter:blur(12px);
            ">

                <div class="section-head"
                     style="margin-bottom:var(--space-4);">

                    <div>

                        <span class="section-label">
                            Join The Collective
                        </span>

                        <h2 class="section-head__title"
                            style="font-size:var(--fs-h3);">

                            Apply for <em>Artist Roster</em>

                        </h2>

                    </div>

                    <p class="section-head__text"
                       style="font-size:0.88rem;">

                        Submit your artist or band details to become part of the VEBRA artist directory.

                    </p>

                </div>


                <!-- SUCCESS MESSAGE -->

                <?php if ($submissionNotice !== ''): ?>

                    <div
                        style="
                            background:rgba(var(--accent-rgb,255,46,147),0.12);
                            border:1px solid var(--accent);
                            color:#fff;
                            padding:16px 20px;
                            border-radius:var(--radius-sm);
                            margin-bottom:24px;
                        "
                        role="status"
                    >

                        <i class="ri-checkbox-circle-fill"
                           style="color:var(--accent);"></i>

                        <?= htmlspecialchars(
                            $submissionNotice,
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>

                    </div>

                    <a href="artists.php"
                       class="btn btn--solid">

                        <span class="btn__label">
                            <i class="ri-arrow-left-line"></i>
                            Back to Artists
                        </span>

                    </a>

                <?php else: ?>


                    <!-- ERROR MESSAGE -->

                    <?php if ($submissionError !== ''): ?>

                        <div
                            style="
                                background:rgba(239,68,68,0.15);
                                border:1px solid #ef4444;
                                color:#fff;
                                padding:16px 20px;
                                border-radius:var(--radius-sm);
                                margin-bottom:24px;
                            "
                            role="alert"
                        >

                            <i class="ri-error-warning-fill"
                               style="color:#ef4444;"></i>

                            <?= htmlspecialchars(
                                $submissionError,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                        </div>

                    <?php endif; ?>


                    <!-- APPLICATION FORM -->

                    <form
                        method="POST"
                        action="add-artist.php"
                        enctype="multipart/form-data"
                        class="stack stack-4"
                    >

                        <div class="form-group">

                            <label
                                for="artist_name"
                                style="
                                    display:block;
                                    font-size:0.82rem;
                                    font-weight:600;
                                    text-transform:uppercase;
                                    letter-spacing:0.04em;
                                    color:var(--ink-70);
                                    margin-bottom:8px;
                                "
                            >

                                Artist or Band Name *

                            </label>

                            <input
                                type="text"
                                id="artist_image"
                                name="artist_image"
                                accept="image/jpeg,image/png,image/webp"
                                required
                                maxlength="150"
                                value="<?= htmlspecialchars(
                                    $_POST['artist_name'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>"
                                placeholder="e.g. Luminary Trio"
                                style="
                                    width:100%;
                                    background:rgba(255,255,255,0.04);
                                    border:1px solid rgba(255,255,255,0.15);
                                    border-radius:var(--radius-xs);
                                    color:#fff;
                                    padding:12px 16px;
                                    font-size:0.95rem;
                                "
                            >

                        </div>


                        <div class="form-group">

                            <label
                                for="artist_bio"
                                style="
                                    display:block;
                                    font-size:0.82rem;
                                    font-weight:600;
                                    text-transform:uppercase;
                                    letter-spacing:0.04em;
                                    color:var(--ink-70);
                                    margin-bottom:8px;
                                "
                            >

                                Bio &amp; Style / Instruments

                            </label>

                            <textarea
                                id="artist_bio"
                                name="artist_bio"
                                rows="4"
                                placeholder="Brief biography, musical style, instrumentation and background..."
                                style="
                                    width:100%;
                                    background:rgba(255,255,255,0.04);
                                    border:1px solid rgba(255,255,255,0.15);
                                    border-radius:var(--radius-xs);
                                    color:#fff;
                                    padding:12px 16px;
                                    font-size:0.95rem;
                                    resize:vertical;
                                "
                            ><?= htmlspecialchars(
                                $_POST['artist_bio'] ?? '',
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?></textarea>

                        </div>


                        <div class="form-group">

                            <label
                                for="artist_image"
                                style="
                                    display:block;
                                    font-size:0.82rem;
                                    font-weight:600;
                                    text-transform:uppercase;
                                    letter-spacing:0.04em;
                                    color:var(--ink-70);
                                    margin-bottom:8px;
                                "
                            >

                                Portrait or Press Photo
                                (JPG, PNG, WEBP — Max 5MB)

                            </label>

                            <input
                                type="file"
                                id="artist_image"
                                name="artist_image"
                                accept="image/jpeg,image/png,image/webp"
                                style="
                                    width:100%;
                                    background:rgba(255,255,255,0.04);
                                    border:1px solid rgba(255,255,255,0.15);
                                    border-radius:var(--radius-xs);
                                    color:var(--ink-70);
                                    padding:10px 14px;
                                    font-size:0.9rem;
                                "
                            >

                        </div>


                        <div style="
                            display:flex;
                            justify-content:space-between;
                            align-items:center;
                            flex-wrap:wrap;
                            gap:16px;
                            margin-top:8px;
                        ">

                            <span style="
                                font-size:0.78rem;
                                color:var(--ink-50);
                            ">

                                <i class="ri-shield-check-line"
                                   style="color:var(--accent);"></i>

                                Artist directory submission

                            </span>


                            <button
                                type="submit"
                                name="submit_artist_request"
                                value="1"
                                class="btn btn--solid"
                                data-magnetic="0.2"
                            >

                                <span class="btn__label">

                                    <i class="ri-send-plane-fill"></i>

                                    Submit Application

                                </span>

                            </button>

                        </div>

                    </form>


                    <div style="margin-top:24px;">

                        <a href="artists.php"
                           class="btn btn--glass btn--sm">

                            <span class="btn__label">

                                <i class="ri-arrow-left-line"></i>

                                Back to Artists

                            </span>

                        </a>

                    </div>

                <?php endif; ?>

            </div>

        </div>

    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>