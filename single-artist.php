<?php
require_once __DIR__ . '/includes/db.php';

$artistId = isset($_GET['id']) ? (int) $_GET['id'] : 2;

$artist = null;

/* Fetch selected artist */
$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM artists WHERE id = ? LIMIT 1"
);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $artistId);
    mysqli_stmt_execute($stmt);

    $res = mysqli_stmt_get_result($stmt);
    $artist = mysqli_fetch_assoc($res);

    mysqli_stmt_close($stmt);
}

/* Fallback to first available artist */
if (!$artist) {
    $fbRes = mysqli_query(
        $conn,
        "SELECT * FROM artists ORDER BY id ASC LIMIT 1"
    );

    if ($fbRes) {
        $artist = mysqli_fetch_assoc($fbRes);

        if ($artist) {
            $artistId = (int) $artist['id'];
        }
    }
}

/* Artist not found */
if (!$artist) {
    $pageTitle = 'Artist Not Found — VEBRA';
    $activePage = 'artists';
    $bodyClass = 'theme-magenta';

    require_once __DIR__ . '/includes/header.php';
    ?>

    <main id="main">
        <section class="section section--head-clear">
            <div class="container-vebra">
                <h1 class="display">Artist not found</h1>
                <p class="section-head__text">
                    This artist profile is not available.
                </p>
                <a href="artists.php" class="btn btn--solid">
                    Back to Artists
                </a>
            </div>
        </section>
    </main>

    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

/* Artist details */
$artistName = $artist['name'] ?? 'Artist';
$artistBio = trim($artist['bio'] ?? '');

if ($artistBio === '') {
    $artistBio = 'Explore the music and releases of ' . $artistName . '.';
}

$artistImage = 'assets/images/team-1.jpg';

if (!empty($artist['image'])) {
    $imagePath = __DIR__ . '/' . $artist['image'];

    if (is_file($imagePath)) {
        $artistImage = $artist['image'];
    }
}

$roleParts = preg_split('/[\.\n]/', $artistBio);

$artistRole = !empty($roleParts[0])
    && mb_strlen(trim($roleParts[0])) < 60
    ? trim($roleParts[0])
    : 'Artist — VEBRA';

/* Fetch only this artist's approved songs */
$tracks = [];

$tStmt = mysqli_prepare(
    $conn,
    "SELECT id, title, artwork, release_year
     FROM songs
     WHERE artist_id = ?
       AND status = 'approved'
     ORDER BY id DESC
     LIMIT 4"
);

if ($tStmt) {
    mysqli_stmt_bind_param($tStmt, "i", $artistId);
    mysqli_stmt_execute($tStmt);

    $tRes = mysqli_stmt_get_result($tStmt);

    while ($row = mysqli_fetch_assoc($tRes)) {
        $tracks[] = $row;
    }

    mysqli_stmt_close($tStmt);
}

$pageTitle = $artistName . ' — Artists — VEBRA';
$activePage = 'artists';
$activeSub = 'single-artist';
$bodyClass = 'theme-magenta';

require_once __DIR__ . '/includes/header.php';
?>

<main id="main">

    <section class="section section--head-clear section--glow">

        <div class="container-vebra">

            <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                <a href="index.php">Home</a>

                <span class="breadcrumb-vebra__sep">/</span>

                <a href="artists.php">Artists</a>

                <span class="breadcrumb-vebra__sep">/</span>

                <span aria-current="page">
                    <?= htmlspecialchars($artistName, ENT_QUOTES, 'UTF-8') ?>
                </span>
            </nav>


            <div class="artist-profile">

                <!-- ARTIST IMAGE -->
                <div class="artist-profile__media" data-reveal="mask">

                    <img
                        src="<?= htmlspecialchars($artistImage, ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($artistName, ENT_QUOTES, 'UTF-8') ?>"
                    >

                </div>


                <!-- ARTIST INFORMATION -->
                <div class="artist-profile__content">

                    <div class="stack stack-3">

                        <span class="section-label">
                            Featured Artist
                        </span>

                        <h1 class="display display--lower artist-profile__name">
                            <?= htmlspecialchars($artistName, ENT_QUOTES, 'UTF-8') ?>
                        </h1>

                        <p class="section-head__text">
                            <?= htmlspecialchars($artistRole, ENT_QUOTES, 'UTF-8') ?>
                        </p>

                    </div>


                    <div class="rich-text" data-reveal="up" data-delay="0.15">
                        <p><?= nl2br(htmlspecialchars($artistBio, ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>


                    <div class="socials" data-reveal="up" data-delay="0.25">

                        <a href="https://facebook.com/"
                           target="_blank"
                           rel="noopener"
                           aria-label="Facebook">
                            <i class="ri-facebook-fill"></i>
                        </a>

                        <a href="https://x.com/"
                           target="_blank"
                           rel="noopener"
                           aria-label="X">
                            <i class="ri-twitter-fill"></i>
                        </a>

                        <a href="https://instagram.com/"
                           target="_blank"
                           rel="noopener"
                           aria-label="Instagram">
                            <i class="ri-instagram-line"></i>
                        </a>

                        <a href="https://spotify.com/"
                           target="_blank"
                           rel="noopener"
                           aria-label="Spotify">
                            <i class="ri-spotify-fill"></i>
                        </a>

                    </div>


                    <img
                        class="artist-profile__signature"
                        src="assets/images/sign.png"
                        alt=""
                        data-reveal="fade"
                        data-delay="0.3"
                    >


                    <!-- ARTIST RELEASES -->
                    <div class="sidebar__block" data-reveal="up" data-delay="0.2">

                        <h2 class="sidebar__title">
                            Releases with VEBRA
                        </h2>

                        <div class="mini-list">

                            <?php if (!empty($tracks)): ?>

                                <?php foreach ($tracks as $t):

                                    $tId = (int) $t['id'];

                                    $tTitle = htmlspecialchars(
                                        $t['title'] ?? 'Untitled',
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );

                                    $tArt = !empty($t['artwork'])
                                        ? htmlspecialchars($t['artwork'], ENT_QUOTES, 'UTF-8')
                                        : 'assets/images/album-1.jpg';

                                    $tYear = (int) ($t['release_year'] ?? 0);

                                ?>

                                    <a href="track.php?id=<?= $tId ?>"
                                       class="mini-item">

                                        <img
                                            src="<?= $tArt ?>"
                                            alt="<?= $tTitle ?>"
                                        >

                                        <div>
                                            <h3><?= $tTitle ?></h3>

                                            <span>
                                                <?= $tYear ?: 'VEBRA' ?> · Song
                                            </span>
                                        </div>

                                    </a>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <p class="section-head__text">
                                    No approved releases available for this artist yet.
                                </p>

                            <?php endif; ?>

                        </div>

                        <a href="discography.php"
                           class="link-arrow"
                           data-magnetic="0.2">
                            All releases
                            <i class="ri-arrow-right-up-line"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
